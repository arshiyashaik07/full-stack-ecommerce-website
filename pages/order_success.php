
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .success-box {
            background: white;
            padding: 40px;
            max-width: 500px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.08);
        }

        h1 {
            color: #27ae60;
        }

        p {
            color: #555;
            font-size: 16px;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            background: #2ecc71;
            color: white;
            padding: 12px 22px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            background: #27ae60;
        }
    </style>
</head>
<body>

<div class="success-box">
    <h1>Order Placed Successfully!</h1>
    <p>Thank you for shopping with us. Your order has been placed successfully.</p>

    <a href="../index.php">Continue Shopping</a>
</div>

</body>
</html>