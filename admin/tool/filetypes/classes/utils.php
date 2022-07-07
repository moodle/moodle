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
 * Class with static back-end methods used by the file type tool.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_filetypes;

defined('MOODLE_INTERNAL') || die();

/**
 * Class with static back-end methods used by the file type tool.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {
    /**
     * Checks if the given file type extension is invalid.
     * The added file type extension must be unique and must not begin with a dot.
     *
     * @param string $extension Extension of the file type to add
     * @param string $oldextension Extension prior to update (empty string if adding new type)
     * @return bool True if it the file type trying to add already exists
     */
    public static function is_extension_invalid($extension, $oldextension = '') {
        $extension = trim($extension);
        if ($extension === '' || $extension[0] === '.') {
            return true;
        }

        $mimeinfo = get_mimetypes_array();
        if ($oldextension !== '') {
            unset($mimeinfo[$oldextension]);
        }

        return array_key_exists($extension, $mimeinfo);
    }

    /**
     * Checks if we are allowed to turn on the 'default icon' option. You can
     * only have one of these for a given MIME type.
     *
     * @param string $mimetype MIME type
     * @param string $oldextension File extension name (before any change)
     */
    public static function is_defaulticon_allowed($mimetype, $oldextension = '') {
        $mimeinfo = get_mimetypes_array();
        if ($oldextension !== '') {
            unset($mimeinfo[$oldextension]);
        }
        foreach ($mimeinfo as $extension => $values) {
            if ($values['type'] !== $mimetype) {
                continue;
            }
            if (!empty($values['defaulticon'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Gets all unique file type icons from a specific path, not including
     * sub-directories.
     *
     * Icon files such as pdf.png, pdf-24.png and pdf-36.png etc. are counted as
     * the same icon type.
     *
     * The resultant array has both key and value set to the icon name prefix,
     * such as 'pdf' => 'pdf'.
     *
     * @param string $path The path of the icon path
     * @return array An array of unique file icons within the given path
     */
    public static function get_icons_from_path($path) {
        $icons = array();
        if ($handle = @opendir($path)) {
            while (($file = readdir($handle)) !== false) {
                $matches = array();
                if (preg_match('~(.+?)(?:-24|-32|-48|-64|-72|-80|-96|-128|-256)?\.(?:svg|gif|png)$~',
                        $file, $matches)) {
                    $key = $matches[1];
                    $icons[$key] = $key;
                }
            }
            closedir($handle);
        }
        ksort($icons);
        return $icons;
    }

    /**
     * Gets unique file type icons from pix/f folder.
     *
     * @return array An array of unique file type icons e.g. 'pdf' => 'pdf'
     */
    public static function get_file_icons() {
        global $CFG;
        $path = $CFG->dirroot . '/pix/f';
        return self::get_icons_from_path($path);
    }
}
