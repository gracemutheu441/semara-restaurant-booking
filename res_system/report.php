<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';
session_start();

// Guard Clause: Only staff can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'staff') {
    die("Access Denied: Only Semara Hotel Staff can view reservation reports. <a href='login.php'>Login here</a>");
}

// Handle Status Changes when buttons are clicked
if (isset($_GET['action']) && isset($_GET['id'])) {
    $reservation_id = mysqli_real_escape_string($conn, $_GET['id']);
    $action = $_GET['action'];
    
    $new_status = "";
    if ($action === 'confirm') {
        $new_status = 'Confirmed';
    } elseif ($action === 'cancel') {
        $new_status = 'Cancelled';
    } elseif ($action === 'reset') {
        $new_status = 'Pending';
    }

    if (!empty($new_status)) {
        $update_sql = "UPDATE reservations SET status = '$new_status' WHERE reservation_id = '$reservation_id'";
        $conn->query($update_sql);
        
        // Refresh the page cleanly to show the change
        header("Location: report.php");
        exit();
    }
}

// Fetch all reservations from the database
$sql = "SELECT r.reservation_id, u.full_name AS customer_name, t.table_number, r.reservation_date, r.reservation_time, r.status 
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN restaurant_tables t ON r.table_id = t.table_id
        ORDER BY r.reservation_date DESC, r.reservation_time DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semara Hotel - Staff Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">🏨 Semara Hotel Dashboard</a>
            <span class="navbar-text text-white small">
                <a href="menu_manage.php" class="text-warning me-3 text-decoration-none fw-bold">Manage Menu</a> | 
                <a href="logout.php" class="text-danger text-decoration-none fw-bold">Logout</a>
            </span>
        </div>
    </nav>

    <div class="container my-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-0">Live Reservation Report</h3>
                        <p class="text-muted small mb-0">Review and change status codes easily.</p>
                    </div>
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">Back Home</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle border-top">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Table</th>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Status</th>
                                <th class="text-center">Quick Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0) { 
                                while($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><span class="text-secondary fw-bold">#<?php echo $row['reservation_id']; ?></span></td>
                                        <td class="fw-semibold text-dark"><?php echo $row['customer_name']; ?></td>
                                        <td><span class="badge bg-secondary">Table <?php echo $row['table_number']; ?></span></td>
                                        <td><?php echo $row['reservation_date']; ?></td>
                                        <td><?php echo $row['reservation_time']; ?></td>
                                        
                                        <td>
                                            <?php 
                                            $status = $row['status'];
                                            if ($status === 'Confirmed') {
                                                echo '<span class="badge bg-success px-3 py-2 rounded-pill small fw-bold">Confirmed</span>';
                                            } elseif ($status === 'Cancelled') {
                                                echo '<span class="badge bg-danger px-3 py-2 rounded-pill small fw-bold">Cancelled</span>';
                                            } else {
                                                echo '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill small fw-bold">Pending</span>';
                                            }
                                            ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if ($status === 'Pending') { ?>
                                                <a href="report.php?action=confirm&id=<?php echo $row['reservation_id']; ?>" class="btn btn-sm btn-success fw-bold me-1">Confirm</a>
                                                <a href="report.php?action=cancel&id=<?php echo $row['reservation_id']; ?>" class="btn btn-sm btn-danger fw-bold">Cancel</a>
                                            <?php } else { ?>
                                                <a href="report.php?action=reset&id=<?php echo $row['reservation_id']; ?>" class="btn btn-sm btn-outline-secondary btn-xs">Undo / Reset</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } 
                            } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No reservations logged in system yet.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>