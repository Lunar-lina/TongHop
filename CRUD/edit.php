<?php include "db.php"; ?>
<?php
$id = $_GET["id"];
$result = $conn->query("SELECT * FROM users WHERE id = $id");
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $sql = "UPDATE users SET name='$name', email='$email' WHERE id=$id";
    if ($conn->query($sql)) {
        header("Location: index.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<form method="POST">
    <input type="text" name="name" value="<?= $user['name'] ?>"><br>
    <input type="email" name="email" value="<?= $user['email'] ?>"><br>
    <button type="submit">Cập nhật</button>
</form>