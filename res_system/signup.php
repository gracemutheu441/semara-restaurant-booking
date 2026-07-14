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
        // Notice 'customer' is directly forced into the SQL query below
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$full_name', '$email', '$hashed_password', 'customer')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>Registration successful! <a href='login.php' class='fw-bold text-success text-decoration-underline'>Login here</a></div>";
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
    <title>Semara Hotel - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-dark">Semara Hotel</h2>
                    <p class="text-muted">Guest Registration Portal</p>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">Create Guest Account</h4>
                        
                        <?php echo $message; ?>

                        <form action="signup.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Full Name</label>
                                <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Password</label>
                                <input type="password" name="password" id="signupPassword" class="form-control" placeholder="••••••••" required>
                            </div>

                            <div class="mb-4 form-check text-start">
                                <input type="checkbox" class="form-check-input" id="toggleSignupPassword" onclick="showSignupPass()">
                                <label class="form-check-label text-muted small user-select-none" for="toggleSignupPassword">Show Password</label>
                            </div>

                            <button type="submit" class="btn btn-dark btn-lg w-100 fs-6 fw-bold shadow-sm py-2">Register Account</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="small text-muted mb-0">Already have an account? <a href="login.php" class="text-decoration-none fw-bold text-dark">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showSignupPass() {
        var passField = document.getElementById("signupPassword");
        if (passField.type === "password") {
            passField.type = "text";
        } else {
            passField.type = "password";
        }
    }
    </script>
</body>
</html>