<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $table_id = mysqli_real_escape_string($conn, $_POST['table_id']);
    $res_date = mysqli_real_escape_string($conn, $_POST['reservation_date']);
    $res_time = mysqli_real_escape_string($conn, $_POST['reservation_time']);

    $check_query = "SELECT * FROM reservations WHERE table_id = '$table_id' AND reservation_date = '$res_date' AND reservation_time = '$res_time' AND status != 'Cancelled'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Sorry, this table is already reserved for that specific time slot!</div>";
    } else {
        $sql = "INSERT INTO reservations (user_id, table_id, reservation_date, reservation_time, status) VALUES ('$user_id', '$table_id', '$res_date', '$res_time', 'Pending')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>Reservation submitted successfully! Your booking is pending confirmation.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }
}

$tables_sql = "SELECT * FROM restaurant_tables WHERE status = 'Available'";
$tables_result = $conn->query($tables_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semara Hotel - Book a Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">🏨 Semara Hotel Booking</a>
            <span class="navbar-text text-white small">Welcome, <strong><?php echo $_SESSION['user_name']; ?></strong> | <a href="logout.php" class="text-danger ms-2 text-decoration-none fw-bold">Logout</a></span>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-1">Book a Table Online</h3>
                        <p class="text-muted small">Fill in the details below to reserve your space instantly.</p>
                        <hr class="mb-4">

                        <?php echo $message; ?>

                        <form action="reserve.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Select Preferred Table</label>
                                <select name="table_id" class="form-select" required>
                                    <option value="">-- Choose a Table Options --</option>
                                    <?php while($table = $tables_result->fetch_assoc()) { ?>
                                        <option value="<?php echo $table['table_id']; ?>">
                                            Table <?php echo $table['table_number']; ?> (Capacity: <?php echo $table['capacity']; ?> guests)
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Reservation Date</label>
                                <input type="date" name="reservation_date" min="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-secondary small fw-bold">Reservation Time Slot</label>
                                <input type="time" name="reservation_time" class="form-control" required>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="index.php" class="btn btn-outline-secondary w-50">Back to Home</a>
                                <button type="submit" class="btn btn-primary w-50 fw-bold shadow-sm">Confirm Booking</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>