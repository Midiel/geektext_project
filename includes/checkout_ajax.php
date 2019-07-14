
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
        $tax = 0;
        $total = 0;

        if($result = mysqli_query($con, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
    
                //echo " ".$row['number_of_items'] . " ";
                $num_items = $row['NumItems'];
                $subtotal = floor((float)$row['Subtotal'] * 100) / 100;
                //$subtotal = number_format((float)$row['Subtotal'], 2, '.', '');


                $tax = 0.07 * $subtotal;
                $tax = floor($tax * 100) / 100;
                //$tax = number_format((float)$tax, 2, '.', '');
                $total = $subtotal + $tax;
                $total = number_format((float)$total, 2, '.', '');


                    
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

            //  gloval variable for order summary
            $_SESSION['num_items'] = $num_items;
            $_SESSION['subtotal'] = $subtotal;
            $_SESSION['tax'] = $tax;
            $_SESSION['total'] = $total;



        }
    

    } else if(isset($_POST['order_confirmation'])){

        
        $query = $con->prepare('CALL emptyCart(?)');
		$query->bind_param('s', $_SESSION['token']);
		$query->execute();
		$query->close();
        
        

        echo "
            <div class=\"border border-danger row\">      <!-- first row -->
                <p class=\"font-weight-bold pl-3\"><h5>Order Details</h5></p><br>
            </div>
            <div class=\"border border-danger row p-3\">
                Ordered on: " . date("l jS \of F Y h:i:s A") . "<br>
            </div>
            <div class=\"border border-danger row\">      <!-- first row -->
                <div class=\"border border-danger col-4\"> <!-- address column -->
                    <p class=\"font-weight-bold\">Shipping Address</p>
                
                    ". $_SESSION['shipping_address']['f_name'] . ", ". $_SESSION['shipping_address']['l_name'] . "<br>
                    ". $_SESSION['shipping_address']['street_address'] . "<br>
                    ". $_SESSION['shipping_address']['city'] . ", ". $_SESSION['shipping_address']['state'] . " ". $_SESSION['shipping_address']['zip_code'] . "<br>
                        

                </div>
                <div class=\"border border-info col-4\">
                    <p class=\"font-weight-bold\">Payment Method</p>
                                    
                    ". $_SESSION['chechout_card']['type'] . " ending in ". $_SESSION['chechout_card']['number'] . "<br>
                    <strong>Nickname</strong>: ". $_SESSION['chechout_card']['nickname'] . "<br>

                </div>
                <div class=\"border border-info col-4\">
                    <div class=\"border border-danger row\">
                        <p class=\"font-weight-bold ml-3\">Order Summary</p>    
                    </div>
                    <div class=\"border border-danger row\">
                        <div class=\"border border-info col-8\">
                            Number of items:<br>
                            Subtotal:<br>
                            Shipping:<br>
                            Tax:<br>
                            <p class=\"text-right\">
                                <p class=\"font-weight-bold\">Total:</p>
                            </P>

                        </div>
                        <div class=\" border border-info col-4\">

                            <p class=\"text-right\">
                                ". $_SESSION['num_items'] ."<br>
                                $". $_SESSION['subtotal'] ."<br>
                                $0.00<br>
                                $". $_SESSION['tax'] ."<br>
                                <p class=\"font-weight-bold text-right\">$". $_SESSION['total'] ."</p>
                            </p>         
        
                        </div>
                    </div>   
                </div>
            </div>
            <div class=\" row pt-3\">
                <div class=\" col-4\">
                    <p class=\"font-weight-bold pl-3\">Thank you!!!</p>
                </div>
                <div class=\" col-md-4 ml-auto\">
                    <button type=\"button\" class=\"btn btn-success\" data-dismiss=\"modal\" onclick=\"javascript:window.location='index.php'\">Return to homepage</button>
                </div>
                
                
            </div>
        
        ";




    } else if(isset($_POST['change_shipping_address'])) {

        $shippingInfo = array();

        //print_r("token" . $_SESSION['token']);

        $query = "SELECT f_name, l_name, street_address, state, city, zip_code FROM user, address WHERE user.user_id = address.user_id AND user.user_id IN (SELECT user_id FROM user WHERE token = '" . $_SESSION['token'] . "')";

        if($result = mysqli_query($con, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                //echo mysqli_error($con);
                array_push($shippingInfo, $row);
            }
        }

        echo "
            <form id=\"address_selector\">
                <div class=\"form-group\">
                    <label class=\"control-label\">
                        <p class=\"text-center\"><h6>Select an address</h6></p>
                    </label>
      
        ";


        $info = array();
        $counter = 0;
        foreach($shippingInfo as $book) :
            $info[$counter] = $book;

            echo "
                <div class=\"row border p-2\" id=\"address_selector\">
                    <div class=\"form-check\">
                        <input class=\"form-check-input\" type=\"radio\" name=\"selection\" value=" . $counter . ">
                        <label>
                            
                            <p>

                            ". $info[$counter]['f_name'] . ", ". $info[$counter]['l_name'] . "<br>
                            ". $info[$counter]['street_address'] . "<br>
                            ". $info[$counter]['city'] . ", ". $info[$counter]['state'] . " ". $info[$counter]['zip_code'] . "<br>

                            </p>
                        </label>
                    </div>
                </div>

                ";

            $counter++;
            //print_r($info);
        endforeach;

        // save list of addresses to gloaval variable to be used later
        $_SESSION['addresses'] = $info;
        $_SESSION['shipping_address'] = $_SESSION['addresses'][0];


        echo "
            <div class=\"form-group\"> <!-- Submit button !-->
                <button class=\"btn btn-primary\" name=\"submit\" type=\"button\" onclick=\"updateAddress(); return false\">Use this address</button>
            </div>
        </form>
        
        
        ";


    } else if(isset($_POST['update_address'])) {

        //print_r($_POST);

        $selection = $_POST['address'];

        //set gloval variable for shipping used shipping address
        $_SESSION['shipping_address'] = $_SESSION['addresses'][$selection];


        echo "
            <div class=\"row borader\">
                <div id=\"selected_address\" class=\"col-sm-9 \">

                <p>
                    
                    ". $_SESSION['addresses'][$selection]['f_name'] . ", " . $_SESSION['addresses'][$selection]['l_name'] . "<br>
                    ". $_SESSION['addresses'][$selection]['street_address'] . "<br>
                    ". $_SESSION['addresses'][$selection]['city'] . ", " . $_SESSION['addresses'][$selection]['state'] . " " . $_SESSION['addresses'][$selection]['zip_code'] ."

                   </p> 
                </div>
                <div class=\" col-sm-3 pt-1 pb-1\">
                    <input type=\"submit\" class=\"btn btn-link\" onclick=\"changeAddress(); return false\" value=\"Change\">
                    
                </div>
            </div>
        
        ";

    } else if(isset($_POST['change_payment_card'])) {

        $cards = $_SESSION['cards'];


        echo "
            <form id=\"card_selector\">
                <div class=\"form-group\">
                    <label class=\"control-label\">
                        <p class=\"text-center\"><h6>Select a card</h6></p>
                    </label>
      
        ";


        $counter = 0;
        foreach($cards as $book) :
            $card[$counter] = $book;
            $card[$counter]['number'] = $card[$counter]['number'] % 10000;
            //echo "last 4: " .$card[$counter2]['number'];


            echo "
                <div class=\"row border mt-1 p-2\" id=\"card_selector\">
                    <div class=\"form-check\">
                        <input class=\"form-check-input\" type=\"radio\" name=\"selection\" value=" . $counter . ">
                        <label>
                            
                            <p>

                            ". $card[$counter]['type'] . " ending in ". $card[$counter]['number'] . "<br>
                            <strong>Nickname</strong>: ". $card[$counter]['nickname'] . "<br>
                            
                            </p>
                        </label>
                    </div>
                </div>

                ";

            $counter++;
            //print_r($card);
        endforeach;

        echo "
            <div class=\"form-group\"> <!-- Submit button !-->
                <button class=\"btn btn-primary\" name=\"submit\" type=\"button\" onclick=\"updateCard(); return false\">Use this card</button>
            </div>
        </form>
        
        
        ";

    } else if(isset($_POST['update_card'])) {

        $selection = $_POST['card'];
        //$cards = $_SESSION['cards'];

        // set gloval variable for card used
        $_SESSION['chechout_card'] = $_SESSION['cards'][$selection];

        echo "
        <div class=\"row borader\">
            <div id=\"selected_card\" class=\"border border-info col-sm-9 \">
            <p>

                ". $_SESSION['cards'][$selection]['type'] . " ending in ". $_SESSION['cards'][$selection]['number'] . "<br>
                <strong>Nickname</strong>: ". $_SESSION['cards'][$selection]['nickname'] . "<br>
            
            </p>
            </div>
            <div class=\"border border-info col-sm-3 pt-1 pb-1\">
                <input type=\"submit\" class=\"btn btn-link\" onclick=\"changeCard(); return false\" value=\"Change\">
                
            </div>
        </div>
    
        ";

    }

}






?>



