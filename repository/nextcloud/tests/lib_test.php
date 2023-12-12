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
 * This file contains tests for the repository_nextcloud class.
 *
 * @package     repository_nextcloud
 * @copyright  2017 Project seminar (Learnweb, University of Münster)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_nextcloud;

use repository;
use repository_nextcloud;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/webdavlib.php');

/**
 * Class repository_nextcloud_lib_testcase
 * @group repository_nextcloud
 * @copyright  2017 Project seminar (Learnweb, University of Münster)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {

    /** @var null|\repository_nextcloud the repository_nextcloud object, which the tests are run on. */
    private $repo = null;

    /** @var null|\core\oauth2\issuer which belongs to the repository_nextcloud object.*/
    private $issuer = null;

    /**
     * SetUp to create an repository instance.
     */
    protected function setUp(): void {
        $this->resetAfterTest(true);

        // Admin is neccessary to create api and issuer objects.
        $this->setAdminUser();

        /** @var repository_nextcloud_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('repository_nextcloud');
        $this->issuer = $generator->test_create_issuer();

        // Create Endpoints for issuer.
        $generator->test_create_endpoints($this->issuer->get('id'));

        // Params for the config form.
        $reptype = $generator->create_type([
            'visible' => 1,
            'enableuserinstances' => 0,
            'enablecourseinstances' => 0,
        ]);

        $instance = $generator->create_instance([
            'issuerid' => $this->issuer->get('id'),
            'pluginname' => 'Nextcloud',
            'controlledlinkfoldername' => 'Moodlefiles',
            'supportedreturntypes' => 'both',
            'defaultreturntype' => FILE_INTERNAL,
        ]);

        // At last, create a repository_nextcloud object from the instance id.
        $this->repo = new repository_nextcloud($instance->id);
        $this->repo->options['typeid'] = $reptype->id;
        $this->repo->options['sortorder'] = 1;
        $this->resetAfterTest(true);
    }

    /**
     * Checks the is_visible method in case the repository is set to hidden in the database.
     */
    public function test_is_visible_parent_false() {
        global $DB;
        $id = $this->repo->options['typeid'];

        // Check, if the method returns false, when the repository is set to visible in the database
        // and the client configuration data is complete.
        $DB->update_record('repository', (object) array('id' => $id, 'visible' => 0));

        $this->assertFalse($this->repo->is_visible());
    }

    /**
     * Test whether the repo is disabled.
     */
    public function test_repo_creation() {
        $issuerid = $this->repo->get_option('issuerid');

        // Config saves the right id.
        $this->assertEquals($this->issuer->get('id'), $issuerid);

        // Function that is used in construct method returns the right id.
        $constructissuer = \core\oauth2\api::get_issuer($issuerid);
        $this->assertEquals($this->issuer->get('id'), $constructissuer->get('id'));

        $this->assertEquals(true, $constructissuer->get('enabled'));
        $this->assertFalse($this->repo->disabled);
    }

    /**
     * Returns an array of endpoints or null.
     * @param string $endpointname
     * @return array|null
     */
    private function get_endpoint_id($endpointname) {
        $endpoints = \core\oauth2\api::get_endpoints($this->issuer);
        $id = array();
        foreach ($endpoints as $endpoint) {
            $name = $endpoint->get('name');
            if ($name === $endpointname) {
                $id[$endpoint->get('id')] = $endpoint->get('id');
            }
        }
        if (empty($id)) {
            return null;
        }
        return $id;
    }
    /**
     * Test if repository is disabled when webdav_endpoint is deleted.
     */
    public function test_issuer_webdav() {
        $idwebdav = $this->get_endpoint_id('webdav_endpoint');
        if (!empty($idwebdav)) {
            foreach ($idwebdav as $id) {
                \core\oauth2\api::delete_endpoint($id);
            }
        }
        $this->assertFalse(\repository_nextcloud\issuer_management::is_valid_issuer($this->issuer));
    }
    /**
     * Test if repository is disabled when ocs_endpoint is deleted.
     */
    public function test_issuer_ocs() {
        $idocs = $this->get_endpoint_id('ocs_endpoint');
        if (!empty($idocs)) {
            foreach ($idocs as $id) {
                \core\oauth2\api::delete_endpoint($id);
            }
        }
        $this->assertFalse(\repository_nextcloud\issuer_management::is_valid_issuer($this->issuer));
    }

    /**
     * Test if repository is disabled when userinfo_endpoint is deleted.
     */
    public function test_issuer_userinfo() {
        $idtoken = $this->get_endpoint_id('userinfo_endpoint');
        if (!empty($idtoken)) {
            foreach ($idtoken as $id) {
                \core\oauth2\api::delete_endpoint($id);
            }
        }
        $this->assertFalse(\repository_nextcloud\issuer_management::is_valid_issuer($this->issuer));
    }

    /**
     * Test if repository is disabled when token_endpoint is deleted.
     */
    public function test_issuer_token() {
        $idtoken = $this->get_endpoint_id('token_endpoint');
        if (!empty($idtoken)) {
            foreach ($idtoken as $id) {
                \core\oauth2\api::delete_endpoint($id);
            }
        }
        $this->assertFalse(\repository_nextcloud\issuer_management::is_valid_issuer($this->issuer));
    }

    /**
     * Test if repository is disabled when auth_endpoint is deleted.
     */
    public function test_issuer_authorization() {
        $idauth = $this->get_endpoint_id('authorization_endpoint');
        if (!empty($idauth)) {
            foreach ($idauth as $id) {
                \core\oauth2\api::delete_endpoint($id);
            }
        }
        $this->assertFalse(\repository_nextcloud\issuer_management::is_valid_issuer($this->issuer));
    }
    /**
     * Test if repository throws an error when endpoint does not exist.
     */
    public function test_parse_endpoint_url_error() {
        $this->expectException(\repository_nextcloud\configuration_exception::class);
        \repository_nextcloud\issuer_management::parse_endpoint_url('notexisting', $this->issuer);
    }
    /**
     * Test get_listing method with an example directory. Tests error cases.
     */
    public function test_get_listing_error() {
        $ret = $this->get_initialised_return_array();
        $this->setUser();
        // WebDAV socket is not opened.
        $mock = $this->createMock(\webdav_client::class);
        $mock->expects($this->once())->method('open')->will($this->returnValue(false));
        $private = $this->set_private_property($mock, 'dav');

        $this->assertEquals($ret, $this->repo->get_listing('/'));

        // Response is not an array.
        $mock = $this->createMock(\webdav_client::class);
        $mock->expects($this->once())->method('open')->will($this->returnValue(true));
        $mock->expects($this->once())->method('ls')->will($this->returnValue('notanarray'));
        $private->setValue($this->repo, $mock);

        $this->assertEquals($ret, $this->repo->get_listing('/'));
    }
    /**
     * Test get_listing method with an example directory. Tests the root directory.
     */
    public function test_get_listing_root() {
        $this->setUser();
        $ret = $this->get_initialised_return_array();

        // This is the expected response from the ls method.
        $response = array(
            array(
                'href' => 'remote.php/webdav/',
                'lastmodified' => 'Thu, 08 Dec 2016 16:06:26 GMT',
                'resourcetype' => 'collection',
                'status' => 'HTTP/1.1 200 OK',
                'getcontentlength' => ''
            ),
            array(
                'href' => 'remote.php/webdav/Documents/',
                'lastmodified' => 'Thu, 08 Dec 2016 16:06:26 GMT',
                'resourcetype' => 'collection',
                'status' => 'HTTP/1.1 200 OK',
                'getcontentlength' => ''
            ),
            array(
                'href' => 'remote.php/webdav/welcome.txt',
                'lastmodified' => 'Thu, 08 Dec 2016 16:06:26 GMT',
                'status' => 'HTTP/1.1 200 OK',
                'getcontentlength' => '163'
            )
        );

        // The expected result from the get_listing method in the repository_nextcloud class.
        $ret['list'] = array(
            'DOCUMENTS/' => array(
                'title' => 'Documents',
                'thumbnail' => null,
                'children' => array(),
                'datemodified' => 1481213186,
                'path' => '/Documents/'
            ),
            'WELCOME.TXT' => array(
                'title' => 'welcome.txt',
                'thumbnail' => null,
                'size' => '163',
                'datemodified' => 1481213186,
                'source' => '/welcome.txt'
            )
        );

        // Valid response from the client.
        $mock = $this->createMock(\webdav_client::class);
        $mock->expects($this->once())->method('open')->will($this->returnValue(true));
        $mock->expects($this->once())->method('ls')->will($this->returnValue($response));
        $this->set_private_property($mock, 'dav');

        $ls = $this->repo->get_listing('/');

        // Those attributes can not be tested properly.
        $ls['list']['DOCUMENTS/']['thumbnail'] = null;
        $ls['list']['WELCOME.TXT']['thumbnail'] = null;

        $this->assertEquals($ret, $ls);
    }
    /**
     * Test get_listing method with an example directory. Tests a different directory than the root
     * directory.
     */
    public function test_get_listing_directory() {
        $ret = $this->get_initialised_return_array();
        $this->setUser();

        // An additional directory path has to be added to the 'path' field within the returned array.
        $ret['path'][1] = array(
            'name' => 'dir',
            'path' => '/dir/'
        );

        // This is the expected response from the get_listing method in the Nextcloud client.
        $response = array(
            array(
                'href' => 'remote.php/webdav/dir/',
                'lastmodified' => 'Thu, 08 Dec 2016 16:06:26 GMT',
                'resourcetype' => 'collection',
                'status' => 'HTTP/1.1 200 OK',
                'getcontentlength' => ''
            ),
            array(
                'href' => 'remote.php/webdav/dir/Documents/',
                'lastmodified' => null,
                'resourcetype' => 'collection',
                'status' => 'HTTP/1.1 200 OK',
                'getcontentlength' => ''
            ),
            array(
                'href' => 'remote.php/webdav/dir/welcome.txt',
                'lastmodified' => 'Thu, 08 Dec 2016 16:06:26 GMT',
                'status' => 'HTTP/1.1 200 OK',
                'getcontentlength' => '163'
            )
        );

        // The expected result from the get_listing method in the repository_nextcloud class.
        $ret['list'] = array(
            'DOCUMENTS/' => array(
                'title' => 'Documents',
                'thumbnail' => null,
                'children' => array(),
                'datemodified' => null,
                'path' => '/dir/Documents/'
            ),
            'WELCOME.TXT' => array(
                'title' => 'welcome.txt',
                'thumbnail' => null,
                'size' => '163',
                'datemodified' => 1481213186,
                'source' => '/dir/welcome.txt'
            )
        );

        // Valid response from the client.
        $mock = $this->createMock(\webdav_client::class);
        $mock->expects($this->once())->method('open')->will($this->returnValue(true));
        $mock->expects($this->once())->method('ls')->will($this->returnValue($response));
        $this->set_private_property($mock, 'dav');

        $ls = $this->repo->get_listing('/dir/');

        // Can not be tested properly.
        $ls['list']['DOCUMENTS/']['thumbnail'] = null;
        $ls['list']['WELCOME.TXT']['thumbnail'] = null;

        $this->assertEquals($ret, $ls);
    }
    /**
     * Test the get_link method.
     */
    public function test_get_link_success() {
        $mock = $this->getMockBuilder(\repository_nextcloud\ocs_client::class)->disableOriginalConstructor()->disableOriginalClone(
            )->getMock();
        $file = '/datei';
        $expectedresponse = <<<XML
<?xml version="1.0"?>
<ocs>
 <meta>
  <status>ok</status>
  <statuscode>100</statuscode>
  <message/>
 </meta>
 <data>
  <id>2</id>
  <share_type>3</share_type>
  <uid_owner>admin</uid_owner>
  <displayname_owner>admin</displayname_owner>
  <permissions>1</permissions>
  <stime>1502883721</stime>
  <parent/>
  <expiration/>
  <token>QXbqrJj8DcMaXen</token>
  <uid_file_owner>admin</uid_file_owner>
  <displayname_file_owner>admin</displayname_file_owner>
  <path>/somefile</path>
  <item_type>file</item_type>
  <mimetype>application/pdf</mimetype>
  <storage_id>home::admin</storage_id>
  <storage>1</storage>
  <item_source>6</item_source>
  <file_source>6</file_source>
  <file_parent>4</file_parent>
  <file_target>/somefile</file_target>
  <share_with/>
  <share_with_displayname/>
  <name/>
  <url>https://www.default.test/somefile</url>
  <mail_send>0</mail_send>
 </data>
</ocs>
XML;
        // Expected Parameters.
        $ocsquery = [
            'path' => $file,
            'shareType' => \repository_nextcloud\ocs_client::SHARE_TYPE_PUBLIC,
            'publicUpload' => false,
            'permissions' => \repository_nextcloud\ocs_client::SHARE_PERMISSION_READ
        ];

        // With test whether mock is called with right parameters.
        $mock->expects($this->once())->method('call')->with('create_share', $ocsquery)->will($this->returnValue($expectedresponse));
        $this->set_private_property($mock, 'ocsclient');

        // Method does extract the link from the xml format.
        $this->assertEquals('https://www.default.test/somefile/download', $this->repo->get_link($file));
    }

    /**
     * get_link can get OCS failure responses. Test that this is handled appropriately.
     */
    public function test_get_link_failure() {
        $mock = $this->getMockBuilder(\repository_nextcloud\ocs_client::class)->disableOriginalConstructor()->disableOriginalClone(
            )->getMock();
        $file = '/datei';
        $expectedresponse = <<<XML
<?xml version="1.0"?>
<ocs>
 <meta>
  <status>failure</status>
  <statuscode>404</statuscode>
  <message>Msg</message>
 </meta>
 <data/>
</ocs>
XML;
        // Expected Parameters.
        $ocsquery = [
            'path' => $file,
            'shareType' => \repository_nextcloud\ocs_client::SHARE_TYPE_PUBLIC,
            'publicUpload' => false,
            'permissions' => \repository_nextcloud\ocs_client::SHARE_PERMISSION_READ
        ];

        // With test whether mock is called with right parameters.
        $mock->expects($this->once())->method('call')->with('create_share', $ocsquery)->will($this->returnValue($expectedresponse));
        $this->set_private_property($mock, 'ocsclient');

        // Suppress (expected) XML parse error... Nextcloud sometimes returns JSON on extremely bad errors.
        libxml_use_internal_errors(true);

        // Method get_link correctly raises an exception that contains error code and message.
        $this->expectException(\repository_nextcloud\request_exception::class);
        $params = array('instance' => $this->repo->get_name(), 'errormessage' => sprintf('(%s) %s', '404', 'Msg'));
        $this->expectExceptionMessage(get_string('request_exception', 'repository_nextcloud', $params));
        $this->repo->get_link($file);
    }

    /**
     * get_link can get OCS responses that are not actually XML. Test that this is handled appropriately.
     */
    public function test_get_link_problem() {
        $mock = $this->getMockBuilder(\repository_nextcloud\ocs_client::class)->disableOriginalConstructor()->disableOriginalClone(
            )->getMock();
        $file = '/datei';
        $expectedresponse = <<<JSON
{"message":"CSRF check failed"}
JSON;
        // Expected Parameters.
        $ocsquery = [
            'path' => $file,
            'shareType' => \repository_nextcloud\ocs_client::SHARE_TYPE_PUBLIC,
            'publicUpload' => false,
            'permissions' => \repository_nextcloud\ocs_client::SHARE_PERMISSION_READ
        ];

        // With test whether mock is called with right parameters.
        $mock->expects($this->once())->method('call')->with('create_share', $ocsquery)->will($this->returnValue($expectedresponse));
        $this->set_private_property($mock, 'ocsclient');

        // Suppress (expected) XML parse error... Nextcloud sometimes returns JSON on extremely bad errors.
        libxml_use_internal_errors(true);

        // Method get_link correctly raises an exception.
        $this->expectException(\repository_nextcloud\request_exception::class);
        $this->repo->get_link($file);
    }

    /**
     * Test get_file reference, merely returns the input if no optional_param is set.
     */
    public function test_get_file_reference_withoutoptionalparam() {
        $this->assertEquals('/somefile', $this->repo->get_file_reference('/somefile'));
    }

    /**
     * Test logout.
     */
    public function test_logout() {
        $mock = $this->createMock(\core\oauth2\client::class);

        $mock->expects($this->exactly(2))->method('log_out');
        $this->set_private_property($mock, 'client');
        $this->repo->options['ajax'] = false;
        $this->expectOutputString('<a target="_blank" rel="noopener noreferrer">Log in to your account</a>' .
            '<a target="_blank" rel="noopener noreferrer">Log in to your account</a>');

        $this->assertEquals($this->repo->print_login(), $this->repo->logout());

        $mock->expects($this->exactly(2))->method('get_login_url')->will($this->returnValue(new \moodle_url('url')));

        $this->repo->options['ajax'] = true;
        $this->assertEquals($this->repo->print_login(), $this->repo->logout());

    }
    /**
     * Test for the get_file method from the repository_nextcloud class.
     */
    public function test_get_file() {
        // WebDAV socket is not open.
        $mock = $this->createMock(\webdav_client::class);
        $mock->expects($this->once())->method('open')->will($this->returnValue(false));
        $private = $this->set_private_property($mock, 'dav');

        $this->assertFalse($this->repo->get_file('path'));

        // WebDAV socket is open and the request successful.
        $mock = $this->createMock(\webdav_client::class);
        $mock->expects($this->once())->method('open')->will($this->returnValue(true));
        $mock->expects($this->once())->method('get_file')->will($this->returnValue(true));
        $private->setValue($this->repo, $mock);

        $result = $this->repo->get_file('path', 'file');

        $this->assertNotNull($result['path']);
    }

    /**
     * Test callback.
     */
    public function test_callback() {
        $mock = $this->createMock(\core\oauth2\client::class);
        // Should call check_login exactly once.
        $mock->expects($this->once())->method('log_out');
        $mock->expects($this->once())->method('is_logged_in');

        $this->set_private_property($mock, 'client');

        $this->repo->callback();
    }
    /**
     * Test check_login.
     */
    public function test_check_login() {
        $mock = $this->createMock(\core\oauth2\client::class);
        $mock->expects($this->once())->method('is_logged_in')->will($this->returnValue(true));
        $this->set_private_property($mock, 'client');

        $this->assertTrue($this->repo->check_login());
    }
    /**
     * Test print_login.
     */
    public function test_print_login() {
        $mock = $this->createMock(\core\oauth2\client::class);
        $mock->expects($this->exactly(2))->method('get_login_url')->will($this->returnValue(new \moodle_url('url')));
        $this->set_private_property($mock, 'client');

        // Test with ajax activated.
        $this->repo->options['ajax'] = true;

        $url = new \moodle_url('url');
        $ret = array();
        $btn = new \stdClass();
        $btn->type = 'popup';
        $btn->url = $url->out(false);
        $ret['login'] = array($btn);

        $this->assertEquals($ret, $this->repo->print_login());

        // Test without ajax.
        $this->repo->options['ajax'] = false;

        $output = \html_writer::link($url, get_string('login', 'repository'),
            array('target' => '_blank',  'rel' => 'noopener noreferrer'));
        $this->expectOutputString($output);
        $this->repo->print_login();
    }

    /**
     * Test the initiate_webdavclient function.
     */
    public function test_initiate_webdavclient() {
        global $CFG;

        $idwebdav = $this->get_endpoint_id('webdav_endpoint');
        if (!empty($idwebdav)) {
            foreach ($idwebdav as $id) {
                \core\oauth2\api::delete_endpoint($id);
            }
        }

        $generator = $this->getDataGenerator()->get_plugin_generator('repository_nextcloud');
        $generator->test_create_single_endpoint($this->issuer->get('id'), "webdav_endpoint",
            "https://www.default.test:8080/webdav/index.php");

        $fakeaccesstoken = new \stdClass();
        $fakeaccesstoken->token = "fake access token";
        $oauthmock = $this->createMock(\core\oauth2\client::class);
        $oauthmock->expects($this->once())->method('get_accesstoken')->will($this->returnValue($fakeaccesstoken));
        $this->set_private_property($oauthmock, 'client');

        $dav = \phpunit_util::call_internal_method($this->repo, "initiate_webdavclient", [], 'repository_nextcloud');

        // Verify that port is set correctly (private property).
        $refclient = new \ReflectionClass($dav);

        $property = $refclient->getProperty('_port');
        $property->setAccessible(true);

        $port = $property->getValue($dav);

        $this->assertEquals('8080', $port);
    }

    /**
     * Test supported_returntypes.
     * FILE_INTERNAL | FILE_REFERENCE when no system account is connected.
     * FILE_INTERNAL | FILE_CONTROLLED_LINK | FILE_REFERENCE when a system account is connected.
     */
    public function test_supported_returntypes() {
        global $DB;
        $this->assertEquals(FILE_INTERNAL | FILE_REFERENCE, $this->repo->supported_returntypes());
        $dataobject = new \stdClass();
        $dataobject->timecreated = time();
        $dataobject->timemodified = time();
        $dataobject->usermodified = 2;
        $dataobject->issuerid = $this->issuer->get('id');
        $dataobject->refreshtoken = 'sometokenthatwillnotbeused';
        $dataobject->grantedscopes = 'openid profile email';
        $dataobject->email = 'some.email@some.de';
        $dataobject->username = 'someusername';

        $DB->insert_record('oauth2_system_account', $dataobject);
        // When a system account is registered the file_type FILE_CONTROLLED_LINK is supported.
        $this->assertEquals(FILE_INTERNAL | FILE_CONTROLLED_LINK | FILE_REFERENCE,
            $this->repo->supported_returntypes());
    }

    /**
     * The reference_file_selected() method is called every time a FILE_CONTROLLED_LINK is chosen for upload.
     * Since the function is very long the private function are tested separately, and merely the abortion of the
     * function are tested.
     *
     */
    public function test_reference_file_selected_error() {
        $this->repo->disabled = true;
        $this->expectException(\repository_exception::class);
        $this->repo->reference_file_selected('', \context_system::instance(), '', '', '');

        $this->repo->disabled = false;
        $this->expectException(\repository_exception::class);
        $this->expectExceptionMessage('Cannot connect as system user');
        $this->repo->reference_file_selected('', \context_system::instance(), '', '', '');

        $mock = $this->createMock(\core\oauth2\client::class);
        $mock->expects($this->once())->method('get_system_oauth_client')->with($this->issuer)->willReturn(true);

        $this->expectException(\repository_exception::class);
        $this->expectExceptionMessage('Cannot connect as current user');
        $this->repo->reference_file_selected('', \context_system::instance(), '', '', '');

        $this->repo->expects($this->once())->method('get_user_oauth_client')->willReturn(true);
        $this->expectException(\repository_exception::class);
        $this->expectExceptionMessage('cannotdownload');
        $this->repo->reference_file_selected('', \context_system::instance(), '', '', '');

        $this->repo->expects($this->once())->method('get_user_oauth_client')->willReturn(true);
        $this->expectException(\repository_exception::class);
        $this->expectExceptionMessage('cannotdownload');
        $this->repo->reference_file_selected('', \context_system::instance(), '', '', '');

        $this->repo->expects($this->once())->method('get_user_oauth_client')->willReturn(true);
        $this->repo->expects($this->once())->method('copy_file_to_path')->willReturn(array('statuscode' =>
            array('success' => 400)));
        $this->expectException(\repository_exception::class);
        $this->expectExceptionMessage('Could not copy file');
        $this->repo->reference_file_selected('', \context_system::instance(), '', '', '');

        $this->repo->expects($this->once())->method('get_user_oauth_client')->willReturn(true);
        $this->repo->expects($this->once())->method('copy_file_to_path')->willReturn(array('statuscode' =>
            array('success' => 201)));
        $this->repo->expects($this->once())->method('delete_share_dataowner_sysaccount')->willReturn(
            array('statuscode' => array('success' => 400)));
        $this->expectException(\repository_exception::class);
        $this->expectExceptionMessage('Share is still present');
        $this->repo->reference_file_selected('', \context_system::instance(), '', '', '');

        $this->repo->expects($this->once())->method('get_user_oauth_client')->willReturn(true);
        $this->repo->expects($this->once())->method('copy_file_to_path')->willReturn(array('statuscode' =>
            array('success' => 201)));
        $this->repo->expects($this->once())->method('delete_share_dataowner_sysaccount')->willReturn(
            array('statuscode' => array('success' => 100)));
        $filereturn = new \stdClass();
        $filereturn->link = 'some/fullpath' . 'some/target/path';
        $filereturn->name = 'mysource';
        $filereturn->usesystem = true;
        $filereturn = json_encode($filereturn);
        $return = $this->repo->reference_file_selected('mysource', \context_system::instance(), '', '', '');
        $this->assertEquals($filereturn, $return);
    }

    /**
     * Test the send_file function for access controlled links.
     */
    public function test_send_file_errors() {
        $fs = get_file_storage();
        $storedfile = $fs->create_file_from_reference([
            'contextid' => \context_system::instance()->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'testfile.txt',
        ], $this->repo->id, json_encode([
            'type' => 'FILE_CONTROLLED_LINK',
            'link' => 'https://test.local/fakelink/',
            'usesystem' => true,
        ]));
        $this->set_private_property('', 'client');
        $this->expectException(repository_nextcloud\request_exception::class);
        $this->expectExceptionMessage(get_string('contactadminwith', 'repository_nextcloud',
            'The OAuth clients could not be connected.'));

        $this->repo->send_file($storedfile, '', '', '');

        // Testing whether the mock up appears is topic to behat.
        $mock = $this->createMock(\core\oauth2\client::class);
        $mock->expects($this->once())->method('is_logged_in')->willReturn(true);
        $this->repo->send_file($storedfile, '', '', '');

        // Checks that setting for foldername are used.
        $mock->expects($this->once())->method('is_dir')->with('Moodlefiles')->willReturn(false);
        // In case of false as return value mkcol is called to create the folder.
        $parsedwebdavurl = parse_url($this->issuer->get_endpoint_url('webdav'));
        $webdavprefix = $parsedwebdavurl['path'];
        $mock->expects($this->once())->method('mkcol')->with(
            $webdavprefix . 'Moodlefiles')->willReturn(400);
        $this->expectException(\repository_nextcloud\request_exception::class);
        $this->expectExceptionMessage(get_string('requestnotexecuted', 'repository_nextcloud'));
        $this->repo->send_file($storedfile, '', '', '');

        $expectedresponse = <<<XML
<?xml version="1.0"?>
<ocs>
 <meta>
  <status>ok</status>
  <statuscode>100</statuscode>
  <message/>
 </meta>
 <data>
  <element>
   <id>6</id>
   <share_type>0</share_type>
   <uid_owner>tech</uid_owner>
   <displayname_owner>tech</displayname_owner>
   <permissions>19</permissions>
   <stime>1511877999</stime>
   <parent/>
   <expiration/>
   <token/>
   <uid_file_owner>tech</uid_file_owner>
   <displayname_file_owner>tech</displayname_file_owner>
   <path>/System/Category Category 1/Course Example Course/File morefiles/mod_resource/content/0/merge.txt</path>
   <item_type>file</item_type>
   <mimetype>text/plain</mimetype>
   <storage_id>home::tech</storage_id>
   <storage>4</storage>
   <item_source>824</item_source>
   <file_source>824</file_source>
   <file_parent>823</file_parent>
   <file_target>/merge (3).txt</file_target>
   <share_with>user2</share_with>
   <share_with_displayname>user1</share_with_displayname>
   <mail_send>0</mail_send>
  </element>
  <element>
   <id>5</id>
   <share_type>0</share_type>
   <uid_owner>tech</uid_owner>
   <displayname_owner>tech</displayname_owner>
   <permissions>19</permissions>
   <stime>1511877999</stime>
   <parent/>
   <expiration/>
   <token/>
   <uid_file_owner>tech</uid_file_owner>
   <displayname_file_owner>tech</displayname_file_owner>
   <path>/System/Category Category 1/Course Example Course/File morefiles/mod_resource/content/0/merge.txt</path>
   <item_type>file</item_type>
   <mimetype>text/plain</mimetype>
   <storage_id>home::tech</storage_id>
   <storage>4</storage>
   <item_source>824</item_source>
   <file_source>824</file_source>
   <file_parent>823</file_parent>
   <file_target>/merged (3).txt</file_target>
   <share_with>user1</share_with>
   <share_with_displayname>user1</share_with_displayname>
   <mail_send>0</mail_send>
  </element>
 </data>
</ocs>
XML;

        // Checks that setting for foldername are used.
        $mock->expects($this->once())->method('is_dir')->with('Moodlefiles')->willReturn(true);
        // In case of true as return value mkcol is not called  to create the folder.
        $shareid = 5;

        $mockocsclient = $this->getMockBuilder(
            \repository_nextcloud\ocs_client::class)->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $mockocsclient->expects($this->exactly(2))->method('call')->with('get_information_of_share',
            array('share_id' => $shareid))->will($this->returnValue($expectedresponse));
        $this->set_private_property($mock, 'ocsclient');
        $this->repo->expects($this->once())->method('move_file_to_folder')->with('/merged (3).txt', 'Moodlefiles',
            $mock)->willReturn(array('success' => 201));

        $this->repo->send_file('', '', '', '');

        // Create test for statuscode 403.

        // Checks that setting for foldername are used.
        $mock->expects($this->once())->method('is_dir')->with('Moodlefiles')->willReturn(true);
        // In case of true as return value mkcol is not called to create the folder.
        $shareid = 5;
        $mockocsclient = $this->getMockBuilder(\repository_nextcloud\ocs_client::class
        )->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $mockocsclient->expects($this->exactly(1))->method('call')->with('get_shares',
            array('path' => '/merged (3).txt', 'reshares' => true))->will($this->returnValue($expectedresponse));
        $mockocsclient->expects($this->exactly(1))->method('call')->with('get_information_of_share',
            array('share_id' => $shareid))->will($this->returnValue($expectedresponse));
        $this->set_private_property($mock, 'ocsclient');
        $this->repo->expects($this->once())->method('move_file_to_folder')->with('/merged (3).txt', 'Moodlefiles',
            $mock)->willReturn(array('success' => 201));
        $this->repo->send_file('', '', '', '');
    }

    /**
     * This function provides the data for test_sync_reference
     *
     * @return array[]
     */
    public function sync_reference_provider():array {
        return [
            'referecncelastsync done recently' => [
                [
                    'storedfile_record' => [
                            'contextid' => \context_system::instance()->id,
                            'component' => 'core',
                            'filearea'  => 'unittest',
                            'itemid'    => 0,
                            'filepath'  => '/',
                            'filename'  => 'testfile.txt',
                    ],
                    'storedfile_reference' => json_encode(
                        [
                            'type' => 'FILE_REFERENCE',
                            'link' => 'https://test.local/fakelink/',
                            'usesystem' => true,
                            'referencelastsync' => DAYSECS + time()
                        ]
                    ),
                ],
                'mockfunctions' => ['get_referencelastsync'],
                'expectedresult' => false
            ],
            'file without link' => [
                [
                    'storedfile_record' => [
                        'contextid' => \context_system::instance()->id,
                        'component' => 'core',
                        'filearea'  => 'unittest',
                        'itemid'    => 0,
                        'filepath'  => '/',
                        'filename'  => 'testfile.txt',
                    ],
                    'storedfile_reference' => json_encode(
                        [
                            'type' => 'FILE_REFERENCE',
                            'usesystem' => true,
                        ]
                    ),
                ],
                'mockfunctions' => [],
                'expectedresult' => false
            ],
            'file extenstion to exclude' => [
                [
                    'storedfile_record' => [
                        'contextid' => \context_system::instance()->id,
                        'component' => 'core',
                        'filearea'  => 'unittest',
                        'itemid'    => 0,
                        'filepath'  => '/',
                        'filename'  => 'testfile.txt',
                    ],
                    'storedfile_reference' => json_encode(
                        [
                            'link' => 'https://test.local/fakelink/',
                            'type' => 'FILE_REFERENCE',
                            'usesystem' => true,
                        ]
                    ),
                ],
                'mockfunctions' => [],
                'expectedresult' => false
            ],
            'file extenstion for image' => [
                [
                    'storedfile_record' => [
                        'contextid' => \context_system::instance()->id,
                        'component' => 'core',
                        'filearea'  => 'unittest',
                        'itemid'    => 0,
                        'filepath'  => '/',
                        'filename'  => 'testfile.png',
                    ],
                    'storedfile_reference' => json_encode(
                        [
                            'link' => 'https://test.local/fakelink/',
                            'type' => 'FILE_REFERENCE',
                            'usesystem' => true,
                        ]
                    ),
                    'mock_curl' => true,
                ],
                'mockfunctions' => [''],
                'expectedresult' => true
            ],
        ];
    }

    /**
     * Testing sync_reference
     *
     * @dataProvider sync_reference_provider
     * @param array $storedfileargs
     * @param array $storedfilemethodsmock
     * @param bool $expectedresult
     * @return void
     */
    public function test_sync_reference(array $storedfileargs, $storedfilemethodsmock, bool $expectedresult):void {
        $this->resetAfterTest(true);

        if (isset($storedfilemethodsmock[0])) {
            $storedfile = $this->createMock(\stored_file::class);

            if ($storedfilemethodsmock[0] === 'get_referencelastsync') {
                if (!$expectedresult) {
                    $storedfile->method('get_referencelastsync')->willReturn(DAYSECS + time());
                }
            } else {
                $storedfile->method('get_referencelastsync')->willReturn(null);
            }

            $storedfile->method('get_reference')->willReturn($storedfileargs['storedfile_reference']);
            $storedfile->method('get_filepath')->willReturn($storedfileargs['storedfile_record']['filepath']);
            $storedfile->method('get_filename')->willReturn($storedfileargs['storedfile_record']['filename']);

            if ((isset($storedfileargs['mock_curl']) && $storedfileargs)) {
                // Lets mock curl, else it would not serve the purpose here.
                $curl = $this->createMock(\curl::class);
                $curl->method('download_one')->willReturn(true);
                $curl->method('get_info')->willReturn(['http_code' => 200]);

                $reflectionproperty = new \ReflectionProperty($this->repo, 'curl');
                $reflectionproperty->setAccessible(true);
                $reflectionproperty->setValue($this->repo, $curl);
            }
        } else {
            $fs = get_file_storage();
            $storedfile = $fs->create_file_from_reference(
                $storedfileargs['storedfile_record'],
                $this->repo->id,
                $storedfileargs['storedfile_reference']);
        }

        $actualresult = $this->repo->sync_reference($storedfile);
        $this->assertEquals($expectedresult, $actualresult);
    }

    /**
     * Helper method, which inserts a given mock value into the repository_nextcloud object.
     *
     * @param mixed $value mock value that will be inserted.
     * @param string $propertyname name of the private property.
     * @return ReflectionProperty the resulting reflection property.
     */
    protected function set_private_property($value, $propertyname) {
        $refclient = new \ReflectionClass($this->repo);
        $private = $refclient->getProperty($propertyname);
        $private->setAccessible(true);
        $private->setValue($this->repo, $value);

        return $private;
    }
    /**
     * Helper method to set required return parameters for get_listing.
     *
     * @return array array, which contains the parameters.
     */
    protected function get_initialised_return_array() {
        $ret = array();
        $ret['dynload'] = true;
        $ret['nosearch'] = true;
        $ret['nologin'] = false;
        $ret['path'] = [
            [
                'name' => $this->repo->get_meta()->name,
                'path' => '',
            ]
        ];
        $ret['manage'] = '';
        $ret['defaultreturntype'] = FILE_INTERNAL;
        $ret['list'] = array();

        $ret['filereferencewarning'] = get_string('externalpubliclinkwarning', 'repository_nextcloud');

        return $ret;
    }
}
