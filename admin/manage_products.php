<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            color: #333;
        }

        .top-actions {
            text-align: right;
            margin-bottom: 20px;
        }

        .add-btn {
            background: #2ecc71;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .add-btn:hover {
            background: #27ae60;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #28a745;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        .product-img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .edit-btn {
            color: #3498db;
            border: 1px solid #3498db;
            padding: 7px 12px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 8px;
            display: inline-block;
        }

        .edit-btn:hover {
            background: #3498db;
            color: white;
        }

        .delete-btn {
            color: #e74c3c;
            border: 1px solid #e74c3c;
            padding: 7px 12px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .delete-btn:hover {
            background: #e74c3c;
            color: white;
        }

        .back-wrapper {
            text-align: center;
            margin-top: 30px;
        }

        .back-btn {
            background: #3498db;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .back-btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Products</h2>

    <div class="top-actions">
        <a href="add_product.php" class="add-btn">Add New Product</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?= htmlspecialchars($product['id']); ?></td>

                    <td><?= htmlspecialchars($product['name']); ?></td>

                    <td>₹<?= number_format($product['price'], 2); ?></td>

                    <td><?= htmlspecialchars($product['description']); ?></td>

                    <td>
                        <img 
                            src="../images/<?= htmlspecialchars($product['image']); ?>" 
                            alt="<?= htmlspecialchars($product['name']); ?>" 
                            class="product-img"
                        >
                    </td>

                    <td>
                        <a 
                            href="edit_product.php?id=<?= htmlspecialchars($product['id']); ?>" 
                            class="edit-btn"
                        >
                            Edit
                        </a>

                        <a 
                            href="delete_product.php?id=<?= htmlspecialchars($product['id']); ?>" 
                            class="delete-btn"
                            onclick="return confirm('Are you sure you want to delete this product?');"
                        >
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="back-wrapper">
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</div>

</body>
</html>