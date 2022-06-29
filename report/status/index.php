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
 * System Status report
 *
 * @package    report_status
 * @copyright  2020 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('reportstatus', '', null, '', ['pagelayout' => 'report']);

$detail = optional_param('detail', '', PARAM_TEXT); // Show detailed info about one check only.

$url = '/report/status/index.php';
$table = new core\check\table('status', $url, $detail);

if (!empty($table->detail)) {
    $PAGE->set_docs_path($url . '?detail=' . $detail);
    $PAGE->navbar->add($table->detail->get_name());
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_status'));
echo $table->render($OUTPUT);
echo $OUTPUT->footer();

