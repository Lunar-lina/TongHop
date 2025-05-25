<?php
session_start();
require('./includes/Header.php');

$product = "";
$price = "";
$description = "";
$stocks = "";
$category = "";
$error = [];
$categories = [];
$creator_id = null;


$result = $conn->query("SELECT Category FROM category");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row["Category"];
    }
}


if (isset($_COOKIE['username'])) {
    $email = $_COOKIE['username'];
    $user_stmt = $conn->prepare("SELECT Id FROM users WHERE Email = ?");
    $user_stmt->bind_param("s", $email);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    if ($user_result->num_rows === 1) {
        $creator_id = $user_result->fetch_assoc()['Id'];
    }
    $user_stmt->close();
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product = trim(htmlspecialchars($_POST["product"]));
    $price = trim($_POST["price"]);
    $description = trim(htmlspecialchars($_POST["Description"]));
    $stocks = intval($_POST["stocks"]);
    $category = htmlspecialchars($_POST['category'] ?? '');
    $imagePath = "";
    if (isset($_FILES['Image']) && $_FILES['Image']['error'] === 0) {
        $uploadDir = "./images/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
    
        $imageName = time() . "_" . basename($_FILES["Image"]["name"]);
        $targetFile = $uploadDir . $imageName;
    
        $fileExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    
        if (!in_array($fileExt, $allowedExts)) {
            $error["image"] = "Invalid image file.";
        } elseif (move_uploaded_file($_FILES["Image"]["tmp_name"], $targetFile)) {
            $imagePath = $imageName; // store only the filename in DB
        } else {
            $error["image"] = "Failed to upload image.";
        }
    } else {
        $error["image"] = "No image uploaded or an error occurred.";
    }
    


    if (empty($product)) {
        $error["product"] = "Must have a name for Product";
    }
    if (empty($price) || !is_numeric($price) || floatval($price) < 1000) {
        $error["price"] = "Must be a number greater than 1000";
    }
    if (!is_numeric($stocks) || $stocks < 1) {
        $error["stocks"] = "Write a valid stock amount";
    }
    if ($_FILES["Image"]["error"] !== 0) {
    $error["image"] = "Image upload error code: " . $_FILES["Image"]["error"];
}

    if (empty($category) || !in_array($category, $categories)) {
        $error["category"] = "Please choose a valid category";
    }
    if (!$creator_id) {
        $error["creator"] = "Unknown user. Please login or register.";
    }

   
    if (empty($error)) {
      $stmt = $conn->prepare("INSERT INTO products (Creators, Name, Description, Price, Stocks, Picture, Categories) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("issdiss",  $creator_id, $product, $description, $price, $stocks, $imagePath, $category);
  
      if ($stmt->execute()) {
          $product = $price = $description = $stocks = $category = "";
          $error["success"] = "Product created successfully.";
      } else {
          $error["database"] = "Database error: " . $stmt->error;
      }
      $stmt->close();
  }
}  
?>

<head>
  <link rel="stylesheet" href="style/Login&Register.css" />
  <title>Tạo Sản Phẩm</title>
</head>

<body>
  <div class="wrapper" style="margin-top: 100px;">
    <div id="form-content" class="fade-in second">
      <h2 class="active">Create Product</h2>
      <form action="" method="POST" enctype="multipart/form-data">
        <a>Product Name</a>
        <input type="text" name="product" placeholder="Product Name"
          value="<?php echo htmlspecialchars($product); ?>" />
        <span class="error"><?php echo $error["product"] ?? ""; ?></span><br>

        <a>Price</a>
        <input type="text" name="price" placeholder="Price" value="<?php echo htmlspecialchars($price); ?>" />
        <span class="error"><?php echo $error["price"] ?? ""; ?></span><br>

        <a>Description</a>
        <input type="text" name="Description" placeholder="Description"
          value="<?php echo htmlspecialchars($description); ?>" />
        <span class="error"><?php echo $error["description"] ?? ""; ?></span><br>

        <a>Category</a><br>
        <select name="category" required>
          <option value="">Choose 1 Category</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?php echo $cat; ?>" <?php if ($category === $cat)
                 echo 'selected'; ?>>
              <?php echo $cat; ?>
            </option>
          <?php endforeach; ?>
        </select><br>
        <span class="error" style="color:#f56642;font-weight:bold;"><?php echo $error["category"] ?? ""; ?></span><br>

        <a>Stocks</a>
        <input type="number" name="stocks" placeholder="Stocks/Quantity" value="<?php echo htmlspecialchars($stocks); ?>" />
        <span class="error"><?php echo $error["stocks"] ?? ""; ?></span><br>

        <a>Imgae</a>
        <div class="upload-box" id="drop-area">
          <input type="file" id="fileElem" name="Image" accept="image/*" hidden />
          <div id="upload-placeholder">
            <div class="upload-icon" id="icon">&#8682;</div>
            <p id="drag-text">Drag & Drop to Upload File</p>
            <p id="or-text">OR</p>
          </div>
          <button type="button" id="browseBtn" class="browse-button">Browse File</button>
          <img id="preview" src="#" alt="Preview Image"
            style="display:none; max-width: 100%; margin-top: 15px; border-radius: 8px;" />
        </div>
        <span class="error"><?php echo $error["image"] ?? ""; ?></span><br>

        <input type="submit" class="fade-in five" value="Tạo Sản Phẩm" />

        <span class="error"><?php echo $error["creator"] ?? ""; ?></span><br>
        <span class="error"><?php echo $error["database"] ?? ""; ?></span>
        <span style="color:green; font-weight:bold;"><?php echo $error["success"] ?? ""; ?></span>
      </form>
    </div>
  </div>

  <script>
    const dropArea = document.getElementById("drop-area");
    const fileInput = document.getElementById("fileElem");
    const preview = document.getElementById("preview");
    const icon = document.getElementById("icon");
    const dragText = document.getElementById("drag-text");
    const orText = document.getElementById("or-text");
    const browseBtn = document.getElementById("browseBtn");

    dropArea.addEventListener("click", () => fileInput.click());

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, e => {
        e.preventDefault();
        e.stopPropagation();
      });
    });

    dropArea.addEventListener("dragover", () => dropArea.classList.add("dragover"));
    dropArea.addEventListener("dragleave", () => dropArea.classList.remove("dragover"));

    dropArea.addEventListener("drop", (e) => {
      dropArea.classList.remove("dragover");
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        fileInput.files = files;
        showPreview(files[0]);
      }
    });

    fileInput.addEventListener("change", () => {
      if (fileInput.files && fileInput.files[0]) {
        showPreview(fileInput.files[0]);
      }
    });

    function showPreview(file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = "block";
        icon.style.display = "none";
        dragText.style.display = "none";
        orText.style.display = "none";
        browseBtn.textContent = "Change File";
      };
      reader.readAsDataURL(file);
    }
  </script>
</body>

<?php require('./includes/Footer.php') ?>