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
 * @package    gradingform_rubric
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/grade/grading/form/lib.php');

/**
 * This controller encapsulates the rubric grading logic
 *
 * @package    gradingform_rubric
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradingform_rubric_controller extends gradingform_controller {
    // Modes of displaying the rubric (used in gradingform_rubric_renderer)
    /** Rubric display mode: For editing (moderator or teacher creates a rubric) */
    const DISPLAY_EDIT_FULL     = 1;
    /** Rubric display mode: Preview the rubric design with hidden fields */
    const DISPLAY_EDIT_FROZEN   = 2;
    /** Rubric display mode: Preview the rubric design (for person with manage permission) */
    const DISPLAY_PREVIEW       = 3;
    /** Rubric display mode: Preview the rubric (for people being graded) */
    const DISPLAY_PREVIEW_GRADED= 8;
    /** Rubric display mode: For evaluation, enabled (teacher grades a student) */
    const DISPLAY_EVAL          = 4;
    /** Rubric display mode: For evaluation, with hidden fields */
    const DISPLAY_EVAL_FROZEN   = 5;
    /** Rubric display mode: Teacher reviews filled rubric */
    const DISPLAY_REVIEW        = 6;
    /** Rubric display mode: Dispaly filled rubric (i.e. students see their grades) */
    const DISPLAY_VIEW          = 7;

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
     * Extends the module navigation
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING and there is an area with the active grading method set to the given plugin.
     *
     * @param global_navigation $navigation {@link global_navigation}
     * @param navigation_node $node {@link navigation_node}
     */
    public function extend_navigation(global_navigation $navigation, navigation_node $node=null) {
        if (has_capability('moodle/grade:managegradingforms', $this->get_context())) {
            // no need for preview if user can manage forms, he will have link to manage.php in settings instead
            return;
        }
        if ($this->is_form_defined() && ($options = $this->get_options()) && !empty($options['alwaysshowdefinition'])) {
            $node->add(get_string('gradingof', 'gradingform_rubric', get_grading_manager($this->get_areaid())->get_area_title()),
                    new moodle_url('/grade/grading/form/'.$this->get_method_name().'/preview.php', array('areaid' => $this->get_areaid())),
                    settings_navigation::TYPE_CUSTOM);
        }
    }

    /**
     * Saves the rubric definition into the database
     *
     * @see parent::update_definition()
     * @param stdClass $newdefinition rubric definition data as coming from gradingform_rubric_editrubric::get_data()
     * @param int|null $usermodified optional userid of the author of the definition, defaults to the current user
     */
    public function update_definition(stdClass $newdefinition, $usermodified = null) {
        $this->update_or_check_rubric($newdefinition, $usermodified, true);
        if (isset($newdefinition->rubric['regrade']) && $newdefinition->rubric['regrade']) {
            $this->mark_for_regrade();
        }
    }

    /**
     * Either saves the rubric definition into the database or check if it has been changed.
     * Returns the level of changes:
     * 0 - no changes
     * 1 - only texts or criteria sortorders are changed, students probably do not require re-grading
     * 2 - added levels but maximum score on rubric is the same, students still may not require re-grading
     * 3 - removed criteria or added levels or changed number of points, students require re-grading but may be re-graded automatically
     * 4 - removed levels - students require re-grading and not all students may be re-graded automatically
     * 5 - added criteria - all students require manual re-grading
     *
     * @param stdClass $newdefinition rubric definition data as coming from gradingform_rubric_editrubric::get_data()
     * @param int|null $usermodified optional userid of the author of the definition, defaults to the current user
     * @param boolean $doupdate if true actually updates DB, otherwise performs a check
     *
     */
    public function update_or_check_rubric(stdClass $newdefinition, $usermodified = null, $doupdate = false) {
        global $DB;

        // firstly update the common definition data in the {grading_definition} table
        if ($this->definition === false) {
            if (!$doupdate) {
                // if we create the new definition there is no such thing as re-grading anyway
                return 5;
            }
            // if definition does not exist yet, create a blank one
            // (we need id to save files embedded in description)
            parent::update_definition(new stdClass(), $usermodified);
            parent::load_definition();
        }
        if (!isset($newdefinition->rubric['options'])) {
            $newdefinition->rubric['options'] = self::get_default_options();
        }
        $newdefinition->options = json_encode($newdefinition->rubric['options']);
        $editoroptions = self::description_form_field_options($this->get_context());
        $newdefinition = file_postupdate_standard_editor($newdefinition, 'description', $editoroptions, $this->get_context(),
            'grading', 'description', $this->definition->id);

        // reload the definition from the database
        $currentdefinition = $this->get_definition(true);

        // update rubric data
        $haschanges = array();
        if (empty($newdefinition->rubric['criteria'])) {
            $newcriteria = array();
        } else {
            $newcriteria = $newdefinition->rubric['criteria']; // new ones to be saved
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
            $criterionmaxscore = null;
            if (preg_match('/^NEWID\d+$/', $id)) {
                // insert criterion into DB
                $data = array('definitionid' => $this->definition->id, 'descriptionformat' => FORMAT_MOODLE); // TODO MDL-31235 format is not supported yet
                foreach ($criteriafields as $key) {
                    if (array_key_exists($key, $criterion)) {
                        $data[$key] = $criterion[$key];
                    }
                }
                if ($doupdate) {
                    $id = $DB->insert_record('gradingform_rubric_criteria', $data);
                }
                $haschanges[5] = true;
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
                    if ($doupdate) {
                        $DB->update_record('gradingform_rubric_criteria', $data);
                    }
                    $haschanges[1] = true;
                }
                // remove deleted levels from DB and calculate the maximum score for this criteria
                foreach ($currentcriteria[$id]['levels'] as $levelid => $currentlevel) {
                    if ($criterionmaxscore === null || $criterionmaxscore < $currentlevel['score']) {
                        $criterionmaxscore = $currentlevel['score'];
                    }
                    if (!array_key_exists($levelid, $levelsdata)) {
                        if ($doupdate) {
                            $DB->delete_records('gradingform_rubric_levels', array('id' => $levelid));
                        }
                        $haschanges[4] = true;
                    }
                }
            }
            foreach ($levelsdata as $levelid => $level) {
                if (isset($level['score'])) {
                    $level['score'] = (float)$level['score'];
                    if ($level['score']<0) {
                        $level['score'] = 0;
                    }
                }
                if (preg_match('/^NEWID\d+$/', $levelid)) {
                    // insert level into DB
                    $data = array('criterionid' => $id, 'definitionformat' => FORMAT_MOODLE); // TODO MDL-31235 format is not supported yet
                    foreach ($levelfields as $key) {
                        if (array_key_exists($key, $level)) {
                            $data[$key] = $level[$key];
                        }
                    }
                    if ($doupdate) {
                        $levelid = $DB->insert_record('gradingform_rubric_levels', $data);
                    }
                    if ($criterionmaxscore !== null && $criterionmaxscore >= $level['score']) {
                        // new level is added but the maximum score for this criteria did not change, re-grading may not be necessary
                        $haschanges[2] = true;
                    } else {
                        $haschanges[3] = true;
                    }
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
                        if ($doupdate) {
                            $DB->update_record('gradingform_rubric_levels', $data);
                        }
                        if (isset($data['score'])) {
                            $haschanges[3] = true;
                        }
                        $haschanges[1] = true;
                    }
                }
            }
        }
        // remove deleted criteria from DB
        foreach (array_keys($currentcriteria) as $id) {
            if (!array_key_exists($id, $newcriteria)) {
                if ($doupdate) {
                    $DB->delete_records('gradingform_rubric_criteria', array('id' => $id));
                    $DB->delete_records('gradingform_rubric_levels', array('criterionid' => $id));
                }
                $haschanges[3] = true;
            }
        }
        foreach (array('status', 'description', 'descriptionformat', 'name', 'options') as $key) {
            if (isset($newdefinition->$key) && $newdefinition->$key != $this->definition->$key) {
                $haschanges[1] = true;
            }
        }
        if ($usermodified && $usermodified != $this->definition->usermodified) {
            $haschanges[1] = true;
        }
        if (!count($haschanges)) {
            return 0;
        }
        if ($doupdate) {
            parent::update_definition($newdefinition, $usermodified);
            $this->load_definition();
        }
        // return the maximum level of changes
        $changelevels = array_keys($haschanges);
        sort($changelevels);
        return array_pop($changelevels);
    }

    /**
     * Marks all instances filled with this rubric with the status INSTANCE_STATUS_NEEDUPDATE
     */
    public function mark_for_regrade() {
        global $DB;
        if ($this->has_active_instances()) {
            $conditions = array('definitionid'  => $this->definition->id,
                        'status'  => gradingform_instance::INSTANCE_STATUS_ACTIVE);
            $DB->set_field('grading_instances', 'status', gradingform_instance::INSTANCE_STATUS_NEEDUPDATE, $conditions);
        }
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
             LEFT JOIN {gradingform_rubric_criteria} rc ON (rc.definitionid = gd.id)
             LEFT JOIN {gradingform_rubric_levels} rl ON (rl.criterionid = rc.id)
                 WHERE gd.areaid = :areaid AND gd.method = :method
              ORDER BY rc.sortorder,rl.score";
        $params = array('areaid' => $this->areaid, 'method' => $this->get_method_name());

        $rs = $DB->get_recordset_sql($sql, $params);
        $this->definition = false;
        foreach ($rs as $record) {
            // pick the common definition data
            if ($this->definition === false) {
                $this->definition = new stdClass();
                foreach (array('id', 'name', 'description', 'descriptionformat', 'status', 'copiedfromid',
                        'timecreated', 'usercreated', 'timemodified', 'usermodified', 'timecopied', 'options') as $fieldname) {
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
                    $value = $record->{'rl'.$fieldname};
                    if ($fieldname == 'score') {
                        $value = (float)$value; // To prevent display like 1.00000
                    }
                    $this->definition->rubric_criteria[$record->rcid]['levels'][$record->rlid][$fieldname] = $value;
                }
            }
        }
        $rs->close();
        $options = $this->get_options();
        if (!$options['sortlevelsasc']) {
            foreach (array_keys($this->definition->rubric_criteria) as $rcid) {
                $this->definition->rubric_criteria[$rcid]['levels'] = array_reverse($this->definition->rubric_criteria[$rcid]['levels'], true);
            }
        }
    }

    /**
     * Returns the default options for the rubric display
     *
     * @return array
     */
    public static function get_default_options() {
        $options = array(
            'sortlevelsasc' => 1,
            'alwaysshowdefinition' => 1,
            'showdescriptionteacher' => 1,
            'showdescriptionstudent' => 1,
            'showscoreteacher' => 1,
            'showscorestudent' => 1,
            'enableremarks' => 1,
            'showremarksstudent' => 1
        );
        return $options;
    }

    /**
     * Gets the options of this rubric definition, fills the missing options with default values
     *
     * @return array
     */
    public function get_options() {
        $options = self::get_default_options();
        if (!empty($this->definition->options)) {
            $thisoptions = json_decode($this->definition->options);
            foreach ($thisoptions as $option => $value) {
                $options[$option] = $value;
            }
        }
        return $options;
    }

    /**
     * Converts the current definition into an object suitable for the editor form's set_data()
     *
     * @param boolean $addemptycriterion whether to add an empty criterion if the rubric is completely empty (just being created)
     * @return stdClass
     */
    public function get_definition_for_editing($addemptycriterion = false) {

        $definition = $this->get_definition();
        $properties = new stdClass();
        $properties->areaid = $this->areaid;
        if ($definition) {
            foreach (array('id', 'name', 'description', 'descriptionformat', 'status') as $key) {
                $properties->$key = $definition->$key;
            }
            $options = self::description_form_field_options($this->get_context());
            $properties = file_prepare_standard_editor($properties, 'description', $options, $this->get_context(),
                'grading', 'description', $definition->id);
        }
        $properties->rubric = array('criteria' => array(), 'options' => $this->get_options());
        if (!empty($definition->rubric_criteria)) {
            $properties->rubric['criteria'] = $definition->rubric_criteria;
        } else if (!$definition && $addemptycriterion) {
            $properties->rubric['criteria'] = array('addcriterion' => 1);
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
        $old = $this->get_definition_for_editing();
        $new->description_editor = $old->description_editor;
        $new->rubric = array('criteria' => array(), 'options' => $old->rubric['options']);
        $newcritid = 1;
        $newlevid = 1;
        foreach ($old->rubric['criteria'] as $oldcritid => $oldcrit) {
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
            $new->rubric['criteria']['NEWID'.$newcritid] = $oldcrit;
            $newcritid++;
        }

        return $new;
    }

    /**
     * Options for displaying the rubric description field in the form
     *
     * @param object $context
     * @return array options for the form description field
     */
    public static function description_form_field_options($context) {
        global $CFG;
        return array(
            'maxfiles' => -1,
            'maxbytes' => get_user_max_upload_file_size($context, $CFG->maxbytes),
            'context'  => $context,
        );
    }

    /**
     * Formats the definition description for display on page
     *
     * @return string
     */
    public function get_formatted_description() {
        if (!isset($this->definition->description)) {
            return '';
        }
        $context = $this->get_context();

        $options = self::description_form_field_options($this->get_context());
        $description = file_rewrite_pluginfile_urls($this->definition->description, 'pluginfile.php', $context->id,
            'grading', 'description', $this->definition->id, $options);

        $formatoptions = array(
            'noclean' => false,
            'trusted' => false,
            'filter' => true,
            'context' => $context
        );
        return format_text($description, $this->definition->descriptionformat, $formatoptions);
    }

    /**
     * Returns the rubric plugin renderer
     *
     * @param moodle_page $page the target page
     * @return gradingform_rubric_renderer
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

        if (!$this->is_form_defined()) {
            throw new coding_exception('It is the caller\'s responsibility to make sure that the form is actually defined');
        }

        $criteria = $this->definition->rubric_criteria;
        $options = $this->get_options();
        $rubric = '';
        if (has_capability('moodle/grade:managegradingforms', $page->context)) {
            $showdescription = true;
        } else {
            if (empty($options['alwaysshowdefinition']))  {
                // ensure we don't display unless show rubric option enabled
                return '';
            }
            $showdescription = $options['showdescriptionstudent'];
        }
        $output = $this->get_renderer($page);
        if ($showdescription) {
            $rubric .= $output->box($this->get_formatted_description(), 'gradingform_rubric-description');
        }
        if (has_capability('moodle/grade:managegradingforms', $page->context)) {
            $rubric .= $output->display_rubric_mapping_explained($this->get_min_max_score());
            $rubric .= $output->display_rubric($criteria, $options, self::DISPLAY_PREVIEW, 'rubric');
        } else {
            $rubric .= $output->display_rubric($criteria, $options, self::DISPLAY_PREVIEW_GRADED, 'rubric');
        }

        return $rubric;
    }

    /**
     * Deletes the rubric definition and all the associated information
     */
    protected function delete_plugin_definition() {
        global $DB;

        // get the list of instances
        $instances = array_keys($DB->get_records('grading_instances', array('definitionid' => $this->definition->id), '', 'id'));
        // delete all fillings
        $DB->delete_records_list('gradingform_rubric_fillings', 'instanceid', $instances);
        // delete instances
        $DB->delete_records_list('grading_instances', 'id', $instances);
        // get the list of criteria records
        $criteria = array_keys($DB->get_records('gradingform_rubric_criteria', array('definitionid' => $this->definition->id), '', 'id'));
        // delete levels
        $DB->delete_records_list('gradingform_rubric_levels', 'criterionid', $criteria);
        // delete critera
        $DB->delete_records_list('gradingform_rubric_criteria', 'id', $criteria);
    }

    /**
     * If instanceid is specified and grading instance exists and it is created by this rater for
     * this item, this instance is returned.
     * If there exists a draft for this raterid+itemid, take this draft (this is the change from parent)
     * Otherwise new instance is created for the specified rater and itemid
     *
     * @param int $instanceid
     * @param int $raterid
     * @param int $itemid
     * @return gradingform_instance
     */
    public function get_or_create_instance($instanceid, $raterid, $itemid) {
        global $DB;
        if ($instanceid &&
                $instance = $DB->get_record('grading_instances', array('id'  => $instanceid, 'raterid' => $raterid, 'itemid' => $itemid), '*', IGNORE_MISSING)) {
            return $this->get_instance($instance);
        }
        if ($itemid && $raterid) {
            $params = array('definitionid' => $this->definition->id, 'raterid' => $raterid, 'itemid' => $itemid);
            if ($rs = $DB->get_records('grading_instances', $params, 'timemodified DESC', '*', 0, 1)) {
                $record = reset($rs);
                $currentinstance = $this->get_current_instance($raterid, $itemid);
                if ($record->status == gradingform_rubric_instance::INSTANCE_STATUS_INCOMPLETE &&
                        (!$currentinstance || $record->timemodified > $currentinstance->get_data('timemodified'))) {
                    $record->isrestored = true;
                    return $this->get_instance($record);
                }
            }
        }
        return $this->create_instance($raterid, $itemid);
    }

    /**
     * Returns html code to be included in student's feedback.
     *
     * @param moodle_page $page
     * @param int $itemid
     * @param array $gradinginfo result of function grade_get_grades
     * @param string $defaultcontent default string to be returned if no active grading is found
     * @param boolean $cangrade whether current user has capability to grade in this context
     * @return string
     */
    public function render_grade($page, $itemid, $gradinginfo, $defaultcontent, $cangrade) {
        return $this->get_renderer($page)->display_instances($this->get_active_instances($itemid), $defaultcontent, $cangrade);
    }

    // ///// full-text search support /////////////////////////////////////////////

    /**
     * Prepare the part of the search query to append to the FROM statement
     *
     * @param string $gdid the alias of grading_definitions.id column used by the caller
     * @return string
     */
    public static function sql_search_from_tables($gdid) {
        return " LEFT JOIN {gradingform_rubric_criteria} rc ON (rc.definitionid = $gdid)
                 LEFT JOIN {gradingform_rubric_levels} rl ON (rl.criterionid = rc.id)";
    }

    /**
     * Prepare the parts of the SQL WHERE statement to search for the given token
     *
     * The returned array cosists of the list of SQL comparions and the list of
     * respective parameters for the comparisons. The returned chunks will be joined
     * with other conditions using the OR operator.
     *
     * @param string $token token to search for
     * @return array
     */
    public static function sql_search_where($token) {
        global $DB;

        $subsql = array();
        $params = array();

        // search in rubric criteria description
        $subsql[] = $DB->sql_like('rc.description', '?', false, false);
        $params[] = '%'.$DB->sql_like_escape($token).'%';

        // search in rubric levels definition
        $subsql[] = $DB->sql_like('rl.definition', '?', false, false);
        $params[] = '%'.$DB->sql_like_escape($token).'%';

        return array($subsql, $params);
    }

    /**
     * Calculates and returns the possible minimum and maximum score (in points) for this rubric
     *
     * @return array
     */
    public function get_min_max_score() {
        if (!$this->is_form_available()) {
            return null;
        }
        $returnvalue = array('minscore' => 0, 'maxscore' => 0);
        foreach ($this->get_definition()->rubric_criteria as $id => $criterion) {
            $scores = array();
            foreach ($criterion['levels'] as $level) {
                $scores[] = $level['score'];
            }
            sort($scores);
            $returnvalue['minscore'] += $scores[0];
            $returnvalue['maxscore'] += $scores[sizeof($scores)-1];
        }
        return $returnvalue;
    }

    /**
     * @return array An array containing a single key/value pair with the 'rubric_criteria' external_multiple_structure.
     * @see gradingform_controller::get_external_definition_details()
     * @since Moodle 2.5
     */
    public static function get_external_definition_details() {
        $rubric_criteria = new external_multiple_structure(
            new external_single_structure(
                array(
                   'id'   => new external_value(PARAM_INT, 'criterion id'),
                   'sortorder' => new external_value(PARAM_INT, 'sortorder'),
                   'description' => new external_value(PARAM_RAW, 'description', VALUE_OPTIONAL),
                   'descriptionformat' => new external_format_value('description', VALUE_OPTIONAL),
                   'levels' => new external_multiple_structure(
                                   new external_single_structure(
                                       array(
                                        'id' => new external_value(PARAM_INT, 'level id'),
                                        'score' => new external_value(PARAM_FLOAT, 'score'),
                                        'definition' => new external_value(PARAM_RAW, 'definition', VALUE_OPTIONAL),
                                        'definitionformat' => new external_format_value('definition', VALUE_OPTIONAL)
                                       )
                                  ), 'levels', VALUE_OPTIONAL
                              )
                   )
              ), 'definition details', VALUE_OPTIONAL
        );
        return array('rubric_criteria' => $rubric_criteria);
    }

}

/**
 * Class to manage one rubric grading instance.
 *
 * Stores information and performs actions like update, copy, validate, submit, etc.
 *
 * @package    gradingform_rubric
 * @copyright  2011 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradingform_rubric_instance extends gradingform_instance {

    /** @var array stores the rubric, has two keys: 'criteria' and 'options' */
    protected $rubric;

    /**
     * Deletes this (INCOMPLETE) instance from database.
     */
    public function cancel() {
        global $DB;
        parent::cancel();
        $DB->delete_records('gradingform_rubric_fillings', array('instanceid' => $this->get_id()));
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
        foreach ($currentgrade['criteria'] as $criterionid => $record) {
            $params = array('instanceid' => $instanceid, 'criterionid' => $criterionid,
                'levelid' => $record['levelid'], 'remark' => $record['remark'], 'remarkformat' => $record['remarkformat']);
            $DB->insert_record('gradingform_rubric_fillings', $params);
        }
        return $instanceid;
    }

    /**
     * Validates that rubric is fully completed and contains valid grade on each criterion
     *
     * @param array $elementvalue value of element as came in form submit
     * @return boolean true if the form data is validated and contains no errors
     */
    public function validate_grading_element($elementvalue) {
        $criteria = $this->get_controller()->get_definition()->rubric_criteria;
        if (!isset($elementvalue['criteria']) || !is_array($elementvalue['criteria']) || sizeof($elementvalue['criteria']) < sizeof($criteria)) {
            return false;
        }
        foreach ($criteria as $id => $criterion) {
            if (!isset($elementvalue['criteria'][$id]['levelid'])
                    || !array_key_exists($elementvalue['criteria'][$id]['levelid'], $criterion['levels'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Retrieves from DB and returns the data how this rubric was filled
     *
     * @param boolean $force whether to force DB query even if the data is cached
     * @return array
     */
    public function get_rubric_filling($force = false) {
        global $DB;
        if ($this->rubric === null || $force) {
            $records = $DB->get_records('gradingform_rubric_fillings', array('instanceid' => $this->get_id()));
            $this->rubric = array('criteria' => array());
            foreach ($records as $record) {
                $this->rubric['criteria'][$record->criterionid] = (array)$record;
            }
        }
        return $this->rubric;
    }

    /**
     * Updates the instance with the data received from grading form. This function may be
     * called via AJAX when grading is not yet completed, so it does not change the
     * status of the instance.
     *
     * @param array $data
     */
    public function update($data) {
        global $DB;
        $currentgrade = $this->get_rubric_filling();
        parent::update($data);
        foreach ($data['criteria'] as $criterionid => $record) {
            if (!array_key_exists($criterionid, $currentgrade['criteria'])) {
                $newrecord = array('instanceid' => $this->get_id(), 'criterionid' => $criterionid,
                    'levelid' => $record['levelid'], 'remarkformat' => FORMAT_MOODLE);
                if (isset($record['remark'])) {
                    $newrecord['remark'] = $record['remark'];
                }
                $DB->insert_record('gradingform_rubric_fillings', $newrecord);
            } else {
                $newrecord = array('id' => $currentgrade['criteria'][$criterionid]['id']);
                foreach (array('levelid', 'remark'/*, 'remarkformat' */) as $key) {
                    // TODO MDL-31235 format is not supported yet
                    if (isset($record[$key]) && $currentgrade['criteria'][$criterionid][$key] != $record[$key]) {
                        $newrecord[$key] = $record[$key];
                    }
                }
                if (count($newrecord) > 1) {
                    $DB->update_record('gradingform_rubric_fillings', $newrecord);
                }
            }
        }
        foreach ($currentgrade['criteria'] as $criterionid => $record) {
            if (!array_key_exists($criterionid, $data['criteria'])) {
                $DB->delete_records('gradingform_rubric_fillings', array('id' => $record['id']));
            }
        }
        $this->get_rubric_filling(true);
    }

    /**
     * Calculates the grade to be pushed to the gradebook
     *
     * @return float|int the valid grade from $this->get_controller()->get_grade_range()
     */
    public function get_grade() {
        $grade = $this->get_rubric_filling();

        if (!($scores = $this->get_controller()->get_min_max_score()) || $scores['maxscore'] <= $scores['minscore']) {
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
        foreach ($grade['criteria'] as $id => $record) {
            $curscore += $this->get_controller()->get_definition()->rubric_criteria[$id]['levels'][$record['levelid']]['score'];
        }
        $gradeoffset = ($curscore-$scores['minscore'])/($scores['maxscore']-$scores['minscore'])*($maxgrade-$mingrade);
        if ($this->get_controller()->get_allow_grade_decimals()) {
            return $gradeoffset + $mingrade;
        }
        return round($gradeoffset, 0) + $mingrade;
    }

    /**
     * Returns html for form element of type 'grading'.
     *
     * @param moodle_page $page
     * @param MoodleQuickForm_grading $gradingformelement
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
        $options = $this->get_controller()->get_options();
        $value = $gradingformelement->getValue();
        $html = '';
        if ($value === null) {
            $value = $this->get_rubric_filling();
        } else if (!$this->validate_grading_element($value)) {
            $html .= html_writer::tag('div', get_string('rubricnotcompleted', 'gradingform_rubric'), array('class' => 'gradingform_rubric-error'));
        }
        $currentinstance = $this->get_current_instance();
        if ($currentinstance && $currentinstance->get_status() == gradingform_instance::INSTANCE_STATUS_NEEDUPDATE) {
            $html .= html_writer::tag('div', get_string('needregrademessage', 'gradingform_rubric'), array('class' => 'gradingform_rubric-regrade'));
        }
        $haschanges = false;
        if ($currentinstance) {
            $curfilling = $currentinstance->get_rubric_filling();
            foreach ($curfilling['criteria'] as $criterionid => $curvalues) {
                $value['criteria'][$criterionid]['savedlevelid'] = $curvalues['levelid'];
                $newremark = null;
                $newlevelid = null;
                if (isset($value['criteria'][$criterionid]['remark'])) $newremark = $value['criteria'][$criterionid]['remark'];
                if (isset($value['criteria'][$criterionid]['levelid'])) $newlevelid = $value['criteria'][$criterionid]['levelid'];
                if ($newlevelid != $curvalues['levelid'] || $newremark != $curvalues['remark']) {
                    $haschanges = true;
                }
            }
        }
        if ($this->get_data('isrestored') && $haschanges) {
            $html .= html_writer::tag('div', get_string('restoredfromdraft', 'gradingform_rubric'), array('class' => 'gradingform_rubric-restored'));
        }
        if (!empty($options['showdescriptionteacher'])) {
            $html .= html_writer::tag('div', $this->get_controller()->get_formatted_description(), array('class' => 'gradingform_rubric-description'));
        }
        $html .= $this->get_controller()->get_renderer($page)->display_rubric($criteria, $options, $mode, $gradingformelement->getName(), $value);
        return $html;
    }
}
