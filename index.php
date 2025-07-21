<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            redirectTo('dashboard.php');
        } else {
            $error = 'Username atau password salah';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARK Sentient - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .farm-image {
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><rect fill="%23f0f8ff" width="1200" height="800"/><rect fill="%23228b22" y="600" width="1200" height="200"/><circle fill="%23ffd700" cx="200" cy="150" r="80"/><polygon fill="%238b4513" points="400,400 500,300 600,400 600,500 400,500"/><polygon fill="%23dc143c" points="480,280 500,260 520,280 520,310 480,310"/><rect fill="%23654321" x="490" y="400" width="20" height="100"/><ellipse fill="%23ffffff" cx="800" cy="450" rx="60" ry="40"/><ellipse fill="%23000000" cx="820" cy="440" rx="8" ry="8"/><path fill="%23ffc0cb" d="M780,460 Q790,470 800,460"/></svg>') center/cover;
            min-height: 100vh;
        }
        .brand-logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c5530;
            margin-bottom: 0.5rem;
        }
        .login-form {
            padding: 3rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #71b142 0%, #5a8f37 100%);
            border: none;
            border-radius: 25px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-social {
            border-radius: 25px;
            padding: 12px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #71b142;
            box-shadow: 0 0 0 0.2rem rgba(113, 177, 66, 0.25);
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row login-container mx-auto" style="max-width: 1000px;">
            <!-- Left side - Farm image -->
            <div class="col-md-6 p-0 d-none d-md-block">
                <div class="farm-image d-flex align-items-center justify-content-center">
                    <div class="text-center text-white">
                        <i class="fas fa-cow fa-5x mb-3"></i>
                        <h3>Marketplace Ternak Terpercaya</h3>
                        <p class="lead">Menghubungkan peternak dengan pembeli</p>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Login form -->
            <div class="col-md-6 login-form">
                <div class="text-center mb-4">
                    <div class="brand-logo">
                        <i class="fas fa-seedling"></i> ARK Sentient
                    </div>
                    <h4 class="text-success fw-bold">Login</h4>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Enter your username" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-login btn-success w-100 mb-3">
                        Login
                    </button>
                </form>
                
                <div class="text-center">
                    <button class="btn btn-outline-primary btn-social w-100 mb-2">
                        <i class="fab fa-google me-2"></i>Continue With Gmail
                    </button>
                    <button class="btn btn-outline-primary btn-social w-100 mb-3">
                        <i class="fab fa-facebook me-2"></i>Continue With Facebook
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="text-muted">Don't you have an account? 
                        <a href="register.php" class="text-success text-decoration-none fw-bold">Sign Up</a>
                    </p>
                </div>
                
                <div class="mt-4 text-center">
                    <small class="text-muted">
                        Demo Login:<br>
                        <strong>Username:</strong> admin | <strong>Password:</strong> password<br>
                        <strong>Username:</strong> farmer1 | <strong>Password:</strong> password<br>
                        <strong>Username:</strong> customer1 | <strong>Password:</strong> password
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>