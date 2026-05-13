<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

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

foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$message = "";

// Place order button logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $payment_method = trim($_POST['payment_method']);

    if (empty($full_name) || empty($phone) || empty($address) || empty($payment_method)) {
        $message = "All fields are required.";
    } elseif (empty($cart_items)) {
        $message = "Your cart is empty.";
    } else {
        // For now, just clear the cart after order
        $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $delete_cart->execute([$user_id]);

        header("Location: order_success.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>

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
        }

        nav {
            display: flex;
            gap: 18px;
            align-items: center;
        }

        nav a,
        .welcome-user {
            color: white;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .logout-button {
            background: #e74c3c;
            padding: 9px 15px;
            border-radius: 5px;
        }

        .checkout-container {
            max-width: 1000px;
            margin: 45px auto;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            font-size: 32px;
            margin-bottom: 30px;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .order-summary,
        .checkout-form {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #ddd;
            padding: 12px 0;
        }

        .cart-item img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .cart-item h4 {
            margin: 0 0 5px;
        }

        .cart-item p {
            margin: 0;
            color: #555;
        }

        .total {
            font-size: 24px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        textarea {
            height: 100px;
            resize: none;
        }

        button {
            width: 100%;
            background: #2ecc71;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #27ae60;
        }

        .message {
            background: #ffe6e6;
            color: #cc0000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #28a745;
            font-weight: bold;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
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

            <a href="../index.php">Shop</a>
            <a href="cart.php">Cart</a>
            <a href="logout.php" class="logout-button">Logout</a>
        </nav>
    </div>
</header>

<div class="checkout-container">
    <h2>Checkout</h2>

    <?php if (!empty($message)) : ?>
        <div class="message">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)) : ?>

        <p style="text-align:center;">Your cart is empty.</p>
        <a href="../index.php" class="back-link">Back to Shop</a>

    <?php else : ?>

        <div class="checkout-grid">

            <div class="order-summary">
                <h3>Order Summary</h3>

                <?php foreach ($cart_items as $item) : ?>
                    <div class="cart-item">
                        <img 
                            src="../images/<?= htmlspecialchars($item['image']); ?>" 
                            alt="<?= htmlspecialchars($item['name']); ?>"
                        >

                        <div>
                            <h4><?= htmlspecialchars($item['name']); ?></h4>
                            <p>₹<?= number_format($item['price'], 2); ?> × <?= htmlspecialchars($item['quantity']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="total">
                    Total: ₹<?= number_format($total, 2); ?>
                </div>
            </div>

            <div class="checkout-form">
                <h3>Shipping Details</h3>

                <form method="POST" action="">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="Enter your full name" required>

                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="Enter phone number" required>

                    <label>Address</label>
                    <textarea name="address" placeholder="Enter delivery address" required></textarea>

                    <label>Payment Method</label>
                    <select name="payment_method" required>
                        <option value="">Select payment method</option>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                        <option value="UPI">UPI</option>
                        <option value="Card">Card</option>
                    </select>

                    <button type="submit">Place Order</button>
                </form>
            </div>

        </div>

        <a href="cart.php" class="back-link">Back to Cart</a>

    <?php endif; ?>
</div>

</body>
</html>