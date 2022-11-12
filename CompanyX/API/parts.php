<?php


if (strpos($_SERVER['REQUEST_URI'], "db.php") !== false) {
    header("Location: ../index.php");
    die();

}

session_start();
$host = "localhost:3307";
$username = "root";
$password = "root";
$dbname = "mydb";

$dataBase = new mysqli($host, $username, $password, $dbname);

if ($dataBase->connect_error) {
    die("Database Unreachable.");
}

try {
    $dataBase->begin_Transaction();
    $query = $dataBase->query(
        "Select * from Xparts543;  ");


    while ($row = $query->fetch_row()) {
        echo "<tr class='$row[0]'> ";

        echo "<th>$row[0]</th>
                      <th>$row[1]</th>
                      <th>$row[2]</th>
                      <th >$row[3]</th>
                      <th style='display: none'>$row[4]</th>
                      <th style='display: none'>X</th>";

        echo "</tr> ";

    }


$dataBase->commit();
}
catch (ErrorException $e)
   {

       $dataBase->rollback();
       return;
   }

$dataBase->close();






