<?php
   include("database.php");

	$query = "SELECT * FROM customer"; //You don't need a ; like you do in SQL
	$result = mysqli_query($db, $query);

	echo "<table>"; // start a table tag in the HTML

	while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
	    echo "<tr><td>" . $row['CEmail'] . "</td><td>" . $row['Age'] . "</td></tr>";  //$row['index'] the index here is a field name
	}

        echo "</table>"; //Close the table in HTML

        mysqli_close($db); //Make sure to close out the database connection
        echo "end";
        ?>