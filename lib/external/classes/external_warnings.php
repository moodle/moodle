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
 * Standard Moodle web service warnings.
 *
 * @package    core_external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_warnings extends external_multiple_structure {

    /**
     * Constructor
     *
     * @param string $itemdesc
     * @param string $itemiddesc
     * @param string $warningcodedesc
     */
    public function __construct(
        $itemdesc = 'item',
        $itemiddesc = 'item id',
        $warningcodedesc = 'the warning code can be used by the client app to implement specific behaviour'
    ) {
        parent::__construct(
            new external_single_structure([
                'item' => new external_value(PARAM_TEXT, $itemdesc, VALUE_OPTIONAL),
                'itemid' => new external_value(PARAM_INT, $itemiddesc, VALUE_OPTIONAL),
                'warningcode' => new external_value(PARAM_ALPHANUM, $warningcodedesc),
                'message' => new external_value(PARAM_RAW, 'untranslated english message to explain the warning'),
            ], 'warning'),
            'list of warnings',
            VALUE_OPTIONAL
        );
    }
}
