<?php
include 'csrf_token.php';

require_once 'connection.php';

$response = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, email, password,role FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $stmt->bind_result($id, $username, $email, $hashed_password,$role);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        
        header("Location: dashboard.php");
        exit;
    } else {
        $response = 'Invalid credentials.';
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
        <div id="alert" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline"></span>
        </div>
        <form action="login.php" method="post" id="loginForm">
        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken();?>">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username/Email:</label>
                <input type="text" id="username" name="username" class="mt-1 p-2 w-full border rounded-lg" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700">Password:</label>
                <input type="password" id="password" name="password" class="mt-1 p-2 w-full border rounded-lg" required>
            </div>
            <input type="submit" value="Login" class="w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600">
            <a href="index.php" class="text-gray">New Member?Register Now</a>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var form = event.target;

            var xhr = new XMLHttpRequest();
            xhr.open(form.method, form.action, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        if (xhr.responseText.includes('Invalid credentials.')) {
                            showAlert('Invalid credentials.', false);
                        } else {
                            showAlert('Login successful!', true);
                            form.reset();
                            setTimeout(function() {
                                window.location.href = 'dashboard.php';
                            }, 1000);
                        }
                    }
                }
            };
            var formData = new FormData(form);
            var encodedData = new URLSearchParams(formData).toString();
            xhr.send(encodedData);
        });

        <?php if (!empty($response)) : ?>
        showAlert('<?= $response ?>', false);
        <?php endif; ?>
    </script>
</body>
</html>
