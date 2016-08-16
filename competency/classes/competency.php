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
 * Class for loading/storing competencies from the DB.
 *
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use context_system;
use lang_string;
use stdClass;

require_once($CFG->libdir . '/grade/grade_scale.php');

/**
 * Class for loading/storing competencies from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency extends persistent {

    const TABLE = 'competency';

    /** Outcome none. */
    const OUTCOME_NONE = 0;
    /** Outcome evidence. */
    const OUTCOME_EVIDENCE = 1;
    /** Outcome complete. */
    const OUTCOME_COMPLETE = 2;
    /** Outcome recommend. */
    const OUTCOME_RECOMMEND = 3;

    /** @var competency Object before update. */
    protected $beforeupdate = null;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'shortname' => array(
                'type' => PARAM_TEXT
            ),
            'idnumber' => array(
                'type' => PARAM_RAW
            ),
            'description' => array(
                'default' => '',
                'type' => PARAM_RAW
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
            'sortorder' => array(
                'default' => null,
                'type' => PARAM_INT
            ),
            'parentid' => array(
                'default' => 0,
                'type' => PARAM_INT
            ),
            'path' => array(
                'default' => '/0/',
                'type' => PARAM_RAW
            ),
            'ruleoutcome' => array(
                'choices' => array(self::OUTCOME_NONE, self::OUTCOME_EVIDENCE, self::OUTCOME_COMPLETE, self::OUTCOME_RECOMMEND),
                'default' => self::OUTCOME_NONE,
                'type' => PARAM_INT
            ),
            'ruletype' => array(
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED
            ),
            'ruleconfig' => array(
                'default' => null,
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED
            ),
            'scaleid' => array(
                'default' => null,
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED
            ),
            'scaleconfiguration' => array(
                'default' => null,
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED
            ),
            'competencyframeworkid' => array(
                'default' => 0,
                'type' => PARAM_INT
            ),
        );
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        $this->beforeupdate = null;
        $this->newparent = null;

        // During update.
        if ($this->get_id()) {
            $this->beforeupdate = new competency($this->get_id());

            // The parent ID has changed.
            if ($this->beforeupdate->get_parentid() != $this->get_parentid()) {
                $this->newparent = $this->get_parent();

                // Update path and sortorder.
                $this->set_new_path($this->newparent);
                $this->set_new_sortorder();
            }

        } else {
            // During create.

            $this->set_new_path();
            // Always generate new sortorder when we create new competency.
            $this->set_new_sortorder();

        }
    }

    /**
     * Hook to execute after an update.
     *
     * @param bool $result Whether or not the update was successful.
     * @return void
     */
    protected function after_update($result) {
        global $DB;

        if (!$result) {
            $this->beforeupdate = null;
            return;
        }

        // The parent ID has changed, we need to fix all the paths of the children.
        if ($this->beforeupdate->get_parentid() != $this->get_parentid()) {
            $beforepath = $this->beforeupdate->get_path() . $this->get_id() . '/';

            $like = $DB->sql_like('path', '?');
            $likesearch = $DB->sql_like_escape($beforepath) . '%';

            $table = '{' . self::TABLE . '}';
            $sql = "UPDATE $table SET path = REPLACE(path, ?, ?) WHERE " . $like;
            $DB->execute($sql, array(
                $beforepath,
                $this->get_path() . $this->get_id() . '/',
                $likesearch
            ));

            // Resolving sortorder holes left after changing parent.
            $table = '{' . self::TABLE . '}';
            $sql = "UPDATE $table SET sortorder = sortorder -1 "
                    . " WHERE  competencyframeworkid = ? AND parentid = ? AND sortorder > ?";
            $DB->execute($sql, array($this->get_competencyframeworkid(),
                                        $this->beforeupdate->get_parentid(),
                                        $this->beforeupdate->get_sortorder()
                                    ));
        }

        $this->beforeupdate = null;
    }


    /**
     * Hook to execute after a delete.
     *
     * @param bool $result Whether or not the delete was successful.
     * @return void
     */
    protected function after_delete($result) {
        global $DB;
        if (!$result) {
            return;
        }

        // Resolving sortorder holes left after delete.
        $table = '{' . self::TABLE . '}';
        $sql = "UPDATE $table SET sortorder = sortorder -1  WHERE  competencyframeworkid = ? AND parentid = ? AND sortorder > ?";
        $DB->execute($sql, array($this->get_competencyframeworkid(), $this->get_parentid(), $this->get_sortorder()));
    }

    /**
     * Extracts the default grade from the scale configuration.
     *
     * Returns an array where the first element is the grade, and the second
     * is a boolean representing whether or not this grade is considered 'proficient'.
     *
     * @return array(int grade, bool proficient)
     */
    public function get_default_grade() {
        $scaleid = $this->get_scaleid();
        $scaleconfig = $this->get_scaleconfiguration();
        if ($scaleid === null) {
            $scaleconfig = $this->get_framework()->get_scaleconfiguration();
        }
        return competency_framework::get_default_grade_from_scale_configuration($scaleconfig);
    }

    /**
     * Get the competency framework.
     *
     * @return competency_framework
     */
    public function get_framework() {
        return new competency_framework($this->get_competencyframeworkid());
    }

    /**
     * Get the competency level.
     *
     * @return int
     */
    public function get_level() {
        $path = $this->get_path();
        $path = trim($path, '/');
        return substr_count($path, '/') + 1;
    }

    /**
     * Return the parent competency.
     *
     * @return null|competency
     */
    public function get_parent() {
        $parentid = $this->get_parentid();
        if (!$parentid) {
            return null;
        }
        return new competency($parentid);
    }

    /**
     * Extracts the proficiency of a grade from the scale configuration.
     *
     * @param int $grade The grade (scale item ID).
     * @return array(int grade, bool proficient)
     */
    public function get_proficiency_of_grade($grade) {
        $scaleid = $this->get_scaleid();
        $scaleconfig = $this->get_scaleconfiguration();
        if ($scaleid === null) {
            $scaleconfig = $this->get_framework()->get_scaleconfiguration();
        }
        return competency_framework::get_proficiency_of_grade_from_scale_configuration($scaleconfig, $grade);
    }

    /**
     * Return the related competencies.
     *
     * @return competency[]
     */
    public function get_related_competencies() {
        return related_competency::get_related_competencies($this->get_id());
    }

    /**
     * Get the rule object.
     *
     * @return null|competency_rule
     */
    public function get_rule_object() {
        $rule = $this->get_ruletype();

        if (!$rule || !is_subclass_of($rule, 'core_competency\\competency_rule')) {
            // Double check that the rule is extending the right class to avoid bad surprises.
            return null;
        }

        return new $rule($this);
    }

    /**
     * Return the scale.
     *
     * @return \grade_scale
     */
    public function get_scale() {
        $scaleid = $this->get_scaleid();
        if ($scaleid === null) {
            return $this->get_framework()->get_scale();
        }
        $scale = \grade_scale::fetch(array('id' => $scaleid));
        $scale->load_items();
        return $scale;
    }

    /**
     * Returns true when the competency has user competencies.
     *
     * This is useful to determine if the competency, or part of it, should be locked down.
     *
     * @return boolean
     */
    public function has_user_competencies() {
        return user_competency::has_records_for_competency($this->get_id()) ||
            user_competency_plan::has_records_for_competency($this->get_id());
    }

    /**
     * Check if the competency is the parent of passed competencies.
     *
     * @param  array $ids IDs of supposedly direct children.
     * @return boolean
     */
    public function is_parent_of(array $ids) {
        global $DB;

        list($insql, $params) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
        $params['parentid'] = $this->get_id();

        return $DB->count_records_select(self::TABLE, "id $insql AND parentid = :parentid", $params) == count($ids);
    }

    /**
     * Reset the rule.
     *
     * @return void
     */
    public function reset_rule() {
        $this->set_ruleoutcome(static::OUTCOME_NONE);
        $this->set_ruletype(null);
        $this->set_ruleconfig(null);
    }

    /**
     * Helper method to set the path.
     *
     * @param competency $parent The parent competency object.
     * @return void
     */
    protected function set_new_path(competency $parent = null) {
        $path = '/0/';
        if ($this->get_parentid()) {
            $parent = $parent !== null ? $parent : $this->get_parent();
            $path = $parent->get_path() . $this->get_parentid() . '/';
        }
        $this->set('path', $path);
    }

    /**
     * Helper method to set the sortorder.
     *
     * @return void
     */
    protected function set_new_sortorder() {
        $search = array('parentid' => $this->get_parentid(), 'competencyframeworkid' => $this->get_competencyframeworkid());
        $this->set('sortorder', $this->count_records($search));
    }

    /**
     * This does a specialised search that finds all nodes in the tree with matching text on any text like field,
     * and returns this node and all its parents in a displayable sort order.
     *
     * @param string $searchtext The text to search for.
     * @param int $competencyframeworkid The competency framework to limit the search.
     * @return persistent[]
     */
    public static function search($searchtext, $competencyframeworkid) {
        global $DB;

        $like1 = $DB->sql_like('shortname', ':like1', false);
        $like2 = $DB->sql_like('idnumber', ':like2', false);
        $like3 = $DB->sql_like('description', ':like3', false);

        $params = array(
            'like1' => '%' . $DB->sql_like_escape($searchtext) . '%',
            'like2' => '%' . $DB->sql_like_escape($searchtext) . '%',
            'like3' => '%' . $DB->sql_like_escape($searchtext) . '%',
            'frameworkid' => $competencyframeworkid
        );

        $sql = 'competencyframeworkid = :frameworkid AND ((' . $like1 . ') OR (' . $like2 . ') OR (' . $like3 . '))';
        $records = $DB->get_records_select(self::TABLE, $sql, $params, 'path, sortorder ASC', '*');

        // Now get all the parents.
        $parents = array();
        foreach ($records as $record) {
            $split = explode('/', trim($record->path, '/'));
            foreach ($split as $parent) {
                $parents[intval($parent)] = true;
            }
        }
        $parents = array_keys($parents);

        // Skip ones we already fetched.
        foreach ($parents as $idx => $parent) {
            if ($parent == 0 || isset($records[$parent])) {
                unset($parents[$idx]);
            }
        }

        if (count($parents)) {
            list($parentsql, $parentparams) = $DB->get_in_or_equal($parents, SQL_PARAMS_NAMED);

            $parentrecords = $DB->get_records_select(self::TABLE, 'id ' . $parentsql,
                    $parentparams, 'path, sortorder ASC', '*');

            foreach ($parentrecords as $id => $record) {
                $records[$id] = $record;
            }
        }

        $instances = array();
        // Convert to instances of this class.
        foreach ($records as $record) {
            $newrecord = new static(0, $record);
            $instances[$newrecord->get_id()] = $newrecord;
        }
        return $instances;
    }

    /**
     * Validate the competency framework ID.
     *
     * @param int $value The framework ID.
     * @return true|lang_string
     */
    protected function validate_competencyframeworkid($value) {

        // During update.
        if ($this->get_id()) {

            // Ensure that we are not trying to move the competency across frameworks.
            if ($this->beforeupdate->get_competencyframeworkid() != $value) {
                return new lang_string('invaliddata', 'error');
            }

        } else {
            // During create.

            // Check that the framework exists.
            if (!competency_framework::record_exists($value)) {
                return new lang_string('invaliddata', 'error');
            }
        }

        return true;
    }

    /**
     * Validate the ID number.
     *
     * @param string $value The ID number.
     * @return true|lang_string
     */
    protected function validate_idnumber($value) {
        global $DB;
        $sql = 'idnumber = :idnumber AND competencyframeworkid = :competencyframeworkid AND id <> :id';
        $params = array(
            'id' => $this->get_id(),
            'idnumber' => $value,
            'competencyframeworkid' => $this->get_competencyframeworkid()
        );
        if ($DB->record_exists_select(self::TABLE, $sql, $params)) {
            return new lang_string('idnumbertaken', 'error');
        }
        return true;
    }

    /**
     * Validate the path.
     *
     * @param string $value The path.
     * @return true|lang_string
     */
    protected function validate_path($value) {

        // The last item should be the parent ID.
        $id = $this->get_parentid();
        if (substr($value, -(strlen($id) + 2)) != '/' . $id . '/') {
            return new lang_string('invaliddata', 'error');

        } else if (!preg_match('@/([0-9]+/)+@', $value)) {
            // The format of the path is not correct.
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

    /**
     * Validate the parent ID.
     *
     * @param string $value The ID.
     * @return true|lang_string
     */
    protected function validate_parentid($value) {

        // Check that the parent exists. But only if we don't have it already, and we actually have a parent.
        if (!empty($value) && !$this->newparent && !self::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }

        // During update.
        if ($this->get_id()) {

            // If there is a new parent.
            if ($this->beforeupdate->get_parentid() != $value && $this->newparent) {

                // Check that the new parent belongs to the same framework.
                if ($this->newparent->get_competencyframeworkid() != $this->get_competencyframeworkid()) {
                    return new lang_string('invaliddata', 'error');
                }
            }
        }

        return true;
    }

    /**
     * Validate the rule.
     *
     * @param string $value The ID.
     * @return true|lang_string
     */
    protected function validate_ruletype($value) {
        if ($value === null) {
            return true;
        }

        if (!class_exists($value) || !is_subclass_of($value, 'core_competency\\competency_rule')) {
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

    /**
     * Validate the rule config.
     *
     * @param string $value The ID.
     * @return true|lang_string
     */
    protected function validate_ruleconfig($value) {
        $rule = $this->get_rule_object();

        // We don't have a rule.
        if (empty($rule)) {
            if ($value === null) {
                // No config, perfect.
                return true;
            }
            // Config but no rules, whoops!
            return new lang_string('invaliddata', 'error');
        }

        $valid = $rule->validate_config($value);
        if ($valid !== true) {
            // Whoops!
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

    /**
     * Validate the scale ID.
     *
     * Note that the value for a scale can never be 0, null has to be used when
     * the framework's scale has to be used.
     *
     * @param  int $value
     * @return true|lang_string
     */
    protected function validate_scaleid($value) {
        global $DB;

        if ($value === null) {
            return true;
        }

        // Always validate that the scale exists.
        if (!$DB->record_exists_select('scale', 'id = :id', array('id' => $value))) {
            return new lang_string('invalidscaleid', 'error');
        }

        // During update.
        if ($this->get_id()) {

            // Validate that we can only change the scale when it is not used yet.
            if ($this->beforeupdate->get_scaleid() != $value) {
                if ($this->has_user_competencies()) {
                    return new lang_string('errorscalealreadyused', 'core_competency');
                }
            }

        }

        return true;
    }

    /**
     * Validate the scale configuration.
     *
     * This logic is adapted from {@link \core_competency\competency_framework::validate_scaleconfiguration()}.
     *
     * @param  string $value The scale configuration.
     * @return bool|lang_string
     */
    protected function validate_scaleconfiguration($value) {
        $scaleid = $this->get('scaleid');
        if ($scaleid === null && $value === null) {
            return true;
        }

        $scaledefaultselected = false;
        $proficientselected = false;
        $scaleconfigurations = json_decode($value);

        if (is_array($scaleconfigurations)) {

            // The first element of the array contains the scale ID.
            $scaleinfo = array_shift($scaleconfigurations);
            if (empty($scaleinfo) || !isset($scaleinfo->scaleid) || $scaleinfo->scaleid != $scaleid) {
                // This should never happen.
                return new lang_string('errorscaleconfiguration', 'core_competency');
            }

            // Walk through the array to find proficient and default values.
            foreach ($scaleconfigurations as $scaleconfiguration) {
                if (isset($scaleconfiguration->scaledefault) && $scaleconfiguration->scaledefault) {
                    $scaledefaultselected = true;
                }
                if (isset($scaleconfiguration->proficient) && $scaleconfiguration->proficient) {
                    $proficientselected = true;
                }
            }
        }

        if (!$scaledefaultselected || !$proficientselected) {
            return new lang_string('errorscaleconfiguration', 'core_competency');
        }

        return true;
    }

    /**
     * Return whether or not the competency IDs share the same framework.
     *
     * @param  array  $ids Competency IDs
     * @return bool
     */
    public static function share_same_framework(array $ids) {
        global $DB;
        list($insql, $params) = $DB->get_in_or_equal($ids);
        return $DB->count_records_select(self::TABLE, "id $insql", $params, "COUNT(DISTINCT(competencyframeworkid))") == 1;
    }

    /**
     * Get the available rules.
     *
     * @return array Keys are the class names, values are the name of the rule.
     */
    public static function get_available_rules() {
        // Fully qualified class names without leading slashes because get_class() does not add them either.
        $rules = array(
            'core_competency\\competency_rule_all' => competency_rule_all::get_name(),
            'core_competency\\competency_rule_points' => competency_rule_points::get_name(),
        );
        return $rules;
    }

    /**
     * Return the current depth of a competency framework.
     *
     * @param int $frameworkid The framework ID.
     * @return int
     */
    public static function get_framework_depth($frameworkid) {
        global $DB;
        $totallength = $DB->sql_length('path');
        $trimmedlength = $DB->sql_length("REPLACE(path, '/', '')");
        $sql = "SELECT ($totallength - $trimmedlength - 1) AS depth
                  FROM {" . self::TABLE . "}
                 WHERE competencyframeworkid = :id
              ORDER BY depth DESC";
        $record = $DB->get_record_sql($sql, array('id' => $frameworkid), IGNORE_MULTIPLE);
        if (!$record) {
            $depth = 0;
        } else {
            $depth = $record->depth;
        }
        return $depth;
    }

    /**
     * Build a framework tree with competency nodes.
     *
     * @param  int  $frameworkid the framework id
     * @return node[] tree of framework competency nodes
     */
    public static function get_framework_tree($frameworkid) {
        $competencies = self::search('', $frameworkid);
        return self::build_tree($competencies, 0);
    }

    /**
     * Get the context from the framework.
     *
     * @return context
     */
    public function get_context() {
        return $this->get_framework()->get_context();
    }

    /**
     * Recursively build up the tree of nodes.
     *
     * @param array $all - List of all competency classes.
     * @param int $parentid - The current parent ID. Pass 0 to build the tree from the top.
     * @return node[] $tree tree of nodes
     */
    protected static function build_tree($all, $parentid) {
        $tree = array();
        foreach ($all as $one) {
            if ($one->get_parentid() == $parentid) {
                $node = new stdClass();
                $node->competency = $one;
                $node->children = self::build_tree($all, $one->get_id());
                $tree[] = $node;
            }
        }
        return $tree;
    }

    /**
     * Check if we can delete competencies safely.
     *
     * This moethod does not check any capablities.
     * Check if competency is used in a plan and user competency.
     * Check if competency is used in a template.
     * Check if competency is linked to a course.
     *
     * @param array $ids Array of competencies ids.
     * @return bool True if we can delete the competencies.
     */
    public static function can_all_be_deleted($ids) {
        if (empty($ids)) {
            return true;
        }
        // Check if competency is used in template.
        if (template_competency::has_records_for_competencies($ids)) {
            return false;
        }
        // Check if competency is used in plan.
        if (plan_competency::has_records_for_competencies($ids)) {
            return false;
        }
        // Check if competency is used in course.
        if (course_competency::has_records_for_competencies($ids)) {
            return false;
        }
        // Check if competency is used in user_competency.
        if (user_competency::has_records_for_competencies($ids)) {
            return false;
        }
        // Check if competency is used in user_competency_plan.
        if (user_competency_plan::has_records_for_competencies($ids)) {
            return false;
        }
        return true;
    }

    /**
     * Delete the competencies.
     *
     * This method is reserved to core usage.
     * This method does not trigger the after_delete event.
     * This method does not delete related objects such as related competencies and evidences.
     *
     * @param array $ids The competencies ids.
     * @return bool True if the competencies were deleted successfully.
     */
    public static function delete_multiple($ids) {
        global $DB;
        list($insql, $params) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
        return $DB->delete_records_select(self::TABLE, "id $insql", $params);
    }

    /**
     * Get descendant ids.
     *
     * @param competency $competency The competency.
     * @return array Array of competencies ids.
     */
    public static function get_descendants_ids($competency) {
        global $DB;

        $path = $DB->sql_like_escape($competency->get_path() . $competency->get_id() . '/') . '%';
        $like = $DB->sql_like('path', ':likepath');
        return $DB->get_fieldset_select(self::TABLE, 'id', $like, array('likepath' => $path));
    }

    /**
     * Get competencyids by frameworkid.
     *
     * @param int $frameworkid The competency framework ID.
     * @return array Array of competency ids.
     */
    public static function get_ids_by_frameworkid($frameworkid) {
        global $DB;

        return $DB->get_fieldset_select(self::TABLE, 'id', 'competencyframeworkid = :frmid', array('frmid' => $frameworkid));
    }

    /**
     * Delete competencies by framework ID.
     *
     * This method is reserved to core usage.
     * This method does not trigger the after_delete event.
     * This method does not delete related objects such as related competencies and evidences.
     *
     * @param int $id the framework ID
     * @return bool Return true if delete was successful.
     */
    public static function delete_by_frameworkid($id) {
        global $DB;
        return $DB->delete_records(self::TABLE, array('competencyframeworkid' => $id));
    }

    /**
     * Get competency ancestors.
     *
     * @return competency[] Return array of ancestors.
     */
    public function get_ancestors() {
        global $DB;
        $ancestors = array();
        $ancestorsids = explode('/', trim($this->get_path(), '/'));
        // Drop the root item from the array /0/.
        array_shift($ancestorsids);
        if (!empty($ancestorsids)) {
            list($insql, $params) = $DB->get_in_or_equal($ancestorsids, SQL_PARAMS_NAMED);
            $ancestors = self::get_records_select("id $insql", $params);
        }
        return $ancestors;
    }

}
