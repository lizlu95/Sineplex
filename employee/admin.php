<?php
session_start();
include("../database.php");
if (!isset($_SESSION['employeeId'])) {
    header("location:index.php");
}

if (!isset($_SESSION['sqlRequest'])){
    $_SESSION['sqlRequest'] = "SELECT a.ArrangeId, a.Showtime, t.Name as TName, a.Location, a.Name, a.SeatsLeft FROM arrange as a, theater as t WHERE a.Location = t.Location;";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // assume this post came from this page and not some external source...
  // begin constructing sql statement for movie filtering
  $_SESSION['sqlRequest'] = "SELECT a.ArrangeId, a.Showtime, t.Name as TName, a.Location, a.Name, a.SeatsLeft FROM arrange as a, theater as t WHERE a.Location = t.Location and 1;";
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
                  <input type="radio" name="delOperation" value="deleteArranges" /> Delete Show <input type = "text" name = "delArrange"><br />
                  <input type="radio" name="delOperation" value="deleteTheatre" /> Delete Theatre <input type = "text" name = "delTheatre"><br />
                  <input type = "submit" value = " Submit "/><br /> 
    </form>

    List Operations: <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                  <input type="radio" name="listOperation" checked=true value="listArranges" /> List Arranges <br>
                  <input type = "submit" value = " Submit "/><br /> 
    </form>

<a href="logout.php">Logout</a>
<br><br>

    <table id="mainTable">
        <tr >
            <td>Show ID</td>
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
               echo "<td>".$row["ArrangeId"]."</td>";
               echo "<td>".$row["Showtime"]."</td>";
               echo "<td>".$row["TName"]."</td>";
               echo "<td>".$row["Location"]."</td>";
               echo "<td>".$row["Name"]."</td>";
               echo "<td>".$row["SeatsLeft"]."</td>";
               echo "</tr>";
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