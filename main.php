<?php
session_start();
include("database.php");
if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

if (!isset($_SESSION['sqlRequest'])){
    $_SESSION['sqlRequest'] = "SELECT a.ArrangeId as ShowID, a.Showtime, t.Name as TheaterName, a.Location, a.Name, a.SeatsLeft as Seats FROM arrange as a, theater as t WHERE a.Location = t.Location;";
}
// buying ticket
if ($_SERVER["REQUEST_METHOD"] == "GET"){
  if (isset($_GET["buyTicket"])){
    $sql = "SELECT SeatsLeft from arrange where ArrangeId = " . $_GET["buyTicket"] . ";";
    $seatsQuery = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($seatsQuery);
    $seats =  $row[0];
    // constraint check done here but creation of table already has CHECK constraint
    if ($seats - $_GET["tickets"] >= 0){
      $Updatesql = "UPDATE arrange Set SeatsLeft = " . ($seats-$_GET["tickets"]) . " WHERE ArrangeId = " . $_GET["buyTicket"] . ";";
      mysqli_query($db, $Updatesql); 
      echo "Update query: " . $Updatesql . "<br>";
      for ($x = $_GET["tickets"]; $x > 0; $x--) {
        $sql = "INSERT into ticket(AuditoriumNo, CEmail, ArrangeId, SeatsNo) VALUES (1, '" . $_SESSION['username'] ."','" .$_GET["buyTicket"] ."', 1);";
        mysqli_query($db, $sql);
        echo "Insert query: " . $sql . "<br>";
      }
      header("location:index.php");
    }else{
      echo "ERROR: No Seats Available. <br>";
    }
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // assume this post came from this page and not some external source...
  // begin constructing sql statement for movie filtering
  // build selection/proj query
    $_SESSION['sqlRequest'] = "SELECT a.ArrangeId as ShowID";
    if (!isset($_POST['hideShowtimeCb'])){
      $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . ", a.Showtime";
    }
    if (!isset($_POST['hideTheaterNameCb'])){
      $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . ", t.Name as Theater";
    }
    if (!isset($_POST['hideLocationCb'])){
      $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . ", a.Location";
    }
    if (!isset($_POST['hideNameCb'])){
      $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . ", a.Name";
    }
    if (!isset($_POST['hideSeatsCb'])){
      $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . ", a.SeatsLeft as Seats";
    }
    if (isset($_POST['searchTickets'])) {
      $_SESSION['sqlRequest'] =  $_SESSION['sqlRequest'] . " FROM arrange as a, theater as t, ticket as ti WHERE a.Location = t.Location and ti.ArrangeId = a.ArrangeId and ti.CEmail LIKE '" . $_SESSION['username'] . "' and 1 ";
    } else {
       $_SESSION['sqlRequest'] =  $_SESSION['sqlRequest'] . " FROM arrange as a, theater as t WHERE a.Location = t.Location and 1 ";
    }
  
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
  if (isset($_POST["locationNameCb"])){
    $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . " AND a.Location LIKE '%" . $_POST['locationName'] . "%'";
  }

  //theater name
  if (isset($_POST["theaterNameCb"])){
    $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . " AND t.Name LIKE '%" . $_POST['theaterName'] . "%'";
  }

  //arrangeid name
  if (isset($_POST["showIdCb"])){
    $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . " AND a.ArrangeId = " . $_POST['showId'];
  }
  // end queries
  $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . " and 1;";
  if (isset($_POST['statsOperation'])) {
    if($_POST['statsOperation'] == "statsAvgSeats"){
        $_SESSION['sqlRequest'] = "SELECT a.ArrangeId as ShowID, t.Name as Theater, a.Name, a.SeatsLeft as Seats from arrange as a inner join theater as t on a.Location = t.Location where SeatsLeft ";
        if ($_POST['statsAvgSeatsOp'] == "greater"){
          $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . ">";
        }else
          $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . "<";
        $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . "= (SELECT avg(SeatsLeft) from arrange);";
    } elseif($_POST['statsOperation'] == "statsAvgDuration"){

        $_SESSION['sqlRequest'] = "SELECT m.Name, m.Duration, t.avg as Average from movie as m, (SELECT AVG(Duration) as avg from movie) as t where Duration ";
        if ($_POST['statsAvgDurationOp'] == "greater"){
          $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . ">";
        }else
          $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . "<";
        $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . "= (SELECT avg(Duration) as avg from movie);";
      }
  }
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
        <style type="text/css">
            tr
            {
                border: 1px #DDD solid;
                padding: 5px;
                cursor: pointer;

            }
            .selected
            {
                background-color: #000080;
                color: #FFF;
            }
        </style>
        <title></title>
    </head>
    <body>

Hello <?= $_SESSION['username']?> <br>
Query is <?= $_SESSION['sqlRequest']?> <br>

    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
        <input type="checkbox" name="movieNameCb"  checked=true value="Yes" /> Movie Title: <input type = "text" name = "movieName"><br />
        <input type="checkbox" name="fromDateCb" value="Yes" /> (from/on) Date: <input type = "datetime-local" name = "fromDate" value="2017-03-03T00:00"> <br />
        <input type="checkbox" name="toDateCb" value="Yes" /> (to) Date: <input type = "datetime-local" name = "toDate"> <br />
        <input type="checkbox" name="locationNameCb" value="Yes" /> Theatre Location: <input type = "text" name = "locationName"><br />
        <input type="checkbox" name="theaterNameCb" value="Yes" /> Theatre Name: <input type = "text" name = "theaterName"><br />
        <input type="checkbox" name="showIdCb" value="Yes" /> Show ID: <input type = "number" name = "showId"><br />
        <input type="checkbox" name="searchTickets" value="Yes" /> Search tickets instead? <br />
        <input type = "submit" value = " Submit "/> 
        <input type="checkbox" name="hideShowtimeCb" value="Yes" /> Hide Showtime?
        <input type="checkbox" name="hideTheaterNameCb" value="Yes" /> Hide TheaterName?
        <input type="checkbox" name="hideLocationCb" value="Yes" /> Hide Location?
        <input type="checkbox" name="hideNameCb" value="Yes" /> Hide Name?
        <input type="checkbox" name="hideSeatsCb" value="Yes" /> Hide Seats? <br /> 
        <input type='button' value='Buy ticket(s) for select show' onclick='fnselect(document.getElementsByName("numberOfTickets")[0].value)' /> # of ticket: <input type = "number" min="0" defaultValue=1 placeholder=1 name = "numberOfTickets"><br />
    </form>

    More search filters: <br/>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                    <input type="radio" name="statsOperation" value="statsAvgSeats" /> List Arranges with number of seats 
                         <select name="statsAvgSeatsOp">
                          <option value="greater">GREATER</option>
                          <option value="less">LESS</option>
                         </select>
                    than or equal to average
                    <br />
                    <input type="radio" name="statsOperation" value="statsAvgDuration" /> List Movies with duration 
                         <select name="statsAvgDurationOp">
                            <option value="greater">GREATER</option>
                            <option value="less">LESS</option>
                         </select>
                    than or equal to average duration
                    <br />
                  <input type = "submit" value = " Submit "/><br /> 
    </form>

<a href="logout.php">Logout</a>
<br><br>

    <table id="mainTable">
        <tr >
        <?php
          $sql = $_SESSION['sqlRequest'];
          $query = mysqli_query($db,$sql);
          $row = mysqli_fetch_array($query);
          if (sizeof($row) > 0){
            $the_keys = array_keys($row);
            for ($i=1; $i <sizeof($the_keys)+1; $i+=2){
              echo "<td><b>".$the_keys[$i]."</b></td>";
            }
          }
        ?>
        </tr>
        <?php
           $i = 0;
           $sql = $_SESSION['sqlRequest'];
           $query = mysqli_query($db,$sql);
           while ($row = mysqli_fetch_array($query)) {
            echo "<tr>";
            for ($k=0; $k <sizeof($row)/2; $k+=1){
              echo "<td>".$row[$k]."</td>";
            }
            $i = ($i==0) ? 1:0;
           }

        ?>
    </table>
    <br><br>
    <script>
    function highlight(e) {
        if (selected[0]) selected[0].className = '';
        e.target.parentNode.className = 'selected';
    }

    var table = document.getElementById('mainTable'),
    selected = table.getElementsByClassName('selected');
    table.onclick = highlight;

    function fnselect($tickets){
        if ($("tr.selected td:first" ).html() == null){
          alert("You must select a show!");
        }else{
          window.location.replace('main.php?buyTicket=' + $("tr.selected td:first" ).html() + '&tickets=' + Math.abs($tickets));
        }
        
    }
</script>
    </body>
</html>