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
 * Utility file.
 *
 * The effort of all given authors below gives you this current version of the file.
 *
 * @package   tool_mergeusers
 * @author    Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author    Mike Holzer
 * @author    Forrest Gaston
 * @author    Juan Pablo Torres Herrera
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>,  SREd, Universitat Rovira i Virgili
 * @author    John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @copyright University of Wisconsin - Madison
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local;

use coding_exception;
use context_system;
use dml_exception;
use dml_transaction_exception;
use Exception;
use html_writer;
use moodle_exception;
use moodle_url;
use ReflectionException;
use tool_mergeusers\event\user_merged_failure;
use tool_mergeusers\event\user_merged_success;
use tool_mergeusers\local\merger\table_merger;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/clilib.php');

/**
 * Tool to merge a pair of users.
 *
 * Lifecycle:
 * <ol>
 *   <li>Once: <code>$mut = new merger();</code></li>
 *   <li>N times: <code>$mut->merge($from, $to);</code> Passing two objects with at least
 *   two attributes ('id' and 'username') on each, this will merge the user $from into the
 *   user $to, so that the $from user will be empty of activity.</li>
 * </ol>
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class user_merger {
    /**
     * @var array associative array showing the user-related fields per database table,
     * without the $CFG->prefix on each.
     */
    protected $userfieldspertable;

    /**
     * @var array string array with all known database table names to skip in analysis,
     * without the $CFG->prefix on each.
     */
    protected $tablestoskip;

    /**
     * @var array string array with the current skipped tables with the $CFG->prefix on each.
     */
    protected $tablesskipped;

    /**
     * @var array associative array with special cases for tables with compound indexes,
     * without the $CFG->prefix on each.
     */
    protected $tableswithcompoundindex;

    /**
     * @var array array with table names (without $CFG->prefix) and the list of field names
     * that are related to user.id. The key 'default' is the default for any non matching table name.
     */
    protected $userfieldnames;

    /**
     * @var logger logger for merging users.
     */
    protected $logger;


    /**
     * @var array associative array (tablename => classname) with the
     * table_merger tools to process all database tables.
     */
    protected $tablemergers;

    /**
     * @var array list of table names processed by table_merger's.
     */
    protected $tablesprocessedbytablemergers;

    /**
     * @var bool if true then never commit the transaction, used for testing.
     */
    protected $alwaysrollback;

    /**
     * @var bool if true then write out all sql, used for testing.
     */
    protected $debugdb;

    /**
     * Initializes the tool to merge users.
     *
     * @param config|null $config local configuration.
     * @param logger|null $logger logger facility to save results of merges.
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception when the merger for a given table is not an instance of table_merger
     */
    public function __construct(?config $config = null, ?logger $logger = null) {
        $this->logger = (is_null($logger)) ? new logger() : $logger;
        $config = (is_null($config)) ? config::instance() : $config;

        $this->check_transaction_support();

        // These are tables we don't want to modify due to logging or security reasons.
        // We flip key<-->value to accelerate lookups.
        $this->tablestoskip = array_flip($config->exceptions);
        $excluded = explode(',', get_config('tool_mergeusers', 'excluded_exceptions'));
        $excluded = array_flip($excluded);
        if (!isset($excluded['none'])) {
            foreach ($excluded as $exclude => $nonused) {
                unset($this->tablestoskip[$exclude]);
            }
        }

        // These are special cases, corresponding to tables with compound indexes that need a special treatment.
        $this->tableswithcompoundindex = $config->compoundindexes;

        // Initializes user-related field names.
        $this->userfieldnames = $config->userfieldnames;

        // Load available table_merger tools.
        $tablemergers = [];
        $tablesprocessedbytablemergers = [];
        foreach ($config->tablemergers as $tablename => $class) {
            $tablemerger = new $class();
            // Ensure any provided class is a class of table_merger.
            if (!$tablemerger instanceof table_merger) {
                // Aborts execution by showing an error.
                if (CLI_SCRIPT) {
                    cli_error('Error: ' . __METHOD__ . ':: ' . get_string(
                        'notablemergerclass',
                        'tool_mergeusers',
                        $class
                    ));
                } else {
                    throw new moodle_exception(
                        'notablemergerclass',
                        'tool_mergeusers',
                        new moodle_url('/admin/tool/mergeusers/index.php'),
                        $class,
                    );
                }
            }
            // Append any additional table to skip.
            $tablesprocessedbytablemergers = array_merge($tablesprocessedbytablemergers, $tablemerger->get_tables_to_skip());
            $tablemergers[$tablename] = $tablemerger;
        }
        $this->tablemergers = $tablemergers;
        $this->tablesprocessedbytablemergers = array_flip($tablesprocessedbytablemergers);

        $this->alwaysrollback = $config->alwaysrollback;
        $this->debugdb = $config->debugdb;

        // Initializes the list of fields and tables to check in the current database, given the local configuration.
        $this->init();
    }

    /**
     * Merges two users into one. User-related data records from user id $fromid are merged into the
     * user with id $toid.
     *
     * @param int $toid The user inheriting the data
     * @param int $fromid The user being replaced
     * @return array An array(bool, array, int) having the following cases: if array(true, log, id)
     * users' merging was successful and log contains all actions done; if array(false, errors, id)
     * means users' merging was aborted and errors contain the list of errors.
     * The last id is the log id of the merging action for later visual revision.
     * @throws dml_exception
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function merge(int $toid, int $fromid): array {
        [$success, $logs] = $this->merge_users($toid, $fromid);

        if ($success) {
            $eventname = user_merged_success::class;
        } else {
            $eventname = user_merged_failure::class;
        }

        $logid = $this->logger->log($toid, $fromid, $success, $logs);

        $event = $eventname::create([
            'context' => context_system::instance(),
            'other' => [
                'usersinvolved' => [
                    'toid' => $toid,
                    'fromid' => $fromid,
                ],
                'logid' => $logid,
                'log' => $logs,
            ],
        ]);
        $event->trigger();

        return [$success, $logs, $logid];
    }

    /**
     * Real method that performs the merging action.
     *
     * @param int $toid The user inheriting the data
     * @param int $fromid The user being replaced
     * @return array An array(bool, array) having the following cases: if array(true, log)
     * users' merging was successful and log contains all actions done; if array(false, errors)
     * means users' merging was aborted and errors contain the list of errors.
     * @throws coding_exception
     * @throws dml_transaction_exception
     */
    private function merge_users(int $toid, int $fromid): array {
        global $DB;

        // Initial checks.
        // Are they the same?
        if ($fromid == $toid) {
            // Do nothing.
            return [false, [get_string('errorsameuser', 'tool_mergeusers')]];
        }

        $someuserdoesnotexists = array_filter(
            array_map(
                function ($userid) use ($DB) {
                    if ($DB->record_exists('user', ['id' => $userid, 'deleted' => 0])) {
                        return null;
                    }
                    return get_string('invaliduser', 'tool_mergeusers', ['field' => 'id', 'value' => $userid]);
                },
                [$toid, $fromid],
            ),
        );

        // Abort merging users when at least one of them is already deleted.
        // We need to enforce this condition here.
        if (count($someuserdoesnotexists) > 0) {
            return [false, $someuserdoesnotexists];
        }

        // Ok, now we have to work ;-)
        // First of all... initialization!
        $errors = [];
        $logs = [];

        if ($this->debugdb) {
            $DB->set_debug(true);
        }

        $starttime = time();
        $starttimestring = get_string('starttime', 'tool_mergeusers', userdate($starttime));
        $logs[] = $starttimestring;

        $transaction = $DB->start_delegated_transaction();

        try {
            // Processing each table name.
            $data = [
                'toid' => $toid,
                'fromid' => $fromid,
            ];
            foreach ($this->userfieldspertable as $tablename => $userfields) {
                $data['tableName'] = $tablename;
                $data['userFields'] = $userfields;
                if (isset($this->tableswithcompoundindex[$tablename])) {
                    $data['compoundIndex'] = $this->tableswithcompoundindex[$tablename];
                } else {
                    unset($data['compoundIndex']);
                }

                $tablemerger = (isset($this->tablemergers[$tablename])) ?
                        $this->tablemergers[$tablename] :
                        $this->tablemergers['default'];

                // Process the given table name.
                $tablemerger->merge($data, $logs, $errors);
            }

            \core\di::get(\core\hook\manager::class)->dispatch(
                new \tool_mergeusers\hook\after_merged_all_tables($toid, $fromid, $logs, $errors),
            );
        } catch (Exception $e) {
            $errors[] = nl2br("Exception thrown when merging: '" . $e->getMessage() . '".' .
                    html_writer::empty_tag('br') . $DB->get_last_error() . html_writer::empty_tag('br') .
                    'Trace:' . html_writer::empty_tag('br') .
                    $e->getTraceAsString() . html_writer::empty_tag('br'));
        }

        if ($this->debugdb) {
            $DB->set_debug(false);
        }

        if ($this->alwaysrollback) {
            $transaction->rollback(new Exception('alwaysrollback option is set so rolling back transaction'));
        }

        // Concludes with true if no error.
        if (empty($errors)) {
            $transaction->allow_commit();

            // Add skipped tables as first action in log.
            $skippedtables = [];
            if (!empty($this->tablesskipped)) {
                $skippedtables[] = get_string('tableskipped', 'tool_mergeusers', implode(", ", $this->tablesskipped));
            }

            $finishtime = time();
            $logs[] = get_string('finishtime', 'tool_mergeusers', userdate($finishtime));
            $logs[] = get_string('timetaken', 'tool_mergeusers', $finishtime - $starttime);

            return [true, array_merge($skippedtables, $logs)];
        } else {
            try {
                // Thrown controlled exception.
                $transaction->rollback(new Exception(__METHOD__ . ':: Rolling back transcation.'));
                // @codingStandardsIgnoreStart Squiz.Commenting.EmptyCatchComment
            } catch (Exception $e) {
                /* Do nothing, just for correctness */
                // @codingStandardsIgnoreEnd
            }
        }

        $finishtime = time();
        $errors[] = $starttimestring;
        $errors[] = get_string('timetaken', 'tool_mergeusers', $finishtime - $starttime);

        // Concludes with an array of error messages otherwise.
        return [false, $errors];
    }

    /**
     * Initializes the list of database table names and user-related fields for each table.
     */
    private function init(): void {
        global $DB;

        $userfieldspertable = [];

        // Name of tables comes without db prefix.
        $tablenames = $DB->get_tables(false);

        foreach ($tablenames as $tablename) {
            if (!trim($tablename)) {
                // This section should never be executed due to the way Moodle returns its resultsets.
                // Skipping due to blank table name.
                continue;
            } else {
                // Table specified to be excluded.
                if (isset($this->tablestoskip[$tablename])) {
                    $this->tablesskipped[$tablename] = $tablename;
                    continue;
                }
                // Table specified to be processed additionally by a table_merger.
                if (isset($this->tablesprocessedbytablemergers[$tablename])) {
                    continue;
                }
            }

            // Detect available user-related fields among database tables.
            $userfields = (isset($this->userfieldnames[$tablename])) ?
                    $this->userfieldnames[$tablename] :
                    $this->userfieldnames['default'];

            $arrayuserfields = array_flip($userfields);
            $currentfields = $this->get_current_user_field_names($tablename, $arrayuserfields);

            if ($currentfields !== false) {
                $userfieldspertable[$tablename] = $currentfields;
            }
        }

        $this->userfieldspertable = $userfieldspertable;

        $existingcompoundindexes = $this->tableswithcompoundindex;
        foreach ($this->tableswithcompoundindex as $tablename => $columns) {
            $chosencolumns = array_merge($columns['userfield'], $columns['otherfields']);

            $columnnames = [];
            foreach ($chosencolumns as $columnname) {
                $columnnames[$columnname] = 0;
            }

            $tablecolumns = $DB->get_columns($tablename, false);

            foreach ($tablecolumns as $column) {
                if (isset($columnnames[$column->name])) {
                    $columnnames[$column->name] = 1;
                }
            }

            // Remove compound index when loaded configuration does not correspond to current database scheme.
            $found = array_sum($columnnames);
            if (count($columnnames) !== $found) {
                unset($existingcompoundindexes[$tablename]);
            }
        }

        // Update the attribute with the current existing compound indexes per table.
        $this->tableswithcompoundindex = $existingcompoundindexes;
    }

    /**
     * Checks whether the current database supports transactions.
     * If settings of this plugin are set up to allow only transactions,
     * this method aborts the execution. Otherwise, this method will return
     * true or false whether the current database supports transactions or not,
     * respectively.
     *
     * @return bool true if database transactions are supported. false otherwise.
     * @throws moodle_exception when the current db instance does not support transactions
     * @throws ReflectionException
     * and the plugin settings prevents merging users under this case.
     */
    public function check_transaction_support(): bool {
        global $CFG;

        $transactionsaresupported = database_transactions::are_supported();
        $forceworkingonlywithtransactions = get_config('tool_mergeusers', 'transactions_only');

        if (!$transactionsaresupported && $forceworkingonlywithtransactions) {
            if (CLI_SCRIPT) {
                cli_error('Error: ' . __METHOD__ . ':: ' . get_string(
                    'errortransactionsonly',
                    'tool_mergeusers',
                    $CFG->dbtype
                ));
            } else {
                throw new moodle_exception(
                    'errortransactionsonly',
                    'tool_mergeusers',
                    new moodle_url('/admin/tool/mergeusers/index.php'),
                    $CFG->dbtype,
                );
            }
        }

        return $transactionsaresupported;
    }

    /**
     * Gets the matching fields on the given $tableName against the given $userFields.
     *
     * @param string $tablename database table name to analyse, with $CFG->prefix.
     * @param array $userfields candidate user fields to check.
     * @return array table columns that correspond to user.id field.
     */
    private function get_current_user_field_names(string $tablename, array $userfields): array {
        global $DB;
        $columns = $DB->get_columns($tablename, false);
        $usercolumns = [];
        foreach ($columns as $column) {
            if (isset($userfields[$column->name])) {
                $usercolumns[$column->name] = $column->name;
            }
        }
        return $usercolumns;
    }
}
