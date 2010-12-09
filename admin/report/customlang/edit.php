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
 * @package    report
 * @subpackage customlang
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/report/customlang/locallib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/report/customlang/filter_form.php');

require_login(SITEID, false);
require_capability('report/customlang:edit', get_system_context());

$lng                    = required_param('lng', PARAM_LANG);
$currentpage            = optional_param('p', 0, PARAM_INT);
$translatorsubmitted    = optional_param('translatorsubmitted', 0, PARAM_BOOL);

$PAGE->set_pagelayout('standard');
$PAGE->set_url('/admin/report/customlang/edit.php', array('lng' => $lng));
navigation_node::override_active_url(new moodle_url('/admin/report/customlang/index.php'));
$PAGE->set_title(get_string('pluginname', 'report_customlang'));
$PAGE->set_heading(get_string('pluginname', 'report_customlang'));
$PAGE->requires->js_init_call('M.report_customlang.init_editor', array(), true);

if (empty($lng)) {
    // PARAM_LANG validation failed
    print_error('missingparameter');
}

// pre-output processing
$filter     = new report_customlang_filter_form($PAGE->url, null, 'post', '', array('class'=>'filterform'));
$filterdata = report_customlang_utils::load_filter($USER);
$filter->set_data($filterdata);

if ($filter->is_cancelled()) {
    redirect($PAGE->url);

} elseif ($submitted = $filter->get_data()) {
    report_customlang_utils::save_filter($submitted, $USER);
    redirect(new moodle_url($PAGE->url, array('p'=>0)));
}

if ($translatorsubmitted) {
    $strings = optional_param('cust', array(), PARAM_RAW);
    $updates = optional_param('updates', array(), PARAM_INT);
    $checkin = optional_param('savecheckin', false, PARAM_RAW);

    if ($checkin === false) {
        $nexturl = $PAGE->url;
    } else {
        $nexturl = new moodle_url('/admin/report/customlang/index.php', array('action'=>'checkin', 'lng' => $lng, 'sesskey'=>sesskey()));
    }

    if (!is_array($strings)) {
        $strings = array();
    }
    $current = $DB->get_records_list('report_customlang', 'id', array_keys($strings));
    $now = time();

    foreach ($strings as $recordid => $customization) {
        $customization = trim($customization);

        if (empty($customization) and !is_null($current[$recordid]->local)) {
            $current[$recordid]->local = null;
            $current[$recordid]->modified = 1;
            $current[$recordid]->outdated = 0;
            $current[$recordid]->timecustomized = null;
            $DB->update_record('report_customlang', $current[$recordid]);
            continue;
        }

        if (empty($customization)) {
            continue;
        }

        if ($customization !== $current[$recordid]->local) {
            $current[$recordid]->local = $customization;
            $current[$recordid]->modified = 1;
            $current[$recordid]->outdated = 0;
            $current[$recordid]->timecustomized = $now;
            $DB->update_record('report_customlang', $current[$recordid]);
            continue;
        }
    }

    if (!is_array($updates)) {
        $updates = array();
    }
    if (!empty($updates)) {
        list($sql, $params) = $DB->get_in_or_equal($updates);
        $DB->set_field_select('report_customlang', 'outdated', 0, "local IS NOT NULL AND id $sql", $params);
    }

    redirect($nexturl);
}

$translator = new report_customlang_translator($PAGE->url, $lng, $filterdata, $currentpage);

// output starts here
$output     = $PAGE->get_renderer('report_customlang');
$paginator  = $output->paging_bar($translator->numofrows, $currentpage, report_customlang_translator::PERPAGE, $PAGE->url, 'p');

echo $output->header();
$filter->display();
echo $paginator;
echo $output->render($translator);
echo $paginator;
echo $output->footer();
