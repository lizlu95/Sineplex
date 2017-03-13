<?php

    include("database.php");
    session_start();
    
    if (isset($_SESSION['username'])) {
        header("location:main.php");
    }
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
      $sql = "INSERT INTO customer(CEmail, Age, CPassword) VALUES ('" . $_POST["email"] . "','" . $_POST["age"] . "','" . $_POST["password"] . "')";
    if (mysqli_query($db, $sql)) {
    $_SESSION["username"] =  $_POST["email"];

    header("location:main.php"); 
} else {
    echo "Email already used, try another";
}

mysqli_close($db);
}
?>
