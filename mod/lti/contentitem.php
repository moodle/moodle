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
 * Display a page containing an iframe for the content-item selection process.
 *
 * @package mod_lti
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/lti/lib.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

$courseid = required_param('course', PARAM_INT);
$sectionid = required_param('section', PARAM_INT);
$id = required_param('id', PARAM_INT);
$sectionreturn = required_param('sr', PARAM_INT);

$title = optional_param('title', null, PARAM_TEXT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

require_login($course);

$url = new moodle_url('/mod/lti/contentitem.php', array('course' => $courseid));

$contentitem = new moodle_url('/mod/lti/contentitem2.php',
    array('course' => $courseid, 'section' => $sectionid, 'id' => $id, 'sr' => $sectionreturn, 'title' => $title));

echo "<p id=\"id_warning\" style=\"display: none; color: red; font-weight: bold; margin-top: 1em; padding-top: 1em;\">\n";
echo get_string('register_warning', 'lti');
echo "\n</p>\n";

echo '<iframe id="contentframe" height="600px" width="100%" src="' . $contentitem->out() . '" onload="doOnload()"></iframe>';

// Output script to make the object tag be as large as possible.
$resize = '
        <script type="text/javascript">
        //<![CDATA[
            function doReveal() {
              var el = document.getElementById(\'id_warning\');
              el.style.display = \'block\';
            }
            function doOnload() {
                window.clearTimeout(mod_lti_timer);
                parent.M.mod_lti.editor.removeLoading();
            }
            var mod_lti_timer = window.setTimeout(doReveal, 20000);
            parent.YUI().use("node", "event", function(Y) {
                //Take scrollbars off the outer document to prevent double scroll bar effect
                var doc = parent.Y.one("body");
                doc.setStyle("overflow", "hidden");

                var frame = parent.Y.one("#contentframe");
                var padding = 15; //The bottom of the iframe wasn\'t visible on some themes. Probably because of border widths, etc.
                var lastHeight;
                var resize = function(e) {
                    var viewportHeight = doc.get("winHeight");
                    if(lastHeight !== Math.min(doc.get("docHeight"), viewportHeight)){
                        frame.setStyle("height", viewportHeight - frame.getY() - padding + "px");
                        lastHeight = Math.min(doc.get("docHeight"), doc.get("winHeight"));
                    }
                };

                resize();

                parent.Y.on("windowresize", resize);
            });
        //]]
        </script>
';

echo $resize;
