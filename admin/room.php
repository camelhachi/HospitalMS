<?php
include 'header.php';
include 'sidebar.php';

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospital_management_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch room data along with patient count
$room_query = "
    SELECT 
        r.room_id, 
        r.room_number, 
        r.room_type, 
        r.is_empty,
        r.price,
        COUNT(p.patient_id) AS patient_count
    FROM rooms r
    LEFT JOIN patients p ON r.room_id = p.room_id
    GROUP BY r.room_id, r.room_number, r.room_type, r.is_empty
";

$room_result = $conn->query($room_query);
$room_data = [];

// Populate $room_data if the query has results
if ($room_result->num_rows > 0) {
    while ($row = $room_result->fetch_assoc()) {
        $room_data[] = $row;
    }
}
$conn->close();

$room_numbers = [];
$patient_counts = [];

// Prepare data for chart
foreach ($room_data as $room) {
    $room_numbers[] = $room['room_number']; // Room numbers for labels
    $patient_counts[] = $room['patient_count']; // Patient counts for chart data
}

?>

<!-- Main Content -->
<div class="ml-40 mr-10 p-10">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">List of Rooms</h2>
            <ul class="list-disc ml-6">
                <li class="text-l font-bold text-gray-500">Room A: Rp. 1.000.000</li>
                <li class="text-l font-bold text-gray-500">Room B: Rp. 2.000.000</li>
                <li class="text-l font-bold text-gray-500">Room C: Rp. 3.000.000</li>
            </ul>
        </div>

        <!-- Room Data Table -->
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Room ID</th>
                    <th class="py-3 px-6 text-left">Room Number</th>
                    <th class="py-3 px-6 text-left">Room Type</th>
                    <th class="py-3 px-6 text-left">Availability</th>
                    <th class="py-3 px-6 text-left">Patient Count</th> <!-- New Column -->
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php if (!empty($room_data)): ?>
                    <?php foreach ($room_data as $room): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6"><?= $room['room_id'] ?></td>
                            <td class="py-3 px-6"><?= $room['room_number'] ?></td>
                            <td class="py-3 px-6"><?= $room['room_type'] ?></td>
                            <td class="py-3 px-6">
                                <?= $room['patient_count'] > 0 ? 'Occupied' : 'Available' ?>
                            </td>
                            <td class="py-3 px-6"><?= $room['patient_count'] ?></td> <!-- Patient Count Display -->
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-3">No rooms available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Chart Container -->
        <div class="mt-6">
            <canvas id="roomStatisticsChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('roomStatisticsChart').getContext('2d');
    const roomStatisticsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($room_numbers) ?>, // Room numbers
            datasets: [{
                label: 'Patient Count per Room',
                data: <?= json_encode($patient_counts) ?>, // Patient counts
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Patients'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Room Number'
                    }
                }
            }
        }
    });
</script>