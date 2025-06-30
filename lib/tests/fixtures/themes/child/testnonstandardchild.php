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
 * Test non standard layout.
 *
 * @package     core
 * @copyright   2025 Laurent David <laurent.david@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../../../config.php");
global $CFG, $SITE, $PAGE, $OUTPUT;
require_once($CFG->dirroot. '/course/lib.php');

require_login();

$heading = $SITE->fullname;

$PAGE->set_pagelayout('standardnonavchild');
$PAGE->set_url('/theme/child/testnonstandardlayoutchild.php');
$PAGE->set_context(context_system::instance());
echo $OUTPUT->header();
echo $OUTPUT->heading($heading);
echo $OUTPUT->box('This is a test of a non-standard layout (child layout).');
echo $OUTPUT->footer();
