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
 * Kaltura media LTI launch page.
 *
 * @package    tinymce_kalturamedia
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))).'/config.php');
require_once($CFG->dirroot.'/local/kaltura/locallib.php');
require_once('renderer.php');

global $PAGE;

require_login();

$PAGE->set_pagelayout('popup');
$PAGE->set_url('/editor/tinymce/plugins/kalturamedia/tinymce/ltilaunch.php');
$PAGE->set_context(context_system::instance());
$PAGE->requires->css('/lib/editor/tinymce/plugins/kalturamedia/tinymce/css/kalturaltipopup.css');

echo $OUTPUT->header();

$editor = get_texteditor('tinymce');
$tinymcebaseurl = $editor->get_tinymce_base_url();

echo html_writer::script('', $tinymcebaseurl.'tiny_mce_popup.js');
echo html_writer::script('', $tinymcebaseurl.'utils/validate.js');
echo html_writer::script('', $tinymcebaseurl.'utils/form_utils.js');
echo html_writer::script('', $tinymcebaseurl.'utils/editable_selects.js');
echo html_writer::script('', 'js/ltipopuplib.js');

echo tinymce_kalturamedia_preview_embed_form();

$urlparams = array(
    'withblocks' => 0,
    'width' => KALTURA_PANEL_WIDTH,
    'height' => KALTURA_PANEL_HEIGHT
);

$url = new moodle_url('/lib/editor/tinymce/plugins/kalturamedia/lti_launch.php', $urlparams);

$params = array(
    'ltilaunchurl' => $url->out(),
    'objecttagheight' => TINMCE_KALTURAMEDIA_OBJECT_TAG_HEIGHT,
    'objecttagid' => TINMCE_KALTURAMEDIA_OBJECT_TAG_ID,
    'previewiframeid' => TINMCE_KALTURAMEDIA_PREVIEW_IFRAME_TAG_ID
);

$PAGE->requires->yui_module('moodle-local_kaltura-ltitinymcepanel', 'M.local_kaltura.init', array($params), null, true);

echo $OUTPUT->footer();
