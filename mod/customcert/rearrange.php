<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Handles position elements on the PDF via drag and drop.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

// The page of the customcert we are editing.
$pid = required_param('pid', PARAM_INT);

$page = $DB->get_record('customcert_pages', array('id' => $pid), '*', MUST_EXIST);
$template = $DB->get_record('customcert_templates', array('id' => $page->templateid), '*', MUST_EXIST);
$elements = $DB->get_records('customcert_elements', array('pageid' => $pid), 'sequence');

// Set the template.
$template = new \mod_customcert\template($template);
// Perform checks.
if ($cm = $template->get_cm()) {
    require_login($cm->course, false, $cm);
} else {
    require_login();
}
// Make sure the user has the required capabilities.
$template->require_manage();

if ($template->get_context()->contextlevel == CONTEXT_MODULE) {
    $customcert = $DB->get_record('customcert', ['id' => $cm->instance], '*', MUST_EXIST);
    $title = $customcert->name;
    $heading = format_string($title);
} else {
    $title = $SITE->fullname;
    $heading = $title;
}

// Set the $PAGE settings.
$pageurl = new moodle_url('/mod/customcert/rearrange.php', array('pid' => $pid));
\mod_customcert\page_helper::page_setup($pageurl, $template->get_context(), $title);

// Add more links to the navigation.
if (!$cm = $template->get_cm()) {
    $str = get_string('managetemplates', 'customcert');
    $link = new moodle_url('/mod/customcert/manage_templates.php');
    $PAGE->navbar->add($str, new \action_link($link, $str));
}

$str = get_string('editcustomcert', 'customcert');
$link = new moodle_url('/mod/customcert/edit.php', array('tid' => $template->get_id()));
$PAGE->navbar->add($str, new \action_link($link, $str));

$PAGE->navbar->add(get_string('rearrangeelements', 'customcert'));

// Include the JS we need.
$PAGE->requires->yui_module('moodle-mod_customcert-rearrange', 'Y.M.mod_customcert.rearrange.init',
    array($template->get_id(),
          $page,
          $elements));

// Create the buttons to save the position of the elements.
$html = html_writer::start_tag('div', array('class' => 'buttons'));
$html .= $OUTPUT->single_button(new moodle_url('/mod/customcert/edit.php', array('tid' => $template->get_id())),
        get_string('saveandclose', 'customcert'), 'get', array('class' => 'savepositionsbtn'));
$html .= $OUTPUT->single_button(new moodle_url('/mod/customcert/rearrange.php', array('pid' => $pid)),
        get_string('saveandcontinue', 'customcert'), 'get', array('class' => 'applypositionsbtn'));
$html .= $OUTPUT->single_button(new moodle_url('/mod/customcert/edit.php', array('tid' => $template->get_id())),
        get_string('cancel'), 'get', array('class' => 'cancelbtn'));
$html .= html_writer::end_tag('div');

// Create the div that represents the PDF.
$style = 'height: ' . $page->height . 'mm; line-height: normal; width: ' . $page->width . 'mm;';
$marginstyle = 'height: ' . $page->height . 'mm; width:1px; float:left; position:relative;';
$html .= html_writer::start_tag('div', array(
    'data-templateid' => $template->get_id(),
    'data-contextid' => $template->get_contextid(),
    'id' => 'pdf',
    'style' => $style)
);
if ($page->leftmargin) {
    $position = 'left:' . $page->leftmargin . 'mm;';
    $html .= "<div id='leftmargin' style='$position $marginstyle'></div>";
}
if ($elements) {
    foreach ($elements as $element) {
        // Get an instance of the element class.
        if ($e = \mod_customcert\element_factory::get_element_instance($element)) {
            switch ($element->refpoint) {
                case \mod_customcert\element_helper::CUSTOMCERT_REF_POINT_TOPRIGHT:
                    $class = 'element refpoint-right';
                    break;
                case \mod_customcert\element_helper::CUSTOMCERT_REF_POINT_TOPCENTER:
                    $class = 'element refpoint-center';
                    break;
                case \mod_customcert\element_helper::CUSTOMCERT_REF_POINT_TOPLEFT:
                default:
                    $class = 'element refpoint-left';
            }
            $html .= html_writer::tag('div', $e->render_html(), array('class' => $class,
                'data-refpoint' => $element->refpoint, 'id' => 'element-' . $element->id));
        }
    }
}
if ($page->rightmargin) {
    $position = 'left:' . ($page->width - $page->rightmargin) . 'mm;';
    $html .= "<div id='rightmargin' style='$position $marginstyle'></div>";
}
$html .= html_writer::end_tag('div');

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);
echo $OUTPUT->heading(get_string('rearrangeelementsheading', 'customcert'), 3);
echo $OUTPUT->notification(get_string('exampledatawarning', 'customcert'), \core\output\notification::NOTIFY_WARNING);
echo $html;
$PAGE->requires->js_call_amd('mod_customcert/rearrange-area', 'init', array('#pdf'));
echo $OUTPUT->footer();
