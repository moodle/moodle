<?php

// This file is part of Moodle - http://moodle.org/
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
 * Lists the course categories
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

 require_once('../../config.php');
 
 // Set up the page context
 $PAGE->set_context(context_system::instance());
 $PAGE->set_url('/local/esupervision/index.php');
 $PAGE->set_pagelayout('course');
 $PAGE->set_title('Dashboard');
 
 // Output the content of the page
 echo $OUTPUT->header();
 echo '<div>
        <ulclass="list-unstyled">
        <li ><a>Project</a></li>
        <li><a>Messages</a></li>
        <li><a>Announcement</a></li>
        <li><a>Chat</a></li>
        <li><a>wiki log</a></li>
        </ul>
 </div>';
 echo $OUTPUT->footer(); 
 ?>