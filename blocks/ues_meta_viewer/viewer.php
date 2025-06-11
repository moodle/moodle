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
 * Search form and results viewer page.
 *
 * @package    block_ues_meta_viewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get the requirements.
require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
ues::require_daos();
require_once($CFG->dirroot . '/blocks/ues_meta_viewer/lib.php');

require_login();

$supportedtypes = ues_meta_viewer::supported_types();

$type = required_param('type', PARAM_TEXT);

if (!isset($supportedtypes[$type])) {
    moodle_exception('unsupported_type', 'block_ues_meta_viewer', '', $type);
}

$supportedtype = $supportedtypes[$type];

if (!$supportedtype->can_use()) {
    moodle_exception('unsupported_type', 'block_ues_meta_viewer', '', $type);
}

$class = $supportedtype->wrapped_class();

$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 100, PARAM_INT);

$s = ues::gen_str('block_ues_meta_viewer');

$blockname = $s('pluginname');
$heading = $s('viewer', $supportedtype->name());

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_heading($blockname . ': '. $heading);
$PAGE->set_title($heading);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($heading);
$PAGE->set_title($heading);
$PAGE->set_url('/blocks/ues_meta_viewer/viewer.php', array('type' => $type));

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

$fields = ues_meta_viewer::generate_keys($type, $class, $USER);

$head = array();
$search = array();
$handlers = array();
$select = array();
$params = array('type' => $type);

foreach ($fields as $field) {
    $handler = ues_meta_viewer::handler($type, $field);

    $head[] = $handler->name();
    $search[] = $handler->html();
    $handlers[] = $handler;

    $value = $handler->value();
    $vdata = $value !== null ? $value : '';
    // Only add searched fields as GET param.
    if (trim($vdata) !== '') {
        $params[$field] = $value;
    }
}

$searchtable = new html_table();
$searchtable->head = $head;
$searchtable->data = array(new html_table_row($search));

if (!empty($_REQUEST['search'])) {
    $byfilters = ues_meta_viewer::sql($handlers);

    $count = $class::count($byfilters);
    $res = $class::get_all($byfilters, true, '', '*', $page + ($page * $perpage), $perpage);

    $params['search'] = get_string('search');

    $result = $count ?
        ues_meta_viewer::result_table($res, $handlers) :
        null;
    $posted = true;
} else {
    $count = 0;
    $posted = false;
}

$baseurl = new moodle_url('viewer.php', $params);

$hidden = function($name, $value) {
    return html_writer::empty_tag('input', array(
        'name' => $name, 'value' => $value, 'type' => 'hidden'
    ));
};

echo html_writer::start_tag('form', array('method' => 'POST', 'class' => 'search-form'));
echo html_writer::tag('div', html_writer::table($searchtable), array('class' => 'search-table'));
echo html_writer::start_tag('div', array('class' => 'search-buttons center padded'));
echo $hidden('page', 0) . $hidden('type', $type);
echo html_writer::empty_tag('input', array(
    'type' => 'submit', 'name' => 'search', 'value' => get_string('search')
));
echo html_writer::end_tag('div');
echo html_writer::end_tag('form');

if ($posted) {
    echo html_writer::start_tag('div', array('class' => 'results'));
    if (empty($result)) {
        echo html_writer::start_tag('div', array('class' => 'no-results center padded'));
        echo $s('no_results');
        echo html_writer::end_tag('div');
    } else {
        echo html_writer::tag('div', $s('found_results') . ' ' . $count,
            array('class' => 'count-results center padded'));
        echo $OUTPUT->paging_bar($count, $page, $perpage, $baseurl->out());
        echo html_writer::tag('div', html_writer::table($result),
            array('class' => 'results-table margin-center'));
    }
    echo html_writer::end_tag('div');
}

echo $OUTPUT->footer();
