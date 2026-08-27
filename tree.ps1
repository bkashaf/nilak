# Project root
Set-Location "C:\xampp\htdocs\nilak"

# Important paths
$paths = @(
    "app\Http\Controllers\Admin",
    "app\Models",
    "resources\views\themes\default",
    "public\js",
    "database"
)

# Output file
$outFile = "nilak-tree.txt"

# Remove old file if exists
if (Test-Path $outFile) { Remove-Item $outFile }

foreach ($p in $paths) {
    $full = Join-Path (Get-Location) $p
    Add-Content $outFile "==============================="
    Add-Content $outFile "PATH: $p"
    Add-Content $outFile "==============================="
    if (Test-Path $full) {
        Get-ChildItem $full -Recurse | ForEach-Object {
            $relative = $_.FullName.Substring($full.Length).TrimStart('\')
            $indent = $relative.Split([IO.Path]::DirectorySeparatorChar).Count - 1
            (" " * ($indent * 2)) + $_.Name
        } | Add-Content $outFile
    } else {
        Add-Content $outFile "PATH NOT FOUND"
    }
    Add-Content $outFile ""
}

Write-Host "Tree structure saved to $outFile"
