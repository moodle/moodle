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
 * This file defines a base class for all grading strategy editing forms.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); //  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php'); // parent class definition


/**
 * Base class for editing all the strategy grading forms.
 *
 * This defines the common fields that all strategy grading forms need. 
 * Strategies should define their own  class that inherits from this one, and 
 * implements the definition_inner() method.
 * 
 * @uses moodleform
 */
class workshop_edit_strategy_form extends moodleform {

    /**
     * Number of dimensions that are populated from DB
     * 
     * @var mixed
     * @access protected
     */
    protected $numofdimensions;

    /**
     * Constructor
     * 
     * @param str $actionurl URL to handle data
     * @param bool $editable Should the form be editable?
     * @param int $initialdimensions Number of assessment dimensions that are already filled
     * @access public
     * @return void
     */
    public function __construct($actionurl, $editable=true, $initialdimensions=0) {

        $this->numofdimensions = $initialdimensions;
        parent::moodleform($actionurl, null, 'post', '', array('class' => 'editstrategyform'), $editable);
    }


    /**
     * Add the fields that are common for all grading strategies.
     *
     * If the strategy does not support all these fields, then you can override 
     * this method and remove the ones you don't want with 
     * $mform->removeElement().
     * Stretegy subclassess should define their own fields in definition_inner()
     * 
     * @access public
     * @return void
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('hidden', 'workshopid');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'strategy');
        $mform->setType('id', PARAM_ALPHA);

        $this->definition_inner($mform);

        if (!empty($CFG->usetags)) {
            $mform->addElement('header', 'tagsheader', get_string('tags'));
            $mform->addElement('tags', 'tags', get_string('tags'));
        }

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }


    /**
     * Add any strategy specific form fields.
     *
     * @param object $mform the form being built.
     */
    protected function definition_inner(&$mform) {
        // By default, do nothing.
    }


    /**
     * Set the form data before it is displayed
     *
     * Strategy plugins should provide the list of fields to be mapped from 
     * DB record to the form fields in their get_dimension_fieldnames() method
     * 
     * @param object $formdata Should contain the array $formdata->dimensions
     * @access public
     * @return void
     */
    public function set_data($formdata) {

        if (is_array($formdata->dimensions) && !empty($formdata->dimensions)) {
            // $formdata->dimensions must be array of dimension records loaded from database
            $key = 0;
            $default_values = array();
            foreach ($formdata->dimensions as $dimension) {
                foreach ($this->get_dimension_fieldnames() as $fielddbname => $fieldformname) {
                    $default_values[$fieldformname . '[' . $key . ']'] = $dimension->$fielddbname;
                }
                $key++;
            }
            $formdata = (object)((array)$formdata + $default_values);
        }
        parent::set_data($formdata);
    }


    /**
     * Return the mapping of the db fields to the form fields for every assessment dimension
     * 
     * @access protected
     * @return array Array ['field_db_name' => 'field_form_name']
     */
    protected function get_dimension_fieldnames() {
        return array();
    }

}
