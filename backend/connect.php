<?php
// connect.php

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = 'mysql';
$DB_NAME = 'book_exchange';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
