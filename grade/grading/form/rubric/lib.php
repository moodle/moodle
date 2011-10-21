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
 * Grading method controller for the Rubric plugin
 *
 * @package    gradingform
 * @subpackage rubric
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/grade/grading/form/lib.php');

/**
 * This controller encapsulates the rubric grading logic
 */
class gradingform_rubric_controller extends gradingform_controller {
    // Modes of displaying the rubric (used in gradingform_rubric_renderer)
    const DISPLAY_EDIT_FULL     = 1; // For editing (moderator or teacher creates a rubric)
    const DISPLAY_EDIT_FROZEN   = 2; // Preview the rubric design with hidden fields
    const DISPLAY_PREVIEW       = 3; // Preview the rubric design
    const DISPLAY_EVAL          = 4; // For evaluation, enabled (teacher grades a student)
    const DISPLAY_EVAL_FROZEN   = 5; // For evaluation, with hidden fields
    const DISPLAY_REVIEW        = 6; // Dispaly filled rubric (i.e. students see their grades)

    /**
     * Extends the module settings navigation with the rubric grading settings
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING, the user has the permission moodle/grade:managegradingforms
     * and there is an area with the active grading method set to 'rubric'.
     *
     * @param settings_navigation $settingsnav {@link settings_navigation}
     * @param navigation_node $node {@link navigation_node}
     */
    public function extend_settings_navigation(settings_navigation $settingsnav, navigation_node $node=null) {
        $node->add(get_string('definerubric', 'gradingform_rubric'),
            $this->get_editor_url(), settings_navigation::TYPE_CUSTOM,
            null, null, new pix_icon('icon', '', 'gradingform_rubric'));
    }

    /**
     * Saves the rubric definition into the database
     *
     * @see parent::update_definition()
     * @param stdClass $newdefinition rubric definition data as coming from gradingform_rubric_editrubric::get_data()
     * @param int|null $usermodified optional userid of the author of the definition, defaults to the current user
     */
    public function update_definition(stdClass $newdefinition, $usermodified = null) {
        global $DB;

        // firstly update the common definition data in the {grading_definition} table
        if ($this->definition === false) {
            // if definition does not exist yet, create a blank one with only required fields set
            // (we need id to save files embedded in description)
            parent::update_definition((object)array('descriptionformat' => FORMAT_MOODLE), $usermodified);
            parent::load_definition();
        }
        $options = self::description_form_field_options($this->get_context());
        $newdefinition = file_postupdate_standard_editor($newdefinition, 'description', $options, $this->get_context(),
            'gradingform_rubric', 'definition_description', $this->definition->id);
        parent::update_definition($newdefinition, $usermodified);

        // reload the definition from the database
        $currentdefinition = $this->get_definition(true);

        // update rubric data
        $haschanges = false;
        if (empty($newdefinition->rubric_criteria)) {
            $newcriteria = array();
        } else {
            $newcriteria = $newdefinition->rubric_criteria; // new ones to be saved
        }
        $currentcriteria = $currentdefinition->rubric_criteria;
        $criteriafields = array('sortorder', 'description', 'descriptionformat');
        $levelfields = array('score', 'definition', 'definitionformat');
        foreach ($newcriteria as $id => $criterion) {
            // get list of submitted levels
            $levelsdata = array();
            if (array_key_exists('levels', $criterion)) {
                $levelsdata = $criterion['levels'];
            }
            if (preg_match('/^NEWID\d+$/', $id)) {
                // insert criterion into DB
                $data = array('formid' => $this->definition->id, 'descriptionformat' => FORMAT_MOODLE); // TODO format is not supported yet
                foreach ($criteriafields as $key) {
                    if (array_key_exists($key, $criterion)) {
                        $data[$key] = $criterion[$key];
                    }
                }
                $id = $DB->insert_record('gradingform_rubric_criteria', $data);
                $haschanges = true;
            } else {
                // update criterion in DB
                $data = array();
                foreach ($criteriafields as $key) {
                    if (array_key_exists($key, $criterion) && $criterion[$key] != $currentcriteria[$id][$key]) {
                        $data[$key] = $criterion[$key];
                    }
                }
                if (!empty($data)) {
                    // update only if something is changed
                    $data['id'] = $id;
                    $DB->update_record('gradingform_rubric_criteria', $data);
                    $haschanges = true;
                }
                // remove deleted levels from DB
                foreach (array_keys($currentcriteria[$id]['levels']) as $levelid) {
                    if (!array_key_exists($levelid, $levelsdata)) {
                        $DB->delete_records('gradingform_rubric_levels', array('id' => $levelid));
                        $haschanges = true;
                    }
                }
            }
            foreach ($levelsdata as $levelid => $level) {
                if (preg_match('/^NEWID\d+$/', $levelid)) {
                    // insert level into DB
                    $data = array('criterionid' => $id, 'definitionformat' => FORMAT_MOODLE); // TODO format is not supported yet
                    foreach ($levelfields as $key) {
                        if (array_key_exists($key, $level)) {
                            $data[$key] = $level[$key];
                        }
                    }
                    $levelid = $DB->insert_record('gradingform_rubric_levels', $data);
                    $haschanges = true;
                } else {
                    // update level in DB
                    $data = array();
                    foreach ($levelfields as $key) {
                        if (array_key_exists($key, $level) && $level[$key] != $currentcriteria[$id]['levels'][$levelid][$key]) {
                            $data[$key] = $level[$key];
                        }
                    }
                    if (!empty($data)) {
                        // update only if something is changed
                        $data['id'] = $levelid;
                        $DB->update_record('gradingform_rubric_levels', $data);
                        $haschanges = true;
                    }
                }
            }
        }
        // remove deleted criteria from DB
        foreach (array_keys($currentcriteria) as $id) {
            if (!array_key_exists($id, $newcriteria)) {
                $DB->delete_records('gradingform_rubric_criteria', array('id' => $id));
                $DB->delete_records('gradingform_rubric_levels', array('criterionid' => $id));
                $haschanges = true;
            }
        }
        $this->load_definition();
    }

    /**
     * Loads the rubric form definition if it exists
     *
     * There is a new array called 'rubric_criteria' appended to the list of parent's definition properties.
     */
    protected function load_definition() {
        global $DB;

        $sql = "SELECT gd.*,
                       rc.id AS rcid, rc.sortorder AS rcsortorder, rc.description AS rcdescription, rc.descriptionformat AS rcdescriptionformat,
                       rl.id AS rlid, rl.score AS rlscore, rl.definition AS rldefinition, rl.definitionformat AS rldefinitionformat
                  FROM {grading_definitions} gd
             LEFT JOIN {gradingform_rubric_criteria} rc ON (rc.formid = gd.id)
             LEFT JOIN {gradingform_rubric_levels} rl ON (rl.criterionid = rc.id)
                 WHERE gd.areaid = :areaid AND gd.method = :method
              ORDER BY rc.sortorder,rl.score";
        $params = array('areaid' => $this->areaid, 'method' => $this->get_method_name());

        $rs = $DB->get_recordset_sql($sql, $params);
        $this->definition = false;
        foreach ($rs as $record) {
            // pick the common definition data
            if (empty($this->definition)) {
                $this->definition = new stdClass();
                foreach (array('id', 'name', 'description', 'descriptionformat', 'status', 'copiedfromid',
                        'timecreated', 'usercreated', 'timemodified', 'usermodified', 'options') as $fieldname) {
                    $this->definition->$fieldname = $record->$fieldname;
                }
                $this->definition->rubric_criteria = array();
            }
            // pick the criterion data
            if (!empty($record->rcid) and empty($this->definition->rubric_criteria[$record->rcid])) {
                foreach (array('id', 'sortorder', 'description', 'descriptionformat') as $fieldname) {
                    $this->definition->rubric_criteria[$record->rcid][$fieldname] = $record->{'rc'.$fieldname};
                }
                $this->definition->rubric_criteria[$record->rcid]['levels'] = array();
            }
            // pick the level data
            if (!empty($record->rlid)) {
                foreach (array('id', 'score', 'definition', 'definitionformat') as $fieldname) {
                    $this->definition->rubric_criteria[$record->rcid]['levels'][$record->rlid][$fieldname] = $record->{'rl'.$fieldname};
                }
            }
        }
        $rs->close();
    }

    /**
     * Converts the current definition into an object suitable for the editor form's set_data()
     *
     * @return stdClass
     */
    public function get_definition_for_editing() {

        $definition = $this->get_definition();
        $properties = new stdClass();
        $properties->areaid = $this->areaid;
        if ($definition) {
            foreach (array('id', 'name', 'description', 'descriptionformat', 'options', 'status') as $key) {
                $properties->$key = $definition->$key;
            }
            $options = self::description_form_field_options($this->get_context());
            $properties = file_prepare_standard_editor($properties, 'description', $options, $this->get_context(),
                'gradingform_rubric', 'definition_description', $definition->id);
        }
        if (!empty($definition->rubric_criteria)) {
            $properties->rubric_criteria = $definition->rubric_criteria;
        } else {
            $properties->rubric_criteria = array();
        }

        return $properties;
    }

    /**
     * Returns the form definition suitable for cloning into another area
     *
     * @see parent::get_definition_copy()
     * @param gradingform_controller $target the controller of the new copy
     * @return stdClass definition structure to pass to the target's {@link update_definition()}
     */
    public function get_definition_copy(gradingform_controller $target) {

        $new = parent::get_definition_copy($target);
        $old = $this->get_definition();
        $new->rubric_criteria = array();
        $newcritid = 1;
        $newlevid = 1;
        foreach ($old->rubric_criteria as $oldcritid => $oldcrit) {
            unset($oldcrit['id']);
            if (isset($oldcrit['levels'])) {
                foreach ($oldcrit['levels'] as $oldlevid => $oldlev) {
                    unset($oldlev['id']);
                    $oldcrit['levels']['NEWID'.$newlevid] = $oldlev;
                    unset($oldcrit['levels'][$oldlevid]);
                    $newlevid++;
                }
            } else {
                $oldcrit['levels'] = array();
            }
            $new->rubric_criteria['NEWID'.$newcritid] = $oldcrit;
            $newcritid++;
        }

        return $new;
    }

    // TODO the following functions may be moved to parent:

    /**
     * @return array options for the form description field
     */
    public static function description_form_field_options($context) {
        global $CFG;
        return array(
            'maxfiles' => -1,
            'maxbytes' => get_max_upload_file_size($CFG->maxbytes),
            'context'  => $context,
        );
    }

    public function get_formatted_description() {
        if (!$this->definition) {
            return null;
        }
        $context = $this->get_context();

        $options = self::description_form_field_options($this->get_context());
        $description = file_rewrite_pluginfile_urls($this->definition->description, 'pluginfile.php', $context->id,
            'gradingform_rubric', 'definition_description', $this->definition->id, $options);

        $formatoptions = array(
            'noclean' => false,
            'trusted' => false,
            'filter' => true,
            'context' => $context
        );
        return format_text($description, $this->definition->descriptionformat, $formatoptions);
    }

    public function is_form_available($foruserid = null) {
        return true;
        // TODO this is temporary for testing!
    }

    /**
     * Returns the rubric plugin renderer
     *
     * @param moodle_page $page the target page
     * @return renderer_base
     */
    public function get_renderer(moodle_page $page) {
        return $page->get_renderer('gradingform_'. $this->get_method_name());
    }

    /**
     * Returns the HTML code displaying the preview of the grading form
     *
     * @param moodle_page $page the target page
     * @return string
     */
    public function render_preview(moodle_page $page) {

        // use the parent's method to render the common information about the form
        $header = parent::render_preview($page);

        // append the rubric itself, using own renderer
        $output = $this->get_renderer($page);
        $criteria = $this->definition->rubric_criteria;
        $rubric = $output->display_rubric($criteria, self::DISPLAY_PREVIEW, 'rubric');

        return $header . $rubric;
    }

    /**
     * Deletes the rubric definition and all the associated information
     */
    protected function delete_plugin_definition() {
        global $DB;

        // get the list of instances
        $instances = array_keys($DB->get_records('grading_instances', array('formid' => $this->definition->id), '', 'id'));
        // delete all fillings
        $DB->delete_records_list('gradingform_rubric_fillings', 'forminstanceid', $instances);
        // delete instances
        $DB->delete_records_list('grading_instances', 'id', $instances);
        // get the list of criteria records
        $criteria = array_keys($DB->get_records('gradingform_rubric_criteria', array('formid' => $this->definition->id), '', 'id'));
        // delete levels
        $DB->delete_records_list('gradingform_rubric_levels', 'criterionid', $criteria);
        // delete critera
        $DB->delete_records_list('gradingform_rubric_criteria', 'id', $criteria);
    }

    /**
     * Returns html code to be included in student's feedback.
     *
     * @param moodle_page $page
     * @param int $itemid
     * @param array $grading_info result of function grade_get_grades
     * @param string $defaultcontent default string to be returned if no active grading is found
     * @return string
     */
    public function render_grade($page, $itemid, $grading_info, $defaultcontent) {
        $instances = $this->get_current_instances($itemid);
        return $this->get_renderer($page)->display_instances($this->get_current_instances($itemid), $defaultcontent);
    }
}

/**
 * Class to manage one rubric grading instance. Stores information and performs actions like
 * update, copy, validate, submit, etc.
 *
 * @copyright  2011 Marina Glancy
 */
class gradingform_rubric_instance extends gradingform_instance {

    /**
     * Deletes this (INCOMPLETE) instance from database.
     */
    public function cancel() {
        global $DB;
        parent::cancel();
        $DB->delete_records('gradingform_rubric_fillings', array('forminstanceid' => $this->get_id()));
    }

    /**
     * Duplicates the instance before editing (optionally substitutes raterid and/or itemid with
     * the specified values)
     *
     * @param int $raterid value for raterid in the duplicate
     * @param int $itemid value for itemid in the duplicate
     * @return int id of the new instance
     */
    public function copy($raterid, $itemid) {
        global $DB;
        $instanceid = parent::copy($raterid, $itemid);
        $currentgrade = $this->get_rubric_filling();
        foreach ($currentgrade as $criterionid => $levelid) {
            $params = array('forminstanceid' => $instanceid, 'criterionid' => $criterionid, 'levelid' => $levelid);
            $DB->insert_record('gradingform_rubric_fillings', $params);
        }
        // TODO remarks
        return $instanceid;
    }

    /**
     * Validates that rubric is fully completed and contains valid grade on each criterion
     * @return boolean true if the form data is validated and contains no errors
     */
    public function validate_grading_element($elementvalue) {
        // TODO: if there is nothing selected in rubric, we don't enter this function at all :(
        $criteria = $this->get_controller()->get_definition()->rubric_criteria;
        if (!is_array($elementvalue) || sizeof($elementvalue) < sizeof($criteria)) {
            return false;
        }
        foreach ($criteria as $id => $criterion) {
            if (!array_key_exists($id, $elementvalue) || !array_key_exists($elementvalue[$id], $criterion['levels'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Retrieves from DB and returns the data how this rubric was filled
     *
     * @return array
     */
    public function get_rubric_filling() {
        // TODO cache
        global $DB;
        $rs = $DB->get_records('gradingform_rubric_fillings', array('forminstanceid' => $this->get_id()));
        $grading = array();
        foreach ($rs as $record) {
            if ($record->levelid) {
                $grading[$record->criterionid] = $record->levelid;
            }
            // TODO: remarks
        }
        return $grading;
    }

    /**
     * Updates the instance with the data received from grading form. This function may be
     * called via AJAX when grading is not yet completed, so it does not change the
     * status of the instance.
     */
    public function update($data) {
        global $DB;
        $currentgrade = $this->get_rubric_filling();
        parent::update($data); // TODO ? +timemodified
        foreach ($data as $criterionid => $levelid) {
            $params = array('forminstanceid' => $this->get_id(), 'criterionid' => $criterionid);
            if (!array_key_exists($criterionid, $currentgrade)) {
                $DB->insert_record('gradingform_rubric_fillings', $params + array('levelid' => $levelid));
            } else if ($currentgrade[$criterionid] != $levelid) {
                $DB->set_field('gradingform_rubric_fillings', 'levelid', $levelid, $params);
            }
        }
        foreach ($currentgrade as $criterionid => $levelid) {
            if (!array_key_exists($criterionid, $data)) {
                $params = array('forminstanceid' => $this->get_id(), 'criterionid' => $criterionid);
                $DB->delete_records('gradingform_rubric_fillings', $params);
            }
        }
        // TODO: remarks
    }

    /**
     * Calculates the grade to be pushed to the gradebook
     *
     * @return int the valid grade from $this->get_controller()->get_grade_range()
     */
    public function get_grade() {
        global $DB, $USER;
        $grade = $this->get_rubric_filling();

        $minscore = 0;
        $maxscore = 0;
        foreach ($this->get_controller()->get_definition()->rubric_criteria as $id => $criterion) {
            $keys = array_keys($criterion['levels']);
            sort($keys);
            $minscore += $criterion['levels'][$keys[0]]['score'];
            $maxscore += $criterion['levels'][$keys[sizeof($keys)-1]]['score'];
        }

        if ($maxscore <= $minscore) {
            return -1;
        }

        $graderange = array_keys($this->get_controller()->get_grade_range());
        if (empty($graderange)) {
            return -1;
        }
        sort($graderange);
        $mingrade = $graderange[0];
        $maxgrade = $graderange[sizeof($graderange) - 1];

        $curscore = 0;
        foreach ($grade as $id => $levelid) {
            $curscore += $this->get_controller()->get_definition()->rubric_criteria[$id]['levels'][$levelid]['score'];
        }
        return round(($curscore-$minscore)/($maxscore-$minscore)*($maxgrade-$mingrade), 0) + $mingrade; // TODO mapping
    }

    /**
     * Returns the error message displayed in case of validation failed
     *
     * @return string
     */
    public function default_validation_error_message() {
        return 'The rubric is incomplete'; //TODO string
    }

    /**
     * Returns html for form element of type 'grading'.
     *
     * @param moodle_page $page
     * @param MoodleQuickForm_grading $formelement
     * @return string
     */
    public function render_grading_element($page, $gradingformelement) {
        global $USER;
        if (!$gradingformelement->_flagFrozen) {
            $module = array('name'=>'gradingform_rubric', 'fullpath'=>'/grade/grading/form/rubric/js/rubric.js');
            $page->requires->js_init_call('M.gradingform_rubric.init', array(array('name' => $gradingformelement->getName())), true, $module);
            $mode = gradingform_rubric_controller::DISPLAY_EVAL;
        } else {
            if ($gradingformelement->_persistantFreeze) {
                $mode = gradingform_rubric_controller::DISPLAY_EVAL_FROZEN;
            } else {
                $mode = gradingform_rubric_controller::DISPLAY_REVIEW;
            }
        }
        $criteria = $this->get_controller()->get_definition()->rubric_criteria;
        $value = $gradingformelement->getValue();
        if ($value === null) {
            $value = $this->get_rubric_filling();
        }
        return $this->get_controller()->get_renderer($page)->display_rubric($criteria, $mode, $gradingformelement->getName(), $value);
    }
}