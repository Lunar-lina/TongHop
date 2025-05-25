<?php 
// Ensure you have a database connection
require('./includes/Header.php');

// Check if user is logged in and userId is available
if (!isset($_COOKIE['username'])) {
    echo "You are not logged in.";
    header("Location: index.php");
    exit;
}

$username = $conn->real_escape_string($_COOKIE['username']);
$userResult = $conn->query("SELECT Id FROM users WHERE Email = '$username'");
if ($userResult->num_rows === 0) {
    echo "Invalid user.";
    header("Location: index.php");
    exit;
}
$user = $userResult->fetch_assoc();
$userId = $user['Id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Check if quantity is 0, then delete the item
    if ($quantity === 0) {
        $deleteQuery = $conn->prepare("DELETE FROM carts WHERE Product_id = ? AND users = ?");
        $deleteQuery->bind_param("ii", $productId, $userId);
        if (!$deleteQuery->execute()) {
            error_log("Delete query failed: " . $deleteQuery->error);
        }
    } else {
        // Update the quantity
        $updateQuery = $conn->prepare("UPDATE carts SET quantity = ? WHERE Product_id = ? AND users = ?");
        $updateQuery->bind_param("iii", $quantity, $productId, $userId);
        if (!$updateQuery->execute()) {
            error_log("Update query failed: " . $updateQuery->error);
        }
    }
    // Redirect back to the cart page
    header("Location: Carts.php");
    exit;
}
?>
