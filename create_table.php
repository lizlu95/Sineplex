<?php
    include("database.php");

// Check connection
if (!db ) {
    die("Connection failed: " . mysqli_connect_error());
} 

// sql to create table
$sql = "CREATE TABLE users(
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
name VARCHAR(30) NOT NULL,
email VARCHAR(50),
reg_date TIMESTAMP,
password VARCHAR(30)
)";

if (mysqli_query($db, $sql)) {
    echo "Table MyGuests created successfully";
} else {
    echo "Error creating table: " . mysqli_error($db);
}

mysqli_close($db);
?>