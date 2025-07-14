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

namespace tool_dataprivacy;

use core\persistent;

/**
 * The dataprivacy_contextlist persistent.
 *
 * @package   tool_dataprivacy
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.3
 */
class dataprivacy_contextlist extends persistent {

    /** The table name this persistent object maps to. */
    const TABLE = 'tool_dataprivacy_contextlist';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'component' => [
                'type' => PARAM_TEXT,
            ],
        ];
    }

    /**
     * Create a new contextlist persistent from an instance of \core_privacy\local\request\contextlist.
     *
     * @param \core_privacy\local\request\contextlist $contextlist the core privacy contextlist.
     * @return dataprivacy_contextlist a dataprivacy_contextlist persistent.
     */
    public static function from_contextlist(\core_privacy\local\request\contextlist $contextlist): dataprivacy_contextlist {
        $contextlistpersistent = new dataprivacy_contextlist();
        return $contextlistpersistent->set('component', $contextlist->get_component());
    }
}
