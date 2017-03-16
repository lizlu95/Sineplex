<?php
session_start();
include("../database.php");
if (!isset($_SESSION['employeeId'])) {
    header("location:index.php");
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
      $_SESSION['sqlRequest'] = "SELECT a.ArrangeId as ShowID, a.Showtime, t.Name as TheaterName, a.Location, a.Name, a.SeatsLeft FROM arrange as a, theater as t WHERE a.Location = t.Location;";
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
              echo "<td>".$the_keys[$i]."</td>";
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