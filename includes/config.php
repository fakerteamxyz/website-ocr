<?php
// includes/config.php

$servername = "localhost";
$username = "root";      // Nama pengguna default XAMPP
$password = "";          // Password default XAMPP adalah kosong
$dbname = "ocr_db";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>