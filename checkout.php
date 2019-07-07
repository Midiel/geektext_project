
<?php 






?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php 
        include("includes/navbar_libs.php"); 
        //include_once("includes/navbar.php");
    ?>
    <title>Checkout</title>
    		
</head>

<body>

    <!-- navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">GeekText</a>

        <div class="mx-auto order-0">
            <h5 class="navbar-brand mx-auto" href="#">Checkout</h5>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-collapse2">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>



    <div class="container border border-primary mt-5 pt-5 pb-5">
        <div class="row border">       <!-- only one row -->
           
            <div class="border border-info col-8">         <!-- left column begins -->
                left column

                <div class="row border">        <!-- row 1 begins -->
                    <div class="border border-info col-1">
                        <h5>1</h5>
                    </div>
                    <div class="border border-info col-3">
                        <h5>Shipping address</h5>
                    </div>
                    <div class="border border-info col-6">
                        <strong>Address</strong>
                    </div>
                    <div class="border border-info col-2">
                        <strong>Change</strong>
                    </div>

                </div>

                <div class="row border">        <!-- row 2 begins -->
                    <div class="border border-info col-1">
                        <h5>2</h5>
                    </div>
                    <div class="border border-info col-3">
                        <h5>Payment method</h5>
                    </div>
                    <div class="border border-info col-6">
                        <strong>Card info</strong>
                    </div>
                    <div class="border border-info col-2">
                        <strong>Change</strong>
                    </div>

                </div>

                <div class="row border">        <!-- row 3 begins -->
                    <div class="border border-info col-1">
                        <h5>3</h5>
                    </div>
                    <div class="border border-info col-11">
                        <h5>Review items</h5>
                    </div>
                   

                </div>




            </div>     <!-- end of left column -->

            <div class="border border-info col-4">         <!-- right column -->
                Order Summary here

                <div class="row">
                    <div class="border border-info col-8">
                        Items:<br>
                        Shipping:<br>
                        Subtotal:<br>
                        Tax:<br>

                    </div>

                    <div class="border border-info col-4">
                        <p class="text-right">
                            $90.00<br>
                            $0.00<br>
                            $90.00<br>
                            $6.22<br>
                        </p>
                        
                    </div>
                </div>

                <div class="border border-info row">
                    <div class="border border-info col-8">
                        <h5 class="mt-3">Order total:</h5>
                    </div>

                    <div class="border border-info col-4">
                        <p>
                            <h5 class="mt-3 text-right">$96.22</h5>
                        </p>
                    </div>

                </div>


            </div>


               
        </div>
            

    </div>
        
    





</body>

</html>