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
 * Provides A/V preview features for the TinyMCE editor Moodle Media plugin.
 * The preview is included in an iframe within the popup dialog.
 * @package editor
 * @subpackage tinymce
 * @copyright 1999 onwards Martin Dougiamas   {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__) . '/../../../../../../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/editorlib.php');
require_once($CFG->libdir . '/editor/tinymce/lib.php');

// Must be logged in
require_login();

// Require path to draftfile.php file
$path = required_param('path', PARAM_PATH);

$editor = new tinymce_texteditor();

// Now output this file which is super-simple
$PAGE->set_pagelayout('embedded');
$PAGE->set_url(new moodle_url('/lib/editor/tinymce/tiny_mce/'.$editor->version.'/plugins/moodlemedia/preview.php',
        array('path' => $path)));
$PAGE->set_context(context_system::instance());
$PAGE->add_body_class('core_media_preview');

echo $OUTPUT->header();

$mediarenderer = $PAGE->get_renderer('core', 'media');

$path = '/'.trim($path, '/');

if (empty($CFG->slasharguments)) {
    $url = new moodle_url('/draftfile.php', array('file'=>$path));
} else {
    $url = new moodle_url('/draftfile.php');
    $url->set_slashargument($path);
}
if ($mediarenderer->can_embed_url($url)) {
    echo $mediarenderer->embed_url($url);
}

echo $OUTPUT->footer();
