<?php 
require('./connect.php');
session_start();

if(isset($_POST['update_btn'])){

    $update_qty_id = $_POST['update_qty_id'];
    $update_qty_id = filter_var($update_qty_id, FILTER_SANITIZE_STRING);
    $update_qty = $_POST['update_qty'];
    $update_qty = filter_var($update_qty, FILTER_SANITIZE_STRING);

   $update_cart = $conn->prepare("UPDATE  `cart` SET quantity= ? WHERE id= ?");
   $update_cart->execute(array($update_qty, $update_qty_id));

}

if(isset($_GET['remove'])){
    $delete_id = $_GET['remove'];
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE id= ?");
    $delete_cart->execute(array($delete_id));
    $message[]= 'this product deleted!';
    $_SESSION['message'] = $message;
    header('Location:./cart.php');
    exit();
}else{
    $delete_id = '';
}

if(isset($_GET['delete_all'])){
    $delete_cart = $conn->prepare("DELETE FROM `cart`");
    $delete_cart->execute();
    $message[] = 'all deleted!';
    $_SESSION['message'] = $message;
    header('Location:./cart.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shopping cart</title>

    
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

    <section class="shopping-cart">

        <h1 class="heading">shopping cart</h1>

        <table>
            <thead>
                <th>image</th>
                <th>name</th>
                <th>price</th>
                <th>quantity</th>
                <th>total price</th>
                <th>action</th>
            </thead>

            <tbody>
                                    
                <?php 
                    $grand_total = 0;
                    $select_cart = $conn->prepare("SELECT * FROM  `cart`");
                    $select_cart->execute();
                    if($select_cart->rowCount() > 0){
                        while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                        $sub_total= $fetch_cart['quantity'] * $fetch_cart['price'];
                ?>
                <tr>
        
                    <td><img src="./assets/uploaded_img/<?= $fetch_cart['image']; ?>" height="100"></td>
                    <td><?= $fetch_cart['name']; ?></td>
                    <td>$<?= number_format($fetch_cart['price']); ?>/-</td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="update_qty_id" value="<?= $fetch_cart['id']; ?>">
                            <input type="number" name="update_qty" value="<?= number_format($fetch_cart['quantity']); ?>" min="1">
                            <input type="submit" name="update_btn" value="update">
                        </form>
                    </td>
                    <td>$<?= number_format($sub_total); ?>/-</td>
                    <td><a href="./cart.php?remove=<?= $fetch_cart['id']; ?>" class="delete-btn"
                    onclick="return confirm('remove item from cart?');"><i class="fas fa-trash"></i>remve</a></td>
                    
                </tr>
                <?php 
                $grand_total +=  $sub_total;
                }
                }
                ?>
                <tr class="table-bottom">
                    <td><a href="./product.php" class="option-btn" style="margin-top: 0;">continue shopping</a></td>
                    <td colspan="3">grand total</td>
                    <td>$<?= number_format($grand_total); ?>/-</td>
                    <td><a href="./cart.php?delete_all" class="delete-btn <?php echo ($grand_total > 1)?'' : 'disabled';?>"
                    onclick="return confirm('are you sure you want to delete all?');">
                    <i class="fas fa-trash"></i> delete all</a></td>
                </tr>
            </tbody>
        </table>

        <div class="checkout-btn">
            <a href="./checkout.php" class="btn <?php echo ($grand_total > 1)? '':'disabled';  ?>">proced to checkout</a>
        </div>

    </section>
</div>
    
<!-- custom js -->
<script src="./assets/js/app.js"></script>

</body>
</html>