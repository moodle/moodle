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
 * Forwards the user to the action they selected.
 *
 * @package    report_insights
 * @copyright  2019 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$actionvisiblename = required_param('actionvisiblename', PARAM_NOTAGS);

$PAGE->set_pagelayout('popup');
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_site()->fullname);
$PAGE->set_url(new \moodle_url('/report/insights/done.php'));

echo $OUTPUT->header();

$notification = new \core\output\notification(get_string('actionsaved', 'report_insights', $actionvisiblename),
    \core\output\notification::NOTIFY_SUCCESS);
$notification->set_show_closebutton(false);
echo $OUTPUT->render($notification);

echo $OUTPUT->footer();
