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
   <!-- Latest compiled and minified CSS -->
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

   <!-- Optional theme -->
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

   <!-- Latest compiled and minified JavaScript -->
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
   <link rel="stylesheet" href="../style/login.css" type="text/css">
</head>
   
<body>
 <div class="container">
   <div class="row">
      <div class="col-md-12">
         
         <div class="wrap">
            <p class="form-title">Sign In</p>
            <form class="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" placeholder="Email"  name = "email"/>
            <input type="password" placeholder="Password" name = "password"/>
            <input type="submit" value="submit" class="btn btn-success btn-sm" />
            <div class="remember-forgot">
            <div class="row">
            <div class="col-md-8">
               <a href="employee.php"> Register here </a>
            </div>
            </div>
            </div>
            </form>
         </div>
       </div>
      </div>
   </div>
</body>
</html>
