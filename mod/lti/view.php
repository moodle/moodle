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
//
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

/**
 * This file contains all necessary code to view a lti activity instance
 *
 * @package    mod
 * @subpackage lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Chris Scribner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/lti/lib.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$l  = optional_param('l', 0, PARAM_INT);  // lti ID

if ($l) {  // Two ways to specify the module
    $lti = $DB->get_record('lti', array('id' => $l), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('lti', $lti->id, $lti->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('lti', $id, 0, false, MUST_EXIST);
    $lti = $DB->get_record('lti', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

$tool = lti_get_tool_by_url_match($lti->toolurl);
if ($tool) {
    $toolconfig = lti_get_type_config($tool->id);
} else {
    $toolconfig = array();
}

$PAGE->set_cm($cm, $course); // set's up global $COURSE
$context = context_module::instance($cm->id);
$PAGE->set_context($context);

$url = new moodle_url('/mod/lti/view.php', array('id'=>$cm->id));
$PAGE->set_url($url);

$launchcontainer = lti_get_launch_container($lti, $toolconfig);

if ($launchcontainer == LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS) {
    $PAGE->set_pagelayout('frametop'); //Most frametops don't include footer, and pre-post blocks
    $PAGE->blocks->show_only_fake_blocks(); //Disable blocks for layouts which do include pre-post blocks
} else if ($launchcontainer == LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW) {
    redirect('launch.php?id=' . $cm->id);
} else {
    $PAGE->set_pagelayout('incourse');
}

require_login($course);

add_to_log($course->id, "lti", "view", "view.php?id=$cm->id", "$lti->id");

$pagetitle = strip_tags($course->shortname.': '.format_string($lti->name));
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);

// Print the page header
echo $OUTPUT->header();

if ($lti->showtitlelaunch) {
    // Print the main part of the page
    echo $OUTPUT->heading(format_string($lti->name));
}

if ($lti->showdescriptionlaunch && $lti->intro) {
    echo $OUTPUT->box($lti->intro, 'generalbox description', 'intro');
}

if ( $launchcontainer == LTI_LAUNCH_CONTAINER_WINDOW ) {
    echo "<script language=\"javascript\">//<![CDATA[\n";
    echo "window.open('launch.php?id=".$cm->id."','lti');";
    echo "//]]\n";
    echo "</script>\n";
    echo "<p>".get_string("basiclti_in_new_window", "lti")."</p>\n";
} else {
    // Request the launch content with an object tag
    echo '<object id="contentframe" height="600px" width="100%" type="text/html" data="launch.php?id='.$cm->id.'"></object>';

    //Output script to make the object tag be as large as possible
    $resize = '
        <script type="text/javascript">
        //<![CDATA[
            YUI().use("yui2-dom", function(Y) {
                //Take scrollbars off the outer document to prevent double scroll bar effect
                document.body.style.overflow = "hidden";

                var dom = Y.YUI2.util.Dom;
                var frame = document.getElementById("contentframe");

                var padding = 15; //The bottom of the iframe wasn\'t visible on some themes. Probably because of border widths, etc.

                var lastHeight;

                var resize = function(){
                    var viewportHeight = dom.getViewportHeight();

                    if(lastHeight !== Math.min(dom.getDocumentHeight(), viewportHeight)){

                        frame.style.height = viewportHeight - dom.getY(frame) - padding + "px";

                        lastHeight = Math.min(dom.getDocumentHeight(), dom.getViewportHeight());
                    }
                };

                resize();

                setInterval(resize, 250);
            });
        //]]
        </script>
';

    echo $resize;
}

// Finish the page
echo $OUTPUT->footer();
