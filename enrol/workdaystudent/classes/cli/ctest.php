<?php
$angle = null;
$hour_angle = null;
$minute_angle = null;
$input_time = '';
$error = '';

function calculate_clock_angle($time_str) {
    list($h, $m) = explode(':', $time_str);
    $h = intval($h) % 12;
    $m = intval($m);

    $hour_angle = 30 * $h + 0.5 * $m;
    $minute_angle = 6 * $m;
    $angle = abs($hour_angle - $minute_angle);
    $smallest_angle = min($angle, 360 - $angle);

    return [$hour_angle, $minute_angle, $smallest_angle, $h, $m];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_time = $_POST['time'] ?? '';
    if (!preg_match('/^\d{1,2}:\d{2}$/', $input_time)) {
        $error = "Please enter time in HH:MM format.";
    } else {
        [$hour_angle, $minute_angle, $angle, $h, $m] = calculate_clock_angle($input_time);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Clock Angle Calculator</title>
  <style>
    body { font-family: sans-serif; text-align: center; padding: 2rem; }
    .clock {
        position: relative;
        width: 300px;
        height: 300px;
        margin: 1rem auto;
        border: 10px solid #333;
        border-radius: 50%;
        background: #fff;
    }
    .hand {
        position: absolute;
        bottom: 50%;
        left: 50%;
        transform-origin: bottom center;
        background: #000;
    }
    .hour {
        width: 8px;
        height: 70px;
        background-color: #222;
        z-index: 2;
    }
    .minute {
        width: 4px;
        height: 100px;
        background-color: #0077cc;
        z-index: 1;
    }
    .center {
        width: 14px;
        height: 14px;
        background: red;
        border-radius: 50%;
        position: absolute;
        left: calc(50% - 7px);
        top: calc(50% - 7px);
        z-index: 3;
    }
    .result { margin-top: 1rem; font-size: 1.2rem; }
    form { margin-bottom: 2rem; }
    input[type="text"] {
        padding: 0.5rem;
        font-size: 1rem;
    }
    button {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    }
    .error { color: red; }
  </style>
</head>
<body>
<h1>Clock Angle Calculator</h1>

<form method="POST">
  <label>Enter Time (HH:MM): 
    <input type="text" name="time" value="<?= htmlspecialchars($input_time) ?>" required />
  </label>
  <button type="submit">Calculate</button>
</form>

<?php if ($error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php elseif ($angle !== null): ?>
  <div class="clock">
    <div class="hand hour" style="transform: rotate(<?= $hour_angle ?>deg);"></div>
    <div class="hand minute" style="transform: rotate(<?= $minute_angle ?>deg);"></div>
    <div class="center"></div>
  </div>
  <div class="result">
    <p><strong>Time:</strong> <?= htmlspecialchars($input_time) ?></p>
    <p><strong>Hour hand angle:</strong> <?= number_format($hour_angle, 2) ?>°</p>
    <p><strong>Minute hand angle:</strong> <?= number_format($minute_angle, 2) ?>°</p>
    <p><strong>Smallest angle between them:</strong> <?= number_format($angle, 2) ?>°</p>
  </div>

  <div class="result">
    <h3>How it works:</h3>
    <p>The hour hand moves 30° each hour. (360° / 12 hours) &mdash; In this case, <?= number_format($h) ?> * 30°  = <?= number_format($h * 30, 2) ?>°.</p>
    <p>The hour hand also moves 0.5° past the hour marker per minute. (360° / 720 minutes)  &mdash; In this case, <?= number_format($m) ?> * 0.5°  = <?= number_format($m * 0.5, 2) ?>°.</p>
    <p>The minute hand moves 6° each minute. (360° / 60 minutes) &mdash; In this case, <?= number_format($m) ?> * 6°  = <?= number_format($m * 6, 2) ?>°.</p>
    <p><strong>Hour hand angle = 30 × H + 0.5 × M</strong></p>
    <p><strong>Minute hand angle = 6 × M</strong></p>
    <p><strong>Angle = hour hand angle - minute hand angle (or 360 - (hour hand angle - minute hand angle), whichever is smaller)</strong></p>
  </div>
<?php endif; ?>
</body>
</html>

