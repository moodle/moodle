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
 * Provides the {@link core_form\filetypes_util} class.
 *
 * @package     core_form
 * @copyright   2017 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_form;

use core_collator;
use core_filetypes;
use core_text;

defined('MOODLE_INTERNAL') || die();

/**
 * Utility class for handling with file types in the forms.
 *
 * This class is supposed to serve as a helper class for {@link MoodleQuickForm_filetypes}
 * and {@link admin_setting_filetypes} classes.
 *
 * The file types can be specified in a syntax compatible with what filepicker
 * and filemanager support via the "accepted_types" option: a list of extensions
 * (e.g. ".doc"), mimetypes ("image/png") or groups ("audio").
 *
 * @copyright 2017 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filetypes_util {

    /** @var array Cache of all file type groups for the {@link self::get_groups_info()}. */
    protected $cachegroups = null;

    /**
     * Converts the argument into an array (list) of file types.
     *
     * The list can be separated by whitespace, end of lines, commas, colons and semicolons.
     * Empty values are not returned. Values are converted to lowercase.
     * Duplicates are removed. Glob evaluation is not supported.
     *
     * The return value can be used as the accepted_types option for the filepicker.
     *
     * @param string|array $types List of file extensions, groups or mimetypes
     * @return array of strings
     */
    public function normalize_file_types($types) {

        if ($types === '') {
            return [];
        }

        // Turn string into a list.
        if (!is_array($types)) {
            $types = preg_split('/[\s,;:"\']+/', $types, null, PREG_SPLIT_NO_EMPTY);
        }

        // Fix whitespace and normalize the syntax a bit.
        foreach ($types as $i => $type) {
            $type = str_replace('*.', '.', $type);
            $type = core_text::strtolower($type);
            $type = trim($type);

            if ($type === '*') {
                return ['*'];
            }

            $types[$i] = $type;
        }

        // Do not make the user think that globs (like ".doc?") would work.
        foreach ($types as $i => $type) {
            if (strpos($type, '*') !== false or strpos($type, '?') !== false) {
                unset($types[$i]);
            }
        }

        foreach ($types as $i => $type) {
            if (substr($type, 0, 1) === '.') {
                // It looks like an extension.
                $type = '.'.ltrim($type, '.');
                $types[$i] = clean_param($type, PARAM_FILE);
            } else if ($this->looks_like_mimetype($type)) {
                // All good, it looks like a mimetype.
                continue;
            } else if ($this->is_filetype_group($type)) {
                // All good, it is a known type group.
                continue;
            } else {
                // We assume the user typed something like "png" so we consider
                // it an extension.
                $types[$i] = '.'.$type;
            }
        }

        $types = array_filter($types, 'strlen');
        $types = array_keys(array_flip($types));

        return $types;
    }

    /**
     * Does the given file type looks like a valid MIME type?
     *
     * This does not check of the MIME type is actually registered here/known.
     *
     * @param string $type
     * @return bool
     */
    public function looks_like_mimetype($type) {
        return (bool)preg_match('~^[-\.a-z0-9]+/[a-z0-9]+([-\.\+][a-z0-9]+)*$~', $type);
    }

    /**
     * Is the given string a known filetype group?
     *
     * @param string $type
     * @return bool|object false or the group info
     */
    public function is_filetype_group($type) {

        $info = $this->get_groups_info();

        if (isset($info[$type])) {
            return $info[$type];

        } else {
            return false;
        }
    }

    /**
     * Provides a list of all known file type groups and their properties.
     *
     * @return array
     */
    public function get_groups_info() {

        if ($this->cachegroups !== null) {
            return $this->cachegroups;
        }

        $groups = [];

        foreach (core_filetypes::get_types() as $ext => $info) {
            if (isset($info['groups']) && is_array($info['groups'])) {
                foreach ($info['groups'] as $group) {
                    if (!isset($groups[$group])) {
                        $groups[$group] = (object) [
                            'extensions' => [],
                            'mimetypes' => [],
                        ];
                    }
                    $groups[$group]->extensions['.'.$ext] = true;
                    if (isset($info['type'])) {
                        $groups[$group]->mimetypes[$info['type']] = true;
                    }
                }
            }
        }

        foreach ($groups as $group => $info) {
            $info->extensions = array_keys($info->extensions);
            $info->mimetypes = array_keys($info->mimetypes);
        }

        $this->cachegroups = $groups;
        return $this->cachegroups;
    }

    /**
     * Return a human readable name of the filetype group.
     *
     * @param string $group
     * @return string
     */
    public function get_group_description($group) {

        if (get_string_manager()->string_exists('group:'.$group, 'core_mimetypes')) {
            return get_string('group:'.$group, 'core_mimetypes');
        } else {
            return s($group);
        }
    }

    /**
     * Describe the list of file types for human user.
     *
     * Given the list of file types, return a list of human readable
     * descriptive names of relevant groups, types or file formats.
     *
     * @param string|array $types
     * @return object
     */
    public function describe_file_types($types) {

        $descriptions = [];
        $types = $this->normalize_file_types($types);

        foreach ($types as $type) {
            if ($type === '*') {
                $desc = get_string('filetypesany', 'core_form');
                $descriptions[$desc] = [];
            } else if ($group = $this->is_filetype_group($type)) {
                $desc = $this->get_group_description($type);
                $descriptions[$desc] = $group->extensions;

            } else if ($this->looks_like_mimetype($type)) {
                $desc = get_mimetype_description($type);
                $descriptions[$desc] = file_get_typegroup('extension', [$type]);

            } else {
                $desc = get_mimetype_description(['filename' => 'fakefile'.$type]);
                if (isset($descriptions[$desc])) {
                    $descriptions[$desc][] = $type;
                } else {
                    $descriptions[$desc] = [$type];
                }
            }
        }

        $data = [];

        foreach ($descriptions as $desc => $exts) {
            sort($exts);
            $data[] = (object)[
                'description' => $desc,
                'extensions' => join(' ', $exts),
            ];
        }

        core_collator::asort_objects_by_property($data, 'description', core_collator::SORT_NATURAL);

        return (object)[
            'hasdescriptions' => !empty($data),
            'descriptions' => array_values($data),
        ];
    }

    /**
     * Prepares data for the filetypes-browser.mustache
     *
     * @param string|array $onlytypes Allow selection from these file types only; for example 'web_image'.
     * @param bool $allowall Allow to select 'All file types'. Does not apply with onlytypes are set.
     * @param string|array $current Current values that should be selected.
     * @return object
     */
    public function data_for_browser($onlytypes=null, $allowall=true, $current=null) {

        $groups = [];
        $current = $this->normalize_file_types($current);

        // Firstly populate the tree of extensions categorized into groups.

        foreach ($this->get_groups_info() as $groupkey => $groupinfo) {
            if (empty($groupinfo->extensions)) {
                continue;
            }

            $group = (object) [
                'key' => $groupkey,
                'name' => $this->get_group_description($groupkey),
                'selectable' => true,
                'selected' => in_array($groupkey, $current),
                'ext' => implode(' ', $groupinfo->extensions),
                'expanded' => false,
            ];

            $types = [];

            foreach ($groupinfo->extensions as $extension) {
                if ($onlytypes && !$this->is_whitelisted($extension, $onlytypes)) {
                    $group->selectable = false;
                    $group->expanded = true;
                    $group->ext = '';
                    continue;
                }

                $desc = get_mimetype_description(['filename' => 'fakefile'.$extension]);

                if ($selected = in_array($extension, $current)) {
                    $group->expanded = true;
                }

                $types[] = (object) [
                    'key' => $extension,
                    'name' => get_mimetype_description(['filename' => 'fakefile'.$extension]),
                    'selected' => $selected,
                    'ext' => $extension,
                ];
            }

            if (empty($types)) {
                continue;
            }

            core_collator::asort_objects_by_property($types, 'name', core_collator::SORT_NATURAL);

            $group->types = array_values($types);
            $groups[] = $group;
        }

        core_collator::asort_objects_by_property($groups, 'name', core_collator::SORT_NATURAL);

        // Append all other uncategorized extensions.

        $others = [];

        foreach (core_filetypes::get_types() as $extension => $info) {
            // Reserved for unknown file types. Not available here.
            if ($extension === 'xxx') {
                continue;
            }
            $extension = '.'.$extension;
            if ($onlytypes && !$this->is_whitelisted($extension, $onlytypes)) {
                continue;
            }
            if (!isset($info['groups']) || empty($info['groups'])) {
                $others[] = (object) [
                    'key' => $extension,
                    'name' => get_mimetype_description(['filename' => 'fakefile'.$extension]),
                    'selected' => in_array($extension, $current),
                    'ext' => $extension,
                ];
            }
        }

        core_collator::asort_objects_by_property($others, 'name', core_collator::SORT_NATURAL);

        if (!empty($others)) {
            $groups[] = (object) [
                'key' => '',
                'name' => get_string('filetypesothers', 'core_form'),
                'selectable' => false,
                'selected' => false,
                'ext' => '',
                'types' => array_values($others),
                'expanded' => true,
            ];
        }

        if (empty($onlytypes) and $allowall) {
            array_unshift($groups, (object) [
                'key' => '*',
                'name' => get_string('filetypesany', 'core_form'),
                'selectable' => true,
                'selected' => in_array('*', $current),
                'ext' => null,
                'types' => [],
                'expanded' => false,
            ]);
        }

        $groups = array_values($groups);

        return $groups;
    }

    /**
     * Expands the file types into the list of file extensions.
     *
     * The groups and mimetypes are expanded into the list of their associated file
     * extensions. Depending on the $keepgroups and $keepmimetypes, the groups
     * and mimetypes themselves are either kept in the list or removed.
     *
     * @param string|array $types
     * @param bool $keepgroups Keep the group item in the list after expansion
     * @param bool $keepmimetypes Keep the mimetype item in the list after expansion
     * @return array list of extensions and eventually groups and types
     */
    public function expand($types, $keepgroups=false, $keepmimetypes=false) {

        $expanded = [];

        foreach ($this->normalize_file_types($types) as $type) {
            if ($group = $this->is_filetype_group($type)) {
                foreach ($group->extensions as $ext) {
                    $expanded[$ext] = true;
                }
                if ($keepgroups) {
                    $expanded[$type] = true;
                }

            } else if ($this->looks_like_mimetype($type)) {
                // A mime type expands to the associated extensions.
                foreach (file_get_typegroup('extension', [$type]) as $ext) {
                    $expanded[$ext] = true;
                }
                if ($keepmimetypes) {
                    $expanded[$type] = true;
                }

            } else {
                // Single extension expands to itself.
                $expanded[$type] = true;
            }
        }

        return array_keys($expanded);
    }

    /**
     * Should the given file type be considered as a part of the given whitelist.
     *
     * If multiple types are provided, all of them must be part of the
     * whitelist. Empty type is part of any whitelist. Any type is part of an
     * empty whitelist.
     *
     * @param string|array $types File types to be checked
     * @param string|array $whitelist An array or string of whitelisted types
     * @return boolean
     */
    public function is_whitelisted($types, $whitelist) {
        return empty($this->get_not_whitelisted($types, $whitelist));
    }

    /**
     * Returns all types that are not part of the give whitelist.
     *
     * This is similar check to the {@link self::is_whitelisted()} but this one
     * actually returns the extra types.
     *
     * @param string|array $types File types to be checked
     * @param string|array $whitelist An array or string of whitelisted types
     * @return array Types not present in the whitelist
     */
    public function get_not_whitelisted($types, $whitelist) {

        $whitelistedtypes = $this->expand($whitelist, true, true);

        if (empty($whitelistedtypes) || $whitelistedtypes == ['*']) {
            return [];
        }

        $giventypes = $this->normalize_file_types($types);

        if (empty($giventypes)) {
            return [];
        }

        return array_diff($giventypes, $whitelistedtypes);
    }

    /**
     * Is the given filename of an allowed file type?
     *
     * Empty whitelist is interpretted as "any file type is allowed" rather
     * than "no file can be uploaded".
     *
     * @param string $filename the file name
     * @param string|array $whitelist list of allowed file extensions
     * @return boolean True if the file type is allowed, false if not
     */
    public function is_allowed_file_type($filename, $whitelist) {

        $allowedextensions = $this->expand($whitelist);

        if (empty($allowedextensions) || $allowedextensions == ['*']) {
            return true;
        }

        $haystack = strrev(trim(core_text::strtolower($filename)));

        foreach ($allowedextensions as $extension) {
            if (strpos($haystack, strrev($extension)) === 0) {
                // The file name ends with the extension.
                return true;
            }
        }

        return false;
    }

    /**
     * Returns file types from the list that are not recognized
     *
     * @param string|array $types list of user-defined file types
     * @return array A list of unknown file types.
     */
    public function get_unknown_file_types($types) {
        $unknown = [];

        foreach ($this->normalize_file_types($types) as $type) {
            if ($type === '*') {
                // Any file is considered as a known type.
                continue;
            } else if ($type === '.xxx') {
                $unknown[$type] = true;
            } else if ($this->is_filetype_group($type)) {
                // The type is a group that exists.
                continue;
            } else if ($this->looks_like_mimetype($type)) {
                // If there's no extension associated with that mimetype, we consider it unknown.
                if (empty(file_get_typegroup('extension', [$type]))) {
                    $unknown[$type] = true;
                }
            } else {
                $coretypes = core_filetypes::get_types();
                $typecleaned = str_replace(".", "", $type);
                if (empty($coretypes[$typecleaned])) {
                    // If there's no extension, it doesn't exist.
                    $unknown[$type] = true;
                }
            }
        }

        return array_keys($unknown);
    }
}
