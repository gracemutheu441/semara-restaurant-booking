<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'staff') {
    die("Access Denied: Only Semara Hotel Staff can manage the digital menu. <a href='login.php'>Login here</a>");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    $sql = "INSERT INTO menus (item_name, description, price, is_available) VALUES ('$item_name', '$description', '$price', 1)";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Menu item added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

if (isset($_GET['toggle_id']) && isset($_GET['current_status'])) {
    $item_id = mysqli_real_escape_string($conn, $_GET['toggle_id']);
    $new_status = ($_GET['current_status'] == 1) ? 0 : 1;

    $update_sql = "UPDATE menus SET is_available = '$new_status' WHERE item_id = '$item_id'";
    $conn->query($update_sql);
    header("Location: menu_manage.php");
    exit();
}

$menu_sql = "SELECT * FROM menus ORDER BY item_name ASC";
$menu_result = $conn->query($menu_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semara Hotel - Manage Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">🏨 Semara Menu Engine</a>
            <span class="navbar-text text-white small"><a href="report.php" class="text-info me-3 text-decoration-none fw-bold">View Reports</a> | <a href="logout.php" class="text-danger text-decoration-none fw-bold">Logout</a></span>
        </div>
    </nav>

    <div class="container-fluid my-5 px-md-5">
        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-1">Add New Menu Item</h4>
                        <p class="text-muted small">Update restaurant selections in real-time.</p>
                        <hr class="mb-4">
                        
                        <?php echo $message; ?>

                        <form action="menu_manage.php" method="POST">
                            <input type="hidden" name="add_item" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Item Title</label>
                                <input type="text" name="item_name" class="form-control" placeholder="e.g. Grilled Chicken Choma" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Recipe Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Sides, ingredients, sizes..." required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-secondary small fw-bold">Base Price (KES)</label>
                                <input type="number" step="0.01" name="price" class="form-control" placeholder="1200" required>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 fw-bold shadow-sm">Publish to Live App</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-1">Active Digital Menu Items</h4>
                        <p class="text-muted small">Current menu visibility status configuration.</p>
                        
                        <div class="table-responsive mt-4">
                            <table class="table table-hover align-middle border-top">
                                <thead class="table-light">
                                    <tr>
                                        <th>Dish Name</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Availability</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($menu_result->num_rows > 0) { 
                                        while($item = $menu_result->fetch_assoc()) { ?>
                                            <tr>
                                                <td class="fw-bold text-dark"><?php echo $item['item_name']; ?></td>
                                                <td class="text-secondary small"><?php echo $item['description']; ?></td>
                                                <td class="fw-semibold text-dark">KES <?php echo number_format($item['price'], 2); ?></td>
                                                <td>
                                                    <?php if ($item['is_available'] == 1) { ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">In-Stock</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Out-of-Stock</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <a class="btn btn-xs btn-outline-dark fs-7 fw-bold py-1" href="menu_manage.php?toggle_id=<?php echo $item['item_id']; ?>&current_status=<?php echo $item['is_available']; ?>">
                                                        🔄 Toggle
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } 
                                    } else { ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No dishes loaded into the database yet.</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>