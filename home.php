<?php
// Database connection details
$host = 'localhost';  
$dbname = 'hospital_management_system';  
$username = 'root';  
$password = '';  

// Establish the database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $password_input = $_POST['password'];

    // Get user data from the database
    $stmt = $conn->prepare("SELECT username, password, role FROM users WHERE username = :username");
    $stmt->execute(['username' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password_input === $user['password']) {
        // Redirect based on user role
        switch ($user['role']) {
            case 'doctor':
                header('Location: /doctor/dashboard.php');
                break;
            case 'admin':
                header('Location: /admin/dashboard.php');
                break;
            case 'nurse':
                header('Location: /nurse/dashboard.php');
                break;
            case 'laborant':
                header('Location: /laborant/dashboard.php');
                break;
            default:
                echo '<p style="color: red;">Invalid role!</p>';
                exit;
        }
        exit;
    } else {
        echo '<p style="color: red;">Incorrect username or password!</p>';
    }
}
?>

<?php include 'header.php'; ?>

<!-- Simplified Layout -->
<div class="flex justify-center items-center h-screen bg-white">
    <div class="text-center">
    <img src="home.png" alt="Hospital Logo" class="h-120 w-120 object-contain">
       
    </div>
</div>

</body>
</html>
