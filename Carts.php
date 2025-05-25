<?php require('./includes/Header.php');

$userEmail = $_COOKIE['username'] ?? '';
$cartItems = [];
$total = 0;
$fullName = $address = $phone = '';


if ($userEmail) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $userID = $user['id'];

        $cartQuery = $conn->prepare("
    SELECT c.quantity, p.ID as product_id, p.name, p.price, p.Picture 
    FROM carts c 
    JOIN products p ON c.Product_id = p.ID 
    WHERE c.users = ? AND c.order_id = 0
");

        $cartQuery->bind_param("i", $userID);
        $cartQuery->execute();
        $cartItems = $cartQuery->get_result();
    }
}
if ($userID) {
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
    $name = trim($_POST['fullname']);
    $addr = trim($_POST['address']);
    $phn = trim($_POST['phone']);

    if ($name && $addr && $phn && $userID) {
        $checkStmt = $conn->prepare("SELECT User FROM customers WHERE User = ?");
        $checkStmt->bind_param("i", $userID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Update
            $updateStmt = $conn->prepare("UPDATE customers SET fullname = ?, address = ?, phone = ? WHERE user = ?");
            $updateStmt->bind_param("sssi", $name, $addr, $phn, $userID);
            $updateStmt->execute();
        } else {
            // Insert
            $insertStmt = $conn->prepare("INSERT INTO customers (user, fullname, address, phone) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("isss", $userID, $name, $addr, $phn);
            $insertStmt->execute();
        }
        
    } else {

    }
}

?>

<head>
    <link rel="stylesheet" href="style/Carts.css" />
    <title>Carts</title>
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
            <h1>Carts</h1>
            <p>Where your planning to purchase items is in.</p>
        <?php endif; ?>
    </div>
</div>

<div class="checkout-container">
    <div class="cart-section">
        <div class="checkout-header">Your Cart</div>
        <?php
        $subtotal = 0;
        if ($cartItems) {
            while ($item = $cartItems->fetch_assoc()) {
                $imageUrl = !empty($item['Picture']) ? $item['Picture'] : 'images/Unknown_person.jpg';
                $itemTotal = $item['price'] * $item['quantity'];
                $subtotal += $itemTotal;
                echo '
                <div class="cart-item" style="display: flex; align-items: center; border: 1px solid #ddd; margin: 10px 0; padding: 10px; border-radius: 8px; background-color: #f9f9f9;">
                    <img src="' . htmlspecialchars($imageUrl) . '" alt="' . htmlspecialchars($item['name']) . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-right: 20px;">
                    <div class="cart-item-info" style="flex-grow: 1;">
                        <div><strong>' . htmlspecialchars($item['name']) . '</strong></div>
                        <div>Price: ' . number_format($item['price']) . ' VND</div>
                        <form action="update_cart.php" method="post" class="quantity-form">
                            <input type="hidden" name="product_id" value="' . $item['product_id'] . '" />
                            <div class="quantity-control" style="display: flex; align-items: center; gap: 10px;">
                                <button type="button" class="qty-btn minus">−</button>
                                <input type="number" name="quantity" min="0" value="' . $item['quantity'] . '" style="width: 60px; text-align: center;" />
                                <button type="button" class="qty-btn plus">+</button>
                                <button type="submit" style="display: none;">Update</button>
                            </div>
                        </form>
                        <div>Subtotal: ' . number_format($itemTotal) . ' VND</div>
                    </div>
                </div>';
            }
        }
        $tax = 10000;
        $grandTotal = $subtotal + $tax;
        ?>
        <div class="total-top">Subtotal: <?= number_format($subtotal) ?> VND</div>
        <div class="total-tax">Tax: <?= number_format($tax) ?> VND</div>
        <div class="total"><strong>Total: <?= number_format($grandTotal) ?> VND</strong></div>
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
    document.querySelectorAll('.quantity-form').forEach(form => {
        const minusBtn = form.querySelector('.minus');
        const plusBtn = form.querySelector('.plus');
        const input = form.querySelector('input[type="number"]');
        const submit = form.querySelector('button[type="submit"]');

        minusBtn.addEventListener('click', () => {
            let val = parseInt(input.value);
            if (val > 0) {
                input.value = val - 1;
                submit.click();
            }
        });

        plusBtn.addEventListener('click', () => {
            input.value = parseInt(input.value) + 1;
            submit.click();
        });
    });
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