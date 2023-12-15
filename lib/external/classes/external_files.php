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

namespace core_external;

/**
 * External structure representing a set of files.
 *
 * @package    core_external
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_files extends external_multiple_structure {
    /**
     * Constructor
     * @param string $desc Description for the multiple structure.
     * @param int $required The type of value (VALUE_REQUIRED OR VALUE_OPTIONAL).
     */
    public function __construct($desc = 'List of files.', $required = VALUE_REQUIRED) {
        parent::__construct(
            new external_single_structure([
                'filename' => new external_value(PARAM_FILE, 'File name.', VALUE_OPTIONAL),
                'filepath' => new external_value(PARAM_PATH, 'File path.', VALUE_OPTIONAL),
                'filesize' => new external_value(PARAM_INT, 'File size.', VALUE_OPTIONAL),
                'fileurl' => new external_value(PARAM_URL, 'Downloadable file url.', VALUE_OPTIONAL),
                'timemodified' => new external_value(PARAM_INT, 'Time modified.', VALUE_OPTIONAL),
                'mimetype' => new external_value(PARAM_RAW, 'File mime type.', VALUE_OPTIONAL),
                'isexternalfile' => new external_value(PARAM_BOOL, 'Whether is an external file.', VALUE_OPTIONAL),
                'repositorytype' => new external_value(PARAM_PLUGIN, 'The repository type for external files.', VALUE_OPTIONAL),
            ], 'File.'),
            $desc,
            $required,
        );
    }

    /**
     * Return the properties ready to be used by an exporter.
     *
     * @return array properties
     * @since  Moodle 3.3
     */
    public static function get_properties_for_exporter() {
        return [
            'filename' => [
                'type' => PARAM_FILE,
                'description' => 'File name.',
                'optional' => true,
                'null' => NULL_NOT_ALLOWED,
            ],
            'filepath' => [
                'type' => PARAM_PATH,
                'description' => 'File path.',
                'optional' => true,
                'null' => NULL_NOT_ALLOWED,
            ],
            'filesize' => [
                'type' => PARAM_INT,
                'description' => 'File size.',
                'optional' => true,
                'null' => NULL_NOT_ALLOWED,
            ],
            'fileurl' => [
                'type' => PARAM_URL,
                'description' => 'Downloadable file url.',
                'optional' => true,
                'null' => NULL_NOT_ALLOWED,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'Time modified.',
                'optional' => true,
                'null' => NULL_NOT_ALLOWED,
            ],
            'mimetype' => [
                'type' => PARAM_RAW,
                'description' => 'File mime type.',
                'optional' => true,
                'null' => NULL_NOT_ALLOWED,
            ],
            'isexternalfile' => [
                'type' => PARAM_BOOL,
                'description' => 'Whether is an external file.',
                'optional' => true,
                'null' => NULL_NOT_ALLOWED,
            ],
            'repositorytype' => [
                'type' => PARAM_PLUGIN,
                'description' => 'The repository type for the external files.',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
        ];
    }
}
