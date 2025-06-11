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
 * Test for wspluginfile service class.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
namespace tool_ally;

defined('MOODLE_INTERNAL') || die();

use tool_ally\abstract_testcase;
use tool_ally\local;
use tool_ally\auto_config;
use tool_ally\webservice\wspluginfile;

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test for wspluginfile service class.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class wspluginfile_test extends abstract_testcase {

    /**
     * @var webservice
     */
    private $webservice;

    /**
     * @var wspluginfile
     */
    private $wspluginfile = null;

    /**
     * @var \stdClass
     */
    private $allywebuser;

    protected function setUp(): void {
        parent::setUp();

        $this->wspluginfile = new wspluginfile();
        $this->webservice = new \webservice();
    }

    public function test_validate_wspluginfile_signature_ally_webuser_exception() {
        $pathnamehash = sha1(uniqid());
        $iat = time();
        $token = '123';
        $signature = hash('sha256', $token . ':' . $iat . ':' . $pathnamehash);

        $msg = 'Access control exception (Ally web user (ally_webuser) does not exist. Has auto configure been run?)';
        $this->expectException(\webservice_access_exception::class);
        $this->expectExceptionMessage($msg);
        $this->wspluginfile->validate_wspluginfile_signature($signature, $iat, $pathnamehash);

    }

    /**
     * Initialise a test with config auto configured.
     */
    private function auto_config() {
        $this->resetAfterTest();

        $ac = new auto_config();
        $ac->configure();

        $this->allywebuser = local::get_ally_web_user();;
    }

    public function test_validate_wspluginfile_signature_signature_invalid_exception() {
        $this->auto_config();

        $pathnamehash = sha1(uniqid());
        $iat = time();
        $token = '123'; // This is an invalid token and will cause the exception!
        $signature = hash('sha256', $token . ':' . $iat . ':' . $pathnamehash);

        $this->expectException(\webservice_access_exception::class);
        $this->expectExceptionMessageMatches('/Signature is invalid/');
        $this->wspluginfile->validate_wspluginfile_signature($signature, $iat, $pathnamehash);
    }

    public function test_validate_wspluginfile_signature_signature() {
        $this->auto_config();

        $tokens = $this->webservice->get_user_ws_tokens($this->allywebuser->id);
        $wstoken = reset($tokens);
        $wstoken = \tool_ally\local::add_token_to_wstoken($wstoken);

        $pathnamehash = sha1(uniqid());
        $iat = time();
        $signature = hash('sha256', $wstoken->token . ':' . $iat . ':' . $pathnamehash);

        $authinfo = $this->wspluginfile->validate_wspluginfile_signature($signature, $iat, $pathnamehash);
        $this->assertArrayHasKey('service', $authinfo);
    }

    private function prepare_get_file() {
        $this->auto_config();

        $tokens = $this->webservice->get_user_ws_tokens($this->allywebuser->id);
        $wstoken = reset($tokens);
        $wstoken = \tool_ally\local::add_token_to_wstoken($wstoken);

        $this->setAdminUser(); // We need a user before we can create a file.

        $course = $this->getDataGenerator()->create_course();
        $resource = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $file = $this->get_resource_file($resource);

        $pathnamehash = $file->get_pathnamehash();
        $iat = time();

        return [$wstoken, $iat, $pathnamehash, $file];
    }

    public function test_get_file_by_signature() {
        list ($wstoken, $iat, $pathnamehash, $file) = $this->prepare_get_file();

        $signature = hash('sha256', $wstoken->token . ':' . $iat . ':' . $pathnamehash);

        $recoveredfile = $this->wspluginfile->get_file($pathnamehash, null, $signature, $iat);
        $this->assertEquals($file, $recoveredfile);
    }

    public function test_get_file_by_token() {
        list ($wstoken, $iat, $pathnamehash, $file) = $this->prepare_get_file();

        $recoveredfile = $this->wspluginfile->get_file($pathnamehash, $wstoken->token, null, null);
        $this->assertEquals($file, $recoveredfile);
    }
}
