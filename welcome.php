<?php

    include("database.php");
    session_start();

    if($_SERVER["REQUEST_METHOD"] == "POST") {
      $sql = "INSERT INTO users(name, email, password) VALUES ('" . $_POST["name"] . "','" . $_POST["email"] . "','" . $_POST["password"] . "')";

    
if (mysqli_query($db, $sql)) {
    echo "Welcome " . $_POST["name"];
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($db);
}

mysqli_close($db);
}
?>
