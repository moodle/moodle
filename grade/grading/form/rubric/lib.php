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
}
