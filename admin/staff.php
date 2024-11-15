<?php
include 'header.php';
include 'sidebar.php';
include 'db.php';

// Initialize an empty array for users
$users = [];

// Handle filtering by role
$selectedRoleId = isset($_POST['role_filter']) ? $_POST['role_filter'] : '';

// Fetch users with their corresponding roles, applying filter if needed
$query = "
    SELECT u.*, r.role_name
    FROM users u
    JOIN roles r ON u.role_id = r.role_id
";
if ($selectedRoleId) {
    $query .= " WHERE u.role_id = :role_id";
}

$stmt = $pdo->prepare($query);

// Bind the selected role ID if it's set
if ($selectedRoleId) {
    $stmt->bindParam(':role_id', $selectedRoleId, PDO::PARAM_INT);
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all roles for the filter dropdown
$roleQuery = "SELECT * FROM roles";
$roleStmt = $pdo->prepare($roleQuery);
$roleStmt->execute();
$roles = $roleStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle the update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $roleId = $_POST['role_id'];
    $dob = $_POST['dob'];
    $phoneNumber = $_POST['phone_number'];
    $gender = $_POST['gender'];

    // Update the user record
    $updateQuery = "UPDATE users SET username = ?, role_id = ?, dob = ?, phone_number = ?, gender = ? WHERE user_id = ?";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([$username, $roleId, $dob, $phoneNumber, $gender, $userId]);

    // Redirect to the same page to see the updated list
    echo '<p>User updated successfully. You will be redirected shortly.</p>';
    echo '<meta http-equiv="refresh" content="3;url=staff.php">';
    exit;
}

// Handle the delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $userIdToDelete = $_POST['user_id'];

    // Delete the user record
    $deleteQuery = "DELETE FROM users WHERE user_id = ?";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute([$userIdToDelete]);

    // Redirect to the same page to see the updated list
    echo '<p>User deleted successfully. You will be redirected shortly.</p>';
    echo '<meta http-equiv="refresh" content="3;url=staff.php">';
    exit;
}
?>
<div class="flex">
    <div class="flex-1 p-6 bg-white shadow-lg rounded-md mt-20 ml-40 mr-10">
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">List of Staff</h2>

        <!-- Role Filter -->
        <form method="POST" class="mb-4">
            <label for="role_filter" class="mr-2">Filter by Role:</label>
            <select name="role_filter" id="role_filter" class="border rounded p-2">
                <option value="">All Roles</option>
                <?php foreach ($roles as $role) : ?>
                    <option value="<?= htmlspecialchars($role['role_id'], ENT_QUOTES) ?>" <?= ($selectedRoleId == $role['role_id']) ? 'selected' : '' ?>><?= htmlspecialchars($role['role_name'], ENT_QUOTES) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="bg-blue-500 text-white rounded p-2 hover:bg-blue-700">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-left text-sm leading-normal">
                        <th class="py-3 px-6">Username</th>
                        <th class="py-3 px-6">Role</th>
                        <th class="py-3 px-6">Date of Birth</th>
                        <th class="py-3 px-6">Phone Number</th>
                        <th class="py-3 px-6">Gender</th>
                        <th class="py-3 px-6">Action</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="text-gray-600 text-sm font-light">
                    <?php foreach ($users as $user) : ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6"><?= htmlspecialchars($user['username']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($user['role_name']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($user['dob']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($user['phone_number']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($user['gender']) ?></td>
                            <td class="py-3 px-6 flex space-x-2">
                                <button onclick="openEditModal(<?= htmlspecialchars($user['user_id']) ?>, '<?= htmlspecialchars($user['username']) ?>', <?= htmlspecialchars($user['role_id']) ?>, '<?= htmlspecialchars($user['dob']) ?>', '<?= htmlspecialchars($user['phone_number']) ?>', '<?= htmlspecialchars($user['gender']) ?>')" class="px-2 py-2 bg-white text-white rounded-md hover:bg-blue-400">
                                    <!-- Edit icon SVG -->
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 50 50" class="h-5 w-5 mr-1">
                                        <path d="M 43.125 2 C 41.878906 2 40.636719 2.488281 39.6875 3.4375 L 38.875 4.25 L 45.75 11.125 C 45.746094 11.128906 46.5625 10.3125 46.5625 10.3125 C 48.464844 8.410156 48.460938 5.335938 46.5625 3.4375 C 45.609375 2.488281 44.371094 2 43.125 2 Z M 37.34375 6.03125 C 37.117188 6.0625 36.90625 6.175781 36.75 6.34375 L 4.3125 38.8125 C 4.183594 38.929688 4.085938 39.082031 4.03125 39.25 L 2.03125 46.75 C 1.941406 47.09375 2.042969 47.457031 2.292969 47.707031 C 2.542969 47.957031 2.90625 48.058594 3.25 47.96875 L 10.75 45.96875 C 10.917969 45.914063 11.070313 45.816406 11.1875 45.6875 L 43.65625 13.25 C 44.054688 12.863281 44.058594 12.226563 43.671875 11.828125 C 43.285156 11.429688 42.648438 11.425781 42.25 11.8125 L 9.96875 44.09375 L 5.90625 40.03125 L 38.1875 7.75 C 38.488281 7.460938 38.578125 7.011719 38.410156 6.628906 C 38.242188 6.246094 37.855469 6.007813 37.4375 6.03125 C 37.40625 6.03125 37.375 6.03125 37.34375 6.03125 Z"></path>
                                    </svg>
                                </button>
                                <button onclick="confirmDelete(<?= htmlspecialchars($user['user_id']) ?>)" class="px-2 py-2 bg-white text-white rounded-md hover:bg-red-600">
                                    <!-- Delete icon SVG -->
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 24 24" class="h-5 w-5 mr-1">
                                        <path d="M 10 2 L 9 3 L 5 3 C 4.4 3 4 3.4 4 4 C 4 4.6 4.4 5 5 5 L 7 5 L 17 5 L 19 5 C 19.6 5 20 4.6 20 4 C 20 3.4 19.6 3 19 3 L 15 3 L 14 2 L 10 2 z M 5 7 L 5 20 C 5 21.1 5.9 22 7 22 L 17 22 C 18.1 22 19 21.1 19 20 L 19 7 L 5 7 z M 9 9 C 9.6 9 10 9.4 10 10 L 10 19 C 10 19.6 9.6 20 9 20 C 8.4 20 8 19.6 8 19 L 8 10 C 8 9.4 8.4 9 9 9 z M 15 9 C 15.6 9 16 9.4 16 10 L 16 19 C 16 19.6 15.6 20 15 20 C 14.4 20 14 19.6 14 19 L 14 10 C 14 9.4 14.4 9 15 9 z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Edit Staff</h2>
        <form id="editUserForm" method="POST">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="mb-4">
                <label for="edit_username" class="block text-gray-700">Username</label>
                <input type="text" name="username" id="edit_username" class="border rounded w-full py-2 px-3" required>
            </div>
            <div class="mb-4">
                <label for="edit_role_id" class="block text-gray-700">Role</label>
                <select name="role_id" id="edit_role_id" class="border rounded w-full py-2 px-3" required>
                    <?php foreach ($roles as $role) : ?>
                        <option value="<?= htmlspecialchars($role['role_id'], ENT_QUOTES) ?>"><?= htmlspecialchars($role['role_name'], ENT_QUOTES) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="edit_dob" class="block text-gray-700">Date of Birth</label>
                <input type="date" name="dob" id="edit_dob" class="border rounded w-full py-2 px-3" required>
            </div>
            <div class="mb-4">
                <label for="edit_phone_number" class="block text-gray-700">Phone Number</label>
                <input type="text" name="phone_number" id="edit_phone_number" class="border rounded w-full py-2 px-3" required>
            </div>
            <div class="mb-4">
                <label for="edit_gender" class="block text-gray-700">Gender</label>
                <select name="gender" id="edit_gender" class="border rounded w-full py-2 px-3" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <button type="submit" name="update" class="bg-blue-500 text-white rounded py-2 px-4 hover:bg-blue-700">Update User</button>
            <button type="button" onclick="closeEditModal()" class="bg-gray-300 text-gray-700 rounded py-2 px-4 hover:bg-gray-400">Cancel</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(userId, username, roleId, dob, phoneNumber, gender) {
        document.getElementById('edit_user_id').value = userId;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_role_id').value = roleId;
        document.getElementById('edit_dob').value = dob;
        document.getElementById('edit_phone_number').value = phoneNumber;
        document.getElementById('edit_gender').value = gender;

        document.getElementById('editUserModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editUserModal').classList.add('hidden');
    }

    function confirmDelete(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            document.getElementById('editUserForm').action = 'staff.php'; // Ensure the action is correct
            const deleteForm = document.createElement('form');
            deleteForm.method = 'POST';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_id';
            input.value = userId;
            deleteForm.appendChild(input);
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete';
            deleteForm.appendChild(deleteInput);
            document.body.appendChild(deleteForm);
            deleteForm.submit();
        }
    }
</script>
