
<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<!-- Doctor Navbar -->
<nav class="bg-blue-800 p-4 shadow-lg sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <a href="dashboard.php" class="text-white text-lg font-bold">Admin Portal</a>
        
        <div class="text-white flex items-center space-x-2">
            <span id="currentDate" class="text-sm text-gray-300"></span> <!-- Date will appear here -->
            <span>Welcome, Admin. <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?></span>
            <div class="ml-4 inline-block relative">
                <button id="userMenuButton" class="focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 19.121A5.002 5.002 0 0012 21a5.002 5.002 0 006.879-1.879M15 11a3 3 0 11-6 0 3 3 0 016 0zM12 14v2m0 4h.01" />
                    </svg>
                </button>
                <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2">
                    <a href="../index.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- JavaScript to toggle user menu and show date -->
<script>
    // Toggle the user menu
    document.getElementById('userMenuButton').addEventListener('click', function() {
        var menu = document.getElementById('userMenu');
        menu.classList.toggle('hidden');
    });

    // Display the current date beside the name
    const currentDate = new Date().toLocaleDateString('en-US', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
    document.getElementById('currentDate').textContent = currentDate;
</script>

</body>
</html>
