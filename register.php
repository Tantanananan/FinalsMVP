<?php
include 'database.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $password_raw = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Server-side validation
    $isValid = true;
    $errorMsg = "";

    if (strlen(trim($full_name)) < 2) {
        $isValid = false;
        $errorMsg = "Full name is too short.";
    } elseif (strlen($username) < 8 || strlen($username) > 16) {
        $isValid = false;
        $errorMsg = "Username must be between 8 and 16 characters.";
    } elseif (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password_raw)) {
        $isValid = false;
        $errorMsg = "Password must be at least 8 characters, with 1 uppercase, 1 lowercase, and 1 number.";
    } elseif ($password_raw !== $confirm_password) {
        $isValid = false;
        $errorMsg = "Passwords do not match.";
    }

    if ($isValid) {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (username, password, full_name, role) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $mysql->prepare($sql)) {
            $stmt->bind_param("ssss", $username, $password, $full_name, $role);
            if ($stmt->execute()) {
                $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire({title: 'Registration Successful!', text: 'Your account has been created.', icon: 'success', showCancelButton: false, confirmButtonColor: '#11998e', confirmButtonText: 'Yes, Go to Login'}).then((result) => {if (result.isConfirmed) {window.location.href = 'login.php';}});});</script>";
            } else {
                $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire({icon: 'error', title: 'Registration Failed!', text: 'Error: Username might already exist.', confirmButtonColor: '#d33', confirmButtonText: '✖ Try Again'});});</script>";
            }
            $stmt->close();
        } else {
            $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire({icon: 'error', title: 'Database Error', text: 'Could not prepare statement.', confirmButtonColor: '#d33', confirmButtonText: '✖ Try Again'});});</script>";
        }
    } else {
        $message = "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire({icon: 'warning', title: 'Invalid Input', text: '$errorMsg', confirmButtonColor: '#d33', confirmButtonText: '✖ Fix and Try Again'});});</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EquipTrack | Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body, html { height: 100%; margin: 0; overflow-x: hidden; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .gradient-custom { background: #11998e; background: linear-gradient(to right, #38ef7d, #11998e); min-height: 100vh; }
        .card { border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.4); }
        .form-control, .form-select { border: 1px solid rgba(0, 0, 0, 0.2); background-color: transparent !important; color: #000000 !important; transition: border-color 0.3s, box-shadow 0.3s; }
        .form-control:focus, .form-select:focus { border-color: #38ef7d; box-shadow: 0 0 8px rgba(56, 239, 125, 0.4); }
        .input-invalid { border-color: #dc3545 !important; box-shadow: 0 0 8px rgba(220, 53, 69, 0.4) !important; }
        .input-valid { border-color: #198754 !important; box-shadow: 0 0 8px rgba(25, 135, 84, 0.4) !important; }
        .btn-outline-dark:hover { background-color: #38ef7d; border-color: #38ef7d; color: #000; }
        .custom-card-width { max-width: 400px; width: 100%; }
        .password-hint { font-size: 0.75rem; color: #6c757d; margin-top: 4px; }
        .cursor-pointer { cursor: pointer; color: #6c757d; transition: color 0.2s; }
        .cursor-pointer:hover { color: #38ef7d; }
    </style>
</head>
<body>

<section class="gradient-custom d-flex align-items-center py-5">
  <div class="container d-flex justify-content-center">
    <div class="card text-dark custom-card-width bg-white" style="border-radius: 1rem;">
      <div class="card-body p-4 p-md-5">

        <div class="text-center">
            <h2 class="fw-bold mb-2 text-uppercase text-success">Create Account</h2>
            <p class="text-dark-50 mb-4">Register new member for EquipTrack</p>
        </div>

        <?php echo $message; ?>

        <form method="POST" id="registrationForm" novalidate>
          <div class="mb-3">
            <label class="form-label small text-dark-50" for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="form-control" placeholder="e.g Juan Dela Cruz" />
          </div>

          <div class="mb-3">
            <label class="form-label small text-dark-50" for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" placeholder="8-16 characters" />
          </div>

          <div class="mb-3">
            <label class="form-label small text-dark-50" for="password">Password</label>
            <div class="position-relative">
                <input type="password" name="password" id="password" class="form-control pe-5" />
                <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer" id="togglePassword"></i>
            </div>
            <div class="password-hint">Min 8 chars, 1 uppercase, 1 lowercase, 1 number.</div>
          </div>

          <div class="mb-3">
            <label class="form-label small text-dark-50" for="confirm_password">Confirm Password</label>
            <div class="position-relative">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control pe-5" />
                <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer" id="toggleConfirmPassword"></i>
            </div>
            <div class="password-hint" id="confirm_hint">Passwords must match.</div>
          </div>

          <div class="mb-4">
            <label class="form-label small text-dark-50" for="role">User Role</label>
            <select name="role" id="role" class="form-select input-valid">
              <option value="Staff" class="text-dark">Staff</option>
              <option value="Admin" class="text-dark">Admin</option>
              <option value="Student" class="text-dark">Student</option>
            </select>
          </div>

          <button class="btn btn-outline-dark btn-lg w-100 fw-bold mt-2" type="submit">REGISTER</button>
        </form>

        <div class="text-center mt-4">
          <p class="mb-0 small">Already have an account? <a href="login.php" class="text-success fw-bold text-decoration-none">Login here</a></p>
        </div>

      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("registrationForm");
        const fullNameInput = document.getElementById("full_name");
        const usernameInput = document.getElementById("username");
        const passwordInput = document.getElementById("password");
        const confirmInput = document.getElementById("confirm_password");
        const passRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

        function setupToggle(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            icon.addEventListener("click", function() {
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove("bi-eye-slash");
                    icon.classList.add("bi-eye");
                } else {
                    input.type = "password";
                    icon.classList.remove("bi-eye");
                    icon.classList.add("bi-eye-slash");
                }
            });
        }
        setupToggle("password", "togglePassword");
        setupToggle("confirm_password", "toggleConfirmPassword");

        function setValidationState(input, isValid) {
            if (isValid) {
                input.classList.remove("input-invalid");
                input.classList.add("input-valid");
            } else {
                input.classList.remove("input-valid");
                input.classList.add("input-invalid");
            }
        }

        fullNameInput.addEventListener("input", function() { setValidationState(this, this.value.trim().length >= 2); });
        usernameInput.addEventListener("input", function() { setValidationState(this, this.value.length >= 8 && this.value.length <= 16); });
        passwordInput.addEventListener("input", function() {
            setValidationState(this, passRegex.test(this.value));
            if (confirmInput.value.length > 0) validateConfirmPassword(); 
        });

        function validateConfirmPassword() {
            const isValid = confirmInput.value === passwordInput.value && passwordInput.value.length > 0;
            setValidationState(confirmInput, isValid);
        }
        confirmInput.addEventListener("input", validateConfirmPassword);

        form.addEventListener("submit", function(e) {
            const isFullValid = fullNameInput.value.trim().length >= 2;
            const isUserValid = usernameInput.value.length >= 8 && usernameInput.value.length <= 16;
            const isPassValid = passRegex.test(passwordInput.value);
            const isConfValid = confirmInput.value === passwordInput.value && passwordInput.value.length > 0;

            if (!isFullValid || !isUserValid || !isPassValid || !isConfValid) {
                e.preventDefault(); 
                setValidationState(fullNameInput, isFullValid);
                setValidationState(usernameInput, isUserValid);
                setValidationState(passwordInput, isPassValid);
                setValidationState(confirmInput, isConfValid);

                let errorHtml = "<div style='text-align:left; font-size: 0.9rem;'><ul>";
                if (!isFullValid) errorHtml += "<li><strong>Full Name</strong> requires at least 2 characters.</li>";
                if (!isUserValid) errorHtml += "<li><strong>Username</strong> must be 8-16 characters.</li>";
                if (!isPassValid) errorHtml += "<li><strong>Password</strong> needs 8+ chars, 1 uppercase, 1 lowercase, and 1 number.</li>";
                if (!isConfValid) errorHtml += "<li><strong>Passwords</strong> do not match.</li>";
                errorHtml += "</ul></div>";

                Swal.fire({
                    icon: 'warning',
                    title: 'Check your inputs',
                    html: errorHtml,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Fix and Try Again'
                });
            }
        });
    });
</script>

</body>
</html>