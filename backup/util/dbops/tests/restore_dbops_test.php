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

    /**
     * Data provider for {@link test_precheck_user()}
     */
    public function precheck_user_provider() {

        $emailmultiplier = [
            'shortmail' => 'normalusername@example.com',
            'longmail' => str_repeat('a', 100)  // It's not validated, hence any string is ok.
        ];

        $providercases = [];

        foreach ($emailmultiplier as $emailk => $email) {
            // Get the related cases.
            $cases = $this->precheck_user_cases($email);
            // Rename them (keys).
            foreach ($cases as $key => $case) {
                $providercases[$key . ' - ' . $emailk] = $case;
            }
        }

        return $providercases;
    }

    /**
     * Get all the cases implemented in {@link restore_dbops::precheck_users()}
     *
     * @param string $email
     */
    private function precheck_user_cases($email) {
        global $CFG;

        $baseuserarr = [
            'username' => 'normalusername',
            'email'    => $email,
            'mnethostid' => $CFG->mnet_localhost_id,
            'firstaccess' => 123456789,
            'deleted'    => 0,
            'forceemailcleanup' => false, // Hack to force the DB record to have empty mail.
            'forceduplicateadminallowed' => false]; // Hack to enable import_general_duplicate_admin_allowed.

        return [
            // Cases with samesite = true.
            'samesite match existing (1A)' => [
                'dbuser' => $baseuserarr,
                'backupuser' => $baseuserarr,
                'samesite' => true,
                'outcome' => 'match'
            ],
            'samesite match existing anon (1B)' => [
                'dbuser' => array_merge($baseuserarr, [
                    'username' => 'anon01']),
                'backupuser' => array_merge($baseuserarr, [
                    'id' => -1, 'username' => 'anon01', 'firstname' => 'anonfirstname01',
                    'lastname' => 'anonlastname01', 'email' => 'anon01@doesntexist.invalid']),
                'samesite' => true,
                'outcome' => 'match'
            ],
            'samesite match existing deleted in db, alive in backup, by db username (1C)' => [
                'dbuser' => array_merge($baseuserarr, [
                    'deleted' => 1]),
                'backupuser' => array_merge($baseuserarr, [
                    'username' => 'this_wont_match']),
                'samesite' => true,
                'outcome' => 'match'
            ],
            'samesite match existing deleted in db, alive in backup, by db email (1C)' => [
                'dbuser' => array_merge($baseuserarr, [
                    'deleted' => 1]),
                'backupuser' => array_merge($baseuserarr, [
                    'email' => 'this_wont_match']),
                'samesite' => true,
                'outcome' => 'match'
            ],
            'samesite match existing alive in db, deleted in backup (1D)' => [
                'dbuser' => $baseuserarr,
                'backupuser' => array_merge($baseuserarr, [
                    'deleted' => 1]),
                'samesite' => true,
                'outcome' => 'match'
            ],
            'samesite conflict (1E)' => [
                'dbuser' => $baseuserarr,
                'backupuser' => array_merge($baseuserarr, ['id' => -1]),
                'samesite' => true,
                'outcome' => false
            ],
            'samesite create user (1F)' => [
                'dbuser' => $baseuserarr,
                'backupuser' => array_merge($baseuserarr, [
                    'username' => 'newusername']),
                'samesite' => false,
                'outcome' => true
            ],

            // Cases with samesite = false.
            'no samesite match existing, by db email (2A1)' => [
                'dbuser' => $baseuserarr,
                'backupuser' => array_merge($baseuserarr, [
                    'firstaccess' => 0]),
                'samesite' => false,
                'outcome' => 'match'
            ],
            'no samesite match existing, by db firstaccess (2A1)' => [
                'dbuser' => $baseuserarr,
                'backupuser' => array_merge($baseuserarr, [
                    'email' => 'this_wont_match@example.con']),
                'samesite' => false,
                'outcome' => 'match'
            ],
            'no samesite match existing anon (2A1 too)' => [
                'dbuser' => array_merge($baseuserarr, [
                    'username' => 'anon01']),
                'backupuser' => array_merge($baseuserarr, [
                    'id' => -1, 'username' => 'anon01', 'firstname' => 'anonfirstname01',
                    'lastname' => 'anonlastname01', 'email' => 'anon01@doesntexist.invalid']),
                'samesite' => false,
                'outcome' => 'match'
            ],
            'no samesite match dupe admin (2A2)' => [
                'dbuser' => array_merge($baseuserarr, [
                    'username' => 'admin_old_site_id',
                    'forceduplicateadminallowed' => true]),
                'backupuser' => array_merge($baseuserarr, [
                    'username' => 'admin']),
                'samesite' => false,
                'outcome' => 'match'
            ],
            'no samesite match existing deleted in db, alive in backup, by db username (2B1)' => [
                'dbuser' => array_merge($baseuserarr, [
                    'deleted' => 1]),
                'backupuser' => array_merge($baseuserarr, [
                    'firstaccess' => 0]),
                'samesite' => false,
                'outcome' => 'match'
            ],
            'no samesite match existing deleted in db, alive in backup, by db firstaccess (2B1)' => [
                'dbuser' => array_merge($baseuserarr, [
                    'deleted' => 1]),
                'backupuser' => array_merge($baseuserarr, [
                    'mail' => 'this_wont_match']),
                'samesite' => false,
                'outcome' => 'match'
            ],
            'no samesite match existing deleted in db, alive in backup (2B2)' => [
                'dbuser' => array_merge($baseuserarr, [
                    'deleted' => 1,
                    'forceemailcleanup' => true]),
                'backupuser' => $baseuserarr,
                'samesite' => false,
                'outcome' => 'match'
            ],
            'no samesite match existing alive in db, deleted in backup (2C)' => [
                'dbuser' => $baseuserarr,
                'backupuser' => array_merge($baseuserarr, [
                    'deleted' => 1]),
                'samesite' => false,
                'outcome' => 'match'
            ],
            'no samesite conflict (2D)' => [
                'dbuser' => $baseuserarr,
                'backupuser' => array_merge($baseuserarr, [
                    'email' => 'anotheruser@example.com', 'firstaccess' => 0]),
                'samesite' => false,
                'outcome' => false
            ],
            'no samesite create user (2E)' => [
                'dbuser' => $baseuserarr,
                'backupuser' => array_merge($baseuserarr, [
                    'username' => 'newusername']),
                'samesite' => false,
                'outcome' => true
            ],

        ];
    }

    /**
     * Test restore precheck_user method
     *
     * @dataProvider precheck_user_provider
     * @covers restore_dbops::precheck_user()
     *
     * @param array $dbuser
     * @param array $backupuser
     * @param bool $samesite
     * @param mixed $outcome
     **/
    public function test_precheck_user($dbuser, $backupuser, $samesite, $outcome) {
        global $DB;

        $this->resetAfterTest();

        $dbuser = (object)$dbuser;
        $backupuser = (object)$backupuser;

        $siteid = null;

        // If the backup user must be deleted, simulate it (by temp inserting to DB, deleting and fetching it back).
        if ($backupuser->deleted) {
            $backupuser->id = $DB->insert_record('user', array_merge((array)$backupuser, ['deleted' => 0]));
            delete_user($backupuser);
            $backupuser = $DB->get_record('user', ['id' => $backupuser->id]);
            $DB->delete_records('user', ['id' => $backupuser->id]);
            unset($backupuser->id);
        }

        // Create the db user, normally.
        $dbuser->id = $DB->insert_record('user', array_merge((array)$dbuser, ['deleted' => 0]));
        $backupuser->id = $backupuser->id ?? $dbuser->id;

        // We may want to enable the import_general_duplicate_admin_allowed setting and look for old admin records.
        if ($dbuser->forceduplicateadminallowed) {
            set_config('import_general_duplicate_admin_allowed', true, 'backup');
            $siteid = 'old_site_id';
        }

        // If the DB user must be deleted, do it and fetch it back.
        if ($dbuser->deleted) {
            delete_user($dbuser);
            // We may want to clean the mail field (old behavior, not containing the current md5(username).
            if ($dbuser->forceemailcleanup) {
                $DB->set_field('user', 'email', '', ['id' => $dbuser->id]);
            }
        }

        // Get the dbuser  record, because we may have changed it above.
        $dbuser = $DB->get_record('user', ['id' => $dbuser->id]);

        $method = (new ReflectionClass('restore_dbops'))->getMethod('precheck_user');
        $method->setAccessible(true);
        $result = $method->invoke(null, $backupuser, $samesite, $siteid);

        if (is_bool($result)) {
            $this->assertSame($outcome, $result);
        } else {
            $outcome = $dbuser; // Outcome is not bool, matching found, so it must be the dbuser,
            // Just check ids, it means the expected match has been found in database.
            $this->assertSame($outcome->id, $result->id);
        }
    }
}
