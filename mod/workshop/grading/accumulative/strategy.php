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
 * This file defines a class with accumulative grading strategy logic
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); //  It must be included from a Moodle page
}

require_once(dirname(dirname(__FILE__)) . '/strategy.php'); // parent class


/**
 * Accumulative grading strategy logic.
 */
class workshop_accumulative_strategy extends workshop_strategy {

    /**
     * Mapping of the db fields to the form fields for every dimension of assessment
     *
     * @return array Array ['field_db_name' => 'field_form_name']
     */
    public function map_dimension_fieldnames() {
        return array(
                'id'                => 'dimensionid',
                'description'       => 'description',
                'descriptionformat' => 'descriptionformat',
                'grade'             => 'grade',
                'weight'            => 'weight',
            );
    }


}
