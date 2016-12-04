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
 * Page to continue after an action.
 *
 * @package    tool_lpimportcsv
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$pagetitle = get_string('pluginname', 'tool_lpimportcsv');

$context = context_system::instance();

$id = required_param('id', PARAM_INT);
$url = new moodle_url("/admin/tool/lpimportcsv/index.php");
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
$urlparams = ['competencyframeworkid' => $id, 'pagecontextid' => $context->id];
$frameworksurl = new moodle_url('/admin/tool/lp/competencies.php', $urlparams);
echo $OUTPUT->notification(get_string('competencyframeworkcreated', 'tool_lp'), 'notifysuccess');
echo $OUTPUT->continue_button($frameworksurl);

echo $OUTPUT->footer();
