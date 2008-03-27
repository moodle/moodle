<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Unit tests for (some of) ../../backup/backuplib.php.
 *
 * @author nicolasconnault@gmail.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/backup/backuplib.php');
Mock::generate('ADODB_mysql');
Mock::generate('ADORecordSet_mysql');

class backuplib_test extends UnitTestCase {
    var $real_db;
    var $real_dataroot;
    var $rs;   
    var $firstcolumn;
    var $db;
    var $testfiles = array();
    var $userbasedir;

    /* The mocking of the $rs and $db objects is problematic. Somehow it's affecting other unit test suites. 
       I'm commenting this off until this is resolved -- nicolasconnault@gmail.com --

    function setUp() {
        global $db, $CFG;
        $this->real_db = fullclone($db);
        $db = new MockADODB_mysql();
        $this->rs = new MockADORecordSet_mysql();
        $this->rs->EOF = false;
        $this->firstcolumn = new stdClass();
        $this->firstcolumn->name = 'id';
        
        // Override dataroot: we don't want to test with live data
        $this->real_dataroot = fullclone($CFG->dataroot);
        $CFG->dataroot .= '/unittests';
        $this->userbasedir = $CFG->dataroot.'/user';

        // Create some sample files in this temporary directory
        mkdir($CFG->dataroot);
        mkdir($this->userbasedir);
        
        $this->testfiles = array('0/1','0/3','1000/1043','457498000/457498167');
        foreach ($this->testfiles as $file) {
            $parts = explode('/', $file);

            if (!file_exists("$this->userbasedir/{$parts[0]}")) {
                mkdir("$this->userbasedir/{$parts[0]}");
            }
            mkdir("$this->userbasedir/$file");
            $handle = fopen("$this->userbasedir/$file/f1.gif", 'w+b');
            fclose($handle);
        } 
    }

    function tearDown() {
        global $CFG, $db;
        
        if (!is_null($this->real_dataroot) && $this->real_dataroot != $CFG->dataroot) {
            remove_dir($CFG->dataroot);
        }
        $db = $this->real_db;
        $CFG->dataroot = $this->real_dataroot;
    }

    function test_backup_copy_user_files() {
        global $CFG, $db;
        $preferences = new stdClass();
        $preferences->backup_unique_code = time();
        
        $db->setReturnValue('Execute', $this->rs);
        $this->rs->setReturnValue('RecordCount', 1);
        $this->rs->fields = array(1);

        // Perform the backup
        backup_copy_user_files($preferences);
        
        // Check for the existence of the backup file
        $backupfile = "$CFG->dataroot/temp/backup/$preferences->backup_unique_code/user_files";
        $this->assertTrue(file_exists($backupfile));

        // Check for the existence of the user files in the backup file
        foreach ($this->testfiles as $file) {
            $parts = explode('/', $file);
            $section = $parts[0];
            $userid = $parts[1]; 
            $userimage = "$CFG->dataroot/temp/backup/$preferences->backup_unique_code/user_files/$section/$userid/f1.gif";
            $this->assertTrue(file_exists($userimage));
        }
    }

    // This is a moodlelib method but it is used in backuplib, so it is tested here in that context, with typical backup data.
    function test_get_user_directories() {
        global $CFG;
        $dirlist = get_user_directories(); 
        $this->assertEqual(4, count($dirlist));

        foreach ($this->testfiles as $file) {
            $parts = explode('/', $file);
            $section = $parts[0];
            $userid = $parts[1];

            $this->assertEqual($file, $dirlist[$userid]['userfolder']);
            $this->assertEqual($this->userbasedir, $dirlist[$userid]['basedir']);
        }
    } 
    */
}

?>
