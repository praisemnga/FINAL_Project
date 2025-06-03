<?php
$mysqli = new mysqli("localhost", "root", "", "tugas_project_akhir");
if ($mysqli->connect_error) {
    die("Gagal koneksi MySQL: " . $mysqli->connect_error);
}