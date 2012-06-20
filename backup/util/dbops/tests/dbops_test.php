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
 * @package    core_backup
 * @category   phpunit
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Restore dbops tests (all).
 */
class restore_dbops_testcase extends advanced_testcase {

    /**
     * Verify the xxx_ids_cached (in-memory backup_ids cache) stuff works as expected.
     *
     * Note that those private implementations are tested here by using the public
     * backup_ids API and later performing low-level tests.
     */
    public function test_backup_ids_cached() {
        global $DB;
        $dbman = $DB->get_manager(); // We are going to use database_manager services.

        $this->resetAfterTest(true); // Playing with temp tables, better reset once finished.

        // Some variables and objects for testing.
        $restoreid = 'testrestoreid';

        $mapping = new stdClass();
        $mapping->itemname = 'user';
        $mapping->itemid = 1;
        $mapping->newitemid = 2;
        $mapping->parentitemid = 3;
        $mapping->info = 'info';

        // Create the backup_ids temp tables used by restore.
        restore_controller_dbops::create_restore_temp_tables($restoreid);

        // Send one mapping using the public api with defaults.
        restore_dbops::set_backup_ids_record($restoreid, $mapping->itemname, $mapping->itemid);
        // Get that mapping and verify everything is returned as expected.
        $result = restore_dbops::get_backup_ids_record($restoreid, $mapping->itemname, $mapping->itemid);
        $this->assertSame($mapping->itemname, $result->itemname);
        $this->assertSame($mapping->itemid, $result->itemid);
        $this->assertSame(0, $result->newitemid);
        $this->assertSame(null, $result->parentitemid);
        $this->assertSame(null, $result->info);

        // Drop the backup_xxx_temp temptables manually, so memory cache won't be invalidated.
        $dbman->drop_table(new xmldb_table('backup_ids_temp'));
        $dbman->drop_table(new xmldb_table('backup_files_temp'));

        // Verify the mapping continues returning the same info,
        // now from cache (the table does not exist).
        $result = restore_dbops::get_backup_ids_record($restoreid, $mapping->itemname, $mapping->itemid);
        $this->assertSame($mapping->itemname, $result->itemname);
        $this->assertSame($mapping->itemid, $result->itemid);
        $this->assertSame(0, $result->newitemid);
        $this->assertSame(null, $result->parentitemid);
        $this->assertSame(null, $result->info);

        // Recreate the temp table, just to drop it using the restore API in
        // order to check that, then, the cache becomes invalid for the same request.
        restore_controller_dbops::create_restore_temp_tables($restoreid);
        restore_controller_dbops::drop_restore_temp_tables($restoreid);

        // No cached info anymore, so the mapping request will arrive to
        // DB leading to error (temp table does not exist).
        try {
            $result = restore_dbops::get_backup_ids_record($restoreid, $mapping->itemname, $mapping->itemid);
            $this->fail('Expecting an exception, none occurred');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof dml_exception);
            $this->assertSame('Table "backup_ids_temp" does not exist', $e->getMessage());
        }

        // Create the backup_ids temp tables once more.
        restore_controller_dbops::create_restore_temp_tables($restoreid);

        // Send one mapping using the public api with complete values.
        restore_dbops::set_backup_ids_record($restoreid, $mapping->itemname, $mapping->itemid,
                $mapping->newitemid, $mapping->parentitemid, $mapping->info);
        // Get that mapping and verify everything is returned as expected.
        $result = restore_dbops::get_backup_ids_record($restoreid, $mapping->itemname, $mapping->itemid);
        $this->assertSame($mapping->itemname, $result->itemname);
        $this->assertSame($mapping->itemid, $result->itemid);
        $this->assertSame($mapping->newitemid, $result->newitemid);
        $this->assertSame($mapping->parentitemid, $result->parentitemid);
        $this->assertSame($mapping->info, $result->info);

        // Finally, drop the temp tables properly and get the DB error again (memory caches empty).
        restore_controller_dbops::drop_restore_temp_tables($restoreid);
        try {
            $result = restore_dbops::get_backup_ids_record($restoreid, $mapping->itemname, $mapping->itemid);
            $this->fail('Expecting an exception, none occurred');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof dml_exception);
            $this->assertSame('Table "backup_ids_temp" does not exist', $e->getMessage());
        }
    }
}

/**
 * Backup dbops tests (all).
 */
class backup_dbops_testcase extends advanced_testcase {

    protected $moduleid;  // course_modules id used for testing
    protected $sectionid; // course_sections id used for testing
    protected $courseid;  // course id used for testing
    protected $userid;      // user record used for testing

    protected function setUp() {
        global $DB, $CFG;
        parent::setUp();

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', array('course'=>$course->id), array('section'=>3));
        $coursemodule = $DB->get_record('course_modules', array('id'=>$page->cmid));

        $this->moduleid  = $coursemodule->id;
        $this->sectionid = $DB->get_field("course_sections", 'id', array("section"=>$coursemodule->section, "course"=>$course->id));
        $this->courseid  = $coursemodule->course;
        $this->userid = 2; // admin

        $CFG->backup_error_log_logger_level = backup::LOG_NONE;
        $CFG->backup_output_indented_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $CFG->backup_database_logger_level = backup::LOG_NONE;
        unset($CFG->backup_file_logger_extra);
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
    }

    /*
     * test backup_ops class
     */
    function test_backup_dbops() {
        // Nothing to do here, abstract class + exception, will be tested by the rest
    }

    /*
     * test backup_controller_dbops class
     */
    function test_backup_controller_dbops() {
        global $DB;

        $dbman = $DB->get_manager(); // Going to use some database_manager services for testing

        // Instantiate non interactive backup_controller
        $bc = new mock_backup_controller4dbops(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $this->assertTrue($bc instanceof backup_controller);
        // Calculate checksum
        $checksum = $bc->calculate_checksum();
        $this->assertEquals(strlen($checksum), 32); // is one md5

        // save controller
        $recid = backup_controller_dbops::save_controller($bc, $checksum);
        $this->assertNotEmpty($recid);
        // save it again (should cause update to happen)
        $recid2 = backup_controller_dbops::save_controller($bc, $checksum);
        $this->assertNotEmpty($recid2);
        $this->assertEquals($recid, $recid2); // Same record in both save operations

        // Try incorrect checksum
        $bc = new mock_backup_controller4dbops(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $checksum = $bc->calculate_checksum();
        try {
            $recid = backup_controller_dbops::save_controller($bc, 'lalala');
            $this->assertTrue(false, 'backup_dbops_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_dbops_exception);
            $this->assertEquals($e->errorcode, 'backup_controller_dbops_saving_checksum_mismatch');
        }

        // Try to save non backup_controller object
        $bc = new stdclass();
        try {
            $recid = backup_controller_dbops::save_controller($bc, 'lalala');
            $this->assertTrue(false, 'backup_controller_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_controller_exception);
            $this->assertEquals($e->errorcode, 'backup_controller_expected');
        }

        // save and load controller (by backupid). Then compare
        $bc = new mock_backup_controller4dbops(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $checksum = $bc->calculate_checksum(); // Calculate checksum
        $backupid = $bc->get_backupid();
        $this->assertEquals(strlen($backupid), 32); // is one md5
        $recid = backup_controller_dbops::save_controller($bc, $checksum); // save controller
        $newbc = backup_controller_dbops::load_controller($backupid); // load controller
        $this->assertTrue($newbc instanceof backup_controller);
        $newchecksum = $newbc->calculate_checksum();
        $this->assertEquals($newchecksum, $checksum);

        // try to load non-existing controller
        try {
            $bc = backup_controller_dbops::load_controller('1234567890');
            $this->assertTrue(false, 'backup_dbops_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_dbops_exception);
            $this->assertEquals($e->errorcode, 'backup_controller_dbops_nonexisting');
        }

        // backup_ids_temp table tests
        // If, for any reason table exists, drop it
        if ($dbman->table_exists('backup_ids_temp')) {
            $dbman->drop_table(new xmldb_table('backup_ids_temp'));
        }
        // Check backup_ids_temp table doesn't exist
        $this->assertFalse($dbman->table_exists('backup_ids_temp'));
        // Create and check it exists
        backup_controller_dbops::create_backup_ids_temp_table('testingid');
        $this->assertTrue($dbman->table_exists('backup_ids_temp'));
        // Drop and check it doesn't exists anymore
        backup_controller_dbops::drop_backup_ids_temp_table('testingid');
        $this->assertFalse($dbman->table_exists('backup_ids_temp'));
    }
}

class mock_backup_controller4dbops extends backup_controller {

    /**
     * Change standard behavior so the checksum is also stored and not onlt calculated
     */
    public function calculate_checksum() {
        $this->checksum = parent::calculate_checksum();
        return $this->checksum;
    }
}
