
<?php
include 'csrf_token.php';

require_once 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}



$result = $conn->query("SELECT id, name, price FROM products ORDER BY created_at DESC");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product_id'])) {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    //require_once 'role_check.php';
    $product_id = $_POST['delete_product_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
    header("Location: products_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function confirmDelete(form) {
            if (confirm("Do you want to delete this product?")) {
                form.submit();
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
<?php
    require_once 'header.php';
    ?>
    <main class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Product List</h2>
            <div class="flex justify-end mb-4">
                <a href="add_products.php" class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600">Add New Product</a>
            </div>
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b-2 border-gray-300">Name</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300">Price</th>
                        <?php if($_SESSION['role'] == 'admin'):?>

                        <th class="py-2 px-4 border-b-2 border-gray-300">Actions</th>
                        <?php endif;?>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-100">
                        <td class="py-2 px-4 border-b border-gray-300 text-center"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td class="py-2 px-4 border-b border-gray-300 text-center"><?php echo htmlspecialchars($row['price']); ?></td>
                        <?php if($_SESSION['role'] == 'admin'):?>
                        <td class="py-2 px-4 border-b border-gray-300 text-center">
                            <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                            <form action="" method="post" style="display:inline;" onsubmit="event.preventDefault(); confirmDelete(this);">
                                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken();?>">
                                <input type="hidden" name="delete_product_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-700 ml-4">Delete</button>
                            </form>
                        </td>
                        <?php endif;?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

<?php
$conn->close();
?>