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

// PHP script to handle saving the screen capture

// Get the data URI from the POST request
$dataUri = $_POST['dataUri'];
$generated_filename = $_POST['filename'];

// Remove the "data:image/png;base64," prefix
$data = base64_decode(preg_replace('#^data:image/png;base64,#', '', $dataUri));

// Specify the folder for saving captures
$folderPath = '../evidences/screen_capture_evidence/';

// Ensure the folder exists
if (!file_exists($folderPath)) {
    mkdir($folderPath, 0777, true);
}

// Generate a unique filename (e.g., based on timestamp)
$filename = $folderPath . $generated_filename;

// Save the file
file_put_contents($filename, $data);

// Respond with the filename for any further use
echo json_encode(['filename' => $filename]);
?>
