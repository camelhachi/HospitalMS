<?php
include 'header.php';
include 'sidebar.php';
include 'db.php';


$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch patients with their corresponding room numbers and doctor usernames
$query = "
    SELECT p.*, r.room_number, u.username AS doctor_username
    FROM patients p
    JOIN rooms r ON p.room_id = r.room_id
    JOIN users u ON p.doctor_id = u.user_id
    WHERE p.name LIKE :searchTerm
";

$stmt = $pdo->prepare($query);
$stmt->execute(['searchTerm' => "%$searchTerm%"]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT user_id, username FROM users WHERE role_id = ?");
$stmt->execute([1]); // Assuming role_id 1 is for doctors
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch rooms for dropdown in Patient Registration Form (is_empty = 1)
$stmt = $pdo->prepare("SELECT room_id, room_number FROM rooms WHERE is_empty = 1");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle the update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $patientId = $_POST['patient_id'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $status = $_POST['status'];
    $roomId = $_POST['room_id']; // Get the room_id from POST data
    $doctorId = $_POST['doctor_id']; // Get the doctor_id from POST data



    // Update the patient record
    $updateQuery = "UPDATE patients SET name = ?, gender = ?, age = ?, dob = ?, status = ?, room_id = ?, doctor_id = ? WHERE patient_id = ?";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([$name, $gender, $age, $dob, $status, $roomId, $doctorId, $patientId]);

    // Redirect to the same page to see the updated list
    echo '<p>Patient updated successfully. You will be redirected shortly.</p>';
    echo '<meta http-equiv="refresh" content="3;url=patients.php">';
    exit;
}

// Handle the delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $patientIdToDelete = $_POST['patient_id'];

    // Delete the patient record
    $deleteQuery = "DELETE FROM patients WHERE patient_id = ?";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute([$patientIdToDelete]);

    // Redirect to the same page to see the updated list
    echo '<p>Patient deleted successfully. You will be redirected shortly.</p>';
    echo '<meta http-equiv="refresh" content="3;url=patients.php">';
    exit;
}

$statusCounts = array_count_values(array_column($patients, 'status'));
$labels = array_keys($statusCounts);
$values = array_values($statusCounts);

?>

<div class="flex">


    <div class="flex-1 p-6 bg-white shadow-lg rounded-md mt-20 ml-40">
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">List of Patients</h2>

        <form method="GET" action="patients.php" class="mb-4">
            <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($searchTerm) ?>" class="border rounded p-2">
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Search</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-left text-sm leading-normal">
                        <th class="py-3 px-6">Name</th>
                        <th class="py-3 px-6">Gender</th>
                        <th class="py-3 px-6">Age</th>
                        <th class="py-3 px-6">Date of Birth</th>
                        <th class="py-3 px-6">Room</th>
                        <th class="py-3 px-6">Doctor</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Action</th>
                    </tr>
                </thead>
                <tbody id="patientTableBody" class="text-gray-600 text-sm font-light">
                    <?php foreach ($patients as $patient) : ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6"><?= htmlspecialchars($patient['name']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($patient['gender']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($patient['age']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($patient['dob']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($patient['room_number']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($patient['doctor_username']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($patient['status']) ?></td>
                            <td class="py-3 px-6 flex space-x-2">
                                <button onclick="openEditModal(<?= htmlspecialchars($patient['patient_id']) ?>, '<?= htmlspecialchars($patient['name']) ?>', '<?= htmlspecialchars($patient['gender']) ?>', <?= htmlspecialchars($patient['age']) ?>, '<?= htmlspecialchars($patient['dob']) ?>', '<?= htmlspecialchars($patient['status']) ?>')" class="px-2 py-2 bg-white text-white rounded-md hover:bg-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 50 50" class="h-5 w-5 mr-1">
                                        <path d="M 43.125 2 C 41.878906 2 40.636719 2.488281 39.6875 3.4375 L 38.875 4.25 L 45.75 11.125 C 45.746094 11.128906 46.5625 10.3125 46.5625 10.3125 C 48.464844 8.410156 48.460938 5.335938 46.5625 3.4375 C 45.609375 2.488281 44.371094 2 43.125 2 Z M 37.34375 6.03125 C 37.117188 6.0625 36.90625 6.175781 36.75 6.34375 L 4.3125 38.8125 C 4.183594 38.929688 4.085938 39.082031 4.03125 39.25 L 2.03125 46.75 C 1.941406 47.09375 2.042969 47.457031 2.292969 47.707031 C 2.542969 47.957031 2.90625 48.058594 3.25 47.96875 L 10.75 45.96875 C 10.917969 45.914063 11.070313 45.816406 11.1875 45.6875 L 43.65625 13.25 C 44.054688 12.863281 44.058594 12.226563 43.671875 11.828125 C 43.285156 11.429688 42.648438 11.425781 42.25 11.8125 L 9.96875 44.09375 L 5.90625 40.03125 L 38.1875 7.75 C 38.488281 7.460938 38.578125 7.011719 38.410156 6.628906 C 38.242188 6.246094 37.855469 6.007813 37.4375 6.03125 C 37.40625 6.03125 37.375 6.03125 37.34375 6.03125 Z"></path>
                                    </svg>
                                </button>
                                <button onclick="confirmDelete(<?= htmlspecialchars($patient['patient_id']) ?>)" class="px-2 py-2 bg-white text-white rounded-md hover:bg-red-600"><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 24 24" class="h-5 w-5 mr-1">
                                        <path d="M 10 2 L 9 3 L 5 3 C 4.4 3 4 3.4 4 4 C 4 4.6 4.4 5 5 5 L 7 5 L 17 5 L 19 5 C 19.6 5 20 4.6 20 4 C 20 3.4 19.6 3 19 3 L 15 3 L 14 2 L 10 2 z M 5 7 L 5 20 C 5 21.1 5.9 22 7 22 L 17 22 C 18.1 22 19 21.1 19 20 L 19 7 L 5 7 z M 9 9 C 9.6 9 10 9.4 10 10 L 10 19 C 10 19.6 9.6 20 9 20 C 8.4 20 8 19.6 8 19 L 8 10 C 8 9.4 8.4 9 9 9 z M 15 9 C 15.6 9 16 9.4 16 10 L 16 19 C 16 19.6 15.6 20 15 20 C 14.4 20 14 19.6 14 19 L 14 10 C 14 9.4 14.4 9 15 9 z"></path>
                                    </svg></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex-1 p-6 bg-white shadow-lg rounded-md mt-20 ml-10 mr-10">
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Patients Analytics</h2>
        <canvas id="statusChart" class="w-full h-64"></canvas>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-black bg-opacity-50">
    <div class="bg-white rounded-lg p-5 w-11/12 md:w-96 h-auto md:h-3/4 overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">Edit Patient</h2>
        <form id="editForm" method="POST" action="patients.php" class="flex flex-col">
            <input type="hidden" name="patient_id" id="patient_id">

            <div class="mb-4">
                <label>Name:</label>
                <input type="text" name="name" id="name" class="border rounded w-full p-2" required>
            </div>
            <div class="mb-4">
                <label>Gender:</label>
                <input type="text" name="gender" id="gender" class="border rounded w-full p-2" required>
            </div>
            <div class="mb-4">
                <label>Age:</label>
                <input type="number" name="age" id="age" class="border rounded w-full p-2" required>
            </div>
            <div class="mb-4">
                <label>Date of Birth:</label>
                <input type="date" name="dob" id="dob" class="border rounded w-full p-2" required>
            </div>
            <div class="mb-4">
                <label>Status:</label>
                <input type="text" name="status" id="status" class="border rounded w-full p-2" required>
            </div>
            <div class="mb-4">
                <label>Room ID:</label>
                <select name="room_id" class="border rounded w-full p-2" required>
                    <?php if (!empty($rooms)): ?>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?= $room['room_id'] ?>"><?= htmlspecialchars($room['room_number']) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No rooms available</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-4">
                <label>Doctor:</label>
                <select name="doctor_id" class="border rounded w-full p-2" required>
                    <?php if (!empty($doctors)): ?>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['user_id'] ?>"><?= htmlspecialchars($doctor['username']) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No doctors found</option>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit" name="update" class="bg-blue-500 text-white rounded-md p-2">Update</button>
            <button type="button" class="mt-4 text-gray-500" onclick="closeEditModal()">Cancel</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx, {
        type: 'bar', // Change to your preferred chart type
        data: {
            labels: <?= json_encode($labels) ?>, // Status labels
            datasets: [{
                label: 'Number of Patients by Status',
                data: <?= json_encode($values) ?>, // Status counts
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<script>
    function openEditModal(patientId, name, gender, age, dob, status) {
        document.getElementById('patient_id').value = patientId;
        document.getElementById('name').value = name;
        document.getElementById('gender').value = gender;
        document.getElementById('age').value = age;
        document.getElementById('dob').value = dob;
        document.getElementById('status').value = status;

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function confirmDelete(patientId) {
        if (confirm('Are you sure you want to delete this patient? This action cannot be undone.')) {
            var form = document.createElement('form');
            form.method = 'POST';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'patient_id';
            input.value = patientId;
            form.appendChild(input);
            var deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete';
            form.appendChild(deleteInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>