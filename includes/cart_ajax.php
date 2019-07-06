
<?php
session_start();
//require_once('connect.inc.php');
//print_r($_POST);

//echo $_POST;
    
if(isset($_SESSION['token'])) {

    require_once('connect.inc.php');

    if(isset($_POST['add_to_cart'])) {

        // (token, book_id, qty)
        $query = $con->prepare('CALL addToCart(?,?,?)');
        $query->bind_param('sii', $_SESSION['token'], $_POST['book_id'], $_POST['qty']);
        $query->execute();
        $query->close();

        echo "
            <div class=\"form-group\">
                <input type=\"hidden\" name=\"book_id\" value=" . $_POST['book_id'] . ">
                    <select class=\"form-control\" id=\"qty\" name=\"qty\">
                        <option value=\"1\" selected=\"1\">1</option>
                        <option value=\"2\">2</option>
                        <option value=\"3\">3</option>
                        <option value=\"4\">4</option>
                        <option value=\"5\">5</option>
                        <option value=\"6\">6</option>
                        <option value=\"7\">7</option>
                        <option value=\"8\">8</option>
                        <option value=\"9\">9</option>
                    </select>
                    <button type=\"submit\" id=\"test\" name=\"add_to_cart\" value=\"true\" class=\"btn btn-primary btn-sm mt-1\" >ADD TO CART </button>                             
                </div>";

    } else if(isset($_POST['changeQty'])) {

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

                echo "<a class=\"nav-link\" href=\"cart.php\"><span class=\"fa fa-shopping-cart\"> ". $row['number_of_items'] . "</span></a>";
                //echo " " . $row['number_of_items'];
            }
        }
        
    } else if(isset($_POST['verify_delete'])) {

        $books_on_cart = array();
        $image_url = "";
        $title = "";
        $author ="";

        echo $_POST['book_id'];

        $query = " SELECT image_url, title, authors FROM book WHERE book_id = '" . $_POST['book_id'] . "'";
        //$query = "CALL getCart('" . $_SESSION['token'] . "')";

        if($result = mysqli_query($con, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                echo mysqli_error($con);
                array_push($books_on_cart, $row);
            }
        }

        // Free Result
        mysqli_free_result($result);

        // Close Connection
        mysqli_close($con);

        foreach($books_on_cart as $book) :

            $image_url = $book['image_url'];
            $title = $book['title'];
            $author = $book['authors'];


        endforeach;


        echo "
            


            <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
                <div class=\"modal-content\">
                    <div class=\"modal-header\">
                        <h5 class=\"modal-title\" id=\"verifyDeleteModalTitle\">Delete</h5>
                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                        <span aria-hidden=\"true\">&times;</span>
                        </button>
                    </div>
                    <div class=\"modal-body\" id=\"verify_delete_modal\">


                        <div class=\"container\">
                            <div class=\"row\">
                                <div class=\"col-sm-3\">
                                    <img src=" . $image_url . " width=\"100\" height=\"100\" alt=\"...\" class=\"img-responsive\" style=\"float:left\" /> 
                                </div>
                                <div class=\"col-sm-9\">
                                    <strong> Title</strong>: ".$title ."<br>
                                    <strong> Author</strong>: ".$author ."<br><br>
                                </div>
                            </div>
                        </div>
                    
                        
                        
                        <p class=\"text-danger\"><br><strong>Are you sure you want to delete this book?</strong></p>
                    </div>
                    <div class=\"modal-footer\">
                        

                        <form name=\"delete-item\" method=\"POST\" action=\"cart.php\">
                            <input type=\"hidden\" name=\"book_id\" value='" . $_POST['book_id'] ."'>
                            <input type=\"hidden\" name=\"delete\" value=\"true\">
                            <button type=\"submit\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Cancel</button>
                            <input type=\"submit\" class=\"btn btn-danger\" value=\"Delete\">
                        </form>
                    </div>
                </div>
            </div>";

    }
}

?>