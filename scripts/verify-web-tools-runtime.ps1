param(
    [string]$BaseUrl = "http://127.0.0.1:9400"
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$BaseUrl = $BaseUrl.TrimEnd("/")
$Failures = New-Object System.Collections.Generic.List[string]
$Session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

function Assert-ToolRuntime([bool]$Condition, [string]$Message) {
    if (-not $Condition) { $Failures.Add($Message) }
}

function Get-ToolPage([string]$Path, [string]$Hook) {
    $Response = Invoke-WebRequest -UseBasicParsing -WebSession $Session "$BaseUrl$Path"
    Assert-ToolRuntime ($Response.StatusCode -eq 200) "Tool page did not return HTTP 200: $Path"
    Assert-ToolRuntime (([regex]::Matches($Response.Content, '<h1\b')).Count -eq 1) "Tool page must render exactly one H1: $Path"
    Assert-ToolRuntime ($Response.Content.Contains($Hook)) "Tool page is missing its interface hook: $Path"
    return $Response.Content
}

$DirectoryHtml = Get-ToolPage "/tools/" "stc-tools-directory"
$FinderHtml = Get-ToolPage "/tools/find-this-place/" "data-stc-place-finder"
$TaxiHtml = Get-ToolPage "/tools/taxi-card/" "data-stc-taxi-tool"

foreach ($DirectoryToken in @("Choose a tool", "Find This Place", "Taxi Card", "Check when tickets open", 'class="stc-tool-card"', "/tools/find-this-place/", "/tools/taxi-card/")) {
    Assert-ToolRuntime ($DirectoryHtml.Contains($DirectoryToken)) "Tools directory is missing: $DirectoryToken"
}
foreach ($FinderToken in @("data-stc-place-dropzone", "data-stc-place-preview", 'name="images[]"', "multiple", "Choose 1–4 photos", "20 MB each", "Include a wide view or visible sign.", "Photos are discarded after identification.", "data-stc-city-hint")) {
    Assert-ToolRuntime ($FinderHtml.Contains($FinderToken)) "Find This Place page is missing: $FinderToken"
}
foreach ($SecretToken in @("STC_PLACE_VISION_TOKEN", "STC_PLACE_RESOLVER_TOKEN", "Authorization: Bearer")) {
    Assert-ToolRuntime (-not ($DirectoryHtml + $FinderHtml + $TaxiHtml).Contains($SecretToken)) "Tool HTML exposed provider configuration: $SecretToken"
}
foreach ($TaxiToken in @("data-stc-taxi-form", "data-stc-driver-mode", "Copy destination", "Show full-screen card")) {
    Assert-ToolRuntime ($TaxiHtml.Contains($TaxiToken)) "Taxi Card page is missing: $TaxiToken"
}

$ResolvedResponse = Invoke-WebRequest -UseBasicParsing -WebSession $Session -Method Post -Uri "$BaseUrl/wp-json/stc/v1/taxi-card" -ContentType "application/json" -Body '{"query":"Forbidden City"}'
$Resolved = $ResolvedResponse.Content | ConvertFrom-Json
Assert-ToolRuntime ($ResolvedResponse.StatusCode -eq 200) "Canonical Taxi Card resolution did not return HTTP 200."
Assert-ToolRuntime ([string]$ResolvedResponse.Headers."Cache-Control" -match "no-store") "Taxi Card response is missing private no-store caching."
Assert-ToolRuntime ($Resolved.status -eq "resolved") "Forbidden City did not resolve."
Assert-ToolRuntime ($Resolved.place.entity_key -eq "forbidden-city") "Forbidden City resolved to the wrong canonical key."
Assert-ToolRuntime ($Resolved.place.name_zh -eq "故宫博物院") "Forbidden City Chinese name is not canonical."
Assert-ToolRuntime ($Resolved.place.verified_address_zh -eq "北京市东城区景山前街4号") "Forbidden City verified Chinese address is wrong."
Assert-ToolRuntime ($Resolved.place.sources.address -eq "VERIFIED") "Forbidden City address is not marked VERIFIED."
Assert-ToolRuntime (-not $Resolved.place.PSObject.Properties.Name.Contains("aliases")) "Taxi Card leaked private resolver aliases."

$Ambiguous = Invoke-RestMethod -WebSession $Session -Method Post -Uri "$BaseUrl/wp-json/stc/v1/taxi-card" -ContentType "application/json" -Body '{"query":"West Lake"}'
Assert-ToolRuntime ($Ambiguous.status -eq "ambiguous") "West Lake did not produce an ambiguity state."
Assert-ToolRuntime (@($Ambiguous.candidates).Count -eq 2) "West Lake ambiguity did not return exactly two curated candidates."
Assert-ToolRuntime (@($Ambiguous.candidates.city_en) -contains "Hangzhou") "West Lake ambiguity is missing Hangzhou."
Assert-ToolRuntime (@($Ambiguous.candidates.city_en) -contains "Huizhou") "West Lake ambiguity is missing Huizhou."
Assert-ToolRuntime (@($Ambiguous.candidates | Where-Object { $_.verified_address_zh }).Count -eq 0) "Unverified West Lake street address was exposed."

try {
    Invoke-WebRequest -UseBasicParsing -WebSession $Session -Method Post -Uri "$BaseUrl/wp-json/stc/v1/taxi-card" -ContentType "application/json" -Body '{"query":"Definitely Not A Verified Place"}' | Out-Null
    Assert-ToolRuntime $false "Unknown Taxi Card query unexpectedly resolved."
} catch {
    Assert-ToolRuntime ($_.Exception.Response.StatusCode.value__ -eq 404) "Unknown Taxi Card query did not return HTTP 404."
    Assert-ToolRuntime (-not $_.ErrorDetails.Message.Contains("trace")) "Unknown Taxi Card response exposed internal diagnostics."
}

$MediaBeforeResponse = Invoke-WebRequest -UseBasicParsing -WebSession $Session "$BaseUrl/wp-json/wp/v2/media?per_page=1"
$MediaBefore = [int](@($MediaBeforeResponse.Headers."X-WP-Total")[0])
$PngImage = Get-Item -LiteralPath (Join-Path $Root "wp-content/themes/solo-to-china/assets/images/card-forbidden-city.png")
$WebpImage = Get-Item -LiteralPath (Join-Path $Root "wp-content/themes/solo-to-china/assets/images/card-forbidden-city-hd.webp")
$JpegPath = Join-Path ([System.IO.Path]::GetTempPath()) ("stc-web-tools-" + [guid]::NewGuid().ToString("N") + ".jpg")
Add-Type -AssemblyName System.Drawing
$SourceImage = [System.Drawing.Image]::FromFile($PngImage.FullName)
try { $SourceImage.Save($JpegPath, [System.Drawing.Imaging.ImageFormat]::Jpeg) } finally { $SourceImage.Dispose() }

foreach ($ValidImage in @($PngImage, $WebpImage, (Get-Item -LiteralPath $JpegPath))) {
    try {
        Invoke-WebRequest -UseBasicParsing -WebSession $Session -Method Post -Uri "$BaseUrl/wp-json/stc/v1/place-finder" -Form @{ image = $ValidImage } | Out-Null
        Assert-ToolRuntime $false "Provider-free valid $($ValidImage.Extension) request unexpectedly succeeded."
    } catch {
        Assert-ToolRuntime ($_.Exception.Response.StatusCode.value__ -eq 503) "Valid $($ValidImage.Extension) upload was not accepted before the safe provider-unavailable response."
        Assert-ToolRuntime ($_.ErrorDetails.Message.Contains("temporarily unavailable")) "Provider-free Find This Place error is not actionable."
        $CacheHeader = @($_.Exception.Response.Headers | Where-Object Key -eq "Cache-Control" | ForEach-Object Value) -join ","
        Assert-ToolRuntime ($CacheHeader -match "no-store") "Find This Place error is missing private no-store caching."
        foreach ($ForbiddenErrorToken in @("STC_PLACE_VISION_TOKEN", "STC_PLACE_VISION_ENDPOINT", "Stack trace", "Fatal error")) {
            Assert-ToolRuntime (-not $_.ErrorDetails.Message.Contains($ForbiddenErrorToken)) "Find This Place error exposed internal detail: $ForbiddenErrorToken"
        }
    }
}
try {
    Invoke-WebRequest -UseBasicParsing -WebSession $Session -Method Post -Uri "$BaseUrl/wp-json/stc/v1/place-finder" -Form @{ "images[]" = @($PngImage, $WebpImage) } | Out-Null
    Assert-ToolRuntime $false "Provider-free multi-photo request unexpectedly succeeded."
} catch {
    Assert-ToolRuntime ($_.Exception.Response.StatusCode.value__ -eq 503) "Two valid photos were not accepted as one combined identification request."
    Assert-ToolRuntime ($_.ErrorDetails.Message.Contains("temporarily unavailable")) "Multi-photo provider-free error is not actionable."
}
try {
    Invoke-WebRequest -UseBasicParsing -WebSession $Session -Method Post -Uri "$BaseUrl/wp-json/stc/v1/place-finder" -Form @{ image = (Get-Item -LiteralPath (Join-Path $Root "README.md")) } | Out-Null
    Assert-ToolRuntime $false "Invalid non-image upload unexpectedly succeeded."
} catch {
    Assert-ToolRuntime ($_.Exception.Response.StatusCode.value__ -eq 415) "Invalid non-image upload did not return HTTP 415."
    Assert-ToolRuntime ($_.ErrorDetails.Message.Contains("not supported")) "Invalid upload error is not actionable."
}
Remove-Item -LiteralPath $JpegPath -Force
$MediaAfterResponse = Invoke-WebRequest -UseBasicParsing -WebSession $Session "$BaseUrl/wp-json/wp/v2/media?per_page=1"
$MediaAfter = [int](@($MediaAfterResponse.Headers."X-WP-Total")[0])
Assert-ToolRuntime ($MediaAfter -eq $MediaBefore) "Find This Place persisted an image in the WordPress Media Library."

if ($Failures.Count -gt 0) { throw ($Failures -join [Environment]::NewLine) }
Write-Host "SoloToChina Web Tools runtime verification passed at $BaseUrl."
