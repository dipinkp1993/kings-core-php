<?php
include 'csrf_token.php';

require_once 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'role_check.php';

$response = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    $name = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
    $stmt->bind_param("sd", $name, $price);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $response = 'Product added successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function showAlert(message, isSuccess) {
            var alertDiv = document.getElementById('alert');
            alertDiv.innerText = message;
            alertDiv.classList.remove('hidden');
            if (isSuccess) {
                alertDiv.classList.remove('bg-red-100', 'border-red-400', 'text-red-700');
                alertDiv.classList.add('bg-green-100', 'border-green-400', 'text-green-700');
            } else {
                alertDiv.classList.remove('bg-green-100', 'border-green-400', 'text-green-700');
                alertDiv.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
            }
        }
    </script>
</head>

<body class="bg-gray-100 min-h-screen">
<?php
    require_once 'header.php';
    ?>
    <main class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-center">Add Product</h2>
            <div id="alert" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                <span class="block sm:inline"></span>
            </div>
            <form action="" method="post" id="productForm">
            <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken();?>">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Product Name:</label>
                    <input type="text" id="name" name="name" class="mt-1 p-2 w-full border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700">Price:</label>
                    <input type="number" id="price" name="price" class="mt-1 p-2 w-full border rounded-lg" required>
                </div>
                <input type="submit" value="Add Product" class="w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600">
            </form>
        </div>
    </main>
    

    <script>
        document.getElementById('productForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var form = event.target;

            var xhr = new XMLHttpRequest();
            xhr.open(form.method, form.action, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        showAlert('Product added successfully!', true);
                        form.reset();
                    }
                }
            };
            var formData = new FormData(form);
            var encodedData = new URLSearchParams(formData).toString();
            xhr.send(encodedData);
        });

        <?php if (!empty($response)) : ?>
        showAlert('<?= $response ?>', true);
        <?php endif; ?>
    </script>
</body>
</html>
