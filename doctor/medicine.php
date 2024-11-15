<?php
include 'header.php';
include 'sidebar.php';
// Database connection settings
$servername = "localhost"; // Your database server name
$username = "root";        // Your database username
$password = "";            // Your database password
$dbname = "hospital_management_system"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get patient ID from the query parameter
$patient_id = $_GET['patient_id'];

// Fetch medication history from the database
$medication_history_query = "SELECT * FROM medicines WHERE patient_id = ?";
$stmt = $conn->prepare($medication_history_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$medication_history_result = $stmt->get_result();

$allergy_query = "SELECT food_allergy, drug_allergy, other_allergy FROM allergies WHERE patient_id = ?";
$stmt = $conn->prepare($allergy_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$allergy_result = $stmt->get_result();
$allergies = $allergy_result->fetch_assoc();

// Save allergies if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_allergies'])) {
    $food_allergy = $_POST['food_allergy'];
    $drug_allergy = $_POST['drug_allergy'];
    $other_allergy = $_POST['other_allergy'];

    // Update allergies in the database
    $update_allergies_query = "UPDATE allergies SET food_allergy = ?, drug_allergy = ?, other_allergy = ? WHERE patient_id = ?";
    $update_stmt = $conn->prepare($update_allergies_query);
    $update_stmt->bind_param("sssi", $food_allergy, $drug_allergy, $other_allergy, $patient_id);
    $update_stmt->execute();
    $update_stmt->close();

}

// Add new medication entry if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_medication'])) {
    $new_item = $_POST['new_item'];
    $new_dose = $_POST['new_dose'];
    $new_frequency = $_POST['new_frequency'];
    $new_route = $_POST['new_route'];
    $new_instruction = $_POST['new_instruction'];
    $new_quantity = $_POST['new_quantity'];
    $new_uom = $_POST['new_uom'];

    // Insert new medication into the database
    $insert_medication_query = "INSERT INTO medicines (patient_id, item, dose, frequency, route, instruction, quantity, unit_of_measurement) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_medication_query);
    $insert_stmt->bind_param("isssssis", $patient_id, $new_item, $new_dose, $new_frequency, $new_route, $new_instruction, $new_quantity, $new_uom);
    $insert_stmt->execute();
    $insert_stmt->close();

    // Redirect to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?patient_id=" . $patient_id);
    exit();
}

// Delete medication if the delete form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_medication'])) {
    $medicine_id = $_POST['medicine_id'];

    // Delete medication from the database
    $delete_medication_query = "DELETE FROM medicines WHERE medicine_id = ?";
    $delete_stmt = $conn->prepare($delete_medication_query);
    $delete_stmt->bind_param("i", $medicine_id);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Redirect to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?patient_id=" . $patient_id);
    exit();
}

$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Unknown';
$gender = isset($_GET['gender']) ? htmlspecialchars($_GET['gender']) : 'Unknown';
$age = isset($_GET['age']) ? htmlspecialchars($_GET['age']) : 'Unknown';
$dob = isset($_GET['dob']) ? htmlspecialchars($_GET['dob']) : 'Unknown';
$patient_id = isset($_GET['patient_id']) ? htmlspecialchars($_GET['patient_id']) : 'Unknown';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #ffffff;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #004080;
            /* Dark blue */
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

        /* Sidebar fixed width */
        .sidebar {
            background-color: #cce0ff;
            /* Light blue */
            width: 200px;
            /* Fixed width */
            padding: 20px;
            color: black;
            display: flex;
            flex-direction: column;
            gap: 15px;
            height: 100vh;
            border-right: 2px solid #b3cde0;
            /* Slightly darker blue for the border */
        }

        /* Main Content Styles */
        .content {
            flex: 1;
            /* Allow content to take remaining space */
            padding: 30px 20px;
            /* Padding for the content area */
            background-color: #ffffff;
            /* White background for content */
            margin-left: 200px;
            /* Offset by sidebar width */
            margin-right: 50px;
          
        }

        /* Headers and Tables Styles */
        h2 {
            border-bottom: 2px solid #004080;
            /* Dark blue */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th,
        table td {
            padding: 12px;
            border: 1px solid #b3cde0;
            /* Light blue border */
            text-align: left;
        }

        table th {
            background-color: #a6c8ff;
            /* Lighter blue for header */
            color: #333;
        }

        table tr:hover {
            background-color: #cce0ff;
            /* Hover effect in light blue */
        }

        /* Button Styles */
        button {
            padding: 10px 15px;
            background-color: #0066cc;
            /* Blue button */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #005bb5;
            /* Darker blue on hover */
        }

        input[type="text"],
        input[type="number"],
        select {
            padding: 8px;
            width: calc(100% - 20px);
            margin-top: 5px;
            border: 1px solid #b3cde0;
            /* Light blue border */
            border-radius: 4px;
            font-size: 14px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                border-right: none;
            }

            .content {
                padding: 20px;
                margin-left: 0;
                /* Reset margin for smaller screens */
            }
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
                <span>Logged in as: <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?></span>
            </div>

            <div class="patient-details">
                <div>Admission No: OP12345</div>
                <div>Age: <?= $age ?></div>
                <div>Religion: Kristen</div>
                <div>Patient Type: Payer</div>
            </div>

            <h2>Allergies</h2>
            <!-- Editable Allergy Table -->
            <form method="POST" action="">
                <table>
                    <thead>
                        <tr>
                            <th>Food Allergy</th>
                            <th>Drug Allergy</th>
                            <th>Other Allergy</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="food_allergy" value="<?= htmlspecialchars($allergies['food_allergy']) ?>" /></td>
                            <td><input type="text" name="drug_allergy" value="<?= htmlspecialchars($allergies['drug_allergy']) ?>" /></td>
                            <td><input type="text" name="other_allergy" value="<?= htmlspecialchars($allergies['other_allergy']) ?>" /></td>
                            <td><button type="submit" name="save_allergies">Save</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>

            <h2>Medication History</h2>
            <form method="POST" action="">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Dose</th>
                            <th>Frequency</th>
                            <th>Route</th>
                            <th>Instruction</th>
                            <th>Quantity</th>
                            <th>UOM</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($medication = $medication_history_result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= htmlspecialchars($medication['item']) ?></td>
                                <td><?= htmlspecialchars($medication['dose']) ?></td>
                                <td><?= htmlspecialchars($medication['frequency']) ?></td>
                                <td><?= htmlspecialchars($medication['route']) ?></td>
                                <td><?= htmlspecialchars($medication['instruction']) ?></td>
                                <td><?= htmlspecialchars($medication['quantity']) ?></td>
                                <td><?= htmlspecialchars($medication['unit_of_measurement']) ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="medicine_id" value="<?= $medication['medicine_id'] ?>" />
                                        <button type="submit" name="delete_medication">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <tr>
                            <td><input type="text" name="new_item" placeholder="Item" required /></td>
                            <td><input type="text" name="new_dose" placeholder="Dose" required /></td>
                            <td><input type="text" name="new_frequency" placeholder="Frequency" required /></td>
                            <td><input type="text" name="new_route" placeholder="Route" required /></td>
                            <td><input type="text" name="new_instruction" placeholder="Instruction" required /></td>
                            <td><input type="number" name="new_quantity" placeholder="Quantity" required /></td>
                            <td><input type="text" name="new_uom" placeholder="UOM" required /></td>
                            <td><button type="submit" name="add_medication">Add</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</body>

</html>