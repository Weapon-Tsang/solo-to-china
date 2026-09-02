param(
    [string]$BaseUrl = "http://127.0.0.1:9400",

    [switch]$ParentOnly
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$BaseUrl = $BaseUrl.TrimEnd("/")
$Failures = New-Object System.Collections.Generic.List[string]

function Add-RuntimeFailure([string]$Message) {
    $Failures.Add($Message)
}

function Assert-Runtime([bool]$Condition, [string]$Message) {
    if (-not $Condition) {
        Add-RuntimeFailure $Message
    }
}

function Get-FixtureHtml([string]$Slug) {
    $Response = Invoke-WebRequest -UseBasicParsing "$BaseUrl/$Slug/"
    Assert-Runtime ($Response.StatusCode -eq 200) "Fixture did not return HTTP 200: $Slug"
    return $Response.Content
}

function Get-FixturePost([string]$Slug) {
    $Posts = Invoke-RestMethod "$BaseUrl/wp-json/wp/v2/posts?slug=$Slug&_fields=id,slug,meta,categories"
    Assert-Runtime (@($Posts).Count -eq 1) "Fixture REST post was not found exactly once: $Slug"
    return @($Posts)[0]
}

$ContractResponse = Invoke-WebRequest -UseBasicParsing "$BaseUrl/wp-json/stc/v1/content-contract"
$ContractAgain = Invoke-WebRequest -UseBasicParsing "$BaseUrl/wp-json/stc/v1/content-contract"
$Contract = $ContractResponse.Content | ConvertFrom-Json

Assert-Runtime ($ContractResponse.StatusCode -eq 200) "Content Contract endpoint did not return HTTP 200."
Assert-Runtime ($ContractResponse.Content -eq $ContractAgain.Content) "Content Contract JSON changed between consecutive reads."
Assert-Runtime ([string]$ContractResponse.Headers.ETag -eq [string]$ContractAgain.Headers.ETag) "Content Contract ETag is not stable."
Assert-Runtime ($Contract.contract_version -eq "1.0.0") "Content Contract version is not 1.0.0."
Assert-Runtime ($Contract.theme_version -eq "0.24.0") "Content Contract Theme version is not 0.24.0."
Assert-Runtime ($Contract.guide_types."survival-kit".category_slug -eq "survival-kit") "Survival Kit category mapping is wrong."
Assert-Runtime ($Contract.guide_types."city-guide".category_slug -eq "city-guides") "City Guide category mapping is wrong."
Assert-Runtime ($Contract.guide_types."attraction-guide".category_slug -eq "attraction-guides") "Attraction Guide category mapping is wrong."
Assert-Runtime ($Contract.guide_types."travel-guide".category_slug -eq "travel-guides") "Travel Guide category mapping is wrong."

$ExpectedRequiredFields = @{
    quick_answer = @("answer")
    key_takeaways = @("items")
    quick_facts = @("items")
    warning = @("content")
    steps = @("items")
    faq = @("items")
    planner_cta = @("title", "description", "cta_label", "target_url")
    ticket_reminder = @("attraction_slug")
    affiliate_cta = @("category", "provider", "title", "description", "cta_label", "target_url")
}

foreach ($ComponentType in $ExpectedRequiredFields.Keys) {
    $Component = @($Contract.components | Where-Object { $_.type -eq $ComponentType })
    Assert-Runtime ($Component.Count -eq 1) "Contract component missing or duplicated: $ComponentType"
    if ($Component.Count -eq 1) {
        $ActualFields = @($Component[0].required_fields)
        foreach ($RequiredField in $ExpectedRequiredFields[$ComponentType]) {
            Assert-Runtime ($ActualFields -contains $RequiredField) "Contract component $ComponentType is missing required field: $RequiredField"
        }
    }
}

$ContractRuntime = Get-Content -LiteralPath (Join-Path $Root "wp-content/themes/solo-to-china/inc/content-contract.php") -Raw
foreach ($MetadataToken in @("register_post_meta", "_stc_guide_type", "_stc_content_contract_version", "sanitize_callback", "auth_callback", "current_user_can", "edit_post")) {
    Assert-Runtime ($ContractRuntime.Contains($MetadataToken)) "REST metadata sanitize/validation boundary is missing: $MetadataToken"
}

$SurvivalSlug = "china-mobile-payment-setup"
$CitySlug = "beijing-first-time-city-guide"
$AttractionSlug = "forbidden-city-first-time-visitor-guide"
$SurvivalHtml = Get-FixtureHtml $SurvivalSlug
$CityHtml = Get-FixtureHtml $CitySlug
$AttractionHtml = Get-FixtureHtml $AttractionSlug
$SurvivalPost = Get-FixturePost $SurvivalSlug
$CityPost = Get-FixturePost $CitySlug
$AttractionPost = Get-FixturePost $AttractionSlug

foreach ($Fixture in @(
    @{ Slug = $SurvivalSlug; Html = $SurvivalHtml },
    @{ Slug = $CitySlug; Html = $CityHtml },
    @{ Slug = $AttractionSlug; Html = $AttractionHtml }
)) {
    Assert-Runtime (([regex]::Matches($Fixture.Html, "<h1\b")).Count -eq 1) "Fixture must render exactly one H1: $($Fixture.Slug)"
    Assert-Runtime (-not [regex]::IsMatch($Fixture.Html, "\sstyle=", "IgnoreCase")) "Fixture renders an inline style attribute: $($Fixture.Slug)"
    foreach ($ForbiddenPublicToken in @("claim_keys", "source_ids", "source_asset_id")) {
        Assert-Runtime (-not $Fixture.Html.Contains($ForbiddenPublicToken)) "Fixture exposes internal provenance token ${ForbiddenPublicToken}: $($Fixture.Slug)"
    }
}

foreach ($SurvivalToken in @("stc-content-block--quick-answer", "stc-content-block--key-takeaways", "stc-content-block--steps", "stc-content-block--warning", "stc-content-block--comparison", "stc-content-block--faq", "stc-dynamic-component--affiliate")) {
    Assert-Runtime ($SurvivalHtml.Contains($SurvivalToken)) "Survival fixture is missing component: $SurvivalToken"
}
foreach ($CityToken in @("stc-content-block--quick-facts", "stc-content-block--checklist", "stc-content-block--steps", "stc-content-block--faq", "stc-dynamic-component--planner")) {
    Assert-Runtime ($CityHtml.Contains($CityToken)) "City fixture is missing component: $CityToken"
}
foreach ($AttractionToken in @("stc-content-block--quick-facts", "stc-content-block--steps", "stc-content-block--warning", "stc-content-block--faq", "stc-dynamic-component--ticket", "data-stc-ticket-tool")) {
    Assert-Runtime ($AttractionHtml.Contains($AttractionToken)) "Attraction fixture is missing component: $AttractionToken"
}

Assert-Runtime ([regex]::IsMatch($SurvivalHtml, 'rel="sponsored nofollow noopener"')) "Affiliate CTA is missing sponsored nofollow noopener."
Assert-Runtime ([regex]::IsMatch($CityHtml, 'rel="sponsored nofollow noopener"')) "Planner CTA is missing sponsored nofollow noopener."
Assert-Runtime (([regex]::Matches($AttractionHtml, "data-stc-ticket-tool")).Count -eq 1) "Ticket Reminder did not delegate exactly one Plugin form."
Assert-Runtime ([regex]::IsMatch($AttractionHtml, '<option(?=[^>]*value="forbidden-city")(?=[^>]*selected)[^>]*>', "IgnoreCase")) "Ticket Reminder did not preselect the Plugin-owned Forbidden City option."

foreach ($MediaToken in @("stc-content-image--context", "srcset=", "sizes=", 'loading="lazy"', 'decoding="async"', "width=", "height=", "wp-element-caption", 'alt="Visitors approaching the Forbidden City in Beijing"')) {
    Assert-Runtime ($AttractionHtml.Contains($MediaToken)) "Responsive Media output is missing: $MediaToken"
}

$HeadingMatches = [regex]::Matches($AttractionHtml, '<h2\b[^>]*id="([^"]+)"[^>]*>')
$HeadingIds = New-Object System.Collections.Generic.HashSet[string]
foreach ($HeadingMatch in $HeadingMatches) {
    [void]$HeadingIds.Add($HeadingMatch.Groups[1].Value)
}
Assert-Runtime ($HeadingMatches.Count -ge 5) "Attraction fixture does not render server-side H2 IDs."
Assert-Runtime ($HeadingIds.Count -eq $HeadingMatches.Count) "Attraction fixture renders duplicate H2 IDs."
Assert-Runtime ($AttractionHtml.Contains('id="forbidden-city-ticket-reminder"')) "Explicit Ticket Reminder anchor was not preserved."

$SurvivalGuideType = $SurvivalPost.meta.PSObject.Properties["_stc_guide_type"].Value
$AttractionGuideType = $AttractionPost.meta.PSObject.Properties["_stc_guide_type"].Value
$CityGuideType = $CityPost.meta.PSObject.Properties["_stc_guide_type"].Value
Assert-Runtime ($SurvivalGuideType -eq "survival-kit") "Survival fixture REST guide type metadata is wrong."
Assert-Runtime ($AttractionGuideType -eq "attraction-guide") "Attraction fixture REST guide type metadata is wrong."
Assert-Runtime ([string]::IsNullOrEmpty([string]$CityGuideType)) "Historical City fixture should rely on category fallback without explicit guide metadata."
Assert-Runtime ($CityHtml.Contains("stc-single--city-guide")) "Historical category-only City fixture did not retain the City Guide shell."

$RendererRuntime = Get-Content -LiteralPath (Join-Path $Root "wp-content/themes/solo-to-china/inc/content-renderers.php") -Raw
Assert-Runtime ($RendererRuntime.Contains("do_shortcode")) "Ticket Reminder is not delegated through the Plugin shortcode."
Assert-Runtime (-not $RendererRuntime.Contains("stc_tools_get_attractions")) "Theme renderer copied Plugin attraction data access."
Assert-Runtime (-not $RendererRuntime.Contains("booking_lead_days")) "Theme renderer copied Plugin booking calculations."

if ($ParentOnly) {
    Assert-Runtime (-not $AttractionHtml.Contains("solo-to-china-child")) "ParentOnly preview still loads the Child Theme."
} else {
    Assert-Runtime ($AttractionHtml.Contains("stc-child-content-components-css")) "Child Theme Content Component stylesheet is not loaded."
}

if ($Failures.Count -gt 0) {
    throw ($Failures -join [Environment]::NewLine)
}

$Mode = if ($ParentOnly) { "Parent Theme fallback" } else { "Child Theme integration" }
Write-Host "SoloToChina Content Runtime verification passed: $Mode at $BaseUrl."
