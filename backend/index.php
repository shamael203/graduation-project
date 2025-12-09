<?php
// تضمين الهيدر
include 'header.php';

// إعادة التوجيه مباشرةً إلى الصفحة الرئيسية
header("Location: home.php");
exit;

// تضمين الفوتر (لن يتم الوصول إليه بسبب exit، لكن إذا أزلت exit يمكن عرضه)
include 'footer.php';
?>
