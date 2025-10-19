<?php
require_once '../init.php'; // Include the database connection

// Check if the product ID is passed via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = $_GET['id'];

    // Prepare the SQL query to fetch product data based on the ID
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ?");
    $stmt->execute([$productId]);

    // Fetch the product data
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Return product data as a JSON response
        echo json_encode([
            'success' => true,
            'product' => $product
        ]);
    } else {
        // If the product doesn't exist
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }
} else {
    // If no valid ID is passed
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID'
    ]);
}
?>
