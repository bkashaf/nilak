$excludeDirs = @('.git','vendor','node_modules','bootstrap/cache','storage/framework','storage/logs','public/build','backup_20260519_233422')
$root = Get-Location
$all = Get-ChildItem -Recurse -Force | Where-Object {
  $rel = $_.FullName.Replace($root.Path + '\','').Replace('\','/')
  -not ($excludeDirs | ForEach-Object { $rel -eq $_ -or $rel.StartsWith($_ + '/') } | Where-Object { $_ })
}
$lines = New-Object System.Collections.Generic.List[string]
foreach ($item in $all) {
  $rel = $item.FullName.Replace($root.Path + '\','').Replace('\','/')
  if ($item.PSIsContainer) { $lines.Add($rel + '/') } else { $lines.Add($rel) }
}
$lines.Sort()
$header = @('Project tree (clean, auto-generated)','Excluded: .git, vendor, node_modules, bootstrap/cache, storage/framework, storage/logs, public/build, backup_20260519_233422','')
($header + $lines) | Set-Content -Encoding UTF8 project_tree_clean.txt
