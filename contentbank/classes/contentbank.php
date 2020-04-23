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
 * Content bank class
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank;

/**
 * Content bank class
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contentbank {

    /**
     * Obtains the list of core_contentbank_content objects currently active.
     *
     * The list does not include players which are disabled.
     *
     * @return string[] Array of contentbank contenttypes.
     */
    private function get_enabled_content_types(): array {
        $enabledtypes = \core\plugininfo\contenttype::get_enabled_plugins();
        $types = [];
        foreach ($enabledtypes as $name) {
            $contenttypeclassname = "\\contenttype_$name\\contenttype";
            $contentclassname = "\\contenttype_$name\\content";
            if (class_exists($contenttypeclassname) && class_exists($contentclassname)) {
                $types[] = $name;
            }
        }
        return $types;
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
     * @param \context $context Optional context to check (default null)
     * @return array The array with all the extensions supported and the supporting plugin names.
     */
    public function load_context_supported_extensions(\context $context = null): array {
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
     * @param \context $context   Optional context to check (default null)
     * @return string A string with all the extensions supported.
     */
    public function get_supported_extensions_as_string(\context $context = null) {
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
     * @param \context $context $context     Optional context to check (default null)
     * @return string contenttype name supports the file extension or null if the extension is not supported by any allowed plugin.
     */
    public function get_extension_supporter(string $extension, \context $context = null): ?string {
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
     *
     * @param  string|null $search Optional string to search (for now it will search only into the name).
     * @param  int $contextid Optional contextid to search.
     * @return array The contents for the enabled contentbank-type plugins having $search as name and placed in $contextid.
     */
    public function search_contents(?string $search = null, ?int $contextid = 0): array {
        global $DB;

        $contents = [];

        // Get only contents for enabled content-type plugins.
        $contenttypes = array_map(function($contenttypename) {
            return "contenttype_$contenttypename";
        }, $this->get_enabled_content_types());
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

        $records = $DB->get_records_select('contentbank_content', $sql, $params);
        foreach ($records as $record) {
            $contentclass = "\\$record->contenttype\\content";
            $content = new $contentclass($record);
            if ($content->is_view_allowed()) {
                $contents[] = $content;
            }
        }

        return $contents;
    }
}
