<?php
include('includes/header.php');
include('session.php');
include_once('db.php');

// Function to get all active users
function getAllUsers() {
    global $conn;
    $sql = "SELECT * FROM users WHERE is_deleted = 0";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get deleted users
function getDeletedUsers() {
    global $conn;
    $sql = "SELECT * FROM users WHERE is_deleted = 1";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Soft delete user
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $current_timestamp = date('Y-m-d H:i:s');
    
    $sql = "UPDATE users SET is_deleted = 1, deleted_at = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $current_timestamp, $user_id);
    
    try {
        $stmt->execute();
        $success_message = "User successfully deactivated.";
    } catch (Exception $e) {
        $error_message = "Error deactivating user: " . $e->getMessage();
    }
    $stmt->close();
}

// Update user
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ? AND is_deleted = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $email, $role, $user_id);
    
    try {
        $stmt->execute();
        $success_message = "User successfully updated.";
    } catch (Exception $e) {
        $error_message = "Error updating user: " . $e->getMessage();
    }
    $stmt->close();
}

// Restore user function
if (isset($_POST['restore_user'])) {
    $user_id = $_POST['user_id'];
    
    $sql = "UPDATE users SET is_deleted = 0, deleted_at = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    try {
        $stmt->execute();
        $success_message = "User successfully restored.";
    } catch (Exception $e) {
        $error_message = "Error restoring user: " . $e->getMessage();
    }
    $stmt->close();
}

$users = getAllUsers();
$deleted_users = getDeletedUsers();
?>

<div class="dashboard-container">
    <?php include('includes/sidebar.php'); ?>
    <main>
        <div class="dashboard-content">
            <h1>User Management</h1>
            
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <h2>Active Users</h2>
            <div class="user-table">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <button onclick="openUpdateModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo htmlspecialchars($user['role']); ?>')">Update</button>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to deactivate this user?')">Deactivate</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <?php if (!empty($deleted_users)): ?>
                <h2>Deactivated Users</h2>
                <div class="user-table">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Deactivated At</th>
                            <th>Action</th>
                        </tr>
                        <?php foreach ($deleted_users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo $user['deleted_at']; ?></td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="restore_user">Restore</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Update User Modal -->
<div id="updateModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Update User</h2>
        <form id="updateForm" method="post">
            <input type="hidden" id="update_user_id" name="user_id">
            <label for="update_username">Username:</label>
            <input type="text" id="update_username" name="username" required>
            
            <label for="update_email">Email:</label>
            <input type="email" id="update_email" name="email" required>
            
            <label for="update_role">Role:</label>
            <select id="update_role" name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            
            <button type="submit" name="update_user">Update User</button>
        </form>
    </div>
</div>

<script>
    var modal = document.getElementById("updateModal");
    var span = document.getElementsByClassName("close")[0];

    function openUpdateModal(id, username, email, role) {
        document.getElementById("update_user_id").value = id;
        document.getElementById("update_username").value = username;
        document.getElementById("update_email").value = email;
        document.getElementById("update_role").value = role;
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php include('includes/footer.php'); ?>