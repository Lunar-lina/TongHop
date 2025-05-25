<?php include "db.php"; ?>

<a href="add.php">+ Thêm người dùng</a>

<table border="1">
    <tr><th>ID</th><th>Tên</th><th>Email</th><th>Hành động</th></tr>
    <?php
    $result = $conn->query("SELECT * FROM users");
    while($row = $result->fetch_assoc()):
    ?>
    <tr>
        <td><?= $row["id"] ?></td>
        <td><?= $row["name"] ?></td>
        <td><?= $row["email"] ?></td>
        <td>
            <a href="edit.php?id=<?= $row["id"] ?>">Sửa</a> |
            <a href="delete.php?id=<?= $row["id"] ?>" onclick="return confirm('Xoá người dùng này?')">Xoá</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>