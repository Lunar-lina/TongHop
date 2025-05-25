<?php
require('./includes/Header.php');

$error = [];
$product = '';
$price = '';
$description = '';
$stocks = '';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  $stmt = $conn->prepare("SELECT name, price, description, stocks FROM Products WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($product, $price, $description, $stocks);
  $stmt->fetch();
  $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $product = htmlspecialchars($_POST["Product"]);
  $price = htmlspecialchars($_POST["price"]);
  $description = htmlspecialchars($_POST["Description"]);
  $stocks = htmlspecialchars($_POST["Stocks"]);

  if (empty($product)) {
    $error["product"] = "Vui Lòng Nhập Tên Sản Phẩm";
  }

  if (empty($price)) {
    $error["price"] = "Vui lòng nhập Giá.";
  } elseif (!is_numeric($price) || $price < 1000) {
    $error["price"] = "Vui lòng nhập Giá hợp lệ và cao hơn 1000";
  }

  if (empty($stocks)) {
    $error["stocks"] = "Vui lòng nhập số lượng kho.";
  } elseif (!ctype_digit($stocks) || intval($stocks) < 0) {
    $error["stocks"] = "Số lượng kho phải là số nguyên không âm.";
  }

  if (empty($error)) {
    // Update product information in the database
    $stmt = $conn->prepare("UPDATE Products SET name = ?, price = ?, description = ?, stocks = ? WHERE id = ?");
    $stmt->bind_param("sdssi", $product, $price, $description, $stocks, $id);
    $stmt->execute();
    $stmt->close();

    $error["success"] = "Sửa thành công";
    header('Location: ./Products index.php');
    exit();
  }
}
?>

<head>
  <link rel="stylesheet" href="style/Login&Register.css" />
  <link rel="stylesheet" href="style/FPstyle.css" />
  <title>Chỉnh Sửa Sản Phẩm</title>
</head>

<body>
  <div class="wrapper" style="margin-top: 100px;">
    <div id="form-content" class="fade-in second">
      <a href="./register.php">
        <h2 class="active">Chỉnh Sửa Sản Phẩm</h2>
      </a>
      <form action="" method="POST">
        <label style="padding:5px;border-radius:4px" class="fade-in second">Product Name</label><br>
        <input type="text" class="fade-in second" name="Product" placeholder="Product Name"
          value="<?php echo htmlspecialchars($product); ?>" />
        <span class="error" style="color:#f56642;font-weight:bold;"><?php echo $error["product"] ?? ""; ?></span>
        <br><br>

        <label style="padding:5px;border-radius:4px;" class="fade-in second">Price</label><br>
        <input type="text" class="fade-in third" name="price" placeholder="Price"
          value="<?php echo htmlspecialchars($price); ?>" />
        <span class="error" style="color:#f56642;font-weight:bold;"><?php echo $error["price"] ?? ""; ?></span>
        <br><br>

        <label style="padding:5px;border-radius:4px;" class="fade-in second">Description</label><br>
        <input type="text" class="fade-in fourth" name="Description" placeholder="Description" value="<?php echo htmlspecialchars($description); ?>"/>
        <span class="error" style="color:#f56642;font-weight:bold;"><?php echo $error["description"] ?? ""; ?></span>
        <br><br>

        <label style="padding:5px;border-radius:4px;" class="fade-in second">Stocks</label><br>
        <input type="number" class="fade-in fourth" name="Stocks" placeholder="Stocks"
          value="<?php echo htmlspecialchars($stocks); ?>" min="0" />
        <span class="error" style="color:#f56642;font-weight:bold;"><?php echo $error["stocks"] ?? ""; ?></span>
        <br><br>

        <input type="submit" class="fade-in five" value="Change Product" />
        <span class="error" style="color:#f56642;font-weight:bold;"><?php echo $error["success"] ?? ""; ?></span>
      </form>
      <div id="form-footer">
      </div>
    </div>
  </div>
</body>
<?php require('./includes/Footer.php') ?>