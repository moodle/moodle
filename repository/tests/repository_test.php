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
 * Repository API unit tests
 *
 * @package   repository
 * @category  phpunit
 * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->dirroot/repository/lib.php");

class repositorylib_testcase extends advanced_testcase {

    /**
     * Installing repository tests
     *
     * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
     */
    public function test_install_repository() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $syscontext = context_system::instance();
        $repositorypluginname = 'boxnet';
        // override repository permission
        $capability = 'repository/' . $repositorypluginname . ':view';
        $allroles = $DB->get_records_menu('role', array(), 'id', 'archetype, id');
        assign_capability($capability, CAP_ALLOW, $allroles['guest'], $syscontext->id, true);

        $plugintype = new repository_type($repositorypluginname);
        $pluginid = $plugintype->create(false);
        $this->assertInternalType('int', $pluginid);
        $args = array();
        $args['type'] = $repositorypluginname;
        $repos = repository::get_instances($args);
        $repository = reset($repos);
        $this->assertInstanceOf('repository', $repository);
        $info = $repository->get_meta();
        $this->assertEquals($repositorypluginname, $info->type);
    }

    public function test_check_capability() {
        $this->resetAfterTest(true);

        $syscontext = context_system::instance();
        $course1 = $this->getDataGenerator()->create_course();
        $course1context = context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course();
        $course2context = context_course::instance($course2->id);

        $forumdata = new stdClass();
        $forumdata->course = $course1->id;
        $forumc1 = $this->getDataGenerator()->create_module('forum', $forumdata);
        $forumc1context = context_module::instance($forumc1->id);
        $forumdata->course = $course2->id;
        $forumc2 = $this->getDataGenerator()->create_module('forum', $forumdata);
        $forumc2context = context_module::instance($forumc2->id);

        $blockdata = new stdClass();
        $blockdata->parentcontextid = $course1context->id;
        $blockc1 = $this->getDataGenerator()->create_block('online_users', $blockdata);
        $blockc1context = context_block::instance($blockc1->id);
        $blockdata->parentcontextid = $course2context->id;
        $blockc2 = $this->getDataGenerator()->create_block('online_users', $blockdata);
        $blockc2context = context_block::instance($blockc2->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user1context = context_user::instance($user1->id);
        $user2 = $this->getDataGenerator()->create_user();
        $user2context = context_user::instance($user2->id);

        // New role prohibiting Flickr Public access.
        $roleid = create_role('No Flickr Public', 'noflickrpublic', 'No Flickr Public', '');
        set_role_contextlevels($roleid, array(CONTEXT_SYSTEM, CONTEXT_COURSE));
        assign_capability('repository/flickr_public:view', CAP_PROHIBIT, $roleid, $syscontext, true);

        // Disallow system access to Flickr Public to user 2.
        role_assign($roleid, $user2->id, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Enable repositories.
        $plugintype = new repository_type('flickr_public');
        $plugintype->create(true);
        $plugintype = new repository_type('dropbox');
        $plugintype->create(true);
        $params = array(
            'name' => 'Flickr Public'
        );

        // Instance on a site level.
        $repoid = repository::static_function('flickr_public', 'create', 'flickr_public', 0, $syscontext, $params);
        $systemrepo = repository::get_repository_by_id($repoid, $syscontext);

        // Check that everyone with right capability can view a site-wide repository.
        $this->setUser($user1);
        $this->assertTrue($systemrepo->check_capability());

        // Without the capability, we cannot view a site-wide repository.
        $this->setUser($user2);
        $caughtexception = false;
        try {
            $systemrepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);

        // Instance on a course level.
        $courserepoid = repository::static_function('flickr_public', 'create', 'flickr_public', 0, $course1context, $params);

        // Within the course, I can view the repository.
        $courserepo = repository::get_repository_by_id($courserepoid, $course1context);
        $this->setUser($user1);
        $this->assertTrue($courserepo->check_capability());
        // But not without the capability.
        $this->setUser($user2);
        $caughtexception = false;
        try {
            $courserepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);

        // From another course I cannot, with or without the capability.
        $courserepo = repository::get_repository_by_id($courserepoid, $course2context);
        $this->setUser($user1);
        $caughtexception = false;
        try {
            $courserepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);
        $this->setUser($user2);
        $caughtexception = false;
        try {
            $courserepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);

        // From a module within the course, I can view the repository.
        $courserepo = repository::get_repository_by_id($courserepoid, $forumc1context);
        $this->setUser($user1);
        $this->assertTrue($courserepo->check_capability());
        // But not without the capability.
        $this->setUser($user2);
        $caughtexception = false;
        try {
            $courserepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);

        // From a module in the wrong course, I cannot view the repository.
        $courserepo = repository::get_repository_by_id($courserepoid, $forumc2context);
        $this->setUser($user1);
        $caughtexception = false;
        try {
            $courserepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);

        // From a block within the course, I can view the repository.
        $courserepo = repository::get_repository_by_id($courserepoid, $blockc1context);
        $this->setUser($user1);
        $this->assertTrue($courserepo->check_capability());
        // But not without the capability.
        $this->setUser($user2);
        $caughtexception = false;
        try {
            $courserepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);

        // From a block in the wrong course, I cannot view the repository.
        $courserepo = repository::get_repository_by_id($courserepoid, $blockc2context);
        $this->setUser($user1);
        $caughtexception = false;
        try {
            $courserepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);

        // Instance on a user level.
        $user1repoid = repository::static_function('flickr_public', 'create', 'flickr_public', 0, $user1context, $params);
        $user2repoid = repository::static_function('flickr_public', 'create', 'flickr_public', 0, $user2context, $params);

        // Check that a user can see its own repository.
        $userrepo = repository::get_repository_by_id($user1repoid, $syscontext);
        $this->setUser($user1);
        $this->assertTrue($userrepo->check_capability());
        // But not without the capability.
        $userrepo = repository::get_repository_by_id($user2repoid, $syscontext);
        $this->setUser($user2);
        $caughtexception = false;
        try {
            $userrepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);

        // Check that a user cannot see someone's repository.
        $userrepo = repository::get_repository_by_id($user2repoid, $syscontext);
        $this->setUser($user1);
        $caughtexception = false;
        try {
            $userrepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);
        // Make sure the repo from user 2 was accessible.
        role_unassign($roleid, $user2->id, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($user2);
        $this->assertTrue($userrepo->check_capability());
        role_assign($roleid, $user2->id, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Check that a user can view SOME repositories when logged in as someone else.
        $params = new stdClass();
        $params->name = 'Dropbox';
        $params->dropbox_key = 'key';
        $params->dropbox_secret = 'secret';
        $privaterepoid = repository::static_function('dropbox', 'create', 'dropbox', 0, $syscontext, $params);
        $params = new stdClass();
        $params->name = 'Upload';
        $notprivaterepoid = repository::static_function('upload', 'create', 'upload', 0, $syscontext, $params);

        $privaterepo = repository::get_repository_by_id($privaterepoid, $syscontext);
        $notprivaterepo = repository::get_repository_by_id($notprivaterepoid, $syscontext);
        $userrepo = repository::get_repository_by_id($user1repoid, $syscontext);

        $this->setAdminUser();
        session_loginas($user1->id, $syscontext);

        // Logged in as, I cannot view a user instance.
        $caughtexception = false;
        try {
            $userrepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);

        // Logged in as, I cannot view a private instance.
        $caughtexception = false;
        try {
            $privaterepo->check_capability();
        } catch (repository_exception $e) {
            $caughtexception = true;
        }
        $this->assertTrue($caughtexception);

        // Logged in as, I can view a non-private instance.
        $this->assertTrue($notprivaterepo->check_capability());
    }

    function test_delete_all_for_context() {
        global $DB;
        $this->resetAfterTest(true);

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();

        // Enable repositories.
        $plugintype = new repository_type('flickr_public');
        $plugintype->create(true);
        $plugintype = new repository_type('filesystem');
        $plugintype->create(true);
        $coursecontext = context_course::instance($course->id);
        $usercontext = context_user::instance($user->id);
        $flickrparams = array('name' => 'Flickr Public');
        $fsparams = array('name' => 'File System');

        // Creating course instances.
        // Instance on a site level.
        $repoid = repository::static_function('flickr_public', 'create', 'flickr_public', 0, $coursecontext, $flickrparams);
        $courserepo1 = repository::get_repository_by_id($repoid, $coursecontext);
        $this->assertEquals(1, $DB->count_records('repository_instances', array('contextid' => $coursecontext->id)));

        $repoid = repository::static_function('filesystem', 'create', 'filesystem', 0, $coursecontext, $fsparams);
        $courserepo2 = repository::get_repository_by_id($repoid, $coursecontext);
        $this->assertEquals(2, $DB->count_records('repository_instances', array('contextid' => $coursecontext->id)));

        // Creating user instances.
        $repoid = repository::static_function('flickr_public', 'create', 'flickr_public', 0, $usercontext, $flickrparams);
        $userrepo1 = repository::get_repository_by_id($repoid, $usercontext);
        $this->assertEquals(1, $DB->count_records('repository_instances', array('contextid' => $usercontext->id)));

        $repoid = repository::static_function('filesystem', 'create', 'filesystem', 0, $usercontext, $fsparams);
        $userrepo2 = repository::get_repository_by_id($repoid, $usercontext);
        $this->assertEquals(2, $DB->count_records('repository_instances', array('contextid' => $usercontext->id)));

        // Simulation of course deletion.
        repository::delete_all_for_context($coursecontext->id);
        $this->assertEquals(0, $DB->count_records('repository_instances', array('contextid' => $coursecontext->id)));
        $this->assertEquals(0, $DB->count_records('repository_instances', array('id' => $courserepo1->id)));
        $this->assertEquals(0, $DB->count_records('repository_instances', array('id' => $courserepo2->id)));
        $this->assertEquals(0, $DB->count_records('repository_instance_config', array('instanceid' => $courserepo1->id)));
        $this->assertEquals(0, $DB->count_records('repository_instance_config', array('instanceid' => $courserepo2->id)));

        // Simulation of user deletion.
        repository::delete_all_for_context($usercontext->id);
        $this->assertEquals(0, $DB->count_records('repository_instances', array('contextid' => $usercontext->id)));
        $this->assertEquals(0, $DB->count_records('repository_instances', array('id' => $userrepo1->id)));
        $this->assertEquals(0, $DB->count_records('repository_instances', array('id' => $userrepo2->id)));
        $this->assertEquals(0, $DB->count_records('repository_instance_config', array('instanceid' => $userrepo1->id)));
        $this->assertEquals(0, $DB->count_records('repository_instance_config', array('instanceid' => $userrepo2->id)));

        // Checking deletion upon course context deletion.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $repoid = repository::static_function('flickr_public', 'create', 'flickr_public', 0, $coursecontext, $flickrparams);
        $courserepo = repository::get_repository_by_id($repoid, $coursecontext);
        $this->assertEquals(1, $DB->count_records('repository_instances', array('contextid' => $coursecontext->id)));
        $coursecontext->delete();
        $this->assertEquals(0, $DB->count_records('repository_instances', array('contextid' => $coursecontext->id)));

        // Checking deletion upon user context deletion.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user->id);
        $repoid = repository::static_function('flickr_public', 'create', 'flickr_public', 0, $usercontext, $flickrparams);
        $userrepo = repository::get_repository_by_id($repoid, $usercontext);
        $this->assertEquals(1, $DB->count_records('repository_instances', array('contextid' => $usercontext->id)));
        $usercontext->delete();
        $this->assertEquals(0, $DB->count_records('repository_instances', array('contextid' => $usercontext->id)));

        // Checking deletion upon course deletion.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $repoid = repository::static_function('flickr_public', 'create', 'flickr_public', 0, $coursecontext, $flickrparams);
        $courserepo = repository::get_repository_by_id($repoid, $coursecontext);
        $this->assertEquals(1, $DB->count_records('repository_instances', array('contextid' => $coursecontext->id)));
        delete_course($course, false);
        $this->assertEquals(0, $DB->count_records('repository_instances', array('contextid' => $coursecontext->id)));

        // Checking deletion upon user deletion.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user->id);
        $repoid = repository::static_function('flickr_public', 'create', 'flickr_public', 0, $usercontext, $flickrparams);
        $userrepo = repository::get_repository_by_id($repoid, $usercontext);
        $this->assertEquals(1, $DB->count_records('repository_instances', array('contextid' => $usercontext->id)));
        delete_user($user);
        $this->assertEquals(0, $DB->count_records('repository_instances', array('contextid' => $usercontext->id)));
    }
}
