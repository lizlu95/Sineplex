<?php
session_start();
include("database.php");
if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

if (!isset($_SESSION['sqlRequest'])){
    $_SESSION['sqlRequest'] = "SELECT a.Showtime, t.Name as TName, a.Location, a.Name, a.SeatsLeft FROM arrange as a, theater as t WHERE a.Location = t.Location;";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // assume this post came from this page and not some external source...
  // begin constructing sql statement for movie filtering
  $_SESSION['sqlRequest'] = "SELECT a.Showtime, t.Name as TName, a.Location, a.Name, a.SeatsLeft FROM arrange as a, theater as t WHERE a.Location = t.Location and 1 ";

  // date filter
  if (isset($_POST['fromDateCb']) and !isset($_POST['toDateCb'])){
     $_SESSION['sqlRequest'] =  $_SESSION['sqlRequest'] . " AND Showtime = '" .  date('Y-m-d H:i:s', strtotime($_POST['fromDate'])) . "'";
  } 
  elseif (isset($_POST['fromDateCb']) and isset($_POST['toDateCb'])){
     $_SESSION['sqlRequest'] =  $_SESSION['sqlRequest'] . " AND Showtime >= '" .  date('Y-m-d H:i:s', strtotime($_POST['fromDate'])) . "' AND Showtime <= '" .  date('Y-m-d H:i:s', strtotime($_POST['toDate'])) . "'";
  } 

  //movie name
  if (isset($_POST["movieNameCb"])){
    $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . " AND a.Name LIKE '%" . $_POST['movieName'] . "%'";
  }

  //theater location
  if (isset($_POST["locationName"])){
    $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . " AND a.Location LIKE '%" . $_POST['locationName'] . "%'";
  }

  //theater name
    if (isset($_POST["theaterNameCb"])){
    $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . " AND t.Name LIKE '%" . $_POST['theaterName'] . "%'";
  }
  $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . " and 1;";
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

Hello <?= $_SESSION['username']?> <br>
Query is <?= $_SESSION['sqlRequest']?> <br>

    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                  <input type="checkbox" name="fromDateCb" checked=true value="Yes" /> (from/on) Date: <input type = "datetime-local" name = "fromDate"> <br />
                  <input type="checkbox" name="toDateCb" value="Yes" /> (to) Date: <input type = "datetime-local" name = "toDate"> <br />
                  <input type="checkbox" name="movieNameCb" value="Yes" /> Movie Title: <input type = "text" name = "movieName"><br />
                  <input type="checkbox" name="locationCb" value="Yes" /> Theatre Location: <input type = "text" name = "locationName"><br />
                  <input type="checkbox" name="theaterNameCb" value="Yes" /> Theatre Name: <input type = "text" name = "theaterName"><br />
                  <input type = "submit" value = " Submit "/><br />
    </form>
<a href="logout.php">Logout</a>
<br><br>

    <table class="striped">
        <tr class="header">
            <td>Showtime</td>
            <td>Theater</td>
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
               echo "<td>".$row["Showtime"]."</td>";
               echo "<td>".$row["TName"]."</td>";
               echo "<td>".$row["Location"]."</td>";
               echo "<td>".$row["Name"]."</td>";
               echo "<td>".$row["SeatsLeft"]."</td>";
               echo "<td>BUY NOW!</td>";
               echo "</tr>";
               $i = ($i==0) ? 1:0;
           }

        ?>
    </table>
    <br><br>

    </body>
</html>