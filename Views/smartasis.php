<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$nama_user = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Nama User';
$inisial = strtoupper(substr($nama_user, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Smart Assistant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #fff;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: 240px;
            background: #c8f7d8;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            z-index: 20;
            transition: width 0.8s cubic-bezier(0.4,0,0.2,1);
            box-shadow: 0 0 8px rgba(0,0,0,0.04);
        }
        .sidebar.collapsed {
            width: 60px;
        }
        .sidebar .sidebar-header {
            width: 100%;
            display: flex;
            align-items: center;
            padding: 16px 18px 0 18px;
            gap: 10px;
        }
        .sidebar .menu-btn, .sidebar .search-btn {
            font-size: 22px;
            color: #222;
            cursor: pointer;
            background: none;
            border: none;
            outline: none;
        }
        .sidebar .search-btn {
            margin-left: 8px;
        }
        .sidebar .sidebar-content {
            width: 100%;
            margin-top: 24px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .sidebar .new-chat {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 0 18px;
            font-size: 16px;
            color: #222;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
            border-radius: 8px;
            padding: 6px 12px 6px 0;
        }
        .sidebar .new-chat i {
            font-size: 20px;
        }
        .sidebar .new-chat:hover {
            background: rgba(44,85,48,0.08);
            color: #2c5530;
        }
        .sidebar .spacer {
            flex: 1;
        }
        .sidebar .logo-bottom {
            margin: 18px 0 10px 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sidebar .logo-bottom i {
            font-size: 22px;
        }
        .sidebar .logo-bottom span {
            font-size: 16px;
            font-weight: bold;
            color: #222;
        }
        .sidebar.collapsed .sidebar-content,
        .sidebar.collapsed .logo-bottom span {
            display: none;
        }
        .sidebar.collapsed .logo-bottom {
            margin-left: 0;
            justify-content: center;
        }
        .sidebar.collapsed .menu-btn,
        .sidebar.collapsed .search-btn {
            margin-left: 0;
        }
        .navbar {
            margin-left: 240px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #e0e0e0;
            position: relative;
            background: #fff;
            transition: margin-left 0.8s cubic-bezier(0.4,0,0.2,1);
        }
        .sidebar.collapsed ~ .navbar {
            margin-left: 60px;
        }
        .navbar-menu {
            display: flex;
            gap: 38px;
            font-size: 17px;
            font-weight: 400;
            color: #222;
            margin: 0 auto;
        }
        .navbar-menu a {
            text-decoration: none;
            color: #222;
            transition: color 0.2s;
        }
        .navbar-menu a:hover {
            color: #2c5530;
        }
        .main-content {
            margin-left: 240px;
            min-height: calc(100vh - 56px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: margin-left 0.8s cubic-bezier(0.4,0,0.2,1);
            padding-bottom: 120px;
            box-sizing: border-box;
        }
        .sidebar.collapsed ~ .main-content {
            margin-left: 60px;
        }
        .background-logo {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%,-50%);
            z-index: 0;
            opacity: 0.12;
        }
        .background-logo img {
            height: 180px;
            width: 180px;
            object-fit: contain;
            filter: grayscale(1) brightness(0.7);
        }
        .content-wrapper {
            z-index: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            flex-grow: 1;
            padding-top: 56px;
        }
        .hello-text {
            font-size: 2rem;
            font-weight: 600;
            color: #222;
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .input-area {
            width: 100%;
            display: flex;
            justify-content: center;
            position: absolute;
            bottom: 38px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }
        .input-box {
            background: #ededed;
            border-radius: 28px;
            padding: 0 28px;
            width: 480px;
            max-width: 92vw;
            box-shadow: 0 2px 18px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        .input-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }
        .input-row .input-label {
            color: #222;
            font-size: 1.1rem;
            font-weight: 400;
            background: transparent;
            border: none;
            outline: none;
            width: 100%;
        }
        .input-row input::placeholder {
            color: #444444;
            opacity: 1;
        }
        .mic-btn, .enter-btn {
            margin-left: 10px;
            background: none;
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #222;
            font-size: 1.3rem;
            transition: background 0.2s, color 0.2s;
        }
        .mic-btn i, .enter-btn i {
            color: #222;
            font-size: 18px;
        }
        .mic-btn:hover, .enter-btn:hover {
            background: #e0e0e0;
            color: #bbb;
        }
        .mic-btn:hover i, .enter-btn:hover i {
            color: #bbb;
        }
        .input-upload {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #222;
            font-size: 1rem;
            margin: 0 0 12px 0;
            cursor: pointer;
            font-weight: 400;
        }
        .input-upload i {
            font-size: 1.1rem;
        }
        .hidden-file-input {
            display: none;
        }

        @media (max-width: 900px) {
            .sidebar { width: 60px; }
            .navbar, .main-content { margin-left: 60px; }
            .sidebar.collapsed { width: 0; }
            .sidebar.collapsed ~ .navbar,
            .sidebar.collapsed ~ .main-content { margin-left: 0; }
            .sidebar .search-btn {
                display: none;
            }
        }
        @media (max-width: 600px) {
            .input-box { width: 99vw; }
            .hello-text {
                margin-top: 40px;
                font-size: 1.8rem;
            }
            .input-area {
                bottom: 20px;
            }
        }
    </style>
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
    <div class="navbar">
        <div class="navbar-menu">
            <a href="dashboard.php">Marketplace Ternak</a>
            <a href="#">Pemeriksa Ternak</a>
            <a href="smartasis.php">Smart Assistan</a>
            <a href="#">History</a>
        </div>
    </div>
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