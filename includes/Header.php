<!DOCTYPE html>
<html lang="en">
	<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thuong_mai_dien_tu";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" type="text/css" href="style/FPstyle.css" />
		<link rel="icon" href="images/what.png" type="image/ico">
	</head>
	<body>
		<header class="header">
			<div class="container">
			   <nav class="nav">
				<img src="images/what.png" height="100px" width="100px" style="margin-bottom:-55px;margin-left:auto;"></img>
				<a class="nav-link" onclick="window.location.href='index.php'">
				 Welcome
				</a>
				<a class="nav-link" onclick="window.location.href='Contact.php'">
				 Contact
				</a>
				<a class="nav-link" onclick="window.location.href='Carts.php'" style="margin-right:auto">
				 Products
				</a>
                <?php 
                $Cusername = isset($_COOKIE['Login-Status']);
                $error = [];
                if (empty($Cusername)) {
                    $error["username2"] = "Sign In"; 
                    $error["link"] = "Register.php";
                  } else {
                    $error["username2"] = "Account";
                    $error["link"] = "Dashboard.php";
                  }
                ?>    
				<a class="nav-link"  onclick="window.location.href='<?php echo $error["link"]; ?>'">
                <?php echo $error["username2"] ?? "error"; ?>
				</a>
				<?php 
                if (empty($Cusername)) {
                    $error["Status"] = " "; 
                  } else {
                    $error["Status"] = "Log Out";
                  }
                ?>
				<a class="nav-link" action="" style="margin-right:auto" href="?logout=true">
                <?php echo $error["Status"] ?? "error"; ?>
				</a>
				<?php
   if (isset($_GET['logout'])) {
    setcookie("email", "", time() - 3600, "/");
    setcookie("username", "", time() - 3600, "/");
    setcookie("password", "", time() - 3600, "/");
	setcookie("Login-Status", "", time() - 3600, "/");
	header('Location:./index.php');
    exit();
}
?>
			</div>
		   </header>