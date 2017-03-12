<?php
session_start();
include("database.php");
if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

if (!isset($_SESSION['sqlRequest'])){
    $_SESSION['sqlRequest'] = "SELECT * FROM Arrange;";
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
        <style type="text/css">
            tr.header
            {
                font-weight:bold;
            }
            tr.alt
            {
                background-color: #777777;
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function(){
               $('.striped tr:even').addClass('alt');
            });
        </script>
        <title></title>
    </head>
    <body>

Hello <?= $_SESSION['username']?>
<br><br>

    <table class="striped">
        <tr class="header">
            <td>Showtime</td>
            <td>Location</td>
            <td>Title</td>
            <td>Seats</td>
            <td></td>
        </tr>
        <?php
           $i = 0;
           $sql = $_SESSION['sqlRequest'];
           $query = mysqli_query($db,$sql);
           while ($row = mysqli_fetch_array($query)) {
               $class = ($i == 0) ? "" : "alt";
               echo "<tr class=\"".$class."\">";
               echo "<td>".$row[Showtime]."</td>";
               echo "<td>".$row[Location]."</td>";
               echo "<td>".$row[Name]."</td>";
               echo "<td>".$row[SeatsLeft]."</td>";
               echo "<td>BUY NOW!</td>";
               echo "</tr>";
               $i = ($i==0) ? 1:0;
           }

        ?>
    </table>
    <br><br>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                  Date: <input type = "date" name = "datetime"> <br />
                  Movie Title: <input type = "text" name = "movieTitle"><br />
                  <input type = "submit" value = " Submit "/><br />
    </form>
<a href="logout.php">Logout</a>
    </body>
</html>