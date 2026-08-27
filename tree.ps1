# Project root
Set-Location "C:\xampp\htdocs\nilak"

# Important paths
$paths = @(
    "app\Http\Controllers",
    "app\Models",
    "routes",
    "config",
    "resources\views",
    "resources\js",
    "resources\css",
    "resources\lang",
    "public",
    "database"
)

# Output file
$outFile = "nilak-tree.txt"

# Remove old file if exists
if (Test-Path $outFile) { Remove-Item $outFile }

# Simple ASCII tree drawer
function Draw-Tree($basePath) {
    Get-ChildItem $basePath -Recurse | ForEach-Object {
        $relative = $_.FullName.Substring($basePath.Length).TrimStart('\')
        $parts = $relative.Split([IO.Path]::DirectorySeparatorChar)
        $indent = $parts.Count - 1
        $prefix = (" " * ($indent * 2)) + "|-- "
        $prefix + $_.Name
    }
}

foreach ($p in $paths) {
    $full = Join-Path (Get-Location) $p
    Add-Content $outFile "==============================="
    Add-Content $outFile "PATH: $p"
    Add-Content $outFile "==============================="
    if (Test-Path $full) {
        Draw-Tree $full | Add-Content $outFile
    } else {
        Add-Content $outFile "PATH NOT FOUND"
    }
    Add-Content $outFile ""
}

Write-Host "Tree structure saved to $outFile"
