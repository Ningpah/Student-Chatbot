<?php
session_start();

// Destroy session on GET (unless coming from a POST login)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || (isset($_POST['message']) && !isset($_POST['matricule']))) {
    session_unset();
    session_destroy();
}

// Check if it's an AJAX chat request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_CONTENT_TYPE']) && strpos($_SERVER['HTTP_CONTENT_TYPE'], 'application/json') !== false) {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['message'])) {
        echo json_encode(['reply' => 'No message provided.']);
        exit;
    }

    // Call Python Flask bot
    $ch = curl_init('http://localhost:5000/chat');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $input['message']]));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['reply' => 'Bot error: ' . curl_error($ch)]);
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    $botReply = json_decode($response, true);
    echo json_encode(['reply' => $botReply['response'] ?? 'No reply from bot']);
    exit;
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
      <input type="text" id="chatInput" placeholder="Type your question..." />
    </footer>
  </div>
</div>
<?php endif; ?>

<script>
  const chatInput = document.getElementById('chatInput');
const chatArea = document.getElementById('chatArea');

if (chatInput) {
  chatInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter' && chatInput.value.trim() !== '') {
      const userMsg = chatInput.value;

      // Show user's message
      const message = document.createElement('div');
      message.textContent = userMsg;
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
      chatInput.value = '';
      chatArea.scrollTop = chatArea.scrollHeight;

      // Send to backend
      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: userMsg })
      })
      .then(res => res.json())
      .then(data => {
        const botMessage = document.createElement('div');
        botMessage.textContent = data.reply;
        botMessage.style.backgroundColor = '#eee';
        botMessage.style.padding = '10px';
        botMessage.style.margin = '5px';
        botMessage.style.borderRadius = '10px';
        botMessage.style.maxWidth = '70%';
        botMessage.style.display = 'inline-block';
        botMessage.style.float = 'left';
        botMessage.style.clear = 'both';
        chatArea.appendChild(botMessage);
        chatArea.scrollTop = chatArea.scrollHeight;
      })
      .catch(err => {
        const errorMsg = document.createElement('div');
        errorMsg.textContent = 'Bot error: ' + err;
        errorMsg.style.color = 'red';
        chatArea.appendChild(errorMsg);
      });
    }
  });
}
</script>

</body>
</html>