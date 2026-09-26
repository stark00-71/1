<?php
$file = 'C:\\xampp\\htdocs\\1\\includes\\sidebar.php';
$content = file_get_contents($file);

$header = "<?php \$current_page = \$_SERVER['REQUEST_URI']; ?>\n<nav id=\"sidebar\" class=\"bg-dark text-white\">";
$content = str_replace('<nav id="sidebar" class="bg-dark text-white">', $header, $content);

$content = str_replace('<li class="active">
                <a href="<?= BASE_URL ?>/admin/dashboard.php">', '<li class="<?= strpos($current_page, \'/admin/dashboard.php\') !== false ? \'active\' : \'\' ?>">
                <a href="<?= BASE_URL ?>/admin/dashboard.php">', $content);

$content = str_replace('<li class="active">
                <a href="<?= BASE_URL ?>/teacher/dashboard.php">', '<li class="<?= strpos($current_page, \'/teacher/dashboard.php\') !== false ? \'active\' : \'\' ?>">
                <a href="<?= BASE_URL ?>/teacher/dashboard.php">', $content);

$content = str_replace('<li class="active">
                <a href="<?= BASE_URL ?>/student/dashboard.php">', '<li class="<?= strpos($current_page, \'/student/dashboard.php\') !== false ? \'active\' : \'\' ?>">
                <a href="<?= BASE_URL ?>/student/dashboard.php">', $content);

$content = preg_replace_callback('/<li><a href="([^"]+)"/', function($matches) {
    $href = $matches[1];
    $path = trim(explode('?>', $href)[1]);
    return "<li class=\"<?= strpos(\$current_page, '$path') !== false ? 'active' : '' ?>\"><a href=\"$href\"";
}, $content);

file_put_contents($file, $content);
echo "Sidebar updated successfully!";
?>
