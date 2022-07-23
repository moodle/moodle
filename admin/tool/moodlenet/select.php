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
 * Select page.
 *
 * @package    tool_moodlenet
 * @copyright  2020 Mathew May {@link https://mathew.solutions}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use tool_moodlenet\local\import_info;
use tool_moodlenet\output\select_page;

require_once(__DIR__ . '/../../../config.php');

$id = required_param('id', PARAM_ALPHANUM);

// Access control.
require_login();
if (!get_config('tool_moodlenet', 'enablemoodlenet')) {
    throw new \moodle_exception('moodlenetnotenabled', 'tool_moodlenet');
}

if (is_null($importinfo = import_info::load($id))) {
    throw new moodle_exception('missinginvalidpostdata', 'tool_moodlenet');
}

$PAGE->set_url('/admin/tool/moodlenet/select.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('selectpagetitle', 'tool_moodlenet'));
$PAGE->set_heading(format_string($SITE->fullname));

echo $OUTPUT->header();

$renderable = new select_page($importinfo);
$renderer = $PAGE->get_renderer('tool_moodlenet');
echo $renderer->render($renderable);

echo $OUTPUT->footer();
