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
 * Upgrade definitions for the hvp module.
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Adds data for tracking when content was created and last modified.
 */
function hvp_upgrade_2016011300() {
    global $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp');

    // Define field timecreated to be added to hvp.
    $timecreated = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'slug');

    // Conditionally launch add field timecreated.
    if (!$dbman->field_exists($table, $timecreated)) {
        $dbman->add_field($table, $timecreated);
    }

    // Define field timemodified to be added to hvp.
    $timemodified = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');

    // Conditionally launch add field timemodified.
    if (!$dbman->field_exists($table, $timemodified)) {
        $dbman->add_field($table, $timemodified);
    }
}

/**
 * Adds table for keeping track of, and cleaning up temporary files
 */
function hvp_upgrade_2016042500() {
    global $DB;
    $dbman = $DB->get_manager();

    // Define table hvp_tmpfiles to be created.
    $table = new xmldb_table('hvp_tmpfiles');

    // Adding fields to table hvp_tmpfiles.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table hvp_tmpfiles.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally launch create table for hvp_tmpfiles.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Adds events table
 */
function hvp_upgrade_2016050600() {
    global $DB;
    $dbman = $DB->get_manager();

    // Define table hvp_events to be created.
    $table = new xmldb_table('hvp_events');

    // Adding fields to table hvp_events.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('type', XMLDB_TYPE_CHAR, '63', null, XMLDB_NOTNULL, null, null);
    $table->add_field('sub_type', XMLDB_TYPE_CHAR, '63', null, XMLDB_NOTNULL, null, null);
    $table->add_field('content_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('content_title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('library_name', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, null);
    $table->add_field('library_version', XMLDB_TYPE_CHAR, '31', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table hvp_events.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally launch create table for hvp_events.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Define table hvp_counters to be created.
    $table = new xmldb_table('hvp_counters');

    // Adding fields to table hvp_counters.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('type', XMLDB_TYPE_CHAR, '63', null, XMLDB_NOTNULL, null, null);
    $table->add_field('library_name', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, null);
    $table->add_field('library_version', XMLDB_TYPE_CHAR, '31', null, XMLDB_NOTNULL, null, null);
    $table->add_field('num', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table hvp_counters.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Adding indexes to table hvp_counters.
    $table->add_index('realkey', XMLDB_INDEX_NOTUNIQUE, [
        'type',
        'library_name',
        'library_version',
    ]);

    // Conditionally launch create table for hvp_counters.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Adds intro and introformat to hvp table
 */
function hvp_upgrade_2016051000() {
    global $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp');

    // Define field intro to be added to hvp.
    $intro = new xmldb_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');

    // Add field intro if not defined already.
    if (!$dbman->field_exists($table, $intro)) {
        $dbman->add_field($table, $intro);
    }

    // Define field introformat to be added to hvp.
    $introformat = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'intro');

    // Add field introformat if not defined already.
    if (!$dbman->field_exists($table, $introformat)) {
        $dbman->add_field($table, $introformat);
    }
}

/**
 * Changes context of activity files to enable backup an restore.
 */
function hvp_upgrade_2016110100() {
    global $DB;

    // Change context of activity files from COURSE to MODULE.
    $filearea  = 'content';
    $component = 'mod_hvp';

    // Find activity ID and correct context ID.
    $hvpsresult = $DB->get_records_sql(
        "SELECT f.id AS fileid, f.itemid, c.id, f.filepath, f.filename, f.pathnamehash
                   FROM {files} f
                   JOIN {course_modules} cm ON f.itemid = cm.instance
                   JOIN {modules} md ON md.id = cm.module
                   JOIN {context} c ON c.instanceid = cm.id
                  WHERE md.name = 'hvp'
                    AND f.filearea = 'content'
                    AND c.contextlevel = " . CONTEXT_MODULE
    );

    foreach ($hvpsresult as $hvp) {
        // Need to re-hash pathname after changing context.
        $pathnamehash = file_storage::get_pathname_hash($hvp->id,
            $component,
            $filearea,
            $hvp->itemid,
            $hvp->filepath,
            $hvp->filename
        );

        // Double check that hash doesn't exist (avoid duplicate entries).
        if (!$DB->get_field_sql("SELECT contextid FROM {files} WHERE pathnamehash = '{$pathnamehash}'")) {
            // Update context ID and pathname hash for files.
            $DB->execute("
                  UPDATE {files}
                  SET contextid = {$hvp->id},
                      pathnamehash = '{$pathnamehash}'
                  WHERE pathnamehash = '{$hvp->pathnamehash}'"
            );
        }
    }
}

/**
 * Notifies about breaking changes to H5P content type styling
 */
function hvp_upgrade_2016122800() {
    // @codingStandardsIgnoreLine
    \mod_hvp\framework::messages('info', '<span style="font-weight: bold;">Upgrade your H5P content types!</span> Old content types will still work, but the authoring tool will look and feel much better if you <a href="https://h5p.org/update-all-content-types">upgrade the content types</a>.');
    \mod_hvp\framework::printMessages('info', \mod_hvp\framework::messages('info'));
}

/**
 * Adds content type cache to enable the content type hub
 */
function hvp_upgrade_2017040500() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add content type cache database.
    $table = new xmldb_table('hvp_libraries_hub_cache');

    // Adding fields to table hvp_libraries_hub_cache.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('machine_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('major_version', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
    $table->add_field('minor_version', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
    $table->add_field('patch_version', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
    $table->add_field('h5p_major_version', XMLDB_TYPE_INTEGER, '4', null, null, null, null);
    $table->add_field('h5p_minor_version', XMLDB_TYPE_INTEGER, '4', null, null, null, null);
    $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('summary', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('icon', XMLDB_TYPE_CHAR, '511', null, XMLDB_NOTNULL, null, null);
    $table->add_field('created_at', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
    $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
    $table->add_field('is_recommended', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
    $table->add_field('popularity', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('screenshots', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('license', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('example', XMLDB_TYPE_CHAR, '511', null, XMLDB_NOTNULL, null, null);
    $table->add_field('tutorial', XMLDB_TYPE_CHAR, '511', null, null, null, null);
    $table->add_field('keywords', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('categories', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('owner', XMLDB_TYPE_CHAR, '511', null, null, null, null);

    // Adding keys to table hvp_libraries_hub_cache.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally create table for hvp_libraries_hub_cache.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Update the content type cache.
    $core = \mod_hvp\framework::instance();
    $core->updateContentTypeCache();

    // Print messages.
    \mod_hvp\framework::printMessages('info', \mod_hvp\framework::messages('info'));
    \mod_hvp\framework::printMessages('error', \mod_hvp\framework::messages('error'));

    // Add has_icon to libraries folder.
    $table = new xmldb_table('hvp_libraries');

    // Define field has_icon to be added to hvp_libraries.
    $hasicon = new xmldb_field('has_icon', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    // Add field has_icon if it does not exist.
    if (!$dbman->field_exists($table, $hasicon)) {
        $dbman->add_field($table, $hasicon);
    }

    // Display hub communication info.
    if (!get_config('mod_hvp', 'external_communication')) {
        // @codingStandardsIgnoreLine
        \mod_hvp\framework::messages('info', 'H5P now fetches content types directly from the H5P Hub. In order to do this, the H5P plugin will communicate with H5P.org once per day to fetch information about new and updated content types. It will send in anonymous data to the hub about H5P usage. You may disable the data contribution and/or the H5P Hub in the H5P settings.');
        \mod_hvp\framework::printMessages('info', \mod_hvp\framework::messages('info'));
    }

    // Enable hub and delete old communication variable.
    set_config('hub_is_enabled', true, 'mod_hvp');
    unset_config('hub_is_enabled', 'mod_hvp');
}

/**
 * Adds xAPI results table to enable reporting
 */
function hvp_upgrade_2017050900() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add report rendering.
    $table = new xmldb_table('hvp_xapi_results');

    // Add fields.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('content_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('parent_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('interaction_type', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('correct_responses_pattern', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('response', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('additionals', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

    // Add keys and index.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_index('result', XMLDB_INDEX_UNIQUE, [
        'id',
        'content_id',
        'user_id',
    ]);

    // Create table if it does not exist.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Adds raw score and max score to xapi results table
 */
function hvp_upgrade_2017060900() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add score to report rendering.
    $table = new xmldb_table('hvp_xapi_results');

    if ($dbman->table_exists($table)) {
        // Raw score field.
        $scorefield = new xmldb_field('raw_score', XMLDB_TYPE_INTEGER, '6');
        if (!$dbman->field_exists($table, $scorefield)) {
            $dbman->add_field($table, $scorefield);
        }

        // Max score field.
        $maxscorefield = new xmldb_field('max_score', XMLDB_TYPE_INTEGER, '6');
        if (!$dbman->field_exists($table, $maxscorefield)) {
            $dbman->add_field($table, $maxscorefield);
        }
    }
}

function hvp_upgrade_2018090300() {
    global $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp');

    // Remove old, unused metadata fields.
    if ($dbman->field_exists($table, 'author')) {
        $dbman->drop_field($table, new xmldb_field('author'));
    }

    if ($dbman->field_exists($table, 'license')) {
        $dbman->drop_field($table, new xmldb_field('license'));
    }

    if ($dbman->field_exists($table, 'meta_keywords')) {
        $dbman->drop_field($table, new xmldb_field('meta_keywords'));
    }

    if ($dbman->field_exists($table, 'meta_description')) {
        $dbman->drop_field($table, new xmldb_field('meta_description'));
    }

    // Create new metadata fields.
    if (!$dbman->field_exists($table, 'authors')) {
        $dbman->add_field($table,
            new xmldb_field('authors', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'source')) {
        $dbman->add_field($table,
            new xmldb_field('source', XMLDB_TYPE_CHAR, '255', null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'year_from')) {
        $dbman->add_field($table,
            new xmldb_field('year_from', XMLDB_TYPE_INTEGER, '4', null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'year_to')) {
        $dbman->add_field($table,
            new xmldb_field('year_to', XMLDB_TYPE_INTEGER, '4', null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'license')) {
        $dbman->add_field($table,
            new xmldb_field('license', XMLDB_TYPE_CHAR, '63', null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'license_version')) {
        $dbman->add_field($table,
            new xmldb_field('license_version', XMLDB_TYPE_CHAR, '15', null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'changes')) {
        $dbman->add_field($table,
            new xmldb_field('changes', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'license_extras')) {
        $dbman->add_field($table,
            new xmldb_field('license_extras', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'author_comments')) {
        $dbman->add_field($table,
            new xmldb_field('author_comments', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }

    // Add new libraries fields.
    $table = new xmldb_table('hvp_libraries');
    if (!$dbman->field_exists($table, 'add_to')) {
        $dbman->add_field($table,
            new xmldb_field('add_to', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }

    if (!$dbman->field_exists($table, 'metadata_settings')) {
        $dbman->add_field($table,
            new xmldb_field('metadata_settings', XMLDB_TYPE_TEXT, null, null, null, null, null)
        );
    }
}


/**
 * Adds authentication table
 *
 * @throws ddl_exception
 */
function hvp_upgrade_2019022600() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add auth table.
    $table = new xmldb_table('hvp_auth');

    // Add fields.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('created_at', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
    $table->add_field('secret', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);

    // Add keys and index.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_index('user_id', XMLDB_INDEX_UNIQUE, ['user_id']);

    // Create table if it does not exist.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Add default language to content
 *
 * @throws ddl_exception
 * @throws ddl_table_missing_exception
 */
function hvp_upgrade_2019030700() {
    global $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp');

    if (!$dbman->field_exists($table, 'default_language')) {
        $dbman->add_field($table,
            new xmldb_field('default_language', XMLDB_TYPE_CHAR, '32', null, null, null, null)
        );
    }
}

function hvp_upgrade_2020080400() {
    global $DB;
    $dbman = $DB->get_manager();
    // Define field completionscorerequired to be added to hvp.
    $table = new xmldb_table('hvp');
    // Conditionally launch add field completionscorerequired.
    if (!$dbman->field_exists($table, 'completionpass')) {
        $dbman->add_field(
            $table,
            new xmldb_field('completionpass', XMLDB_TYPE_INTEGER, '1', null, null, null, 0, 'timemodified')
        );
    }
}

function hvp_upgrade_2020080401() {
    global $DB;
    $dbman = $DB->get_manager();

    // Changing nullability of field completionpass on table hvp to not null.
    $table = new xmldb_table('hvp');
    $field = new xmldb_field('completionpass', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'timemodified');

    // Launch change of nullability for field completionpass.
    $dbman->change_field_notnull($table, $field);
}

function hvp_upgrade_2020082800() {
    global $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('hvp');

    if (!$dbman->field_exists($table, 'a11y_title')) {
        $dbman->add_field($table,
            new xmldb_field('a11y_title', XMLDB_TYPE_CHAR, '255', null, null, null, null)
        );
    }
}

/**
 * Drop old unused unique index, add nonunique index.
 */
function hvp_upgrade_2020091500() {
    global $DB;
    $dbman = $DB->get_manager();
    $table = new xmldb_table('hvp_xapi_results');
    $index = new xmldb_index('results', XMLDB_INDEX_NOTUNIQUE, ['content_id', 'user_id']);
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    $oldindex = new xmldb_index('result', XMLDB_INDEX_UNIQUE, ['id', 'content_id', 'user_id']);
    $dbman->drop_index($table, $oldindex);
}

function hvp_upgrade_2020112600() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add Content Hub fields to main content table.
    $table = new xmldb_table('hvp');
    if (!$dbman->field_exists($table, 'shared')) {
        $dbman->add_field($table, new xmldb_field('shared', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'completionpass'));
    }
    if (!$dbman->field_exists($table, 'synced')) {
        $dbman->add_field($table, new xmldb_field('synced', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'shared'));
    }
    if (!$dbman->field_exists($table, 'hub_id')) {
        $dbman->add_field($table, new xmldb_field('hub_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'synced'));
    }

    // Create table for caching content hub metadata.
    $table = new xmldb_table('hvp_content_hub_cache');
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('language', XMLDB_TYPE_CHAR, '31', null, XMLDB_NOTNULL, null, null);
    $table->add_field('json', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('last_checked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_index('language', XMLDB_INDEX_UNIQUE, ['language']);

    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Hvp module upgrade function.
 *
 * @param string $oldversion The version we are upgrading from
 *
 * @return bool Success
 */
function xmldb_hvp_upgrade($oldversion) {
    $upgrades = [
        2016011300,
        2016042500,
        2016050600,
        2016051000,
        2016110100,
        2016122800,
        2017040500,
        2017050900,
        2017060900,
        2018090300,
        2019022600,
        2019030700,
        2020080400,
        2020080401,
        2020082800,
        2020091500,
        2020112600,
    ];

    foreach ($upgrades as $version) {
        if ($oldversion < $version) {
            call_user_func("hvp_upgrade_{$version}");
            upgrade_mod_savepoint(true, $version, 'hvp');
        }
    }

    return true;
}
