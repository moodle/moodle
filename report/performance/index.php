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
 * Performance overview report
 *
 * @package   report_performance
 * @copyright 2013 Rajesh Taneja
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('reportperformance', '', null, '', array('pagelayout' => 'report'));

$detail = optional_param('detail', '', PARAM_TEXT); // Show detailed info about one check only.

$url = '/report/performance/index.php';
$table = new core\check\table('performance', $url, $detail);

if (!empty($table->detail)) {
    $PAGE->set_docs_path(new moodle_url($url, ['detail' => $detail]));
    $PAGE->navbar->add($table->detail->get_name());
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_performance'));
echo $table->render($OUTPUT);
echo $OUTPUT->footer();

