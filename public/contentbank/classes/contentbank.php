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

namespace core_contentbank;

use core_plugin_manager;
use stored_file;
use context;

/**
 * Content bank class
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contentbank {

    /** @var array All the context levels allowed in the content bank */
    private const ALLOWED_CONTEXT_LEVELS = [CONTEXT_SYSTEM, CONTEXT_COURSECAT, CONTEXT_COURSE];

    /** @var array Enabled content types. */
    private $enabledcontenttypes = null;

    /**
     * Obtains the list of core_contentbank_content objects currently active.
     *
     * The list does not include players which are disabled.
     *
     * @return string[] Array of contentbank contenttypes.
     */
    public function get_enabled_content_types(): array {
        if (!is_null($this->enabledcontenttypes)) {
            return $this->enabledcontenttypes;
        }

        $enabledtypes = \core\plugininfo\contenttype::get_enabled_plugins();
        $types = [];
        foreach ($enabledtypes as $name) {
            $contenttypeclassname = "\\contenttype_$name\\contenttype";
            $contentclassname = "\\contenttype_$name\\content";
            if (class_exists($contenttypeclassname) && class_exists($contentclassname)) {
                $types[$contenttypeclassname] = $name;
            }
        }
        return $this->enabledcontenttypes = $types;
    }

    /**
     * Obtains an array of supported extensions by active plugins.
     *
     * @return array The array with all the extensions supported and the supporting plugin names.
     */
    public function load_all_supported_extensions(): array {
        $extensionscache = \cache::make('core', 'contentbank_enabled_extensions');
        $supportedextensions = $extensionscache->get('enabled_extensions');
        if ($supportedextensions === false) {
            // Load all enabled extensions.
            $supportedextensions = [];
            foreach ($this->get_enabled_content_types() as $type) {
                $classname = "\\contenttype_$type\\contenttype";
                $contenttype = new $classname;
                if ($contenttype->is_feature_supported($contenttype::CAN_UPLOAD)) {
                    $extensions = $contenttype->get_manageable_extensions();
                    foreach ($extensions as $extension) {
                        if (array_key_exists($extension, $supportedextensions)) {
                            $supportedextensions[$extension][] = $type;
                        } else {
                            $supportedextensions[$extension] = [$type];
                        }
                    }
                }
            }
            $extensionscache->set('enabled_extensions', $supportedextensions);
        }
        return $supportedextensions;
    }

    /**
     * Obtains an array of supported extensions in the given context.
     *
     * @param context $context Optional context to check (default null)
     * @return array The array with all the extensions supported and the supporting plugin names.
     */
    public function load_context_supported_extensions(?context $context = null): array {
        $extensionscache = \cache::make('core', 'contentbank_context_extensions');

        $contextextensions = $extensionscache->get($context->id);
        if ($contextextensions === false) {
            $contextextensions = [];
            $supportedextensions = $this->load_all_supported_extensions();
            foreach ($supportedextensions as $extension => $types) {
                foreach ($types as $type) {
                    $classname = "\\contenttype_$type\\contenttype";
                    $contenttype = new $classname($context);
                    if ($contenttype->can_upload()) {
                        $contextextensions[$extension] = $type;
                        break;
                    }
                }
            }
            $extensionscache->set($context->id, $contextextensions);
        }
        return $contextextensions;
    }

    /**
     * Obtains a string with all supported extensions by active plugins.
     * Mainly to use as filepicker options parameter.
     *
     * @param context $context   Optional context to check (default null)
     * @return string A string with all the extensions supported.
     */
    public function get_supported_extensions_as_string(?context $context = null) {
        $supported = $this->load_context_supported_extensions($context);
        $extensions = array_keys($supported);
        return implode(',', $extensions);
    }

    /**
     * Returns the file extension for a file.
     *
     * @param  string $filename The name of the file
     * @return string The extension of the file
     */
    public function get_extension(string $filename) {
        $dot = strrpos($filename, '.');
        if ($dot === false) {
            return '';
        }
        return strtolower(substr($filename, $dot));
    }

    /**
     * Get the first content bank plugin supports a file extension.
     *
     * @param string $extension Content file extension
     * @param context $context $context     Optional context to check (default null)
     * @return string contenttype name supports the file extension or null if the extension is not supported by any allowed plugin.
     */
    public function get_extension_supporter(string $extension, ?context $context = null): ?string {
        $supporters = $this->load_context_supported_extensions($context);
        if (array_key_exists($extension, $supporters)) {
            return $supporters[$extension];
        }
        return null;
    }

    /**
     * Find the contents with %$search% in the contextid defined.
     * If contextid and search are empty, all contents are returned.
     * In all the cases, only the contents for the enabled contentbank-type plugins are returned.
     * No content-type permissions are validated here. It is the caller responsability to check that the user can access to them.
     * The only validation done here is, for each content, a call to the method $content->is_view_allowed().
     *
     * @param  string|null $search Optional string to search (for now it will search only into the name).
     * @param  int $contextid Optional contextid to search.
     * @param  array $contenttypenames Optional array with the list of content-type names to search.
     * @return array The contents for the enabled contentbank-type plugins having $search as name and placed in $contextid.
     */
    public function search_contents(?string $search = null, ?int $contextid = 0, ?array $contenttypenames = null): array {
        global $DB;

        $contents = [];

        // Get only contents for enabled content-type plugins.
        $contenttypes = [];
        $enabledcontenttypes = $this->get_enabled_content_types();
        foreach ($enabledcontenttypes as $contenttypename) {
            if (empty($contenttypenames) || in_array($contenttypename, $contenttypenames)) {
                $contenttypes[] = "contenttype_$contenttypename";
            }
        }

        if (empty($contenttypes)) {
            // Early return if there are no content-type plugins enabled.
            return $contents;
        }

        list($sqlcontenttypes, $params) = $DB->get_in_or_equal($contenttypes, SQL_PARAMS_NAMED);
        $sql = " contenttype $sqlcontenttypes ";

        // Filter contents on this context (if defined).
        if (!empty($contextid)) {
            $params['contextid'] = $contextid;
            $sql .= ' AND contextid = :contextid ';
        }

        // Search for contents having this string (if defined).
        if (!empty($search)) {
            $sql .= ' AND ' . $DB->sql_like('name', ':name', false, false);
            $params['name'] = '%' . $DB->sql_like_escape($search) . '%';
        }

        $records = $DB->get_records_select('contentbank_content', $sql, $params, 'name ASC');
        foreach ($records as $record) {
            $content = $this->get_content_from_id($record->id);
            if ($content->is_view_allowed()) {
                $contents[] = $content;
            }
        }

        return $contents;
    }


    /**
     * Return all the context where a user has all the given capabilities.
     *
     * @param  string $capability The capability the user needs to have.
     * @param  int|null $userid Optional userid. $USER by default.
     * @return array Array of the courses and course categories where the user has the given capability.
     */
    public function get_contexts_with_capabilities_by_user($capability = 'moodle/contentbank:access', $userid = null): array {
        global $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $categoriescache = \cache::make('core', 'contentbank_allowed_categories');
        $coursescache = \cache::make('core', 'contentbank_allowed_courses');

        $categories = $categoriescache->get($userid);
        $courses = $coursescache->get($userid);

        if ($categories === false || $courses === false) {
            // Required fields for preloading the context record.
            $contextfields = 'ctxid, ctxpath, ctxdepth, ctxlevel, ctxinstance, ctxlocked';

            list($categories, $courses) = get_user_capability_contexts($capability, true, $userid, true,
                "fullname, {$contextfields}", "name, {$contextfields}", 'fullname', 'name');
            $categoriescache->set($userid, $categories);
            $coursescache->set($userid, $courses);
        }

        return [$categories, $courses];
    }

    /**
     * Create content from a file information.
     *
     * @param \context $context Context where to upload the file and content.
     * @param int $userid Id of the user uploading the file.
     * @param stored_file $file The file to get information from
     * @return content
     */
    public function create_content_from_file(\context $context, int $userid, stored_file $file): ?content {
        global $USER;
        if (empty($userid)) {
            $userid = $USER->id;
        }
        // Get the contenttype to manage given file's extension.
        $filename = $file->get_filename();
        $extension = $this->get_extension($filename);
        $plugin = $this->get_extension_supporter($extension, $context);
        $classname = '\\contenttype_'.$plugin.'\\contenttype';
        $record = new \stdClass();
        $record->name = $filename;
        $record->usercreated = $userid;
        $contentype = new $classname($context);
        $content = $contentype->upload_content($file, $record);
        $event = \core\event\contentbank_content_uploaded::create_from_record($content->get_content());
        $event->trigger();
        return $content;
    }

    /**
     * Delete content bank content by context.
     *
     * @param context $context The context to delete content from.
     * @return bool
     */
    public function delete_contents(context $context): bool {
        global $DB;

        $result = true;
        $records = $DB->get_records('contentbank_content', ['contextid' => $context->id]);
        foreach ($records as $record) {
            $content = $this->get_content_from_id($record->id);
            $contenttype = $content->get_content_type_instance();
            if (!$contenttype->delete_content($content)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Move content bank content from a context to another.
     *
     * @param context $from The context to get content from.
     * @param context $to The context to move content to.
     * @return bool
     */
    public function move_contents(context $from, context $to): bool {
        global $DB;

        $result = true;
        $records = $DB->get_records('contentbank_content', ['contextid' => $from->id]);
        foreach ($records as $record) {
            $content = $this->get_content_from_id($record->id);
            $contenttype = $content->get_content_type_instance();
            if (!$contenttype->move_content($content, $to)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Get the list of content types that have the requested feature.
     *
     * @param string $feature Feature code e.g CAN_UPLOAD.
     * @param null|\context $context Optional context to check the permission to use the feature.
     * @param bool $enabled Whether check only the enabled content types or all of them.
     *
     * @return string[] List of content types where the user has permission to access the feature.
     */
    public function get_contenttypes_with_capability_feature(string $feature, ?\context $context = null, bool $enabled = true): array {
        $contenttypes = [];
        // Check enabled content types or all of them.
        if ($enabled) {
            $contenttypestocheck = $this->get_enabled_content_types();
        } else {
            $plugins = core_plugin_manager::instance()->get_plugins_of_type('contenttype');
            foreach ($plugins as $plugin) {
                $contenttypeclassname = "\\{$plugin->type}_{$plugin->name}\\contenttype";
                $contenttypestocheck[$contenttypeclassname] = $plugin->name;
            }
        }

        foreach ($contenttypestocheck as $classname => $name) {
            $contenttype = new $classname($context);
            // The method names that check the features permissions must follow the pattern can_feature.
            if ($contenttype->{"can_$feature"}()) {
                $contenttypes[$classname] = $name;
            }
        }

        return $contenttypes;
    }

    /**
     * Return a content class form a content id.
     *
     * @throws coding_exception if the ID is not valid or some class does no exists
     * @param int $id the content id
     * @return content the content class instance
     */
    public function get_content_from_id(int $id): content {
        global $DB;
        $record = $DB->get_record('contentbank_content', ['id' => $id], '*', MUST_EXIST);
        $contentclass = "\\$record->contenttype\\content";
        return new $contentclass($record);
    }

    /**
     * Whether the context is allowed.
     *
     * @param context $context Context to check.
     * @return bool
     */
    public function is_context_allowed(context $context): bool {
        return in_array($context->contextlevel, self::ALLOWED_CONTEXT_LEVELS);
    }
}
