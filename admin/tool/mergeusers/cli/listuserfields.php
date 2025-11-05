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
 * List potential user-related fields from Moodle database.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 Universitat Rovira i Virgili
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define("CLI_SCRIPT", true);

require_once(__DIR__ . '/../../../../config.php');

ini_set('display_errors', true);
ini_set('error_reporting', E_ALL | E_STRICT);

global $CFG, $DB;
require_once($CFG->libdir . '/clilib.php');


cli_heading('List of Moodle database tables and potential %user%-related columns');
$tables = $DB->get_tables(false);

cli_writeln(sprintf('Processing %d tables and loading their XML schema...', count($tables)));
$schema = $DB->get_manager()->get_install_xml_schema();
$matching = [];
$compoundindexes = [];
// All these %user%-related compound indexes are ignored, since they are false positives.
// Potential reasons, to name a few:
// 1. They are not user-related, actually.
// 2. They are somehow user-related, but the schema shows that the related index is not unique, actually.
$compoundindexestoignore = [
    'competency_userevidencecomp,competencyid,userevidenceid' => true,
    'enrol_lti_user_resource_link,ltiuserid,resourcelinkid' => true,
];
$matchingcount = [];
$matchingbykeys = [];
$nonmatching = [];
$alluserrelatedcolumns = [];
$alluserrelatedcolumnswithtable = [];
$omittedtables = [];
foreach ($tables as $table) {
    $columns = $DB->get_columns($table, false);
    $schematable = $schema->getTable($table);
    if (!$schematable) {
        $omittedtables[$table] = $table;
        continue;
    }
    $tablekeys = $schema->getTable($table)->getKeys();
    $userrelatedbykeys = array_filter(
        array_map(
            /** @param xmldb_key $key */
            function ($key) {
                $keyfields = implode(',', $key->getRefFields());
                if ($key->getRefTable() == 'user' && $keyfields == 'id') {
                    return implode(',', $key->getFields());
                }
                return null;
            },
            $tablekeys,
        ),
    );
    $userrelatedbykeys = array_flip($userrelatedbykeys);
    $userrelatedcolumns = array_filter(
        $columns,
        function ($column) use ($table, $userrelatedbykeys) {
            return (strstr($column->name, 'user') && $column->meta_type == 'I') ||
                isset($userrelatedbykeys[$column->name]);
        }
    );

    // Table schema contain the XML definition of the table.
    $schemaindexes = $schematable->getIndexes();
    $allindexesfromtable = [];
    foreach ($schemaindexes as $index) {
        $orderedfields = $index->getFields();
        if (count($orderedfields) <= 1) {
            // We are only interested in compound indexes.
            continue;
        }
        sort($orderedfields);
        $key = $table . ',' . implode(',', $orderedfields);
        $allindexesfromtable[$key] = [
            'name' => $index->getName(),
            'fields' => $orderedfields,
            'unique' => $index->getUnique(),
            'comment' => $index->getComment() ?? '',
        ];
    }
    // Database table may contain different indexes than the XML definition.
    $dbindexes = $DB->get_indexes($table);
    foreach ($dbindexes as $dbindexname => $dbindex) {
        if (count($dbindex['columns']) <= 1) {
            // We are only interested in compound indexes.
            continue;
        }
        $orderedfields = $dbindex['columns'];
        sort($orderedfields);
        $key = $table . ',' . implode(',', $orderedfields);
        $unique = $dbindex['unique'];
        if (isset($allindexesfromtable[$key])) {
            if ($allindexesfromtable[$key]['unique'] == $unique) {
                // Index in database and in XML definition, with same uniqueness.
                continue;
            }
            // For any reason, the index uniqueness is different. Update with the actual value.
            $allindexesfromtable[$key]['unique'] = $unique;
            continue;
        }
        // Index in database but not in XML definition.
        $allindexesfromtable[$key] = [
            'name' => $dbindexname,
            'fields' => $orderedfields,
            'unique' => $unique,
            'comment' => 'Index in database but not in XML definition.',
        ];
    }

    $tablecompoundindexes = array_filter(
        array_map(
            /** @param xmldb_index $index */
            function ($index) use ($table, $userrelatedbykeys, $userrelatedcolumns) {
                $fields = array_flip($index['fields']);
                // Differentiate indexes matching columns with foreign keys.
                // They are for sure user-related compound indexes.
                $intersect = array_intersect_key($userrelatedbykeys, $fields);
                $type = '';
                if (count($intersect) > 0) {
                    $type = 'bykey';
                } else {
                    // Otherwise, indexes' fields may match some user-related column by name.
                    $intersect = array_intersect_key($userrelatedcolumns, $fields);
                    if (count($intersect) > 0) {
                        $type = 'byname';
                    }
                }
                if ($type != '') {
                    $userfields = array_intersect_key($fields, $userrelatedcolumns);
                    $otherfields = array_diff_key($fields, $userrelatedcolumns);
                    $order = 0; // Just to inform: non-unique index, matching column by name.
                    if ($index['unique']) {
                        if ($type == 'bykey') {
                            $order = 3; // Index to consider for sure: unique index, matching column by foreign key.
                        } else {
                            $order = 2; // Index to potentially consider: unique index, matching column by name.
                        }
                    } else {
                        if ($type == 'bykey') {
                            $order = 1; // Just to inform: non-unique index, matching column by foreign key.
                        }
                    }
                    return [
                        'name' => $index['name'],
                        'userfield' => array_keys($userfields),
                        'otherfields' => array_keys($otherfields),
                        'type' => $type,
                        'unique' => $index['unique'],
                        'order' => $order,
                        'table' => $table,
                        'comment' => $index['comment'],
                    ];
                }
                return null;
            },
            $allindexesfromtable,
        ),
    );
    if (count($userrelatedcolumns) <= 0) {
        $nonmatching[$table] = $table;
        continue;
    }
    $userrelatedcolumns = array_map(
        function ($column) {
            return $column->name;
        },
        $userrelatedcolumns,
    );
    sort($userrelatedcolumns);
    $matching[$table] = $userrelatedcolumns;
    $userrelatedbykeys = array_keys($userrelatedbykeys);
    sort($userrelatedbykeys);
    $matchingbykeys[$table] = $userrelatedbykeys;
    $matchingcount[$table] = count($userrelatedcolumns);
    if (count($tablecompoundindexes) > 0) {
        // Keys from $compoundindexes include the table name and the list of fields. So keys are unique.
        $compoundindexes = array_merge($compoundindexes, $tablecompoundindexes);
    }
    foreach ($userrelatedcolumns as $column) {
        if (!isset($alluserrelatedcolumns[$column])) {
            $alluserrelatedcolumns[$column] = 0;
        }
        $alluserrelatedcolumns[$column]++;
        $alluserrelatedcolumnswithtable[$column][$table] = $table;
    }
}
ksort($matchingcount);
sort($nonmatching);
ksort($alluserrelatedcolumns);
foreach ($compoundindexestoignore as $key => $true) {
    unset($compoundindexes[$key]);
}
$indextable = array_column($compoundindexes, 'table');
array_multisort($indextable, SORT_ASC, $compoundindexes);
$uniquebykeyindexes = array_filter(
    $compoundindexes,
    function ($index) {
        return $index['order'] == 3;
    }
);
$uniquebynameindexes = array_filter(
    $compoundindexes,
    function ($index) {
        return $index['order'] == 2;
    }
);
$nonuniquebykeyindexes = array_filter(
    $compoundindexes,
    function ($index) {
        return $index['order'] == 1;
    }
);
$nonuniquebynameindexes = array_filter(
    $compoundindexes,
    function ($index) {
        return $index['order'] == 0;
    }
);
cli_writeln('... done!');
$log = new text_progress_trace();
$log->output('##### Tables without potential %user%-related fields #####', 1);
foreach ($nonmatching as $table) {
    $log->output($table, 2);
}
$log->output('##### Tables with potential %user%-related fields #####', 1);
$log->output(
    'NOTE: All tables with non-default user-related field names must appear into ' .
    '"userfieldnames" config.php setting.',
    2,
);
$log->output(
    'FORMAT: {number of user-related fields}: \'{table name}\' => [{list of fields}] ' .
    '// {list of fields that appear as foreign key to user.id on the XML definition.}',
    2,
);
arsort($matchingcount);
foreach ($matchingcount as $table => $numberofcolumns) {
    $log->output(
        sprintf(
            "%d: '%s' => ['%s'], // %s",
            $numberofcolumns,
            $table,
            implode("', '", $matching[$table]),
            implode(", ", $matchingbykeys[$table]),
        ),
        2,
    );
}
$log->output('##### List of user-related column names and number of appearances #####', 1);
arsort($alluserrelatedcolumns);
foreach ($alluserrelatedcolumns as $column => $appearances) {
    $log->output(
        sprintf(
            '%d: %s: %s',
            $appearances,
            $column,
            implode(',', $alluserrelatedcolumnswithtable[$column]),
        ),
        2,
    );
}
if (count($omittedtables) <= 0) {
    $log->output('INFO: There were no omitted tables: your database tables and XML definition are consistent.', 1);
} else {
    $log->output(sprintf('INFO: Omitted tables (no XML definition found for %d tables):', count($omittedtables)), 1);
    foreach ($omittedtables as $table) {
        $log->output($table, 2);
    }
    $log->output('');
    $log->output('Omitted tables can come for several reasons and they talk about your Moodle and database consistency:', 2);
    $log->output('1. Uninstalled plugins, with kept database tables.', 3);
    $log->output('2. Plugins without a proper install.xml definition.', 3);
    $log->output('3. Manually created database tables, or other reasons.', 3);
    $log->output('Recommendation: Double check these omitted tables to be sure they should exist and take the action ' .
        'that fits better.', 1);
}

$log->output('##### List of compound indexes #####', 1);
$log->output('      IMPORTANT: Unique indexes should appear into the default plugin settings, at least.', 1);
$log->output('            // Unique indexes with matching user-related field with foreign key to user.id.', 0);

/**
 * Printer helper function to list all indexes, ready for copy and paste into default_db_config.php file.
 *
 * @param array $indexes
 * @param text_progress_trace $log
 * @return void
 */
function tool_mergeusers_print_indexes(array $indexes, text_progress_trace $log): void {
    foreach ($indexes as $index) {
        $userfieldstring = implode("', '", $index['userfield']);
        if ($userfieldstring != '') {
            $userfieldstring = "'$userfieldstring'";
        }
        $otherfieldstring = implode("', '", $index['otherfields']);
        if ($otherfieldstring != '') {
            $otherfieldstring = "'$otherfieldstring'";
        }

        // We want to generate code that can be copy-pasted into plugin default configuration file default_db_config.php.
        $comment = trim($index['comment']);
        if ($comment != '') {
            $comment = PHP_EOL . '                // Index comment: ' . rtrim($comment, '.') . '.';
        }
        $log->output(
            sprintf(
                "            '%s' => [
                // For index '%s'.
                // Type of index: %s; type of matching: %s.%s
                'userfield' => [%s],
                'otherfields' => [%s],
            ],",
                $index['table'],
                $index['name'],
                $index['unique'] ? 'unique' : 'non-unique',
                $index['type'] == 'bykey' ? 'by foreign key' : 'by %user%-related column name',
                $comment,
                $userfieldstring,
                $otherfieldstring,
            ),
            0,
        );
    }
}

tool_mergeusers_print_indexes($uniquebykeyindexes, $log);
$log->output('', 1);
$log->output('', 1);
$log->output('            // Unique indexes with matching user-related field by name.', 0);
tool_mergeusers_print_indexes($uniquebynameindexes, $log);
$log->output('', 1);
$log->output('', 1);
$log->output('            // If we need to add non-unique indexes is because they are treated in Moodle as unique, actually.');
$log->output('            // Non-unique indexes with matching user-related field by foreign key.', 0);
tool_mergeusers_print_indexes($nonuniquebykeyindexes, $log);
$log->output('', 1);
$log->output('', 1);
$log->output('            // Non-unique indexes with matching user-related field by name.', 0);
tool_mergeusers_print_indexes($nonuniquebynameindexes, $log);
$log->finished();
cli_writeln('End!');
