<?php 
include 'header.php'; 
include 'sidebar.php'; 

// Database connection

$host = 'localhost';
$dbname = 'catq7745_hospital_management_system';
$username = 'catq7745_root';
$password = 'WG=e9O,e*Nbm';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Fetch patient data from the database
$query = "SELECT patient_id, name, dob, age, gender, religion, phone_number, room_id, doctor_id, created_at, status FROM patients";
$stmt = $pdo->prepare($query);
$stmt->execute();
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex">
    <!-- Main content area -->
    <div class="flex-1 p-6 bg-white shadow-lg rounded-md mt-10 ml-40">
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">List of Patients</h2>
    
        <!-- Patient List Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-left text-sm leading-normal">
                        <th class="py-3 px-6">Patient ID</th>
                        <th class="py-3 px-6">Name</th>
                        <th class="py-3 px-6">Gender</th>
                        <th class="py-3 px-6">Age</th>
                        <th class="py-3 px-6">Date of Birth</th>
                        <th class="py-3 px-6">Religion</th>
                        <th class="py-3 px-6">Phone Number</th>
                        <th class="py-3 px-6">Room Number</th>
                        <th class="py-3 px-6">Doctor ID</th>
                        <th class="py-3 px-6">Created At</th>
                        <th class="py-3 px-6">Status</th>
                    </tr>
                </thead>
                <tbody id="patientTableBody" class="text-gray-600 text-sm font-light">
                    <?php foreach ($patients as $patient) : ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100" data-status="<?= $patient['status'] ?>">
                            <td class="py-3 px-6"><?= $patient['patient_id'] ?></td>
                            <td class="py-3 px-6">
                                <a href="soap.php?patient_id=<?= htmlspecialchars($patient['patient_id']) ?>&name=<?= urlencode($patient['name']) ?>&gender=<?= urlencode($patient['gender']) ?>&age=<?= $patient['age'] ?>&dob=<?= urlencode($patient['dob']) ?>" 
                                   class="text-blue-600 hover:underline">
                                    <?= $patient['name'] ?>
                                </a>
                            </td>
                            <td class="py-3 px-6"><?= $patient['gender'] ?></td>
                            <td class="py-3 px-6"><?= $patient['age'] ?></td>
                            <td class="py-3 px-6"><?= $patient['dob'] ?></td>
                            <td class="py-3 px-6"><?= $patient['religion'] ?></td>
                            <td class="py-3 px-6"><?= $patient['phone_number'] ?></td>
                            <td class="py-3 px-6"><?= $patient['room_id'] ?></td>
                            <td class="py-3 px-6"><?= $patient['doctor_id'] ?></td>
                            <td class="py-3 px-6"><?= $patient['created_at'] ?></td>
                            <td class="py-3 px-6"><?= $patient['status'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


</body>
</html>
