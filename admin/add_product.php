<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);

    if (empty($name) || empty($price) || empty($description)) {
        $message = "All fields are required.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        $message = "Please select a product image.";
    } else {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];

        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($image_extension, $allowed_extensions)) {
            $message = "Only JPG, JPEG, PNG, and WEBP images are allowed.";
        } else {
            $new_image_name = time() . "_" . uniqid() . "." . $image_extension;
            $upload_path = "../images/" . $new_image_name;

            if (move_uploaded_file($image_tmp, $upload_path)) {
                $stmt = $conn->prepare("
                    INSERT INTO products (name, price, description, image) 
                    VALUES (?, ?, ?, ?)
                ");

                if ($stmt->execute([$name, $price, $description, $new_image_name])) {
                    header("Location: manage_products.php");
                    exit();
                } else {
                    $message = "Failed to add product.";
                }
            } else {
                $message = "Image upload failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 520px;
            margin: 50px auto;
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
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
            font-size: 14px;
        }

        textarea {
            height: 110px;
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
            text-align: center;
            margin-top: 18px;
            color: #28a745;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add Product</h2>

    <?php if (!empty($message)) : ?>
        <div class="message">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Product Name</label>
        <input type="text" name="name" placeholder="Enter product name" required>

        <label>Price</label>
        <input type="number" step="0.01" name="price" placeholder="Enter price" required>

        <label>Description</label>
        <textarea name="description" placeholder="Enter product description" required></textarea>

        <label>Image</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit">Add Product</button>
    </form>

    <a href="manage_products.php" class="back-link">Back to Manage Products</a>
</div>

</body>
</html>