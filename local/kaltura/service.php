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
 * Kaltura LTI service script used receive data sent from the Kaltura content provider.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->dirroot.'/local/kaltura/locallib.php');

require_login();

global $PAGE;

$url = required_param('url', PARAM_URL);
$width = required_param('width', PARAM_INT);
$height = required_param('height', PARAM_INT);
$entryid = required_param('entry_id', PARAM_TEXT);
$title = required_param('title', PARAM_TEXT);
$thumbnailurl = optional_param('thumbnailUrl', '', PARAM_URL);
$duration = optional_param('duration', '', PARAM_TEXT);
$description = optional_param('description', '', PARAM_TEXT);
$createdat = optional_param('createdAt', '', PARAM_TEXT);
$owner = optional_param('owner', '', PARAM_TEXT);
$tags = optional_param('tags', '', PARAM_TEXT);
$showtitle = optional_param('showTitle', '', PARAM_TEXT);
$showdescription = optional_param('showDescription', '', PARAM_TEXT);
$showtags = optional_param('showTags', '', PARAM_TEXT);
$showduration = optional_param('showDuration', '', PARAM_TEXT);
$showowner = optional_param('showOwner', '', PARAM_TEXT);
$player = optional_param('player', '', PARAM_TEXT);
$size = optional_param('size', '', PARAM_TEXT);
$editor = optional_param('editor', 'tinymce', PARAM_TEXT);


$serviceurl = new moodle_url('/local/kaltura/service.php');

// Log the request.
$enablelogging = get_config(KALTURA_PLUGIN_NAME, 'enable_logging');
if (!empty($enablelogging)) {
    $param = array(
        'url' => $url,
        'width' => $width,
        'height' => $height,
        'entryid'  => $entryid,
        '$title' => $title
    );
    local_kaltura_log_data(KAF_BROWSE_EMBED_MODULE, $serviceurl->out(), $param, false);
}

// Create a metadata object and serialize it.
$metadata = new stdClass();
$metadata->url = $url;
$metadata->width = $width;
$metadata->height = $height;
$metadata->entryid = $entryid;
$metadata->title = $title;
$metadata->thumbnailurl = $thumbnailurl;
$metadata->duration = $duration;
$metadata->description = $description;
$metadata->createdat = $createdat;
$metadata->owner = $owner;
$metadata->tags = $tags;
$metadata->showtitle = $showtitle;
$metadata->showdescription = $showdescription;
$metadata->showduration = $showduration;
$metadata->showowner = $showowner;
$metadata->player = $player;
$metadata->size = $size;

$metadata = local_kaltura_encode_object_for_storage($metadata);

$PAGE->set_url($serviceurl);
$PAGE->set_context(context_system::instance());
$previewltilaunchurl = new moodle_url('/local/kaltura/bsepreview_ltilaunch.php?playurl=' . urlencode($url));
$params = array(
    'iframeurl' => urlencode($url),
    'width' => $width,
    'height' => $height,
    'entryid' => $entryid,
    'title' => $title,
    'metadata' => $metadata,
    'editor' => $editor,
    'previewltilauncher' => $previewltilaunchurl->out(),
);
if($editor == 'atto')
{
    require_once('attobsepreview.php');
}
else
{
    $PAGE->requires->yui_module('moodle-local_kaltura-ltiservice', 'M.local_kaltura.init', array($params));

    echo $OUTPUT->header();
    echo $OUTPUT->footer();
}