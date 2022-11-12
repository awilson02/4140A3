<?php

session_start();
$host = "localhost:3307";
$username = "root";
$password = "root";
$dbname = "mydb";

$dataBase = new mysqli($host, $username, $password, $dbname);

if ($dataBase->connect_error) {
    die("Database Unreachable.");
}


echo "<html>";
$client = $_GET['client'];
$po = $_GET['po'];


//validate po and client

$valid = true;

try {
    $dataBase->begin_Transaction();
    $query = $dataBase->query(
        "Select * from Xpos543 WHERE XpoNo543 = '$po';  ");
    $row = $query->fetch_row();
    if($row)
    {
        echo "<h3 id='invalidPO'>This purchase order number is already in use</h3>";
        $valid = false;
    }
    $query = $dataBase->query(
        "Select * from Xclients543 WHERE XclientId543 = '$client';  ");
    $row = $query->fetch_row();
    if(!$row)
    {
        echo "<h3 id=''>This client does not exist</h3>";
        $valid = false;
    }
    if($valid)
    {
        $query = $dataBase->query(
            "insert into Xpos543 values('$po', curdate(), 'processing', '$client');  ");

        $lineNum = $_GET['lineNum'];
        for($x = $lineNum -1; $x>=0;$x--)
        {

            $part = $_GET["partNum$x"];
            $quantity = $_GET["quant$x"];
            $query = $dataBase->query(
                "insert into Xlines543 values('$part', 'l$x','$po', NULL, '$quantity');");


        }
        echo "<p id ='done'>order complete</p>";
    }
    echo "</html>";
    $dataBase->commit();
}
catch (ErrorException $e)
{

    $dataBase->rollback();
    return;
}
$dataBase->close();
