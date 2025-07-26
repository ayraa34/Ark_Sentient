<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$nama_user = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Nama User';
$inisial = strtoupper(substr($nama_user, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ark Sentient - Smart Assistant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../Asset/css/smartasis.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="menu-btn" id="burgerBtn"><i class="fas fa-bars"></i></button>
            <button class="search-btn"><i class="fas fa-search"></i></button>
        </div>
        <div class="sidebar-content">
            <div class="new-chat">
                <i class="fas fa-pen"></i>
                <span>New Chat</span>
            </div>
        </div>
        <div class="spacer"></div>
        <div class="logo-bottom">
            <i class="fas fa-cow"></i>
            <span><b>ARK Sentient</b></span>
        </div>
    </div>
    <!-- Bootstrap Navbar (copy dari dashboard.php) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-success" href="home.php">
                <i class="fas fa-seedling me-2"></i>ARK Sentient
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Marketplace Ternak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Views/priksaternak.php">Pemeriksaan Ternak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="smartasis.php">Smart Assistant</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">History</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <!-- Cart badge can be added here if needed -->
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php
                            $user_display = isset($_SESSION['full_name']) && $_SESSION['full_name'] !== ''
                                ? htmlspecialchars($_SESSION['full_name'])
                                : (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User');
                            echo $user_display;
                            ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-list me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="main-content">
        <div class="background-logo">
            <img src="../Asset/icon/logoweb.png" alt="Logo">
        </div>
        <div class="content-wrapper">
            <div class="hello-text">
                Hello, <?php echo htmlspecialchars($nama_user); ?>
            </div>
        </div>
        <div class="input-area">
            <div class="input-box">
                <div class="input-row">
                    <input
                        type="text"
                        class="input-label"
                        placeholder="Minta smart asistant"
                    />
                    <button class="mic-btn">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <button class="enter-btn">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                </div>
                <input type="file" id="fileUploadInput" class="hidden-file-input">
                <div class="input-upload" id="uploadFileButton">
                    <i class="fas fa-plus"></i> Upload File
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const burgerBtn = document.getElementById('burgerBtn');
        const uploadFileButton = document.getElementById('uploadFileButton');
        const fileUploadInput = document.getElementById('fileUploadInput');

        burgerBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });

        uploadFileButton.addEventListener('click', () => {
            fileUploadInput.click();
        });

        fileUploadInput.addEventListener('change', () => {
            if (fileUploadInput.files.length > 0) {
                console.log('File selected:', fileUploadInput.files[0].name);
            } else {
                console.log('No file selected.');
            }
        });
    </script>
</body>
</html>