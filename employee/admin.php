<?php
session_start();
include("../database.php");
if (!isset($_SESSION['employeeId'])) {
    header("location:index.php");
}
function runSql($sql, $db) {
    echo "Insert query: " . $sql . "<br/>";
    $result = mysqli_query($db, $sql);
    return $result;
}
if (!isset($_SESSION['sqlRequest'])){
    $_SESSION['sqlRequest'] = "SELECT a.ArrangeId as ShowID, a.Showtime, t.Name as TheaterName, a.Location, a.Name, a.SeatsLeft FROM arrange as a, theater as t WHERE a.Location = t.Location;";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // assume this post came from this page and not some external source...
  // begin constructing sql statement for movie filtering
  $_SESSION['sqlRequest'] = "SELECT a.ArrangeId as ShowID, a.Showtime, t.Name as TheaterName, a.Location, a.Name, a.SeatsLeft FROM arrange as a, theater as t WHERE a.Location = t.Location and 1;";
  if (isset($_POST['delOperation'])){
    // delete movie operation
    if ($_POST['delOperation'] == "deleteMovie"){
      $sql = "DELETE FROM movie where Name LIKE '" . $_POST['delMovie'] . "';";
      if (mysqli_query($db, $sql)) {
        echo "Deletion Query: " . $sql ."<br>";
      }else{
        echo "Unable to delete.";
      }
      // delete user
    } elseif ($_POST['delOperation'] == "delUser"){
      $sql = "DELETE FROM customer where CEmail LIKE '" . $_POST['delUser'] . "';";
      if (mysqli_query($db, $sql)) {
        echo "Deletion Query: " . $sql ."<br>";
      }else{
        echo "Unable to delete.";
      }
      // delete theater
    }elseif ($_POST['delOperation'] == "deleteTheatre"){
      $sql = "DELETE FROM theater where TheaterId LIKE '" . $_POST['delTheatre'] . "' OR Name LIKE '" . $_POST['delTheatre'] . "';";
      if (mysqli_query($db, $sql)) {
        echo "Deletion Query: " . $sql ."<br>";
      }else{
        echo "Unable to delete.";
      }
    }elseif ($_POST['delOperation'] == "deleteArranges"){
      $sql = "DELETE FROM arrange where ArrangeId LIKE '" . $_POST['delArrange'] . "';";
      if (mysqli_query($db, $sql)) {
        echo "Deletion Query: " . $sql ."<br>";
      }else{
        echo "Unable to delete.";
      }
    }
  }
  elseif (isset($_POST['listOperation'])){
    //listOps
    if ($_POST['listOperation'] == "listArranges"){
      $_SESSION['sqlRequest'] = "SELECT a.ArrangeId as ShowID, a.Showtime, t.Name as TheaterName, a.Location, a.Name, a.SeatsLeft FROM arrange as a INNER JOIN theater as t ON a.Location = t.Location;";
    }elseif ($_POST['listOperation'] == "listCustomers"){
      $_SESSION['sqlRequest'] = "SELECT CEmail as CustomerEmail, CPassword as Password, Age from customer;";
    }elseif ($_POST['listOperation'] == "listTheaters"){
      $_SESSION['sqlRequest'] = "SELECT TheaterId, Name, Location, OpenTime, CloseTime from theater;";
    }elseif ($_POST['listOperation'] == "listTickets"){
      $_SESSION['sqlRequest'] = "SELECT t.ConfirmationNo as TicketNo, a.ArrangeId as ShowID, c.CEmail as Customer, t.SeatsNo as Seat, t.AuditoriumNo as Auditorium, a.Name as Movie, th.Name as TheaterName, th.Location as Location from ticket as t, arrange as a, customer as c, theater as th where c.CEmail = t.CEmail and t.ArrangeId = a.ArrangeId and a.Location = th.Location;";
    }elseif ($_POST['listOperation'] == "listMovies"){
      $_SESSION['sqlRequest'] = "SELECT Name, Duration, Price, Type from movie;";
    }

  }
  elseif (isset($_POST['statsOperation'])){
    if ($_POST['statsOperation'] == "statsSeatsLeft"){
      $_SESSION['sqlRequest'] = "SELECT Name, sum(SeatsLeft) as Seats from arrange group by Name order by ";
        if ($_POST['statsSeatsLeftOrder'] == "seats"){
          $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . "sum(seatsLeft) ";
        }else
          $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . "name ";
        if ($_POST['statsSeatsLeftOrderBy'] == "desc"){
          $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . "DESC;";
        } else
          $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . "ASC;";
    }elseif($_POST['statsOperation'] == "statsAvgSeats"){
      $_SESSION['sqlRequest'] = "SELECT a.ArrangeId as ShowID, t.Name as Theater, a.Name, a.SeatsLeft as Seats from arrange as a inner join theater as t on a.Location = t.Location where SeatsLeft ";
      if ($_POST['statsAvgSeatsOp'] == "greater"){
        $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . ">";
      }else
        $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . "<";
      $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . "= (SELECT avg(SeatsLeft) from arrange);";
    }
  }
  elseif (isset($_POST['insertOperation'])){
    if ($_POST['insertOperation'] == "insertArranges"){
      $showtime = "'" . date('Y-m-d H:i:s', strtotime($_POST['arrangeShowTime'])) ."'";
      $location = "'" .$_POST['arrangeLocation'] ."'";
      $name = "'" . $_POST['arrangeMovie'] ."'";
      $seats = $_POST['arrangeSeats'];
      $sql = "INSERT into arrange(Location, Name, SeatsLeft, Showtime) VALUES ($location, $name, $seats, $showtime);";
      $result = mysqli_query($db, $sql);
      echo "Insert query: " . $sql . "<br/>";
      if ($result) {
        echo "Successfully inserted <br />";
      } else 
        echo "Failed to insert, check query. <br />";
    }elseif ($_POST['insertOperation'] == "insertMovie"){
      $name = "'" . $_POST['movieName'] . "'"; 
      $type = "'" . $_POST['movieRating'] . "'"; 
      $duration =  $_POST['movieDuration'] ; 
      $date = "'" . date('Y-m-d', strtotime($_POST['movieDate'])) ."'";
      $price =  $_POST['moviePrice'] ; 
      $sql = "INSERT into movie(Name, Type, Duration, StartingDate, Price) VALUES ($name, $type, $duration, $date, $price);";
      echo runSql($sql, $db) . "<br/>";
      if (mysqli_error($db)) {
        echo "Failed to insert, check query. <br />";
        echo mysqli_error($db) ."<br/>";
      }
    }
  }
}


?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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

List Query is <?= $_SESSION['sqlRequest']?> <br>
Hello <?= $_SESSION['employeeId']?> <br>

    Delete Operations: <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                  <input type="radio" name="delOperation" checked=true value="delUser" /> Delete User: <input type = "text" name="delUser"> <br />
                  <input type="radio" name="delOperation" value="deleteMovie" /> Delete Movie: <input type = "text" name = "delMovie"> <br />
                  <input type="radio" name="delOperation" value="deleteArranges" /> Delete Show <input type = "number" name = "delArrange"><br />
                  <input type="radio" name="delOperation" value="deleteTheatre" /> Delete Theatre <input type = "text" name = "delTheatre"><br />
                  <input type = "submit" value = " Submit "/><br /> 
    </form>

    List Operations: <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                  <input type="radio" name="listOperation" checked=true value="listArranges" /> List Arranges <br>
                  <input type="radio" name="listOperation" checked=false value="listCustomers" /> List Customers <br>
                  <input type="radio" name="listOperation" checked=false value="listTheaters" /> List Theaters <br>
                  <input type="radio" name="listOperation" checked=false value="listTickets" /> List Tickets <br>
                  <input type="radio" name="listOperation" checked=false value="listMovies" /> List Movies <br>
                  <input type = "submit" value = " Submit "/><br /> 
    </form>

    Stats Operations: <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                  <input type="radio" name="statsOperation" checked=true value="statsSeatsLeft" /> View Total Seats ordered by
                    <select name="statsSeatsLeftOrder">
                        <option value="movie">Movie Name</option>
                        <option value="seats">Seats Left</option>
                    </select>

                    <select name="statsSeatsLeftOrderBy">
                        <option value="desc">DESC</option>
                        <option value="asc">ASCE</option>
                    </select><br />
                    <input type="radio" name="statsOperation" value="statsAvgSeats" /> List Arranges with number of seats 
                         <select name="statsAvgSeatsOp">
                        <option value="greater">GREATER</option>
                        <option value="less">LESS</option>
                    </select>
                    than or equal to average
                    <br />
                  <input type = "submit" value = " Submit "/><br /> 
    </form>

    Insert Operations: <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                  <input type="radio" name="insertOperation" checked=true  value="insertMovie" /> Insert a movie:<br/>
                        <input type = "test" placeholder="Movie Name" name="movieName">
                        <input type = "text" placeholder="Advisory Rating" name="movieRating">
                        <input type = "number" placeholder="Duration in min" name="movieDuration">
                        <input type = "date" placeholder="Starting Date" name="movieDate">
                        <input type = "number" placeholder="Price (no symbols)" name="moviePrice">
                  <br/>
                  <input type="radio" name="insertOperation" value="insertArranges" /> Insert an Arrange:<br/>
                        <input type = "datetime-local" name="arrangeShowTime">
                        <input type = "text" placeholder="Theater Location" name="arrangeLocation">
                        <input type = "text" placeholder="Movie Name" name="arrangeMovie">
                        <input type = "number" placeholder="Seats" name="arrangeSeats">
                  <br/>

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

    function fnselect(){
        if ($("tr.selected td:first" ).html() == null){
          alert("You must select a show!");
        }else{
          alert($("tr.selected td:first" ).html());
        }
        
    }
</script>
    </body>
</html>