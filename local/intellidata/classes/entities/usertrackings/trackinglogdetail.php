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
 * Class for preparing data Tracking Log Detail.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2021 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\usertrackings;

/**
 * Class for preparing data Tracking Log Detail.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2021 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class trackinglogdetail extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'trackinglogdetail';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Record ID.',
                'default' => 0,
            ],
            'logid' => [
                'type' => PARAM_INT,
                'description' => 'Log Id.',
                'default' => 0,
            ],
            'visits' => [
                'type' => PARAM_INT,
                'description' => 'Visits.',
                'default' => 0,
            ],
            'timespend' => [
                'type' => PARAM_INT,
                'description' => 'Timespend.',
                'default' => 0,
            ],
            'timepoint' => [
                'type' => PARAM_INT,
                'description' => 'Timepoint.',
                'default' => 0,
            ],
        ];
    }
}
