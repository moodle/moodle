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
 * @package    moodlecore
 * @subpackage backup-helper
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Non instantiable helper class providing general helper methods for backup/restore
 *
 * This class contains various general helper static methods available for backup/restore
 *
 * TODO: Finish phpdocs
 */
abstract class backup_general_helper extends backup_helper {

    /**
     * Calculate one checksum for any array/object. Works recursively
     */
    public static function array_checksum_recursive($arr) {

        $checksum = ''; // Init checksum

        // Check we are going to process one array always, objects must be cast before
        if (!is_array($arr)) {
            throw new backup_helper_exception('array_expected');
        }
        foreach ($arr as $key => $value) {
            if ($value instanceof checksumable) {
                $checksum = md5($checksum . '-' . $key . '-' . $value->calculate_checksum());
            } else if (is_object($value)) {
                $checksum = md5($checksum . '-' . $key . '-' . self::array_checksum_recursive((array)$value));
            } else if (is_array($value)) {
                $checksum = md5($checksum . '-' . $key . '-' . self::array_checksum_recursive($value));
            } else {
                $checksum = md5($checksum . '-' . $key . '-' . $value);
            }
        }
        return $checksum;
    }

    /**
     * Given one temp/backup/xxx dir, detect its format
     *
     * TODO: Move harcoded detection here to delegated classes under backup/format (moodle1, imscc..)
     *       conversion code will be there too.
     */
    public static function detect_backup_format($tempdir) {
        global $CFG;

        // First look for MOODLE (moodle2) format
        $filepath = $CFG->dataroot . '/temp/backup/' . $tempdir . '/moodle_backup.xml';
        if (file_exists($filepath)) { // Looks promising, lets load some information
            $handle = fopen ($filepath, "r");
            $first_chars = fread($handle,200);
            $status = fclose ($handle);
            // Check if it has the required strings
            if (strpos($first_chars,'<?xml version="1.0" encoding="UTF-8"?>') !== false &&
                strpos($first_chars,'<moodle_backup>') !== false &&
                strpos($first_chars,'<information>') !== false) {
                    return backup::FORMAT_MOODLE;
            }
        }

        // Then look for MOODLE1 (moodle1) format
        $filepath = $CFG->dataroot . '/temp/backup/' . $tempdir . '/moodle.xml';
        if (file_exists($filepath)) { // Looks promising, lets load some information
            $handle = fopen ($filepath, "r");
            $first_chars = fread($handle,200);
            $status = fclose ($handle);
            // Check if it has the required strings
            if (strpos($first_chars,'<?xml version="1.0" encoding="UTF-8"?>') !== false &&
                strpos($first_chars,'<MOODLE_BACKUP>') !== false &&
                strpos($first_chars,'<INFO>') !== false) {
                    return backup::FORMAT_MOODLE1;
            }
        }

        // Other formats

        // Arrived here, unknown format
        return backup::FORMAT_UNKNOWN;
    }
}
