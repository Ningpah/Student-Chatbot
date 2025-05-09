<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cuib-bot"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT event_date, event_name, event_location FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);
?>

<?php
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CUIB BOT - Events</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const menuBtn = document.querySelector(".menu");
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.querySelector(".overlay");

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
  });
</script>

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
        <span class="menu"><img src="images/menu.svg" alt="menu"></span>
      </div>
      <h1 class="title">CUIB BOT <img src="images/bot.svg" alt="Bot Icon" class="bot-icon" /></h1>
      <div class="right-icons">
        <span class="user-icon"><img src="images/avatar.svg" alt="Profile Photo"></span>
      </div>
    </header>

    <!-- Events Section -->
    <main class="chat-area" id="chatArea">
      <div class="event-header">
        <h2>School Events <img src="images/list.svg" alt="List Icon" class="event-header-icon"></h2>
        <button class="calendar-button">Switch to Calendar</button>
      </div>

      <table class="event-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Event</th>
            <th>Location</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                <td><?php echo htmlspecialchars($row['event_location']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="3">No events found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </main>

    <!-- Footer -->
    <footer class="footer" id="footer">
      <p style="text-align: center; color:rgba(24, 24, 24, 0.72);">©2025 CUIB BOT | All Rights Reserved</p>
    </footer>
  </div>
</div>
</body>
</html>