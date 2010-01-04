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
interface workshop_strategy {

    /**
     * Factory method returning an instance of an assessment form editor class
     * 
     * The returned class will probably expand the base workshop_edit_strategy_form. The number of
     * dimensions that will be passed by set_data() must be already known here becase the
     * definition() of the form has to know the number and it is called before set_data().
     *
     * @param string $actionurl URL of the action handler script, defaults to auto detect
     * @access public
     * @return object The instance of the assessment form editor class
     */
    public function get_edit_strategy_form($actionurl=null);


    /**
     * Load the assessment dimensions and other grading form elements
     * 
     * Assessment dimension (also know as assessment element) represents one aspect or criterion 
     * to be evaluated. Each dimension consists of a set of form fields. Strategy-specific information
     * are saved in workshop_forms_{strategyname} tables.
     * The returned object is passed to the mform set_data() method.
     *
     * @access public
     * @return object Object representing the form fields values
     */
    public function load_form();


    /**
     * Save the assessment dimensions and other grading form elements
     *
     * Assessment dimension (also know as assessment element) represents one aspect or criterion 
     * to be evaluated. Each dimension consists of a set of form fields. Strategy-specific information
     * are saved in workshop_forms_{strategyname} tables.
     *
     * @access public
     * @param object $data Raw data as returned by the form editor
     * @return void
     */
    public function save_form(stdClass $data);


    /**
     * Return the number of assessment dimensions defined in the instance of the strategy
     * 
     * @return int Zero or positive integer
     */
    public function get_number_of_dimensions();

}



/**
 * Base class for grading strategy logic
 *
 * This base class implements the default behaviour that should be suitable for the most
 * of simple grading strategies.
 */
abstract class workshop_base_strategy implements workshop_strategy {

    /** the name of the strategy */
    public $name;

    /** the parent workshop instance */
    protected $workshop;

    /** number of dimensions defined in database */
    protected $nodimensions;

    /**
     * Constructor 
     * 
     * @param object $workshop The workshop instance record
     * @access public
     * @return void
     */
    public function __construct($workshop) {

        $this->name         = $workshop->strategy;
        $this->workshop     = $workshop;
        $this->nodimensions = null;
    }


    /**
     * Factory method returning an instance of an assessment form editor class
     *
     * By default, the class is defined in grading/{strategy}/edit_form.php and is named
     * workshop_edit_{strategy}_strategy_form
     *
     * @param $actionurl URL of form handler, defaults to auto detect the current url
     */
    public function get_edit_strategy_form($actionurl=null) {
        global $CFG;    // needed because the included files use it
    
        $strategyform = dirname(__FILE__) . '/' . $this->name . '/edit_form.php';
        if (file_exists($strategyform)) {
            require_once($strategyform);
        } else {
            throw new moodle_exception('errloadingstrategyform', 'workshop');
        }
        $classname = 'workshop_edit_' . $this->name . '_strategy_form';

        $customdata = new stdClass;
        $customdata = array(
                        'strategy'      => $this,
                        );
        $attributes = array('class' => 'editstrategyform');

        return new $classname($actionurl, $customdata, 'post', '', $attributes);

    }


    /**
     * By default, the number of loaded dimensions is set by load_form() 
     * 
     * @access public
     * @return Array of records
     */
    public function get_number_of_dimensions() {
        return $this->nodimensions;
    }


    /**
     * Factory method returning an instance of an assessment form
     *
     * By default, the class is defined in grading/{strategy}/assessment_form.php and is named
     * workshop_{strategy}_assessment_form
     *
     * @param string $actionurl URL of form handler, defaults to auto detect the current url
     * @param string $mode Mode to open the form in: preview/assessment
     */
    public function get_assessment_form($actionurl=null, $mode='preview') {
        global $CFG;    // needed because the included files use it
    
        $assessmentform = dirname(__FILE__) . '/' . $this->name . '/assessment_form.php';
        if (is_readable($assessmentform)) {
            require_once($assessmentform);
        } else {
            throw new moodle_exception('errloadingassessmentform', 'workshop');
        }
        $classname = 'workshop_' . $this->name . '_assessment_form';

        $customdata = new stdClass;
        $customdata = array(
                        'strategy'  => $this,
                        'fields'    => $this->load_form(),
                        'mode'      => $mode,
                        );
        $attributes = array('class' => 'assessmentform');

        return new $classname($actionurl, $customdata, 'post', '', $attributes);

    }


}
