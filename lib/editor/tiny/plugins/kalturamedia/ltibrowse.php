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
 * Kaltura media LTI launch wrapper page.
 *
 * @module      tiny_kalturamedia
 * @copyright   2023 Roi Levi <roi.levi@kaltura.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/config.php');
require_once($CFG->dirroot.'/local/kaltura/locallib.php');
require_once('renderer.php');

global $PAGE;

require_login();

$contextid = required_param('contextid', PARAM_INT);

$PAGE->set_pagelayout('popup');
$PAGE->set_url('/editor/tiny/plugins/kalturamedia/lti_launch.php');
$PAGE->set_context(context_system::instance());

echo $OUTPUT->header();

echo html_writer::script('', 'js/ltidialoglib.js');

echo tiny_kalturamedia_preview_embed_form($contextid);

$urlparams = array(
    'withblocks' => 0,
    'width' => KALTURA_PANEL_WIDTH,
    'height' => KALTURA_PANEL_HEIGHT
);
$url = new moodle_url('/lib/editor/tiny/plugins/kalturamedia/lti_launch.php', $urlparams);

$params = array(
    'ltilaunchurl' => $url->out(),
    'objecttagheight' => TINY_KALTURAMEDIA_OBJECT_TAG_HEIGHT,
    'objecttagid' => TINY_KALTURAMEDIA_OBJECT_TAG_ID,
    'previewiframeid' => TINY_KALTURAMEDIA_PREVIEW_IFRAME_TAG_ID
);
$PAGE->requires->yui_module('moodle-local_kaltura-ltitinymcepanel', 'M.local_kaltura.init', array($params), null, true);

echo $OUTPUT->footer();
