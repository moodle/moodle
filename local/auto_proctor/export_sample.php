<?php
// URL of the file on the server
$url = 'http://localhost/PROJECTS/e-RTU/local/auto_proctor/proctor_tools/evidences/microphone_capture_evidence/EVD_USER_2_QUIZ_2_ATTEMPT_1_02292024115852PMGMT+8_847_speech_detected_.wav';

// Local path where you want to save the file
$localPath = 'C:/Users/angel/OneDrive/Desktop/export.mp4';

// Fetch the file contents from the server
$fileContents = file_get_contents($url);

if ($fileContents !== false) {
    // Save the file contents to the local path
    $result = file_put_contents($localPath, $fileContents);

    if ($result !== false) {
        echo "File saved successfully to $localPath";
    } else {
        echo "Failed to save the file to $localPath";
    }
} else {
    echo "Failed to fetch the file from $url";
}
?>
