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
 *
 * @package   tinymce_moodlemedia
 * @copyright 1999 onwards Martin Dougiamas   {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../../../config.php');
require_once($CFG->libdir . '/filelib.php');

// Decode the url - it can not be passed around unencoded because security filters might block it.
$media = required_param('media', PARAM_RAW);
$media = base64_decode($media);
$url = clean_param($media, PARAM_URL);
$url = new moodle_url($url);

// Now output this file which is super-simple
$PAGE->set_pagelayout('embedded');
$PAGE->set_url(new moodle_url('/lib/editor/tinymce/plugins/moodlemedia/preview.php'));
$PAGE->set_context(context_system::instance());
$PAGE->add_body_class('core_media_preview');

echo $OUTPUT->header();

$mediarenderer = core_media_manager::instance($PAGE);

if (isloggedin() and !isguestuser() and $mediarenderer->can_embed_url($url)) {
    require_sesskey();
    echo $mediarenderer->embed_url($url);
} else {
    print_string('nopreview', 'tinymce_moodlemedia');
}

echo $OUTPUT->footer();
