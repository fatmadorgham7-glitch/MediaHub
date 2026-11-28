[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [ValidateNotNullOrEmpty()]
    [string] $DestinationDirectory,

    [string] $DatabasePath,

    [string] $ProjectRoot = (Split-Path -Parent $PSScriptRoot)
)

$ErrorActionPreference = 'Stop'

$projectPath = (Resolve-Path -LiteralPath $ProjectRoot).ProviderPath.TrimEnd('\')
$destinationPath = [System.IO.Path]::GetFullPath($DestinationDirectory)
$databasePath = if ([string]::IsNullOrWhiteSpace($DatabasePath)) {
    Join-Path $projectPath 'database\database.sqlite'
} else {
    (Resolve-Path -LiteralPath $DatabasePath).ProviderPath
}

if (-not (Test-Path -LiteralPath $databasePath -PathType Leaf)) {
    throw "SQLite database not found: $databasePath"
}

$projectPrefix = $projectPath + '\'
if ($destinationPath.Equals($projectPath, [System.StringComparison]::OrdinalIgnoreCase) -or
    $destinationPath.StartsWith($projectPrefix, [System.StringComparison]::OrdinalIgnoreCase)) {
    throw 'Choose a backup directory outside the project directory.'
}

New-Item -ItemType Directory -Path $destinationPath -Force | Out-Null
$archiveName = 'mediahub-{0}.zip' -f (Get-Date -Format 'yyyyMMdd-HHmmss-fff')
$archivePath = Join-Path $destinationPath $archiveName
$stagingPath = Join-Path ([System.IO.Path]::GetTempPath()) ('mediahub-backup-{0}' -f [guid]::NewGuid().ToString('N'))

try {
    $stagingDatabase = Join-Path $stagingPath 'database'
    New-Item -ItemType Directory -Path $stagingDatabase -Force | Out-Null
    Copy-Item -LiteralPath $databasePath -Destination (Join-Path $stagingDatabase 'database.sqlite')

    foreach ($directory in @('public', 'private')) {
        $source = Join-Path $projectPath "storage\app\$directory"
        $stagingMedia = Join-Path $stagingPath "storage\app\$directory"
        New-Item -ItemType Directory -Path $stagingMedia -Force | Out-Null

        if (Test-Path -LiteralPath $source -PathType Container) {
            Get-ChildItem -LiteralPath $source -Force |
                Copy-Item -Destination $stagingMedia -Recurse -Force
        }
    }

    Add-Type -AssemblyName System.IO.Compression.FileSystem
    [System.IO.Compression.ZipFile]::CreateFromDirectory(
        $stagingPath,
        $archivePath,
        [System.IO.Compression.CompressionLevel]::Optimal,
        $false
    )

    Write-Output "Backup created: $archivePath"
} finally {
    if (Test-Path -LiteralPath $stagingPath -PathType Container) {
        Remove-Item -LiteralPath $stagingPath -Recurse -Force
    }
}
