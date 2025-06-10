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
 * Class storage
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\persistent;

use local_intellidata\persistent\base;

/**
 * Class storage
 *
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class logs extends base {

    /** The table name. */
    const TABLE = 'local_intellidata_logs';

    /** Type file export. */
    const TYPE_FILE_EXPORT = 'fileexport';
    /** The file created. */
    const ACTION_CREATED = 'c';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'type' => [
                'type' => PARAM_TEXT,
                'description' => 'Log type.',
            ],
            'datatype' => [
                'type' => PARAM_TEXT,
                'description' => 'Datatype.',
            ],
            'action' => [
                'type' => PARAM_TEXT,
                'description' => 'Action.',
            ],
            'details' => [
                'type' => PARAM_RAW,
                'description' => 'Details.',
            ],
            'count_in_file' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Count of exported records in file',
            ],
        ];
    }
}
