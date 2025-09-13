<?php
include 'config.php';

function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (isset($_POST['add_user'])) {
    $username = validate_input($_POST['username']);
    $email = validate_input($_POST['email']);
    $password = validate_input($_POST['password']); // Removed password_hash
    $phonenumber = validate_input($_POST['phonenumber']);
    $gender = validate_input($_POST['gender']);
    $address = validate_input($_POST['address']);
    
    $check_sql = "SELECT * FROM signup WHERE email=?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check_result = $stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $message = "Email already exists!";
        $alert_class = "alert-danger";
    } else {
        $insert_sql = "INSERT INTO signup (username, email, password, phonenumber, gender, address, user_type) VALUES (?, ?, ?, ?, ?, ?, 2)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssssss", $username, $email, $password, $phonenumber, $gender, $address);
        
        if ($stmt->execute()) {
            $message = "User added successfully!";
            $alert_class = "alert-success";
        } else {
            $message = "Error: " . $conn->error;
            $alert_class = "alert-danger";
        }
    }
}

if (isset($_POST['edit_user'])) {
    $id = validate_input($_POST['user_id']);
    $username = validate_input($_POST['username']);
    $email = validate_input($_POST['email']);
    $phonenumber = validate_input($_POST['phonenumber']);
    $gender = validate_input($_POST['gender']);
    $address = validate_input($_POST['address']);
    
    if (!empty($_POST['password'])) {
        $password = validate_input($_POST['password']); // Removed password_hash
        $update_sql = "UPDATE signup SET username=?, email=?, password=?, phonenumber=?, gender=?, address=? WHERE id=? AND user_type=2";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssssi", $username, $email, $password, $phonenumber, $gender, $address, $id);
    } else {
        $update_sql = "UPDATE signup SET username=?, email=?, phonenumber=?, gender=?, address=? WHERE id=? AND user_type=2";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssi", $username, $email, $phonenumber, $gender, $address, $id);
    }
    
    if ($stmt->execute()) {
        $message = "User updated successfully!";
        $alert_class = "alert-success";
    } else {
        $message = "Error updating user: " . $conn->error;
        $alert_class = "alert-danger";
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = validate_input($_GET['delete_id']);
    $delete_sql = "DELETE FROM signup WHERE id=? AND user_type=2";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $message = "User deleted successfully!";
        $alert_class = "alert-success";
    } else {
        $message = "Error deleting user: " . $conn->error;
        $alert_class = "alert-danger";
    }
}

$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = validate_input($_GET['edit_id']);
    $edit_sql = "SELECT * FROM signup WHERE id=? AND user_type=2";
    $stmt = $conn->prepare($edit_sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    
    if ($edit_result->num_rows > 0) {
        $edit_data = $edit_result->fetch_assoc();
    }
}

$sql = "SELECT * FROM signup WHERE user_type=2 ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Users | Meta Ticket Exchange</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --sidebar-width: 280px;
        --topbar-height: 64px;
        --primary-color: #1a1f2b;
        --secondary-color: #252b3b;
        --accent-color: #6366f1;
        --text-light: #ffffff;
        --card-bg: #ffffff;
        --transition-speed: 0.3s;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        overflow-x: hidden;
    }

    .sidebar {
        position: fixed;
        width: var(--sidebar-width);
        height: 100vh;
        background: var(--primary-color);
        color: var(--text-light);
        transition: transform var(--transition-speed) ease;
        z-index: 1000;
        overflow-y: auto;
    }

    @media (max-width: 991px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }
    }

    .sidebar-header {
        padding: 20px;
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-logo img {
        width: 40px;
        border-radius: 8px;
        transition: transform var(--transition-speed);
    }

    .sidebar-logo img:hover {
        transform: scale(1.1);
    }

    .sidebar-menu {
        padding: 15px 0;
        list-style: none;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 25px;
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        transition: all var(--transition-speed);
    }

    .sidebar-link:hover,
    .sidebar-link.active {
        background: var(--secondary-color);
        color: var(--accent-color);
        padding-left: 30px;
    }

    .sidebar-link i {
        width: 24px;
        margin-right: 15px;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        transition: margin-left var(--transition-speed);
        min-height: 100vh;
    }

    @media (max-width: 991px) {
        .main-content {
            margin-left: 0;
        }
    }

    .topbar {
        background: var(--card-bg);
        height: var(--topbar-height);
        padding: 0 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 999;
    }

    .menu-toggle {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--primary-color);
        cursor: pointer;
        transition: transform var(--transition-speed);
        display: none;
    }

    .menu-toggle:hover {
        transform: rotate(90deg);
    }

    @media (max-width: 991px) {
        .menu-toggle {
            display: block;
        }
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-left: auto;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(99, 102, 241, 0.3);
    }

    .filter-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all var(--transition-speed);
    }

    .filter-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .users-table {
        background: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .users-table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .users-table table {
        width: 100%;
        margin-bottom: 0;
        min-width: 700px;
    }

    .users-table th {
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        padding: 15px;
        font-weight: 600;
        color: #374151;
        white-space: nowrap;
        border-bottom: 2px solid #e5e7eb;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        transition: background 0.3s ease;
    }

    .users-table th:hover {
        background: linear-gradient(135deg, #eef2f7 0%, #e5e7eb 100%);
    }

    .users-table th .d-flex {
        justify-content: flex-start;
    }

    .users-table th i {
        font-size: 1.1rem;
        opacity: 0.9;
        transition: transform 0.3s ease;
    }

    .users-table th:hover i {
        transform: scale(1.15);
    }

    .users-table td {
        padding: 15px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.7rem 1rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        border-color: var(--accent-color);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.7rem 1.2rem;
        transition: all 0.3s;
    }

    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background: var(--primary-color);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px;
    }

    .modal-body {
        padding: 25px;
    }

    .modal-footer {
        padding: 15px 25px;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
    }

    @media (max-width: 768px) {
        .filter-card {
            padding: 15px;
        }

        .users-table th,
        .users-table td {
            padding: 10px;
            font-size: 0.85rem;
        }

        .form-control,
        .form-select {
            padding: 0.6rem 0.8rem;
        }

        .btn {
            padding: 0.6rem 1rem;
        }
    }

    @media (max-width: 576px) {
        .filter-card {
            padding: 10px;
        }

        .users-table th,
        .users-table td {
            padding: 8px;
            font-size: 0.75rem;
        }

        .form-control,
        .form-select {
            padding: 0.5rem 0.7rem;
            font-size: 0.85rem;
        }

        .btn {
            padding: 0.5rem 0.8rem;
            font-size: 0.85rem;
        }

        .modal-body {
            padding: 15px;
        }
    }

    .page-title {
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 1.8rem;
        font-size: 2.2rem;
        letter-spacing: -0.5px;
        position: relative;
        display: inline-block;
    }

    .page-title:after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--accent-color);
        border-radius: 2px;
    }
    </style>
</head>

<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="assets/images/logo.png" alt="MetaTicket Logo">
                <h2 style="font-size: 1.5rem; font-weight: 600;">MetaTicket</h2>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_home.php" class="sidebar-link"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="admin_bookings.php" class="sidebar-link"><i class="fas fa-ticket-alt"></i> Bookings</a></li>
            <li><a href="manage_users.php" class="sidebar-link active"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="manage_agency.php" class="sidebar-link"><i class="fas fa-building"></i> Bus Agencies</a></li>
            <li><a href="admin_wallet.php" class="sidebar-link"><i class="fas fa-wallet"></i> Wallet</a></li>
            <li><a href="index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
        </ul>
    </aside>

    <div class="main-content" id="main-content">
        <div class="topbar">
            <div class="user-profile ms-auto">
                <div class="user-avatar">A</div>
                <span class="d-none d-md-inline fw-medium">Admin</span>
                <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
            </div>
        </div>

        <div class="container-fluid py-4">
            <h1 class="page-title text-center">Manage Users</h1>

            <div class="filter-card">
                <?php if (isset($message)): ?>
                <div class="alert <?php echo $alert_class; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                <div class="row g-3 align-items-center">
                    <div class="col-md-3 col-sm-6">
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                            data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-2"></i>Add New User
                        </button>
                    </div>
                    <div class="col-md-9 col-sm-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control" id="searchInput"
                                placeholder="Search users by username or email...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="users-table">
                <div class="users-table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-hashtag text-primary"></i>
                                        <span>ID</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-user text-primary"></i>
                                        <span>Username</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-envelope text-primary"></i>
                                        <span>Email</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-phone text-primary"></i>
                                        <span>Phone</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-venus-mars text-primary"></i>
                                        <span>Gender</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        <span>Address</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-cog text-primary"></i>
                                        <span>Actions</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phonenumber']); ?></td>
                                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td>
                                    <a href="manage_users.php?edit_id=<?php echo $row['id']; ?>"
                                        class="btn btn-sm btn-outline-success me-2">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No users found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="POST" action="manage_users.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="phonenumber" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phonenumber" name="phonenumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="" selected disabled>Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true"
        <?php echo $edit_data ? 'data-bs-backdrop="static"' : ''; ?>>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="POST" action="manage_users.php">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="<?php echo $edit_data ? $edit_data['id'] : ''; ?>">
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username"
                                value="<?php echo $edit_data ? htmlspecialchars($edit_data['username']) : ''; ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="edit_email" name="email"
                                value="<?php echo $edit_data ? htmlspecialchars($edit_data['email']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Password (Leave blank to keep current)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="edit_phonenumber" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="edit_phonenumber" name="phonenumber"
                                value="<?php echo $edit_data ? htmlspecialchars($edit_data['phonenumber']) : ''; ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_gender" class="form-label">Gender</label>
                            <select class="form-select" id="edit_gender" name="gender" required>
                                <option value="" disabled>Select gender</option>
                                <option value="Male"
                                    <?php echo ($edit_data && $edit_data['gender'] == 'Male') ? 'selected' : ''; ?>>Male
                                </option>
                                <option value="Female"
                                    <?php echo ($edit_data && $edit_data['gender'] == 'Female') ? 'selected' : ''; ?>>
                                    Female</option>
                                <option value="Others"
                                    <?php echo ($edit_data && $edit_data['gender'] == 'Others') ? 'selected' : ''; ?>>
                                    Others</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Address</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="3"
                                required><?php echo $edit_data ? htmlspecialchars($edit_data['address']) : ''; ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menu-toggle');

    menuToggle.addEventListener('click', () => sidebar.classList.toggle('show'));

    document.addEventListener('click', (e) => {
        if (window.innerWidth < 992 && !sidebar.contains(e.target) && !menuToggle.contains(e.target) && sidebar
            .classList.contains('show')) {
            sidebar.classList.remove('show');
        }
    });

    <?php if ($edit_data): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        editModal.show();
    });
    <?php endif; ?>

    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const tableBody = document.getElementById('usersTableBody');
        const rows = tableBody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const username = rows[i].getElementsByTagName('td')[1].textContent.toLowerCase();
            const email = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();

            if (username.includes(searchText) || email.includes(searchText)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    });
    </script>
</body>

</html>

<?php
$conn->close();
?>