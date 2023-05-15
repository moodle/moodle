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
 * Responsible for handling AJAX requests related to H5P.
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_hvp\framework;

define('AJAX_SCRIPT', true);
require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once("locallib.php");

require_login();

$action = required_param('action', PARAM_ALPHA);
switch($action) {

    /*
     * Handle user data reporting
     *
     * Type: HTTP POST
     *
     * Parameters:
     *  - content_id
     *  - data_type
     *  - sub_content_id
     */
    case 'contentsuserdata':
        \mod_hvp\content_user_data::handle_ajax();
        break;

    /*
     * Handle restricting H5P libraries
     *
     * Type: HTTP GET
     *
     * Parameters:
     *  - library_id
     *  - restrict (0 or 1)
     *  - token
     */
    case 'restrictlibrary':

        // Check permissions.
        $context = \context_system::instance();
        if (!has_capability('mod/hvp:restrictlibraries', $context)) {
            \H5PCore::ajaxError(get_string('nopermissiontorestrict', 'hvp'));
            http_response_code(403);
            break;
        }

        $libraryid = required_param('library_id', PARAM_INT);
        $restrict = required_param('restrict', PARAM_INT);

        if (!\H5PCore::validToken('library_' . $libraryid, required_param('token', PARAM_RAW))) {
            \H5PCore::ajaxError(get_string('invalidtoken', 'hvp'));
            exit;
        }

        hvp_restrict_library($libraryid, $restrict);
        header('Cache-Control: no-cache');
        header('Content-Type: application/json');
        echo json_encode(array(
            'url' => (new moodle_url('/mod/hvp/ajax.php', array(
                'action' => 'restrict_library',
                'token' => \H5PCore::createToken('library_' . $libraryid),
                'restrict' => ($restrict === '1' ? 0 : 1),
                'library_id' => $libraryid
            )))->out(false)));
        break;

    /*
     * Collecting data needed by H5P content upgrade
     *
     * Type: HTTP GET
     *
     * Parameters:
     *  - library (Format: /<machine-name>/<major-version>/<minor-version>)
     */
    case 'getlibrarydataforupgrade':

        // Check permissions.
        $context = \context_system::instance();
        if (!has_capability('mod/hvp:updatelibraries', $context)) {
            \H5PCore::ajaxError(get_string('nopermissiontoupgrade', 'hvp'));
            http_response_code(403);
            break;
        }

        $library = required_param('library', PARAM_TEXT);
        $library = explode('/', substr($library, 1));

        if (count($library) !== 3) {
            http_response_code(422);
            return;
        }

        $library = hvp_get_library_upgrade_info($library[0], $library[1], $library[2]);

        header('Cache-Control: no-cache');
        header('Content-Type: application/json');
        print json_encode($library);

        break;

    /*
     * Saving upgraded content, and returning next batch to process
     *
     * Type: HTTP POST
     *
     * Parameters:
     *  - library_id
     */
    case 'libraryupgradeprogress':
        // Check upgrade permissions.
        $context = \context_system::instance();
        if (!has_capability('mod/hvp:updatelibraries', $context)) {
            \H5PCore::ajaxError(get_string('nopermissiontoupgrade', 'hvp'));
            http_response_code(403);
            break;
        }

        // Because of a confirmed bug in PHP, filter_input(INPUT_SERVER, ...)
        // will return null on some versions of FCGI/PHP (5.4 and probably
        // older versions as well), ref. https://bugs.php.net/bug.php?id=49184.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $libraryid = required_param('library_id', PARAM_INT);
            $out = hvp_content_upgrade_progress($libraryid);
            header('Cache-Control: no-cache');
            header('Content-Type: application/json');
            print json_encode($out);
        } else {
            // Only allow POST.
            http_response_code(405);
        }
        break;

    /*
     * Handle set finished / storing grades
     *
     * Type: HTTP GET
     *
     * Parameters:
     *  - contentId
     *  - score
     *  - maxScore
     */
    case 'setfinished':
        \mod_hvp\user_grades::handle_ajax();
        break;

    /*
     * Saves a dynamically graded grade to the gradebook
     *
     * Type: HTTP POST
     *
     * Parameters:
     *  - subcontent_id
     *  - score
     */
    case 'updatesubcontentscore':
        \mod_hvp\user_grades::handle_dynamic_grading();
        break;

    /*
     * Returns a grade
     *
     * Type: HTTP GET
     *
     * Parameters:
     *  - subcontent_id
     */
    case 'getsubcontentscore':
        \mod_hvp\user_grades::return_subcontent_grade();
        break;

    /*
     * Provide data for results view
     *
     * Type: HTTP GET
     *
     * Parameters:
     *  int content_id
     *  int offset
     *  int limit
     *  int sortBy
     *  int sortDir
     *  string[] filters
     */
    case 'results':
        $results = new \mod_hvp\results();
        $results->print_results();
        break;

    /*
     * Load list of libraries or details for library.
     *
     * Parameters:
     *  string machineName
     *  int majorVersion
     *  int minorVersion
     */
    case 'libraries':
        if (!framework::has_editor_access('nopermissiontoviewcontenttypes')) {
            break;
        }

        // Get parameters.
        $name = optional_param('machineName', '', PARAM_TEXT);
        $major = optional_param('majorVersion', 0, PARAM_INT);
        $minor = optional_param('minorVersion', 0, PARAM_INT);
        $editor = framework::instance('editor');
        $language = optional_param('default-language', null, PARAM_RAW);

        if (!empty($name)) {
            $editor->ajax->action(H5PEditorEndpoints::SINGLE_LIBRARY, $name,
                $major, $minor, framework::get_language(), '', '', $language);

            new \mod_hvp\event(
                    'library', null,
                    null, null,
                    $name, $major . '.' . $minor
            );
        } else {
            $editor->ajax->action(H5PEditorEndpoints::LIBRARIES);
        }

        break;

    /*
     * Load content type cache list to display available libraries in hub
     */
    case 'contenttypecache':
        if (!framework::has_editor_access('nopermissiontoviewcontenttypes')) {
            break;
        }

        $editor = framework::instance('editor');
        $editor->ajax->action(H5PEditorEndpoints::CONTENT_TYPE_CACHE);
        break;

    /*
     * Handle file upload through the editor.
     *
     * Parameters:
     *  int contentId
     *  int contextId
     */
    case 'files':
        $token = required_param('token', PARAM_RAW);
        $contentid = required_param('contentId', PARAM_INT);
        if (!framework::has_editor_access('nopermissiontouploadfiles')) {
            break;
        }

        $editor = framework::instance('editor');
        $editor->ajax->action(H5PEditorEndpoints::FILES, $token, $contentid);
        break;

    /*
     * Handle file upload through the editor.
     *
     * Parameters:
     *  raw token
     *  raw contentTypeUrl
     */
    case 'libraryinstall':
        $token = required_param('token', PARAM_RAW);
        $machinename = required_param('id', PARAM_TEXT);
        $editor = framework::instance('editor');
        $editor->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, $token, $machinename);
        break;

    /*
     * Install libraries from h5p and retrieve content json
     *
     * Parameters:
     *  file h5p
     */
    case 'libraryupload':
        $token = required_param('token', PARAM_RAW);
        if (!framework::has_editor_access('nopermissiontouploadcontent')) {
            break;
        }

        $editor = framework::instance('editor');
        $uploadpath = $_FILES['h5p']['tmp_name'];
        $contentid = optional_param('contentId', 0, PARAM_INT);
        $editor->ajax->action(H5PEditorEndpoints::LIBRARY_UPLOAD, $token, $uploadpath, $contentid);
        break;

    /*
     * Record xAPI result from view
     */
    case 'xapiresult':
        \mod_hvp\xapi_result::handle_ajax();
        break;

    case 'translations':
        if (!framework::has_editor_access('nopermissiontogettranslations')) {
            break;
        }
        $language = required_param('language', PARAM_RAW);
        $editor = framework::instance('editor');
        $editor->ajax->action(H5PEditorEndpoints::TRANSLATIONS, $language);
        break;

    /*
     * Handle filtering of parameters through AJAX.
     */
    case 'filter':
        $token = required_param('token', PARAM_RAW);
        $libraryparameters = required_param('libraryParameters', PARAM_RAW);

        if (!framework::has_editor_access('nopermissiontouploadfiles')) {
            break;
        }

        $editor = framework::instance('editor');
        $editor->ajax->action(H5PEditorEndpoints::FILTER, $token, $libraryparameters);
        break;

    /*
     * Handle filtering of parameters through AJAX.
     */
    case 'contenthubmetadatacache':
        if (!framework::has_editor_access('nopermissiontoviewcontenthubcache')) {
            break;
        }
        $editor = framework::instance('editor');
        $editor->ajax->action(H5PEditorEndpoints::CONTENT_HUB_METADATA_CACHE, framework::get_language());
        break;

    case 'contenthubregistration':
        // Check permission.
        $context = \context_system::instance();
        if (!has_capability('mod/hvp:contenthubregistration', $context)) {
            \H5PCore::ajaxError(get_string('contenthub:nopermissions', 'hvp'), 'NO_PERMISSION', 403);
            return;
        }

        $token = required_param('_token', PARAM_RAW);

        if (!H5PCore::validToken('contentHubRegistration', $token)) {
            H5PCore::ajaxError('Invalid token', 'INVALID_TOKEN', 401);
            return;
        }
        $logo = isset($_FILES['logo']) ? $_FILES['logo'] : null;

        $formdata = [
            'name'           => required_param('name', PARAM_TEXT),
            'email'          => required_param('email', PARAM_EMAIL),
            'description'    => optional_param('description', '', PARAM_TEXT),
            'contact_person' => optional_param('contact_person', '', PARAM_TEXT),
            'phone'          => optional_param('phone', '', PARAM_TEXT),
            'address'        => optional_param('address', '', PARAM_TEXT),
            'city'           => optional_param('city', '', PARAM_TEXT),
            'zip'            => optional_param('zip', '', PARAM_TEXT),
            'country'        => optional_param('country', '', PARAM_TEXT),
            'remove_logo'    => optional_param('remove_logo', '', PARAM_BOOL),
        ];

        $core = framework::instance();
        $result = $core->hubRegisterAccount($formdata, $logo);

        if ($result['success'] === false) {
            $core->h5pF->setErrorMessage($result['message']);
            H5PCore::ajaxError($result['message'], $result['error_code'], $result['status_code']);
            return;
        }

        $core->h5pF->setInfoMessage($result['message']);
        H5PCore::ajaxSuccess($result['message']);
        break;

    /*
     * Handle filtering of parameters through AJAX.
     */
    case 'getcontent':
        $token = required_param('token', PARAM_RAW);
        $hubid = required_param('hubId', PARAM_INT);

        $editor = \mod_hvp\framework::instance('editor');
        $editor->ajax->action(H5PEditorEndpoints::GET_HUB_CONTENT, $token, $hubid, null);
        break;

    /*
     * Handle publishing of content to the H5P OER Hub.
     */
    case 'share':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
        }
        $token = required_param('_token', PARAM_RAW);
        $id = required_param('id', PARAM_INT);

        if (!\H5PCore::validToken('share_' . $id, $token)) {
            \H5PCore::ajaxError(get_string('invalidtoken', 'hvp'));
            break;
        }

        $data = array(
            'title'                => required_param('title', PARAM_RAW),
            'language'             => required_param('language', PARAM_RAW),
            'license'              => required_param('license', PARAM_RAW),
            'license_version'      => required_param('license_version', PARAM_RAW),
            'level'                => optional_param('level', null, PARAM_RAW),
            'disciplines'          => optional_param_array('disciplines', null, PARAM_RAW),
            'keywords'             => optional_param_array('keywords', null, PARAM_RAW),
            'summary'              => optional_param('summary', null, PARAM_RAW),
            'description'          => optional_param('description', null, PARAM_RAW),
            'screenshot_alt_texts' => optional_param_array('screenshot_alt_texts', null, PARAM_RAW),
            'remove_screenshots'   => optional_param_array('remove_screenshots', null, PARAM_RAW),
            'remove_icon'          => optional_param('remove_icon', null, PARAM_RAW),
            'age'                  => optional_param('age', null, PARAM_RAW),
        );

        // Load content.
        $core = \mod_hvp\framework::instance();
        $cm = get_coursemodule_from_id('hvp', $id);
        $content = $core->loadContent($cm->instance);

        // Update Hub status for content before proceeding.
        $newstate = hvp_update_hub_status($content);
        $synced = $newstate !== false ? $newstate : intval($content['synced']);

        if ($synced === \H5PContentHubSyncStatus::WAITING) {
            \H5PCore::ajaxError(get_string('contentissyncing', 'hvp'));
            break;
        }

        // Create URL.
        $data['download_url'] = hvp_create_hub_export_url($cm->id, $content);

        // Get file size.
        $slug = $content['slug'] ? $content['slug'] . '-' : '';
        $filecontext = context_course::instance($cm->course);
        $file = get_file_storage()->get_file($filecontext->id, 'mod_hvp', 'exports', 0, '/', "{$slug}{$content['id']}.h5p");
        if (!$file) {
            \H5PCore::ajaxError(get_string('noexport', 'hvp'));
            break;
        }
        $size = $file->get_filesize();
        $data['size'] = empty($size) ? -1 : $size;

        // Add the icon and any screenshots.
        $files = array(
            'icon' => !empty($_FILES['icon']) ? $_FILES['icon'] : null,
            'screenshots' => !empty($_FILES['screenshots']) ? $_FILES['screenshots'] : null,
        );

        try {
            $isedit = !empty($content['contentHubId']);
            $updatecontent = $synced === \H5PContentHubSyncStatus::NOT_SYNCED && $isedit;
            if ($updatecontent) {
                // Node has been edited since the last time it was published.
                $data['resync'] = 1;
            }
            $result = $core->hubPublishContent($data, $files, $isedit ? $content['contentHubId'] : null);

            $fields = array(
                'shared' => 1, // Content is always shared after sharing or editing.
            );
            if (!$isedit) {
                $fields['hub_id'] = $result->content->id;
                // Sync will not happen on 'edit info', only for 'publish' or 'sync'.
                $fields['synced'] = \H5PContentHubSyncStatus::WAITING;
            } else if ($updatecontent) {
                $fields['synced'] = \H5PContentHubSyncStatus::WAITING;
            }

            // Store the content hub id.
            $core->h5pF->updateContentFields($cm->instance, $fields);

            H5PCore::ajaxSuccess();
        } catch (Exception $e) {
            H5PCore::ajaxError(!empty($e->errors) ? $e->errors : $e->getMessage());
        }

        break;

    /*
     * Throw error if AJAX isnt handeled
     */
    default:
        throw new coding_exception('Unhandled AJAX');
        break;
}
