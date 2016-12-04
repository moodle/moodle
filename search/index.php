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
 * Global Search index page for entering queries and display of results
 *
 * @package   core_search
 * @copyright Prateek Sachan {@link http://prateeksachan.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');

$page = optional_param('page', 0, PARAM_INT);
$q = optional_param('q', '', PARAM_NOTAGS);
$title = optional_param('title', '', PARAM_NOTAGS);
// Moving areaids, courseids, timestart, and timeend further down as they might come as an array if they come from the form.

$context = context_system::instance();
$pagetitle = get_string('globalsearch', 'search');
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

if (!empty($CFG->forcelogin)) {
    require_login();
}

require_capability('moodle/search:query', $context);

$searchrenderer = $PAGE->get_renderer('core_search');

if (\core_search\manager::is_global_search_enabled() === false) {
    $PAGE->set_url(new moodle_url('/search/index.php'));
    echo $OUTPUT->header();
    echo $OUTPUT->heading($pagetitle);
    echo $searchrenderer->render_search_disabled();
    echo $OUTPUT->footer();
    exit;
}

$search = \core_search\manager::instance();

// We first get the submitted data as we want to set it all in the page URL.
$mform = new \core_search\output\form\search(null, array('searchengine' => $search->get_engine()->get_plugin_name()));

$data = $mform->get_data();
if (!$data && $q) {
    // Data can also come from the URL.

    $data = new stdClass();
    $data->q = $q;
    $data->title = $title;
    $areaids = optional_param('areaids', '', PARAM_RAW);
    if (!empty($areaids)) {
        $areaids = explode(',', $areaids);
        $data->areaids = clean_param_array($areaids, PARAM_ALPHANUMEXT);
    }
    $courseids = optional_param('courseids', '', PARAM_RAW);
    if (!empty($courseids)) {
        $courseids = explode(',', $courseids);
        $data->courseids = clean_param_array($courseids, PARAM_INT);
    }
    $data->timestart = optional_param('timestart', 0, PARAM_INT);
    $data->timeend = optional_param('timeend', 0, PARAM_INT);
    $mform->set_data($data);
}

// Set the page URL.
$urlparams = array('page' => $page);
if ($data) {
    $urlparams['q'] = $data->q;
    $urlparams['title'] = $data->title;
    if (!empty($data->areaids)) {
        $urlparams['areaids'] = implode(',', $data->areaids);
    }
    if (!empty($data->courseids)) {
        $urlparams['courseids'] = implode(',', $data->courseids);
    }
    $urlparams['timestart'] = $data->timestart;
    $urlparams['timeend'] = $data->timeend;
}
$url = new moodle_url('/search/index.php', $urlparams);
$PAGE->set_url($url);

// We are ready to render.
echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);

// Get the results.
if ($data) {
    $results = $search->paged_search($data, $page);
}

if ($errorstr = $search->get_engine()->get_query_error()) {
    echo $OUTPUT->notification(get_string('queryerror', 'search', $errorstr), 'notifyproblem');
} else if (empty($results->totalcount) && !empty($data)) {
    echo $OUTPUT->notification(get_string('noresults', 'search'), 'notifymessage');
}

$mform->display();

if (!empty($results)) {
    echo $searchrenderer->render_results($results->results, $results->actualpage, $results->totalcount, $url);

    \core_search\manager::trigger_search_results_viewed([
        'q' => $data->q,
        'page' => $page,
        'title' => $data->title,
        'areaids' => !empty($data->areaids) ? $data->areaids : array(),
        'courseids' => !empty($data->courseids) ? $data->courseids : array(),
        'timestart' => isset($data->timestart) ? $data->timestart : 0,
        'timeend' => isset($data->timeend) ? $data->timeend : 0
    ]);

}

echo $OUTPUT->footer();
