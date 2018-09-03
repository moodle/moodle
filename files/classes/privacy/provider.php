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
 * Data provider.
 *
 * @package    core_files
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_files\privacy;
defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;

/**
 * Data provider class.
 *
 * This only describes the files table, all components must handle the file exporting
 * and deletion themselves.
 *
 * @package    core_files
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\plugin_provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        $collection->add_database_table('files', [
            'contenthash' => 'privacy:metadata:files:contenthash',
            'filepath' => 'privacy:metadata:files:filepath',
            'filename' => 'privacy:metadata:files:filename',
            'userid' => 'privacy:metadata:files:userid',
            'filesize' => 'privacy:metadata:files:filesize',
            'mimetype' => 'privacy:metadata:files:mimetype',
            'source' => 'privacy:metadata:files:source',
            'author' => 'privacy:metadata:files:author',
            'license' => 'privacy:metadata:files:license',
            'timecreated' => 'privacy:metadata:files:timecreated',
            'timemodified' => 'privacy:metadata:files:timemodified',
        ], 'privacy:metadata:files');

        return $collection;
    }

}
