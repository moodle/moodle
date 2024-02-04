<?php
$dataUri = $_POST['dataUri'];
$filename = $_POST['filename'];

// Specify the folder for saving captures
$folderPath = 'camera_capture_evidence'; // Removed leading slash

// Ensure the folder exists
if (!file_exists($folderPath)) {
    mkdir($folderPath, 0777, true);
}

// Decode the dataUri
$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $dataUri));

// Save the file
file_put_contents($folderPath . '/' . $filename, $data); // Concatenated folderPath with filename

// Respond with the filename for any further use
echo json_encode(['filename' => $filename]);
?>
