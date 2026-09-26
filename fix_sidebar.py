import re
with open('C:\\xampp\\htdocs\\1\\includes\\sidebar.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add php snippet at the top
header = "<?php $current_page = $_SERVER['REQUEST_URI']; ?>\n<nav id=\"sidebar\" class=\"bg-dark text-white\">"
content = content.replace('<nav id="sidebar" class="bg-dark text-white">', header)

# Remove hardcoded class="active" on Dashboard
content = content.replace('<li class="active">\n                <a href="<?= BASE_URL ?>/admin/dashboard.php">', '<li class="<?= strpos($current_page, \'/admin/dashboard.php\') !== false ? \'active\' : \'\' ?>">\n                <a href="<?= BASE_URL ?>/admin/dashboard.php">')

content = content.replace('<li class="active">\n                <a href="<?= BASE_URL ?>/teacher/dashboard.php">', '<li class="<?= strpos($current_page, \'/teacher/dashboard.php\') !== false ? \'active\' : \'\' ?>">\n                <a href="<?= BASE_URL ?>/teacher/dashboard.php">')

content = content.replace('<li class="active">\n                <a href="<?= BASE_URL ?>/student/dashboard.php">', '<li class="<?= strpos($current_page, \'/student/dashboard.php\') !== false ? \'active\' : \'\' ?>">\n                <a href="<?= BASE_URL ?>/student/dashboard.php">')

# Now add dynamic active classes to all other <li> tags
def replacer(match):
    href = match.group(1)
    # Extract path after BASE_URL
    path = href.split('?>')[1].strip()
    return f'<li class="<?= strpos($current_page, \'{path}\') !== false ? \'active\' : \'\' ?>"><a href="{href}"'

content = re.sub(r'<li><a href="(.*?)"', replacer, content)

with open('C:\\xampp\\htdocs\\1\\includes\\sidebar.php', 'w', encoding='utf-8') as f:
    f.write(content)
