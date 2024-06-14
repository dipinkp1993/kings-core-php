<?php
include 'csrf_token.php';
require_once 'connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password,role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password,$role);
    $stmt->execute();
    $stmt->close();
    

    echo "Registration successful!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function showAlert(message) {
            document.getElementById('alert').innerText = message;
            document.getElementById('alert').classList.remove('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
        <div id="alert" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline"></span>
        </div>
        <form action="" method="post" id="registerForm">
            <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken();?>">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username:</label>
                <input type="text" id="username" name="username" class="mt-1 p-2 w-full border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email:</label>
                <input type="email" id="email" name="email" class="mt-1 p-2 w-full border rounded-lg" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700">Password:</label>
                <input type="password" id="password" name="password" class="mt-1 p-2 w-full border rounded-lg" required>
            </div>
            <div class="mb-6">
                <label for="role" class="block text-gray-700">Role:</label>
                <select name="role" id="role" class="mt-1 p-2 w-full border rounded-lg" required>
                <option value="admin">Admin</option>
                <option value="user">NormalUser</option>
            </div>
            <input type="submit" value="Register" class=" mt-6 w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600">
            <br>
            <a href="login.php">Already Registered?Login Now</a>
        </form>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var form = event.target;

            var xhr = new XMLHttpRequest();
            xhr.open(form.method, form.action, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    showAlert('Registration successful!');
                    form.reset();
                }
            };
            var formData = new FormData(form);
            var encodedData = new URLSearchParams(formData).toString();
            xhr.send(encodedData);
        });
    </script>
</body>
</html>


