<?php
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

require_once "./connect.php";

$stmt = $conn->prepare("INSERT INTO users (email, city_id, firstName, lastName, birthday, password, created_at) VALUES (?, ?, ?, ?, ?, ?, current_timestamp());");

$pwd_hashed = password_hash($_POST["pass"], PASSWORD_ARGON2ID); #hashowanie hasła
$stmt->bind_param('sissss', $_POST["email"], $_POST["city_id"], $_POST["firstName"], $_POST["lastName"], $_POST["birthday"], $pwd_hashed);

$stmt->execute();

echo $stmt->affected_rows;