<?php
   include("../database.php");
   session_start();
   if (isset($_SESSION['employeeId'])) {
        header("location:admin.php");
   }
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form 
      $myusername = mysqli_real_escape_string($db,$_POST['email']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']); 
      
      $sql = "SELECT * FROM employee WHERE EEmail = '$myusername' and EPassword = '$mypassword'";
      $result = mysqli_query($db,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $active = $row['active'];
      
      $count = mysqli_num_rows($result);
      
      // If result matched $myusername and $mypassword, table row must be 1 row
		
      if($count == 1) {
         $_SESSION["employeeId"] = $myusername;
         header("Location: welcome_employee.php");
      }else {
        echo "Your Login Name or Password is invalid";
      }
   }
?>
<html>
   
   <head>
      <title>Employee Login Page</title>
   </head>
   
   <body>
	
     <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                  Email: <input type = "text" name = "email"> <br />
                  Password: <input type = "password" name = "password"><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>
               <a href="employee.php"> Register here </a>
   </body>
</html>
