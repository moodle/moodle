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

if (isset($_FILES['audio']) && isset($_FILES['audio']['tmp_name'])) {
    // Directory where the audio files will be stored
    $target_dir = "../evidences/microphone_capture_evidence/";

    // Create the directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Create directory recursively
    }

    // Check if the directory is writable
    if (!is_writable($target_dir)) {
        echo "Error: Directory is not writable.";
        exit;
    }

    // Get the original filename
    $filename = $_FILES['audio']['name'];

    // Set the target location where the audio file will be moved
    $target_file = $target_dir . $filename;

    // Move the uploaded audio file to the target location
    if (move_uploaded_file($_FILES['audio']['tmp_name'], $target_file)) {
        echo "Audio file uploaded successfully.";
    } else {
        echo "Failed to move the uploaded audio file.";
    }
} else {
    echo "No audio file uploaded.";
}
?>
