<?php 
require('./connect.php');
session_start();

if(isset($_POST['add_product'])){

    $p_name = $_POST['p_name'];
    $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
    $p_price = $_POST['p_price'];
    $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);

    $p_image = $_FILES['p_image']['name'];
    $p_image_size = $_FILES['p_image']['size'];
    $p_image_tmp_name = $_FILES['p_image']['tmp_name'];
    $p_image_floder = './assets/uploaded_img/'. $p_image;

    if(!empty($p_image)){
     if($p_image_size > 2000000){
            $message[] = 'image size is too large!';
        }else{
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE image= ?");
            $select_products->execute(array($p_image));
            if($select_products->rowCount() > 0){
                $message[] = 'rename image name!';
            }else{
                $insert_products = $conn->prepare("INSERT INTO `products` (name, price, image) 
                VALUES(?, ? , ?)");
                $insert_products->execute(array($p_name, $p_price, $p_image));
                move_uploaded_file($p_image_tmp_name, $p_image_floder);
                $message[] = 'product added sucessfully!';
            }
        }
    }    
    $_SESSION['message'] = $message;
    header('Location: ./admin.php');
    exit();
}


if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];

    $select_products = $conn->prepare("SELECT * FROM  `products` WHERE id= ?");
    $select_products->execute(array($delete_id));
    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
    if($select_products->rowCount() > 0){
        $delete_products = $conn->prepare("DELETE FROM  `products` WHERE id= ?");
        $delete_products->execute(array($delete_id));
        $message[] = 'product has been deleted!';
        unlink('./assets/uploaded_img/'. $fetch_products['image']);
    }else{
        $message[] = 'product cold not be deleted!';
    }
    $_SESSION['message'] = $message;
    header('Location:./admin.php');
    exit();

}

if(isset($_POST['update_product'])){
    $update_id = $_POST['update_id'];
    $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
    $update_p_name = $_POST['update_p_name'];
    $update_p_name = filter_var($update_p_name, FILTER_SANITIZE_STRING);
    $update_p_price = $_POST['update_p_price'];
    $update_p_price = filter_var($update_p_price, FILTER_SANITIZE_STRING);
    $update_p_img = $_FILES['update_p_img']['name'];
    $update_p_img_size = $_FILES['update_p_img']['size'];
    $update_p_img_tmp_name = $_FILES['update_p_img']['tmp_name'];
    $update_p_img_folder = './assets/uploaded_img/'. $update_p_img;
  

    $select_products = $conn->prepare("SELECT * FROM  `products` WHERE id= ?");
    $select_products->execute(array($update_id));
    $fetch_products= $select_products->fetch(PDO::FETCH_ASSOC);
    $old_img = $fetch_products['image'];
    $old_name = $fetch_products['name'];
    $old_price = $fetch_products['price'];

    if($update_p_name != $old_name || $update_p_price!= $old_price){
        $update_products = $conn->prepare("UPDATE `products` SET name= ?, price= ? WHERE id= ?");
        $update_products->execute(array($update_p_name, $update_p_price, $update_id));
        $message[] = 'product name and price updated successfully!';
    }


    
    if(!empty($update_p_img)){
        if($update_p_img_size > 2000000){
            $message[] = 'image file size is too large!image!';
            $_SESSION['message'] = $message;
        
        }else{
                $select_products = $conn->prepare("SELECT * FROM `products` WHERE  image= ?");
                $select_products->execute(array($update_p_img));
                if($select_products->rowCount() > 0){
                    $message[] = 'rename file name!';
                }else{
                    $update_products = $conn->prepare("UPDATE `products` SET image= ? WHERE id= ?");
                    $update_products->execute(array($update_p_img, $update_id));
                    move_uploaded_file($update_p_img_tmp_name, $update_p_img_folder);
                    unlink('./assets/uploaded_img/'. $old_img);
                    $message[] = 'image updated!';
                }
            }
    }
    $_SESSION['message'] = $message;
    header('Location:./admin.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin panel</title>

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
    <section>
        <form action="" class="add-product-form" method="post" enctype="multipart/form-data">
            <h3>add a new product</h3>
            <input type="text" name="p_name" class="box" placeholder="enter the product name" required>
            <input type="number" name="p_price" min="0" class="box" placeholder="enter the product price" required>
            <input type="file" name="p_image" class="box" accept="image/jpg, image/png, image/jpeg">
            <input type="submit" value="add the product" class="btn" name="add_product">
        </form>
    </section>

    <section class="display-product-table">
        <table>
            <thead>
                <th>product image</th>
                <th>product name</th>
                <th>product price</th>
                <th>action</th>
            </thead>

            <tbody>
                <?php 
                $select_products = $conn->prepare("SELECT * FROM `products`");
                $select_products->execute();
                if($select_products->rowCount() > 0){
                    while($fetch_products= $select_products->fetch(PDO::FETCH_ASSOC)){
                ?>

                    <tr>
                        <td><img src="./assets/uploaded_img/<?= $fetch_products['image']; ?>" height="100" alt=""></td>
                        <td><?= $fetch_products['name']; ?></td>
                        <td>$<?= number_format($fetch_products['price']); ?>-/</td>
                        <td>
                            <a href="./admin.php?delete=<?= $fetch_products['id']; ?>" onclick="return confirm('are your sure you want to delete this?');" 
                            class="delete-btn"><i class="fas fa-trash"></i> delete</a>
                            <a href="./admin.php?edit=<?= $fetch_products['id']; ?>" class="option-btn">
                            <i class="fas fa-edit"></i>update</a>
                        </td>
                    </tr>

                <?php 
                }
                }else{
                    echo '<div class="empty">no product added</div>';
                }
                ?>
            </tbody>
        </table>
    </section>

    <section class="edit-form-container">
        <?php 
        if(isset($_GET['edit'])){
            $edit_id = $_GET['edit'];
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE id= ?");
            $select_products->execute(array($edit_id));
            if($select_products->rowCount() > 0){
                while($fetch_products= $select_products->fetch(PDO::FETCH_ASSOC)){

        ?>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="update_id" value="<?= $fetch_products['id']; ?>">
                <img src="./assets/uploaded_img/<?= $fetch_products['image']; ?>" height="200" alt="">
                <input type="text" name="update_p_name" class="box" value="<?= $fetch_products['name'];?>">
                <input type="number" name="update_p_price" class="box" min="0" value="<?= $fetch_products['price'];?>">
                <input type="file" name="update_p_img" class="box" accept="image/jpg, image/png, image/jpeg">
                <input type="submit" value="update the product" name="update_product" class="btn">
                <input type="reset" value="cancel" class="option-btn" id="close-edit">
            </form>
        <?php 
        }
        }
        echo '<script>document.querySelector(".edit-form-container").style.display = 
        "flex";</script>';
        }
        ?>
    </section>
</div>



<!-- custom js -->
<script src="./assets/js/app.js"></script>
    
</body>
</html>