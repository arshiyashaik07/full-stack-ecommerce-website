<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$product_id = $_GET['id'];

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);

    if (empty($name) || empty($price) || empty($description) || empty($image)) {
        $message = "All fields are required.";
    } else {
        $update = $conn->prepare("
            UPDATE products 
            SET name = ?, price = ?, description = ?, image = ? 
            WHERE id = ?
        ");

        if ($update->execute([$name, $price, $description, $image, $product_id])) {
            header("Location: manage_products.php");
            exit();
        } else {
            $message = "Failed to update product.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
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
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #27ae60;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #3498db;
            text-decoration: none;
        }

        .message {
            background: #ffe6e6;
            color: #cc0000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }

        .preview {
            text-align: center;
            margin-bottom: 15px;
        }

        .preview img {
            width: 120px;
            height: 120px;
            object-fit: contain;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Product</h2>

    <?php if (!empty($message)) : ?>
        <div class="message">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="preview">
        <img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="Product Image">
    </div>

    <form method="POST" action="">
        <label>Product Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

        <label>Price</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']); ?>" required>

        <label>Description</label>
        <textarea name="description" required><?= htmlspecialchars($product['description']); ?></textarea>

        <label>Image Filename</label>
        <input type="text" name="image" value="<?= htmlspecialchars($product['image']); ?>" required>

        <button type="submit">Update Product</button>
    </form>

    <a href="manage_products.php" class="back-link">Back to Manage Products</a>
</div>

</body>
</html>