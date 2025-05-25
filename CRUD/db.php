<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "crud_php";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>

<?php include "db.php"; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
    if ($conn->query($sql)) {
        header("Location: index.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<form method="POST">
    <input type="text" name="name" placeholder="Tên"><br>
    <input type="email" name="email" placeholder="Email"><br>
    <button type="submit">Thêm</button>
</form>