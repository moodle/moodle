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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/blocks/my_picture/tests/webservices_test.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

class lib_test extends mypic_webservices_testcase {

    // Ensure that the tested function properly returns only valid idnumbers.
    public function test_mypic_WebserviceIntersectMoodleReturnsValidUsers() {
        $knownuser  = $this->getValidUser();
        $idnumbers  = array($knownuser->idnumber, 'id'.$this->ws->getFakeId(), 'id'.$this->ws->getFakeId());
        $validusers = mypic_WebserviceIntersectMoodle($idnumbers);

        $this->assertNotEmpty($validusers);
        $this->assertEquals(1, count($validusers));
        $this->assertTrue(array_key_exists(0, $validusers));
        $this->assertEquals($knownuser->idnumber, $validusers[0]->idnumber);
    }

    /**
     * Create a set of users that don't have pictures.
     * ensure that the library function under test returns the
     * correct number of users without pictures
     * @global type $DB
     */
    public function test_mypic_get_users_without_pictures() {
        global $DB;

        // The admin and guest users created by moodle/phpunit should be excluded from this test.
        $defaultusers = $DB->get_records('user');
        $this->assertEquals(2, count($defaultusers));

        foreach ($defaultusers as $def) {
            $def->picture = 1;
            $DB->update_record('user', $def);
        }

        $validids = $this->ws->getValidUserIds();
        foreach ($validids as $id) {
            $this->generateUser(array('idnumber'  => $id, 'picture'   => 0, ));
        }
        $this->assertEquals(count($validids), count(mypic_get_users_without_pictures()));
    }

    // Ensure that fn under test returns false given bad input.
    public function test_mypic_insert_picture_nofile() {
        $user    = $this->getValidUser();
        $badpath = 'nonexistent/path.jpg';

        $this->assertFalse(mypic_insert_picture($user->id, $badpath));
    }

    // Ensure that fn under test returns false given bad input.
    public function test_mypic_insert_picture_oneByte() {
        $user     = $this->getValidUser();
        $bytepath = 'oneByte';
        $filesize = file_put_contents($bytepath, " ");

        $this->assertFileExists($bytepath);
        $this->assertEquals(1, $filesize);
        $this->assertFalse(mypic_insert_picture($user->id, $bytepath));
    }

    // Ensure that fn under test returns true given good input.
    public function test_mypic_insert_picture_success() {
        $user = $this->getValidUser();
        $goodpath = 'tests/mike.jpg';

        $this->assertFileExists($goodpath);
        $this->assertTrue(mypic_insert_picture($user->id, $goodpath));
    }

    /**
     * ensure that given a bad user idnumber, the fn under test
     * returns the intended integer response
     * @global type $DB
     */
    public function test_mypic_update_picture_badid() {
        $badiduser = $this->getBadIdUser();
        $this->assertEquals(0, $this->getDbPicStatusForUser($badiduser));

        // Now test function result.
        $this->assertEquals(1, mypic_update_picture($badiduser));
        $this->assertEquals(1, $this->getDbPicStatusForUser($badiduser));
    }

    /**
     * ensure that given a valid user idnumber, the fn under test
     * returns the intended integer response and that the user object
     * 'picture' attribute is correctly updated in the DB
     * @global type $DB
     */
    public function test_mypic_update_picture_success() {
        $gooduser = $this->getValidUser();
        $this->assertEquals(0, $this->getDbPicStatusForUser($gooduser));
        $this->assertEquals(2, mypic_update_picture($gooduser));
        $this->assertEquals(1, $this->getDbPicStatusForUser($gooduser));
    }

    /**
     * ensure that given a valid user idnumber, but for which
     * no picture exists in the webservice, the fn under test
     * returns the intended integer response 3 and that the user object
     * 'picture' attribute is correctly updated in the DB
     * @global type $DB
     */
    public function test_mypic_update_picture_nopic() {
        $nopicuser = $this->getNoPicUser();
        $this->assertEquals(0, $this->getDbPicStatusForUser($nopicuser));

        $this->assertEquals(3, mypic_update_picture($nopicuser));
        $this->assertEquals(1, $this->getDbPicStatusForUser($nopicuser));
    }

    public function test_mypic_batch_update() {
        $users = array($this->getNoPicUser(), $this->getBadIdUser(), $this->getValidUser());
        $result = mypic_batch_update($users);

        $this->assertEquals(3, $result['count']);
        $this->assertEquals(1, $result['badid']);
        $this->assertEquals(1, $result['nopic']);
        $this->assertEquals(1, $result['success']);

    }

    /**
     * @expectedException     coding_exception
     */
    public function test_mypic_verifyWebserviceExists() {
        mypic_force_update_picture(123);
    }
}