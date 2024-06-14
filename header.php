<header class="bg-white shadow-md">
        <div class="container mx-auto flex justify-between items-center p-6">
            <div class="text-xl font-semibold">
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
            </div>
            <nav class="flex space-x-4">
                <a href="products_list.php" class="text-gray-700 hover:text-blue-500">Manage Product</a>
                <a href="logout.php" class="text-gray-700 hover:text-blue-500">Logout</a>
            </nav>
        </div>
    </header>