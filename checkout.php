<?php
    if(empty($_GET['pro_id'])){
        header("location: index.php");
        exit;
    }
    include("database/db.php");

    $product_id = $_GET['pro_id'];
    $get_product= "SELECT * FROM product WHERE product_id=$product_id";
    $query = mysqli_query($con,$get_product);
    if($query->num_rows > 0){
        $row_p = mysqli_fetch_array($query);
        $product_name = $row_p['product_title'];
        $product_price = $row_p['product_price'];
    
        $currency = $row_p['currency'];
    }else{
        header("location:index.php");
    }

    require "setup/init.php";
    // \Stripe\Stripe::setVerifySslCerts(false);
    \Stripe\Stripe::setApiKey('sk_test_51IidfrCuTOTS51rEPNfjvHaLT6dk15sYYrxEtC0FQU2IZOIo0HrLZjUidEZi5vIuSDZLgY1CQHWbIdwPksCyDfX500DyvL02nT');

    $session = \Stripe\Checkout\Session::create([
          'payment_method_types' => ['card'],
          'line_items' => [[
        'price_data' => [
          'currency' => $currency,
          'product_data' => [
            'name' => $product_name,
            
            
          ],
          'unit_amount' => $product_price,
        ],
        'quantity' => 1,
      ]],
      'mode' => 'payment',
      'success_url' => 'http://localhost/stripeecom/success.php',
      'cancel_url' => 'http://localhost/stripeecom/cancel.php',
    ]);
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
</head>
<body>
    

<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
      // Create an instance of the Stripe object with your publishable API key
      var stripe = Stripe('pk_test_51IidfrCuTOTS51rEVB0SFWTMRRdobGiJeuwB5RRTAsS9qHZm1HLajF9zURooya4CtMu1s4Fs21UF0xWm4vrPow2h00bRAtzAtL');

    var session = "<?php echo $session['id']; ?>"
      
        stripe.redirectToCheckout({ sessionId: session })
      
        .then(function(result) {
          // If `redirectToCheckout` fails due to a browser or network
          // error, you should display the localized error message to your
          // customer using `error.message`.
          if (result.error) {
            alert(result.error.message);
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
        });
    
    </script>

</body>
</html>