<?php 
echo "Admin: " . password_hash('Admin@123', PASSWORD_DEFAULT) . "\n"; 
echo "Teacher: " . password_hash('Teacher@123', PASSWORD_DEFAULT) . "\n"; 
echo "Student: " . password_hash('Student@123', PASSWORD_DEFAULT) . "\n"; 
?>
