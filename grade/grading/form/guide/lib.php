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
 * Grading method controller for the guide plugin
 *
 * @package    gradingform_guide
 * @copyright  2012 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/grade/grading/form/lib.php');

/**
 * This controller encapsulates the guide grading logic
 *
 * @package    gradingform_guide
 * @copyright  2012 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradingform_guide_controller extends gradingform_controller {
    // Modes of displaying the guide (used in gradingform_guide_renderer).
    /** guide display mode: For editing (moderator or teacher creates a guide) */
    const DISPLAY_EDIT_FULL     = 1;
    /** guide display mode: Preview the guide design with hidden fields */
    const DISPLAY_EDIT_FROZEN   = 2;
    /** guide display mode: Preview the guide design (for person with manage permission) */
    const DISPLAY_PREVIEW       = 3;
    /** guide display mode: Preview the guide (for people being graded) */
    const DISPLAY_PREVIEW_GRADED= 8;
    /** guide display mode: For evaluation, enabled (teacher grades a student) */
    const DISPLAY_EVAL          = 4;
    /** guide display mode: For evaluation, with hidden fields */
    const DISPLAY_EVAL_FROZEN   = 5;
    /** guide display mode: Teacher reviews filled guide */
    const DISPLAY_REVIEW        = 6;
    /** guide display mode: Dispaly filled guide (i.e. students see their grades) */
    const DISPLAY_VIEW          = 7;

    /** @var stdClass|false the definition structure */
    protected $moduleinstance = false;

    /**
     * Extends the module settings navigation with the guide grading settings
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING, the user has the permission moodle/grade:managegradingforms
     * and there is an area with the active grading method set to 'guide'.
     *
     * @param settings_navigation $settingsnav {@link settings_navigation}
     * @param navigation_node $node {@link navigation_node}
     */
    public function extend_settings_navigation(settings_navigation $settingsnav, navigation_node $node=null) {
        $node->add(get_string('definemarkingguide', 'gradingform_guide'),
            $this->get_editor_url(), settings_navigation::TYPE_CUSTOM,
            null, null, new pix_icon('icon', '', 'gradingform_guide'));
    }

    /**
     * Extends the module navigation
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING and there is an area with the active grading method set to the given plugin.
     *
     * @param global_navigation $navigation {@link global_navigation}
     * @param navigation_node $node {@link navigation_node}
     * @return void
     */
    public function extend_navigation(global_navigation $navigation, navigation_node $node=null) {
        if (has_capability('moodle/grade:managegradingforms', $this->get_context())) {
            // No need for preview if user can manage forms, he will have link to manage.php in settings instead.
            return;
        }
        if ($this->is_form_defined() && ($options = $this->get_options()) && !empty($options['alwaysshowdefinition'])) {
            $node->add(get_string('gradingof', 'gradingform_guide', get_grading_manager($this->get_areaid())->get_area_title()),
                    new moodle_url('/grade/grading/form/'.$this->get_method_name().'/preview.php',
                        array('areaid' => $this->get_areaid())), settings_navigation::TYPE_CUSTOM);
        }
    }

    /**
     * Saves the guide definition into the database
     *
     * @see parent::update_definition()
     * @param stdClass $newdefinition guide definition data as coming from gradingform_guide_editguide::get_data()
     * @param int $usermodified optional userid of the author of the definition, defaults to the current user
     */
    public function update_definition(stdClass $newdefinition, $usermodified = null) {
        $this->update_or_check_guide($newdefinition, $usermodified, true);
        if (isset($newdefinition->guide['regrade']) && $newdefinition->guide['regrade']) {
            $this->mark_for_regrade();
        }
    }

    /**
     * Either saves the guide definition into the database or check if it has been changed.
     *
     * Returns the level of changes:
     * 0 - no changes
     * 1 - only texts or criteria sortorders are changed, students probably do not require re-grading
     * 2 - added levels but maximum score on guide is the same, students still may not require re-grading
     * 3 - removed criteria or changed number of points, students require re-grading but may be re-graded automatically
     * 4 - removed levels - students require re-grading and not all students may be re-graded automatically
     * 5 - added criteria - all students require manual re-grading
     *
     * @param stdClass $newdefinition guide definition data as coming from gradingform_guide_editguide::get_data()
     * @param int|null $usermodified optional userid of the author of the definition, defaults to the current user
     * @param bool $doupdate if true actually updates DB, otherwise performs a check
     * @return int
     */
    public function update_or_check_guide(stdClass $newdefinition, $usermodified = null, $doupdate = false) {
        global $DB;

        // Firstly update the common definition data in the {grading_definition} table.
        if ($this->definition === false) {
            if (!$doupdate) {
                // If we create the new definition there is no such thing as re-grading anyway.
                return 5;
            }
            // If definition does not exist yet, create a blank one
            // (we need id to save files embedded in description).
            parent::update_definition(new stdClass(), $usermodified);
            parent::load_definition();
        }
        if (!isset($newdefinition->guide['options'])) {
            $newdefinition->guide['options'] = self::get_default_options();
        }
        $newdefinition->options = json_encode($newdefinition->guide['options']);
        $editoroptions = self::description_form_field_options($this->get_context());
        $newdefinition = file_postupdate_standard_editor($newdefinition, 'description', $editoroptions, $this->get_context(),
            'grading', 'description', $this->definition->id);

        // Reload the definition from the database.
        $currentdefinition = $this->get_definition(true);

        // Update guide data.
        $haschanges = array();
        if (empty($newdefinition->guide['criteria'])) {
            $newcriteria = array();
        } else {
            $newcriteria = $newdefinition->guide['criteria']; // New ones to be saved.
        }
        $currentcriteria = $currentdefinition->guide_criteria;
        $criteriafields = array('sortorder', 'description', 'descriptionformat', 'descriptionmarkers',
            'descriptionmarkersformat', 'shortname', 'maxscore');
        foreach ($newcriteria as $id => $criterion) {
            if (preg_match('/^NEWID\d+$/', $id)) {
                // Insert criterion into DB.
                $data = array('definitionid' => $this->definition->id, 'descriptionformat' => FORMAT_MOODLE,
                    'descriptionmarkersformat' => FORMAT_MOODLE); // TODO format is not supported yet.
                foreach ($criteriafields as $key) {
                    if (array_key_exists($key, $criterion)) {
                        $data[$key] = $criterion[$key];
                    }
                }
                if ($doupdate) {
                    $id = $DB->insert_record('gradingform_guide_criteria', $data);
                }
                $haschanges[5] = true;
            } else {
                // Update criterion in DB.
                $data = array();
                foreach ($criteriafields as $key) {
                    if (array_key_exists($key, $criterion) && $criterion[$key] != $currentcriteria[$id][$key]) {
                        $data[$key] = $criterion[$key];
                    }
                }
                if (!empty($data)) {
                    // Update only if something is changed.
                    $data['id'] = $id;
                    if ($doupdate) {
                        $DB->update_record('gradingform_guide_criteria', $data);
                    }
                    $haschanges[1] = true;
                }
            }
        }
        // Remove deleted criteria from DB.
        foreach (array_keys($currentcriteria) as $id) {
            if (!array_key_exists($id, $newcriteria)) {
                if ($doupdate) {
                    $DB->delete_records('gradingform_guide_criteria', array('id' => $id));
                }
                $haschanges[3] = true;
            }
        }
        // Now handle comments.
        if (empty($newdefinition->guide['comments'])) {
            $newcomment = array();
        } else {
            $newcomment = $newdefinition->guide['comments']; // New ones to be saved.
        }
        $currentcomments = $currentdefinition->guide_comments;
        $commentfields = array('sortorder', 'description');
        foreach ($newcomment as $id => $comment) {
            if (preg_match('/^NEWID\d+$/', $id)) {
                // Insert criterion into DB.
                $data = array('definitionid' => $this->definition->id, 'descriptionformat' => FORMAT_MOODLE);
                foreach ($commentfields as $key) {
                    if (array_key_exists($key, $comment)) {
                        $data[$key] = $comment[$key];
                    }
                }
                if ($doupdate) {
                    $id = $DB->insert_record('gradingform_guide_comments', $data);
                }
            } else {
                // Update criterion in DB.
                $data = array();
                foreach ($commentfields as $key) {
                    if (array_key_exists($key, $comment) && $comment[$key] != $currentcomments[$id][$key]) {
                        $data[$key] = $comment[$key];
                    }
                }
                if (!empty($data)) {
                    // Update only if something is changed.
                    $data['id'] = $id;
                    if ($doupdate) {
                        $DB->update_record('gradingform_guide_comments', $data);
                    }
                }
            }
        }
        // Remove deleted criteria from DB.
        foreach (array_keys($currentcomments) as $id) {
            if (!array_key_exists($id, $newcomment)) {
                if ($doupdate) {
                    $DB->delete_records('gradingform_guide_comments', array('id' => $id));
                }
            }
        }
        // End comments handle.
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
        // Return the maximum level of changes.
        $changelevels = array_keys($haschanges);
        sort($changelevels);
        return array_pop($changelevels);
    }

    /**
     * Marks all instances filled with this guide with the status INSTANCE_STATUS_NEEDUPDATE
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
     * Loads the guide form definition if it exists
     *
     * There is a new array called 'guide_criteria' appended to the list of parent's definition properties.
     */
    protected function load_definition() {
        global $DB;

        // Check to see if the user prefs have changed - putting here as this function is called on post even when
        // validation on the page fails. - hard to find a better place to locate this as it is specific to the guide.
        $showdesc = optional_param('showmarkerdesc', null, PARAM_BOOL); // Check if we need to change pref.
        $showdescstudent = optional_param('showstudentdesc', null, PARAM_BOOL); // Check if we need to change pref.
        if ($showdesc !== null) {
            set_user_preference('gradingform_guide-showmarkerdesc', $showdesc);
        }
        if ($showdescstudent !== null) {
            set_user_preference('gradingform_guide-showstudentdesc', $showdescstudent);
        }

        // Get definition.
        $definition = $DB->get_record('grading_definitions', array('areaid' => $this->areaid,
            'method' => $this->get_method_name()), '*');
        if (!$definition) {
            // The definition doesn't have to exist. It may be that we are only now creating it.
            $this->definition = false;
            return false;
        }

        $this->definition = $definition;
        // Now get criteria.
        $this->definition->guide_criteria = array();
        $this->definition->guide_comments = array();
        $criteria = $DB->get_recordset('gradingform_guide_criteria', array('definitionid' => $this->definition->id), 'sortorder');
        foreach ($criteria as $criterion) {
            foreach (array('id', 'sortorder', 'description', 'descriptionformat',
                           'maxscore', 'descriptionmarkers', 'descriptionmarkersformat', 'shortname') as $fieldname) {
                if ($fieldname == 'maxscore') {  // Strip any trailing 0.
                    $this->definition->guide_criteria[$criterion->id][$fieldname] = (float)$criterion->{$fieldname};
                } else {
                    $this->definition->guide_criteria[$criterion->id][$fieldname] = $criterion->{$fieldname};
                }
            }
        }
        $criteria->close();

        // Now get comments.
        $comments = $DB->get_recordset('gradingform_guide_comments', array('definitionid' => $this->definition->id), 'sortorder');
        foreach ($comments as $comment) {
            foreach (array('id', 'sortorder', 'description', 'descriptionformat') as $fieldname) {
                $this->definition->guide_comments[$comment->id][$fieldname] = $comment->{$fieldname};
            }
        }
        $comments->close();
        if (empty($this->moduleinstance)) { // Only set if empty.
            $modulename = $this->get_component();
            $context = $this->get_context();
            if (strpos($modulename, 'mod_') === 0) {
                $dbman = $DB->get_manager();
                $modulename = substr($modulename, 4);
                if ($dbman->table_exists($modulename)) {
                    $cm = get_coursemodule_from_id($modulename, $context->instanceid);
                    if (!empty($cm)) { // This should only occur when the course is being deleted.
                        $this->moduleinstance = $DB->get_record($modulename, array("id"=>$cm->instance));
                    }
                }
            }
        }
    }

    /**
     * Returns the default options for the guide display
     *
     * @return array
     */
    public static function get_default_options() {
        $options = array(
            'alwaysshowdefinition' => 1,
            'showmarkspercriterionstudents' => 1,
        );
        return $options;
    }

    /**
     * Gets the options of this guide definition, fills the missing options with default values
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
     * @param bool $addemptycriterion whether to add an empty criterion if the guide is completely empty (just being created)
     * @return stdClass
     */
    public function get_definition_for_editing($addemptycriterion = false) {

        $definition = $this->get_definition();
        $properties = new stdClass();
        $properties->areaid = $this->areaid;
        if (isset($this->moduleinstance->grade)) {
            $properties->modulegrade = $this->moduleinstance->grade;
        }
        if ($definition) {
            foreach (array('id', 'name', 'description', 'descriptionformat', 'status') as $key) {
                $properties->$key = $definition->$key;
            }
            $options = self::description_form_field_options($this->get_context());
            $properties = file_prepare_standard_editor($properties, 'description', $options, $this->get_context(),
                'grading', 'description', $definition->id);
        }
        $properties->guide = array('criteria' => array(), 'options' => $this->get_options(), 'comments' => array());
        if (!empty($definition->guide_criteria)) {
            $properties->guide['criteria'] = $definition->guide_criteria;
        } else if (!$definition && $addemptycriterion) {
            $properties->guide['criteria'] = array('addcriterion' => 1);
        }
        if (!empty($definition->guide_comments)) {
            $properties->guide['comments'] = $definition->guide_comments;
        } else if (!$definition && $addemptycriterion) {
            $properties->guide['comments'] = array('addcomment' => 1);
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
        $new->guide = array('criteria' => array(), 'options' => $old->guide['options'], 'comments' => array());
        $newcritid = 1;
        foreach ($old->guide['criteria'] as $oldcritid => $oldcrit) {
            unset($oldcrit['id']);
            $new->guide['criteria']['NEWID'.$newcritid] = $oldcrit;
            $newcritid++;
        }
        $newcomid = 1;
        foreach ($old->guide['comments'] as $oldcritid => $oldcom) {
            unset($oldcom['id']);
            $new->guide['comments']['NEWID'.$newcomid] = $oldcom;
            $newcomid++;
        }
        return $new;
    }

    /**
     * Options for displaying the guide description field in the form
     *
     * @param context $context
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
     * Returns the guide plugin renderer
     *
     * @param moodle_page $page the target page
     * @return gradingform_guide_renderer
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

        // Check if current user is able to see preview
        $options = $this->get_options();
        if (empty($options['alwaysshowdefinition']) && !has_capability('moodle/grade:managegradingforms', $page->context))  {
            return '';
        }

        $criteria = $this->definition->guide_criteria;
        $comments = $this->definition->guide_comments;
        $output = $this->get_renderer($page);

        $guide = '';
        $guide .= $output->box($this->get_formatted_description(), 'gradingform_guide-description');
        if (has_capability('moodle/grade:managegradingforms', $page->context)) {
            $guide .= $output->display_guide_mapping_explained($this->get_min_max_score());
            $guide .= $output->display_guide($criteria, $comments, $options, self::DISPLAY_PREVIEW, 'guide');
        } else {
            $guide .= $output->display_guide($criteria, $comments, $options, self::DISPLAY_PREVIEW_GRADED, 'guide');
        }

        return $guide;
    }

    /**
     * Deletes the guide definition and all the associated information
     */
    protected function delete_plugin_definition() {
        global $DB;

        // Get the list of instances.
        $instances = array_keys($DB->get_records('grading_instances', array('definitionid' => $this->definition->id), '', 'id'));
        // Delete all fillings.
        $DB->delete_records_list('gradingform_guide_fillings', 'instanceid', $instances);
        // Delete instances.
        $DB->delete_records_list('grading_instances', 'id', $instances);
        // Get the list of criteria records.
        $criteria = array_keys($DB->get_records('gradingform_guide_criteria',
            array('definitionid' => $this->definition->id), '', 'id'));
        // Delete critera.
        $DB->delete_records_list('gradingform_guide_criteria', 'id', $criteria);
        // Delete comments.
        $DB->delete_records('gradingform_guide_comments', array('definitionid' => $this->definition->id));
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
                $instance = $DB->get_record('grading_instances',
                    array('id'  => $instanceid, 'raterid' => $raterid, 'itemid' => $itemid), '*', IGNORE_MISSING)) {
            return $this->get_instance($instance);
        }
        if ($itemid && $raterid) {
            if ($rs = $DB->get_records('grading_instances', array('raterid' => $raterid, 'itemid' => $itemid),
                'timemodified DESC', '*', 0, 1)) {
                $record = reset($rs);
                $currentinstance = $this->get_current_instance($raterid, $itemid);
                if ($record->status == gradingform_guide_instance::INSTANCE_STATUS_INCOMPLETE &&
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
     * @param bool $cangrade whether current user has capability to grade in this context
     * @return string
     */
    public function render_grade($page, $itemid, $gradinginfo, $defaultcontent, $cangrade) {
        return $this->get_renderer($page)->display_instances($this->get_active_instances($itemid), $defaultcontent, $cangrade);
    }

    // Full-text search support.

    /**
     * Prepare the part of the search query to append to the FROM statement
     *
     * @param string $gdid the alias of grading_definitions.id column used by the caller
     * @return string
     */
    public static function sql_search_from_tables($gdid) {
        return " LEFT JOIN {gradingform_guide_criteria} gc ON (gc.definitionid = $gdid)";
    }

    /**
     * Prepare the parts of the SQL WHERE statement to search for the given token
     *
     * The returned array cosists of the list of SQL comparions and the list of
     * respective parameters for the comparisons. The returned chunks will be joined
     * with other conditions using the OR operator.
     *
     * @param string $token token to search for
     * @return array An array containing two more arrays
     *     Array of search SQL fragments
     *     Array of params for the search fragments
     */
    public static function sql_search_where($token) {
        global $DB;

        $subsql = array();
        $params = array();

        // Search in guide criteria description.
        $subsql[] = $DB->sql_like('gc.description', '?', false, false);
        $params[] = '%'.$DB->sql_like_escape($token).'%';

        return array($subsql, $params);
    }

    /**
     * Calculates and returns the possible minimum and maximum score (in points) for this guide
     *
     * @return array
     */
    public function get_min_max_score() {
        if (!$this->is_form_available()) {
            return null;
        }
        $returnvalue = array('minscore' => 0, 'maxscore' => 0);
        $maxscore = 0;
        foreach ($this->get_definition()->guide_criteria as $id => $criterion) {
            $maxscore += $criterion['maxscore'];
        }
        $returnvalue['maxscore'] = $maxscore;
        $returnvalue['minscore'] = 0;
        if (!empty($this->moduleinstance->grade)) {
            $graderange = make_grades_menu($this->moduleinstance->grade);
            $returnvalue['modulegrade'] = count($graderange) - 1;
        }
        return $returnvalue;
    }

    /**
     * @return array An array containing 2 key/value pairs which hold the external_multiple_structure
     * for the 'guide_criteria' and the 'guide_comments'.
     * @see gradingform_controller::get_external_definition_details()
     * @since Moodle 2.5
     */
    public static function get_external_definition_details() {
        $guide_criteria = new external_multiple_structure(
                              new external_single_structure(
                                  array(
                                      'id'   => new external_value(PARAM_INT, 'criterion id', VALUE_OPTIONAL),
                                      'sortorder' => new external_value(PARAM_INT, 'sortorder', VALUE_OPTIONAL),
                                      'description' => new external_value(PARAM_RAW, 'description', VALUE_OPTIONAL),
                                      'descriptionformat' => new external_format_value('description', VALUE_OPTIONAL),
                                      'shortname' => new external_value(PARAM_TEXT, 'description'),
                                      'descriptionmarkers' => new external_value(PARAM_RAW, 'markers description', VALUE_OPTIONAL),
                                      'descriptionmarkersformat' => new external_format_value('descriptionmarkers', VALUE_OPTIONAL),
                                      'maxscore' => new external_value(PARAM_FLOAT, 'maximum score')
                                      )
                                  )
        );
        $guide_comments = new external_multiple_structure(
                              new external_single_structure(
                                  array(
                                      'id'   => new external_value(PARAM_INT, 'criterion id', VALUE_OPTIONAL),
                                      'sortorder' => new external_value(PARAM_INT, 'sortorder', VALUE_OPTIONAL),
                                      'description' => new external_value(PARAM_RAW, 'description', VALUE_OPTIONAL),
                                      'descriptionformat' => new external_format_value('description', VALUE_OPTIONAL)
                                   )
                              ), 'comments', VALUE_OPTIONAL
        );
        return array('guide_criteria' => $guide_criteria, 'guide_comments' => $guide_comments);
    }

    /**
     * Returns an array that defines the structure of the guide's filling. This function is used by
     * the web service function core_grading_external::get_gradingform_instances().
     *
     * @return An array containing a single key/value pair with the 'criteria' external_multiple_structure
     * @see gradingform_controller::get_external_instance_filling_details()
     * @since Moodle 2.6
     */
    public static function get_external_instance_filling_details() {
        $criteria = new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'filling id'),
                    'criterionid' => new external_value(PARAM_INT, 'criterion id'),
                    'levelid' => new external_value(PARAM_INT, 'level id', VALUE_OPTIONAL),
                    'remark' => new external_value(PARAM_RAW, 'remark', VALUE_OPTIONAL),
                    'remarkformat' => new external_format_value('remark', VALUE_OPTIONAL),
                    'score' => new external_value(PARAM_FLOAT, 'maximum score')
                )
            ), 'filling', VALUE_OPTIONAL
        );
        return array ('criteria' => $criteria);
    }

}

/**
 * Class to manage one guide grading instance. Stores information and performs actions like
 * update, copy, validate, submit, etc.
 *
 * @package    gradingform_guide
 * @copyright  2012 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradingform_guide_instance extends gradingform_instance {

    /** @var array */
    protected $guide;

    /** @var array An array of validation errors */
    protected $validationerrors = array();

    /**
     * Deletes this (INCOMPLETE) instance from database.
     */
    public function cancel() {
        global $DB;
        parent::cancel();
        $DB->delete_records('gradingform_guide_fillings', array('instanceid' => $this->get_id()));
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
        $currentgrade = $this->get_guide_filling();
        foreach ($currentgrade['criteria'] as $criterionid => $record) {
            $params = array('instanceid' => $instanceid, 'criterionid' => $criterionid,
                'score' => $record['score'], 'remark' => $record['remark'], 'remarkformat' => $record['remarkformat']);
            $DB->insert_record('gradingform_guide_fillings', $params);
        }
        return $instanceid;
    }

    /**
     * Determines whether the submitted form was empty.
     *
     * @param array $elementvalue value of element submitted from the form
     * @return boolean true if the form is empty
     */
    public function is_empty_form($elementvalue) {
        $criteria = $this->get_controller()->get_definition()->guide_criteria;
        foreach ($criteria as $id => $criterion) {
            $score = $elementvalue['criteria'][$id]['score'];
            $remark = $elementvalue['criteria'][$id]['remark'];

            if ((isset($score) && $score !== '')
                    || ((isset($remark) && $remark !== ''))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validates that guide is fully completed and contains valid grade on each criterion
     *
     * @param array $elementvalue value of element as came in form submit
     * @return boolean true if the form data is validated and contains no errors
     */
    public function validate_grading_element($elementvalue) {
        $criteria = $this->get_controller()->get_definition()->guide_criteria;
        if (!isset($elementvalue['criteria']) || !is_array($elementvalue['criteria']) ||
            count($elementvalue['criteria']) < count($criteria)) {
            return false;
        }
        // Reset validation errors.
        $this->validationerrors = null;
        foreach ($criteria as $id => $criterion) {
            if (!isset($elementvalue['criteria'][$id]['score'])
                    || $criterion['maxscore'] < $elementvalue['criteria'][$id]['score']
                    || !is_numeric($elementvalue['criteria'][$id]['score'])
                    || $elementvalue['criteria'][$id]['score'] < 0) {
                $this->validationerrors[$id]['score'] =  $elementvalue['criteria'][$id]['score'];
            }
        }
        if (!empty($this->validationerrors)) {
            return false;
        }
        return true;
    }

    /**
     * Retrieves from DB and returns the data how this guide was filled
     *
     * @param bool $force whether to force DB query even if the data is cached
     * @return array
     */
    public function get_guide_filling($force = false) {
        global $DB;
        if ($this->guide === null || $force) {
            $records = $DB->get_records('gradingform_guide_fillings', array('instanceid' => $this->get_id()));
            $this->guide = array('criteria' => array());
            foreach ($records as $record) {
                $record->score = (float)$record->score; // Strip trailing 0.
                $this->guide['criteria'][$record->criterionid] = (array)$record;
            }
        }
        return $this->guide;
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
        $currentgrade = $this->get_guide_filling();
        parent::update($data);

        foreach ($data['criteria'] as $criterionid => $record) {
            if (!array_key_exists($criterionid, $currentgrade['criteria'])) {
                $newrecord = array('instanceid' => $this->get_id(), 'criterionid' => $criterionid,
                    'score' => $record['score'], 'remarkformat' => FORMAT_MOODLE);
                if (isset($record['remark'])) {
                    $newrecord['remark'] = $record['remark'];
                }
                $DB->insert_record('gradingform_guide_fillings', $newrecord);
            } else {
                $newrecord = array('id' => $currentgrade['criteria'][$criterionid]['id']);
                foreach (array('score', 'remark'/*, 'remarkformat' TODO */) as $key) {
                    if (isset($record[$key]) && $currentgrade['criteria'][$criterionid][$key] != $record[$key]) {
                        $newrecord[$key] = $record[$key];
                    }
                }
                if (count($newrecord) > 1) {
                    $DB->update_record('gradingform_guide_fillings', $newrecord);
                }
            }
        }
        foreach ($currentgrade['criteria'] as $criterionid => $record) {
            if (!array_key_exists($criterionid, $data['criteria'])) {
                $DB->delete_records('gradingform_guide_fillings', array('id' => $record['id']));
            }
        }
        $this->get_guide_filling(true);
    }

    /**
     * Removes the attempt from the gradingform_guide_fillings table
     * @param array $data the attempt data
     */
    public function clear_attempt($data) {
        global $DB;

        foreach ($data['criteria'] as $criterionid => $record) {
            $DB->delete_records('gradingform_guide_fillings',
                array('criterionid' => $criterionid, 'instanceid' => $this->get_id()));
        }
    }

    /**
     * Calculates the grade to be pushed to the gradebook
     *
     * @return float|int the valid grade from $this->get_controller()->get_grade_range()
     */
    public function get_grade() {
        $grade = $this->get_guide_filling();

        if (!($scores = $this->get_controller()->get_min_max_score()) || $scores['maxscore'] <= $scores['minscore']) {
            return -1;
        }

        $graderange = array_keys($this->get_controller()->get_grade_range());
        if (empty($graderange)) {
            return -1;
        }
        sort($graderange);
        $mingrade = $graderange[0];
        $maxgrade = $graderange[count($graderange) - 1];

        $curscore = 0;
        foreach ($grade['criteria'] as $record) {
            $curscore += $record['score'];
        }
        $gradeoffset = ($curscore-$scores['minscore'])/($scores['maxscore']-$scores['minscore'])*
            ($maxgrade-$mingrade);
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
        if (!$gradingformelement->_flagFrozen) {
            $module = array('name'=>'gradingform_guide', 'fullpath'=>'/grade/grading/form/guide/js/guide.js');
            $page->requires->js_init_call('M.gradingform_guide.init', array(
                array('name' => $gradingformelement->getName())), true, $module);
            $mode = gradingform_guide_controller::DISPLAY_EVAL;
        } else {
            if ($gradingformelement->_persistantFreeze) {
                $mode = gradingform_guide_controller::DISPLAY_EVAL_FROZEN;
            } else {
                $mode = gradingform_guide_controller::DISPLAY_REVIEW;
            }
        }
        $criteria = $this->get_controller()->get_definition()->guide_criteria;
        $comments = $this->get_controller()->get_definition()->guide_comments;
        $options = $this->get_controller()->get_options();
        $value = $gradingformelement->getValue();
        $html = '';
        if ($value === null) {
            $value = $this->get_guide_filling();
        } else if (!$this->validate_grading_element($value)) {
            $html .= html_writer::tag('div', get_string('guidenotcompleted', 'gradingform_guide'),
                array('class' => 'gradingform_guide-error'));
            if (!empty($this->validationerrors)) {
                foreach ($this->validationerrors as $id => $err) {
                    $a = new stdClass();
                    $a->criterianame = s($criteria[$id]['shortname']);
                    $a->maxscore = $criteria[$id]['maxscore'];
                    $html .= html_writer::tag('div', get_string('err_scoreinvalid', 'gradingform_guide', $a),
                        array('class' => 'gradingform_guide-error'));
                }
            }
        }
        $currentinstance = $this->get_current_instance();
        if ($currentinstance && $currentinstance->get_status() == gradingform_instance::INSTANCE_STATUS_NEEDUPDATE) {
            $html .= html_writer::tag('div', get_string('needregrademessage', 'gradingform_guide'),
                array('class' => 'gradingform_guide-regrade'));
        }
        $haschanges = false;
        if ($currentinstance) {
            $curfilling = $currentinstance->get_guide_filling();
            foreach ($curfilling['criteria'] as $criterionid => $curvalues) {
                $value['criteria'][$criterionid]['score'] = $curvalues['score'];
                $newremark = null;
                $newscore = null;
                if (isset($value['criteria'][$criterionid]['remark'])) {
                    $newremark = $value['criteria'][$criterionid]['remark'];
                }
                if (isset($value['criteria'][$criterionid]['score'])) {
                    $newscore = $value['criteria'][$criterionid]['score'];
                }
                if ($newscore != $curvalues['score'] || $newremark != $curvalues['remark']) {
                    $haschanges = true;
                }
            }
        }
        if ($this->get_data('isrestored') && $haschanges) {
            $html .= html_writer::tag('div', get_string('restoredfromdraft', 'gradingform_guide'),
                array('class' => 'gradingform_guide-restored'));
        }
        $html .= html_writer::tag('div', $this->get_controller()->get_formatted_description(),
            array('class' => 'gradingform_guide-description'));
        $html .= $this->get_controller()->get_renderer($page)->display_guide($criteria, $comments, $options, $mode,
            $gradingformelement->getName(), $value, $this->validationerrors);
        return $html;
    }
}
