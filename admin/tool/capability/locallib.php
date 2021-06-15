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
 * Functions used by the capability tool.
 *
 * @package    tool_capability
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Calculates capability data organised by context for the given roles.
 *
 * @param string $capability The capability to get data for.
 * @param array $roles An array of roles to get data for.
 * @return context[] An array of contexts.
 */
function tool_capability_calculate_role_data($capability, array $roles) {
    global $DB;

    $systemcontext = context_system::instance();
    $roleids = array_keys($roles);

    // Work out the bits needed for the SQL WHERE clauses.
    $params = array($capability);
    list($sqlroletest, $roleparams) = $DB->get_in_or_equal($roleids);
    $params = array_merge($params, $roleparams);
    $sqlroletest = 'AND roleid ' . $sqlroletest;

    // Get all the role_capabilities rows for this capability - that is, all
    // role definitions, and all role overrides.
    $sql = 'SELECT id, roleid, contextid, permission
              FROM {role_capabilities}
             WHERE capability = ? '.$sqlroletest;
    $rolecaps = $DB->get_records_sql($sql, $params);

    // In order to display a nice tree of contexts, we need to get all the
    // ancestors of all the contexts in the query we just did.
    $sql = 'SELECT DISTINCT con.path, 1
              FROM {context} con
              JOIN {role_capabilities} rc ON rc.contextid = con.id
             WHERE capability = ? ' .
            $sqlroletest .
            // Context path should never be null, but can happen in old database with
            // bad data (e.g. a course_module where the corresponding course no longer exists).
            // We need to leave these out of the report to prevent errors.
            ' AND con.path IS NOT NULL';
    $relevantpaths = $DB->get_records_sql_menu($sql, $params);
    $requiredcontexts = array($systemcontext->id);
    foreach ($relevantpaths as $path => $notused) {
        $requiredcontexts = array_merge($requiredcontexts, explode('/', trim($path, '/')));
    }
    $requiredcontexts = array_unique($requiredcontexts);

    // Now load those contexts.
    list($sqlcontexttest, $contextparams) = $DB->get_in_or_equal($requiredcontexts);
    $contexts = get_sorted_contexts('ctx.id ' . $sqlcontexttest, $contextparams);

    // Prepare some empty arrays to hold the data we are about to compute.
    foreach ($contexts as $conid => $con) {
        $contexts[$conid]->children = array();
        $contexts[$conid]->rolecapabilities = array();
    }

    // Put the contexts into a tree structure.
    foreach ($contexts as $conid => $con) {
        $context = context::instance_by_id($conid);
        try {
            $parentcontext = $context->get_parent_context();
            if ($parentcontext) { // Will be false if $context is the system context.
                $contexts[$parentcontext->id]->children[] = $conid;
            }
        } catch (dml_missing_record_exception $e) {
            // Ignore corrupt context tree structure here. Don't let it break
            // showing the rest of the report.
            continue;
        }
    }

    // Put the role capabilities into the context tree.
    foreach ($rolecaps as $rolecap) {
        if (!isset($contexts[$rolecap->contextid])) {
            // Skip capabilities in orphaned contexts that are not in the tree.
            continue;
        }
        $contexts[$rolecap->contextid]->rolecapabilities[$rolecap->roleid] = $rolecap->permission;
    }

    // Fill in any missing rolecaps for the system context.
    foreach ($roleids as $roleid) {
        if (!isset($contexts[$systemcontext->id]->rolecapabilities[$roleid])) {
            $contexts[$systemcontext->id]->rolecapabilities[$roleid] = CAP_INHERIT;
        }
    }

    return $contexts;
}
