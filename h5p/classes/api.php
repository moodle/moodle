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
 * Contains API class for the H5P area.
 *
 * @package    core_h5p
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

defined('MOODLE_INTERNAL') || die();

use core\lock\lock_config;

/**
 * Contains API class for the H5P area.
 *
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Delete a library and also all the libraries depending on it and the H5P contents using it. For the H5P content, only the
     * database entries in {h5p} are removed (the .h5p files are not removed in order to let users to deploy them again).
     *
     * @param  factory   $factory The H5P factory.
     * @param  \stdClass $library The library to delete.
     */
    public static function delete_library(factory $factory, \stdClass $library): void {
        global $DB;

        // Get the H5P contents using this library, to remove them from DB. The .h5p files won't be removed
        // so they will be displayed by the player next time a user with the proper permissions accesses it.
        $sql = 'SELECT DISTINCT hcl.h5pid
                  FROM {h5p_contents_libraries} hcl
                 WHERE hcl.libraryid = :libraryid';
        $params = ['libraryid' => $library->id];
        $h5pcontents = $DB->get_records_sql($sql, $params);
        foreach ($h5pcontents as $h5pcontent) {
            $factory->get_framework()->deleteContentData($h5pcontent->h5pid);
        }

        $fs = $factory->get_core()->fs;
        $framework = $factory->get_framework();
        // Delete the library from the file system.
        $fs->delete_library(array('libraryId' => $library->id));
        // Delete also the cache assets to rebuild them next time.
        $framework->deleteCachedAssets($library->id);

        // Remove library data from database.
        $DB->delete_records('h5p_library_dependencies', array('libraryid' => $library->id));
        $DB->delete_records('h5p_libraries', array('id' => $library->id));

        // Remove the libraries using this library.
        $requiredlibraries = self::get_dependent_libraries($library->id);
        foreach ($requiredlibraries as $requiredlibrary) {
            self::delete_library($factory, $requiredlibrary);
        }
    }

    /**
     * Get all the libraries using a defined library.
     *
     * @param  int    $libraryid The library to get its dependencies.
     * @return array  List of libraryid with all the libraries required by a defined library.
     */
    public static function get_dependent_libraries(int $libraryid): array {
        global $DB;

        $sql = 'SELECT *
                  FROM {h5p_libraries}
                 WHERE id IN (SELECT DISTINCT hl.id
                                FROM {h5p_library_dependencies} hld
                                JOIN {h5p_libraries} hl ON hl.id = hld.libraryid
                               WHERE hld.requiredlibraryid = :libraryid)';
        $params = ['libraryid' => $libraryid];

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get a library from an identifier.
     *
     * @param  int    $libraryid The library identifier.
     * @return \stdClass The library object having the library identifier defined.
     * @throws dml_exception A DML specific exception is thrown if the libraryid doesn't exist.
     */
    public static function get_library(int $libraryid): \stdClass {
        global $DB;

        return $DB->get_record('h5p_libraries', ['id' => $libraryid], '*', MUST_EXIST);
    }

    /**
     * Returns a library as an object with properties that correspond to the fetched row's field names.
     *
     * @param array $params An associative array with the values of the machinename, majorversion and minorversion fields.
     * @param bool $configurable A library that has semantics so it can be configured in the editor.
     * @param string $fields Library attributes to retrieve.
     *
     * @return \stdClass|null An object with one attribute for each field name in $fields param.
     */
    public static function get_library_details(array $params, bool $configurable, string $fields = ''): ?\stdClass {
        global $DB;

        $select = "machinename = :machinename
                   AND majorversion = :majorversion
                   AND minorversion = :minorversion";

        if ($configurable) {
            $select .= " AND semantics IS NOT NULL";
        }

        $fields = $fields ?: '*';

        $record = $DB->get_record_select('h5p_libraries', $select, $params, $fields);

        return $record ?: null;
    }

    /**
     * Get all the H5P content type libraries versions.
     *
     * @param string|null $fields Library fields to return.
     *
     * @return array An array with an object for each content type library installed.
     */
    public static function get_contenttype_libraries(?string $fields = ''): array {
        global $DB;

        $libraries = [];
        $fields = $fields ?: '*';
        $select = "runnable = :runnable
                   AND semantics IS NOT NULL";
        $params = ['runnable' => 1];
        $sort = 'title, majorversion DESC, minorversion DESC';

        $records = $DB->get_records_select('h5p_libraries', $select, $params, $sort, $fields);

        $added = [];
        foreach ($records as $library) {
            // Remove unique index.
            unset($library->id);

            // Convert snakes to camels.
            $library->majorVersion = (int) $library->majorversion;
            unset($library->major_version);
            $library->minorVersion = (int) $library->minorversion;
            unset($library->minorversion);
            $library->metadataSettings = json_decode($library->metadatasettings);

            // If we already add this library means that it is an old version,as the previous query was sorted by version.
            if (isset($added[$library->name])) {
                $library->isOld = true;
            } else {
                $added[$library->name] = true;
            }

            // Add new library.
            $libraries[] = $library;
        }

        return $libraries;
    }

    /**
     * Get the H5P DB instance id for a H5P pluginfile URL. If it doesn't exist, it's not created.
     *
     * @param string $url H5P pluginfile URL.
     * @param bool $preventredirect Set to true in scripts that can not redirect (CLI, RSS feeds, etc.), throws exceptions
     *
     * @return array of [file, stdClass|false]:
     *             - file local file for this $url.
     *             - stdClass is an H5P object or false if there isn't any H5P with this URL.
     */
    public static function get_content_from_pluginfile_url(string $url, bool $preventredirect = true): array {
        global $DB;

        // Deconstruct the URL and get the pathname associated.
        $pathnamehash = self::get_pluginfile_hash($url, $preventredirect);
        if (!$pathnamehash) {
            return [false, false];
        }

        // Get the file.
        $fs = get_file_storage();
        $file = $fs->get_file_by_hash($pathnamehash);
        if (!$file) {
            return [false, false];
        }

        $h5p = $DB->get_record('h5p', ['pathnamehash' => $pathnamehash]);
        return [$file, $h5p];
    }

    /**
     * Create, if it doesn't exist, the H5P DB instance id for a H5P pluginfile URL. If it exists:
     * - If the content is not the same, remove the existing content and re-deploy the H5P content again.
     * - If the content is the same, returns the H5P identifier.
     *
     * @param string $url H5P pluginfile URL.
     * @param stdClass $config Configuration for H5P buttons.
     * @param factory $factory The \core_h5p\factory object
     * @param stdClass $messages The error, exception and info messages, raised while preparing and running an H5P content.
     * @param bool $preventredirect Set to true in scripts that can not redirect (CLI, RSS feeds, etc.), throws exceptions
     *
     * @return array of [file, h5pid]:
     *             - file local file for this $url.
     *             - h5pid is the H5P identifier or false if there isn't any H5P with this URL.
     */
    public static function create_content_from_pluginfile_url(string $url, \stdClass $config, factory $factory,
        \stdClass &$messages, bool $preventredirect = true): array {
        global $USER;

        $core = $factory->get_core();
        list($file, $h5p) = self::get_content_from_pluginfile_url($url, $preventredirect);

        if (!$file) {
            $core->h5pF->setErrorMessage(get_string('h5pfilenotfound', 'core_h5p'));
            return [false, false];
        }

        $contenthash = $file->get_contenthash();
        if ($h5p && $h5p->contenthash != $contenthash) {
            // The content exists and it is different from the one deployed previously. The existing one should be removed before
            // deploying the new version.
            self::delete_content($h5p, $factory);
            $h5p = false;
        }

        $context = \context::instance_by_id($file->get_contextid());
        if ($h5p) {
            // The H5P content has been deployed previously.
            $displayoptions = helper::get_display_options($core, $config);
            // Check if the user can set the displayoptions.
            if ($displayoptions != $h5p->displayoptions && has_capability('moodle/h5p:setdisplayoptions', $context)) {
                // If the displayoptions has changed and the user has permission to modify it, update this information in the DB.
                $core->h5pF->updateContentFields($h5p->id, ['displayoptions' => $displayoptions]);
            }
            return [$file, $h5p->id];
        } else {
            // The H5P content hasn't been deployed previously.

            // Check if the user uploading the H5P content is "trustable". If the file hasn't been uploaded by a user with this
            // capability, the content won't be deployed and an error message will be displayed.
            if (!helper::can_deploy_package($file)) {
                $core->h5pF->setErrorMessage(get_string('nopermissiontodeploy', 'core_h5p'));
                return [$file, false];
            }

            // The H5P content can be only deployed if the author of the .h5p file can update libraries or if all the
            // content-type libraries exist, to avoid users without the h5p:updatelibraries capability upload malicious content.
            $onlyupdatelibs = !helper::can_update_library($file);

            // Start lock to prevent synchronous access to save the same H5P.
            $lockfactory = lock_config::get_lock_factory('core_h5p');
            $lockkey = 'core_h5p_' . $file->get_pathnamehash();
            if ($lock = $lockfactory->get_lock($lockkey, 10)) {
                try {
                    // Validate and store the H5P content before displaying it.
                    $h5pid = helper::save_h5p($factory, $file, $config, $onlyupdatelibs, false);
                } finally {
                    $lock->release();
                }
            } else {
                $core->h5pF->setErrorMessage(get_string('lockh5pdeploy', 'core_h5p'));
                return [$file, false];
            };

            if (!$h5pid && $file->get_userid() != $USER->id && has_capability('moodle/h5p:updatelibraries', $context)) {
                // The user has permission to update libraries but the package has been uploaded by a different
                // user without this permission. Check if there is some missing required library error.
                $missingliberror = false;
                $messages = helper::get_messages($messages, $factory);
                if (!empty($messages->error)) {
                    foreach ($messages->error as $error) {
                        if ($error->code == 'missing-required-library') {
                            $missingliberror = true;
                            break;
                        }
                    }
                }
                if ($missingliberror) {
                    // The message about the permissions to upload libraries should be removed.
                    $infomsg = "Note that the libraries may exist in the file you uploaded, but you're not allowed to upload " .
                        "new libraries. Contact the site administrator about this.";
                    if (($key = array_search($infomsg, $messages->info)) !== false) {
                        unset($messages->info[$key]);
                    }

                    // No library will be installed and an error will be displayed, because this content is not trustable.
                    $core->h5pF->setInfoMessage(get_string('notrustablefile', 'core_h5p'));
                }
                return [$file, false];

            }
            return [$file, $h5pid];
        }
    }

    /**
     * Delete an H5P package.
     *
     * @param stdClass $content The H5P package to delete with, at least content['id].
     * @param factory $factory The \core_h5p\factory object
     */
    public static function delete_content(\stdClass $content, factory $factory): void {
        $h5pstorage = $factory->get_storage();

        // Add an empty slug to the content if it's not defined, because the H5P library requires this field exists.
        // It's not used when deleting a package, so the real slug value is not required at this point.
        $content->slug = $content->slug ?? '';
        $h5pstorage->deletePackage( (array) $content);
    }

    /**
     * Delete an H5P package deployed from the defined $url.
     *
     * @param string $url pluginfile URL of the H5P package to delete.
     * @param factory $factory The \core_h5p\factory object
     */
    public static function delete_content_from_pluginfile_url(string $url, factory $factory): void {
        // Get the H5P to delete.
        list($file, $h5p) = self::get_content_from_pluginfile_url($url);
        if ($h5p) {
            self::delete_content($h5p, $factory);
        }
    }

    /**
     * Get the pathnamehash from an H5P internal URL.
     *
     * @param  string $url H5P pluginfile URL poiting to an H5P file.
     * @param bool $preventredirect Set to true in scripts that can not redirect (CLI, RSS feeds, etc.), throws exceptions
     *
     * @return string|false pathnamehash for the file in the internal URL.
     */
    protected static function get_pluginfile_hash(string $url, bool $preventredirect = true) {
        global $USER, $CFG;

        // Decode the URL before start processing it.
        $url = new \moodle_url(urldecode($url));

        // Remove params from the URL (such as the 'forcedownload=1'), to avoid errors.
        $url->remove_params(array_keys($url->params()));
        $path = $url->out_as_local_url();

        // We only need the slasharguments.
        $path = substr($path, strpos($path, '.php/') + 5);
        $parts = explode('/', $path);
        $filename = array_pop($parts);

        // If the request is made by tokenpluginfile.php we need to avoid userprivateaccesskey.
        if (strpos($url, '/tokenpluginfile.php')) {
            array_shift($parts);
        }

        // Get the contextid, component and filearea.
        $contextid = array_shift($parts);
        $component = array_shift($parts);
        $filearea = array_shift($parts);

        // Ignore draft files, because they are considered temporary files, so shouldn't be displayed.
        if ($filearea == 'draft') {
            return false;
        }

        // Get the context.
        try {
            list($context, $course, $cm) = get_context_info_array($contextid);
        } catch (\moodle_exception $e) {
            throw new \moodle_exception('invalidcontextid', 'core_h5p');
        }

        // For CONTEXT_USER, such as the private files, raise an exception if the owner of the file is not the current user.
        if ($context->contextlevel == CONTEXT_USER && $USER->id !== $context->instanceid) {
            throw new \moodle_exception('h5pprivatefile', 'core_h5p');
        }

        if (!is_siteadmin($USER)) {
            // For CONTEXT_COURSECAT No login necessary - unless login forced everywhere.
            if ($context->contextlevel == CONTEXT_COURSECAT) {
                if ($CFG->forcelogin) {
                    require_login(null, true, null, false, true);
                }
            }

            // For CONTEXT_BLOCK.
            if ($context->contextlevel == CONTEXT_BLOCK) {
                if ($context->get_course_context(false)) {
                    // If block is in course context, then check if user has capability to access course.
                    require_course_login($course, true, null, false, true);
                } else if ($CFG->forcelogin) {
                    // No login necessary - unless login forced everywhere.
                    require_login(null, true, null, false, true);
                } else {
                    // Get parent context and see if user have proper permission.
                    $parentcontext = $context->get_parent_context();
                    if ($parentcontext->contextlevel === CONTEXT_COURSECAT) {
                        // Check if category is visible and user can view this category.
                        if (!core_course_category::get($parentcontext->instanceid, IGNORE_MISSING)) {
                            send_file_not_found();
                        }
                    } else if ($parentcontext->contextlevel === CONTEXT_USER && $parentcontext->instanceid != $USER->id) {
                        // The block is in the context of a user, it is only visible to the user who it belongs to.
                        send_file_not_found();
                    }
                    if ($filearea !== 'content') {
                        send_file_not_found();
                    }
                }
            }

            // For CONTEXT_MODULE and CONTEXT_COURSE check if the user is enrolled in the course.
            // And for CONTEXT_MODULE has permissions view this .h5p file.
            if ($context->contextlevel == CONTEXT_MODULE ||
                    $context->contextlevel == CONTEXT_COURSE) {
                // Require login to the course first (without login to the module).
                require_course_login($course, true, null, !$preventredirect, $preventredirect);

                // Now check if module is available OR it is restricted but the intro is shown on the course page.
                if ($context->contextlevel == CONTEXT_MODULE) {
                    $cminfo = \cm_info::create($cm);
                    if (!$cminfo->uservisible) {
                        if (!$cm->showdescription || !$cminfo->is_visible_on_course_page()) {
                            // Module intro is not visible on the course page and module is not available, show access error.
                            require_course_login($course, true, $cminfo, !$preventredirect, $preventredirect);
                        }
                    }
                }
            }
        }

        // Some components, such as mod_page or mod_resource, add the revision to the URL to prevent caching problems.
        // So the URL contains this revision number as itemid but a 0 is always stored in the files table.
        // In order to get the proper hash, a callback should be done (looking for those exceptions).
        $pathdata = null;
        if ($context->contextlevel == CONTEXT_MODULE || $context->contextlevel == CONTEXT_BLOCK) {
            $pathdata = component_callback($component, 'get_path_from_pluginfile', [$filearea, $parts], null);
        }
        if (null === $pathdata) {
            // Look for the components and fileareas which have empty itemid defined in xxx_pluginfile.
            $hasnullitemid = false;
            $hasnullitemid = $hasnullitemid || ($component === 'user' && ($filearea === 'private' || $filearea === 'profile'));
            $hasnullitemid = $hasnullitemid || (substr($component, 0, 4) === 'mod_' && $filearea === 'intro');
            $hasnullitemid = $hasnullitemid || ($component === 'course' &&
                    ($filearea === 'summary' || $filearea === 'overviewfiles'));
            $hasnullitemid = $hasnullitemid || ($component === 'coursecat' && $filearea === 'description');
            $hasnullitemid = $hasnullitemid || ($component === 'backup' &&
                    ($filearea === 'course' || $filearea === 'activity' || $filearea === 'automated'));
            if ($hasnullitemid) {
                $itemid = 0;
            } else {
                $itemid = array_shift($parts);
            }

            if (empty($parts)) {
                $filepath = '/';
            } else {
                $filepath = '/' . implode('/', $parts) . '/';
            }
        } else {
            // The itemid and filepath have been returned by the component callback.
            [
                'itemid' => $itemid,
                'filepath' => $filepath,
            ] = $pathdata;
        }

        $fs = get_file_storage();
        $pathnamehash = $fs->get_pathname_hash($contextid, $component, $filearea, $itemid, $filepath, $filename);
        return $pathnamehash;
    }

    /**
     * Returns the H5P content object corresponding to an H5P content file.
     *
     * @param string $pathnamehash The pathnamehash of the file associated to an H5P content.
     *
     * @return null|\stdClass H5P content object or null if not found.
     */
    public static function get_content_from_pathnamehash(string $pathnamehash): ?\stdClass {
        global $DB;

        $h5p = $DB->get_record('h5p', ['pathnamehash' => $pathnamehash]);

        return ($h5p) ? $h5p : null;
    }

    /**
     * Return the H5P export information file when the file has been deployed.
     * Otherwise, return null if H5P file:
     * i) has not been deployed.
     * ii) has changed the content.
     *
     * The information returned will be:
     * - filename, filepath, mimetype, filesize, timemodified and fileurl.
     *
     * @param int $contextid ContextId of the H5P activity.
     * @param factory $factory The \core_h5p\factory object.
     * @param string $component component
     * @param string $filearea file area
     * @return array|null Return file info otherwise null.
     */
    public static function get_export_info_from_context_id(int $contextid,
        factory $factory,
        string $component,
        string $filearea): ?array {

        $core = $factory->get_core();
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, $component, $filearea, 0, 'id', false);
        $file = reset($files);

        if ($h5p = self::get_content_from_pathnamehash($file->get_pathnamehash())) {
            if ($h5p->contenthash == $file->get_contenthash()) {
                $content = $core->loadContent($h5p->id);
                $slug = $content['slug'] ? $content['slug'] . '-' : '';
                $filename = "{$slug}{$content['id']}.h5p";
                $deployedfile = helper::get_export_info($filename, null, $factory);

                return $deployedfile;
            }
        }

        return null;
    }
}
