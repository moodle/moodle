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
 * Displays personal tags for a course with some editing facilites
 *
 * @package    core_tag
 * @category   tag
 * @copyright  2007 j.beedell@open.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/tag/coursetagslib.php');
require_once($CFG->dirroot.'/tag/lib.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$keyword = optional_param('coursetag_new_tag', '', PARAM_TEXT);
$deltag = optional_param('del_tag', 0, PARAM_INT);

$url = new moodle_url('/tag/coursetags_edit.php');
if ($courseid !== 0) {
    $url->param('courseid', $courseid);
}
if ($keyword !== '') {
    $url->param('coursetag_new_tag', $keyword);
}
if ($deltag !== 0) {
    $url->param('del_tag', $deltag);
}
$PAGE->set_url($url);

require_login();

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

if ($courseid != SITEID) {
    if (! ($course = $DB->get_record('course', array('id' => $courseid), '*')) ) {
        print_error('invalidcourse');
    }
} else {
    print_error('errortagfrontpage', 'tag');
}

// Permissions
$sitecontext = context_system::instance();
require_login($course);
$canedit = has_capability('moodle/tag:create', $sitecontext);

// Language strings
$tagslang = 'block_tags';

// Store data
if ($data = data_submitted()) {
    if (confirm_sesskey() and $courseid > 0 and $USER->id > 0 and $canedit) {
        // store personal tag
        if (trim(strip_tags($keyword))) {
            $myurl = 'tag/search.php';
            $keywords = explode(',', $keyword);
            coursetag_store_keywords($keywords, $courseid, $USER->id, 'default', $myurl);
        }
        // delete personal tag
        if ($deltag > 0) {
            coursetag_delete_keyword($deltag, $USER->id, $courseid);
        }
    }
}

// The title and breadcrumb
$title = get_string('edittitle', $tagslang);
$coursefullname = format_string($course->fullname);
$courseshortname = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(false);
echo $OUTPUT->header();

    // Print personal tags for all courses
    $title = get_string('edittitle', $tagslang);
    echo $OUTPUT->heading($title, 2, 'mdl-align');

    $mytags = tag_print_cloud(coursetag_get_tags(0, $USER->id, 'default'), 150, true);
    $outstr = '
        <div class="coursetag_edit_centered">
            <div>
                '.get_string('editmytags', $tagslang).'
            </div>
            <div>';

    if ($mytags) {
        $outstr .= $mytags;
    } else {
        $outstr .= get_string('editnopersonaltags', $tagslang);
    }

    $outstr .= '
            </div>
        </div>';
    echo $outstr;

    // Personal tag editing
    if ($canedit) {
        $title = get_string('editmytagsfor', $tagslang, '"'.$coursefullname.' ('.$courseshortname.')"');
        echo $OUTPUT->heading($title, 2, 'main mdl-align');

        // Deletion here is open to the users own tags for this course only
        $selectoptions = '<option value="0">'.get_string('select', $tagslang).'</option>';
        $coursetabs = '';
        if ($options = coursetag_get_records($courseid, $USER->id)) {
            $coursetabs = '"';
            foreach ($options as $option) {
                $selectoptions .= '<option value="'.$option->id.'">'.$option->rawname.'</option>';
                $coursetabs .= $option->rawname . ', ';
            }
            $coursetabs = rtrim($coursetabs, ', ');
            $coursetabs .= '"';
        }
        if ($coursetabs) {
            $outstr = '
            <div class="coursetag_edit_centered">
                '.get_string('editthiscoursetags', $tagslang, $coursetabs).'
            </div>';
        } else {
            $outstr = '
            <div class="coursetag_edit_centered">
                '.get_string('editnopersonaltags', $tagslang).'
            </div>';
        }

        // Print the add and delete form
        coursetag_get_jscript();
        $edittagthisunit = get_string('edittagthisunit', $tagslang);
        $suggestedtagthisunit = get_string('suggestedtagthisunit', $tagslang);
        $arrowtitle = get_string('arrowtitle', $tagslang);
        $sesskey = sesskey();
        $leftarrow = $OUTPUT->pix_url('t/arrow_left');
        $outstr .= <<<EOT
            <form action="$CFG->wwwroot/tag/coursetags_edit.php" method="post" id="coursetag">
                <div style="display: none;">
                    <input type="hidden" name="courseid" value="$course->id" />
                    <input type="hidden" name="sesskey" value="$sesskey" />
                </div>
                <div class="coursetag_edit_centered">
                    <div class="coursetag_edit_row">
                        <div class="coursetag_edit_left">
                            <label class="accesshide" for="coursetag_sug_keyword">$suggestedtagthisunit</label>
                        </div>
                        <div class="coursetag_edit_right">
                            <div class="coursetag_form_input1">
                                <input type="text" name="coursetag_sug_keyword" id="coursetag_sug_keyword" class="coursetag_form_input1a" disabled="disabled" />
                            </div>
                        </div>
                    </div>
                    <div class="coursetag_edit_row">
                        <div class="coursetag_edit_left">
                            <label for="coursetag_new_tag">$edittagthisunit</label>
                        </div>
                        <div class="coursetag_edit_right">
                            <div class="coursetag_form_input2">
                                <input type="text" name="coursetag_new_tag" id="coursetag_new_tag" class="coursetag_form_input2a"
                                    onfocus="ctags_getKeywords()" onkeyup="ctags_getKeywords()" maxlength="50" />
                            </div>
                            <div class="coursetag_edit_input3" id="coursetag_sug_btn">
                                <a title="$arrowtitle">
                                    <img src="$leftarrow" width="10" height="10" alt="enter" onclick="ctags_setKeywords()" />
                                </a>
                            </div>
                        </div>
                    </div>
EOT;
        if ($coursetabs) {
            $editdeletemytag = get_string('editdeletemytag', $tagslang);
            $outstr .= <<<EOT1
                    <div class="coursetag_edit_row">
                        <div class="coursetag_edit_left">
                            <label for="del_tag">
                                $editdeletemytag
                            </label>
                        </div>
                        <div class="coursetag_edit_right">
                            <select id="del_tag" name="del_tag">
                                $selectoptions
                            </select>
                        </div>
                    </div>
EOT1;
        }
        $submitstr = get_string('submit');
        $outstr .= <<<EOT2
                    <div class="clearer"></div>
                    <div class="coursetag_edit_row">
                        <button type="submit">$submitstr</button>
                    </div>
                </div>
            </form>
EOT2;
        echo $outstr;
    }

echo $OUTPUT->footer();
