$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent $PSScriptRoot
$Dist = Join-Path $Root "dist"
$ThemeSource = Join-Path $Root "wp-content/themes/solo-to-china"
$ChildThemeSource = Join-Path $Root "wp-content/themes/solo-to-china-child"
$PluginSource = Join-Path $Root "wp-content/plugins/solo-to-china-tools"
$ThemeZip = Join-Path $Dist "solo-to-china-theme.zip"
$ChildThemeZip = Join-Path $Dist "solo-to-china-child-theme.zip"
$PluginZip = Join-Path $Dist "solo-to-china-tools-plugin.zip"
$Manifest = Join-Path $Dist "release-manifest.txt"

if (-not (Test-Path -LiteralPath $ThemeSource -PathType Container)) {
    throw "Theme source is missing: wp-content/themes/solo-to-china"
}

if (-not (Test-Path -LiteralPath $ChildThemeSource -PathType Container)) {
    throw "Child Theme source is missing: wp-content/themes/solo-to-china-child"
}

if (-not (Test-Path -LiteralPath $PluginSource -PathType Container)) {
    throw "Plugin source is missing: wp-content/plugins/solo-to-china-tools"
}

New-Item -ItemType Directory -Force -Path $Dist | Out-Null

if (Test-Path -LiteralPath $ThemeZip) {
    Remove-Item -LiteralPath $ThemeZip -Force
}

if (Test-Path -LiteralPath $ChildThemeZip) {
    Remove-Item -LiteralPath $ChildThemeZip -Force
}

if (Test-Path -LiteralPath $PluginZip) {
    Remove-Item -LiteralPath $PluginZip -Force
}

if (Test-Path -LiteralPath $Manifest) {
    Remove-Item -LiteralPath $Manifest -Force
}

Compress-Archive -Path $ThemeSource -DestinationPath $ThemeZip -Force
Compress-Archive -Path $ChildThemeSource -DestinationPath $ChildThemeZip -Force
Compress-Archive -Path $PluginSource -DestinationPath $PluginZip -Force

$ThemeHash = (Get-FileHash -LiteralPath $ThemeZip -Algorithm SHA256).Hash
$ChildThemeHash = (Get-FileHash -LiteralPath $ChildThemeZip -Algorithm SHA256).Hash
$PluginHash = (Get-FileHash -LiteralPath $PluginZip -Algorithm SHA256).Hash

@(
    "SoloToChina release artifacts",
    "Generated: $((Get-Date).ToString('yyyy-MM-dd HH:mm:ss zzz'))",
    "",
    "Theme version: 0.22.0",
    "Theme: solo-to-china-theme.zip",
    "Theme SHA256: $ThemeHash",
    "",
    "Child Theme version: 0.3.0",
    "Child Theme: solo-to-china-child-theme.zip",
    "Child Theme SHA256: $ChildThemeHash",
    "",
    "Plugin version: 0.21.0",
    "Plugin: solo-to-china-tools-plugin.zip",
    "Plugin SHA256: $PluginHash"
) | Set-Content -LiteralPath $Manifest -Encoding UTF8

Write-Host "Created dist/solo-to-china-theme.zip"
Write-Host "Created dist/solo-to-china-child-theme.zip"
Write-Host "Created dist/solo-to-china-tools-plugin.zip"
Write-Host "Created dist/release-manifest.txt"
