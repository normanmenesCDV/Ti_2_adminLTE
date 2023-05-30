<?php
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

function sanitizeInput($input){
    $input = htmlentities(stripslashes(trim($input)));
    return $input;
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    session_start();
    //print_r($_POST);
    $requiredFields = ["firstName", "lastName", "email", "confirm_email", "pass", "confirm_pass", "birthday", "city_id"];

    $errors = [];
    foreach ($requiredFields as $key => $value){
        //echo "$key: $value<br>";
        if (empty($_POST[$value])){
            //echo "$value<br>";
            $errors[] = "Pole <b>$value</b> jest wymagane";
        }
    }

    if ($_POST["email"] != $_POST["confirm_email"])
        $errors[] = "Adresy email muszą być identyczne";

    if ($_POST["pass"] != $_POST["confirm_pass"])
        $errors[] = "Hasła muszą być identyczne";

    if (!isset($_POST["terms"]))
        $errors[] = "Zatwierdź regulamin";

    if (!empty($errors)){
        // print_r($errors);
        // echo "test: ".$errors[0];
        //print_r($errors);
        //$_SESSION['error_message'] = implode(", ", $errors);
        $_SESSION['error_message'] = implode("<br>", $errors);
        //echo $_SESSION['error_message'];
        echo "<script>history.back();</script>";
        exit();
    }

    /*
    foreach ($requiredFields as $value){
        //echo $_POST[$value]." ==> ";
        ${$value} = sanitizeInput($_POST[$value]);
        //echo $firstName."<br>";
    }*/

    //echo $firstName;

    foreach ($_POST as $key => $value){
        //echo $_POST[$value]." ==> ";
        ${$key} = sanitizeInput($value);
        //echo $firstName."<br>";
    }
    //echo $firstName;

    require_once "./connect.php";
    
    $stmt = $conn->prepare("INSERT INTO `users` (`email`, `password`, `firstName`, `lastName`, `birthday`, `city_id`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, current_timestamp());");

    $pwd_hashed = password_hash($_POST["pass"], PASSWORD_ARGON2ID); #hashowanie hasła
    $stmt->bind_param('sssssi', $_POST["email"], $pwd_hashed, $_POST["firstName"], $_POST["lastName"], $_POST["birthday"], $_POST["city_id"]);

    $stmt->execute();

    //echo $stmt->affected_rows;

    }else{
        header("location: ../pages/register.php");
    }