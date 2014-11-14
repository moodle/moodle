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
 * Class that holds a tree of availability conditions.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

/**
 * Class that holds a tree of availability conditions.
 *
 * The structure of this tree in JSON input data is:
 *
 * { op:'&', c:[] }
 *
 * where 'op' is one of the OP_xx constants and 'c' is an array of children.
 *
 * At the root level one of the following additional values must be included:
 *
 * op '|' or '!&'
 *   show:true
 *   Boolean value controlling whether a failed match causes the item to
 *   display to students with information, or be completely hidden.
 * op '&' or '!|'
 *   showc:[]
 *   Array of same length as c with booleans corresponding to each child; you
 *   can make it be hidden or shown depending on which one they fail. (Anything
 *   with false takes precedence.)
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tree extends tree_node {
    /** @var int Operator: AND */
    const OP_AND = '&';
    /** @var int Operator: OR */
    const OP_OR = '|';
    /** @var int Operator: NOT(AND) */
    const OP_NOT_AND = '!&';
    /** @var int Operator: NOT(OR) */
    const OP_NOT_OR = '!|';

    /** @var bool True if this tree is at root level */
    protected $root;

    /** @var string Operator type (OP_xx constant) */
    protected $op;

    /** @var tree_node[] Children in this branch (may be empty array if needed) */
    protected $children;

    /**
     * Array of 'show information or hide completely' options for each child.
     * This array is only set for the root tree if it is in AND or NOT OR mode,
     * otherwise it is null.
     *
     * @var bool[]
     */
    protected $showchildren;

    /**
     * Single 'show information or hide completely' option for tree. This option
     * is only set for the root tree if it is in OR or NOT AND mode, otherwise
     * it is true.
     *
     * @var bool
     */
    protected $show;

    /**
     * Display a representation of this tree (used for debugging).
     *
     * @return string Text representation of tree
     */
    public function __toString() {
        $result = '';
        if ($this->root && is_null($this->showchildren)) {
            $result .= $this->show ? '+' : '-';
        }
        $result .= $this->op . '(';
        $first = true;
        foreach ($this->children as $index => $child) {
            if ($first) {
                $first = false;
            } else {
                $result .= ',';
            }
            if (!is_null($this->showchildren)) {
                $result .= $this->showchildren[$index] ? '+' : '-';
            }
            $result .= (string)$child;
        }
        $result .= ')';
        return $result;
    }

    /**
     * Decodes availability structure.
     *
     * This function also validates the retrieved data as follows:
     * 1. Data that does not meet the API-defined structure causes a
     *    coding_exception (this should be impossible unless there is
     *    a system bug or somebody manually hacks the database).
     * 2. Data that meets the structure but cannot be implemented (e.g.
     *    reference to missing plugin or to module that doesn't exist) is
     *    either silently discarded (if $lax is true) or causes a
     *    coding_exception (if $lax is false).
     *
     * @see decode_availability
     * @param \stdClass $structure Structure (decoded from JSON)
     * @param boolean $lax If true, throw exceptions only for invalid structure
     * @param boolean $root If true, this is the root tree
     * @return tree Availability tree
     * @throws \coding_exception If data is not valid structure
     */
    public function __construct($structure, $lax = false, $root = true) {
        $this->root = $root;

        // Check object.
        if (!is_object($structure)) {
            throw new \coding_exception('Invalid availability structure (not object)');
        }

        // Extract operator.
        if (!isset($structure->op)) {
            throw new \coding_exception('Invalid availability structure (missing ->op)');
        }
        $this->op = $structure->op;
        if (!in_array($this->op, array(self::OP_AND, self::OP_OR,
                self::OP_NOT_AND, self::OP_NOT_OR), true)) {
            throw new \coding_exception('Invalid availability structure (unknown ->op)');
        }

        // For root tree, get show options.
        $this->show = true;
        $this->showchildren = null;
        if ($root) {
            if ($this->op === self::OP_AND || $this->op === self::OP_NOT_OR) {
                // Per-child show options.
                if (!isset($structure->showc)) {
                    throw new \coding_exception(
                            'Invalid availability structure (missing ->showc)');
                }
                if (!is_array($structure->showc)) {
                    throw new \coding_exception(
                            'Invalid availability structure (->showc not array)');
                }
                foreach ($structure->showc as $value) {
                    if (!is_bool($value)) {
                        throw new \coding_exception(
                                'Invalid availability structure (->showc value not bool)');
                    }
                }
                // Set it empty now - add corresponding ones later.
                $this->showchildren = array();
            } else {
                // Entire tree show option. (Note: This is because when you use
                // OR mode, say you have A OR B, the user does not meet conditions
                // for either A or B. A is set to 'show' and B is set to 'hide'.
                // But they don't have either, so how do we know which one to do?
                // There might as well be only one value.)
                if (!isset($structure->show)) {
                    throw new \coding_exception(
                            'Invalid availability structure (missing ->show)');
                }
                if (!is_bool($structure->show)) {
                    throw new \coding_exception(
                            'Invalid availability structure (->show not bool)');
                }
                $this->show = $structure->show;
            }
        }

        // Get list of enabled plugins.
        $pluginmanager = \core_plugin_manager::instance();
        $enabled = $pluginmanager->get_enabled_plugins('availability');

        // For unit tests, also allow the mock plugin type (even though it
        // isn't configured in the code as a proper plugin).
        if (PHPUNIT_TEST) {
            $enabled['mock'] = true;
        }

        // Get children.
        if (!isset($structure->c)) {
            throw new \coding_exception('Invalid availability structure (missing ->c)');
        }
        if (!is_array($structure->c)) {
            throw new \coding_exception('Invalid availability structure (->c not array)');
        }
        if (is_array($this->showchildren) && count($structure->showc) != count($structure->c)) {
            throw new \coding_exception('Invalid availability structure (->c, ->showc mismatch)');
        }
        $this->children = array();
        foreach ($structure->c as $index => $child) {
            if (!is_object($child)) {
                throw new \coding_exception('Invalid availability structure (child not object)');
            }

            // First see if it's a condition. These have a defined type.
            if (isset($child->type)) {
                // Look for a plugin of this type.
                $classname = '\availability_' . $child->type . '\condition';
                if (!array_key_exists($child->type, $enabled)) {
                    if ($lax) {
                        // On load of existing settings, ignore if class
                        // doesn't exist.
                        continue;
                    } else {
                        throw new \coding_exception('Unknown condition type: ' . $child->type);
                    }
                }
                $this->children[] = new $classname($child);
            } else {
                // Not a condition. Must be a subtree.
                $this->children[] = new tree($child, $lax, false);
            }
            if (!is_null($this->showchildren)) {
                $this->showchildren[] = $structure->showc[$index];
            }
        }
    }

    public function check_available($not, info $info, $grabthelot, $userid) {
        // If there are no children in this group, we just treat it as available.
        $information = '';
        if (!$this->children) {
            return new result(true);
        }

        // Get logic flags from operator.
        list($innernot, $andoperator) = $this->get_logic_flags($not);

        if ($andoperator) {
            $allow = true;
        } else {
            $allow = false;
        }
        $failedchildren = array();
        $totallyhide = !$this->show;
        foreach ($this->children as $index => $child) {
            // Check available and get info.
            $childresult = $child->check_available(
                    $innernot, $info, $grabthelot, $userid);
            $childyes = $childresult->is_available();
            if (!$childyes) {
                $failedchildren[] = $childresult;
                if (!is_null($this->showchildren) && !$this->showchildren[$index]) {
                    $totallyhide = true;
                }
            }

            if ($andoperator && !$childyes) {
                $allow = false;
                // Do not exit loop at this point, as we will still include other info.
            } else if (!$andoperator && $childyes) {
                // Exit loop since we are going to allow access (from this tree at least).
                $allow = true;
                break;
            }
        }

        if ($allow) {
            return new result(true);
        } else if ($totallyhide) {
            return new result(false);
        } else {
            return new result(false, $this, $failedchildren);
        }
    }

    public function is_applied_to_user_lists() {
        return true;
    }

    /**
     * Tests against a user list. Users who cannot access the activity due to
     * availability restrictions will be removed from the list.
     *
     * This test ONLY includes conditions which are marked as being applied to
     * user lists. For example, group conditions are included but date
     * conditions are not included.
     *
     * The function operates reasonably efficiently i.e. should not do per-user
     * database queries. It is however likely to be fairly slow.
     *
     * @param array $users Array of userid => object
     * @param bool $not If tree's parent indicates it's being checked negatively
     * @param info $info Info about current context
     * @param capability_checker $checker Capability checker
     * @return array Filtered version of input array
     */
    public function filter_user_list(array $users, $not, info $info,
            capability_checker $checker) {
        // Get logic flags from operator.
        list($innernot, $andoperator) = $this->get_logic_flags($not);

        if ($andoperator) {
            // For AND, start with the whole result and whittle it down.
            $result = $users;
        } else {
            // For OR, start with nothing.
            $result = array();
            $anyconditions = false;
        }

        // Loop through all valid children.
        foreach ($this->children as $index => $child) {
            if (!$child->is_applied_to_user_lists()) {
                continue;
            }
            $childresult = $child->filter_user_list($users, $innernot, $info, $checker);
            if ($andoperator) {
                $result = array_intersect_key($result, $childresult);
            } else {
                // Combine results into array.
                foreach ($childresult as $id => $user) {
                    $result[$id] = $user;
                }
                $anyconditions = true;
            }
        }

        // For OR operator, if there were no conditions just return input.
        if (!$andoperator && !$anyconditions) {
            return $users;
        } else {
            return $result;
        }
    }

    public function get_user_list_sql($not, info $info, $onlyactive) {
        global $DB;
        // Get logic flags from operator.
        list($innernot, $andoperator) = $this->get_logic_flags($not);

        // Loop through all valid children, getting SQL for each.
        $childresults = array();
        foreach ($this->children as $index => $child) {
            if (!$child->is_applied_to_user_lists()) {
                continue;
            }
            $childresult = $child->get_user_list_sql($innernot, $info, $onlyactive);
            if ($childresult[0]) {
                $childresults[] = $childresult;
            } else if (!$andoperator) {
                // When using OR operator, if any part doesn't have restrictions,
                // then nor does the whole thing.
                return array('', array());
            }
        }

        // If there are no conditions, return null.
        if (!$childresults) {
            return array('', array());
        }
        // If there is a single condition, return it.
        if (count($childresults) === 1) {
            return $childresults[0];
        }

        // Combine results using INTERSECT or UNION.
        $outsql = null;
        $subsql = array();
        $outparams = array();
        foreach ($childresults as $childresult) {
            $subsql[] = $childresult[0];
            $outparams = array_merge($outparams, $childresult[1]);
        }
        if ($andoperator) {
            $outsql = $DB->sql_intersect($subsql, 'id');
        } else {
            $outsql = '(' . join(') UNION (', $subsql) . ')';
        }
        return array($outsql, $outparams);
    }

    public function is_available_for_all($not = false) {
        // Get logic flags.
        list($innernot, $andoperator) = $this->get_logic_flags($not);

        // No children = always available.
        if (!$this->children) {
            return true;
        }

        // Check children.
        foreach ($this->children as $child) {
            $innerall = $child->is_available_for_all($innernot);
            if ($andoperator) {
                // When there is an AND operator, then any child that results
                // in unavailable status would cause the whole thing to be
                // unavailable.
                if (!$innerall) {
                    return false;
                }
            } else {
                // When there is an OR operator, then any child which must only
                // be available means the whole thing must be available.
                if ($innerall) {
                    return true;
                }
            }
        }

        // If we get to here then for an AND operator that means everything must
        // be available. From OR it means that everything must be possibly
        // not available.
        return $andoperator;
    }

    /**
     * Gets full information about this tree (including all children) as HTML
     * for display to staff.
     *
     * @param info $info Information about location of condition tree
     * @throws \coding_exception If you call on a non-root tree
     * @return string HTML data (empty string if none)
     */
    public function get_full_information(info $info) {
        if (!$this->root) {
            throw new \coding_exception('Only supported on root item');
        }
        return $this->get_full_information_recursive(false, $info, null, true);
    }

    /**
     * Gets information about this tree corresponding to the given result
     * object. (In other words, only conditions which the student actually
     * fails will be shown - and nothing if display is turned off.)
     *
     * @param info $info Information about location of condition tree
     * @param result $result Result object
     * @throws \coding_exception If you call on a non-root tree
     * @return string HTML data (empty string if none)
     */
    public function get_result_information(info $info, result $result) {
        if (!$this->root) {
            throw new \coding_exception('Only supported on root item');
        }
        return $this->get_full_information_recursive(false, $info, $result, true);
    }

    /**
     * Gets information about this tree (including all or selected children) as
     * HTML for display to staff or student.
     *
     * @param bool $not True if there is a NOT in effect
     * @param info $info Information about location of condition tree
     * @param result $result Result object if this is a student display, else null
     * @param bool $root True if this is the root item
     * @param bool $hidden Staff display; true if this tree has show=false (from parent)
     */
    protected function get_full_information_recursive(
            $not, info $info, result $result = null, $root, $hidden = false) {
        global $PAGE;

        // Get list of children - either full list, or those which are shown.
        $children = $this->children;
        $staff = true;
        if ($result) {
            $children = $result->filter_nodes($children);
            $staff = false;
        }

        // If no children, return empty string.
        if (!$children) {
            return '';
        }

        list($innernot, $andoperator) = $this->get_logic_flags($not);

        // If there is only one child, don't bother displaying this tree
        // (AND and OR makes no difference). Recurse to the child if a tree,
        // otherwise display directly.
        if (count ($children) === 1) {
            $child = reset($children);
            if ($this->root && is_null($result)) {
                if (is_null($this->showchildren)) {
                    $childhidden = !$this->show;
                } else {
                    $childhidden = !$this->showchildren[0];
                }
            } else {
                $childhidden = $hidden;
            }
            if ($child instanceof tree) {
                return $child->get_full_information_recursive(
                        $innernot, $info, $result, $root, $childhidden);
            } else {
                if ($root) {
                    $result = $child->get_standalone_description($staff, $innernot, $info);
                } else {
                    $result = $child->get_description($staff, $innernot, $info);
                }
                if ($childhidden) {
                    $result .= ' ' . get_string('hidden_marker', 'availability');
                }
                return $result;
            }
        }

        // Multiple children, so prepare child messages (recursive).
        $items = array();
        $index = 0;
        foreach ($children as $child) {
            // Work out if this node is hidden (staff view only).
            $childhidden = $this->root && is_null($result) &&
                    !is_null($this->showchildren) && !$this->showchildren[$index];
            if ($child instanceof tree) {
                $items[] = $child->get_full_information_recursive(
                        $innernot, $info, $result, false, $childhidden);
            } else {
                $childdescription = $child->get_description($staff, $innernot, $info);
                if ($childhidden) {
                    $childdescription .= ' ' . get_string('hidden_marker', 'availability');
                }
                $items[] = $childdescription;
            }
            $index++;
        }

        // If showing output to staff, and root is set to hide completely,
        // then include this information in the message.
        if ($this->root) {
            $treehidden = !$this->show && is_null($result);
        } else {
            $treehidden = $hidden;
        }

        // Format output for display.
        $renderer = $PAGE->get_renderer('core', 'availability');
        return $renderer->multiple_messages($root, $andoperator, $treehidden, $items);
    }

    /**
     * Converts the operator for the tree into two flags used for computing
     * the result.
     *
     * The 2 flags are $innernot (whether to set $not when calling for children)
     * and $andoperator (whether to use AND or OR operator to combine children).
     *
     * @param bool $not Not flag passed to this tree
     * @return array Array of the 2 flags ($innernot, $andoperator)
     */
    public function get_logic_flags($not) {
        // Work out which type of logic to use for the group.
        switch($this->op) {
            case self::OP_AND:
            case self::OP_OR:
                $negative = false;
                break;
            case self::OP_NOT_AND:
            case self::OP_NOT_OR:
                $negative = true;
                break;
            default:
                throw new \coding_exception('Unknown operator');
        }
        switch($this->op) {
            case self::OP_AND:
            case self::OP_NOT_AND:
                $andoperator = true;
                break;
            case self::OP_OR:
            case self::OP_NOT_OR:
                $andoperator = false;
                break;
            default:
                throw new \coding_exception('Unknown operator');
        }

        // Select NOT (or not) for children. It flips if this is a 'not' group.
        $innernot = $negative ? !$not : $not;

        // Select operator to use for this group. If flips for negative, because:
        // NOT (a AND b) = (NOT a) OR (NOT b)
        // NOT (a OR b) = (NOT a) AND (NOT b).
        if ($innernot) {
            $andoperator = !$andoperator;
        }
        return array($innernot, $andoperator);
    }

    public function save() {
        $result = new \stdClass();
        $result->op = $this->op;
        // Only root tree has the 'show' options.
        if ($this->root) {
            if ($this->op === self::OP_AND || $this->op === self::OP_NOT_OR) {
                $result->showc = $this->showchildren;
            } else {
                $result->show = $this->show;
            }
        }
        $result->c = array();
        foreach ($this->children as $child) {
            $result->c[] = $child->save();
        }
        return $result;
    }

    /**
     * Checks whether this tree is empty (contains no children).
     *
     * @return boolean True if empty
     */
    public function is_empty() {
        return count($this->children) === 0;
    }

    /**
     * Recursively gets all children of a particular class (you can use a base
     * class to get all conditions, or a specific class).
     *
     * @param string $classname Full class name e.g. core_availability\condition
     * @return array Array of nodes of that type (flattened, not a tree any more)
     */
    public function get_all_children($classname) {
        $result = array();
        $this->recursive_get_all_children($classname, $result);
        return $result;
    }

    /**
     * Internal function that implements get_all_children efficiently.
     *
     * @param string $classname Full class name e.g. core_availability\condition
     * @param array $result Output array of nodes
     */
    protected function recursive_get_all_children($classname, array &$result) {
        foreach ($this->children as $child) {
            if (is_a($child, $classname)) {
                $result[] = $child;
            }
            if ($child instanceof tree) {
                $child->recursive_get_all_children($classname, $result);
            }
        }
    }

    public function update_after_restore($restoreid, $courseid,
            \base_logger $logger, $name) {
        $changed = false;
        foreach ($this->children as $child) {
            $thischanged = $child->update_after_restore($restoreid, $courseid,
                    $logger, $name);
            $changed = $changed || $thischanged;
        }
        return $changed;
    }

    public function update_dependency_id($table, $oldid, $newid) {
        $changed = false;
        foreach ($this->children as $child) {
            $thischanged = $child->update_dependency_id($table, $oldid, $newid);
            $changed = $changed || $thischanged;
        }
        return $changed;
    }

    /**
     * Returns a JSON object which corresponds to a tree.
     *
     * Intended for unit testing, as normally the JSON values are constructed
     * by JavaScript code.
     *
     * This function generates 'nested' (i.e. not root-level) trees.
     *
     * @param array $children Array of JSON objects from component children
     * @param string $op Operator (tree::OP_xx)
     * @return stdClass JSON object
     * @throws coding_exception If you get parameters wrong
     */
    public static function get_nested_json(array $children, $op = self::OP_AND) {

        // Check $op and work out its type.
        switch($op) {
            case self::OP_AND:
            case self::OP_NOT_OR:
            case self::OP_OR:
            case self::OP_NOT_AND:
                break;
            default:
                throw new \coding_exception('Invalid $op');
        }

        // Do simple tree.
        $result = new \stdClass();
        $result->op = $op;
        $result->c = $children;
        return $result;
    }

    /**
     * Returns a JSON object which corresponds to a tree at root level.
     *
     * Intended for unit testing, as normally the JSON values are constructed
     * by JavaScript code.
     *
     * The $show parameter can be a boolean for all OP_xx options. For OP_AND
     * and OP_NOT_OR where you have individual show options, you can specify
     * a boolean (same for all) or an array.
     *
     * @param array $children Array of JSON objects from component children
     * @param string $op Operator (tree::OP_xx)
     * @param bool|array $show Whether 'show' option is turned on (see above)
     * @return stdClass JSON object ready for encoding
     * @throws coding_exception If you get parameters wrong
     */
    public static function get_root_json(array $children, $op = self::OP_AND, $show = true) {

        // Get the basic object.
        $result = self::get_nested_json($children, $op);

        // Check $op type.
        switch($op) {
            case self::OP_AND:
            case self::OP_NOT_OR:
                $multishow = true;
                break;
            case self::OP_OR:
            case self::OP_NOT_AND:
                $multishow = false;
                break;
        }

        // Add show options depending on operator.
        if ($multishow) {
            if (is_bool($show)) {
                $result->showc = array_pad(array(), count($result->c), $show);
            } else if (is_array($show)) {
                // The JSON will break if anything isn't an actual bool, so check.
                foreach ($show as $item) {
                    if (!is_bool($item)) {
                        throw new \coding_exception('$show array members must be bool');
                    }
                }
                // Check the size matches.
                if (count($show) != count($result->c)) {
                    throw new \coding_exception('$show array size does not match $children');
                }
                $result->showc = $show;
            } else {
                throw new \coding_exception('$show must be bool or array');
            }
        } else {
            if (!is_bool($show)) {
                throw new \coding_exception('For this operator, $show must be bool');
            }
            $result->show = $show;
        }

        return $result;
    }
}
