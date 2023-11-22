<?php 
require('./connect.php');
session_start();

if(isset($_POST['add_to_cart'])){

    $product_name = $_POST['product_name'];
    $product_name = filter_var($product_name, FILTER_SANITIZE_STRING);
    $product_price = $_POST['product_price'];
    $product_price = filter_var($product_price, FILTER_SANITIZE_STRING);
    $product_image = $_POST['product_image'];
    $product_image = filter_var($product_image, FILTER_SANITIZE_STRING);
    $product_qty = 1;

    $select_cart = $conn->prepare("SELECT * FROM  `cart` WHERE name= ?");
    $select_cart->execute(array($product_name));
    if($select_cart->rowCount() > 0){
        $message[] = 'product already added to cart!';
    }else{
        $insert_cart = $conn->prepare("INSERT INTO `cart` (name, price, image, quantity) VALUES
        (?, ?, ?, ?)");
        $insert_cart->execute(array($product_name, $product_price, $product_image, $product_qty));
        $message[] = 'product added to cart successfully!';
    }
    $_SESSION['message'] = $message;
    header('Location:./product.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>products</title>

    
    <!-- font awesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- custom css -->
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
<?php    
if(isset($_SESSION['message'])){
    foreach($_SESSION['message'] as $message){
        echo '<div class="message"><span>'.$message.'</span>
        <i class="fas fa-times" onclick="this.parentElement.style.display= `none`;"></i></div>';
    }
    unset($_SESSION['message']);
}
?>    

<?php require('./header.php'); ?>

<div class="container">
    
    <section class="products">

        <h1 class="heading">latest products</h1>

        <div class="box-container">
            <?php 
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products->execute();
            if($select_products->rowCount() > 0){
                while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
            ?>

            <form action="" method="post">
                <input type="hidden" name="product_name" value="<?= $fetch_products['name']; ?>">
                <input type="hidden" name="product_price" value="<?= $fetch_products['price']; ?>">
                <input type="hidden" name="product_image" value="<?= $fetch_products['image']; ?>">
                <div class="box">
                    <img src="./assets/uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                    <h3><?= $fetch_products['name']; ?></h3>
                    <div class="price">$<?= number_format($fetch_products['price']); ?>/-</div>
                    <input type="submit" class="btn" name="add_to_cart" value="add to cart">
                </div>
            </form>

            <?php 
            }
            }
            ?>
        </div>
    </section>
</div>

    
<!-- custom js -->
<script src="./assets/js/app.js"></script>

</body>
</html>