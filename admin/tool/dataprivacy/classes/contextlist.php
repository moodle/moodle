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
 * Contains the contextlist persistent.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * The contextlist persistent.
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contextlist extends persistent {

    /** The table name this persistent object maps to. */
    const TABLE = 'tool_dataprivacy_contextlist';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'component' => [
                'type' => PARAM_TEXT
            ]
        ];
    }

    /**
     * Create a new contextlist persistent from an instance of \core_privacy\local\request\contextlist.
     *
     * @param \core_privacy\local\request\contextlist $contextlist the core privacy contextlist.
     * @return contextlist a contextlist persistent.
     */
    public static function from_contextlist(\core_privacy\local\request\contextlist $contextlist) {
        $contextlistpersistent = new contextlist();
        return $contextlistpersistent->set('component', $contextlist->get_component());
    }
}
