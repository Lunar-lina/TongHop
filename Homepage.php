<?php
require('./includes/Header.php');

$sql = "SELECT ID, Name, Description, Price, Stocks, Picture, Categories FROM products WHERE Visibility = 1";
$result = $conn->query($sql);

if ($result === false) {
  $conn->error;
} else {

}
$defaultImage = 'images/Unknown_person.jpg';
?>
<div style="position: relative;">
  <img src="images/6876899.jpg" style="z-index:-1;border-radius: 0px 0px 10px 10px;object-fit: cover;" height='800hv'
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
      <h1>Welcome back! <a style="color:red"><?php echo $username ?></a></h1>
      <p>Thanks You for put your trust into us.</p>
    <?php endif; ?>
  </div>
</div>

<div class="shop-section" style="background-color:pink;margin-top:-20px">
  <div class="shop-header">
    <h1>Shop</h1>
    <p style="font-size: 0.85rem; color: white; font-weight: 500; max-width: 700px; margin: 0 auto;">
      disclaim : we do not control the quality and stocks of those items, all of those come from our community (include
      you).
      <br> We recommend you contact the seller to confirm before actually buying.
    </p>
  </div>

  <?php if ($result && $result->num_rows > 0): ?>
    <div class="product-grid">
      <?php while ($row = $result->fetch_assoc()):
        $sanitizedName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['Name']);
        $imagePath = "images/{$sanitizedName}.jpg";
        $serverPath = __DIR__ . '/' . $imagePath;
        $image = file_exists($serverPath) ? $imagePath : $defaultImage;
        ?>
        <div class="product-card">
          <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($row['Name']); ?>" />
          <div class="product-name"><?php echo htmlspecialchars($row['Name']); ?></div>
          <div class="product-description"><?php echo htmlspecialchars($row['Description']); ?></div>
          <div class="product-name">Danh Mục : <?php echo htmlspecialchars($row['Categories']); ?></div>
          <div class="product-price"><?php echo number_format($row['Price']); ?> VND</div>
          <div class="product-stocks">Remaining: <?php echo htmlspecialchars($row['Stocks']); ?></div>
          <a class="buy-btn" href="Products.php?id=<?php echo urlencode($row['ID']); ?>">Purchase</a>
        </div>
      <?php endwhile; ?>

    </div>
  <?php else: ?>
    <p style="margin-top: 30px; color: white;">No products available.</p>
  <?php endif; ?>

  <?php $conn->close(); ?>
</div>
<?php require('./includes/Footer.php') ?>