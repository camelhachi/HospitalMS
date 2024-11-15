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

// Handle search query
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

// Fetch diagnosis data with optional filtering by patient name
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
            WHEN d.test_result IS NOT NULL THEN 'Complete' 
            ELSE 'Incomplete' 
        END AS status
    FROM diagnosis d
    JOIN patients p ON d.patient_id = p.patient_id
    JOIN users u ON d.doctor_id = u.user_id
    WHERE p.name LIKE :searchQuery
";
$stmt = $pdo->prepare($query);
$stmt->execute([':searchQuery' => "%$searchQuery%"]);
$diagnoses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex">
    <!-- Main content area -->
    <div class="flex-1 p-6 bg-white shadow-lg rounded-md mt-10 ml-40">
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Patients List</h2>

        <!-- Search Bar -->
        <form method="GET" class="mb-4">
            <input type="text" name="search" placeholder="Search by Patient Name" value="<?= htmlspecialchars($searchQuery) ?>" class="border px-3 py-2 rounded" />
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
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
                                <?= $diagnosis['test_result'] ? htmlspecialchars($diagnosis['test_result']) : 'N/A' ?>
                            </td>
                            <td class="py-3 px-6"><?= htmlspecialchars($diagnosis['result_date']) ?></td>
                            <td class="py-3 px-6">
                                <span class="<?= $diagnosis['status'] == 'Complete' ? 'text-green-500' : 'text-red-500' ?>">
                                    <?= htmlspecialchars($diagnosis['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
