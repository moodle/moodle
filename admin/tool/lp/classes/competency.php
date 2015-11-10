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
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use context_system;
use lang_string;
use stdClass;

/**
 * Class for loading/storing competencies from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency extends persistent {

    const TABLE = 'tool_lp_competency';

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
                'type' => PARAM_TEXT
            ),
            'description' => array(
                'default' => '',
                'type' => PARAM_TEXT
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
            'visible' => array(
                'default' => 1,
                'type' => PARAM_BOOL
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
                $this->set_new_path($parent);
                $this->set_new_sortorder();
            }

        // During create.
        } else {

            $this->set_new_path();
            if ($this->get_sortorder() === null) {
                // Get a sortorder if it wasn't set.
                $this->set_new_sortorder();
            }
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

        // We need to delete all the children.
        $deletepath = $DB->sql_like_escape($this->get_path() . $this->get_id() . '/') . '%';
        $like = $DB->sql_like('path', ':deletepath');
        $DB->delete_records_select(self::TABLE, $like, array('deletepath' => $deletepath));

        // And all the links to courses.
        $DB->delete_records('tool_lp_course_competency', array('competencyid' => $this->get_id()));
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

        if (!$rule || !is_subclass_of($rule, '\tool_lp\competency_rule')) {
            // Double check that the rule is extending the right class to avoid bad surprises.
            return null;
        }

        return new $rule($this);
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

        // During create.
        } else {

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

        // The format of the path should be as follows.
        } else if (!preg_match('@/([0-9]+/)+@', $value)) {
            return new lang_string('invaliddata', 'error');

        // Validate the depth of the path.
        } else if ((substr_count($value, '/') - 1) > competency_framework::get_taxonomies_max_level()) {
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
        if (!empty($value) && !$this->newparent && !competency::record_exists($value)) {
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

        if (!class_exists($value) || !is_subclass_of($value, '\tool_lp\competency_rule')) {
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
            } else if (empty($rule) && !$value !== null) {
                // Config but no rules, whoops!
                return new lang_string('invaliddata', 'error');
            }
        }

        $valid = $rule->validate_config($value);
        if ($valid !== true) {
            // Whoops!
            return new lang_string('invaliddata', 'error');
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
     * @return array Keys are the class names, values is an object containing name and amd.
     */
    public static function get_available_rules() {
        // Fully qualified class names withough leading slashes because get_class() does not add them either.
        $rules = array(
            'tool_lp\competency_rule_all' => (object) array(),
            'tool_lp\competency_rule_points' => (object) array(),
        );
        foreach ($rules as $class => $rule) {
            $rule->name = $class::get_name();
            $rule->amd = $class::get_amd_module();
        }
        return $rules;
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

}
