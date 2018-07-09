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
 * Contains the request_contextlist persistent.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * The request_contextlist persistent.
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class request_contextlist extends persistent {

    /** The table name this persistent object maps to. */
    const TABLE = 'tool_dataprivacy_rqst_ctxlst';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'requestid' => [
                'type' => PARAM_INT
            ],
            'contextlistid' => [
                'type' => PARAM_INT
            ]
        ];
    }

    /**
     * Creates a new relation, but does not persist it.
     *
     * @param int $requestid
     * @param int $contextlistid
     * @return $this
     * @throws \coding_exception
     */
    public static function create_relation($requestid, $contextlistid) {
        $requestcontextlist = new request_contextlist();
        return $requestcontextlist->set('requestid', $requestid)
            ->set('contextlistid', $contextlistid);
    }
}
