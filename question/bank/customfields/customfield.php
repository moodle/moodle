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
 * Manage question custom fields
 *
 * @package     qbank_customfields
 * @copyright   2021 Catalyst IT Australia Pty Ltd
 * @author      Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../..//config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('qbank_customfields');

$output = $PAGE->get_renderer('core_customfield');
$customfieldhandler = qbank_customfields\customfield\question_handler::create();
$outputpage = new \core_customfield\output\management($customfieldhandler);

echo $output->header(),
     $output->heading(new lang_string('pluginname', 'qbank_customfields')),
     $output->render($outputpage),
     $output->footer();
