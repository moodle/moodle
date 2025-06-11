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
 * Handles content item return.
 *
 * @package    tiny_panoptoltibutton
 * @copyright  2023 Panopto
 * @author     Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../../config.php');
require_once($CFG->dirroot . '/blocks/panopto/lib/lti/panoptoblock_lti_utility.php');
require_once($CFG->dirroot . '/mod/lti/lib.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');

$courseid = required_param('course', PARAM_INT);
$id = required_param('id', PARAM_INT);
$callback = required_param('callback', PARAM_ALPHANUMEXT);
$jwt = optional_param('JWT', '', PARAM_RAW);

require_login($courseid);

$context = context_course::instance($courseid);

$config = lti_get_type_type_config($id);
$islti1p3 = $config->lti_ltiversion === LTI_VERSION_1P3;

if (!empty($jwt)) {
    $params = lti_convert_from_jwt($id, $jwt);
    $consumerkey = $params['oauth_consumer_key'] ?? '';
    $messagetype = $params['lti_message_type'] ?? '';
    $items = $params['content_items'] ?? '';
    $version = $params['lti_version'] ?? '';
    $errormsg = $params['lti_errormsg'] ?? '';
    $msg = $params['lti_msg'] ?? '';
} else {
    $consumerkey = required_param('oauth_consumer_key', PARAM_RAW);
    $messagetype = required_param('lti_message_type', PARAM_TEXT);
    $version = required_param('lti_version', PARAM_TEXT);
    $items = optional_param('content_items', '', PARAM_RAW_TRIMMED);
    $errormsg = optional_param('lti_errormsg', '', PARAM_TEXT);
    $msg = optional_param('lti_msg', '', PARAM_TEXT);
}

$contentitems = json_decode($items);

$errors = [];

// Affirm that the content item is a JSON object.
if (!is_object($contentitems) && !is_array($contentitems)) {
    $errors[] = 'invalidjson';
}

if ($islti1p3) {
    // Update content items data if this is lti 1.3 and not embed.
    $doctarget = $contentitems->{'@graph'}[0]->placementAdvice->presentationDocumentTarget
                    ? $contentitems->{'@graph'}[0]->placementAdvice->presentationDocumentTarget
                    : ($contentitems->{'@graph'}[0]->iframe ? "iframe" : "frame");
    $thumbnail = $contentitems->{'@graph'}[0]->thumbnail;
    if ($doctarget == 'iframe' && !empty($thumbnail)) {
        $contentitems->{'@graph'}[0]->placementAdvice->presentationDocumentTarget = 'frame';
        $contentitems->{'@graph'}[0]->placementAdvice->windowTarget = '_blank';
        $contentitems->{'@graph'}[0]->{'@type'} = 'ContentItem';
        $contentitems->{'@graph'}[0]->mediaType = 'text/html';
    }
}

?>

<script type="text/javascript">
    <?php if (count($errors) > 0): ?>
        parent.document.CALLBACKS.handleError(<?php echo json_encode($errors); ?>);
    <?php else: ?>
        parent.document.CALLBACKS.<?php echo $callback ?>(<?php echo json_encode($contentitems) ?>);
    <?php endif; ?>
</script>
