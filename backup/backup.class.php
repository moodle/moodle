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
 * @package moodlecore
 * @subpackage backup
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract class defining common stuff to be used by the backup stuff
 *
 * This class defines various constants and methods that will be used
 * by different classes, all related with the backup process. Just provides
 * the top hierarchy of the backup controller/worker stuff.
 *
 * TODO: Finish phpdocs
 */
abstract class backup implements checksumable {

    // Backup type
    const TYPE_1ACTIVITY = 'activity';
    const TYPE_1SECTION  = 'section';
    const TYPE_1COURSE   = 'course';

    // Backup format
    const FORMAT_MOODLE  = 'moodle2';
    const FORMAT_MOODLE1 = 'moodle1';
    const FORMAT_IMSCC1  = 'imscc1';
    const FORMAT_IMSCC11 = 'imscc11';
    const FORMAT_UNKNOWN = 'unknown';

    // Interactive
    const INTERACTIVE_YES = true;
    const INTERACTIVE_NO  = false;

    // Predefined modes (purposes) of the backup
    const MODE_GENERAL   = 10;

    /**
     * This is used for importing courses, and for duplicating activities.
     *
     * This mode will ensure that files are not included in the backup generation, and
     * during a restore they are copied from the existing file record.
     */
    const MODE_IMPORT    = 20;
    const MODE_HUB       = 30;

    /**
     * This mode is intended for duplicating courses and cases where the backup target is
     * within the same site.
     *
     * This mode will ensure that files are not included in the backup generation, and
     * during a restore they are copied from the existing file record.
     *
     * For creating a backup for archival purposes or greater longevity, use MODE_GENERAL.
     */
    const MODE_SAMESITE  = 40;
    const MODE_AUTOMATED = 50;
    const MODE_CONVERTED = 60;

    /**
     * This mode is for asynchronous backups.
     * These backups will run via adhoc scheduled tasks.
     */
    const MODE_ASYNC = 70;

    // Target (new/existing/current/adding/deleting)
    const TARGET_CURRENT_DELETING = 0;
    const TARGET_CURRENT_ADDING   = 1;
    const TARGET_NEW_COURSE       = 2;
    const TARGET_EXISTING_DELETING= 3;
    const TARGET_EXISTING_ADDING  = 4;

    // Execution mode
    const EXECUTION_INMEDIATE = 1;
    const EXECUTION_DELAYED   = 2;

    // Status of the backup_controller
    const STATUS_CREATED     = 100;
    const STATUS_REQUIRE_CONV= 200;
    const STATUS_PLANNED     = 300;
    const STATUS_CONFIGURED  = 400;
    const STATUS_SETTING_UI  = 500;
    const STATUS_NEED_PRECHECK=600;
    const STATUS_AWAITING    = 700;
    const STATUS_EXECUTING   = 800;
    const STATUS_FINISHED_ERR= 900;
    const STATUS_FINISHED_OK =1000;

    // Logging levels
    const LOG_DEBUG   = 50;
    const LOG_INFO    = 40;
    const LOG_WARNING = 30;
    const LOG_ERROR   = 20;
    const LOG_NONE    = 10;

    // Some constants used to identify some helpfull processor variables
    // (using negative numbers to avoid any collision posibility
    // To be used when defining backup structures
    const VAR_COURSEID   = -1;  // To reference id of course in a processor
    const VAR_SECTIONID  = -11; // To reference id of section in a processor
    const VAR_ACTIVITYID = -21; // To reference id of activity in a processor
    const VAR_MODID      = -31; // To reference id of course_module in a processor
    const VAR_MODNAME    = -41; // To reference name of module in a processor
    const VAR_BLOCKID    = -51; // To reference id of block in a processor
    const VAR_BLOCKNAME  = -61; // To reference name of block in a processor
    const VAR_CONTEXTID  = -71; // To reference context id in a processor
    const VAR_PARENTID   = -81; // To reference the first parent->id in a backup structure

    // Used internally by the backup process
    const VAR_BACKUPID   = -1001; // To reference the backupid being processed
    const VAR_BASEPATH   = -1011; // To reference the dir where the file is generated

    // Type of operation
    const OPERATION_BACKUP  ='backup'; // We are performing one backup
    const OPERATION_RESTORE ='restore';// We are performing one restore

    // Options for "Include enrolment methods" restore setting.
    const ENROL_NEVER     = 0;
    const ENROL_WITHUSERS = 1;
    const ENROL_ALWAYS    = 2;

    // Version and release (to keep CFG->backup_version (and release) updated automatically).
    /**
     * Usually same than major release version, this is used to mark important
     * point is backup when some behavior/approach channged, in order to allow
     * conditional coding based on it.
     */
    const VERSION = 2018120300;
    /**
     * Usually same than major release zero version, mainly for informative/historic purposes.
     */
    const RELEASE = '3.7';

    /**
     * Cipher to be used in backup and restore operations.
     */
    const CIPHER = 'aes-256-cbc';
    /**
     * Bytes enforced for key, using the cypher above. Restrictive? Yes, but better than unsafe lengths
     */
    const CIPHERKEYLEN = 32;
}

/*
 * Exception class used by all the @backup stuff
 */
abstract class backup_exception extends moodle_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, 'error', '', $a, $debuginfo);
    }
}
