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
 * Share H5P Content on the Hub
 *
 * @package    mod_hvp
 * @copyright  2020 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
require_once("locallib.php");

global $PAGE, $DB, $CFG, $OUTPUT;

$id = required_param('id', PARAM_INT);

// Verify course context.
$cm = get_coursemodule_from_id('hvp', $id);
if (!$cm) {
    print_error('invalidcoursemodule');
}
$course = $DB->get_record('course', array('id' => $cm->course));
if (!$course) {
    print_error('coursemisconf');
}
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/hvp:share', $context);

// Check if Hub registered, if not redirect to hub registration.
if (empty(get_config('mod_hvp', 'site_uuid')) || empty(get_config('mod_hvp', 'hub_secret'))) {
    if (!has_capability('mod/hvp:contenthubregistration', \context_system::instance())) {
        print_error('nohubregistration');
    }
    redirect(new moodle_url('/mod/hvp/content_hub_registration.php'));
}

// Try to load existing content from the Hub.
$core    = \mod_hvp\framework::instance();
$content = $core->loadContent($cm->instance);

$action = optional_param('action', '', PARAM_TEXT);
if ($action) {
    // Prepare to do a Hub Action.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
    }
    $token = required_param('_token', PARAM_RAW);
    if (!\H5PCore::validToken('share_' . $id, $token)) {
        print_error('invalidtoken');
    }
    if (empty($content['contentHubId']) || $content['shared'] !== '1') {
        print_error('contentnotshared');
    }

    $core = \mod_hvp\framework::instance();
    if ($action === 'sync') {
        // Sync content already shared on the Hub.
        $exporturl = hvp_create_hub_export_url($cm->id, $content);
        if ($core->hubSyncContent($content['contentHubId'], $exporturl)) {
            $core->h5pF->updateContentFields($content['id'], array('synced' => \H5PContentHubSyncStatus::WAITING));
        }
    } else if ($action === 'unpublish') {
        // Unpublish content already shared on the Hub.
        if ($core->hubUnpublishContent($content['contentHubId'])) {
            $core->h5pF->updateContentFields($content['id'], array('shared' => 0));
        }
    }
    redirect(new moodle_url('/mod/hvp/view.php', ['id' => $id]));
    exit;
}

$hubcontent = !empty($content['contentHubId']) ? $core->hubRetrieveContent($content['contentHubId']) : null;
if (empty($content['contentHubId'])) {
    // Try to populate with license from content.
    $license        = !empty($content['metadata']['license']) ? $content['metadata']['license'] : null;
    $licenseversion = !empty($license) && !empty($content['metadata']['licenseVersion']) ? $content['metadata']['licenseVersion'] : null;
    $showcopyrightwarning = false;

    if ($license === 'U') {
        $license = null;
    }

    if ($license === 'C') {
        $license = null;
        $showcopyrightwarning = true;
    }

    $hubcontent     = [
        'license'        => $license,
        'licenseVersion' => $licenseversion,
        'showCopyrightWarning' => $showcopyrightwarning,
    ];
}

// Prepare settings for the UI.
$locale   = \mod_hvp\framework::get_language();
$settings = [
    'token'       => \H5PCore::createToken('share_' . $id),
    'publishURL'  => (new \moodle_url('/mod/hvp/ajax.php', array('action' => 'share', 'id' => $id)))->out(false),
    'returnURL'   => (new \moodle_url('/mod/hvp/view.php', array('id' => $id)))->out(false),
    'l10n'        => $core->getLocalization(),
    'metadata'    => json_decode($core->getUpdatedContentHubMetadataCache($locale)),
    'title'       => html_entity_decode($cm->name, ENT_QUOTES),
    'contentType' => "{$content['library']['name']} {$content['library']['majorVersion']}.{$content['library']['minorVersion']}",
    'language'    => $locale,
    'hubContent'  => $hubcontent,
    'context'     => empty($content['shared']) ? 'share' : 'edit',
];

// Configure page.
$PAGE->set_url(new \moodle_url('/mod/hvp/share.php', array('id' => $id)));
$PAGE->set_title(format_string($cm->name));
$PAGE->set_heading($course->fullname);

// Load JavaScript and styles.
$PAGE->requires->js(new \moodle_url(\mod_hvp\view_assets::getsiteroot() . '/mod/hvp/library/js/h5p-hub-sharing.js'), true);
foreach (\H5PCore::$styles as $style) {
    $PAGE->requires->css(new \moodle_url(\mod_hvp\view_assets::getsiteroot() . "/mod/hvp/library/{$style}"));
}
$PAGE->requires->css(new \moodle_url(\mod_hvp\view_assets::getsiteroot() . '/mod/hvp/library/styles/h5p-hub-sharing.css'));

// Print page HTML.
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($cm->name));

?>
    <div id="h5p-hub-share">
        <div class="loading-screen">
            <?php echo get_string('javascriptloading', 'hvp'); ?>
        </div>
    </div>
    <script>
      H5PHub.createSharingUI(document.getElementById('h5p-hub-share'), <?php echo json_encode($settings); ?>);
    </script>
<?php

echo $OUTPUT->footer();
