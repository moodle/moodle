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
            new moodle_url('/grade/grading/form/rubric/edit.php', array('areaid' => $this->areaid)), settings_navigation::TYPE_CUSTOM,
            null, null, new pix_icon('icon', '', 'gradingform_rubric'));
    }

    /**
     * Saves the rubric definition into the database
     *
     * @see parent::update_definition()
     * @param stdClass $newdefinition rubric definition data as coming from {@link self::postupdate_definition_data()}
     * @param int|null $usermodified optional userid of the author of the definition, defaults to the current user
     */
    public function update_definition(stdClass $newdefinition, $usermodified = null) {
        global $DB;

        // firstly update the common definition data in the {grading_definition} table
        parent::update_definition($newdefinition, $usermodified);
        // reload the definition from the database
        $this->load_definition();
        $currentdefinition = $this->get_definition();

        // update current data
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

    public function get_grading($raterid, $itemid) {
        global $DB;
        $sql = "SELECT f.id, f.criterionid, f.levelid, f.remark, f.remarkformat
                    FROM {grading_instances} i, {gradingform_rubric_fillings} f
                    WHERE i.formid = :formid ".
                    "AND i.raterid = :raterid ".
                    "AND i.itemid = :itemid
                    AND i.id = f.forminstanceid";
        $params = array('formid' => $this->definition->id, 'itemid' => $itemid, 'raterid' => $raterid);
        $rs = $DB->get_recordset_sql($sql, $params);
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
     * Converts the rubric data to the gradebook score 0-100
     */
    protected function calculate_grade($grade, $itemid) {
        if (!$this->validate_grading_element($grade, $itemid)) {
            return -1;
        }

        $minscore = 0;
        $maxscore = 0;
        foreach ($this->definition->rubric_criteria as $id => $criterion) {
            $keys = array_keys($criterion['levels']);
            // TODO array_reverse($keys) if levels are sorted DESC
            $minscore += $criterion['levels'][$keys[0]]['score'];
            $maxscore += $criterion['levels'][$keys[sizeof($keys)-1]]['score'];
        }

        if ($maxscore == 0) {
            return -1;
        }

        $curscore = 0;
        foreach ($grade as $id => $levelid) {
            $curscore += $this->definition->rubric_criteria[$id]['levels'][$levelid]['score'];
        }
        return $curscore/$maxscore*100; // TODO mapping
    }

    /**
     * Saves non-js data and returns the gradebook grade
     */
    public function save_and_get_grade($raterid, $itemid, $formdata) {
        global $DB, $USER;
        $instance = $this->prepare_instance($raterid, $itemid);
        $currentgrade = $this->get_grading($raterid, $itemid);
        if (!is_array($formdata)) {
            return $this->calculate_grade($currentgrade, $itemid);
        }
        foreach ($formdata as $criterionid => $levelid) {
            $params = array('forminstanceid' => $instance->id, 'criterionid' => $criterionid);
            if (!array_key_exists($criterionid, $currentgrade)) {
                $DB->insert_record('gradingform_rubric_fillings', $params + array('levelid' => $levelid));
            } else if ($currentgrade[$criterionid] != $levelid) {
                $DB->set_field('gradingform_rubric_fillings', 'levelid', $levelid, $params);
            }
        }
        foreach ($currentgrade as $criterionid => $levelid) {
            if (!array_key_exists($criterionid, $formdata)) {
                $params = array('forminstanceid' => $instance->id, 'criterionid' => $criterionid);
                $DB->delete_records('gradingform_rubric_fillings', $params);
            }
        }
        // TODO: remarks
        return $this->calculate_grade($formdata, $itemid);
    }

    /**
     * Returns html for form element
     */
    public function to_html($gradingformelement) {
        global $PAGE, $USER;
        //TODO move to renderer

        //$gradingrenderer = $this->prepare_renderer($PAGE);
        $html = '';
        $elementname = $gradingformelement->getName();
        $elementvalue = $gradingformelement->getValue();
        $submissionid = $gradingformelement->get_grading_attribute('submissionid');
        $raterid = $USER->id; // TODO - this is very strange!
        $html .= "assessing submission $submissionid<br />";
        //$html .= html_writer::empty_tag('input', array('type' => 'text', 'name' => $elementname.'[grade]', 'size' => '20', 'value' => $elementvalue['grade']));

        if (!$gradingformelement->_flagFrozen) {
            $module = array('name'=>'gradingform_rubric', 'fullpath'=>'/grade/grading/form/rubric/js/rubric.js');
            $PAGE->requires->js_init_call('M.gradingform_rubric.init', array(array('name' => $gradingformelement->getName(), 'criteriontemplate' =>'', 'leveltemplate' => '')), true, $module);
        }
        $criteria = $this->definition->rubric_criteria;

        $html .= html_writer::start_tag('div', array('id' => 'rubric-'.$gradingformelement->getName(), 'class' => 'form_rubric evaluate'));
        $criteria_cnt = 0;

        $value = $gradingformelement->getValue();
        if ($value === null) {
            $value = $this->get_grading($raterid, $submissionid); // TODO maybe implement in form->set_data() ?
        }

        foreach ($criteria as $criterionid => $criterion) {
            $html .= html_writer::start_tag('div', array('class' => 'criterion'.$this->get_css_class_suffix($criteria_cnt++, count($criteria)-1)));
            $html .= html_writer::tag('div', $criterion['description'], array('class' => 'description')); // TODO descriptionformat
            $html .= html_writer::start_tag('div', array('class' => 'levels'));
            $level_cnt = 0;
            foreach ($criterion['levels'] as $levelid => $level) {
                $checked = (is_array($value) && array_key_exists($criterionid, $value) && ((int)$value[$criterionid] === $levelid));
                $classsuffix = $this->get_css_class_suffix($level_cnt++, count($criterion['levels'])-1);
                if ($checked) {
                    $classsuffix .= ' checked';
                }
                $html .= html_writer::start_tag('div', array('id' => $gradingformelement->getName().'-'.$criterionid.'-levels-'.$levelid, 'class' => 'level'.$classsuffix));
                $input = html_writer::empty_tag('input', array('type' => 'radio', 'name' => $gradingformelement->getName().'['.$criterionid.']', 'value' => $levelid) +
                    ($checked ? array('checked' => 'checked') : array())); // TODO rewrite
                $html .= html_writer::tag('div', $input, array('class' => 'radio'));
                $html .= html_writer::tag('div', $level['definition'], array('class' => 'definition')); // TODO definitionformat
                $html .= html_writer::tag('div', (float)$level['score'].' pts', array('class' => 'score'));  //TODO span, get_string
                $html .= html_writer::end_tag('div'); // .level
            }
            $html .= html_writer::end_tag('div'); // .levels
            $html .= html_writer::end_tag('div'); // .criterion
        }
        $html .= html_writer::end_tag('div'); // .rubric
        return $html;

    }

    private function get_css_class_suffix($cnt, $maxcnt) {
        $class = '';
        if ($cnt == 0) {
            $class .= ' first';
        }
        if ($cnt == $maxcnt) {
            $class .= ' last';
        }
        if ($cnt%2) {
            $class .= ' odd';
        } else {
            $class .= ' even';
        }
        return $class;
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

    /**
     * Converts the rubric definition data from the submitted form back to the form
     * suitable for storing in database
     */
    public function postupdate_definition_data($data) {
        if (!$this->definition) {
            return $data;
        }
        $options = self::description_form_field_options($this->get_context());
        $data = file_postupdate_standard_editor($data, 'description', $options, $this->get_context(),
            'gradingform_rubric', 'definition_description', $this->definition->id);
            // TODO change filearea for embedded files in grading_definition.description
        return $data;
    }

    public function is_form_available($foruserid = null) {
        return true;
        // TODO this is temporary for testing!
    }

    /**
     * Returns the error message displayed in case of validation failed
     *
     * @see validate_grading_element
     */
    public function default_validation_error_message() {
        return 'The rubric is incomplete'; //TODO string
    }

    /**
     * Validates that rubric is fully completed and contains valid grade on each criterion
     */
    public function validate_grading_element($elementvalue, $itemid) {
        // TODO: if there is nothing selected in rubric, we don't enter this function at all :(
        $criteria = $this->definition->rubric_criteria;
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
}
