param(
    [ValidateRange(1024, 65535)]
    [int]$Port = 9400,

    [switch]$ParentOnly,

    [switch]$Editor
)

$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent $PSScriptRoot
if ($ParentOnly -and $Editor) {
    throw "Choose either -ParentOnly or -Editor, not both."
}

$BlueprintName = if ($ParentOnly) {
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
$NpxCommand = Get-Command npx -ErrorAction SilentlyContinue

if (-not $NpxCommand) {
    throw "npx is required. Install Node.js 20.18 or newer, then run this script again."
}

Write-Host "Starting SoloToChina WordPress Playground at http://127.0.0.1:$Port using $BlueprintName"

$PlaygroundArguments = @(
    "--yes"
    "@wp-playground/cli@latest"
    "server"
    "--port=$Port"
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
)

if (-not $ParentOnly) {
    $PlaygroundArguments += @(
        "--mount-dir"
        $ChildTheme
        "/wordpress/wp-content/themes/solo-to-china-child"
    )
}

& $NpxCommand.Source @PlaygroundArguments
