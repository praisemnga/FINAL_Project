<?php
$mysqli = new mysqli("sql209.infinityfree.com", "if0_39149676", "Projectweb1234", "if0_39149676_tugas_project_akhir");
if ($mysqli->connect_error) {
    die("Gagal koneksi MySQL: " . $mysqli->connect_error);
}