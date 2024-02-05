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

if (isset($_POST['evidence_name_type'])) {
    $filename = $_POST['filename'];
    $evidence_name_type = $_POST['evidence_name_type']; // Ensure it's an integer

    switch ($evidence_name_type) {
        case 'no_face':
            $evidence_name_type = 7;
            break;
        case 'multiple_face':
            $evidence_name_type = 8;
            break;
        case 'suspicious_movement':
            $evidence_name_type = 9;
            break;
        // default:
    }
    
    echo "evdtype: " . $evidence_name_type;
    echo "</br>";
    echo "filename: " . $filename;
}
?>