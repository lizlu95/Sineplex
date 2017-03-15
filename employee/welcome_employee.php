<?php

    include("../database.php");
    session_start();
    
    if (isset($_SESSION['employeeId'])) {
        header("location:admin.php");
    }
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
      $sql = "INSERT INTO employee(EEmail, EPassword) VALUES ('" . $_POST["email"] . "','" . $_POST["password"] . "')";
    if (mysqli_query($db, $sql)) {
    $_SESSION["employeeId"] =  $_POST["email"];

    header("location:admin.php"); 
} else {
    echo "Email already used, try another";
}

mysqli_close($db);
}
?>
