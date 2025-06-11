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
 * @package    mod_panoptosubmission
 * @copyright  2024 Panopto
 * @author     Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/blocks/panopto/lib/lti/panoptoblock_lti_utility.php');
require_once($CFG->dirroot . '/blocks/panopto/lib/panopto_data.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/mod/lti/lib.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/mod/lti/locallib.php');

$courseid = required_param('course', PARAM_INT);
$id = required_param('id', PARAM_INT);
$jwt = optional_param('JWT', '', PARAM_RAW);

require_login($courseid);

$context = context_course::instance($courseid);

$config = lti_get_type_type_config($id);
$islti1p3 = $config->lti_ltiversion === LTI_VERSION_1P3;
$items = '';

if (!empty($jwt)) {
    $params = lti_convert_from_jwt($id, $jwt);
    $consumerkey = $params['oauth_consumer_key'] ?? '';
    $messagetype = $params['lti_message_type'] ?? '';
    $version = $params['lti_version'] ?? '';
    $items = $params['content_items'] ?? '';
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

// Get and validate frame and thumbnail sizes.
$framewidth = panoptosubmission_get_property_or_default(
    $contentitems->{'@graph'}[0]->placementAdvice ?? new stdClass(),
    'displayWidth',
    720
);

$frameheight = panoptosubmission_get_property_or_default(
    $contentitems->{'@graph'}[0]->placementAdvice ?? new stdClass(),
    'displayHeight',
    480
);

$title = panoptosubmission_get_property_or_default(
    $contentitems->{'@graph'}[0] ?? new stdClass(),
    'title',
    ''
);

if (!empty($title)) {
    $invalidcharacters = ["$", "%", "#", "<", ">"];
    $title = str_replace($invalidcharacters, "", $title);
}

$url = panoptosubmission_get_property_or_default(
    $contentitems->{'@graph'}[0] ?? new stdClass(),
    'url',
    ''
);

if (!empty($url)) {
    $panoptodata = new \panopto_data($courseid);
    $baseurl = parse_url($url, PHP_URL_HOST);
    if (strcmp($panoptodata->servername, $baseurl) !== 0) {
        $url = '';
    }
}

$thumbnail = $contentitems->{'@graph'}[0]->thumbnail ?? new stdClass();
$thumbnailurlfinal = panoptosubmission_get_property_or_default(
    $thumbnail,
    'id',
    panoptosubmission_get_property_or_default($thumbnail, '@id', '')
);

$thumbnailwidth = panoptosubmission_get_property_or_default($thumbnail, 'width', 128);
$thumbnailheight = panoptosubmission_get_property_or_default($thumbnail, 'height', 72);

$customdata = $contentitems->{'@graph'}[0]->custom ?? new stdClass();

// In this version of Moodle LTI contentitem request we do not want the interactive viewer.
unset($customdata->use_panopto_interactive_view);

$ltiviewerurl = new moodle_url("/mod/panoptosubmission/view_submission.php");
?>

<script type="text/javascript">
    /**
     * Trigger the handleError callback with the errors.
     */
    <?php if (count($errors) > 0): ?>
        parent.document.CALLBACKS.handleError(<?php echo json_encode($errors); ?>);
    <?php else: ?>
        /**
         * Create and dispatch a custom event 'sessionSelected' with session details.
         * This event should close the Panopto popup and pass the new content URL to the existing iframe.
         */
        const detailObject = {
            title: "<?php echo $title ?>",
            ltiViewerUrl: "<?php echo $ltiviewerurl->out(false) ?>",
            contentUrl: "<?php echo $url ?>",
            customData: "<?php echo urlencode(json_encode($customdata)) ?>",
            width: <?php echo $framewidth ?>,
            height: <?php echo $frameheight ?>,
            thumbnailUrl: "<?php echo $thumbnailurlfinal ?>",
            thumbnailWidth: <?php echo $thumbnailwidth ?>,
            thumbnailHeight: <?php echo $thumbnailheight ?>
        };

        const sessionSelectedEvent = new CustomEvent('sessionSelected', {
            detail: detailObject,
            bubbles: false,
            cancelable: false
        });

        parent.document.body.dispatchEvent(sessionSelectedEvent);
    <?php endif; ?>

</script>
