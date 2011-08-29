<?php
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains all necessary code to view a basiclti activity instance
 *
 * @package blti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/blti/lib.php');
require_once($CFG->dirroot.'/mod/blti/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // blti ID

if ($id) {
    if (! $cm = get_coursemodule_from_id("blti", $id)) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Course Module ID was incorrect');
    }

    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Course is misconfigured');
    }

    if (! $basiclti = $DB->get_record("blti", array("id" => $cm->instance))) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Course module is incorrect');
    }

} else {
    if (! $basiclti = $DB->get_record("blti", array("id" => $a))) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Course module is incorrect');
    }
    if (! $course = $DB->get_record("course", array("id" => $basiclti->course))) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance("blti", $basiclti->id, $course->id)) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', 'Course Module ID was incorrect');
    }
}

$PAGE->set_cm($cm, $course); // set's up global $COURSE
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$PAGE->set_context($context);

$url = new moodle_url('/mod/blti/view.php', array('id'=>$cm->id));
$PAGE->set_url($url);
$PAGE->set_pagelayout('incourse');
require_login($course);

add_to_log($course->id, "blti", "view", "view.php?id=$cm->id", "$basiclti->id");

$pagetitle = strip_tags($course->shortname.': '.format_string($basiclti->name));
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);

/// Print the page header
echo $OUTPUT->header();

/// Print the main part of the page
echo $OUTPUT->heading(format_string($basiclti->name));
echo $OUTPUT->box($basiclti->intro, 'generalbox description', 'intro');

if ($basiclti->instructorchoiceacceptgrades == 1) {
    echo '<div class="reportlink">'.submittedlink($cm).'</div>';
}

echo $OUTPUT->box_start('generalbox activity');


if ( false /*$basiclti->launchinpopup > 0*/ ) {
    print "<script language=\"javascript\">//<![CDATA[\n";
    print "window.open('launch.php?id=".$cm->id."','window name');";
    print "//]]\n";
    print "</script>\n";
    print "<p>".get_string("basiclti_in_new_window", "blti")."</p>\n";
} else {
    // Request the launch content with an object tag
    /*$height = $basiclti->preferheight;
    if ((!$height) || ($height == 0)) {
        $height = 400;
    }*/
    $height=600;
    print '<object height="'.$height.'" width="100%" data="launch.php?id='.$cm->id.'&amp;withobject=true"></object>';

}

echo $OUTPUT->box_end();

/// Finish the page
echo $OUTPUT->footer();
