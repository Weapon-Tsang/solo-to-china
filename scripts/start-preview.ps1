param(
    [ValidateRange(1024, 65535)]
    [int]$Port = 9400,

    [switch]$ParentOnly,

    [switch]$Editor,

    [switch]$NoTools
)

$ErrorActionPreference = "Stop"
$PlaygroundCliVersion = "3.1.52"
$WordPressVersion = "6.8.3"

$Root = Split-Path -Parent $PSScriptRoot
if ($ParentOnly -and $Editor) {
    throw "Choose either -ParentOnly or -Editor, not both."
}

$BlueprintName = if ($NoTools) {
    "playground-no-tools-blueprint.json"
} elseif ($ParentOnly) {
    "playground-parent-blueprint.json"
} elseif ($Editor) {
    "playground-editor-blueprint.json"
} else {
    "playground-blueprint.json"
}
$Blueprint = Join-Path $PSScriptRoot $BlueprintName
$ParentTheme = Join-Path $Root "wp-content/themes/solo-to-china"
$ChildTheme = Join-Path $Root "wp-content/themes/solo-to-china-child"
$ToolsPlugin = Join-Path $Root "wp-content/plugins/solo-to-china-tools"
$LocalCredentialDirectory = Join-Path $Root "output/v3-preview"
New-Item -ItemType Directory -Force -Path $LocalCredentialDirectory | Out-Null
$LocalCredentialFile = Join-Path $LocalCredentialDirectory "wp-application-password.txt"
if (Test-Path -LiteralPath $LocalCredentialFile) { Remove-Item -LiteralPath $LocalCredentialFile }
$NpxCommand = Get-Command npx -ErrorAction SilentlyContinue

if (-not $NpxCommand) {
    throw "npx is required. Install Node.js 20.18 or newer, then run this script again."
}

Write-Host "Starting SoloToChina WordPress Playground at http://127.0.0.1:$Port using $BlueprintName"

$PlaygroundArguments = @(
    "--yes"
    "@wp-playground/cli@$PlaygroundCliVersion"
    "server"
    "--port=$Port"
    "--wp=$WordPressVersion"
    "--define-bool"
    "AUTOMATIC_UPDATER_DISABLED"
    "true"
    "--define-bool"
    "WP_AUTO_UPDATE_CORE"
    "false"
    "--define"
    "WP_ENVIRONMENT_TYPE"
    "local"
    "--blueprint=$Blueprint"
    "--mount-dir"
    $ParentTheme
    "/wordpress/wp-content/themes/solo-to-china"
    "--mount-dir"
    $ToolsPlugin
    "/wordpress/wp-content/plugins/solo-to-china-tools"
    "--mount-dir"
    $PSScriptRoot
    "/tmp/solo-to-china-scripts"
    "--mount-dir"
    $LocalCredentialDirectory
    "/tmp/solo-to-china-preview-credentials"
)

if (-not $ParentOnly) {
    $PlaygroundArguments += @(
        "--mount-dir"
        $ChildTheme
        "/wordpress/wp-content/themes/solo-to-china-child"
    )
}

& $NpxCommand.Source @PlaygroundArguments
if ($LASTEXITCODE -ne 0) {
    throw "WordPress Playground CLI $PlaygroundCliVersion exited with code $LASTEXITCODE."
}
