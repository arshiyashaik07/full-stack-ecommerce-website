<?php
session_start();

include 'includes/db.php';

// Fetch products from the database
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>

    <!-- v=20 is added to force browser to load latest CSS -->
    <link rel="stylesheet" href="css/style.css?v=20">
</head>
<body>

    <!-- Header -->
    <header>
        <div class="header-container">
            <h1>Welcome to Our Store</h1>

            <nav>
                <?php if (isset($_SESSION['user_id'])) : ?>

                    <span class="welcome-user">
                        Hi, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                    </span>

                    <a href="pages/cart.php" class="nav-link">🛒 Cart</a>

                    <a href="pages/logout.php" class="logout-button">Logout</a>

                <?php else : ?>

                    <a href="pages/login.php" class="nav-link">Login</a>

                    <a href="pages/register.php" class="nav-link">Register</a>

                    <a href="pages/cart.php" class="nav-link">🛒 Cart</a>

                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Products Section -->
    <main class="main-container">
        <h2 class="section-title">Products</h2>

        <div class="product-list">

            <?php if (empty($products)) : ?>

                <p>No products available.</p>

            <?php else : ?>

                <?php foreach ($products as $product) : ?>

                    <div class="product">

                        <?php if (!empty($product['image'])) : ?>
                            <img 
                                src="images/<?= htmlspecialchars($product['image']); ?>" 
                                alt="<?= htmlspecialchars($product['name']); ?>" 
                                class="product-image"
                            >
                        <?php endif; ?>

                        <h3><?= htmlspecialchars($product['name']); ?></h3>

                        <p class="price">
                            ₹<?= number_format($product['price'], 2); ?>
                        </p>

                        <p class="description">
                            <?= htmlspecialchars($product['description']); ?>
                        </p>

                        <?php if (isset($_SESSION['user_id'])) : ?>

                            <form method="POST" action="pages/cart.php">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']); ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart-button">
                                    Add to Cart
                                </button>
                            </form>

                        <?php else : ?>

                            <a href="pages/login.php" class="login-to-cart-button">
                                Login to Add Cart
                            </a>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; <?= date('Y'); ?> Online Store. All rights reserved.</p>
    </footer>

</body>
</html>