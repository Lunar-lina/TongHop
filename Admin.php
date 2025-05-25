<?php require('./includes/Header.php'); ?>
<link rel="stylesheet" href="./style/Product.css" />
<style>
    input {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
    font-weight: bold;
        box-sizing: border-box;
        width: 100%;
        padding: 10px;
        border-radius: 3px;
        border: 1px solid #ccc;
    }
</style>

<div style="position: relative;">
    <img src="images/6876899.jpg" style="border-radius: 0px 0px 10px 10px;object-fit: cover;" height='1200hv'
        width='100%' />
    <div class="Content"
        style="text-align:center;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">
        <h1 style="margin-bottom:40px;background-color:black;border-radius:10px;padding:10px">Control Panel</h1>
        <?php
        $products = [];
        $userEmail = $_COOKIE['username'] ?? '';
        $userPermission = 0;

        if (!empty($userEmail)) {
            // Fetch user ID and permission level
            $stmt = $conn->prepare("SELECT id, permission FROM users WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $userEmail);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();

                if ($user) {
                    $userID = $user['id'];
                    $userPermission = $user['permission'];

                    // Fetch products created by the user
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


        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_table'])) {
            $table = $_POST['update_table'];
            $primaryKey = $_POST['primary_key'];
            $primaryValue = $_POST['primary_value'];

            // Build SET clause dynamically
            $setParts = [];
            $types = '';
            $values = [];

            foreach ($_POST as $key => $value) {
                if (in_array($key, ['update_table', 'primary_key', 'primary_value']))
                    continue;

                $setParts[] = "$key = ?";
                $types .= is_numeric($value) ? (is_float($value + 0) ? 'd' : 'i') : 's';
                $values[] = $value;
            }

            $setClause = implode(', ', $setParts);
            $query = "UPDATE `$table` SET $setClause WHERE `$primaryKey` = ?";

            $types .= is_numeric($primaryValue) ? (is_float($primaryValue + 0) ? 'd' : 'i') : 's';
            $values[] = $primaryValue;

            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param($types, ...$values);
                $stmt->execute();
                $stmt->close();
                header("Location: " . $_SERVER['REQUEST_URI']); // Refresh page to reflect changes
                exit();
            } else {
                echo "<p style='color:red;'>Update failed: " . $conn->error . "</p>";
            }
        }


        function displayTable($conn, $query, $headers, $tableName, $primaryKey = 'id')
        {
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                echo '<table><thead><tr>';
                foreach ($headers as $header) {
                    echo "<th>$header</th>";
                }
                echo '</tr></thead><tbody>';

                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<form method="post">';

                    foreach ($row as $key => $value) {
                        echo '<td>';
                        if ($key === $primaryKey) {
                            // Show ID as plain text, and keep it in a hidden input
                            echo htmlspecialchars($value);
                            echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
                        } else {
                            // Editable input for all other fields
                            echo "<input name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "' 
                                onkeydown='if(event.key === \"Enter\"){ this.form.submit(); return false; }'>";
                        }
                        echo '</td>';
                    }

                    // Hidden metadata for update logic
                    echo "<input type='hidden' name='update_table' value='" . htmlspecialchars($tableName) . "'>";
                    echo "<input type='hidden' name='primary_key' value='" . htmlspecialchars($primaryKey) . "'>";
                    echo "<input type='hidden' name='primary_value' value='" . htmlspecialchars($row[$primaryKey]) . "'>";

                    echo '</form>';
                    echo '</tr>';
                }

                echo '</tbody></table>';
            } else {
                echo '<p>No Data</p>';
            }
        }


        ?>


        <div class="box">
            <?php
            if ($userPermission == 10) {
                echo '<h2>Users</h2>';
                displayTable($conn, "SELECT id, email, passwords, permission FROM users", ['ID', 'Email', 'Passwords', 'Permission'], 'users', 'id');
                echo '<br>';
                echo '<h2>Products</h2>';
                displayTable($conn, "SELECT id, name, price, Description, Visibility FROM products", ['ID', 'Name', 'Price', 'Description', 'Visibility'], 'products', 'id');
                echo '<br>';
                echo '<h2>Order Details</h2>';
                displayTable($conn, "SELECT Orders_id, items, Amounts, Price, Status FROM orders_details", ['Order ID', 'Items', 'Amounts', 'Price', 'Status'], 'orders_details', 'Orders_id');
                echo '<br>';
                echo '<h2>Category</h2>';
                displayTable($conn, "SELECT category, Description FROM category", ['Category', 'Description'], 'category', 'category');
                echo '<br>';
            } else {
                echo "<p style='color:red;'>You Don't Have Permission to access this</p>
                <button> Go Back</button>";
                header('Location: ./Homepage.php');
            }
            ?>
        </div>
    </div>
</div>
<?php require('./includes/Footer.php'); ?>