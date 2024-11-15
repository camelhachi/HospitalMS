<?php
include 'db.php';
include 'header.php';
include 'sidebar.php';

// Fetch doctors for dropdown in Patient Registration Form (role_id 1)
$stmt = $pdo->prepare("SELECT user_id, username FROM users WHERE role_id = ?");
$stmt->execute([1]); // Assuming role_id 1 is for doctors
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch rooms for dropdown in Patient Registration Form (is_empty = 1)
$stmt = $pdo->prepare("SELECT room_id, room_number FROM rooms WHERE is_empty = 1");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch roles for dropdown in User Registration Form
$stmt = $pdo->prepare("SELECT role_id, role_name FROM roles");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Patient Registration
if (isset($_POST['register_patient'])) {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $phone_number = $_POST['phone_number'];
    $room_id = $_POST['room_id'];
    $doctor_id = $_POST['doctor_id'];
    $status = $_POST['status'];

    $sql = "INSERT INTO patients (name, dob, age, gender, religion, phone_number, room_id, doctor_id, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $dob, $age, $gender, $religion, $phone_number, $room_id, $doctor_id, $status]);

    echo "<script>alert('Patient registered successfully!');</script>";
}

// Handle User Registration
if (isset($_POST['register_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone_number = $_POST['phone_number'];

    $sql = "INSERT INTO users (username, password, role_id, dob, gender, phone_number) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $password, $role_id, $dob, $gender, $phone_number]);

    echo "<script>alert('User registered successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Registration Page</title>
    <style>
        /* Container and Button Styling */
        .form-container {
            background-color: #d8eaff;
            padding: 20px;
            /* This adds padding all around */
            padding-top: 40px;
            /* Increase the top padding */
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .form-container h2 {
            text-align: center;
        }

        .form-container form {
            display: grid;
            gap: 10px;
        }

        .form-container input,
        .form-container select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
        }

        .form-container button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container button:hover {
            background-color: #218838;
        }

        .toggle-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
            margin-top: 50px;
        }

        .toggle-buttons button {
            margin: 0 10px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(135deg, #4a90e2, #007aff);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .toggle-buttons button:hover {
            background: linear-gradient(135deg, #007aff, #4a90e2);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .toggle-buttons button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.4);
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>

    <div class="toggle-buttons">
        <button onclick="showForm('patient')">Patient Registration</button>
        <button onclick="showForm('user')">Staff Registration</button>
    </div>

    <div class="form-container">
        <!-- Patient Registration Form -->
        <div id="patient-form" class="form-section">
            <h2>Patient Registration</h2>
            <form method="POST">
                <input type="hidden" name="register_patient">
                <label>Name</label>
                <input type="text" name="name" required>

                <label>Date of Birth</label>
                <input type="date" name="dob" required>

                <label>Age</label>
                <input type="number" name="age" required>

                <label>Gender</label>
                <select name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <label>Religion</label>
                <input type="text" name="religion">

                <label>Phone Number</label>
                <input type="text" name="phone_number">

                <label>Room</label>
                <select name="room_id" required>
                    <?php if (!empty($rooms)): ?>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?= $room['room_id'] ?>"><?= htmlspecialchars($room['room_number']) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No rooms available</option>
                    <?php endif; ?>
                </select>

                <label>Doctor</label>
                <select name="doctor_id" required>
                    <?php if (!empty($doctors)): ?>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['user_id'] ?>"><?= htmlspecialchars($doctor['username']) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No doctors found</option>
                    <?php endif; ?>
                </select>

                <label>Status</label>
                <select name="status" required>
                    <option value="Admitted">Admitted</option>
                    <option value="Discharged">Discharged</option>
                </select>

                <button type="submit">Submit</button>
            </form>
        </div>

        <!-- User Registration Form -->
        <div id="user-form" class="form-section hidden">
            <h2>Staff Registration</h2>
            <form method="POST">
                <input type="hidden" name="register_user">
                <label>Username</label>
                <input type="text" name="username" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <label>Date of Birth</label>
                <input type="date" name="dob" required>

                <label>Gender</label>
                <select name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <label>Phone Number</label>
                <input type="text" name="phone_number" required>

                <label>Role</label>
                <select name="role_id" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['role_id'] ?>"><?= $role['role_name'] ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        // Function to show the selected form and hide the other
        function showForm(formType) {
            document.getElementById('patient-form').classList.add('hidden');
            document.getElementById('user-form').classList.add('hidden');

            if (formType === 'patient') {
                document.getElementById('patient-form').classList.remove('hidden');
            } else if (formType === 'user') {
                document.getElementById('user-form').classList.remove('hidden');
            }
        }
    </script>

</body>

</html>
