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

// SQL query to fetch room data
$room_query = "
    SELECT 
        room_id, 
        room_number, 
        room_type, 
        is_empty 
    FROM rooms
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
?>

<!-- Main Content -->
<div class="ml-40 p-10">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">List of Rooms</h2>
        </div>

        <!-- Room Data Table -->
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Room ID</th>
                    <th class="py-3 px-6 text-left">Room Number</th>
                    <th class="py-3 px-6 text-left">Room Type</th>
                    <th class="py-3 px-6 text-left">Availability</th>
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
                                <?= $room['is_empty'] ? 'Available' : 'Occupied' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-3">No rooms available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
