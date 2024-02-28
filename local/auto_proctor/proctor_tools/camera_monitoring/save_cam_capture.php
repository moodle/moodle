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

$dataUri = $_POST['dataUri'];
$filename = $_POST['filename'];

// The destination folder for saving the captured image from the camera.
// DIRECTTORY PATH: auto_proctor/proctor_tools/evidences/camera_capture_evidence
$folderPath = '../evidences/camera_capture_evidence/';

// Ensuring that the folder exists
if (!file_exists($folderPath)) {
    mkdir($folderPath, 0777, true);
}

// Since we converted the picture to a data URL, 
// decode the dataURI to be a picture.
$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $dataUri));

// Save the file in the designated folder
file_put_contents($folderPath . '/' . $filename, $data); // Concatenated folderPath with filename

echo json_encode(['filename' => $filename]);
?>
