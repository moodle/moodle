<?php
// This file is part of Moodle Course Rollover Plugin
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     local_auto_proctor
 * @author      Angelica
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
*/
require_once(__DIR__ . '/../../../../config.php');

// // Specify the path where the video will be saved
// $upload_dir = '../evidences/camera_capture_evidence/';
// if (!file_exists($upload_dir)) {
//     mkdir($upload_dir, 0777, true);
// }

// // Get the recording filename from the custom header
// $recording_filename = isset($_SERVER['HTTP_X_RECORDING_FILENAME']) ? $_SERVER['HTTP_X_RECORDING_FILENAME'] : 'recorded_video';

// // Define the file paths
// $webm_file = $upload_dir . $recording_filename . '.webm';

// // Save the WebM file
// file_put_contents($webm_file, file_get_contents('php://input'));

// // Check if WebM file was saved successfully
// if (file_exists($webm_file)) {
//     echo 'Video saved as: ' . $webm_file;
// } else {
//     echo 'Error: Failed to save video';
// }

// ========= USING XHR ===========
$uploadDir = '../evidences/camera_capture_evidence/';
$recordingFilename = isset($_SERVER['HTTP_X_RECORDING_FILENAME']) ? $_SERVER['HTTP_X_RECORDING_FILENAME'] : 'recorded_video';
$webmFile = $uploadDir . $recordingFilename . '.webm';

if (!file_exists($uploadDir) && !mkdir($uploadDir, 0777, true)) {
    die('Error: Failed to create directory');
}

$uploadedFile = fopen('php://input', 'rb');
$targetFile = fopen($webmFile, 'wb');

if (!$uploadedFile || !$targetFile || stream_copy_to_stream($uploadedFile, $targetFile) === false) {
    die('Error: Failed to save recording');
}

fclose($uploadedFile);
fclose($targetFile);

echo 'Recording saved as: ' . $webmFile;






// ============ IF FFMPEG IS INSTALLED IN THE SERVER ============
// This script convert the webm file to mp4, if conversion is successful then it deletes the webm file.

// // Specify the path where the video will be saved
// $upload_dir = 'videos/';
// if (!file_exists($upload_dir)) {
//     mkdir($upload_dir, 0777, true);
// }

// // Get the recording filename from the custom header
// $recording_filename = isset($_SERVER['HTTP_X_RECORDING_FILENAME']) ? $_SERVER['HTTP_X_RECORDING_FILENAME'] : 'recorded_video';

// // Define the file paths
// $webm_file = $upload_dir . $recording_filename . '.webm';
// $mp4_file = $upload_dir . $recording_filename . '.mp4';

// // Save the WebM file
// file_put_contents($webm_file, file_get_contents('php://input'));

// // Convert WebM to MP4 using FFmpeg
// // $ffmpeg_command = "C:\\ffmpeg\\bin\\ffmpeg -i $webm_file -c:v libx264 -preset medium -crf 23 -c:a aac -strict experimental $mp4_file";
// $ffmpeg_command = "/home/s24oaeekvw21/ffmpeg -i $webm_file -c:v libx264 -preset medium -crf 23 -c:a aac -strict experimental $mp4_file";


// // Execute FFmpeg command
// exec($ffmpeg_command, $output, $return_value);

// // Check if FFmpeg command was executed successfully
// if ($return_value === 0) {
//     // Delete the WebM file after conversion
//     if (file_exists($webm_file)) {
//         unlink($webm_file);
//     }
//     echo 'Video saved as: ' . $mp4_file;
// } else {
//     echo 'Error: Failed to convert video to MP4';
// }
?>
