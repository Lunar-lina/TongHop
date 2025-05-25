<?php
require('./includes/Header.php');

if (!isset($_COOKIE['username'])) {
  echo "You are not logged in.";
  exit;
}

$username = $conn->real_escape_string($_COOKIE['username']);

// Get user ID
$userResult = $conn->query("SELECT Id FROM users WHERE Email = '$username'");
if ($userResult->num_rows === 0) {
  echo "Invalid user.";
  exit;
}
$user = $userResult->fetch_assoc();
$userId = $user['Id'];

$defaultImage = 'images/Unknown_person.jpg';
$product = null;

if (isset($_GET['id'])) {
  $productId = intval($_GET['id']);
  if ($productId <= 0) {
    echo "Invalid product ID.";
    exit;
  } // Sanitize input
  $sql = "SELECT * FROM products WHERE ID = $productId AND Visibility = 1";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $serverPath = __DIR__ . '/' . $product['Picture'];
    $product['Picture'] = (!empty($product['Picture']) && is_file($serverPath))
      ? $product['Picture']
      : $defaultImage;
    } else {
      echo "Product not found.";
      exit;
    }
  } else {
    echo "Invalid product ID.";
    exit;
  }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
  if ($quantity <= 0)
    $quantity = 1;

  $existing = $conn->query("SELECT * FROM carts WHERE Users = $userId AND Product_id = $productId");

  if ($existing->num_rows > 0) {
    $conn->query("UPDATE carts SET quantity = quantity + $quantity WHERE Users = $userId AND Product_id = $productId");
    $message = "Quantity updated in cart!";
  } else {
    $conn->query("INSERT INTO carts (Users, Product_id, quantity) VALUES ($userId, $productId, $quantity)");
    $message = "Product added to cart!";
  }
}
?>

<head>
  <link rel="stylesheet" href="style/FPstyle.css" />
  <link rel="stylesheet" href="style/Product.css" />
  <title>Prducts</title>
</head>
<div>
  <div style="position: relative;">
    <img src="images/6876899.jpg" style="z-index:-1;border-radius: 0px 0px 10px 10px;object-fit: cover;" height='300hv'
      width='100%' max-width='100%' max-height='100%' id="target-section" />
    <div class="Content"
      style="text-align:center;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">

      <?php if (!isset($_COOKIE["Login-Status"]) || $_COOKIE["Login-Status"] != "1"): ?>
        <h1>Welcome to The Website</h1>
        <p>You need to Have an account to start making purchases</p>
        <button style="border: #000 solid 0px; background-color: #00000000;">
          <div class="interest" onclick="window.location.href='login.php'">Login</div>
        </button>
        <button style="border: #000 solid 0px; background-color: #00000000;">
          <div class="interest" onclick="window.location.href='register.php'">Sign Up</div>
        </button>
      <?php else: ?>
        <h1>Product : "<?php echo htmlspecialchars($product['Name']); ?>"</h1>
        <p>Check Details About Prodcut Before Purchase</p>
      <?php endif; ?>
    </div>
  </div>
  <div style="background-color:pink;padding: 95px 0px 95px 0px;margin-top:-20px">
    <div class="product-page">
      <?php if ($product): ?>
        <div class="product-left">
          <img src="<?php echo htmlspecialchars($product['Picture']); ?>"
            alt="<?php echo htmlspecialchars($product['Name']); ?>" />
        </div>
        <div class="product-right">
        
          <h1><?php echo htmlspecialchars($product['Name']); ?></h1>
          <div class="product-price"><?php echo number_format($product['Price'], ); ?> VND</div>
          <div class="product-stock"><strong>Stock:</strong> <?php echo $product['Stocks']; ?></div>
          <div class="product-stock"><strong>Category:</strong> <?php echo $product['Categories']; ?></div>
          <p class="product-description"><?php echo nl2br(htmlspecialchars($product['Description'])); ?></p>
          <div class="quantity-form" data-product-id="<?= $product['ID'] ?>">
          <div style="font-size: 13px;">Disclaim: Don't press to much "Add to Cart" since the amounts will continue increase in your cart. </div>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $productId; ?>">
              <div class="quantity-control">
                <button type="button" class="qty-btn minus">−</button>
                <input type="number" name="quantity" id="quantity-input" class="qty-input" min="1" value="1" />
                <button type="button" class="qty-btn plus">+</button>
              </div>
              <div class="product-actions">
              <button type="button" class="buy-now" onclick="window.location.href='checkout.php?id=<?php echo $productId; ?>'">Buy Now</button>
                <button type="submit" class="add-cart">Add to Cart</button>
              </div>
            </form>
          </div>

        </div>
        <div class="creator-info">
          <h2>Creator</h2>
          <p style="color: grey;"><strong>Name:</strong><?php echo htmlspecialchars($product['Creators']); ?></p>
          <p style="color: grey;"><strong>Product Date:</strong>
            <?php
            $created_at = $product['Created_at'];
            $shortDate = date("Y/m/d", strtotime($created_at));
            echo htmlspecialchars(string: $shortDate); ?>
          </p>
          <p style="color: grey;"><strong>Rating:</strong> ?/5</p>
        </div>
      </div>
    <?php else: ?>
      <p class="not-found">Product not found or unavailable.</p>
    <?php endif; ?>
  </div>
</div>
</body>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById('quantity-input');
    const plus = document.querySelector('.qty-btn.plus');
    const minus = document.querySelector('.qty-btn.minus');

    plus.addEventListener('click', () => {
      input.value = parseInt(input.value) + 1;
    });

    minus.addEventListener('click', () => {
      const val = parseInt(input.value);
      if (val > 1) input.value = val - 1;
    });
  });
</script>
<?php require('./includes/Footer.php') ?>