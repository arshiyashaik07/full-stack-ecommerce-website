<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #333;
        }

        header {
            background: #2c3e50;
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

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 9px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .dashboard-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
        }

        .dashboard-container h2 {
            margin-top: 0;
            font-size: 28px;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-top: 30px;
        }

        .card {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #eee;
        }

        .card h3 {
            margin-bottom: 10px;
        }

        .card a {
            display: inline-block;
            margin-top: 10px;
            background: #2ecc71;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
        }

        .card a:hover {
            background: #27ae60;
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }

            .header-container {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <h1>Admin Dashboard</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<div class="dashboard-container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></h2>
    <p>You can manage your e-commerce store from here.</p>

    <div class="dashboard-cards">
        <div class="card">
            <h3>Products</h3>
            <p>Add, update, or delete products.</p>
            <a href="manage_products.php">Manage Products</a>
        </div>

        <div class="card">
            <h3>Add Product</h3>
            <p>Add a new product to your store.</p>
            <a href="add_product.php">Add Product</a>
        </div>

        <div class="card">
            <h3>Store</h3>
            <p>Go back to customer store page.</p>
            <a href="../index.php">View Store</a>
        </div>
    </div>
</div>

</body>
</html>