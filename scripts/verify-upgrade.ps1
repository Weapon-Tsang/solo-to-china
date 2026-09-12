#requires -Version 7.0
param([string]$BaseUrl = "", [switch]$ParentOnly)
$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
Push-Location $Root
try {
    foreach ($check in @('verify-page-architecture.ps1','verify-component-registry.ps1','verify-content-contract.ps1','verify-web-tools.ps1','verify-project.ps1')) {
        & pwsh -NoProfile -File (Join-Path $PSScriptRoot $check)
        if ($LASTEXITCODE -ne 0) { throw "Failed: $check" }
    }
    & python (Join-Path $PSScriptRoot 'verify-experience-contracts.py')
    if ($LASTEXITCODE -ne 0) { throw 'Experience schema/image validation failed' }
    if ($BaseUrl) {
        $runtimeArgs = @('-NoProfile','-File',(Join-Path $PSScriptRoot 'verify-content-runtime.ps1'),'-BaseUrl',$BaseUrl)
        if ($ParentOnly) { $runtimeArgs += '-ParentOnly' }
        & pwsh @runtimeArgs
        if ($LASTEXITCODE -ne 0) { throw 'Content runtime failed' }
        & pwsh -NoProfile -File (Join-Path $PSScriptRoot 'verify-web-tools-runtime.ps1') -BaseUrl $BaseUrl
        if ($LASTEXITCODE -ne 0) { throw 'Tools runtime failed' }
    }
    Write-Host 'Upgrade static/runtime checks passed. Run browser suites separately; see docs/qa/frontend-upgrade-v1.md.'
} finally { Pop-Location }
