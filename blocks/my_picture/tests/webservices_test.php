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

require_once(dirname(dirname(__FILE__)).'/lib.php');

// Implementing classes will provide local values through these methods.
interface photoWebservice {
    public function webservice_url();
    public function ready_url();
    public function update_url();
    public function info_url();

    public function getMoodleUserDetailsForKnownUser();
    public function getWebserviceJsonDetailsForKnownUser();
    public function getValidUserIds();
    public function getIdnumberWithoutPicture();
    public function getFakeId();
}


// Exercises the webservice endpoints directly. Provides helper functions for subclasses.
class mypic_webservices_testcase extends advanced_testcase {

    public $ws;
    public $knownMoodleUser;

    public function setup() {
        $this->resetAfterTest();
        $this->initWebserviceConfigValues();
    }

    // Iniitalize config with local webservices URLs.
    private function initWebserviceConfigValues() {
        require_once('webservices.php');

        $this->ws = new TigerTracker();
        set_config('webservice_url', $this->ws->webservice_url(), 'block_my_picture');
        set_config('ready_url', $this->ws->ready_url(), 'block_my_picture');
        set_config('update_url', $this->ws->update_url(), 'block_my_picture');
        set_config('info_url', $this->ws->info_url(), 'block_my_picture');
    }

    // Helper function building WS URL for given user.
    public function buildUrlByIdnumber($base, $idnumber) {
        $hash = hash('sha256', $idnumber);
        return sprintf($base, $hash);
    }

    // Makes a WS request using the param $url.
    public function fetchFromWebservice($url) {
        $curl = new curl();
        return $curl->get($url);
    }

    // Fetch image for the given user directly from the WS.
    public function downloadFfromWebserviceByIdnumber($base, $idnumber) {
        global $CFG;

        $url  = $this->buildUrlByIdnumber($base, $idnumber);
        $path = $CFG->dataroot . '/temp/' . $idnumber . '.jpg';;
        $curl = new curl();
        $file = fopen($path, 'w');
        $curl->download(array(array('url' => $url, 'file' => $file)));
        fclose($file);

        return $path;
    }

    protected function getValidUser() {
        return $this->knownMoodleUser = $this->generateUser(
                $this->ws->getMoodleUserDetailsForKnownUser()
            );
    }

    protected function generateUser($params = array()) {
        return $this->getDataGenerator()->create_user($params);
    }

    protected function getDbPicStatusForUser($user) {
        global $DB;
        return $DB->get_field('user', 'picture', array('id' => $user->id));
    }

    protected function getNoPicUser() {
        return $this->generateUser(array(
            'idnumber'  => $this->ws->getIdnumberWithoutPicture(),
            'picture'   => 0
        ));
    }

    protected function getBadIdUser() {
        return $this->generateUser(
                array(
                    'idnumber' => $this->ws->getFakeId(),
                    'picture'  => 0,
                ));
    }

    /**
     * call the verify fn with all webservice URLs configured properly.
     * Then, reset all URLs to the default (wwwroot)
     * @global type $CFG
     * @global type $DB
     */
    public function test_mypic_verifyWebserviceExists() {
        global $CFG, $DB, $USER;
        $this->assertTrue(mypic_verifyWebserviceExists());
        // Reset to defaults.
        set_config('webservice_url', $CFG->wwwroot, 'block_my_picture');
        set_config('ready_url', $CFG->wwwroot, 'block_my_picture');
        set_config('update_url', $CFG->wwwroot, 'block_my_picture');
        set_config('info_url', $CFG->wwwroot, 'block_my_picture');

        // Ensure admin has an email address.
        $admin = $DB->get_record('user', array('username' => 'admin'));
        $admin->email = 'admin@example.com';
        $USER->maildisplay = $admin->maildisplay = 2;
        $USER->email = "USER@example.com";

        $DB->update_record('user', $admin);
        $this->assertFalse(mypic_verifyWebserviceExists());
    }

    // Ensure that the webservice response matches known values for a known user.
    public function testInfoUrlForKnownUser() {
        $this->getValidUser();

        $serviceurl = $this->buildUrlByIdnumber(
                get_config('block_my_picture', 'info_url'),
                $this->knownMoodleUser->idnumber
                );

        $webserviceresponse = $this->fetchFromWebservice($serviceurl);

        $this->assertJsonStringEqualsJsonString(
            $this->ws->getWebserviceJsonDetailsForKnownUser(),
            $webserviceresponse
            );
    }

    // Ensure that image downloaded for a known user is identical to the test suite image.
    public function testWebserviceUrlForKnownUser() {
        $this->getValidUser();

        $path = $this->downloadFfromWebserviceByIdnumber(
                    get_config('block_my_picture', 'webservice_url'),
                    $this->knownMoodleUser->idnumber
                );
        $this->assertInternalType('string', $path);
        $this->assertFileExists($path, sprintf("Couldn't find file %s", $path));
        $this->assertFileEquals('tests/mike.jpg', $path);
        $size = filesize($path);
        $this->assertGreaterThan(1, $size);
    }

    public function test_mypic_get_users_updated_pictures() {
        $this->assertNotEmpty($this->fetchFromWebservice(sprintf(get_config('block_my_picture', 'ready_url'), time())));
        $this->assertNotEmpty($this->fetchFromWebservice(sprintf(get_config('block_my_picture'
                                                        , 'ready_url'), time() - 30 * 86400)));
    }
}