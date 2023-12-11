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
 * @package     local_message
 * @author      Angelica
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

require_once(__DIR__ . '/../../config.php'); // Setup moodle global variable also
global $DB;

 $PAGE->set_url(new moodle_url(url:'/local/message/manage.php')); // Set url
 $PAGE->set_context(\context_system::instance()); // Set context
 $PAGE->set_title(get_string('manage_messages', 'local_message')); // Set title

 $messages = $DB->get_records('local_message');

 echo $OUTPUT->header(); // Output header

 $templatecontext = (object)[
    'messages' => array_values($messages),
    'editurl' => new moodle_url('/local/message/edit.php'),
 ];

 echo $OUTPUT->render_from_template('local_message/manage', $templatecontext);
 echo $OUTPUT->footer();
?>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Hello bitch</h1>
</body>
</html> -->
