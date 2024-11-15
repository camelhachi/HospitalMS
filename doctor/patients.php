<?php 
include 'header.php'; 
include 'sidebar.php'; 

// Database connection details
$host = 'localhost';  
$dbname = 'hospital_management_system';  
$username = 'root';  
$password = '';  

// Check if user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}

// Get the doctor ID from the session
$doctor_id = $_SESSION['user_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch patient data assigned to the doctor, including room number
    $query = "
        SELECT 
            p.patient_id, 
            p.name, 
            p.dob, 
            p.age, 
            p.gender, 
            p.religion, 
            p.phone_number, 
            r.room_number AS room_id, 
            p.doctor_id, 
            p.created_at, 
            p.status 
        FROM 
            patients p
        JOIN 
            rooms r ON p.room_id = r.room_id 
        WHERE 
            p.doctor_id = :doctor_id";
    
    $stmt = $pdo->prepare($query);
    
    try {
        $stmt->execute(['doctor_id' => $doctor_id]);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Query failed: " . htmlspecialchars($e->getMessage()); // Debugging line
    }

} catch (PDOException $e) {
    echo "Connection failed: " . htmlspecialchars($e->getMessage());
}
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
                        <th class="py-3 px-6">Status</th>
                    </tr>
                </thead>
                <tbody id="patientTableBody" class="text-gray-600 text-sm font-light">
                    <?php if (empty($patients)): ?>
                        <tr>
                            <td colspan="9" class="py-3 px-6 text-center">No patients found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($patients as $patient) : ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100" data-status="<?= htmlspecialchars($patient['status']) ?>">
                                <td class="py-3 px-6"><?= htmlspecialchars($patient['patient_id']) ?></td>
                                <td class="py-3 px-6 text-left">
                                    <a href="soap.php?patient_id=<?= htmlspecialchars($patient['patient_id']) ?>&name=<?= urlencode($patient['name']) ?>&gender=<?= urlencode($patient['gender']) ?>&age=<?= $patient['age'] ?>&dob=<?= urlencode($patient['dob']) ?>" 
                                       class="text-blue-600 hover:underline">
                                        <?= htmlspecialchars($patient['name']) ?>
                                    </a> | 
                                    <a href="medicine.php?patient_id=<?= htmlspecialchars($patient['patient_id']) ?>&name=<?= urlencode($patient['name']) ?>&gender=<?= urlencode($patient['gender']) ?>&age=<?= $patient['age'] ?>&dob=<?= urlencode($patient['dob']) ?>" 
                                       class="text-blue-600 hover:underline">
                                        Medicine
                                    </a> | 
                                    <a href="radiology.php?patient_id=<?= htmlspecialchars($patient['patient_id']) ?>&name=<?= urlencode($patient['name']) ?>&gender=<?= urlencode($patient['gender']) ?>&age=<?= $patient['age'] ?>&dob=<?= urlencode($patient['dob']) ?>" 
                                       class="text-blue-600 hover:underline">
                                        Radiology
                                    </a>
                                </td>
                                <td class="py-3 px-6"><?= htmlspecialchars($patient['gender']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($patient['age']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars(date('d-m-Y', strtotime($patient['dob']))) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($patient['religion']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($patient['phone_number']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($patient['room_id']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($patient['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
