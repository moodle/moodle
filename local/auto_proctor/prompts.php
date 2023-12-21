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

require_once(__DIR__ . '/../../config.php'); // Setup moodle global variable also
require_login();
// Get the global $DB object
global $DB, $PAGE;

require_once($CFG->libdir . '/outputrenderers.php');

// Get required parameters
$attemptId = optional_param('attempt', 0, PARAM_INT);
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url(url:'/local/auto_proctor/prompts.php')); // Set url

// Check if consent was given
$consent = optional_param('consent', 0, PARAM_INT);
if ($consent) {
    // Perform actions when consent is given, e.g., update a database or set a session variable
    // Replace the following line with your actual logic
    echo '<p>Consent given. Implement your logic here.</p>';
} else {
    // Display a message or additional options if consent is not given
    echo '<button onclick="giveConsent()">Give Consent</button>';
}

// Add JavaScript function to give consent and redirect
echo '<script>
    function giveConsent() {
        // Assuming $attemptId is the attempt identifier
        window.location.href = "' . $CFG->wwwroot . '/local/auto_proctor/prompts.php?attempt=' . $attemptId . '&consent=1";
    }
</script>';
?>
