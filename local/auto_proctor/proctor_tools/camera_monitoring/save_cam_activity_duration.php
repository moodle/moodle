<?php
if (isset($_POST['filename'])) {
    $filename = $_POST['filename'];
    $duration = $_POST['duration']; // Ensure it's an integer
    
    echo "filename: " . $filename;
    echo "</br>";
    echo "duration: " . $duration;
}
?>