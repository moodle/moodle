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
 * Helper functions to implement the complex get_user_capability_course function.
 *
 * @package core
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\access;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper functions to implement the complex get_user_capability_course function.
 *
 * @package core
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_user_capability_course_helper {
    /**
     * Based on the given user's access data (roles) and system role definitions, works out
     * an array of capability values at each relevant context for the given user and capability.
     *
     * This is organised by the effective context path (the one at which the capability takes
     * effect) and then by role id.
     *
     * @param int $userid User id
     * @param string $capability Capability e.g. 'moodle/course:view'
     * @return array Array of capability constants, indexed by context path and role id
     */
    protected static function get_capability_info_at_each_context($userid, $capability) {
        // Get access data for user.
        $accessdata = get_user_accessdata($userid);

        // Get list of roles for user (any location) and information about these roles.
        $roleids = [];
        foreach ($accessdata['ra'] as $path => $roles) {
            foreach ($roles as $roleid) {
                $roleids[$roleid] = true;
            }
        }
        $rdefs = get_role_definitions(array_keys($roleids));

        // Get data for required capability at each context path where the user has a role that can
        // affect it.
        $systemcontext = \context_system::instance();
        $pathroleperms = [];
        foreach ($accessdata['ra'] as $userpath => $roles) {
            foreach ($roles as $roleid) {
                // Get role definition for that role.
                foreach ($rdefs[$roleid] as $rolepath => $caps) {
                    // Ignore if this override/definition doesn't refer to the relevant cap.
                    if (!array_key_exists($capability, $caps)) {
                        continue;
                    }

                    // Check path is /1 or matches a path the user has.
                    if ($rolepath === '/' . $systemcontext->id) {
                        // Note /1 is listed first in the array so this entry will be overridden
                        // if there is an override for the role on this actual level.
                        $effectivepath = $userpath;
                    } else if (preg_match('~^' . $userpath . '($|/)~', $rolepath)) {
                        $effectivepath = $rolepath;
                    } else {
                        // Not inside an area where the user has the role, so ignore.
                        continue;
                    }

                    if (!array_key_exists($effectivepath, $pathroleperms)) {
                        $pathroleperms[$effectivepath] = [];
                    }
                    $pathroleperms[$effectivepath][$roleid] = $caps[$capability];
                }
            }
        }

        return $pathroleperms;
    }

    /**
     * Calculates a permission tree based on an array of information about role permissions.
     *
     * The input parameter must be in the format returned by get_capability_info_at_each_context.
     *
     * The output is the root of a tree of stdClass objects with the fields 'path' (a context path),
     * 'allow' (true or false), and 'children' (an array of similar objects).
     *
     * @param array $pathroleperms Array of permissions
     * @return \stdClass Root object of permission tree
     */
    protected static function calculate_permission_tree(array $pathroleperms) {
        // Considering each discovered context path as an inflection point, evaluate the user's
        // permission (based on all roles) at each point.
        $pathallows = [];
        $mindepth = 1000;
        $maxdepth = 0;
        foreach ($pathroleperms as $path => $roles) {
            $evaluatedroleperms = [];

            // Walk up the tree starting from this path.
            $innerpath = $path;
            while ($innerpath !== '') {
                $roles = $pathroleperms[$innerpath];

                // Evaluate roles at this path level.
                foreach ($roles as $roleid => $perm) {
                    if (!array_key_exists($roleid, $evaluatedroleperms)) {
                        $evaluatedroleperms[$roleid] = $perm;
                    } else {
                        // The existing one is at a more specific level so it takes precedence
                        // UNLESS this is a prohibit.
                        if ($perm == CAP_PROHIBIT) {
                            $evaluatedroleperms[$roleid] = $perm;
                        }
                    }
                }

                // Go up to next path level (if any).
                do {
                    $innerpath = substr($innerpath, 0, strrpos($innerpath, '/'));
                    if ($innerpath === '') {
                        // No higher level data.
                        break;
                    }
                } while (!array_key_exists($innerpath, $pathroleperms));
            }

            // If we have an allow from any role, and no prohibits, then user can access this path,
            // else not.
            $allow = false;
            foreach ($evaluatedroleperms as $perm) {
                if ($perm == CAP_ALLOW) {
                    $allow = true;
                } else if ($perm == CAP_PROHIBIT) {
                    $allow = false;
                    break;
                }
            }

            // Store the result based on path and depth so that we can process in depth order in
            // the next step.
            $depth = strlen(preg_replace('~[^/]~', '', $path));
            $mindepth = min($depth, $mindepth);
            $maxdepth = max($depth, $maxdepth);
            $pathallows[$depth][$path] = $allow;
        }

        // Organise into a tree structure, processing in depth order so that we have ancestors
        // set up before we encounter their children.
        $root = (object)['allow' => false, 'path' => null, 'children' => []];
        $nodesbypath = [];
        for ($depth = $mindepth; $depth <= $maxdepth; $depth++) {
            // Skip any missing depth levels.
            if (!array_key_exists($depth, $pathallows)) {
                continue;
            }
            foreach ($pathallows[$depth] as $path => $allow) {
                // Value for new tree node.
                $leaf = (object)['allow' => $allow, 'path' => $path, 'children' => []];

                // Try to find a place to join it on if there is one.
                $ancestorpath = $path;
                $found = false;
                while ($ancestorpath) {
                    $ancestorpath = substr($ancestorpath, 0, strrpos($ancestorpath, '/'));
                    if (array_key_exists($ancestorpath, $nodesbypath)) {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    $nodesbypath[$ancestorpath]->children[] = $leaf;
                } else {
                    $root->children[] = $leaf;
                }
                $nodesbypath[$path] = $leaf;
            }
        }

        return $root;
    }

    /**
     * Given a permission tree (in calculate_permission_tree format), removes any subtrees that
     * are negative from the root. For example, if a top-level node of the permission tree has
     * 'false' permission then it is meaningless because the default permission is already false;
     * this function will remove it. However, if there is a child within that node that is positive,
     * then that will need to be kept.
     *
     * @param \stdClass $root Root object
     * @return \stdClass Filtered tree root
     */
    protected static function remove_negative_subtrees($root) {
        // If a node 'starts' negative, we don't need it (as negative is the default) - extract only
        // subtrees that start with a positive value.
        $positiveroot = (object)['allow' => false, 'path' => null, 'children' => []];
        $consider = [$root];
        while ($consider) {
            $first = array_shift($consider);
            foreach ($first->children as $node) {
                if ($node->allow) {
                    // Add directly to new root.
                    $positiveroot->children[] = $node;
                } else {
                    // Consider its children for adding to root (if there are any positive ones).
                    $consider[] = $node;
                }
            }
        }
        return $positiveroot;
    }

    /**
     * Removes duplicate nodes of a tree - where a child node has the same permission as its
     * parent.
     *
     * @param \stdClass $parent Tree root node
     */
    protected static function remove_duplicate_nodes($parent) {
        $length = count($parent->children);
        $index = 0;
        while ($index < $length) {
            $child = $parent->children[$index];
            if ($child->allow === $parent->allow) {
                // Remove child node, but add its children to this node instead.
                array_splice($parent->children, $index, 1);
                $length--;
                $index--;
                foreach ($child->children as $grandchild) {
                    $parent->children[] = $grandchild;
                    $length++;
                }
            } else {
                // Keep child node, but recurse to remove its unnecessary children.
                self::remove_duplicate_nodes($child);
            }
            $index++;
        }
    }

    /**
     * Gets a permission tree for the given user and capability, representing the value of that
     * capability at different contexts across the system. The tree will be simplified as far as
     * possible.
     *
     * The output is the root of a tree of stdClass objects with the fields 'path' (a context path),
     * 'allow' (true or false), and 'children' (an array of similar objects).
     *
     * @param int $userid User id
     * @param string $capability Capability e.g. 'moodle/course:view'
     * @return \stdClass Root node of tree
     */
    protected static function get_tree($userid, $capability) {
        // Extract raw capability data for this user and capability.
        $pathroleperms = self::get_capability_info_at_each_context($userid, $capability);

        // Convert the raw data into a permission tree based on context.
        $root = self::calculate_permission_tree($pathroleperms);
        unset($pathroleperms);

        // Simplify the permission tree by removing unnecessary nodes.
        $root = self::remove_negative_subtrees($root);
        self::remove_duplicate_nodes($root);

        // Return the tree.
        return $root;
    }

    /**
     * Creates SQL suitable for restricting by contexts listed in the given permission tree.
     *
     * This function relies on the permission tree being in the format created by get_tree.
     * Specifically, all the children of the root element must be set to 'allow' permission,
     * children of those children must be 'not allow', children of those grandchildren 'allow', etc.
     *
     * @param \stdClass $parent Root node of permission tree
     * @return array Two-element array of SQL (containing ? placeholders) and then a params array
     */
    protected static function create_sql($parent) {
        global $DB;

        $sql = '';
        $params = [];
        if ($parent->path !== null) {
            // Except for the root element, create the condition that it applies to the context of
            // this element (or anything within it).
            $sql = ' (x.path = ? OR ' . $DB->sql_like('x.path', '?') .')';
            $params[] = $parent->path;
            $params[] = $parent->path . '/%';
            if ($parent->children) {
                // When there are children, these are assumed to have the opposite sign i.e. if we
                // are allowing the parent, we are not allowing the children, and vice versa. So
                // the 'OR' clause for children will be inside this 'AND NOT'.
                $sql .= ' AND NOT (';
            }
        } else if (count($parent->children) > 1) {
            // Place brackets in the query when it is going to be an OR of multiple conditions.
            $sql .= ' (';
        }
        if ($parent->children) {
            $first = true;
            foreach ($parent->children as $child) {
                if ($first) {
                    $first = false;
                } else {
                    $sql  .= ' OR';
                }

                // Recuse to get the child requirements - this will be the check that the context
                // is within the child, plus possibly and 'AND NOT' for any different contexts
                // within the child.
                list ($childsql, $childparams) = self::create_sql($child);
                $sql .= $childsql;
                $params = array_merge($params, $childparams);
            }
            // Close brackets if opened above.
            if ($parent->path !== null || count($parent->children) > 1) {
                $sql .= ')';
            }
        }
        return [$sql, $params];
    }

    /**
     * Gets SQL to restrict a query to contexts in which the user has a capability.
     *
     * This returns an array with two elements (SQL containing ? placeholders, and a params array).
     * The SQL is intended to be used as part of a WHERE clause. It relies on the prefix 'x' being
     * used for the Moodle context table.
     *
     * If the user does not have the permission anywhere at all (so that there is no point doing
     * the query) then the two returned values will both be false.
     *
     * @param int $userid User id
     * @param string $capability Capability e.g. 'moodle/course:view'
     * @return array Two-element array of SQL (containing ? placeholders) and then a params array
     */
    public static function get_sql($userid, $capability) {
        // Get a tree of capability permission at various contexts for current user.
        $root = self::get_tree($userid, $capability);

        // The root node always has permission false. If there are no child nodes then the user
        // cannot access anything.
        if (!$root->children) {
            return [false, false];
        }

        // Get SQL to limit contexts based on the permission tree.
        return self::create_sql($root);

    }
}
