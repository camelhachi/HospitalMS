<?php
include 'header.php';
include 'sidebar.php';

// Database connection
$host = 'localhost';
$dbname = 'hospital_management_system';
$username = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Unknown';
$gender = isset($_GET['gender']) ? htmlspecialchars($_GET['gender']) : 'Unknown';
$age = isset($_GET['age']) ? htmlspecialchars($_GET['age']) : 'Unknown';
$dob = isset($_GET['dob']) ? htmlspecialchars($_GET['dob']) : 'Unknown';
$patient_id = isset($_GET['patient_id']) ? htmlspecialchars($_GET['patient_id']) : null;

// Handle form submission for radiology information
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_date = htmlspecialchars(trim($_POST['order_date']));
    $test_types = isset($_POST['test_type']) ? $_POST['test_type'] : [];
    $diagnosis = htmlspecialchars(trim($_POST['diagnosis']));
    $notes = htmlspecialchars(trim($_POST['notes']));

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("User is not logged in.");
    }
    $doctor_id = $_SESSION['user_id'];

    // Prepare the SQL statement for inserting diagnosis information into the diagnosis table
    $sql = "INSERT INTO diagnosis (patient_id, doctor_id, test_type, diagnosis_date, test_result, result_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Preparation failed: " . $conn->error);
    }

    // Prepare and execute for each test type
    foreach ($test_types as $test_type) {
        // Set test_result to NULL
        $test_result = null; // Set to NULL

        $stmt->bind_param("iissss", $patient_id, $doctor_id, $test_type, $order_date, $test_result, $result_date);
        if (!$stmt->execute()) {
            echo "Error adding diagnosis: " . $stmt->error;
        }
    }

    // Clean up
    $stmt->close();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #ffffff;
        }

        /* Navbar Styles */
        .navbar {
            background-color: black;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        /* Sidebar Styles */
        .container {
            display: flex;
        }

        .sidebar {
            background-color: #f5eddf;
            width: 200px;
            padding: 20px;
            color: black;
            display: flex;
            flex-direction: column;
            gap: 15px;
            height: 100vh;
        }

        /* Content Styles */
        .content {
            flex: 1;
            padding: 50px 50px 20px 300px;
            background-color: #ffffff;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ccc;
            vertical-align: top;
        }

        table th {
            background-color: #eee;
        }

        /* Button Styles */
        .save-button {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .save-button:hover {
            background-color: #45a049;
        }

        /* Form Styles */
        .form-container {
            margin-top: 20px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        /* Checkbox Styles */
        .checkbox-group {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>

<body>

    <!-- Sidebar and Content -->
    <div class="container">
        <!-- Sidebar included here -->

        <!-- Main Content -->
        <div class="content">
            <h2><span><?= $name ?></span> <span><?= $gender === 'F' ? '♀' : '♂' ?></span> | <?= $age ?> | <?= $dob ?></h2>
            <div class="patient-info">
                <span> <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?></span>
            </div>

            <div class="patient-details">
                <div>Admission No : OP12345</div>
                <div>Age: <?= $age ?></div>
                <div>Religion: Kristen</div>
                <div>Patient Type: Payer</div>
            </div>

            <!-- Radiology Section -->
            <h2>Radiology</h2>
            <div class="form-container">
                <form method="POST" action="">
                    <label for="order-date">Order Date</label>
                    <input type="text" id="order-date" name="order_date" value="<?= date('Y-m-d') ?>" readonly>

                    <label>Test Type</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="test_type[]" value="X-ray"> X-ray</label>
                        <label><input type="checkbox" name="test_type[]" value="MRI"> MRI</label>
                        <label><input type="checkbox" name="test_type[]" value="CT Scan"> CT Scan</label>
                        <label><input type="checkbox" name="test_type[]" value="Brain Scan"> Brain Scan</label>
                    </div>

                    <label for="diagnosis">Diagnosis</label>
                    <input type="text" id="diagnosis" name="diagnosis" required>

                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="4"></textarea>

                    <button type="submit" class="save-button">Save</button>
                </form>
            </div>

            <!-- Radiology Results Table -->
            <h2>Radiology Results</h2>
            <div class="form-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order Date</th>
                            <th>Doctor</th>
                            <th>Test Type</th>
                            <th>Result</th>
                            <th>Result Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch results from the diagnosis table for the current patient
                        $sql = "SELECT doctor_id, test_type, test_result, result_date FROM diagnosis WHERE patient_id = ?";
                        $stmt = $conn->prepare($sql);
                        if ($stmt) {
                            $stmt->bind_param("i", $patient_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                // Output data for each row
                                while ($row = $result->fetch_assoc()) {
                                    // Fetch the doctor's name based on the doctor_id
                                    $doctor_sql = "SELECT username FROM users WHERE user_id = ?"; // Update here
                                    $doctor_stmt = $conn->prepare($doctor_sql);
                                    $doctor_stmt->bind_param("i", $row['doctor_id']);
                                    $doctor_stmt->execute();
                                    $doctor_result = $doctor_stmt->get_result();
                                    $doctor_name = $doctor_result->num_rows > 0 ? htmlspecialchars($doctor_result->fetch_assoc()['username']) : 'Unknown Doctor';

                                    echo "<tr>
                <td>" . date('Y-m-d') . "</td>
                <td>" . $doctor_name . "</td>
                <td>" . htmlspecialchars($row['test_type']) . "</td>
                <td>" . (!empty($row['test_result']) ? htmlspecialchars($row['test_result']) : "Test hasn't come back yet") . "</td>
                <td>" . htmlspecialchars($row['result_date']) . "</td>
            </tr>";
                                    $doctor_stmt->close(); // Close the doctor statement
                                }
                            } else {
                                echo "<tr><td colspan='5'>No results found</td></tr>";
                            }
                            $stmt->close(); // Close the main statement
                        } else {
                            echo "Error preparing the statement: " . $conn->error;
                        }

                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>

<?php
$conn->close(); // Close the connection at the end
?>