<?php
require_once '../includes/db.php';
include '../includes/header.php';

$error = '';
$success = '';

// Log
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];

            header("Location: ../index.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}

// Reg
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['reg_username']);
    $email = trim($_POST['reg_email']);
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
        $check->execute([
            ':email' => $email,
            ':username' => $username
        ]);

        if ($check->rowCount() > 0) {
            $error = "Email or username already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare("
                INSERT INTO users (full_name, username, email, password)
                VALUES (:full_name, :username, :email, :password)
            ");

            if ($insert->execute([
                ':full_name' => $full_name,
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashed
            ])) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>

<style>
.auth-container {
    max-width: 900px;
    margin: 60px auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

.auth-box {
    background: #fff;
    padding: 40px;
    border: 2px solid var(--light-brown);
    border-radius: 8px;
}

.auth-box h2 {
    text-align: center;
    margin-bottom: 25px;
    border-bottom: 2px solid var(--primary-brown);
    padding-bottom: 10px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 6px;
    display: block;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--light-brown);
    border-radius: 5px;
}

.submit-btn {
    width: 100%;
    padding: 12px;
    background: var(--primary-brown);
    color: var(--cream);
    border: none;
    border-radius: 5px;
    font-size: 1.1rem;
    cursor: pointer;
}

.submit-btn:hover {
    background: var(--accent-gold);
    color: #000;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
}

.success-message {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
}

.toggle-text {
    text-align: center;
    margin-top: 15px;
}

.toggle-text a {
    color: var(--primary-brown);
    font-weight: bold;
    cursor: pointer;
}

@media (max-width: 768px) {
    .auth-container {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container">
    <div class="auth-container">

        <!--log-->
        <div class="auth-box" id="loginBox">
            <h2>Login</h2>

            <?php if ($error && isset($_POST['login'])): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" name="login" class="submit-btn">Login</button>
            </form>

            <p class="toggle-text">
                Do not have an account?
                <a onclick="showRegister()">Register</a>
            </p>
        </div>

        <!-- Reg -->
        <div class="auth-box" id="registerBox" style="display:none;">
            <h2>Register</h2>

            <?php if ($error && isset($_POST['register'])): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="reg_username" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="reg_email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="reg_password" required>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <button type="submit" name="register" class="submit-btn">Register</button>
            </form>

            <p class="toggle-text">
                Already have an account?
                <a onclick="showLogin()">Login</a>
            </p>
        </div>

    </div>
</div>

<script>
function showRegister() {
    document.getElementById('loginBox').style.display = 'none';
    document.getElementById('registerBox').style.display = 'block';
}

function showLogin() {
    document.getElementById('registerBox').style.display = 'none';
    document.getElementById('loginBox').style.display = 'block';
}
</script>

<?php include '../includes/footer.php'; ?>
