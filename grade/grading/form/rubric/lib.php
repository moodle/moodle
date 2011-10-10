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

require_once($CFG->dirroot.'/grade/grading/form/lib.php'); // parent class

/**
 * This controller encapsulates the rubric grading logic
 */
class gradingform_rubric_controller extends gradingform_controller {
    protected $_rubric;

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
        // TODO check permission to edit rubric
        $node->add(get_string('definerubric', 'gradingform_rubric'),
            new moodle_url('/grade/grading/form/rubric/edit.php', array('areaid' => $this->areaid)), settings_navigation::TYPE_CUSTOM,
            null, null, new pix_icon('icon', '', 'gradingform_rubric'));
    }

    public function make_grading_widget($raterid, $itemid, $bulk = false) {
        return new gradingform_rubric_widget();
    }

    protected function get_method_name() {
        return 'rubric';
    }

    /**
     * Updates DB with the changes
     *
     * @param $properties array or object of elements ($form->get_data())
     * @param boolean $force specifies whether usermodified/timemodified should be explicitly updated even if $properties is empty. Usually false for this class
     */
    public function update($properties, $force = false) {
        global $DB;
        $parentmodified = parent::update($properties, $force);

        if (!empty($properties)) {
            $properties = (array)$properties;
        } else {
            $properties = array();
        }
        $haschanges = false;
        if (array_key_exists('rubric', $properties)) {
            $rubricdata = $properties['rubric'];
            $rubric = $this->get_rubric();
            $criteriafields = array('sortorder', 'description', 'descriptionformat');
            $levelfields = array('score', 'definition', 'definitionformat');
            foreach ($rubricdata as $id => $criterion) {
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
                        if (array_key_exists($key, $criterion) && $criterion[$key] != $rubric[$id][$key]) {
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
                    foreach (array_keys($rubric[$id]['levels']) as $levelid) {
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
                            if (array_key_exists($key, $level) && $level[$key] != $rubric[$id]['levels'][$levelid][$key]) {
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
            foreach (array_keys($rubric) as $id) {
                if (!array_key_exists($id, $rubricdata)) {
                    $DB->delete_records('gradingform_rubric_criteria', array('id' => $id));
                    $DB->delete_records('gradingform_rubric_levels', array('criterionid' => $id));
                    $haschanges = true;
                }
            }
            $this->get_rubric($haschanges);
        }

        // update time modified if was not updated before
        if (!$parentmodified && ($force || $haschanges)) {
            parent::update($properties, true);
        }
    }

    /**
     * Returns the criteria/levels information stored in DB for current rubric
     *
     * @param boolean $force if true retrieves from DB even if cached result exists
     * @return array
     */
    public function get_rubric($force = false) {
        global $DB;
        if ($this->_rubric === null || $force) {
            if (!$this->definition) {
                return array(); // definition does not exist yet
            }
            $this->_rubric = array();
            $records = $DB->get_records('gradingform_rubric_criteria', array('formid' => $this->definition->id), 'sortorder, id');
            foreach ($records as $criterion) {
                $this->_rubric[$criterion->id] = array(
                    'description' => $criterion->description,
                    'descriptionformat' => $criterion->descriptionformat,
                    'sortorder' => $criterion->sortorder,
                    'levels' => array(),
                );
                $levels = $DB->get_records('gradingform_rubric_levels', array('criterionid' => $criterion->id), 'score, id'); // TODO sort order may be DESC!
                foreach ($levels as $level) {
                    $this->_rubric[$criterion->id]['levels'][$level->id] = array(
                        'definition' => $level->definition,
                        'definitionformat' => $level->definitionformat,
                        'score' => (float)$level->score,
                    );
                }
            }
        }
        // TODO descriptionformat and definitionformat are not used at the moment
        return $this->_rubric;
    }

    /**
     * returns an object for edit-rubric-form
     *
     * @return object
     */
    public function get_data_for_edit() {
        //TODO the following code may be moved to parent
        $properties = new stdClass();
        if ($this->definition) {
            foreach (array('id', 'name', 'description', 'descriptionformat', 'options', 'status') as $key) {
                $properties->$key = $this->definition->$key;
            }
        }
        $properties->areaid = $this->areaid;
        if ($this->definition) {
            $options = self::description_form_field_options($this->get_context());
            $properties = file_prepare_standard_editor($properties, 'description', $options, $this->get_context(), 'gradingform_rubric' /* TODO or gradingform? */, 'definition_description', $this->definition->id);
        }
        //TODO end // $properties = parent::get_data_for_edit();

        $properties->rubric = $this->get_rubric();
        return $properties;
    }

    // TODO the following functions may be moved to parent:

    public static function description_form_field_options($context) {
        global $CFG;
        return array(
            'maxfiles' => -1,
            'maxbytes' => get_max_upload_file_size($CFG->maxbytes),
            'context' => $context,
        );
    }

    public function get_formatted_description() {
        if (!$this->definition) {
            return null;
        }
        $context = $this->get_context();

        $options = self::description_form_field_options($this->get_context());
        $description = file_rewrite_pluginfile_urls($this->definition->description, 'pluginfile.php', $context->id, 'gradingform_rubric' /* TODO or gradingform? */, 'definition_description', $this->definition->id, $options);

        $formatoptions = array(
            'noclean' => false,
            'trusted' => false,
            'filter' => true,
            'context' => $context
        );
        return format_text($description, $this->definition->descriptionformat, $formatoptions);
    }

    public function postupdate_data($data) {
        if (!$this->definition) {
            return $data;
        }
        $options = self::description_form_field_options($this->get_context());
        $data = file_postupdate_standard_editor($data, 'description', $options, $this->get_context(), 'gradingform_rubric' /* TODO or gradingform? */, 'definition_description', $this->definition->id);
            // TODO change filearea for embedded files in grading_definition.description
        return $data;
    }
}

class gradingform_rubric_widget extends gradingform_widget {

    /** @var string unique identifier */
    public $id;

}