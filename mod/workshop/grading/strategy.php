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
 * This file defines a base class for all grading strategy logic
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); //  It must be included from a Moodle page
}


/**
 * Strategy interface defines all methods that strategy subplugins has to implemens
 */
interface workshop_strategy_interface {

    /**
     * Load the assessment dimensions from database
     * 
     * Assessment dimension (also know as assessment element) represents one aspect or criterion 
     * to be evaluated. Each dimension consists of a set of form fields. Strategy-specific information
     * are saved in workshop_forms_{strategyname} tables.
     *
     * @uses $DB
     * @access public
     * @return void
     */
    public function load_dimensions();

}

/**
 * Base class for grading strategy logic.
 */
class workshop_strategy implements workshop_strategy_interface {

    /** the name of the strategy */
    public $name;

    /** the parent workshop instance */
    protected $_workshop;

    /**
     * Constructor 
     * 
     * @param object $workshop The workshop instance record
     * @access public
     * @return void
     */
    public function __construct($workshop) {

        $this->name         = $workshop->strategy;
        $this->_workshop    = $workshop;
    }


    public function get_edit_strategy_form($actionurl, $edit=true, $nodimensions=0) {
        global $CFG;    // needed because the included files use it
    
        $strategyform = dirname(__FILE__) . '/' . $this->name . '/gradingform.php';
        if (file_exists($strategyform)) {
            require_once($strategyform);
        } else {
            throw new moodle_exception('errloadingstrategyform', 'workshop');
        }
        $classname = 'workshop_edit_' . $this->name . '_strategy_form';

        return new $classname($actionurl, $edit, $nodimensions);

    }

    /**
     * Load the assessment dimensions from database
     * 
     * This base method just fetches all relevant records from the strategy form table.
     *
     * @uses $DB
     * @access public
     * @return void
     */
    public function load_dimensions() {
        global $DB;

        return $DB->get_records('workshop_forms_' . $this->name, array('workshopid' => $this->_workshop->id), 'sort');
    }

}
