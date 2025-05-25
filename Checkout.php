<?php require('./includes/Header.php');

$productID = $_GET['id'] ?? null;
$product = null;
$quantity = 1;
$tax = 10000;
$subtotal = 0;

if ($productID) {
    $stmt = $conn->prepare("SELECT ID as product_id, name, price, Picture FROM products WHERE ID = ?");
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        $subtotal = $product['price'] * $quantity;
    }
}

$userEmail = $_COOKIE['username'] ?? '';
$fullName = $address = $phone = '';
$userID = null;

if ($userEmail) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $userID = $user['id'];

        $infoStmt = $conn->prepare("SELECT Fullname, Address, Phone FROM customers WHERE User = ?");
        $infoStmt->bind_param("i", $userID);
        $infoStmt->execute();
        $infoResult = $infoStmt->get_result();
        if ($infoRow = $infoResult->fetch_assoc()) {
            $fullName = htmlspecialchars($infoRow['Fullname']);
            $address = htmlspecialchars($infoRow['Address']);
            $phone = htmlspecialchars($infoRow['Phone']);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
    $name = trim($_POST['fullname']);
    $addr = trim($_POST['address']);
    $phn = trim($_POST['phone']);

    if ($name && $addr && $phn) {
        // You can implement payment logic here
        echo "<script>alert('Payment processed successfully!');</script>";
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>

<head>
    <link rel="stylesheet" href="style/Carts.css" />
    <title>Checkout</title>
</head>
<div style="position: relative;">
    <img src="images/6876899.jpg" style="z-index:-1; border-radius: 0px 0px 10px 10px; object-fit: cover;"
        height='300hv' width='100%' id="target-section" />
    <div class="Content"
        style="text-align:center; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <?php if (!isset($_COOKIE["Login-Status"]) || $_COOKIE["Login-Status"] != "1"): ?>
            <h1>Welcome to The Website</h1>
            <p>You need an account to make purchases</p>
            <button style="border: none; background-color: transparent;">
                <div class="interest" onclick="window.location.href='login.php'">Login</div>
            </button>
            <button style="border: none; background-color: transparent;">
                <div class="interest" onclick="window.location.href='register.php'">Sign Up</div>
            </button>
        <?php else: ?>
            <h1>Checkout</h1>
            <p>Check Your Items and Information</p>
        <?php endif; ?>
    </div>
</div>

<div class="checkout-container">
    <div class="cart-section">
        <div class="checkout-header">Your Item</div>
        <?php if ($product): ?>
            <?php
            $imageUrl = !empty($product['Picture']) ? $product['Picture'] : 'images/Unknown_person.jpg';
            $itemTotal = $product['price'] * $quantity;
            $grandTotal = $itemTotal + $tax;
            ?>
            <div class="cart-item" style="display: flex; align-items: center; border: 1px solid #ddd; margin: 10px 0; padding: 10px; border-radius: 8px; background-color: #f9f9f9;">
                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-right: 20px;">
                <div class="cart-item-info" style="flex-grow: 1;">
                    <div><strong><?= htmlspecialchars($product['name']) ?></strong></div>
                    <div>Price: <?= number_format($product['price']) ?> VND</div>
                    <div>Quantity: <?= $quantity ?></div>
                    <div>Subtotal: <?= number_format($itemTotal) ?> VND</div>
                </div>
            </div>
            <div class="total-top">Subtotal: <?= number_format($itemTotal) ?> VND</div>
            <div class="total-tax">Tax: <?= number_format($tax) ?> VND</div>
            <div class="total"><strong>Total: <?= number_format($grandTotal) ?> VND</strong></div>
        <?php else: ?>
            <p>Product not found.</p>
        <?php endif; ?>
    </div>

    <div class="payment-section">
        <div class="checkout-header">Your Information</div>
        <div class="paycard">
            <form method="POST" id="payment-form">
            <input type="text" name="fullname" placeholder="Full Name" value="<?= $fullName ?>" required>
            <input type="text" name="address" placeholder="Address" value="<?= $address ?>" required>
            <input type="text" name="phone" placeholder="Phone Number" value="<?= $phone ?>" required>
                <div class="Seperate checkout-header2">Payment</div>
                <input type="text" name="card" placeholder="Card Number" required>
                <input type="text" name="expiry" placeholder="MM / YY" required>
                <input type="text" name="cvc" placeholder="CVC" required>
                <button class="payment-button pay-now" name="pay_now" type="submit"
                    style="width: 100%; padding: 15px; font-size: 16px; margin-top: 10px;">Pay Now</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('payment-form').addEventListener('submit', function (e) {
        const inputs = this.querySelectorAll('input[required]');
        for (let input of inputs) {
            if (!input.value.trim()) {
                alert("Please fill in all required fields.");
                input.focus();
                e.preventDefault();
                return false;
            }
        }
    });
</script>

<?php require('./includes/Footer.php') ?>
