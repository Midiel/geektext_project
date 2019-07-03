
<?php
session_start();
require_once('connect.inc.php');
//print_r($_POST);
    

    if(isset($_POST['changeQty'])) {

        echo "\nbook id: " . $_POST['book_id'];
        
        echo "\nbook qty: " . $_POST['changeQty'];
        echo "\ntoken: " . $_SESSION['token'];

        $query = $con->prepare('CALL changeQty(?,?,?)');
        $query->bind_param('sii', $_SESSION['token'], $_POST['book_id'], $_POST['changeQty']);
        $query->execute();
        $query->close();


    $result = "<option value=\"\" selected disabled hidden>". $_POST['changeQty'] . "</option>
                        <option value=\"1\">1</option>
                        <option value=\"2\">2</option>
                        <option value=\"3\">3</option>
                        <option value=\"4\">4</option>
                        <option value=\"5\">5</option>
                        <option value=\"6\">6</option>
                        <option value=\"7\">7</option>
                        <option value=\"8\">8</option>
                        <option value=\"9\">9</option>
                    </select>";

    echo $result;
        
    } else if(isset($_POST['get_subtotal'])) {

        $subtotal = 0;
        $num_items = 0;
        $itemsString = "items";
    
        if(isset($_SESSION['token'])) {
            $query = "CALL getCartSubtotal('" . $_SESSION['token'] . "')";

            if($result = mysqli_query($con, $query)) {
                while($row = mysqli_fetch_assoc($result)) {
                    $num_items = $row['NumItems'];
                    $subtotal = number_format((float)$row['Subtotal'], 2, '.', '');
                }
            }

            // Free Result
            mysqli_free_result($result);

            // Close Connection
            mysqli_close($con);
        }

        if($num_items < 1) {
            $itemsString = "item";
        }

        echo "<strong>Subtotal (" . $num_items . " " . $itemsString . "): </strong> $" . $subtotal . "</div>";     
         
    } else if(isset($_POST['update_nav'])) {

        //echo "\ntoken: " . $_POST['update_nav'];
        //$_SESSION['token'] = "7854c8701c5da6d80d602a20133d2bf8";
       
        $query = "CALL getCartQty('" . $_SESSION['token'] . "')";
    
        if($result = mysqli_query($con, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                echo " " . $row['number_of_items'];
            }
        }
        
    }

?>