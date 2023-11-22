<header class="header">

    <div class="flex">

        <a href="#" class="logo">foodies</a>

        <nav class="navbar">
            <a href="./admin.php">add products</a>
            <a href="./product.php">view products</a>
        </nav>

        <?php 
            $select_cart = $conn->prepare("SELECT * FROM  `cart`");
            $select_cart->execute();
            $count_cart = $select_cart->rowCount();
        ?>
        <a href="./cart.php" class="cart">cart <span><?= $count_cart; ?></span></a>

        <div id="menu-btn" class="fas fa-bars"></div>

    </div>

</header>