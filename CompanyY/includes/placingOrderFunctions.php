<?php
if (strpos($_SERVER['REQUEST_URI'], "placingOrderFunctions.php") !== false) {
    header("Location: ../index.php");
    die();

}

function PoCi543($dataBase)
{
    $valid = true;
    $line = $_POST["SubmitPO"]+1;
    $client = $_POST["cliendId"];
    $poID = $_POST["PoID"];

    if($line ==1)
    {
        try {
            $dataBase->begin_Transaction();
            $query = $dataBase->query(
                "Select * from Ypos543 WHERE YpoNo543 = '$poID';  ");
            $row = $query->fetch_row();
                $dataBase->commit();
            }
        catch (ErrorException $e)
        {

            $dataBase->rollback();
            return;
        }
        if($row)
        {
            echo "<h3>This purchase order number is already in use</h3>";
            $valid = false;
        }
        try {
            $dataBase->begin_Transaction();
            $query = $dataBase->query(
                "Select * from Yclients543 WHERE YclientId543 = '$client';  ");
            $row = $query->fetch_row();
            $dataBase->commit();
        }
        catch (ErrorException $e)
        {

            $dataBase->rollback();
            return;
        }

        if(!$row)
        {
            echo "<h3>This client does not exist</h3>";
            $valid = false;
        }
        if($valid)
        {
            try {
                $dataBase->begin_Transaction();
                $query = $dataBase->query(
                    "insert into Ypos543 values('$poID', curdate(), 'processing', '$client');  ");
                $dataBase->commit();
            }
            catch (ErrorException $e)
            {

                $dataBase->rollback();
                return;
            }



        }
    }


    if($valid) {
        echo
        " <form id='submit' class='mt-5' method='post'> 
            <div id='formTop'>
                <div><h4 id='poLabel'> PoID: </h4><label id='Po' class='users'  >$poID</label> </div> <div> <h4 id='clientLabel'>Client Id:</h4><label class='users'  id='cliend' >$client</label></div>
                <input  value='$poID' id='PoID' class='textField' type='hidden' name='PoID'><input  value='$client'id='cliendId' class='textField' type='hidden' name='cliendId'>
             </div>
             <div id='lines'>
             ";
        for ($x = 0; $x < $line; $x++) {
            if ($x + 1 == $line) {
                echo "
             <div class='line'>
               <label id='partLabel'> Part Number </label><input id='partNum$x'  class='textField' type='text' name='partNum$x'>
               <label id='quantLabel'> Quantity </label><input id='quant$x'  class='textField' type='text' name='quant$x'>
                
               </div>
              ";
            } else {
                $part = $_POST["partNum$x"];
                $quantity = $_POST["quant$x"];
                echo "
             <div class='line'>
               <label id='partLabel'> Part Number </label><input id='partNum$x'  class='textField' type='text' name='partNum$x' value='$part'>
               <label id='quantLabel'> Quantity </label><input id='quant$x'  class='textField' type='text' name='quant$x' value='$quantity'>
                
               </div>
              ";
            }

        }
        echo "
   <button id='newLine' name='SubmitPO' value='$line'>New Line</button><button id='newLine' name='PlaceOrder' value='$line'>Place Order</button>  </div></form>";


        echo "<h3>Available  Parts</h3>";
        displayParts543($dataBase);
    }
}

function placing543($dataBase)
{
    $valid = true;
    $line = $_POST["PlaceOrder"];
    $poID = $_POST["PoID"];
    try {
        $dataBase->begin_Transaction();
    for($x = $line-1; $x>=0;$x--)
    {

        $part = $_POST["partNum$x"];
        $quantity = $_POST["quant$x"];
        $query = $dataBase->query(
            "Select * from Yparts543 WHERE YpartNo543 = '$part' AND YQoh543 >= '$quantity' ;  ");
        $row = $query->fetch_row();
        if(!$row) {
            echo "<h3>Not Enough Quantity available order rejected</h3>";
            $valid = false;
            break;
        }
    }
    if($valid)
    {
        for($x = $line-1; $x>=0;$x--)
        {

            $part = $_POST["partNum$x"];
            $quantity = $_POST["quant$x"];
            $query = $dataBase->query(
                "insert into Ylines543 values('$part', 'l$x','$poID', NULL, '$quantity');");


        }
        echo "Order has been Placed";
    }
        $dataBase->commit();
    }
    catch (ErrorException $e)
    {

        $dataBase->rollback();
        return;
    }

}
?>