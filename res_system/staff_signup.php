<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check_email = "SELECT email FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);

    if ($result->num_rows > 0) {
        $message = "<div class='alert alert-danger'>An account with this email already exists!</div>";
    } else {
        // Notice 'staff' is forced directly into the SQL query below
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$full_name', '$email', '$hashed_password', 'staff')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>Internal Staff Account Registered Successfully! <a href='login.php' class='fw-bold text-success text-decoration-underline'>Test Login</a></div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semara Hotel - Internal Staff Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height: 100vh;"> <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-white">Semara Hotel</h2>
                    <p class="text-light opacity-75">Internal Employee Onboarding Portal</p>
                </div>

                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body p-4">
                        <div class="alert alert-warning py-2 small fw-bold">⚠️ Warning: Authorised Administrative Use Only.</div>
                        <h4 class="fw-bold mb-3">Register New Employee</h4>
                        
                        <?php echo $message; ?>

                        <form action="staff_register.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Employee Full Name</label>
                                <input type="text" name="full_name" class="form-control" placeholder="Jane Doe" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Official Work Email</label>
                                <input type="email" name="email" class="form-control" placeholder="employee@semarahotel.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Assign Temporary Password</label>
                                <input type="password" name="password" id="staffPassword" class="form-control" placeholder="••••••••" required>
                            </div>

                            <div class="mb-4 form-check text-start">
                                <input type="checkbox" class="form-check-input" id="toggleStaffPassword" onclick="showStaffPass()">
                                <label class="form-check-label text-muted small user-select-none" for="toggleStaffPassword">Show Password</label>
                            </div>

                            <button type="submit" class="btn btn-warning btn-lg w-100 fs-6 fw-bold shadow-sm py-2 text-dark">Create Employee Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showStaffPass() {
        var passField = document.getElementById("staffPassword");
        if (passField.type === "password") {
            passField.type = "text";
        } else {
            passField.type = "password";
        }
    }
    </script>
</body>
</html>