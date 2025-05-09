<?php
session_start();

// Destroy any previous session unless it’s a login POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    session_unset();
    session_destroy();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cuib-bot";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['matricule']) && isset($_POST['password'])) {
    $matricule = $conn->real_escape_string($_POST['matricule']);
    $pass = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM accounts WHERE matricule = '$matricule' AND password = '$pass'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $_SESSION['user'] = $matricule;
    } else {
        $login_error = "Invalid matricule or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CUIB BOT</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

<?php if (!isset($_SESSION['user'])): ?>
  <!-- Login Popup -->
  <div class="login-popup" id="loginPopup">
    <div class="login-box">
      <h2>Let’s get started.</h2>
      <?php if ($login_error): ?>
        <p class="error"><?= $login_error ?></p>
      <?php endif; ?>
      <form method="POST" action="">
        <input type="text" name="matricule" placeholder="Enter matricule" required />
        <input type="password" name="password" placeholder="Enter password" required />
        <button type="submit">Login</button>
      </form>
      <div class="help-text">
        <p><strong>Having trouble logging in?</strong><br>Get help at the Student Service Center</p>
      </div>
    </div>
  </div>
<?php else: ?>
<div class="app-wrapper">
  <div class="main-content">
    <!-- Header -->
    <header class="header">
      <nav class="sidebar" id="sidebar">
        <ul>
          <li><a href="#"><img src="images/home.png" alt="Home"> Home</a></li>
          <li><a href="#"><img src="images/calendar1.svg" alt="calendar"> School Calendar</a></li>
          <li><a href="#"><img src="images/settings.svg" alt="Settings"> Chat History</a></li>
          <li><a href="#"><img src="images/help.svg" alt="Help"> Help</a></li>
        </ul>
      </nav>
      <div class="overlay"></div>
      <div class="left-icons">
        <span class="menu" onclick="toggleSidebar()"><img src="images/menu.svg" alt="menu"></span>
      </div>
      <h1 class="title">CUIB BOT <img src="images/bot.svg" alt="Bot Icon" class="bot-icon" /></h1>
      <div class="right-icons">
        <span class="user-icon"><img src="images/avatar.svg" alt="Profile Photo"></span>
      </div>
    </header>

    <!-- Chat area -->
    <main class="chat-area" id="chatArea">
      <p>Welcome! Ask me anything.</p>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <input type="text" id="chatInput" placeholder="Type a message and press Enter..." />
    </footer>
  </div>
</div>
<?php endif; ?>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const menuBtn = document.querySelector(".menu");
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.querySelector(".overlay");

    if (menuBtn && sidebar && overlay) {
      menuBtn.addEventListener("click", function (e) {
        sidebar.classList.toggle("active");
        e.stopPropagation();
      });

      sidebar.addEventListener("click", function (e) {
        e.stopPropagation();
      });

      overlay.addEventListener("click", function () {
        sidebar.classList.remove("active");
      });

      document.addEventListener("click", function () {
        sidebar.classList.remove("active");
      });
    }

    const chatInput = document.getElementById('chatInput');
    const chatArea = document.getElementById('chatArea');

    if (chatInput) {
      chatInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && chatInput.value.trim() !== '') {
          const message = document.createElement('div');
          message.textContent = chatInput.value;
          message.style.backgroundColor = '#00cfff';
          message.style.color = 'white';
          message.style.padding = '10px';
          message.style.margin = '5px';
          message.style.borderRadius = '10px';
          message.style.maxWidth = '70%';
          message.style.display = 'inline-block';
          message.style.float = 'right';
          message.style.clear = 'both';

          chatArea.appendChild(message);
          chatArea.scrollTop = chatArea.scrollHeight;
          chatInput.value = '';
        }
      });
    }
  });
</script>

</body>
</html>