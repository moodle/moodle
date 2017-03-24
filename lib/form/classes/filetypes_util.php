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
     * @param string|array $extensions list of file extensions, groups or mimetypes
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
            $type = strtolower($type);
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
            } else if ($this->is_filetype_group($type)) {
                // All good, it is a known type group.
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
                $desc = get_string('any', 'core_mimetypes');
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
}
