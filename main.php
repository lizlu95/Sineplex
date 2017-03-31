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
      }
        echo '<script language="javascript">';
        echo 'alert("Successfully bought tickets!")';
        echo '</script>';
        echo "Insert query: " . $sql . "<br>";
        //header("location:index.php");
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
  <link href="style/main.css" rel="stylesheet">
  <style>
    .demo-card-wide.mdl-card{
      width: 87%;
      opacity:0.7;
    }
    .demo-card-wide > .mdl-card__title {
      color: #fff;
      height: 120px;
      background: url('img/welcome.jpg') center / cover;
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
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/jquery-ui-timepicker-addon.css" rel="stylesheet">

  <!-- Material Design Bootstrap -->
  <link href="css/mdb.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
  <link rel="stylesheet" type="text/css" href="https://storage.googleapis.com/code.getmdl.io/1.3.0/material.indigo-pink.min.css">
  <link rel="stylesheet" type="text/css" href="https://rawgit.com/MEYVN-digital/mdl-selectfield/master/mdl-selectfield.min.css">
  <link rel="stylesheet" href="bower_components/jquery-labelauty/source/jquery-labelauty.css" type="text/css"  media="screen" charset="utf-8">
 
  <title>Sineplex</title>
</head>
<body>
  <div class="content">
    <div class="white-card demo-card-wide mdl-card"></div>
  </div><!--end of content-->
  <br>
  <br>

  <!-- welcome msg, current query, and log out -->
  <div class="container" align="middle">
    <div class="demo-card-wide mdl-card mdl-shadow--2dp">
      <div class="mdl-card__title">
        <h2 class="mdl-card__title-text">Welcome <?= $_SESSION['username']?></h2>
      </div>
      <div class="mdl-card__supporting-text">
        Query is <?= $_SESSION['sqlRequest']?>
      </div>
      <div class="mdl-card__actions mdl-card--border">
        <a href="logout.php" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
        Log out</a>
      </div>
    </div>
  </div>

  <!-- search -->
  <div class="container">
    <h2>Search By</h2><br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <!-- First row -->
      <div class="row">
        <!-- <div class="col-md-0.6"></div> -->
        <!--First column-->
        <div class="col-md-2">
          <input class="to-labelauty synch-icon lcheckbox" type="checkbox" name="hideShowtimeCb" data-labelauty="Hide Showtime" value = "Yes"/>   
        </div>
        <!--Second column-->
        <div class="col-md-2">
          <input class="to-labelauty synch-icon lcheckbox" type="checkbox" name="hideTheaterNameCb" data-labelauty="Hide TheaterName" value = "Yes"/>
        </div>
        <!--Third column-->
        <div class="col-md-2">
          <input class="to-labelauty synch-icon lcheckbox" type="checkbox" name="hideLocationCb" data-labelauty="Hide Location" value = "Yes"/>
        </div>
        <!--4th column-->
        <div class="col-md-2">
          <input class="to-labelauty synch-icon lcheckbox" type="checkbox" name="hideNameCb" data-labelauty="Hide Name" value = "Yes"/>   
        </div>
        <!--5th column-->
        <div class="col-md-2">
          <input class="to-labelauty synch-icon lcheckbox" type="checkbox" name="hideSeatsCb" data-labelauty="Hide Seats" value = "Yes"/>
        </div>
        <!--6th column-->
        <div class="col-md-2">
          <input class="to-labelauty synch-icon lcheckbox" type="checkbox" name="searchTickets" data-labelauty="Search from Purchased" value = "Yes"/>
        </div>
        <!-- <div class="col-md-0.6"></div> -->
      </div>
      <br>
      <!--Second row-->
      <div class="row">

        <!--First column-->
        <div class="col-md-3">
          <div class="md-form">
            <i class="fa fa-file-text prefix" aria-hidden="true"></i>
            <input type="text" id="form76" class="form-control validate md-textarea" name = "movieName">
            <label for="form76" data-error="wrong" data-success="right">Movie Title</label>
          </div>
        </div>
        <div class="col-md-1">
          <input type="checkbox" name="movieNameCb" class="to-labelauty-icon" value="Yes"/>
        </div>

        <!--Second column-->
        <div class="col-md-3">
          <div class="md-form">
            <i class="fa fa-location-arrow prefix" aria-hidden="true"></i>
            <input type="text" id="form76" class="form-control validate" name = "locationName" value = >
            <label for="form76" data-error="wrong" data-success="right">Theatre Location</label>
          </div>
        </div>
        <div class="col-md-1">
          <input type="checkbox" name="locationNameCb" class="to-labelauty-icon" value="Yes"/>
        </div>


        <!--Third column-->
        <div class="col-md-3">
          <div class="md-form">
            <i class="fa fa-user prefix" aria-hidden="true"></i>
            <input type="text" id="form76" class="form-control validate" name = "theaterName">
            <label for="form76" data-error="wrong" data-success="right">Theatre Name</label>
          </div>
        </div>
        <div class="col-md-1">
          <input type="checkbox" name="theaterNameCb" class="to-labelauty-icon" value="Yes"/>
        </div>
      </div>
      <!--Third row-->
      <div class="row">
        <!--First column-->
        <div class="col-md-3">
          <div class="md-form">
            <i class="fa fa-snapchat-ghost prefix" aria-hidden="true"></i>
            <input type="number" id="showId" class="form-control validate" name = "showId">
            <label for="form76" data-error="wrong" data-success="right">Show ID</label>
          </div>
        </div>
        <div class="col-md-1">
         <input type="checkbox" name="showIdCb" class="to-labelauty-icon" value="Yes"/>
        </div>

       <!--Second column-->
       <div class="col-md-3">
         <input type="checkbox" name="fromDateCb" value="Yes" class = "hidden"/><p>From Date: <input id = "fromDate" class="datepicker" name = "fromDate" value="2017-03-03T00:00"></p>
       </div>
       <div class="col-md-1">
         <input type="checkbox" name="fromDateCb" class="to-labelauty-icon" value="Yes"/>
       </div>

       <!--Second column-->
       <div class="col-md-3">
        <input type="checkbox" name="toDateCb" value="Yes" class = "hidden"/><p>To Date: <input  id = "toDate" class="datepicker" name = "toDate"></p>
       </div>
       <div class="col-md-1">
       <input type="checkbox" name="toDateCb" class="to-labelauty-icon" value="Yes"/>
       </div>
      </div>

      <div class="row">
        <!--Third column-->
        <div class="col-md-2"></div>
        <div class="col-md-3">
          <div class="md-form">
            <input type = "submit" class="myButton" value = " Submit "/>
          </div>
        </div>
        <div class="col-md-3">
          <div class="md-form">
          <input type="button" class="myButton" value='Buy!' onclick='fnselect(document.getElementsByName("numberOfTickets")[0].value) '/></div>
        </div>
        <div class="col-md-2">
          <div class="md-form">
          <input type="number" min="0" value=1 name = "numberOfTickets" id="form76" class="form-control validate md-textarea"> <label for="form76" data-error="wrong" data-success="right">Number of tickets</label></div>
        </div>
        <div class="col-md-2"></div>
      </div>
    </form>
  </div><!--end of container-->

  <!-- filters -->
  <div class="container">
    <h2> More search filters</h2> <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <div class="row">
        <div class="col-md-1">
          <input class="to-labelauty-icon" type="radio" name="statsOperation" value="statsAvgSeats"/> 
        </div>
        <div class="col-md-5">
          <p><b>List Arranges with number of seats</b></p>
          <div class="mdl-selectfield mdl-js-selectfield mdl-selectfield--floating-label" style="width:90px">
           <select name="statsAvgSeatsOp" class="mdl-selectfield__select">
            <option value="greater">GREATER</option>
            <option value="less">LESS</option>
           </select>
          </div>
          <p><b>than or equal to average</b></p>
        </div>

        <div class="col-md-1">
          <input class="to-labelauty-icon" type="radio" name="statsOperation" value="statsAvgDuration" /> 
        </div>
        <div class="col-md-5">
          <p><b>List Movies with duration </b></p>
          <div class="mdl-selectfield mdl-js-selectfield mdl-selectfield--floating-label" style="width:90px">

           <select name="statsAvgDurationOp" class="mdl-selectfield__select">
            <option value="greater">GREATER</option>
            <option value="less">LESS</option>
           </select>
          </div>
          <p><b>than or equal to average duration</b></p>
        </div>
      </div>  

      <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
          <div class="md-form">
            <input class="myButton" type = "submit" value = " Submit "/>
          </div>
        </div>
        <div class="col-md-4"></div>
      </div>
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
<!-- SCRIPTS -->
<!-- JQuery -->
<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
<!-- Bootstrap tooltips -->
<script type="text/javascript" src="js/tether.min.js"></script>
<!-- Bootstrap core JavaScript -->
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- MDB core JavaScript -->
<script type="text/javascript" src="js/mdb.min.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="jquery.iMissYou.js"></script>
<script src="js/jquery-ui-timepicker-addon.js"></script>
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
<script type="text/javascript" src="bower_components/jquery-labelauty/source/jquery-labelauty.js"></script>
</body>
</html>