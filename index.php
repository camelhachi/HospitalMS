<?php
// Start the session
session_start();

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

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $password_input = $_POST['password'];

    // Prepare the SQL query to get the user by username
    $stmt = $conn->prepare("SELECT user_id, username, password, role_id FROM Users WHERE username = :username");
    $stmt->execute(['username' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Compare input password with stored password directly
        if ($password_input === $user['password']) {
            // Store the username, user ID, and role_id in the session
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['user_id']; // Store the actual user ID in the session
            $_SESSION['role_id'] = $user['role_id'];

            // Redirect based on role
            switch ($user['role_id']) {
                case 1: // Doctor
                    header('Location: /doctor/dashboard.php');
                    break;
                case 2: // Nurse
                    header('Location: /nurse/dashboard.php');
                    break;
                case 3: // Laborant
                    header('Location: /laborant/dashboard.php');
                    break;
                case 4: // Admin
                    header('Location: /admin/dashboard.php');
                    break;
                default:
                    echo '<p style="color: red;">Invalid role!</p>';
                    exit;
            }
            exit;
        } else {
            echo '<p style="color: red;">Incorrect password!</p>';
        }
    } else {
        echo '<p style="color: red;">User not found!</p>';
    }
}
?>

<?php include 'header.php'; ?>

<!-- HTML Login Form -->
<div class="flex justify-center items-center h-screen">
    <div class="bg-white p-10 rounded-lg shadow-lg w-96">
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-blue-800 mx-auto mb-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-9a3 3 0 100-6 3 3 0 000 6zm-4 8a6 6 0 1112 0H6z" clip-rule="evenodd" />
            </svg>
        </div>
        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">User Login</h2>
        <form method="POST" action="">
            <div class="mb-4">
                <input type="text" name="user_id" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Username" required>
            </div>
            <div class="mb-4">
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Password" required>
            </div>
            <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500">Login</button>
        </form>
    </div>
</div>

</body>
</html>
