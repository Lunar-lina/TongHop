<?php require('./includes/Header.php'); ?>
<link rel="stylesheet" href="./style/Product.css" />

<div style="position: relative;">
    <img src="images/6876899.jpg" style="border-radius: 0px 0px 10px 10px;object-fit: cover;" height='1200hv'
        width='100%' />
    <div class="Content"
        style="text-align:center;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">
        <h1 style="margin-bottom:40px">Danh Sách Sản Phẩm</h1>
        <?php
        $products = [];
        $userEmail = $_COOKIE['username'] ?? '';

        if (!empty($userEmail)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $userEmail);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();

                if ($user) {
                    $userID = $user['id'];

                    $productQuery = $conn->prepare("SELECT id, name, price, Description, Picture, Visibility FROM products WHERE Creators = ?");
                    if ($productQuery) {
                        $productQuery->bind_param("i", $userID);
                        $productQuery->execute();
                        $products = $productQuery->get_result();
                    } else {
                        echo "Lỗi truy vấn sản phẩm: " . $conn->error;
                    }
                } else {
                    echo "<p style='color:red;'>Không tìm thấy người dùng.</p>";
                }
            } else {
                echo "Lỗi truy vấn người dùng: " . $conn->error;
            }
        } else {
            echo "<p style='color:red;'>Người dùng chưa đăng nhập.</p>";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            $productID = intval($_POST['product_id']);
            $visibility = isset($_POST['visibility']) ? 1 : 0;

            $updateQuery = $conn->prepare("UPDATE products SET Visibility = ? WHERE id = ?");
            if ($updateQuery) {
                $updateQuery->bind_param("ii", $visibility, $productID);
                $updateQuery->execute();
                $updateQuery->close();
            }
        }
        ?>

        <div class="box">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th style="width:100%;">Description</th>
                        <th>Price</th>
                        <th>Visibility</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php if (!empty($products) && $products->num_rows > 0): ?>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($product['Picture'])): ?>
                                    <img src="<?= htmlspecialchars($product['Picture']) ?>" width="80" height="80" />
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($product['id']) ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['Description']) ?></td>
                            <td><?= htmlspecialchars($product['price']) ?> VND</td>
                            <td>
                                <form method="post" action="" class="visibility-form">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
                                    <label>
                                        <input type="checkbox" name="visibility" onchange="this.form.submit()"
                                            <?= $product['Visibility'] ? 'checked' : '' ?> />
                                    </label>
                                </form>
                            </td>
                            <td>
                                <a href="Products update.php?id=<?= $product['id'] ?>" class="btn btn-edit">Edit</a>
                                <a class="btn btn-delete"
                                    onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Không có sản phẩm nào.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <a href="Products create.php" class="btn btn-add">Thêm Sản Phẩm</a>
        </div>
    </div>
</div>
<?php require('./includes/Footer.php'); ?>