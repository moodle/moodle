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
 * Course completion critieria - marked by role
 *
 * @package   moodlecore
 * @copyright 2009 Catalyst IT Ltd
 * @author    Aaron Barnes <aaronb@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_criteria_role extends completion_criteria {

    /**
     * Criteria type constant
     * @var int
     */
    public $criteriatype = COMPLETION_CRITERIA_TYPE_ROLE;

    /**
     * Finds and returns a data_object instance based on params.
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return object data_object instance or false if none found.
     */
    public static function fetch($params) {
        $params['criteriatype'] = COMPLETION_CRITERIA_TYPE_ROLE;
        return self::fetch_helper('course_completion_criteria', __CLASS__, $params);
    }

   /**
     * Add appropriate form elements to the critieria form
     * @access  public
     * @param   object  $mform  Moodle forms object
     * @param   mixed   $data   optional
     * @return  void
     */
    public function config_form_display(&$mform, $data = null) {

        $mform->addElement('checkbox', 'criteria_role['.$data->id.']', $data->name);

        if ($this->id) {
            $mform->setDefault('criteria_role['.$data->id.']', 1);
        }
    }

     /**
     * Update the criteria information stored in the database
     * @access  public
     * @param   array   $data   Form data
     * @return  void
     */
    public function update_config(&$data) {

        if (!empty($data->criteria_role) && is_array($data->criteria_role)) {

            $this->course = $data->id;

            foreach (array_keys($data->criteria_role) as $role) {

                $this->role = $role;
                $this->id = NULL;
                $this->insert();
            }
        }
    }

    /**
     * Mark this criteria as complete
     * @access  public
     * @param   object  $completion     The user's completion record
     * @return  void
     */
    public function complete($completion) {
        $this->review($completion, true, true);
    }

    /**
     * Review this criteria and decide if the user has completed
     * @access  public
     * @param   object  $completion     The user's completion record
     * @param   boolean $mark           Optionally set false to not save changes to database
     * @return  boolean
     */
    public function review($completion, $mark = true, $is_complete = false)  {
        // If we are marking this as complete
        if ($is_complete && $mark)
        {
            $completion->completedself = 1;
            $completion->mark_complete();

            return true;
        }

        return $completion->is_complete();
    }

    /**
     * Return criteria title for display in reports
     * @access  public
     * @return  string
     */
    public function get_title() {
        global $DB;
        $role = $DB->get_field('role', 'name', array('id' => $this->role));
        return $role;
    }

    /**
     * Return a more detailed criteria title for display in reports
     * @access  public
     * @return  string
     */
    public function get_title_detailed() {
        global $DB;
        return $DB->get_field('role', 'name', array('id' => $this->role));
    }

    /**
     * Return criteria type title for display in reports
     * @access  public
     * @return  string
     */
    public function get_type_title() {
        return get_string('approval', 'completion');
    }

    /**
     * Return criteria progress details for display in reports
     * @access  public
     * @param   object  $completion     The user's completion record
     * @return  array
     */
    public function get_details($completion) {
        $details = array();
        $details['type'] = get_string('manualcompletionby', 'completion');
        $details['criteria'] = $this->get_title();
        $details['requirement'] = get_string('markedcompleteby', 'completion', $details['criteria']);
        $details['status'] = '';

        return $details;
    }
}
