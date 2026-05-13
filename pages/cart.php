<?php
session_start();
include '../includes/db.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Add product to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    // Check if product already exists in cart
    $check = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $check->execute([$user_id, $product_id]);
    $existing_item = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing_item) {
        // If already exists, increase quantity
        $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $update->execute([$user_id, $product_id]);
    } else {
        // Add new product to cart
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $insert->execute([$user_id, $product_id]);
    }

    header("Location: cart.php");
    exit();
}

// Update quantity
if (isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    if ($quantity > 0) {
        $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $update->execute([$quantity, $cart_id, $user_id]);
    }

    header("Location: cart.php");
    exit();
}

// Remove item from cart
if (isset($_POST['remove_item'])) {
    $cart_id = $_POST['cart_id'];

    $delete = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $delete->execute([$cart_id, $user_id]);

    header("Location: cart.php");
    exit();
}

// Fetch cart items
$stmt = $conn->prepare("
    SELECT 
        cart.id AS cart_id,
        cart.quantity,
        products.name,
        products.price,
        products.image
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f6f8;
            color: #333;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 18px 40px;
        }

        .header-container {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-container h1 {
            margin: 0;
            font-size: 28px;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .nav-link:hover {
            color: #2ecc71;
        }

        .welcome-user {
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .logout-button {
            background-color: #e74c3c;
            color: white;
            padding: 9px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .logout-button:hover {
            background-color: #c0392b;
        }

        .cart-container {
            max-width: 1100px;
            margin: 50px auto;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
        }

        .cart-container h2 {
            text-align: center;
            font-size: 32px;
            margin-bottom: 30px;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr 270px;
            align-items: center;
            gap: 25px;
            padding: 20px 0;
            border-bottom: 1px solid #ddd;
        }

        .cart-image {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .cart-details h3 {
            margin: 0 0 8px;
            font-size: 22px;
        }

        .cart-details p {
            margin: 0;
            color: #555;
            font-size: 16px;
        }

        .quantity-form {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
        }

        .quantity-input {
            width: 70px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .update-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 9px 14px;
            border-radius: 5px;
            cursor: pointer;
        }

        .remove-btn {
            background: #2980b9;
            color: white;
            border: none;
            padding: 9px 14px;
            border-radius: 5px;
            cursor: pointer;
        }

        .update-btn:hover {
            background: #2471a3;
        }

        .remove-btn:hover {
            background: #1f618d;
        }

        .total {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin: 30px 0;
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
        }

        .back-btn,
        .checkout-btn {
            background: #2ecc71;
            color: white;
            text-decoration: none;
            padding: 14px 22px;
            border-radius: 6px;
            font-size: 16px;
        }

        .back-btn:hover,
        .checkout-btn:hover {
            background: #27ae60;
        }

        .empty-cart {
            text-align: center;
            font-size: 20px;
            color: #666;
            margin: 30px 0;
        }

        @media (max-width: 768px) {
            .cart-item {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .quantity-form {
                justify-content: center;
                flex-wrap: wrap;
            }

            .cart-actions {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .header-container {
                flex-direction: column;
                gap: 15px;
            }

            nav {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <h1>Online Store</h1>

        <nav>
            <span class="welcome-user">
                Hi, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
            </span>

            <a href="../index.php" class="nav-link">Shop</a>

            <a href="cart.php" class="nav-link">🛒 Cart</a>

            <a href="logout.php" class="logout-button">Logout</a>
        </nav>
    </div>
</header>

<div class="cart-container">
    <h2>Your Cart</h2>

    <?php if (empty($cart_items)) : ?>

        <p class="empty-cart">Your cart is empty.</p>

        <div class="cart-actions">
            <a href="../index.php" class="back-btn">Back to Shop</a>
        </div>

    <?php else : ?>

        <?php foreach ($cart_items as $item) : ?>
            <?php 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>

            <div class="cart-item">
                <img 
                    src="../images/<?= htmlspecialchars($item['image']); ?>" 
                    alt="<?= htmlspecialchars($item['name']); ?>" 
                    class="cart-image"
                >

                <div class="cart-details">
                    <h3><?= htmlspecialchars($item['name']); ?></h3>
                    <p>₹<?= number_format($item['price'], 2); ?> × <?= htmlspecialchars($item['quantity']); ?></p>
                    <p>Subtotal: ₹<?= number_format($subtotal, 2); ?></p>
                </div>

                <form method="POST" action="" class="quantity-form">
                    <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item['cart_id']); ?>">

                    <input 
                        type="number" 
                        name="quantity" 
                        value="<?= htmlspecialchars($item['quantity']); ?>" 
                        min="1" 
                        class="quantity-input"
                    >

                    <button type="submit" name="update_quantity" class="update-btn">
                        Update
                    </button>

                    <button type="submit" name="remove_item" class="remove-btn">
                        Remove
                    </button>
                </form>
            </div>

        <?php endforeach; ?>

        <div class="total">
            Total: ₹<?= number_format($total, 2); ?>
        </div>

        <div class="cart-actions">
            <a href="../index.php" class="back-btn">Back to Shop</a>
            <a href="checkout.php"class="checkout-btn">Proceed to Checkout</a>
        </div>

    <?php endif; ?>
</div>

</body>
</html>