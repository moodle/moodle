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
 * Block XP upgrade.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Block XP upgrade function.
 *
 * @param int $oldversion Old version.
 * @return true
 */
function xmldb_block_xp_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014031500) {

        // Define field enabled to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'courseid');

        // Conditionally launch add field enabled.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2014031500, 'xp');
    }

    if ($oldversion < 2014072301) {

        // Define field enableladder to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('enableladder', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'lastlogpurge');

        // Conditionally launch add field enableladder.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2014072301, 'xp');
    }

    if ($oldversion < 2014072401) {

        // Define field levelsdata to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('levelsdata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'enableladder');

        // Conditionally launch add field levelsdata.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2014072401, 'xp');
    }

    if ($oldversion < 2014072402) {

        // Define field enableinfos to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('enableinfos', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'enableladder');

        // Conditionally launch add field enableinfos.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2014072402, 'xp');
    }

    if ($oldversion < 2014072403) {

        // Define index courseid (unique) to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $index = new xmldb_index('courseid', XMLDB_INDEX_UNIQUE, ['courseid']);

        // Conditionally launch add index courseid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2014072403, 'xp');
    }

    if ($oldversion < 2014090800) {

        // Define field enablelevelupnotif to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('enablelevelupnotif', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'levelsdata');

        // Conditionally launch add field enablelevelupnotif.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2014090800, 'xp');
    }

    if ($oldversion < 2014090900) {

        // Define table block_xp_filters to be created.
        $table = new xmldb_table('block_xp_filters');

        // Adding fields to table block_xp_filters.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ruledata', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('points', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table block_xp_filters.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table block_xp_filters.
        $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);

        // Conditionally launch create table for block_xp_filters.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2014090900, 'xp');
    }

    if ($oldversion < 2014091200) {

        // Define field enablecustomlevelbadges to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('enablecustomlevelbadges', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'enablelevelupnotif'); // @codingStandardsIgnoreLine

        // Conditionally launch add field enablecustomlevelbadges.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2014091200, 'xp');
    }

    if ($oldversion < 2015030901) {

        // Define field maxactionspertime to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('maxactionspertime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '10', 'enablecustomlevelbadges'); // @codingStandardsIgnoreLine

        // Conditionally launch add field maxactionspertime.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2015030901, 'xp');
    }

    if ($oldversion < 2015030902) {

        // Define field timeformaxactions to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('timeformaxactions', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '60', 'maxactionspertime'); // @codingStandardsIgnoreLine

        // Conditionally launch add field timeformaxactions.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2015030902, 'xp');
    }

    if ($oldversion < 2015030903) {

        // Define field timebetweensameactions to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('timebetweensameactions', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '180', 'timeformaxactions'); // @codingStandardsIgnoreLine

        // Conditionally launch add field timebetweensameactions.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2015030903, 'xp');
    }

    if ($oldversion < 2016021500) {

        // We changed the way the "Level up" notifications are triggered, so we'll remove the old flags from the database.
        $DB->delete_records('user_preferences', ['name' => 'block_xp_notify_level_up']);

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2016021500, 'xp');
    }

    if ($oldversion < 2016021501) {

        // Define field identitymode to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('identitymode', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'timebetweensameactions');

        // Conditionally launch add field identitymode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2016021501, 'xp');
    }

    if ($oldversion < 2016021502) {

        // Define field rankmode to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('rankmode', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'identitymode');

        // Conditionally launch add field rankmode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2016021502, 'xp');
    }

    if ($oldversion < 2016021503) {

        // Define field neighbours to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('neighbours', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'rankmode');

        // Conditionally launch add field neighbours.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2016021503, 'xp');
    }

    if ($oldversion < 2016022401) {

        // Define field identitymode to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('identitymode', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'timebetweensameactions');

        // Conditionally launch add field identitymode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2016022401, 'xp');
    }

    if ($oldversion < 2016022402) {

        // Define field rankmode to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('rankmode', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'identitymode');

        // Conditionally launch add field rankmode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2016022402, 'xp');
    }

    if ($oldversion < 2016022403) {

        // Define field neighbours to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('neighbours', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'rankmode');

        // Conditionally launch add field neighbours.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2016022403, 'xp');
    }

    if ($oldversion < 2017021401) {

        // Define field enablecheatguard to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('enablecheatguard', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'lastlogpurge');

        // Conditionally launch add field enablecheatguard.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2017021401, 'xp');
    }

    if ($oldversion < 2017062900) {

        // Define field defaultfilters to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('defaultfilters', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1', 'neighbours');

        // Conditionally launch add field defaultfilters.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2017062900, 'xp');
    }

    if ($oldversion < 2017062901) {

        // Although this should have been done when adding the database field, here
        // we ensure that existing instances of the block will be set to the 'static'
        // flag for default filters. This ensures that they are properly marked as
        // legacy instances, so that we can convert them on the fly later on.
        define('BLOCK_XP_UPGRADE_DEFAULT_FILTERS_STATIC', 1);
        $DB->set_field('block_xp_config', 'defaultfilters', BLOCK_XP_UPGRADE_DEFAULT_FILTERS_STATIC);

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2017062901, 'xp');
    }

    if ($oldversion < 2017070400) {

        // Define field laddercols to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('laddercols', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'xp,progress', 'defaultfilters');

        // Conditionally launch add field laddercols.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2017070400, 'xp');
    }

    if ($oldversion < 2017071601) {

        // Find what courses were set to, and use that for our admin setting.
        $keeplogsforever = $DB->record_exists('block_xp_config', ['keeplogs' => 0]);
        $keeplogsmax = (int) $DB->get_field('block_xp_config', 'MAX(keeplogs)', []);
        set_config('keeplogs', $keeplogsforever ? 0 : $keeplogsmax, 'block_xp');

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2017071601, 'xp');
    }

    if ($oldversion < 2017071602) {

        // Define field enablelog to be dropped from block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('enablelog');

        // Conditionally launch drop field enablelog.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2017071602, 'xp');
    }

    if ($oldversion < 2017071603) {

        // Define field keeplogs to be dropped from block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('keeplogs');

        // Conditionally launch drop field keeplogs.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2017071603, 'xp');
    }

    if ($oldversion < 2017082000) {

        // Some webservices were broken because we introduced a format of user
        // preferences which was not supported. Any preference that was introduced
        // with the former name needs to be removed. See MDL-59876.
        $like = $DB->sql_like('name', ':name');
        $sql = "DELETE FROM {user_preferences}
                      WHERE $like";
        $params = ['name' => 'block_xp|%'];
        $DB->execute($sql, $params);

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2017082000, 'xp');
    }

    if ($oldversion < 2019020301) {

        // Define field instructions to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('instructions', XMLDB_TYPE_TEXT, null, null, null, null, null, 'laddercols');

        // Conditionally launch add field instructions.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2019020301, 'xp');
    }

    if ($oldversion < 2019020302) {

        // Define field instructions_format to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('instructions_format', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'instructions');

        // Conditionally launch add field instructions_format.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2019020302, 'xp');
    }

    if ($oldversion < 2019120200) {

        // Define field category to be added to block_xp_filters.
        $table = new xmldb_table('block_xp_filters');
        $field = new xmldb_field('category', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0', 'courseid');

        // Conditionally launch add field category.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2019120200, 'xp');
    }

    if ($oldversion < 2019120300) {

        // Define index courseidcat (not unique) to be added to block_xp_filters.
        $table = new xmldb_table('block_xp_filters');
        $index = new xmldb_index('courseidcat', XMLDB_INDEX_NOTUNIQUE, ['courseid', 'category']);

        // Conditionally launch add index courseidcat.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2019120300, 'xp');
    }

    if ($oldversion < 2020043001) {

        // For the first time since 2015, display previously dismissed notices.
        $DB->delete_records('user_preferences', ['name' => 'block_xp_notices']);

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2020043001, 'xp');
    }

    if ($oldversion < 2022021121) {

        // Define field blocktitle to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('blocktitle', XMLDB_TYPE_TEXT, null, null, null, null, null, 'instructions_format');

        // Conditionally launch add field blocktitle.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2022021121, 'xp');
    }

    if ($oldversion < 2022021122) {

        // Define field blockdescription to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('blockdescription', XMLDB_TYPE_TEXT, null, null, null, null, null, 'blocktitle');

        // Conditionally launch add field blockdescription.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2022021122, 'xp');
    }

    if ($oldversion < 2022021123) {

        // Define field blockrecentactivity to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('blockrecentactivity', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'blockdescription');

        // Conditionally launch add field blockrecentactivity.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2022021123, 'xp');
    }

    if ($oldversion < 2022090112) {

        // Define field blockrankingsnapshot to be added to block_xp_config.
        $table = new xmldb_table('block_xp_config');
        $field = new xmldb_field('blockrankingsnapshot', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'blockrecentactivity'); // @codingStandardsIgnoreLine

        // Conditionally launch add field blockrankingsnapshot.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2022090112, 'xp');
    }

    if ($oldversion < 2023080702) {

        // The lvl column is deprecated, but kept to avoid breaking external integrations.
        // Nevertheless, all values are set to the default of 1 to avoid confusion with mixed data.
        $DB->set_field('block_xp', 'lvl', 1, []);

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2023080702, 'xp');
    }

    if ($oldversion < 2024040211) {

        // Define table block_xp_rule to be created.
        $table = new xmldb_table('block_xp_rule');

        // Adding fields to table block_xp_rule.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('childcontextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('points', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('type', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filter', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filtercourseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('filtercmid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('filterint1', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('filterchar1', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table block_xp_rule.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);

        // Adding indexes to table block_xp_rule.
        $table->add_index('contextids', XMLDB_INDEX_NOTUNIQUE, ['contextid', 'childcontextid']);

        // Conditionally launch create table for block_xp_rule.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Xp savepoint reached.
        upgrade_block_savepoint(true, 2024040211, 'xp');
    }

    return true;
}
