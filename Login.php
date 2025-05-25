
 <?php require('./includes/Header.php');
  
$username = isset($_COOKIE['username']);

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $error = [];
     $email = isset($_POST['email']) ? trim($_POST['email']) : '';
     $password = isset($_POST['password']) ? $_POST['password'] : '';

     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error["email"] = "Email sai cú pháp.";
    }  
    $stmt = $conn->prepare("SELECT Passwords,Hashed_password FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 0) {
        $error["email"] = "Email không tồn tại.";
    } else {
        
        $stmt->bind_result($password,$hashed_password);
        $stmt->fetch();
        
        if (empty($password)) {
            $error["password"] = "Vui lòng nhập Mật Khẩu."; 
        } elseif (strlen($password) <= 6) {
            $error["password"] = "Vui lòng nhập Mật Khẩu dài hơn 6 ký tự.";
        } elseif (!password_verify($password, $hashed_password)) {
            $error["password"] = "Mật Khẩu không đúng.";
        }
    }
    $stmt->close();
    if (empty($error)) {
      setcookie("username", $email, time() + (86400 * 30), "/");
      setcookie("Login-Status", True, time() + (86400 * 30), "/");
        header('Location: ./Dashboard.php');
        exit();
    }
  }
  ?>

  <head>
    <link rel="stylesheet" href="style/Login&Register.css" />
    <link rel="stylesheet" href="style/FPstyle.css" />
    <title>Login Page</title>
  </head>
  <body>
  
    <div class="wrapper" style="margin-top: 100px;">
      <div id="form-content" class="fade-in second">
        <a href="./login.php">
          <h2 class="active" style="pointer-events: none;cursor: default;">Đăng nhập</h2>
        </a>
        <a href="./register.php">
          <h2 class="inactive underline-hover">Đăng ký</h2>
        </a>
        <form action="" method="POST">
          <a style="padding:5px;border-radius:4px" class="fade-in second">Email</a>
        <input
          type="email"
          id="Email"
          class="fade-in second"
          name="email"
          placeholder="Email"
          value="<?php echo isset($email) ? $email : ''; ?>" ; 
          />
        <span class="error" style="color:#f56642;font-weight:bold;"> <?php echo $error["email"] ?? ""; ?> </span><br>
        <a style="padding:5px;border-radius:4px;" class="fade-in second">Mật Khẩu</a>
          <input
            type="password"
            id="password"
            class="fade-in third"
            name="password"
            placeholder="Mật khẩu"
          />
          <span class="error" style="color:#f56642;font-weight:bold;"> <?php echo $error["password"] ?? ""; ?> </span><br>
          <a class="underline-hover fade-in second" href="./Reset-password.php" style="color:#FFD2A0">Quên mật khẩu?</a>
          <input type="submit" class="fade-in five" value="Đăng Nhập" />
          <span class="error" style="color:#f56642;font-weight:bold;"> <?php echo $error["success"] ?? ""; ?> </span>
        </form>

        <div id="form-footer">
          <a style="color:white;"> Chưa có tài khoản?-</a><a class="underline-hover" href="./Register.php">Đăng Ký ngay</a>
          
        </div>
      </div>
    </div>
  </body>
  <?php require('./includes/Footer.php')?>
