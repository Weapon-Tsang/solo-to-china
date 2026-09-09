$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$Failures = New-Object System.Collections.Generic.List[string]

function Require-Token([string]$Source, [string]$Token, [string]$Message) {
    if (-not $Source.Contains($Token)) { $Failures.Add($Message) }
}

$Files = @(
    "wp-content/plugins/solo-to-china-tools/includes/places.php",
    "wp-content/plugins/solo-to-china-tools/includes/providers.php",
    "wp-content/plugins/solo-to-china-tools/includes/rest-api.php",
    "wp-content/plugins/solo-to-china-tools/includes/shortcodes.php",
    "wp-content/plugins/solo-to-china-tools/assets/js/tools.js",
    "wp-content/plugins/solo-to-china-tools/assets/css/tools.css",
    "wp-content/themes/solo-to-china-child/assets/css/tools.css"
)

foreach ($File in $Files) {
    if (-not (Test-Path -LiteralPath (Join-Path $Root $File) -PathType Leaf)) { $Failures.Add("Missing web tools file: $File") }
}

$Plugin = Get-Content -Raw -LiteralPath (Join-Path $Root "wp-content/plugins/solo-to-china-tools/solo-to-china-tools.php")
$Places = Get-Content -Raw -LiteralPath (Join-Path $Root "wp-content/plugins/solo-to-china-tools/includes/places.php")
$Providers = Get-Content -Raw -LiteralPath (Join-Path $Root "wp-content/plugins/solo-to-china-tools/includes/providers.php")
$Rest = Get-Content -Raw -LiteralPath (Join-Path $Root "wp-content/plugins/solo-to-china-tools/includes/rest-api.php")
$Shortcodes = Get-Content -Raw -LiteralPath (Join-Path $Root "wp-content/plugins/solo-to-china-tools/includes/shortcodes.php")
$Javascript = Get-Content -Raw -LiteralPath (Join-Path $Root "wp-content/plugins/solo-to-china-tools/assets/js/tools.js")
$Registry = Get-Content -Raw -LiteralPath (Join-Path $Root "wp-content/themes/solo-to-china/content-contract/component-registry.v1.json")
$AllToolSource = $Plugin + $Places + $Providers + $Rest + $Shortcodes + $Javascript

foreach ($Token in @("STC_Place_Vision_Provider", "STC_HTTP_Place_Vision_Provider", "STC_Place_Resolver_Provider", "STC_PLACE_VISION_ENDPOINT", "STC_PLACE_VISION_TOKEN", "'images'", "'image_count'", "stc-place-v2")) {
    Require-Token $Providers $Token "Provider abstraction is missing: $Token"
}
foreach ($Token in @("/place-finder", "/taxi-card", "permission_callback", "private, no-store", "hash_hmac", "set_transient", "is_uploaded_file", "getimagesize", "wp_get_image_mime", "image/jpeg", "image/png", "image/webp", "@unlink", "40000000", "STC_TOOLS_MAX_IMAGE_COUNT", "STC_TOOLS_MAX_UPLOAD_BYTES", "provider_timeout", "504", "stc_rate_limited", "429")) {
    Require-Token $Rest $Token "REST upload/rate/privacy boundary is missing: $Token"
}
foreach ($Token in @("confidence", "match_level", "exact_viewpoint", "AI_INFERRED", "UNKNOWN", "VERIFIED", "alternative_candidates")) {
    Require-Token ($Providers + $Places + $Rest) $Token "Confidence or verification state is missing: $Token"
}
foreach ($Token in @("forbidden-city", "west-lake-hangzhou", "west-lake-huizhou", "上海虹桥站", "外滩", "_stc_entity_key", "related_city_guide", "related_attraction_guide")) {
    Require-Token $Places $Token "Canonical place/guide resolver is missing: $Token"
}
foreach ($Token in @("data-stc-place-finder", "data-stc-place-dropzone", "data-stc-place-preview", 'name="images[]"', "multiple", "Choose 1–4 photos", "20 MB each", "Photos are discarded after identification.", "data-stc-taxi-tool", "data-stc-driver-mode", "Copy destination", "Show full-screen card", '<a class="stc-tool-card"', "stc-tool-card__action")) {
    Require-Token $Shortcodes $Token "Tool interface is missing: $Token"
}
foreach ($Token in @("entity_key", "FormData", "images[]", "currentFiles", "maxImageCount", "maxUploadBytes", "clipboard", "dragover", "paste", "HIGH CONFIDENCE", "LIKELY MATCH", "Address not verified", "AI inferred", "Escape")) {
    Require-Token $Javascript $Token "Tool interaction is missing: $Token"
}
Require-Token $Registry '"id": "destination_card"' "Registry does not publish destination_card."

foreach ($Forbidden in @("wp_insert_attachment", "media_handle_sideload", "wp_handle_upload", "localStorage", "sessionStorage", "text/calendar", "data:image")) {
    if ($AllToolSource.Contains($Forbidden)) { $Failures.Add("Forbidden persistence/upload behavior found: $Forbidden") }
}
if ($Shortcodes.Contains('<article class="stc-tool-card"')) { $Failures.Add("Tool cards are not full-card links.") }
if ($AllToolSource -match '(?i)(sk-[a-z0-9]{20,}|api[_-]?key\s*[=:]\s*["''][^"'']+)') { $Failures.Add("Possible API credential found in tool source.") }
if ($Javascript -match '(?i)\b\d{1,3}%\s*(match|confidence)') { $Failures.Add("UI contains pseudo-precise confidence percentages.") }

if ($Failures.Count -gt 0) { throw ($Failures -join [Environment]::NewLine) }
Write-Host "SoloToChina Web Tools verification passed."
