$path = "..\public\admin\index.html"
$content = Get-Content $path -Raw
# Add scrolling to form-grid and padding
$content = $content -replace '<div class="form-grid">', '<div class="form-grid" style="max-height: 400px; overflow-y: auto; padding-right: 0.5rem; margin-bottom: 1rem;">'
# Update the button text to be more descriptive and ensure it's not "Add Student" if it's potentially misleading
$content = $content -replace 'Add Student\s*</button>', 'Save Student Details</button>'
# Increase modal width slightly to prevent squishing
$content = $content -replace '<div class="modal-content" style="max-width:580px;">', '<div class="modal-content" style="max-width:620px;">'
Set-Content $path $content
