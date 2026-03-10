<?php
$files = [
    'c:/xampp/htdocs/fee-management/admin/index.php' => [
        "db.collection('students').get()" => "db.collection('students').limit(50).get()"
    ],
    'c:/xampp/htdocs/fee-management/admin/students_list.php' => [
        's.name' => 's.name LIMIT 50'
    ],
    'c:/xampp/htdocs/fee-management/public/admin/index.html' => [
        'limit(100)' => 'limit(50)'
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
