$path = "c:\xampp\htdocs\fee-management\public\admin\index.html"
$lines = Get-Content $path
$newLines = @()
$skip = $false
for ($i = 0; $i -lt $lines.Count; $i++) {
    if ($lines[$i] -match "function exportAdminCSV\(\) \{") {
        # Check if it's the first one (the broken one)
        if ($lines[$i+4] -match "csv \+= ") {
            $skip = $true
        }
    }
    
    if (-not $skip) {
        $newLines += $lines[$i]
    }
    
    if ($skip -and $lines[$i] -match "document\.getElementById\('search-input-REMOVED'\)") {
        $skip = $false
    }
}
$newLines | Set-Content $path
