<?php
$files = [
    'c:/xampp/htdocs/fee-management/admin/index.php' => [
        "limit(50).get()" => "limit(30).get()"
    ],
    'c:/xampp/htdocs/fee-management/admin/students_list.php' => [
        'LIMIT 50' => 'LIMIT 30'
    ],
    'c:/xampp/htdocs/fee-management/public/admin/index.html' => [
        'limit(50)' => 'limit(30)'
    ]
];

foreach ($files as $file => $replaces) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        foreach ($replaces as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        file_put_contents($file, $content);
        echo "Updated $file\n";
    } else {
        echo "File not found: $file\n";
    }
}
?>
