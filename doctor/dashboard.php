

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="flex">
    <!-- Main Content -->
    <div class="flex-1 p-8 bg-white flex flex-col items-center">
        <h1 class="text-3xl font-bold text-gray-800 mt-20 mb-4">
            Welcome, <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?>
        </h1>
        
        <!-- Landing Image Below the Welcome Message -->
        <img src="home.png" alt="Description of image" class="w-100 h-auto mb-4">
    </div>
</div>
