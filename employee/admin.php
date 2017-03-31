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
    }elseif ($_POST['delOperation'] == "deleteTicket"){
      $sql = "DELETE FROM ticket where ConfirmationNo = " . $_POST['delTicket'] . ";";
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
      $_SESSION['sqlRequest'] = "SELECT Name, " . $_POST['statsSeatsLeftOp'] . "(SeatsLeft) from arrange group by Name order by ";
        if ($_POST['statsSeatsLeftOrder'] == "seats"){
          $_SESSION['sqlRequest'] = $_SESSION['sqlRequest'] . $_POST['statsSeatsLeftOp'] . "(seatsLeft) ";
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

    }elseif($_POST['statsOperation'] == "statsSeatsPerTheater"){
      $_SESSION['sqlRequest'] = "Select w.Location, " . $_POST['statsSeatsPerTheaterMinMax'] . "(w.m) as AvgSeatsLeft from (SELECT t.Location, avg(a.SeatsLeft) as m from theater as t, arrange as a where a.Location = t.Location GROUP BY t.Location) as w;";

    }elseif($_POST['statsOperation'] == "statsSpecificMovie"){
      $_SESSION['sqlRequest'] = "SELECT c.CEmail as Email from customer as c where c.CEmail = ANY (SELECT t.CEmail from ticket as t where t.ArrangeId = ANY (SELECT a.ArrangeId from arrange as a where a.Name LIKE '" . $_POST['statsSpecificMovieName'] . "'));";
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
  <link href="../style/main.css" rel="stylesheet">
  <style>
    .demo-card-wide.mdl-card{
      width: 87%;
      opacity:0.7;
    }
    .demo-card-wide > .mdl-card__title {
      color: #fff;
      height: 120px;
      background: url('../img/welcome.jpg') center / cover;
    }
    .demo-card-wide > .mdl-card__menu {
      color: #fff;
    }
  </style>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css">
  <!-- Bootstrap core CSS -->
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/jquery-ui-timepicker-addon.css" rel="stylesheet">

  <!-- Material Design Bootstrap -->
  <link href="../css/mdb.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
  <link rel="stylesheet" type="text/css" href="https://storage.googleapis.com/code.getmdl.io/1.3.0/material.indigo-pink.min.css">
  <link rel="stylesheet" type="text/css" href="https://rawgit.com/MEYVN-digital/mdl-selectfield/master/mdl-selectfield.min.css">
  <link rel="stylesheet" href="../bower_components/jquery-labelauty/source/jquery-labelauty.css" type="text/css"  media="screen" charset="utf-8">

  <title>Admin</title>
</head>
<body>
  <div class="content">
      <div class="white-card demo-card-wide mdl-card"></div>
  </div><!--end of content-->
  <br><br>

<!-- welcome msg, current query, and log out -->
  <div class="container" align="middle">
    <div class="demo-card-wide mdl-card mdl-shadow--2dp">
      <div class="mdl-card__title">
        <h2 class="mdl-card__title-text">Welcome <?= $_SESSION['employeeId']?></h2>
      </div>
      <div class="mdl-card__supporting-text">
        List Query is <?= $_SESSION['sqlRequest']?>
      </div>
      <div class="mdl-card__actions mdl-card--border">
        <a href="logout.php" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
        Log out</a>
      </div>
    </div>
  </div>

  <div class="container">
    <h2>Delete Operations</h2> <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <div class="row">

        <div class="col-md-1"><input class="to-labelauty-icon" type="radio" name="delOperation" value="delUser"/></div>
        <div class="col-md-3"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "text" id="form76" class="mdl-textfield__input" name="delUser">
        <label class="mdl-textfield__label" for="form76">Delete User</label>
        </div></div>

        <div class="col-md-1"><input class="to-labelauty-icon" type="radio" name="delOperation" value="delMovie"/></div>
        <div class="col-md-3"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "text" id="form76" class="mdl-textfield__input" name="delMovie">
        <label class="mdl-textfield__label" for="form76">Delete Movie</label>
        </div></div>

        <div class="col-md-1"><input class="to-labelauty-icon" type="radio" name="delOperation" value="delArrange"/></div>
        <div class="col-md-3"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "text" id="form76" class="mdl-textfield__input" name="delArrange">
        <label class="mdl-textfield__label" for="form76">Delete Arrange</label>
        </div></div>
      </div>
      <div class="row">

        <div class="col-md-1"><input class="to-labelauty-icon" type="radio" name="delOperation" value="delTheatre"/></div>
        <div class="col-md-3"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "text" id="form76" class="mdl-textfield__input" name="delTheatre">
        <label class="mdl-textfield__label" for="form76">Delete Theatre</label>
        </div></div>

        <div class="col-md-1"><input class="to-labelauty-icon" type="radio" name="delOperation" value="delTicket"/></div>
        <div class="col-md-3"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "text" id="form76" class="mdl-textfield__input" name="delTicket">
        <label class="mdl-textfield__label" for="form76">Delete Ticket</label>
        </div></div>
      </div>
      <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4"><input class="myButton" type = "submit" value = " Submit "/></div>
      <div class="col-md-4"></div>
      </div>
    </form>
  </div>

  <div class="container">
    <h2>List Operations</h2> <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <div class="row">
        <div class="col-md-1"><input class="to-labelauty-icon" type="radio" name="listOperation" value="listArranges"/></div>
        <div class="col-md-3"><p><b>Arranges</b></p></div>
        <div class="col-md-1"><input class="to-labelauty-icon" type="radio" name="listOperation" value="listCustomers"/></div>
        <div class="col-md-3"><p><b>Customers</b></p></div>
        <div class="col-md-1"><input class="to-labelauty-icon" type="radio" name="listOperation" value="listTheaters"/></div>
        <div class="col-md-3"><p><b>Theatres</b></p></div>
      </div>
      <div class="row">
        <div class="col-md-1"><input class="to-labelauty-icon" type="radio" name="listOperation" value="listTickets"/></div>
        <div class="col-md-3"><p><b>Tickets</b></p></div>
        <div class="col-md-1"><input class="to-labelauty-icon" type="radio" name="listOperation" value="listMovies"/></div>
        <div class="col-md-3"><p><b>Movies</b></p></div>
      </div>
      <br>
      <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4"><input class="myButton" type = "submit" value = " Submit "/></div>
      <div class="col-md-4"></div></div>
    </form>
  </div>

  <div class="container">
    <h2>Stats Operations</h2> <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <div class="row">
        <div class="col-md-1">
          <input class="to-labelauty-icon" type="radio" name="statsOperation" value="statsSeatsLeft"/> 
        </div>
        <div class="col-md-5">
          <b>View
          <div class="mdl-selectfield mdl-js-selectfield mdl-selectfield--floating-label" style="width:70px">
           <select name="statsSeatsLeftOp" class="mdl-selectfield__select">
            <option value="min">MIN</option>
            <option value="max">MAX</option>
            <option value="count">COUNT</option>
            <option value="sum">SUM</option>
            <option value="avg">AVG</option>
           </select></div>
           of Seats ordered by
           <div class="mdl-selectfield mdl-js-selectfield mdl-selectfield--floating-label" style="width:110px">
           <select name="statsSeatsLeftOrder" class="mdl-selectfield__select">
            <option value="movie">Movie Name</option>
            <option value="seats">Seats Left</option>
           </select></div>
           <div class="mdl-selectfield mdl-js-selectfield mdl-selectfield--floating-label" style="width:90px">
           <select name="statsSeatsLeftOrderBy" class="mdl-selectfield__select">
            <option value="desc">DESC</option>
            <option value="asc">ASCE</option>
           </select></div></b>
        </div>

        <div class="col-md-1">
          <input class="to-labelauty-icon" type="radio" name="statsOperation" value="statsAvgSeats"/> 
        </div>
        <div class="col-md-5">
          <b>List Arranges with number of seats
          <div class="mdl-selectfield mdl-js-selectfield mdl-selectfield--floating-label" style="width:90px">
           <select name="statsAvgSeatsOp" class="mdl-selectfield__select">
            <option value="greater">GREATER</option>
            <option value="less">LESS</option>
           </select>
          </div>
          than or equal to average</b>
        </div>
      </div>

      <div class="row">
        <div class="col-md-1">
          <input class="to-labelauty-icon" type="radio" name="statsOperation" value="statsSeatsPerTheater"/> 
        </div>
        <div class="col-md-5">
          <b>List the 
          <div class="mdl-selectfield mdl-js-selectfield mdl-selectfield--floating-label" style="width:70px">
           <select name="statsSeatsPerTheaterMinMax" class="mdl-selectfield__select">
            <option value="min">MIN</option>
            <option value="max">MAX</option>
           </select>
          </div></b>
          <p><b>of the averages of seats left for all arranges, by theater.</b></p>
        </div>

        <div class="col-md-1">
          <input class="to-labelauty-icon" type="radio" name="statsOperation" value="statsSpecificMovie"/>
        </div>
        
        <div class="col-md-5">
        <p><b>List all users that bought this movie: </b></p>
        <div class="md-form mdl-textfield mdl-js-textfield">
          <input type = "text" id="form76" class="mdl-textfield__input" name = "statsSpecificMovieName">
          <label class="mdl-textfield__label" for="form76">Movie Title</label>
          <p><b>of the averages of seats left for all arranges, by theatre</b></p>
        </div></div>
      </div>
      <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4"><input class="myButton" type = "submit" value = " Submit "/></div>
      <div class="col-md-4"></div></div>
    </form>
  </div>


<div class="container">
    <h2>Insert Operations</h2> <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <div class="row">

        <div class="col-md-2"><input class="to-labelauty-icon" type="radio" name="insertOperation" value="insertMovie"/><b>Insert a movie</b></div>

        <div class="col-md-2"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "text" id="form76" class="mdl-textfield__input" name="movieName">
        <label class="mdl-textfield__label" for="form76">Movie</label>
        </div></div>

        <div class="col-md-2"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "text" id="form76" class="mdl-textfield__input" name="movieRating">
        <label class="mdl-textfield__label" for="form76">Rating</label>
        </div></div>

        <div class="col-md-2"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "number" id="form76" class="mdl-textfield__input" name="movieDuration">
        <label class="mdl-textfield__label" for="form76">Duration</label>
        <span class="mdl-textfield__error">Input is not a number!</span>
        </div></div>

        <div class="col-md-2"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "number" id="form76" class="mdl-textfield__input" name="moviePrice">
        <label class="mdl-textfield__label" for="form76">Price</label>
        <span class="mdl-textfield__error">Input is not a number!</span>
        </div></div>

        <div class="col-md-2"><div class="md-form">
        <input type = "date" class="datepicker" name="movieDate">
        </div></div>
      </div>
      <div class="row">
        <div class="col-md-2">
        <input type="radio" class="to-labelauty-icon" name="insertOperation" value="insertArranges" /><b>Insert an Arrange</b></div>

        <div class="col-md-2"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "number" id="form76" class="mdl-textfield__input" name="arrangeMovie">
        <label class="mdl-textfield__label" for="form76">Movie</label>
        </div></div>

        <div class="col-md-2"><div class="md-form mdl-textfield mdl-js-textfield">
        <input type = "number" id="form76" class="mdl-textfield__input" name="arrangeSeats">
        <label class="mdl-textfield__label" for="form76" data-error="wrong" data-success="right">Seat number</label>
        </div></div>

        <div class="col-md-2"><div class="md-form">
        <input type = "datetime-local"  class="datepicker" name="arrangeShowTime">
        </div></div>

        </div>
      <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4"><input class="myButton" type = "submit" value = " Submit "/></div>
      <div class="col-md-4"></div></div>
    </form>
  </div>

<div class="container">
    <table id="mainTable" class="table table-bordered">
        <tr>
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
</div>
    <br><br>
    <script>
    function highlight(e) {
        if (selected[0]) selected[0].className = '';
        e.target.parentNode.className = 'selected';
    }
    var table = document.getElementById('mainTable'),
    selected = table.getElementsByClassName('selected');
    table.onclick = highlight;
</script>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="../jquery.iMissYou.js"></script>
  <script src="../js/jquery-ui-timepicker-addon.js"></script>
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <script type="text/javascript" src="https://storage.googleapis.com/code.getmdl.io/1.3.0/material.min.js"></script>
  <script type="text/javascript" src="https://rawgit.com/MEYVN-digital/mdl-selectfield/master/mdl-selectfield.min.js"></script>
  <script>
    jQuery(document).ready(function($){
      $.iMissYou({
        title: "I Miss you !"
      });
    });
    jQuery(document).ready(function($){
     $(".lcheckbox").labelauty();
     $(".to-labelauty-icon").labelauty({ label: false });
     $(".datepicker").datetimepicker();
     $('#mainTable').DataTable();
   });
  </script>
  <script type="text/javascript" src="../bower_components/jquery-labelauty/source/jquery-labelauty.js"></script>
</body>
</html>