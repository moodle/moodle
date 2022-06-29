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
 * Version information
 *
 * @package    tool
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/config.php';

global $CFG;

require_once $CFG->dirroot . '/lib/clilib.php';
require_once __DIR__ . '/autoload.php';
require_once($CFG->dirroot . '/'.$CFG->admin.'/tool/iomadmerge/lib.php');

/**
 *
 *
 * Lifecycle:
 * <ol>
 *   <li>Once: <code>$mut = new IomadMergeTool();</code></li>
 *   <li>N times: <code>$mut->merge($from, $to);</code> Passing two objects with at least
 *   two attributes ('id' and 'username') on each, this will merge the user $from into the
 *   user $to, so that the $from user will be empty of activity.</li>
 * </ol>
 *
 * @author Jordi Pujol-Ahulló
 */
class IomadMergeTool
{
    /**
     * @var array associative array showing the user-related fields per database table,
     * without the $CFG->prefix on each.
     */
    protected $userFieldsPerTable;

    /**
     * @var array string array with all known database table names to skip in analysis,
     * without the $CFG->prefix on each.
     */
    protected $tablesToSkip;

    /**
     * @var array string array with the current skipped tables with the $CFG->prefix on each.
     */
    protected $tablesSkipped;

    /**
     * @var array associative array with special cases for tables with compound indexes,
     * without the $CFG->prefix on each.
     */
    protected $tablesWithCompoundIndex;

    /**
     * @var string Database-specific SQL to get the list of database tables.
     */
    protected $sqlListTables;

    /**
     * @var array array with table names (without $CFG->prefix) and the list of field names
     * that are related to user.id. The key 'default' is the default for any non matching table name.
     */
    protected $userFieldNames;

    /**
     * @var tool_iomadmerge_logger logger for merging users.
     */
    protected $logger;

    /**
     * @var array associative array (tablename => classname) with the
     * TableMerger tools to process all database tables.
     */
    protected $tableMergers;

    /**
     * @var array list of table names processed by TableMerger's.
     */
    protected $tablesProcessedByTableMergers;

    /**
     * @var bool if true then never commit the transaction, used for testing.
     */
    protected $alwaysRollback;

    /**
     * @var bool if true then write out all sql, used for testing.
     */
    protected $debugdb;

    /**
     * Initializes
     * @global object $CFG
     * @param tool_iomadmerge_config $config local configuration.
     * @param tool_iomadmerge_logger $logger logger facility to save results of mergings.
     */
    public function __construct(tool_iomadmerge_config $config = null, tool_iomadmerge_logger $logger = null)
    {
        $this->logger = (is_null($logger)) ? new tool_iomadmerge_logger() : $logger;
        $config = (is_null($config)) ? tool_iomadmerge_config::instance() : $config;

        $this->checkTransactionSupport();

        // These are tables we don't want to modify due to logging or security reasons.
        // We flip key<-->value to accelerate lookups.
        $this->tablesToSkip = array_flip($config->exceptions);
        $excluded = explode(',', get_config('tool_iomadmerge', 'excluded_exceptions'));
        $excluded = array_flip($excluded);
        if (!isset($excluded['none'])) {
            foreach ($excluded as $exclude => $nonused) {
                unset($this->tablesToSkip[$exclude]);
            }
        }

        // These are special cases, corresponding to tables with compound indexes that need a special treatment.
        $this->tablesWithCompoundIndex = $config->compoundindexes;

        // Initializes user-related field names.
        $this->userFieldNames = $config->userfieldnames;

        // Load available TableMerger tools.
        $tableMergers = array();
        $tablesProcessedByTableMergers = array();
        foreach ($config->tablemergers as $tableName => $class) {
            $tm = new $class();
            // ensure any provided class is a class of TableMerger
            if (!$tm instanceof TableMerger) {
                // aborts execution by showing an error.
                if (CLI_SCRIPT) {
                    cli_error('Error: ' . __METHOD__ . ':: ' . get_string('notablemergerclass', 'tool_iomadmerge',
                                    $class));
                } else {
                    print_error('notablemergerclass', 'tool_iomadmerge',
                            new moodle_url('/admin/tool/iomadmerge/index.php'), $class);
                }
            }
            // Append any additional table to skip.
            $tablesProcessedByTableMergers = array_merge($tablesProcessedByTableMergers, $tm->getTablesToSkip());
            $tableMergers[$tableName] = $tm;
        }
        $this->tableMergers = $tableMergers;
        $this->tablesProcessedByTableMergers = array_flip($tablesProcessedByTableMergers);

        $this->alwaysRollback = !empty($config->alwaysRollback);
        $this->debugdb = !empty($config->debugdb);

        // Initializes the list of fields and tables to check in the current database, given the local configuration.
        $this->init();
    }

    /**
     * Merges two users into one. User-related data records from user id $fromid are merged into the
     * user with id $toid.
     * @global object $CFG
     * @global moodle_database $DB
     * @param int $toid The user inheriting the data
     * @param int $fromid The user being replaced
     * @return array An array(bool, array, int) having the following cases: if array(true, log, id)
     * users' merging was successful and log contains all actions done; if array(false, errors, id)
     * means users' merging was aborted and errors contain the list of errors.
     * The last id is the log id of the merging action for later visual revision.
     */
    public function merge($toid, $fromid)
    {
        list($success, $log) = $this->_merge($toid, $fromid);

        $eventpath = "\\tool_iomadmerge\\event\\";
        $eventpath .= ($success) ? "user_merged_success" : "user_merged_failure";

        $event = $eventpath::create(array(
            'context' => \context_system::instance(),
            'other' => array(
                'usersinvolved' => array(
                    'toid' => $toid,
                    'fromid' => $fromid,
                ),
                'log' => $log,
            ),
        ));
        $event->trigger();
        $logid = $this->logger->log($toid, $fromid, $success, $log);
        return array($success, $log, $logid);
    }

    /**
     * Real method that performs the merging action.
     * @global object $CFG
     * @global moodle_database $DB
     * @param int $toid The user inheriting the data
     * @param int $fromid The user being replaced
     * @return array An array(bool, array) having the following cases: if array(true, log)
     * users' merging was successful and log contains all actions done; if array(false, errors)
     * means users' merging was aborted and errors contain the list of errors.
     */
    private function _merge($toid, $fromid)
    {
        global $DB;

        // initial checks.
        // are they the same?
        if ($fromid == $toid) {
            // yes. do nothing.
            return array(false, array(get_string('errorsameuser', 'tool_iomadmerge')));
        }

        // ok, now we have to work;-)
        // first of all... initialization!
        $errorMessages = array();
        $actionLog = array();

        if ($this->debugdb) {
            $DB->set_debug(true);
        }

        $startTime = time();
        $startTimeString = get_string('starttime', 'tool_iomadmerge', userdate($startTime));
        $actionLog[] = $startTimeString;

        $transaction = $DB->start_delegated_transaction();

        try {
            // processing each table name
            $data = array(
                'toid' => $toid,
                'fromid' => $fromid,
            );
            foreach ($this->userFieldsPerTable as $tableName => $userFields) {
                $data['tableName'] = $tableName;
                $data['userFields'] = $userFields;
                if (isset($this->tablesWithCompoundIndex[$tableName])) {
                    $data['compoundIndex'] = $this->tablesWithCompoundIndex[$tableName];
                } else {
                    unset($data['compoundIndex']);
                }

                $tableMerger = (isset($this->tableMergers[$tableName])) ?
                        $this->tableMergers[$tableName] :
                        $this->tableMergers['default'];

                // process the given $tableName.
                $tableMerger->merge($data, $actionLog, $errorMessages);
            }

            $this->updateGrades($toid, $fromid);
            $this->reaggregateCompletions($toid);
        } catch (Exception $e) {
            $errorMessages[] = nl2br("Exception thrown when merging: '" . $e->getMessage() . '".' .
                    html_writer::empty_tag('br') . $DB->get_last_error() . html_writer::empty_tag('br') .
                    'Trace:' . html_writer::empty_tag('br') .
                    $e->getTraceAsString() . html_writer::empty_tag('br'));
        }

        if ($this->debugdb) {
            $DB->set_debug(false);
        }

        if ($this->alwaysRollback) {
            $transaction->rollback(new Exception('alwaysRollback option is set so rolling back transaction'));
        }

        // concludes with true if no error
        if (empty($errorMessages)) {
            $transaction->allow_commit();

            // add skipped tables as first action in log
            $skippedTables = array();
            if (!empty($this->tablesSkipped)) {
                $skippedTables[] = get_string('tableskipped', 'tool_iomadmerge', implode(", ", $this->tablesSkipped));
            }

            $finishTime = time();
            $actionLog[] = get_string('finishtime', 'tool_iomadmerge', userdate($finishTime));
            $actionLog[] = get_string('timetaken', 'tool_iomadmerge', $finishTime - $startTime);

            return array(true, array_merge($skippedTables, $actionLog));
        } else {
            try {
                //thrown controlled exception.
                $transaction->rollback(new Exception(__METHOD__ . ':: Rolling back transcation.'));
            } catch (Exception $e) { /* do nothing, just for correctness */
            }
        }

        $finishTime = time();
        $errorMessages[] = $startTimeString;
        $errorMessages[] = get_string('timetaken', 'tool_iomadmerge', $finishTime - $startTime);

        // concludes with an array of error messages otherwise.
        return array(false, $errorMessages);
    }

    // ****************** INTERNAL UTILITY METHODS ***********************************************

    /**
     * Initializes the list of database table names and user-related fields for each table.
     * @global object $CFG
     * @global moodle_database $DB
     */
    private function init()
    {
        global $DB;

        $userFieldsPerTable = array();

        // Name of tables comes without db prefix.
        $tableNames = $DB->get_tables(false);

        foreach ($tableNames as $tableName) {

            if (!trim($tableName)) {
                // This section should never be executed due to the way Moodle returns its resultsets.
                // Skipping due to blank table name.
                continue;
            } else {
                // Table specified to be excluded.
                if (isset($this->tablesToSkip[$tableName])) {
                    $this->tablesSkipped[$tableName] = $tableName;
                    continue;
                }
                // Table specified to be processed additionally by a TableMerger.
                if (isset($this->tablesProcessedByTableMergers[$tableName])) {
                    continue;
                }
            }

            // detect available user-related fields among database tables.
            $userFields = (isset($this->userFieldNames[$tableName])) ?
                    $this->userFieldNames[$tableName] :
                    $this->userFieldNames['default'];

            $arrayUserFields = array_flip($userFields);
            $currentFields = $this->getCurrentUserFieldNames($tableName, $arrayUserFields);

            if ($currentFields !== false) {
                $userFieldsPerTable[$tableName] = $currentFields;
            }
        }

        $this->userFieldsPerTable = $userFieldsPerTable;

        $existingCompoundIndexes = $this->tablesWithCompoundIndex;
        foreach ($this->tablesWithCompoundIndex as $tableName => $columns) {
            $chosenColumns = array_merge($columns['userfield'], $columns['otherfields']);

            $columnNames = array();
            foreach ($chosenColumns as $columnName) {
                $columnNames[$columnName] = 0;
            }

            $tableColumns = $DB->get_columns($tableName, false);

            foreach ($tableColumns as $column) {
                if (isset($columnNames[$column->name])) {
                    $columnNames[$column->name] = 1;
                }
            }

            // If we find some compound index with missing columns,
            // it is that loaded configuration does not corresponds to current database scheme
            // and this index does not apply.
            $found = array_sum($columnNames);
            if (sizeof($columnNames) !== $found) {
                unset($existingCompoundIndexes[$tableName]);
            }
        }

        // update the attribute with the current existing compound indexes per table.
        $this->tablesWithCompoundIndex = $existingCompoundIndexes;
    }

    /**
     * Checks whether the current database supports transactions.
     * If settings of this plugin are set up to allow only transactions,
     * this method aborts the execution. Otherwise, this method will return
     * true or false whether the current database supports transactions or not,
     * respectively.
     * @return bool true if database transactions are supported. false otherwise.
     */
    public function checkTransactionSupport()
    {
        global $CFG;

        $transactionsSupported = tool_iomadmerge_transactionssupported();
        $forceOnlyTransactions = get_config('tool_iomadmerge', 'transactions_only');

        if (!$transactionsSupported && $forceOnlyTransactions) {
            if (CLI_SCRIPT) {
                cli_error('Error: ' . __METHOD__ . ':: ' . get_string('errortransactionsonly', 'tool_iomadmerge',
                                $CFG->dbtype));
            } else {
                print_error('errortransactionsonly', 'tool_iomadmerge',
                        new moodle_url('/admin/tool/iomadmerge/index.php'), $CFG->dbtype);
            }
        }

        return $transactionsSupported;
    }

    /**
     * Gets the matching fields on the given $tableName against the given $userFields.
     * @param string $tableName database table name to analyse, with $CFG->prefix.
     * @param string $userFields candidate user fields to check.
     * @return bool | array false if no matching field name;
     * string array with matching field names otherwise.
     */
    private function getCurrentUserFieldNames($tableName, $userFields)
    {
        global $DB;
        $columns = $DB->get_columns($tableName,false);
        $usercolumns = [];
        foreach($columns as $column) {
            if (isset($userFields[$column->name])) {
                $usercolumns[$column->name] = $column->name;
            }
        }
        return $usercolumns;
    }

    /**
     * Update all of the target user's grades.
     * @param int $toid User id
     */
    private function updateGrades($toid, $fromid) {
        global $DB, $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        $sql = "SELECT DISTINCT gi.id, gi.iteminstance, gi.itemmodule, gi.courseid
                FROM {grade_grades} gg
                INNER JOIN {grade_items} gi on gg.itemid = gi.id
                WHERE itemtype = 'mod' AND (gg.userid = :toid OR gg.userid = :fromid)";

        $iteminstances = $DB->get_records_sql($sql, array('toid' => $toid, 'fromid' => $fromid));

        foreach ($iteminstances as $iteminstance) {
            if (!$activity = $DB->get_record($iteminstance->itemmodule, array('id' => $iteminstance->iteminstance))) {
                throw new \Exception("Can not find $iteminstance->itemmodule activity with id $iteminstance->iteminstance");
            }
            if (!$cm = get_coursemodule_from_instance($iteminstance->itemmodule, $activity->id, $iteminstance->courseid)) {
                throw new \Exception('Can not find course module');
            }

            $activity->modname    = $iteminstance->itemmodule;
            $activity->cmidnumber = $cm->idnumber;

            grade_update_mod_grades($activity, $toid);
        }
    }

    private function reaggregateCompletions($toid) {
        global $DB;

        $now = time();
        $DB->execute(
                'UPDATE {course_completions} set reaggregate = :now where userid = :toid and (timecompleted is null or timecompleted = 0)',
                ['now' => $now, 'toid' => $toid]
        );
    }
}
