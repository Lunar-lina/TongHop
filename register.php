<?php
 require('./includes/Header.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $error = [];
  $username = htmlspecialchars($_POST["username"]);
  $email = htmlspecialchars($_POST["email"]);
  $password = htmlspecialchars($_POST["password"]);
  $repeatPassword = htmlspecialchars($_POST["rPassword"]);

  if (empty($username)) {
    $error["username"] = "Vui lòng nhập Tên Đăng Nhập.";
  }
  setcookie("username", $username, time() + (86400 * 30), "/");

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  }
    setcookie("email", $email, time() + (86400 * 30), "/");

  if (empty($password)) {
    $error["password"] = "Vui lòng nhập Mật Khẩu.";
  } elseif (strlen($password) <= 6) {
    $error["password"] = "Vui lòng nhập Mật Khẩu dài hơn 6 ký tự.";
  }
    setcookie("password", $password, time() + (86400 * 30), "/");

  if (empty($repeatPassword)) {
    $error["repeatPassword"] = "Vui lòng nhập xác nhận Mật Khẩu.";
  } elseif ($password != $repeatPassword) {
    $error["repeatPassword"] = "Xác nhận Mật Khẩu phải giống với Mật Khẩu.";
  }


  if (empty($error)) {
   
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (Username, Email, Passwords, Hashed_password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $hashedPassword);
    if ($stmt->execute()) {
        $username = $email = $password = $repeatPassword = "";
        header('Location: ./Login.php');
        exit();
  }
}
}
?>

<head>
  <link rel="stylesheet" href="style/Login&Register.css" />
  <link rel="stylesheet" href="style/FPstyle.css" />
  <title>Register Page</title>
</head>

<body>
  <div class="wrapper" style="margin-top: 100px;">
    <div id="form-content" class="fade-in second">
      <a href="./login.php">
        <h2 class="inactive underline-hover">Đăng nhập</h2>
      </a>
      <a href="./register.php" style="pointer-events: none;cursor: default;">
        <h2 class="active">Đăng ký</h2>
      </a>

      <form action="" method="POST">
        <a style="padding:5px;border-radius:4px" class="fade-in second">Tên Đăng Nhập</a>
        <input
          type="text"
          id="username"
          class="fade-in first"
          name="username"
          placeholder="Họ tên"
          value="<?php echo isset($username) ? $username : ''; ?>" ; />
        <span class="error" style="color:#f56642;font-weight:bold;"> <?php echo $error["username"] ?? ""; ?> </span>
        <a style="padding:5px;border-radius:4px" class="fade-in second">Email</a>
        <input
          type="email"
          id="Email"
          class="fade-in second"
          name="email"
          placeholder="Email"
          value="<?php echo isset($email) ? $email : ''; ?>" ; />
          <a style="padding:5px;border-radius:4px" class="fade-in second">Mật Khẩu</a>
        <input
          type="password"
          id="password"
          class="fade-in third"
          name="password"
          placeholder="Mật khẩu"
          value="<?php echo isset($password) ? $password : ''; ?>" ; />
        <span class="error" style="color:#f56642;font-weight:bold;"> <?php echo $error["password"] ?? ""; ?> </span>
        <a style="padding:5px;border-radius:4px" class="fade-in second">Xác Nhận Mật Khẩu</a>
        <input
          type="password"
          id="rPassword"
          class="fade-in fourth"
          name="rPassword"
          placeholder="Xác nhận mật khẩu"
          value="<?php echo isset($repeatPassword) ? $repeatPassword : ''; ?>" ; />
        <span class="error" style="color:#f56642;font-weight:bold;"> <?php echo $error["repeatPassword"] ?? ""; ?> </span>

        <input type="submit" class="fade-in five" value="Đăng ký" /><br>
        <div id="form-footer">
        <a style="color:white;"> Đã có tài khoản?-</a><a class="underline-hover" href="./Login.php"><p></p>Đăng Nhập ngay</a>
        </div>
      </form>
    </div>
  </div>
</body>

<?php require('./includes/Footer.php')?>