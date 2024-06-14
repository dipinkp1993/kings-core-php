<?php
include 'csrf_token.php';
require_once 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'role_check.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT name, price FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $price);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    $name = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE products SET name=?, price=? WHERE id=?");
    $stmt->bind_param("sdi", $name, $price, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: products_list.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
<?php
    require_once 'header.php';
    ?>
    <main class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-center">Edit Product</h2>
            <form action="edit_product.php?id=<?php echo $id; ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken();?>">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Product Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" class="mt-1 p-2 w-full border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($price); ?>" class="mt-1 p-2 w-full border rounded-lg" required>
                </div>
                <input type="submit" value="Update Product" class="w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600">
            </form>
        </div>
    </main>
</body>
</html>