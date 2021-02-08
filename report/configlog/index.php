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
 * Config changes report
 *
 * @package    report
 * @subpackage configlog
 * @copyright  2009 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

// Allow searching by setting when providing parameter directly.
$search = optional_param('search', '', PARAM_TEXT);

admin_externalpage_setup('reportconfiglog', '', ['search' => $search], '', ['pagelayout' => 'report']);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('configlog', 'report_configlog'));

/** @var cache_session $cache */
$cache = cache::make_from_params(cache_store::MODE_SESSION, 'report_customlog', 'search');

if (!empty($search)) {
    $searchdata = (object) ['setting' => $search];
} else {
    $searchdata = $cache->get('data');
}

$mform = new \report_configlog\form\search();
$mform->set_data($searchdata);

$searchclauses = [];

// Check if we have a form submission, or a cached submission.
$data = ($mform->is_submitted() ? $mform->get_data() : fullclone($searchdata));
if ($data instanceof stdClass) {
    if (!empty($data->value)) {
        $searchclauses[] = $data->value;
    }
    if (!empty($data->setting)) {
        $searchclauses[] = "setting:{$data->setting}";
    }
    if (!empty($data->user)) {
        $searchclauses[] = "user:{$data->user}";
    }
    if (!empty($data->datefrom)) {
        $searchclauses[] = "datefrom:{$data->datefrom}";
    }
    if (!empty($data->dateto)) {
        $dateto = $data->dateto + DAYSECS - 1;
        $searchclauses[] = "dateto:{$dateto}";
    }

    // Cache form submission so that it is preserved while paging through the report.
    unset($data->submitbutton);
    $cache->set('data', $data);
}

$mform->display();

$table = new \report_configlog\output\report_table(implode(' ', $searchclauses));
$table->define_baseurl($PAGE->url);

echo $PAGE->get_renderer('report_configlog')->render($table);

echo $OUTPUT->footer();