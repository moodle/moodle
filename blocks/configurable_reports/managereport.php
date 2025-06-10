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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot . "/blocks/configurable_reports/locallib.php");
require_once('import_form.php');

$courseid = optional_param('courseid', SITEID, PARAM_INT);
$importurl = optional_param('importurl', '', PARAM_RAW);

if (!$course = $DB->get_record("course", ['id' => $courseid])) {
    throw new moodle_exception("No such course id");
}

// Force user login in course (SITE or Course).
if ($course->id == SITEID) {
    require_login();
    $context = context_system::instance();
} else {
    require_login($course->id);
    $context = context_course::instance($course->id);
}

if (!has_capability('block/configurable_reports:managereports', $context) &&
    !has_capability('block/configurable_reports:manageownreports', $context)) {
    throw new moodle_exception('badpermissions');
}

$PAGE->set_url('/blocks/configurable_reports/managereport.php', ['courseid' => $course->id]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

if ($importurl) {
    $c = new curl();
    if ($data = $c->get($importurl)) {
        $data = json_decode($data);
        $xml = base64_decode($data->content);
    } else {
        throw new moodle_exception('errorimporting');
    }

    if (cr_import_xml($xml, $course)) {
        redirect(
            "$CFG->wwwroot/blocks/configurable_reports/managereport.php?courseid={$course->id}",
            get_string('reportcreated', 'block_configurable_reports')
        );
    } else {
        throw new moodle_exception('errorimporting');
    }
}

$mform = new import_form(null, $course->id);

if ($data = $mform->get_data()) {
    if ($xml = $mform->get_file_content('userfile')) {
        if (cr_import_xml($xml, $course)) {
            redirect(
                "$CFG->wwwroot/blocks/configurable_reports/managereport.php?courseid={$course->id}",
                get_string('reportcreated', 'block_configurable_reports')
            );
        } else {
            throw new moodle_exception('errorimporting');
        }
    }
}

$reports = cr_get_my_reports($course->id, $USER->id);

$title = get_string('reports', 'block_configurable_reports');

$PAGE->navbar->add(get_string('managereports', 'block_configurable_reports'));

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_cacheable(true);
$jsmodule = [
    'name' => 'block_configurable_reports',
    'fullpath' => '/blocks/configurable_reports/js/configurable_reports.js',
    'requires' => ['io'],
];
$PAGE->requires->js_init_call('M.block_configurable_reports.loadReportCategories', null, false, $jsmodule);

echo $OUTPUT->header();

if ($reports) {
    $table = new stdclass;
    $table->width = "100%";
    $table->head = [
        get_string('name'),
        get_string('reportsmanage', 'admin') . ' ' . get_string('course'),
        get_string('type', 'block_configurable_reports'),
        get_string('username'),
        get_string('edit'),
        get_string('download', 'block_configurable_reports'),
    ];
    $table->align = ['left', 'left', 'left', 'left', 'center', 'center'];
    $table->size = ['30%', '10%', '10%', '10%', '20%', '20%'];
    $stredit = get_string('edit');
    $strdelete = get_string('delete');
    $strhide = get_string('hide');
    $strshow = get_string('show');
    $strcopy = get_string('duplicate');
    $strexport = get_string('exportreport', 'block_configurable_reports');

    foreach ($reports as $r) {
        if ($r->courseid == 1) {
            $coursename = '<a href="' . $CFG->wwwroot . '">' . get_string('site') . '</a>';
        } else if (!$coursename = $DB->get_field('course', 'fullname', ['id' => $r->courseid])) {
            $coursename = get_string('deleted');
        } else {
            $coursename = format_string($coursename);
            $coursename =
                '<a href="' . $CFG->wwwroot . '/blocks/configurable_reports/managereport.php?courseid=' . $r->courseid . '">' .
                $coursename . '</a>';
        }

        if ($owneruser = $DB->get_record('user', ['id' => $r->ownerid])) {
            $owner = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $r->ownerid . '">' . fullname($owneruser) . '</a>';
        } else {
            $owner = get_string('deleted');
        }
        if ($r->type === 'sql' && !block_configurable_reports_can_managesqlreports($context)) {
            $editcell = '';
        } else {
            $editcell = '<a title="' . $stredit . '"  href="editreport.php?id=' . $r->id . '">' .
                $OUTPUT->pix_icon('t/edit', $stredit) .
                '</a>&nbsp;&nbsp;';
            $editcell .= '<a title="' . $strdelete . '"  href="editreport.php?id=' . $r->id . '&amp;delete=1&amp;sesskey=' .
                $USER->sesskey . '">' .
                $OUTPUT->pix_icon('t/delete', $strdelete) .
                '</a>&nbsp;&nbsp;';

            if (!empty($r->visible)) {
                $editcell .= '<a title="' . $strhide . '" href="editreport.php?id=' . $r->id . '&amp;hide=1&amp;sesskey=' .
                    $USER->sesskey . '">' .
                    $OUTPUT->pix_icon('t/hide', $strhide) .
                    '</a> ';
            } else {
                $editcell .= '<a title="' . $strshow . '" href="editreport.php?id=' . $r->id . '&amp;show=1&amp;sesskey=' .
                    $USER->sesskey . '">' .
                    $OUTPUT->pix_icon('t/show', $strshow) .
                    '</a> ';
            }
            $editcell .= '<a title="' . $strcopy . '" href="editreport.php?id=' . $r->id . '&amp;duplicate=1&amp;sesskey=' .
                $USER->sesskey . '">' .
                $OUTPUT->pix_icon('t/copy', $strcopy) .
                '</a>&nbsp;&nbsp;';
            $editcell .= '<a title="' . $strexport . '" href="export.php?id=' . $r->id . '&amp;sesskey=' . $USER->sesskey . '">' .
                $OUTPUT->pix_icon('t/backup', $strexport) .
                '</a>&nbsp;&nbsp;';

        }

        $download = '';
        $export = explode(',', $r->export);
        if (!empty($export)) {
            foreach ($export as $e) {
                if ($e) {
                    $download .= '<a href="viewreport.php?id=' . $r->id . '&amp;download=1&amp;format=' . $e . '">' .
                        '<img src="' . $CFG->wwwroot . '/blocks/configurable_reports/export/' . $e . '/pix.gif" alt="' . $e . '">' .
                        '&nbsp;' . (strtoupper($e)) . '</a>&nbsp;&nbsp;';
                }
            }
        }

        $table->data[] = [
            '<a href="viewreport.php?id=' . $r->id . '">' . format_string($r->name) . '</a>',
            $coursename,
            get_string('report_' . $r->type, 'block_configurable_reports'),
            $owner,
            $editcell,
            $download,
        ];
    }

    $table->id = 'reportslist';
    cr_add_jsordering("#reportslist", $PAGE);
    cr_print_table($table);
} else {
    echo $OUTPUT->heading(get_string('noreportsavailable', 'block_configurable_reports'));
}

$addreporturl = $CFG->wwwroot . '/blocks/configurable_reports/editreport.php?courseid=' . $course->id;
echo $OUTPUT->heading(
    '<div class="addbutton">
                       <a href="' . $addreporturl . '" class="btn btn-secondary">' .
    get_string('addreport', 'block_configurable_reports') .
    '</a> </div>'
);

// Repository report import.
if ($userandrepo = get_config('block_configurable_reports', 'crrepository')) {
    echo html_writer::start_tag('div', ['class' => 'mform']);
    echo html_writer::start_tag('fieldset');
    echo html_writer::tag('legend', get_string('importfromrepository', 'block_configurable_reports'));

    echo $OUTPUT->help_icon('repository', 'block_configurable_reports') . "&nbsp;&nbsp;";

    $reportcategories = ['' => '...'];
    echo get_string('categories', 'block_configurable_reports');

    $attrs = [
        'onchange' => 'M.block_configurable_reports.onchange_crreportcategories(this,"' . sesskey() . '")',
        'id' => 'id_crreportcategories',
    ];
    echo html_writer::select($reportcategories, 'crreportcategories', '', null, $attrs);
    echo get_string('report', 'block_configurable_reports');

    $attrs = [
        'onchange' => 'M.block_configurable_reports.onchange_crreportnames(this,"' . sesskey() . '")',
        'id' => 'id_crreportnames',
    ];
    echo html_writer::select([], 'crreportnames', '', [], $attrs);

    echo html_writer::end_tag('fieldset');
    echo html_writer::end_tag('div');
}

$mform->display();

echo $OUTPUT->footer();
