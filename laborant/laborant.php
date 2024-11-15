<?php
include 'header.php';
include 'sidebar.php';

// Database connection
$host = 'localhost';
$dbname = 'hospital_management_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Update test result if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['diagnosis_id'], $_POST['test_result'])) {
    $diagnosis_id = $_POST['diagnosis_id'];
    $test_result = $_POST['test_result'];
    $result_date = date('Y-m-d');

    // Update test result in the database
    $query = "UPDATE diagnosis SET test_result = ?, result_date = ? WHERE diagnosis_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$test_result, $result_date, $diagnosis_id]);
}

// Check if search term is provided
$search_patient_id = isset($_GET['search_patient_id']) ? $_GET['search_patient_id'] : '';

// Fetch diagnosis data from the database with optional search
$query = "
    SELECT 
        d.diagnosis_id, 
        d.patient_id, 
        p.name AS patient_name, 
        d.doctor_id, 
        u.username AS doctor_name, 
        d.test_type, 
        d.diagnosis_date, 
        d.test_result, 
        d.result_date,
        CASE 
            WHEN d.test_result IS NOT NULL THEN 'Uploaded' 
            ELSE 'Pending' 
        END AS status
    FROM diagnosis d
    JOIN patients p ON d.patient_id = p.patient_id
    JOIN users u ON d.doctor_id = u.user_id
";

if ($search_patient_id) {
    $query .= " WHERE d.patient_id = :search_patient_id";
}

$stmt = $pdo->prepare($query);

if ($search_patient_id) {
    $stmt->bindParam(':search_patient_id', $search_patient_id, PDO::PARAM_STR);
}

$stmt->execute();
$diagnoses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex">
    <!-- Main content area -->
    <div class="flex-1 p-6 bg-white shadow-lg rounded-md mt-10 ml-40">
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Report List</h2>

        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <label for="search_patient_id" class="text-gray-700">Search by Patient ID:</label>
            <input type="text" id="search_patient_id" name="search_patient_id" value="<?= htmlspecialchars($search_patient_id) ?>" placeholder="Enter Patient ID" class="border border-gray-300 px-3 py-2 rounded-md">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Search</button>
        </form>

        <!-- Diagnosis List Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-left text-sm leading-normal">
                        <th class="py-3 px-6">Diagnosis ID</th>
                        <th class="py-3 px-6">Patient Name</th>
                        <th class="py-3 px-6">Doctor Name</th>
                        <th class="py-3 px-6">Test Type</th>
                        <th class="py-3 px-6">Diagnosis Date</th>
                        <th class="py-3 px-6">Test Result</th>
                        <th class="py-3 px-6">Result Date</th>
                        <th class="py-3 px-6">Status</th>
                    </tr>
                </thead>
                <tbody id="diagnosisTableBody" class="text-gray-600 text-sm font-light">
                    <?php foreach ($diagnoses as $diagnosis) : ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6"><?= htmlspecialchars($diagnosis['diagnosis_id']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($diagnosis['patient_name']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($diagnosis['doctor_name']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($diagnosis['test_type']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($diagnosis['diagnosis_date']) ?></td>
                            <td class="py-3 px-6">
                                <?php if ($diagnosis['test_result'] === null): ?>
                                    <form method="POST">
                                        <input type="hidden" name="diagnosis_id" value="<?= $diagnosis['diagnosis_id'] ?>">
                                        <input type="text" name="test_result" placeholder="Enter result" required>
                                        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Save</button>
                                    </form>
                                <?php else: ?>
                                    <?= htmlspecialchars($diagnosis['test_result']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-6"><?= htmlspecialchars($diagnosis['result_date']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($diagnosis['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
