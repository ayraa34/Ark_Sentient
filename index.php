<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARK Sentient - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="asset/css/auth.css" rel="stylesheet">
</head>
<body></body>
    <div class="login-container">
        <!-- Left side - Farm image -->
        <div class="left-section">
            <div class="left-content">
                <i class="fas fa-cow fa-4x mb-3"></i>
                <h3>Marketplace Ternak Terpercaya</h3>
                <p class="lead">Menghubungkan peternak dengan pembeli</p>
            </div>
        </div>
        
        <!-- Right side - Login form -->
        <div class="right-section">
            <div class="login-form-container">
                <div class="brand-header">
                    <div class="brand-logo">
                        <div class="brand-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        ARK Sentient
                    </div>
                    <h2 class="login-title">Login</h2>
                </div>
                
                <!-- Alert messages would go here -->
                <div id="alertContainer"></div>
                
                <form id="loginForm">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Enter your username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        Login
                    </button>
                </form>
                
                <div class="social-buttons">
                    <button class="btn btn-social">
                        <i class="fab fa-google google-icon"></i>
                        Continue With Gmail
                    </button>
                    <button class="btn btn-social">
                        <i class="fab fa-facebook facebook-icon"></i>
                        Continue With Facebook
                    </button>
                </div>
                
                <div class="signup-link">
                    <p>Don't you have an account? 
                        <a href="views/Auth/register.php">Sign Up</a>
                    </p>
                </div>
                
                <div class="demo-info">
                    <div class="text-center">
                        <small>
                            <strong>Demo Login:</strong><br>
                            <strong>Username:</strong> admin | <strong>Password:</strong> password<br>
                            <strong>Username:</strong> farmer1 | <strong>Password:</strong> password<br>
                            <strong>Username:</strong> customer1 | <strong>Password:</strong> password
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form submission handler
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                showAlert('Username dan password wajib diisi', 'danger');
                return;
            }
            
            // Demo validation
            const demoUsers = {
                'admin': 'password',
                'farmer1': 'password',
                'customer1': 'password'
            };
            
            if (demoUsers[username] && demoUsers[username] === password) {
                showAlert('Login berhasil! Redirecting...', 'success');
                setTimeout(() => {
                    // In real implementation, redirect to dashboard
                    console.log('Redirecting to dashboard...');
                }, 1500);
            } else {
                showAlert('Username atau password salah', 'danger');
            }
        });
        
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            alertContainer.innerHTML = '';
            alertContainer.appendChild(alertDiv);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>