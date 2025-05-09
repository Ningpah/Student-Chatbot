<?php
// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cuib-bot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch events
$events = [];

$sql = "SELECT event_date, event_name FROM events";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $events[$row['event_date']] = $row['event_name'];
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CUIB BOT - Calendar</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

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

    <!-- Calendar Section -->
    <main class="chat-area">
      <div class="event-header">
        <h2>School Calendar <img src="images/calendar2.svg" alt="Calendar Icon" class="event-header-icon"></h2>
        <a href="event.php">
          <button class="calendar-button">Switch to Table</button>
        </a>
      </div>

      <div class="calendar-year">
        <?php
        $year = date('Y');

        $months = [
          1 => "January", 2 => "February", 3 => "March",
          4 => "April", 5 => "May", 6 => "June",
          7 => "July", 8 => "August", 9 => "September",
          10 => "October", 11 => "November", 12 => "December"
        ];

        foreach ($months as $num => $name) {
          echo "<div class='month'>";
          echo "<h3>$name</h3>";
          echo "<div class='day-names'>
                  <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                </div>";
          echo "<div class='days'>";

          $firstDayOfMonth = mktime(0, 0, 0, $num, 1, $year);
          $daysInMonth = date('t', $firstDayOfMonth);
          $startDayOfWeek = date('w', $firstDayOfMonth);

          // Blank spaces before first day
          for ($i = 0; $i < $startDayOfWeek; $i++) {
            echo "<div></div>";
          }

          // Print the days
          for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = sprintf('%04d-%02d-%02d', $year, $num, $day);

            if (array_key_exists($currentDate, $events)) {
              $eventName = htmlspecialchars($events[$currentDate], ENT_QUOTES);
              echo "<div class='event' data-title='$eventName'>$day</div>";
            } else {
              echo "<div>$day</div>";
            }
          }

          echo "</div></div>";
        }
        ?>
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <p style="text-align: center; color: rgba(24, 24, 24, 0.72);">©2025 CUIB BOT | All Rights Reserved</p>
    </footer>

  </div>
</div>

<script>
  // Sidebar Toggle
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

</body>
</html>
