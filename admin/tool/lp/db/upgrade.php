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
 * Leaning plan upgrade steps.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_tool_lp_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    if ($oldversion < 2015052403) {

        // Define index idnumber (unique) to be added to tool_lp_competency_framework.
        $table = new xmldb_table('tool_lp_competency_framework');
        $index = new xmldb_index('idnumber', XMLDB_INDEX_UNIQUE, array('idnumber'));

        // Conditionally launch add index idnumber.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052403, 'tool', 'lp');
    }

    if ($oldversion < 2015052404) {

        // Define index idnumberframework (unique) to be added to tool_lp_competency.
        $table = new xmldb_table('tool_lp_competency');
        $index = new xmldb_index('idnumberframework', XMLDB_INDEX_UNIQUE, array('competencyframeworkid', 'idnumber'));

        // Conditionally launch add index idnumberframework.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052404, 'tool', 'lp');
    }

    if ($oldversion < 2015052405) {

        // Define field contextid to be added to tool_lp_competency_framework.
        $table = new xmldb_table('tool_lp_competency_framework');
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null,
            context_system::instance()->id, 'shortname');

        // Conditionally launch add field contextid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052405, 'tool', 'lp');
    }

    if ($oldversion < 2015052406) {

        // Define field sortorder to be dropped from tool_lp_competency_framework.
        $table = new xmldb_table('tool_lp_competency_framework');
        $field = new xmldb_field('sortorder');

        // Conditionally launch drop field sortorder.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052406, 'tool', 'lp');
    }

    if ($oldversion < 2015052407) {

        // Define field contextid to be added to tool_lp_template.
        $table = new xmldb_table('tool_lp_template');
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null,
            context_system::instance()->id, 'shortname');

        // Conditionally launch add field contextid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052407, 'tool', 'lp');
    }

    if ($oldversion < 2015052408) {

        // Define field sortorder to be dropped from tool_lp_template.
        $table = new xmldb_table('tool_lp_template');
        $field = new xmldb_field('sortorder');

        // Conditionally launch drop field sortorder.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052408, 'tool', 'lp');
    }

    if ($oldversion < 2015052412) {

        // Define table tool_lp_related_competency to be created.
        $table = new xmldb_table('tool_lp_related_competency');

        // Adding fields to table tool_lp_related_competency.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('relatedcompetencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_lp_related_competency.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for tool_lp_related_competency.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052412, 'tool', 'lp');
    }

    if ($oldversion < 2015052414) {

        // Define field taxonomies to be added to tool_lp_competency_framework.
        $table = new xmldb_table('tool_lp_competency_framework');
        $field = new xmldb_field('taxonomies', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'visible');

        // Conditionally launch add field taxonomies.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052414, 'tool', 'lp');
    }

    if ($oldversion < 2015052416) {

        // Define field idnumber to be dropped from tool_lp_template.
        $table = new xmldb_table('tool_lp_template');
        $field = new xmldb_field('idnumber');

        // Conditionally launch drop field idnumber.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052416, 'tool', 'lp');
    }

    if ($oldversion < 2015052420) {

        // Define table tool_lp_user_competency to be created.
        $table = new xmldb_table('tool_lp_user_competency');

        // Adding fields to table tool_lp_user_competency.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('reviewerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('proficiency', XMLDB_TYPE_INTEGER, '2', null, null, null, '0');
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_lp_user_competency.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $index = new xmldb_index('useridcompetency', XMLDB_INDEX_UNIQUE, array('userid', 'competencyid'));

        // Conditionally launch create table for tool_lp_user_competency.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Conditionally launch add index useridcompetency.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052420, 'tool', 'lp');
    }

    if ($oldversion < 2015052423) {

        // Define table tool_lp_user_competency_plan to be created.
        $table = new xmldb_table('tool_lp_user_competency_plan');

        // Adding fields to table tool_lp_user_competency_plan.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('reviewerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('proficiency', XMLDB_TYPE_INTEGER, '2', null, null, null, '0');
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_lp_user_competency_plan.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $index = new xmldb_index('usercompetencyplan', XMLDB_INDEX_UNIQUE, array('userid', 'competencyid', 'planid'));

        // Conditionally launch create table for tool_lp_user_competency_plan.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Conditionally launch add index useridcompetency.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052423, 'tool', 'lp');
    }

    if ($oldversion < 2015052424) {

        // Define table tool_lp_plan_competency to be created.
        $table = new xmldb_table('tool_lp_plan_competency');

        // Adding fields to table tool_lp_plan_competency.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_lp_user_competency.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Index for table tool_lp_plan_competency.
        $index = new xmldb_index('planidcompetencyid', XMLDB_INDEX_UNIQUE, array('planid', 'competencyid'));

        // Conditionally launch create table for tool_lp_plan_competency.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Conditionally launch add index planidcompetencyid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052424, 'tool', 'lp');
    }

    if ($oldversion < 2015052427) {

        // Define fields status and reviewerid to be dropped from tool_lp_user_competency_plan.
        $table = new xmldb_table('tool_lp_user_competency_plan');
        $fieldstatus = new xmldb_field('status');
        $fieldreviewerid = new xmldb_field('reviewerid');

        // Conditionally launch drop field status.
        if ($dbman->field_exists($table, $fieldstatus)) {
            $dbman->drop_field($table, $fieldstatus);
        }

        // Conditionally launch drop field status.
        if ($dbman->field_exists($table, $fieldreviewerid)) {
            $dbman->drop_field($table, $fieldreviewerid);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052427, 'tool', 'lp');
    }

    if ($oldversion < 2015111001) {

        // Define field ruletype to be added to tool_lp_competency.
        $table = new xmldb_table('tool_lp_competency');
        $field = new xmldb_field('ruletype', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'sortorder');

        // Conditionally launch add field ruletype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015111001, 'tool', 'lp');
    }

    if ($oldversion < 2015111002) {

        // Define field ruleoutcome to be added to tool_lp_competency.
        $table = new xmldb_table('tool_lp_competency');
        $field = new xmldb_field('ruleoutcome', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'ruletype');

        // Conditionally launch add field ruleoutcome.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015111002, 'tool', 'lp');
    }

    if ($oldversion < 2015111003) {

        // Define field ruleconfig to be added to tool_lp_competency.
        $table = new xmldb_table('tool_lp_competency');
        $field = new xmldb_field('ruleconfig', XMLDB_TYPE_TEXT, null, null, null, null, null, 'ruleoutcome');

        // Conditionally launch add field ruleconfig.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015111003, 'tool', 'lp');
    }

    if ($oldversion < 2015111004) {

        // Define index ruleoutcome (not unique) to be added to tool_lp_competency.
        $table = new xmldb_table('tool_lp_competency');
        $index = new xmldb_index('ruleoutcome', XMLDB_INDEX_NOTUNIQUE, array('ruleoutcome'));

        // Conditionally launch add index ruleoutcome.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015111004, 'tool', 'lp');
    }

    return true;
}
