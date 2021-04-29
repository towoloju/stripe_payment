<?php
    include("database/db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Stripe Checkout</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1>Stripe Checkout Page with PHP</h1>

            </div>
        </div>
    </div>
    <?php
        $get_product = "SELECT * FROM product ORDER BY product_id ASC LIMIT 0,3";
        $query = mysqli_query($con,$get_product);
        if($query->num_rows > 0){
            while ($row = mysqli_fetch_array($query)){
                $pro_id = $row['product_id'];
                $pro_name = $row['product_title'];
                $pro_model = $row['product_model'];
                $price = $row['product_price']/100;
                $pro_img = $row['product_img1'];
          
    ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="product-container">
                    <img src="product_images/<?php echo $pro_img ?> " class="product-image img-responsive" alt="product_image">
                    <div class="product-content">
                        <h3><?php echo $pro_name ?> </h3>
                        <p><?php echo $pro_model?></p>
                        <p><?php echo $price?></p>
                        <a href="checkout.php?pro_id=<?php echo $pro_id ?>" class="btn btn-primary">Buy Now</a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>



    <?php
          }
        }
    ?>

<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>