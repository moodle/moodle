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

defined('MOODLE_INTERNAL') || die();

/**
 * Strategy interface defines all methods that strategy subplugins has to implemens
 */
interface workshop_strategy_interface {

    /**
     * Factory method returning an instance of an assessment form editor class
     * 
     * The returned class will probably expand the base workshop_edit_strategy_form
     *
     * @param string $actionurl URL of the action handler script
     * @param boolean $edit Open the form in editing mode
     * @param int $nodimensions Number of dimensions to be provided by set_data
     * @access public
     * @return object The instance of the assessment form editor class
     */
    public function get_edit_strategy_form($actionurl, $edit=true, $nodimensions=0);


    /**
     * Load the assessment dimensions from database
     * 
     * Assessment dimension (also know as assessment element) represents one aspect or criterion 
     * to be evaluated. Each dimension consists of a set of form fields. Strategy-specific information
     * are saved in workshop_forms_{strategyname} tables.
     *
     * @access public
     * @return array Array of database records
     */
    public function load_dimensions();


    /**
     * Save the assessment dimensions into database
     *
     * Assessment dimension (also know as assessment element) represents one aspect or criterion 
     * to be evaluated. Each dimension consists of a set of form fields. Strategy-specific information
     * are saved in workshop_forms_{strategyname} tables.
     *
     * @access public
     * @return void
     */
    public function save_dimensions($data);
}

/**
 * Base class for grading strategy logic
 *
 * This base class implements the default behaviour that should be suitable for the most
 * of simple grading strategies.
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


    /**
     * Factory method returning an instance of an assessment form editor class
     *
     * By default, the class is defined in grading/{strategy}/gradingform.php and is named
     * workshop_edit_{strategy}_strategy_form
     */
    public function get_edit_strategy_form($actionurl, $edit=true, $nodimensions=0) {
        global $CFG;    // needed because the included files use it
    
        $strategyform = dirname(__FILE__) . '/' . $this->name . '/gradingform.php';
        if (file_exists($strategyform)) {
            require_once($strategyform);
        } else {
            throw new moodle_exception('errloadingstrategyform', 'workshop');
        }
        $classname = 'workshop_edit_' . $this->name . '_strategy_form';

        return new $classname($this, $actionurl, $edit, $nodimensions);

    }


    /**
     * Load the assessment dimensions from database
     * 
     * This base method just fetches all relevant records from the main strategy form table.
     *
     * @uses $DB
     * @access public
     * @return void
     */
    public function load_dimensions() {
        global $DB;

        return $DB->get_records('workshop_forms_' . $this->name, array('workshopid' => $this->_workshop->id), 'sort');
    }


    /**
     * Save the assessment dimensions into database
     *
     * This base method saves data into the main strategy form table. If the record->id is null or zero,
     * new record is created. If the record->id is not empty, the existing record is updated. Records with
     * empty 'description' field are not saved.
     * The passed data object are the raw data returned by the get_data(). They must be cooked here.
     *
     * @uses $DB
     * @param object $data Raw data returned by the dimension editor form
     * @access public
     * @return void
     */
    public function save_dimensions($data) {
        global $DB;
        
        $data = $this->cook_form_data($data);

        foreach ($data as $record) {
            if (empty($record->description)) {
                continue;
            }
            if (empty($record->id)) {
                // new field
                $record->id = $DB->insert_record('workshop_forms_' . $this->name, $record);
            } else {
                // exiting field
                $DB->update_record('workshop_forms_' . $this->name, $record);
            }
        }
    }


    /**
     * The default implementation transposes the returned structure
     *
     * It automatically adds 'sort' column and 'workshopid' column into every record.
     * The sorting is done by the order of the returned array and starts with 1.
     * 
     * @param object $raw 
     * @return void
     */
    protected function cook_form_data($raw) {

        $cook = array();
        foreach (array_flip($this->map_dimension_fieldnames()) as $formfield => $dbfield) {
            for ($k = 0; $k < $raw->numofdimensions; $k++) {
                $cook[$k]->{$dbfield}   = isset($raw->{$formfield}[$k]) ? $raw->{$formfield}[$k] : null;
                $cook[$k]->sort         = $k + 1;
                $cook[$k]->workshopid   = $raw->workshopid;
            }
        }
        return $cook;
    }


    /**
     * Return the mapping of the db fields to the form fields for every assessment dimension
     *
     * This must be public because it is also used by the dimensions editor class.
     * 
     * @return array Array ['field_db_name' => 'field_form_name']
     */
    public function map_dimension_fieldnames() {
        return array();
    }



}
