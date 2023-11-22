<?php 
require('./connect.php');
session_start();

if(isset($_POST['order_btn'])){
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $number = $_POST['number'];
    $number = filter_var($number, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $method = $_POST['method'];
    $method = filter_var($method, FILTER_SANITIZE_STRING);
    $flat = $_POST['flat'];
    $flat = filter_var($flat, FILTER_SANITIZE_STRING);
    $street = $_POST['street'];
    $street = filter_var($street, FILTER_SANITIZE_STRING);
    $city = $_POST['city'];
    $city = filter_var($city, FILTER_SANITIZE_STRING);
    $state = $_POST['state'];
    $state = filter_var($state, FILTER_SANITIZE_STRING);
    $country = $_POST['country'];
    $country = filter_var($country, FILTER_SANITIZE_STRING);
    $pin_code = $_POST['pin_code'];
    $pin_code = filter_var($pin_code, FILTER_SANITIZE_STRING);


    $select_cart = $conn->prepare("SELECT * FROM `cart`");
    $select_cart->execute();
    $price_total = 0;
    if($select_cart->rowCount() > 0){
        while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
            $product_name[] = $fetch_cart['name']. '('.$fetch_cart['quantity'].')';
            $product_price = $fetch_cart['price'] * $fetch_cart['quantity'];
            $price_total += $product_price; 
        }
        $total_products = implode(',' ,$product_name);

        $insert_orders = $conn->prepare("INSERT INTO `orders` (name, number, email, method, flat, street, city, state, country,
         pin_code, total_products, total_price) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
         $insert_orders->execute(array($name, $number, $email, $method, $flat, $street, $city, $state,$country, $pin_code,
        $total_products, number_format($price_total)));
   
        echo '<div class="order-message-container">
        <div class="message-container">
            <h3>thank you for shopping!</h3>
            <div class="order-detail">
                <span>'.$total_products.'</span>
                <span class="total"> total :$'.number_format($price_total).'/-</span>
            </div>
            <div class="customer-details">
                <p>your name : <span>'.$name.'</span></p>
                <p>your number : <span>'.$number.'</span></p>
                <p>your email : <span>'.$email.'</span></p>
                <p>your address : <span>'.$flat.', '.$street.', '.$city.',
                '.$state.', '.$country.'- '.$pin_code.'  </span></p>
                <p>your payment mode : <span>'.$method.'</span></p>
                <p>(*pay when product arrives*)</p>
            </div>
            <a href="./product.php" class="btn">continue shopping</a>
        </div>
    </div>';

        $delete_cart = $conn->prepare("DELETE FROM `cart`");
        $delete_cart->execute();
    }

    


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>checkout</title>

    <!-- font awesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- custom css -->
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>


<?php require('./header.php'); ?>

<div class="container">

    <section class="check-form">

        <h1 class="heading">complete your order</h1>
            
        <form action="" method="post">

            
        <div class="display-order">
            <?php  
            $select_cart = $conn->prepare("SELECT * FROM `cart`");
            $select_cart->execute();
            $grand_total = 0;
            if($select_cart->rowCount() > 0){
                while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){

                 $total_price = $fetch_cart['price'] * $fetch_cart['quantity'] ;
                 $grand_total += $total_price;
                
            ?>
                <span><?= $fetch_cart['name']; ?>(<?= $fetch_cart['quantity'];?>)</span>
            <?php 
            }
            }else{
                echo '<div class="display-order"><span>your cart is empty</span></div>';
            }
            ?>
            <span class="grand-total">grand total : <?= number_format($grand_total); ?></span>
        </div>
            
            <div class="flex">
                <div class="input-box">
                    <span>your name</span>
                    <input type="text" name="name" placeholder="enter your name" required>
                </div>
    
                <div class="input-box">
                    <span>your number</span>
                    <input type="number" name="number" placeholder="enter your number" required>
                </div>
    
                <div class="input-box">
                    <span>your email</span>
                    <input type="email" name="email" placeholder="enter your email" required>
                </div>
    
                <div class="input-box">
                    <span>payment method</span>
                    <select name="method">
                        <option value="cah on delivery">cah on delivery</option>
                        <option value="credit card">credit card</option>
                        <option value="paypal">paypal</option>
                    </select>
                </div>
    
                
                <div class="input-box">
                    <span>address line 1</span>
                    <input type="text" name="flat" placeholder="e.g. flat no." required>
                </div>
                
                <div class="input-box">
                    <span>address line 2</span>
                    <input type="text" name="street" placeholder="e.g. street name" required>
                </div>
                
                <div class="input-box">
                    <span>city</span>
                    <input type="text" name="city" placeholder="e.g. shibuya" required>
                </div>
                
                <div class="input-box">
                    <span>state</span>
                    <input type="text" name="state" placeholder="e.g. tokyo" required>
                </div>
                
                <div class="input-box">
                    <span>country</span>
                    <input type="text" name="country" placeholder="e.g. japan" required>
                </div>
                
                <div class="input-box">
                    <span>pin code</span>
                    <input type="text" name="pin_code" placeholder="e.g. 123456" required>
                </div>
                <input type="submit" name="order_btn" class="btn" value="order now">
            </div>

        </form>
        
    </section>

</div>



<!-- custom js -->
<script src="./assets/js/app.js"></script>
    
</body>
</html>