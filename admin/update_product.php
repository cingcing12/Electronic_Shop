<?php
require_once '../init.php'; // Include the initialization file

// Check if data is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';

    // Validate the data
    if (empty($name) || $category_id <= 0 || $price <= 0 || empty($image)) {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required and must be valid!'
        ]);
        exit;
    }

    try {
        // First, check if the product exists
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found.'
            ]);
            exit;
        }

        // Check if the data has changed
        if ($product['name'] === $name && $product['category_id'] === $category_id && $product['price'] === $price && $product['image'] === $image) {
            echo json_encode([
                'success' => false,
                'message' => 'No changes were made.'
            ]);
            exit;
        }

        // Update the product in the database
        $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $category_id, $price, $image, $id]);

        // Check if the update was successful
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Product updated successfully!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No changes were made.'
            ]);
        }
    } catch (PDOException $e) {
        // Handle any database errors
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
