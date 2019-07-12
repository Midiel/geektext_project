
<?php
session_start();
require_once('connect.inc.php');
//print_r($_POST);

//echo $_POST;
    
if(isset($_SESSION['token'])) {



    if(isset($_POST['get_num_items'])) {

        $query = "CALL getCartSubtotal('" . $_SESSION['token'] . "')";

        $num_items = 0;
        $subtotal = 0;
        
        if($result = mysqli_query($con, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
    
                //echo " ".$row['number_of_items'] . " ";
                $num_items = $row['NumItems'];
                $subtotal = number_format((float)$row['Subtotal'], 2, '.', '');


                $tax = 0.07 * $subtotal;
                $tax = number_format((float)$tax, 2, '.', '');
                $total = $subtotal + $tax;


                    
                //echo "<a class=\"nav-link\" href=\"cart.php\"><span class=\"fa fa-shopping-cart\"> ". $row['number_of_items'] . "</span></a>";
                //echo " " . $row['number_of_items'];

                echo "
                <div class=\"row border\">
                    <h5>Order Summary</h5>

                </div>

                <div class=\"row\">
                    <div class=\"d-flex border border-info col-8\">
                        Items (".$row['NumItems'] ."):<br>
                        Shipping:<br>
                        Subtotal:<br>
                        Tax (7.00 %):<br>

                    </div>

                    <div class=\" border border-info col-4\">
                        <p class=\"text-right\">
                            $". $subtotal ."<br>
                            $0.00<br>
                            $". $subtotal ."<br>
                            $". $tax ."<br>
                        </p>
                        
                    </div>
                </div>

                <div class=\"border border-warning row\">
                    <div class=\"d-flex border border-info col-8\">
                        <h5 class=\"mt-3\">Order total:</h5>
                    </div>

                    <div class=\" border border-info col-4\">
                        <p>
                            <h5 class=\"mt-3 text-right\">$". $total ."</h5>
                        </p>
                    </div>

                </div>

                <div class=\"border border-info row\">
                    <div class=\"border border-info col-sm align-self-end\">

                        

                        <button id=\"submit_order\" class=\"btn btn-default btn-block btn-warning mt-2 mb-2\" onclick=\"orderConfirmation(); return false;\" type=\"submit\">Checkout</button>
                        
                    </div>
                </div>
                ";



            }
        }
    

    } else if(isset($_POST['order_confirmation'])){

        
        $query = $con->prepare('CALL emptyCart(?)');
		$query->bind_param('s', $_SESSION['token']);
		$query->execute();
		$query->close();
        
        echo "
            <div class=\"border border-warning row\">
                <h5>Order Confirmation</h5>
            </div>

            <div class=\"border border-info row\">
                <p>Your order is confirmed!!!<br>Please sit tight and wait for it ;)</p><br>
                
            </div>
            ";
    }

}




?>



