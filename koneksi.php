<?php
$host = "sql209.infinityfree.com";
$user = "if0_39149676";
$password = "Projectweb1234"; 
$database =
"if0_39149676_tugas_project_akhir";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("koneksi database gagal" .
mysqli_connect_error());    
}