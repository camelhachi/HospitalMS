<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php
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


<!-- Main Content Area -->
<div class="ml-40 p-6 bg-white shadow-lg rounded-md"> <!-- Adjusted margin-left for sidebar -->
    <!-- Patient Information Header -->
    <div class="grid grid-cols-4 gap-4 border-b-2 pb-4 mb-4">
        <div class="text-2xl font-bold col-span-1"><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?>'s Analytics</div>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-5 gap-4 mb-8">
        <div class="col-span-4">
            <div class="border rounded-lg bg-gray-50">
                <canvas id="patientChart" height="150"></canvas>
            </div>
        </div>
        <div class="col-span-1 bg-gray-50 p-4 rounded-lg shadow-md">
            <div class="font-semibold mb-2 ">Date: <?= date('F Y') ?></div>
            <div class="text-sm text-gray-600">
                <div class="grid grid-cols-7 gap-2">
                    <?php
                    // Get the current month and year
                    $currentMonth = date('m'); // Current month (1-12)
                    $currentYear = date('Y'); // Current year

                    // Day labels
                    $days = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
                    foreach ($days as $day) {
                        echo "<div class='font-bold text-center'>$day</div>";
                    }

                    // Get the first day of the month and the total number of days
                    $firstDay = strtotime("$currentYear-$currentMonth-01");
                    $totalDays = date('t', $firstDay);
                    $firstDayOfWeek = date('w', $firstDay);

                    // Fill empty cells until the first day of the month
                    for ($i = 0; $i < $firstDayOfWeek; $i++) {
                        echo "<div></div>"; // Empty cell
                    }

                    // Fill in the days of the month
                    for ($day = 1; $day <= $totalDays; $day++) {
                        echo "<div class='border p-2 text-center'>$day</div>";
                        // Check if the last day of the week has been reached
                        if (($day + $firstDayOfWeek) % 7 == 0) {
                            echo "<div class='col-span-7'></div>"; // New row
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>


    <!-- Table Section -->
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

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('patientChart').getContext('2d');
    var patientChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['12', '13', '14', '15', '16', '17', '18'],
            datasets: [{
                label: 'Patient Count',
                data: [1,2,3,1,5,0],
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245, 158, 11, 0.2)',
                fill: true,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>

</html>