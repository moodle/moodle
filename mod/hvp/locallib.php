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
 * Internal library of functions for module hvp
 *
 * All the hvp specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once('autoloader.php');

/**
 * Get array with settings for hvp core
 *
 * @param \context_course|\context_module [$context]
 * @return array Settings
 */
function hvp_get_core_settings($context) {
    global $USER, $CFG;

    $systemcontext = \context_system::instance();
    $basepath = $CFG->httpswwwroot . '/';

    // Check permissions and generate ajax paths.
    $ajaxpaths = array();
    $savefreq = false;
    $ajaxpath = "{$basepath}mod/hvp/ajax.php?contextId={$context->instanceid}&token=";
    if ($context->contextlevel == CONTEXT_MODULE && has_capability('mod/hvp:saveresults', $context)) {
        $ajaxpaths['setFinished'] = $ajaxpath . \H5PCore::createToken('result') . '&action=set_finished';
        $ajaxpaths['xAPIResult'] = $ajaxpath . \H5PCore::createToken('xapiresult') . '&action=xapiresult';
    }
    if (has_capability('mod/hvp:savecontentuserdata', $context)) {
        $ajaxpaths['contentUserData'] = $ajaxpath . \H5PCore::createToken('contentuserdata') .
            '&action=contents_user_data&content_id=:contentId&data_type=:dataType&sub_content_id=:subContentId';

        if (get_config('mod_hvp', 'enable_save_content_state')) {
            $savefreq = get_config('mod_hvp', 'content_state_frequency');
        }
    }

    $core = \mod_hvp\framework::instance('core');

    $settings = array(
        'baseUrl' => $basepath,
        'url' => "{$basepath}pluginfile.php/{$context->instanceid}/mod_hvp",
        'libraryUrl' => "{$basepath}pluginfile.php/{$systemcontext->id}/mod_hvp/libraries",
        'postUserStatistics' => true,
        'ajax' => $ajaxpaths,
        'saveFreq' => $savefreq,
        'siteUrl' => $CFG->wwwroot,
        'l10n' => array('H5P' => $core->getLocalization()),
        'user' => array(
            'name' => $USER->firstname . ' ' . $USER->lastname,
            'mail' => $USER->email
        ),
        'hubIsEnabled' => get_config('mod_hvp', 'hub_is_enabled') ? true : false,
        'reportingIsEnabled' => true,
    );

    return $settings;
}

/**
 * Get assets (scripts and styles) for hvp core.
 *
 * @param \context_course|\context_module $context
 * @return array
 */
function hvp_get_core_assets($context) {
    global $CFG, $PAGE;

    // Get core settings.
    $settings = \hvp_get_core_settings($context);
    $settings['core'] = array(
        'styles' => array(),
        'scripts' => array()
    );
    $settings['loadedJs'] = array();
    $settings['loadedCss'] = array();

    // Make sure files are reloaded for each plugin update.
    $cachebuster = \hvp_get_cache_buster();

    // Use relative URL to support both http and https.
    $liburl = $CFG->httpswwwroot . '/mod/hvp/library/';
    $relpath = '/' . preg_replace('/^[^:]+:\/\/[^\/]+\//', '', $liburl);

    // Add core stylesheets.
    foreach (\H5PCore::$styles as $style) {
        $settings['core']['styles'][] = $relpath . $style . $cachebuster;
        $PAGE->requires->css(new moodle_url($liburl . $style . $cachebuster));
    }
    // Add core JavaScript.
    foreach (\H5PCore::$scripts as $script) {
        $settings['core']['scripts'][] = $relpath . $script . $cachebuster;
        $PAGE->requires->js(new moodle_url($liburl . $script . $cachebuster), true);
    }

    return $settings;
}

/**
 * Add required assets for displaying the editor.
 *
 * @param int $id Content being edited. null for creating new content
 */
function hvp_add_editor_assets($id = null) {
    global $PAGE, $CFG, $COURSE;

    // First we need to determine the context for permission handling.
    if ($id) {
        // Use cm context when editing existing content.
        $cm = get_coursemodule_from_instance('hvp', $id);
        $context = \context_module::instance($cm->id);
    } else {
        // Use course context when there's no content, i.e. adding new content.
        $context = \context_course::instance($COURSE->id);
    }

    $settings = \hvp_get_core_assets($context);

    // Use jQuery and styles from core.
    $assets = array(
        'css' => $settings['core']['styles'],
        'js' => $settings['core']['scripts']
    );

    // Use relative URL to support both http and https.
    $url = $CFG->httpswwwroot . '/mod/hvp/';
    $url = '/' . preg_replace('/^[^:]+:\/\/[^\/]+\//', '', $url);

    // Make sure files are reloaded for each plugin update.
    $cachebuster = \hvp_get_cache_buster();

    // Add editor styles.
    foreach (H5peditor::$styles as $style) {
        $assets['css'][] = $url . 'editor/' . $style . $cachebuster;
    }

    // Add editor JavaScript.
    foreach (H5peditor::$scripts as $script) {
        // We do not want the creator of the iframe inside the iframe.
        if ($script !== 'scripts/h5peditor-editor.js') {
            $assets['js'][] = $url . 'editor/' . $script . $cachebuster;
        }
    }

    // Add JavaScript with library framework integration (editor part).
    $PAGE->requires->js(new moodle_url('/mod/hvp/editor/scripts/h5peditor-editor.js' . $cachebuster), true);
    $PAGE->requires->js(new moodle_url('/mod/hvp/editor/scripts/h5peditor-init.js' . $cachebuster), true);
    $PAGE->requires->js(new moodle_url('/mod/hvp/editor.js' . $cachebuster), true);

    // Add translations.
    $language = \mod_hvp\framework::get_language();
    $languagescript = "editor/language/{$language}.js";
    if (!file_exists("{$CFG->dirroot}/mod/hvp/{$languagescript}")) {
        $languagescript = 'editor/language/en.js';
    }
    $PAGE->requires->js(new moodle_url('/mod/hvp/' . $languagescript . $cachebuster), true);

    // Add JavaScript settings.
    $filespathbase = "{$CFG->httpswwwroot}/pluginfile.php/{$context->id}/mod_hvp/";
    $contentvalidator = \mod_hvp\framework::instance('contentvalidator');
    $editorajaxtoken = \H5PCore::createToken('editorajax');
    $settings['editor'] = array(
      'filesPath' => $filespathbase . 'editor',
      'fileIcon' => array(
        'path' => $url . 'editor/images/binary-file.png',
        'width' => 50,
        'height' => 50,
      ),
      'ajaxPath' => "{$url}ajax.php?contextId={$context->id}&token={$editorajaxtoken}&action=",
      'libraryUrl' => $url . 'editor/',
      'copyrightSemantics' => $contentvalidator->getCopyrightSemantics(),
      'assets' => $assets,
      // @codingStandardsIgnoreLine
      'apiVersion' => H5PCore::$coreApi
    );

    if ($id !== null) {
        $settings['editor']['nodeVersionId'] = $id;

        // Find cm context.
        $cm      = \get_coursemodule_from_instance('hvp', $id);
        $context = \context_module::instance($cm->id);

        // Override content URL.
        $contenturl = "{$CFG->httpswwwroot}/pluginfile.php/{$context->id}/mod_hvp/content/{$id}";
        $settings['contents']['cid-' . $id]['contentUrl'] = $contenturl;
    }

    $PAGE->requires->data_for_js('H5PIntegration', $settings, true);
}

/**
 * Add core JS and CSS to page.
 *
 * @param moodle_page $page
 * @param moodle_url|string $liburl
 * @param array|null $settings
 * @throws \coding_exception
 */
function hvp_admin_add_generic_css_and_js($page, $liburl, $settings = null) {
    // @codingStandardsIgnoreLine
    foreach (\H5PCore::$adminScripts as $script) {
        $page->requires->js(new moodle_url($liburl . $script . hvp_get_cache_buster()), true);
    }

    if ($settings === null) {
        $settings = array();
    }

    $settings['containerSelector'] = '#h5p-admin-container';
    $settings['l10n'] = array(
        'NA' => get_string('notapplicable', 'hvp'),
        'viewLibrary' => '',
        'deleteLibrary' => '',
        'upgradeLibrary' => get_string('upgradelibrarycontent', 'hvp')
    );

    $page->requires->data_for_js('H5PAdminIntegration', $settings, true);
    $page->requires->css(new moodle_url($liburl . 'styles/h5p.css' . hvp_get_cache_buster()));
    $page->requires->css(new moodle_url($liburl . 'styles/h5p-admin.css' . hvp_get_cache_buster()));

    // Add settings.
    $page->requires->data_for_js('h5p', hvp_get_core_settings(\context_system::instance()), true);
}

/**
 * Get a query string with the plugin version number to include at the end
 * of URLs. This is used to force the browser to reload the asset when the
 * plugin is updated.
 *
 * @return string
 */
function hvp_get_cache_buster() {
    return '?ver=' . get_config('mod_hvp', 'version');
}

/**
 * Restrict access to a given content type.
 *
 * @param int $library_id
 * @param bool $restrict
 */
function hvp_restrict_library($libraryid, $restrict) {
    global $DB;
    $DB->update_record('hvp_libraries', (object) array(
        'id' => $libraryid,
        'restricted' => $restrict ? 1 : 0
    ));
}

/**
 * Handle content upgrade progress
 *
 * @method hvp_content_upgrade_progress
 * @param  int $library_id
 * @return object An object including the json content for the H5P instances
 *                (maximum 40) that should be upgraded.
 */
function hvp_content_upgrade_progress($libraryid) {
    global $DB;

    $tolibraryid = filter_input(INPUT_POST, 'libraryId');

    // Verify security token.
    if (!\H5PCore::validToken('contentupgrade', required_param('token', PARAM_RAW))) {
        print get_string('upgradeinvalidtoken', 'hvp');
        return;
    }

    // Get the library we're upgrading to.
    $tolibrary = $DB->get_record('hvp_libraries', array(
        'id' => $tolibraryid
    ));
    if (!$tolibrary) {
        print get_string('upgradelibrarymissing', 'hvp');
        return;
    }

    // Prepare response.
    $out = new stdClass();
    $out->params = array();
    $out->token = \H5PCore::createToken('contentupgrade');

    // Prepare our interface.
    $interface = \mod_hvp\framework::instance('interface');

    // Get updated params.
    $params = filter_input(INPUT_POST, 'params');
    if ($params !== null) {
        // Update params.
        $params = json_decode($params);
        foreach ($params as $id => $param) {
            $DB->update_record('hvp', (object) array(
                'id' => $id,
                'main_library_id' => $tolibrary->id,
                'json_content' => $param,
                'filtered' => ''
            ));

            // Log content upgrade successful.
            new \mod_hvp\event(
                    'content', 'upgrade',
                    $id, $DB->get_field_sql("SELECT name FROM {hvp} WHERE id = ?", array($id)),
                    $tolibrary->machine_name, $tolibrary->major_version . '.' . $tolibrary->minor_version
            );
        }
    }

    // Get number of contents for this library.
    $out->left = $interface->getNumContent($libraryid);

    if ($out->left) {
        // Find the 40 first contents using this library version and add to params.
        $results = $DB->get_records_sql(
            "SELECT id, json_content as params
               FROM {hvp}
              WHERE main_library_id = ?
           ORDER BY name ASC", array($libraryid), 0 , 40
        );

        foreach ($results as $content) {
            $out->params[$content->id] = $content->params;
        }
    }

    return $out;
}

/**
 * Gets the information needed when content is upgraded
 *
 * @method hvp_get_library_upgrade_info
 * @param  string $name
 * @param  int $major
 * @param  int $minor
 * @return object Library metadata including name, version, semantics and path
 *                to upgrade script
 */
function hvp_get_library_upgrade_info($name, $major, $minor) {
    global $CFG;

    $library = (object) array(
        'name' => $name,
        'version' => (object) array(
            'major' => $major,
            'minor' => $minor
        )
    );

    $core = \mod_hvp\framework::instance();

    $library->semantics = $core->loadLibrarySemantics($library->name, $library->version->major, $library->version->minor);
    if ($library->semantics === null) {
        http_response_code(404);
        return;
    }

    $context = \context_system::instance();
    $libraryfoldername = "{$library->name}-{$library->version->major}.{$library->version->minor}";
    if (\mod_hvp\file_storage::fileExists($context->id, 'libraries', '/' . $libraryfoldername . '/', 'upgrades.js')) {
        $basepath = $CFG->httpswwwroot . '/';
        $library->upgradesScript = "{$basepath}pluginfile.php/{$context->id}/mod_hvp/libraries/{$libraryfoldername}/upgrades.js";
    }

    return $library;
}

/**
 * Check permissions to view given user's results
 *
 * @param int $userid Id of the user the results belong to
 * @param context $context Current context, usually course context
 *
 * @return bool true if current user has permission to view given user results
 */
function hvp_has_view_results_permission($userid, $context) {
    global $USER;

    // Check if user can view all results.
    if (has_capability('mod/hvp:viewallresults', $context)) {
        return true;
    }

    // Check if viewing own results, and have permission for it.
    return $userid === (int) $USER->id ? has_capability('mod/hvp:viewresults', $context) : false;
}

/**
 * Require view results capability for this page
 *
 * @param int $userid User id who owns results
 * @param context $context Current context
 * @param int $redirectcontentid Redirect to this content id if not allowed
 *  to view own results
 */
function hvp_require_view_results_permission($userid, $context, $redirectcontentid = null) {
    global $USER;

    if (!hvp_has_view_results_permission($userid, $context)) {
        if ($userid === (int) $USER->id && isset($redirectcontentid)) {
            // Not allowed to view own results, redirect.
            redirect(new moodle_url('/mod/hvp/view.php', ['id' => $redirectcontentid]));
        } else {
            // Other user's results, require capability to view all results.
            require_capability('mod/hvp:viewallresults', $context);
        }
    }
}
