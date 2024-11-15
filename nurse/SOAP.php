<?php 
include 'header.php'; 
include 'sidebar.php'; 

$host = 'localhost';
$dbname = 'catq7745_hospital_management_system';
$username = 'catq7745_root';
$password = 'WG=e9O,e*Nbm';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert or update SOAP data if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = date('Y-m-d');

    if (!isset($_SESSION['user_id'])) {
        die("Unauthorized access. Please log in.");
    }

    // Get the doctor ID from the session
    $doctor_id = $_SESSION['user_id'];

    // Retrieve patient_id and soap_id from the form submission
    $patient_id = htmlspecialchars($_POST['patient_id']);
    $soap_id = isset($_POST['soap_id']) ? intval($_POST['soap_id']) : null; // Get SOAP ID for update

    $subjective = htmlspecialchars($_POST['subjective']);
    $objective = htmlspecialchars($_POST['objective']);
    $assessment = htmlspecialchars($_POST['assessment']);
    $plan = htmlspecialchars($_POST['plan']);

    if ($soap_id) { // If SOAP ID is present, update the existing record
        $stmt = $conn->prepare("UPDATE soap SET subjective=?, objective=?, assessment=?, plan=? WHERE id=?");
        $stmt->bind_param("ssssi", $subjective, $objective, $assessment, $plan, $soap_id);
    } else { // Otherwise, insert a new record
        $stmt = $conn->prepare("INSERT INTO soap (patient_id, doctor_id, subjective, objective, assessment, plan, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $patient_id, $doctor_id, $subjective, $objective, $assessment, $plan, $date);
    }

    // Execute the statement
    if ($stmt->execute()) {
        echo "Record saved successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Retrieve patient data from URL parameters
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Unknown';
$gender = isset($_GET['gender']) ? htmlspecialchars($_GET['gender']) : 'Unknown';
$age = isset($_GET['age']) ? htmlspecialchars($_GET['age']) : 'Unknown';
$dob = isset($_GET['dob']) ? htmlspecialchars($_GET['dob']) : 'Unknown';
$patient_id = isset($_GET['patient_id']) ? htmlspecialchars($_GET['patient_id']) : 'Unknown';

// Retrieve existing SOAP records for the patient along with doctor names
$existing_soap_records = [];
if ($patient_id !== 'Unknown') {
    $stmt = $conn->prepare("SELECT s.*, u.username AS doctor_name FROM soap s JOIN users u ON s.doctor_id = u.user_id WHERE s.patient_id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $existing_soap_records[] = $row;
    }

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
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ccc;
            vertical-align: top;
            cursor: pointer;
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
                <span><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?></span>
            </div>

            <div class="patient-details">
                <div>Admission No : OP12345</div>
                <div>Age: <?= $age ?></div>
                <div>Religion: Kristen</div>
                <div>Patient Type: Payer</div>
            </div>

            <!-- Existing SOAP Records -->
            <h3>SOAP Records</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Doctor</th>
                        <th>Subjective</th>
                        <th>Objective</th>
                        <th>Assessment</th>
                        <th>Plan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($existing_soap_records)): ?>
                        <tr>
                            <td colspan="7">No existing records found for this patient.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($existing_soap_records as $record): ?>
                            <tr>
                                <td><?= $record['created_at'] ?></td>
                                <td><?= htmlspecialchars($record['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($record['subjective']) ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="patient_id" value="<?= $patient_id ?>">
                                        <input type="hidden" name="soap_id" value="<?= $record['soap_id'] ?>">
                                        <textarea name="objective" required><?= htmlspecialchars($record['objective']) ?></textarea>
                                        <button type="submit" class="save-button">Update</button>
                                    </form>
                                </td>
                                <td><?= htmlspecialchars($record['assessment']) ?></td>
                                <td><?= htmlspecialchars($record['plan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
        </div>
    </div>

</body>
</html>
