<?php
include 'db.php';
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] == 'staff') {
                $message = "<div class='alert alert-success'>Welcome Staff! Redirecting...</div>";
                header("Refresh: 1; url=index.php"); 
            } else {
                $message = "<div class='alert alert-success'>Welcome Customer! Redirecting...</div>";
                header("Refresh: 1; url=index.php");
            }
        } else {
            $message = "<div class='alert alert-danger'>Incorrect password!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>No account found with that email!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semara Hotel - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-dark">Semara Hotel</h2>
                    <p class="text-muted">Restaurant Management Portal</p>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">Sign In</h4>
                        
                        <?php echo $message; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="name@example.com" required>
                            </div>
                            <div class="mb-3">
    <label class="form-label text-secondary small fw-bold">Password</label>
    <input type="password" name="password" id="loginPassword" class="form-control form-control-lg fs-6" placeholder="••••••••" required>
</div>

<div class="mb-4 form-check text-start">
    <input type="checkbox" class="form-check-input" id="toggleLoginPassword" onclick="showLoginPass()">
    <label class="form-check-label text-muted small user-select-none" for="toggleLoginPassword">Show Password</label>
</div>

                            <button type="submit" class="btn btn-dark btn-lg w-100 fs-6 fw-bold shadow-sm py-2">Login</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="small text-muted mb-0">Don't have an account? <a href="signup.php" class="text-decoration-none fw-bold text-dark">Sign up here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
function showLoginPass() {
    var passField = document.getElementById("loginPassword");
    if (passField.type === "password") {
        passField.type = "text";
    } else {
        passField.type = "password";
    }
}
</script>
</body>
</html>