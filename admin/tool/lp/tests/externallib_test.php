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
 * External learning plans webservice API tests.
 *
 * @package tool_lp
 * @copyright 2015 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use tool_lp\external;
use tool_lp\plan;

/**
 * External learning plans webservice API tests.
 *
 * @package tool_lp
 * @copyright 2015 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_external_testcase extends externallib_advanced_testcase {

    /** @var stdClass $creator User with enough permissions to create insystem context. */
    protected $creator = null;

    /** @var stdClass $learningplancreator User with enough permissions to create incategory context. */
    protected $catcreator = null;

    /** @var stdClass $category Category */
    protected $category = null;

    /** @var stdClass $user User with enough permissions to view insystem context */
    protected $user = null;

    /** @var stdClass $catuser User with enough permissions to view incategory context */
    protected $catuser = null;

    /** @var int Creator role id */
    protected $creatorrole = null;

    /** @var int User role id */
    protected $userrole = null;

    /** @var string scaleconfiguration */
    protected $scaleconfiguration1 = null;

    /** @var string scaleconfiguration */
    protected $scaleconfiguration2 = null;

    /** @var string catscaleconfiguration */
    protected $scaleconfiguration3 = null;

    /** @var string catscaleconfiguration */
    protected $catscaleconfiguration4 = null;

    /**
     * Setup function- we will create a course and add an assign instance to it.
     */
    protected function setUp() {
        global $DB;

        $this->resetAfterTest(true);

        // Create some users.
        $creator = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();
        $catuser = $this->getDataGenerator()->create_user();
        $category = $this->getDataGenerator()->create_category();
        $catcreator = $this->getDataGenerator()->create_user();

        $syscontext = context_system::instance();
        $catcontext = context_coursecat::instance($category->id);

        // Fetching default authenticated user role.
        $userroles = get_archetype_roles('user');
        $this->assertCount(1, $userroles);
        $authrole = array_pop($userroles);

        // Reset all default authenticated users permissions.
        unassign_capability('tool/lp:competencymanage', $authrole->id);
        unassign_capability('tool/lp:competencyread', $authrole->id);
        unassign_capability('tool/lp:planmanageall', $authrole->id);
        unassign_capability('tool/lp:planmanageown', $authrole->id);
        unassign_capability('tool/lp:planviewall', $authrole->id);
        unassign_capability('tool/lp:templatemanage', $authrole->id);
        unassign_capability('tool/lp:templateread', $authrole->id);

        // Creating specific roles.
        $this->creatorrole = create_role('Creator role', 'creatorrole', 'learning plan creator role description');
        $this->userrole = create_role('User role', 'userrole', 'learning plan user role description');

        assign_capability('tool/lp:competencymanage', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('tool/lp:competencyread', CAP_ALLOW, $this->userrole, $syscontext->id);
        assign_capability('tool/lp:planmanageall', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('tool/lp:planviewall', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('tool/lp:templatemanage', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('tool/lp:templateread', CAP_ALLOW, $this->userrole, $syscontext->id);

        role_assign($this->creatorrole, $creator->id, $syscontext->id);
        role_assign($this->creatorrole, $catcreator->id, $catcontext->id);
        role_assign($this->userrole, $user->id, $syscontext->id);
        role_assign($this->userrole, $catuser->id, $catcontext->id);

        $this->creator = $creator;
        $this->catcreator = $catcreator;
        $this->user = $user;
        $this->catuser = $catuser;
        $this->category = $category;
        $this->scaleconfiguration1 = '[{"scaleid":"1"},{"name":"value1","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"value2","id":2,"scaledefault":0,"proficient":1}]';
        $this->scaleconfiguration2 = '[{"scaleid":"2"},{"name":"value3","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"value4","id":2,"scaledefault":0,"proficient":1}]';
        $this->scaleconfiguration3 = '[{"scaleid":"3"},{"name":"value5","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"value6","id":2,"scaledefault":0,"proficient":1}]';
        $this->scaleconfiguration4 = '[{"scaleid":"4"},{"name":"value8","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"value8","id":2,"scaledefault":0,"proficient":1}]';
        accesslib_clear_all_caches_for_unit_testing();
    }

    /**
     * Test we can't create a competency framework with only read permissions.
     */
    public function test_create_competency_frameworks_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->user);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
    }

    /**
     * Test we can't create a competency framework with only read permissions.
     */
    public function test_create_competency_frameworks_with_read_permissions_in_category() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->catuser);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
    }

    /**
     * Test we can create a competency framework with manage permissions.
     */
    public function test_create_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(1, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can create a competency framework with manage permissions.
     */
    public function test_create_competency_frameworks_with_manage_permissions_in_category() {
        $this->setUser($this->catcreator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->catcreator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(1, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);

        try {
            external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 2,
                $this->scaleconfiguration2, true, array('contextid' => context_system::instance()->id));
            $this->fail('User cannot create a framework at system level.');
        } catch (required_capability_exception $e) {
        }
    }

    /**
     * Test we cannot create a competency framework with nasty data.
     */
    public function test_create_competency_frameworks_with_nasty_data() {
        $this->setUser($this->creator);
        $this->setExpectedException('invalid_parameter_exception');
        $result = external::create_competency_framework('short<a href="">', 'id;"number', 'de<>\\..scription', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
    }

    /**
     * Test we can read a competency framework with manage permissions.
     */
    public function test_read_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $id = $result->id;
        $result = external::read_competency_framework($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(1, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can read a competency framework with manage permissions.
     */
    public function test_read_competency_frameworks_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $insystem = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency_framework('catshortname', 'catidnumber', 'catdescription', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
        $incat = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $this->setUser($this->catcreator);
        $id = $incat->id;
        $result = external::read_competency_framework($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('catshortname', $result->shortname);
        $this->assertEquals('catidnumber', $result->idnumber);
        $this->assertEquals('catdescription', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(2, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration2, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);

        try {
            $id = $insystem->id;
            $result = external::read_competency_framework($id);
            $result = (object) external_api::clean_returnvalue(external::read_competency_framework_returns(), $result);
            $this->fail('User cannot read a framework at system level.');
        } catch (required_capability_exception $e) {
        }
    }

    /**
     * Test we can read a competency framework with read permissions.
     */
    public function test_read_competency_frameworks_with_read_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        // Switch users to someone with less permissions.
        $this->setUser($this->user);
        $id = $result->id;
        $result = external::read_competency_framework($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(1, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);
    }
    /**
     * Test we can read a competency framework with read permissions.
     */
    public function test_read_competency_frameworks_with_read_permissions_in_category() {
        $this->setUser($this->creator);

        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $insystem = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency_framework('catshortname', 'catidnumber', 'catdescription', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
        $incat = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        // Switch users to someone with less permissions.
        $this->setUser($this->catuser);
        $id = $incat->id;
        $result = external::read_competency_framework($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('catshortname', $result->shortname);
        $this->assertEquals('catidnumber', $result->idnumber);
        $this->assertEquals('catdescription', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(2, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration2, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);

        // Switching to user with no permissions.
        try {
            $result = external::read_competency_framework($insystem->id);
            $this->fail('Current user cannot should not be able to read the framework.');
        } catch (required_capability_exception $e) {
        }
    }

    /**
     * Test we can delete a competency framework with manage permissions.
     */
    public function test_delete_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $id = $result->id;
        $result = external::delete_competency_framework($id);
        $result = external_api::clean_returnvalue(external::delete_competency_framework_returns(), $result);

        $this->assertTrue($result);
    }

    /**
     * Test we can delete a competency framework with manage permissions.
     */
    public function test_delete_competency_frameworks_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $insystem = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency_framework('catshortname', 'catidnumber', 'catdescription', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
        $incat = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $this->setUser($this->catcreator);
        $id = $incat->id;
        $result = external::delete_competency_framework($id);
        $result = external_api::clean_returnvalue(external::delete_competency_framework_returns(), $result);

        $this->assertTrue($result);

        try {
            $id = $insystem->id;
            $result = external::delete_competency_framework($id);
            $result = external_api::clean_returnvalue(external::delete_competency_framework_returns(), $result);
            $this->fail('Current user cannot should not be able to delete the framework.');
        } catch (required_capability_exception $e) {
        }
    }

    /**
     * Test we can delete a competency framework with read permissions.
     */
    public function test_delete_competency_frameworks_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $id = $result->id;
        // Switch users to someone with less permissions.
        $this->setUser($this->user);
        $result = external::delete_competency_framework($id);
    }

    /**
     * Test we can update a competency framework with manage permissions.
     */
    public function test_update_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $result = external::update_competency_framework($result->id, 'shortname2',
                'idnumber2', 'description2', FORMAT_PLAIN, 2, $this->scaleconfiguration2, false);
        $result = external_api::clean_returnvalue(external::update_competency_framework_returns(), $result);

        $this->assertTrue($result);
    }

    /**
     * Test we can update a competency framework with manage permissions.
     */
    public function test_update_competency_frameworks_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $insystem = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency_framework('catshortname', 'catidnumber', 'catdescription', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
        $incat = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $this->setUser($this->catcreator);
        $id = $incat->id;

        $result = external::update_competency_framework($id, 'shortname2', 'idnumber2', 'description2', FORMAT_PLAIN, 3,
             $this->scaleconfiguration3, false);
        $result = external_api::clean_returnvalue(external::update_competency_framework_returns(), $result);

        $this->assertTrue($result);

        try {
            $id = $insystem->id;
            $result = external::update_competency_framework($id, 'shortname3', 'idnumber3', 'description3', FORMAT_PLAIN, 4,
                 $this->scaleconfiguration4,  false);
            $result = external_api::clean_returnvalue(external::update_competency_framework_returns(), $result);
            $this->fail('Current user cannot should not be able to update the framework.');
        } catch (required_capability_exception $e) {
        }
    }

    /**
     * Test we can update a competency framework with read permissions.
     */
    public function test_update_competency_frameworks_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $this->setUser($this->user);
        $result = external::update_competency_framework($result->id, 'shortname2',
                'idnumber2', 'description2', FORMAT_PLAIN, 2, $this->scaleconfiguration2, false);
    }

    /**
     * Test we can list and count competency frameworks with manage permissions.
     */
    public function test_list_and_count_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $result = external::create_competency_framework('shortname2', 'idnumber2', 'description', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_system::instance()->id));
        $result = external::create_competency_framework('shortname3', 'idnumber3', 'description', FORMAT_HTML, 3,
            $this->scaleconfiguration3, true, array('contextid' => context_system::instance()->id));
        $result = external::create_competency_framework('shortname4', 'idnumber4', 'description', FORMAT_HTML, 4,
            $this->scaleconfiguration4, true, array('contextid' => context_coursecat::instance($this->category->id)->id));

        $result = external::count_competency_frameworks(array('contextid' => context_system::instance()->id), 'self');
        $result = external_api::clean_returnvalue(external::count_competency_frameworks_returns(), $result);

        $this->assertEquals($result, 3);

        $result = external::list_competency_frameworks('shortname', 'ASC', 0, 10,
            array('contextid' => context_system::instance()->id), 'self');
        $result = external_api::clean_returnvalue(external::list_competency_frameworks_returns(), $result);

        $this->assertEquals(count($result), 3);
        $result = (object) $result[0];

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(1, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can list and count competency frameworks with read permissions.
     */
    public function test_list_and_count_competency_frameworks_with_read_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $result = external::create_competency_framework('shortname2', 'idnumber2', 'description', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_system::instance()->id));
        $result = external::create_competency_framework('shortname3', 'idnumber3', 'description', FORMAT_HTML, 3,
            $this->scaleconfiguration3, true, array('contextid' => context_system::instance()->id));
        $result = external::create_competency_framework('shortname4', 'idnumber4', 'description', FORMAT_HTML, 4,
            $this->scaleconfiguration4, true, array('contextid' => context_coursecat::instance($this->category->id)->id));

        $this->setUser($this->user);
        $result = external::count_competency_frameworks(array('contextid' => context_system::instance()->id), 'self');
        $result = external_api::clean_returnvalue(external::count_competency_frameworks_returns(), $result);
        $this->assertEquals($result, 3);

        $result = external::list_competency_frameworks('shortname', 'ASC', 0, 10,
            array('contextid' => context_system::instance()->id), 'self');
        $result = external_api::clean_returnvalue(external::list_competency_frameworks_returns(), $result);

        $this->assertEquals(count($result), 3);
        $result = (object) $result[0];

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(1, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can't create a competency with only read permissions.
     */
    public function test_create_competency_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $this->setUser($this->user);
        $competency = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
    }

    /**
     * Test we can create a competency with manage permissions.
     */
    public function test_create_competency_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);

        $competency = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $competency = (object) external_api::clean_returnvalue(external::create_competency_returns(), $competency);

        $this->assertGreaterThan(0, $competency->timecreated);
        $this->assertGreaterThan(0, $competency->timemodified);
        $this->assertEquals($this->creator->id, $competency->usermodified);
        $this->assertEquals('shortname', $competency->shortname);
        $this->assertEquals('idnumber', $competency->idnumber);
        $this->assertEquals('description', $competency->description);
        $this->assertEquals(FORMAT_HTML, $competency->descriptionformat);
        $this->assertEquals(true, $competency->visible);
        $this->assertEquals(0, $competency->parentid);
        $this->assertEquals($framework->id, $competency->competencyframeworkid);
    }


    /**
     * Test we can create a competency with manage permissions.
     */
    public function test_create_competency_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $insystem = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency_framework('catshortname', 'catidnumber', 'catdescription', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
        $incat = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $this->setUser($this->catcreator);

        $competency = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $incat->id, 0);
        $competency = (object) external_api::clean_returnvalue(external::create_competency_returns(), $competency);

        $this->assertGreaterThan(0, $competency->timecreated);
        $this->assertGreaterThan(0, $competency->timemodified);
        $this->assertEquals($this->catcreator->id, $competency->usermodified);
        $this->assertEquals('shortname', $competency->shortname);
        $this->assertEquals('idnumber', $competency->idnumber);
        $this->assertEquals('description', $competency->description);
        $this->assertEquals(FORMAT_HTML, $competency->descriptionformat);
        $this->assertEquals(true, $competency->visible);
        $this->assertEquals(0, $competency->parentid);
        $this->assertEquals($incat->id, $competency->competencyframeworkid);

        try {
            $competency = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $insystem->id, 0);
            $competency = (object) external_api::clean_returnvalue(external::create_competency_returns(), $competency);
            $this->fail('User should not be able to create a competency in system context.');
        } catch (required_capability_exception $e) {
        }
    }

    /**
     * Test we cannot create a competency with nasty data.
     */
    public function test_create_competency_with_nasty_data() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $this->setExpectedException('invalid_parameter_exception');
        $competency = external::create_competency('shortname<a href="">', 'id;"number',
                'de<>\\..scription', FORMAT_HTML, true, $framework->id, 0);
    }

    /**
     * Test we can read a competency with manage permissions.
     */
    public function test_read_competencies_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $id = $result->id;
        $result = external::read_competency($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(true, $result->visible);
        $this->assertEquals(0, $result->parentid);
        $this->assertEquals($framework->id, $result->competencyframeworkid);
    }

    /**
     * Test we can read a competency with manage permissions.
     */
    public function test_read_competencies_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $sysframework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $result->id, 0);
        $insystem = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $result = external::create_competency_framework('catshortname', 'catidnumber', 'catdescription', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
        $catframework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $result->id, 0);
        $incat = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $this->setUser($this->catcreator);
        $id = $incat->id;
        $result = external::read_competency($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(true, $result->visible);
        $this->assertEquals(0, $result->parentid);
        $this->assertEquals($catframework->id, $result->competencyframeworkid);

        try {
            external::read_competency($insystem->id);
            $this->fail('User should not be able to read a competency in system context.');
        } catch (required_capability_exception $e) {
        }
    }

    /**
     * Test we can read a competency with read permissions.
     */
    public function test_read_competencies_with_read_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        // Switch users to someone with less permissions.
        $this->setUser($this->user);
        $id = $result->id;
        $result = external::read_competency($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(true, $result->visible);
        $this->assertEquals(0, $result->parentid);
        $this->assertEquals($framework->id, $result->competencyframeworkid);
    }

    /**
     * Test we can read a competency with read permissions.
     */
    public function test_read_competencies_with_read_permissions_in_category() {
        $this->setUser($this->creator);

        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $sysframework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $result->id, 0);
        $insystem = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $result = external::create_competency_framework('catshortname', 'catidnumber', 'catdescription', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
        $catframework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $result->id, 0);
        $incat = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        // Switch users to someone with less permissions.
        $this->setUser($this->catuser);
        $id = $incat->id;
        $result = external::read_competency($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(true, $result->visible);
        $this->assertEquals(0, $result->parentid);
        $this->assertEquals($catframework->id, $result->competencyframeworkid);

        try {
            external::read_competency($insystem->id);
            $this->fail('User should not be able to read a competency in system context.');
        } catch (required_capability_exception $e) {
        }
    }

    /**
     * Test we can delete a competency with manage permissions.
     */
    public function test_delete_competency_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $id = $result->id;
        $result = external::delete_competency($id);
        $result = external_api::clean_returnvalue(external::delete_competency_returns(), $result);

        $this->assertTrue($result);
    }

    /**
     * Test we can delete a competency with manage permissions.
     */
    public function test_delete_competency_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $sysframework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $result->id, 0);
        $insystem = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $result = external::create_competency_framework('catshortname', 'catidnumber', 'catdescription', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
        $catframework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $result->id, 0);
        $incat = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $this->setUser($this->catcreator);
        $id = $incat->id;
        $result = external::delete_competency($id);
        $result = external_api::clean_returnvalue(external::delete_competency_returns(), $result);

        $this->assertTrue($result);

        try {
            $result = external::delete_competency($insystem->id);
            $this->fail('User should not be able to delete a competency in system context.');
        } catch (required_capability_exception $e) {
        }
    }

    /**
     * Test we can delete a competency with read permissions.
     */
    public function test_delete_competency_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $id = $result->id;
        // Switch users to someone with less permissions.
        $this->setUser($this->user);
        $result = external::delete_competency($id);
    }

    /**
     * Test we can update a competency with manage permissions.
     */
    public function test_update_competency_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $result = external::update_competency($result->id, 'shortname2', 'idnumber2', 'description2', FORMAT_HTML, false);
        $result = external_api::clean_returnvalue(external::update_competency_returns(), $result);

        $this->assertTrue($result);
    }

    /**
     * Test we can update a competency with manage permissions.
     */
    public function test_update_competency_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $sysframework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $result->id, 0);
        $insystem = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $result = external::create_competency_framework('catshortname', 'catidnumber', 'catdescription', FORMAT_HTML, 2,
            $this->scaleconfiguration2, true, array('contextid' => context_coursecat::instance($this->category->id)->id));
        $catframework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $result->id, 0);
        $incat = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $this->setUser($this->catcreator);

        $result = external::update_competency($incat->id, 'shortname2', 'idnumber2', 'description2', FORMAT_HTML, false);
        $result = external_api::clean_returnvalue(external::update_competency_returns(), $result);

        $this->assertTrue($result);

        try {
            external::update_competency($insystem->id, 'shortname2', 'idnumber2', 'description2', FORMAT_HTML, false);
            $this->fail('User should not be able to update a competency in system context.');
        } catch (required_capability_exception $e) {
        }
    }

    /**
     * Test we can update a competency with read permissions.
     */
    public function test_update_competency_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $this->setUser($this->user);
        $result = external::update_competency($result->id, 'shortname2', 'idnumber2', 'description2', FORMAT_HTML, false);
    }

    /**
     * Test we can list and count competencies with manage permissions.
     */
    public function test_list_and_count_competencies_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = external::create_competency('shortname2', 'idnumber2', 'description2', FORMAT_HTML, true, $framework->id, 0);
        $result = external::create_competency('shortname3', 'idnumber3', 'description3', FORMAT_HTML, true, $framework->id, 0);

        $result = external::count_competencies(array());
        $result = external_api::clean_returnvalue(external::count_competencies_returns(), $result);

        $this->assertEquals($result, 3);

        array('id' => $result = external::list_competencies(array(), 'shortname', 'ASC', 0, 10, context_system::instance()->id));
        $result = external_api::clean_returnvalue(external::list_competencies_returns(), $result);

        $this->assertEquals(count($result), 3);
        $result = (object) $result[0];

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can list and count competencies with read permissions.
     */
    public function test_list_and_count_competencies_with_read_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = external::create_competency('shortname2', 'idnumber2', 'description2', FORMAT_HTML, true, $framework->id, 0);
        $result = external::create_competency('shortname3', 'idnumber3', 'description3', FORMAT_HTML, true, $framework->id, 0);

        $this->setUser($this->user);

        $result = external::count_competencies(array());
        $result = external_api::clean_returnvalue(external::count_competencies_returns(), $result);

        $this->assertEquals($result, 3);

        array('id' => $result = external::list_competencies(array(), 'shortname', 'ASC', 0, 10, context_system::instance()->id));
        $result = external_api::clean_returnvalue(external::list_competencies_returns(), $result);

        $this->assertEquals(count($result), 3);
        $result = (object) $result[0];

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can search for competencies.
     */
    public function test_search_competencies_with_read_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = external::create_competency('shortname2', 'idnumber2', 'description2', FORMAT_HTML, true, $framework->id, 0);
        $result = external::create_competency('shortname3', 'idnumber3', 'description3', FORMAT_HTML, true, $framework->id, 0);

        $this->setUser($this->user);

        $result = external::search_competencies('short', $framework->id);
        $result = external_api::clean_returnvalue(external::search_competencies_returns(), $result);

        $this->assertEquals(count($result), 3);
        $result = (object) $result[0];

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test plans creation and updates.
     */
    public function test_create_and_update_plans() {
        $syscontext = context_system::instance();

        $this->setUser($this->creator);
        $plan0 = external::create_plan('Complete plan', 'A description',
                FORMAT_HTML, $this->creator->id, 0, plan::STATUS_COMPLETE, 0);

        $this->setUser($this->user);

        try {
            $plan1 = external::create_plan('Draft plan (they can not with the default capabilities)',
                    'A description', FORMAT_HTML, $this->user->id, 0, plan::STATUS_DRAFT, 0);
            $this->fail('Exception expected due to not permissions to create draft plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        assign_capability('tool/lp:plancreatedraft', CAP_ALLOW, $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($this->user);

        $plan2 = external::create_plan('Draft plan', 'A description', FORMAT_HTML, $this->user->id, 0, plan::STATUS_DRAFT, 0);

        try {
            $plan3 = external::create_plan('Active plan (they can not)', 'A description',
                    FORMAT_HTML, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
            $this->fail('Exception expected due to not permissions to create active plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
        try {
            $plan3 = external::update_plan($plan2['id'], 'Updated active plan', 'A description',
                    FORMAT_HTML, $this->user->id, 0, plan::STATUS_COMPLETE, 0);
            $this->fail('Exception expected due to not permissions to update plans to complete status');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $plan3 = external::create_plan('Active plan', 'A description', FORMAT_HTML, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        $plan4 = external::create_plan('Complete plan', 'A description', FORMAT_HTML, $this->user->id, 0, plan::STATUS_COMPLETE, 0);
        try {
            $plan4 = external::create_plan('Plan for another user', 'A description',
                    FORMAT_HTML, $this->creator->id, 0, plan::STATUS_COMPLETE, 0);
            $this->fail('Exception expected due to not permissions to manage other users plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        try {
            $plan0 = external::update_plan($plan0['id'], 'Can not update other users plans',
                    'A description', FORMAT_HTML, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        unassign_capability('tool/lp:planmanageown', $this->userrole, $syscontext->id);
        unassign_capability('tool/lp:plancreatedraft', $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            $plan1 = external::update_plan($plan2['id'], 'Can not be updated even if they created it',
                    'A description', FORMAT_HTML, $this->user->id, 0, plan::STATUS_COMPLETE, 0);
            $this->fail('Exception expected due to not permissions to create draft plan');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
    }

    /**
     * Test that we can read plans.
     */
    public function test_read_plans() {
        $this->setUser($this->creator);

        $syscontext = context_system::instance();

        $plan1 = external::create_plan('Plan draft by creator', 'A description',
                FORMAT_HTML, $this->user->id, 0, plan::STATUS_DRAFT, 0);
        $plan2 = external::create_plan('Plan active by creator', 'A description',
                FORMAT_HTML, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        $plan3 = external::create_plan('Plan complete by creator', 'A description',
                FORMAT_HTML, $this->user->id, 0, plan::STATUS_COMPLETE, 0);

        $this->assertEquals((Array)$plan1, external::read_plan($plan1['id']));
        $this->assertEquals((Array)$plan2, external::read_plan($plan2['id']));
        $this->assertEquals((Array)$plan3, external::read_plan($plan3['id']));

        $this->setUser($this->user);

        // The normal user can not edit these plans.
        $plan1['usercanupdate'] = false;
        $plan2['usercanupdate'] = false;
        $plan3['usercanupdate'] = false;

        // You need planmanage, planmanageown or plancreatedraft to see draft plans.
        try {
            external::read_plan($plan1['id']);
            $this->fail('Exception expected due to not permissions to read draft plan');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
        $this->assertEquals((Array)$plan2, external::read_plan($plan2['id']));
        $this->assertEquals((Array)$plan3, external::read_plan($plan3['id']));

        assign_capability('tool/lp:plancreatedraft', CAP_ALLOW, $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->assertEquals((Array)$plan1, external::read_plan($plan1['id']));

        assign_capability('tool/lp:planviewown', CAP_PROHIBIT, $this->userrole, $syscontext->id);
        unassign_capability('tool/lp:plancreatedraft', $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            $plan = external::read_plan($plan2['id']);
            $this->fail('Exception expected due to not permissions to view own plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
    }

    public function test_delete_plans() {
        $this->setUser($this->creator);

        $syscontext = context_system::instance();

        $plan1 = external::create_plan('1', 'A description', FORMAT_HTML, $this->user->id, 0, plan::STATUS_COMPLETE, 0);
        $plan2 = external::create_plan('2', 'A description', FORMAT_HTML, $this->user->id, 0, plan::STATUS_COMPLETE, 0);
        $plan3 = external::create_plan('3', 'A description', FORMAT_HTML, $this->creator->id, 0, plan::STATUS_COMPLETE, 0);

        $this->assertTrue(external::delete_plan($plan1['id']));

        unassign_capability('tool/lp:planmanageall', $this->creatorrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            external::delete_plan($plan2['id']);
            $this->fail('Exception expected due to not permissions to manage plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        $this->setUser($this->user);

        // Can not delete plans created by other users.
        try {
            external::delete_plan($plan2['id']);
            $this->fail('Exception expected due to not permissions to manage plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->assertTrue(external::delete_plan($plan2['id']));

        // Can not delete plans created for other users.
        try {
            external::delete_plan($plan3['id']);
            $this->fail('Exception expected due to not permissions to manage plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        $plan4 = external::create_plan('4', 'A description', FORMAT_HTML, $this->user->id, 0, plan::STATUS_COMPLETE, 0);
        $this->assertTrue(external::delete_plan($plan4['id']));
    }

    public function test_add_competency_to_template() {
        $this->setUser($this->creator);

        $syscontext = context_system::instance();

        // Create a template.
        $template = external::create_template('shortname', 'idnumber', time(), 'description', FORMAT_HTML, true,
            array('contextid' => $syscontext->id));
        $template = (object) external_api::clean_returnvalue(external::create_template_returns(), $template);

        // Create a competency.
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $competency = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $competency = (object) external_api::clean_returnvalue(external::create_competency_returns(), $competency);

        // Add the competency.
        external::add_competency_to_template($template->id, $competency->id);

        // Check that it was added.
        $this->assertEquals(1, external::count_competencies_in_template($template->id));

        // Unassign capability.
        unassign_capability('tool/lp:templatemanage', $this->creatorrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Check we can not add the competency now.
        try {
            external::add_competency_to_template($template->id, $competency->id);
            $this->fail('Exception expected due to not permissions to manage template competencies');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
    }

    public function test_remove_competency_from_template() {
        $this->setUser($this->creator);

        $syscontext = context_system::instance();

        // Create a template.
        $template = external::create_template('shortname', 'idnumber', time(), 'description', FORMAT_HTML, true,
            array('contextid' => $syscontext->id));
        $template = (object) external_api::clean_returnvalue(external::create_template_returns(), $template);

        // Create a competency.
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $competency = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $competency = (object) external_api::clean_returnvalue(external::create_competency_returns(), $competency);

        // Add the competency.
        external::add_competency_to_template($template->id, $competency->id);

        // Check that it was added.
        $this->assertEquals(1, external::count_competencies_in_template($template->id));

        // Check that we can remove the competency.
        external::remove_competency_from_template($template->id, $competency->id);

        // Check that it was removed.
        $this->assertEquals(0, external::count_competencies_in_template($template->id));

        // Unassign capability.
        unassign_capability('tool/lp:templatemanage', $this->creatorrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Check we can not remove the competency now.
        try {
            external::add_competency_to_template($template->id, $competency->id);
            $this->fail('Exception expected due to not permissions to manage template competencies');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
    }

    /**
     * Test we can re-order competency frameworks.
     */
    public function test_reorder_template_competencies() {
        $this->setUser($this->creator);

        $syscontext = context_system::instance();

        // Create a template.
        $template = external::create_template('shortname', 'idnumber', time(), 'description', FORMAT_HTML, true,
            array('contextid' => $syscontext->id));
        $template = (object) external_api::clean_returnvalue(external::create_template_returns(), $template);

        // Create a competency framework.
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, 1,
            $this->scaleconfiguration1, true, array('contextid' => context_system::instance()->id));
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);

        // Create multiple competencies.
        $competency1 = external::create_competency('shortname1', 'idnumber1', 'description', FORMAT_HTML, true, $framework->id, 0);
        $competency1 = (object) external_api::clean_returnvalue(external::create_competency_returns(), $competency1);
        $competency2 = external::create_competency('shortname2', 'idnumber2', 'description', FORMAT_HTML, true, $framework->id, 0);
        $competency2 = (object) external_api::clean_returnvalue(external::create_competency_returns(), $competency2);
        $competency3 = external::create_competency('shortname3', 'idnumber3', 'description', FORMAT_HTML, true, $framework->id, 0);
        $competency3 = (object) external_api::clean_returnvalue(external::create_competency_returns(), $competency3);

        // Add the competencies.
        external::add_competency_to_template($template->id, $competency1->id);
        external::add_competency_to_template($template->id, $competency2->id);
        external::add_competency_to_template($template->id, $competency3->id);

        // This is a move up.
        external::reorder_template_competency($template->id, $competency3->id, $competency2->id);
        $result = external::list_competencies_in_template($template->id);
        $result = external_api::clean_returnvalue(external::list_competencies_in_template_returns(), $result);

        $r1 = (object) $result[0];
        $r2 = (object) $result[1];
        $r3 = (object) $result[2];

        $this->assertEquals($competency1->id, $r1->id);
        $this->assertEquals($competency3->id, $r2->id);
        $this->assertEquals($competency2->id, $r3->id);

        // This is a move down.
        external::reorder_template_competency($template->id, $competency1->id, $competency3->id);
        $result = external::list_competencies_in_template($template->id);
        $result = external_api::clean_returnvalue(external::list_competencies_in_template_returns(), $result);

        $r1 = (object) $result[0];
        $r2 = (object) $result[1];
        $r3 = (object) $result[2];

        $this->assertEquals($competency3->id, $r1->id);
        $this->assertEquals($competency1->id, $r2->id);
        $this->assertEquals($competency2->id, $r3->id);

        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->user);
        external::reorder_template_competency($template->id, $competency1->id, $competency2->id);
    }

    /**
     * Test that we can return scale values for a scale with the scale ID.
     */
    public function test_get_scale_values() {
        global $DB;
        // Create a scale.
        $record = new stdClass();
        $record->courseid = 0;
        $record->userid = $this->creator->id;
        $record->name = 'Test scale';
        $record->scale = 'Poor, Not good, Okay, Fine, Excellent';
        $record->description = '<p>Test scale description.</p>';
        $record->descriptionformat = 1;
        $record->timemodified = time();
        $scaleid = $DB->insert_record('scale', $record);
        // Expected return value.
        $expected = array(array(
                'id' => 1,
                'name' => 'Excellent'
            ), array(
                'id' => 2,
                'name' => 'Fine'
            ), array(
                'id' => 3,
                'name' => 'Okay'
            ), array(
                'id' => 4,
                'name' => 'Not good'
            ), array(
                'id' => 5,
                'name' => 'Poor'
            )
        );
        // Call the webservice.
        $result = external::get_scale_values($scaleid);
        $this->assertEquals($expected, $result);
    }

    /**
     * Create a template.
     */
    public function test_create_template() {
        $syscontextid = context_system::instance()->id;
        $catcontextid = context_coursecat::instance($this->category->id)->id;

        // A user without permission.
        $this->setUser($this->user);
        try {
            $result = external::create_template('shortname', 'idnumber', 0, 'description', FORMAT_HTML, true,
                array('contextid' => $syscontextid));
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        // A user without permission in a category.
        $this->setUser($this->catuser);
        try {
            $result = external::create_template('shortname', 'idnumber', 0, 'description', FORMAT_HTML, true,
                array('contextid' => $catcontextid));
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        // A user with permissions in the system.
        $this->setUser($this->creator);
        $result = external::create_template('shortname', 'idnumber', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $syscontextid));
        $result = external_api::clean_returnvalue(external::create_template_returns(), $result);
        $this->assertEquals('shortname', $result['shortname']);
        $this->assertEquals($syscontextid, $result['contextid']);
        $this->assertNotEmpty($result['id']);

        $result = external::create_template('catshortname', 'catid', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));
        $result = external_api::clean_returnvalue(external::create_template_returns(), $result);
        $this->assertEquals('catshortname', $result['shortname']);
        $this->assertEquals($catcontextid, $result['contextid']);
        $this->assertNotEmpty($result['id']);

        // A user with permissions in the category.
        $this->setUser($this->catcreator);
        try {
            $result = external::create_template('sysshortname', 'sysidnumber', 0, 'description', FORMAT_HTML, true,
                array('contextid' => $syscontextid));
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        $result = external::create_template('catshortname2', 'catid2', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));
        $result = external_api::clean_returnvalue(external::create_template_returns(), $result);
        $this->assertEquals('catshortname2', $result['shortname']);
        $this->assertEquals($catcontextid, $result['contextid']);
        $this->assertNotEmpty($result['id']);
    }

    /**
     * Read a template.
     */
    public function test_read_template() {
        $syscontextid = context_system::instance()->id;
        $catcontextid = context_coursecat::instance($this->category->id)->id;

        // Set a due date for the next year.
        $date = new DateTime('now');
        $date->modify('+1 year');
        $duedate = $date->getTimestamp();

        // Creating two templates.
        $this->setUser($this->creator);
        $systemplate = external::create_template('sys', 'sysid', $duedate, 'description', FORMAT_HTML, true,
            array('contextid' => $syscontextid));
        $cattemplate = external::create_template('cat', 'catid', $duedate, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));

        // User without permissions to read in system.
        assign_capability('tool/lp:templateread', CAP_PROHIBIT, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($this->user);
        $this->assertFalse(has_capability('tool/lp:templateread', context_system::instance()));
        try {
            external::read_template($systemplate->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }
        try {
            external::read_template($cattemplate->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        // User with permissions to read in a category.
        assign_capability('tool/lp:templateread', CAP_PREVENT, $this->userrole, $syscontextid, true);
        assign_capability('tool/lp:templateread', CAP_ALLOW, $this->userrole, $catcontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertFalse(has_capability('tool/lp:templateread', context_system::instance()));
        $this->assertTrue(has_capability('tool/lp:templateread', context_coursecat::instance($this->category->id)));
        try {
            external::read_template($systemplate->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        $result = external::read_template($cattemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($cattemplate->id, $result['id']);
        $this->assertEquals('cat', $result['shortname']);
        $this->assertEquals('catid', $result['idnumber']);
        $this->assertEquals('description', $result['description']);
        $this->assertEquals(FORMAT_HTML, $result['descriptionformat']);
        $this->assertEquals(true, $result['visible']);
        $this->assertEquals($duedate, $result['duedate']);
        $this->assertEquals(userdate($duedate), $result['duedateformatted']);

        // User with permissions to read in the system.
        assign_capability('tool/lp:templateread', CAP_ALLOW, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertTrue(has_capability('tool/lp:templateread', context_system::instance()));
        $result = external::read_template($systemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($systemplate->id, $result['id']);
        $this->assertEquals('sys', $result['shortname']);
        $this->assertEquals('sysid', $result['idnumber']);
        $this->assertEquals('description', $result['description']);
        $this->assertEquals(FORMAT_HTML, $result['descriptionformat']);
        $this->assertEquals(true, $result['visible']);
        $this->assertEquals($duedate, $result['duedate']);
        $this->assertEquals(userdate($duedate), $result['duedateformatted']);

        $result = external::read_template($cattemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($cattemplate->id, $result['id']);
        $this->assertEquals('cat', $result['shortname']);
        $this->assertEquals('catid', $result['idnumber']);
        $this->assertEquals('description', $result['description']);
        $this->assertEquals(FORMAT_HTML, $result['descriptionformat']);
        $this->assertEquals(true, $result['visible']);
        $this->assertEquals($duedate, $result['duedate']);
        $this->assertEquals(userdate($duedate), $result['duedateformatted']);
    }

    /**
     * Update a template.
     */
    public function test_update_template() {
        $syscontextid = context_system::instance()->id;
        $catcontextid = context_coursecat::instance($this->category->id)->id;

        // Set a due date for the next year.
        $date = new DateTime('now');
        $date->modify('+1 year');
        $duedate = $date->getTimestamp();

        // Creating two templates.
        $this->setUser($this->creator);
        $systemplate = external::create_template('sys', 'sysid', $duedate, 'description', FORMAT_HTML, true,
            array('contextid' => $syscontextid));
        $cattemplate = external::create_template('cat', 'catid', $duedate, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));

        // Trying to update in a without permissions.
        $this->setUser($this->user);
        try {
            external::update_template($systemplate->id, 'a', 'b', 1234, 'c', FORMAT_MARKDOWN, false);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        try {
            external::update_template($cattemplate->id, 'a', 'b', 1234, 'c', FORMAT_MARKDOWN, false);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        // User with permissions to update in category.
        $this->setUser($this->catcreator);
        try {
            external::update_template($systemplate->id, 'a', 'b', 1234, 'c', FORMAT_MARKDOWN, false);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        // Set a due date for the next 2 years.
        $date->modify('+1 year');
        $duedateupdated = $date->getTimestamp();

        $result = external::update_template($cattemplate->id, 'a', 'b', $duedateupdated, 'c', FORMAT_MARKDOWN, false);
        $result = external_api::clean_returnvalue(external::update_template_returns(), $result);
        $this->assertTrue($result);
        $result = external::read_template($cattemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($cattemplate->id, $result['id']);
        $this->assertEquals('a', $result['shortname']);
        $this->assertEquals('b', $result['idnumber']);
        $this->assertEquals('c', $result['description']);
        $this->assertEquals(FORMAT_MARKDOWN, $result['descriptionformat']);
        $this->assertEquals(0, $result['visible']);
        $this->assertEquals($duedateupdated, $result['duedate']);
        $this->assertEquals(userdate($duedateupdated), $result['duedateformatted']);

        // User with permissions to update in the system.
        $this->setUser($this->creator);
        $result = external::update_template($systemplate->id, 'x1', 'y1', $duedateupdated, 'z1', FORMAT_PLAIN, false);
        $result = external_api::clean_returnvalue(external::update_template_returns(), $result);
        $this->assertTrue($result);
        $result = external::read_template($systemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($systemplate->id, $result['id']);
        $this->assertEquals('x1', $result['shortname']);
        $this->assertEquals('y1', $result['idnumber']);
        $this->assertEquals('z1', $result['description']);
        $this->assertEquals(FORMAT_PLAIN, $result['descriptionformat']);
        $this->assertEquals(0, $result['visible']);
        $this->assertEquals($duedateupdated, $result['duedate']);
        $this->assertEquals(userdate($duedateupdated), $result['duedateformatted']);

        $result = external::update_template($cattemplate->id, 'x2', 'y2', $duedateupdated, 'z2', FORMAT_PLAIN, true);
        $result = external_api::clean_returnvalue(external::update_template_returns(), $result);
        $this->assertTrue($result);
        $result = external::read_template($cattemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($cattemplate->id, $result['id']);
        $this->assertEquals('x2', $result['shortname']);
        $this->assertEquals('y2', $result['idnumber']);
        $this->assertEquals('z2', $result['description']);
        $this->assertEquals(FORMAT_PLAIN, $result['descriptionformat']);
        $this->assertEquals(1, $result['visible']);
        $this->assertEquals($duedateupdated, $result['duedate']);
        $this->assertEquals(userdate($duedateupdated), $result['duedateformatted']);
    }

    /**
     * Delete a template.
     */
    public function test_delete_template() {
        global $DB;
        $syscontextid = context_system::instance()->id;
        $catcontextid = context_coursecat::instance($this->category->id)->id;

        // Creating a few templates.
        $this->setUser($this->creator);
        $sys1 = external::create_template('sys1', 'sysid1', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $syscontextid));
        $cat1 = external::create_template('cat1', 'catid1', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));
        $cat2 = external::create_template('cat2', 'catid2', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));
        $this->assertTrue($DB->record_exists('tool_lp_template', array('id' => $sys1->id)));
        $this->assertTrue($DB->record_exists('tool_lp_template', array('id' => $cat1->id)));
        $this->assertTrue($DB->record_exists('tool_lp_template', array('id' => $cat2->id)));

        // User without permissions.
        $this->setUser($this->user);
        try {
            external::delete_template($sys1->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }
        try {
            external::delete_template($cat1->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        // User with category permissions.
        $this->setUser($this->catcreator);
        try {
            external::delete_template($sys1->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        $result = external::delete_template($cat1->id);
        $result = external_api::clean_returnvalue(external::delete_template_returns(), $result);
        $this->assertTrue($result);
        $this->assertFalse($DB->record_exists('tool_lp_template', array('id' => $cat1->id)));

        // User with system permissions.
        $this->setUser($this->creator);
        $result = external::delete_template($sys1->id);
        $result = external_api::clean_returnvalue(external::delete_template_returns(), $result);
        $this->assertTrue($result);
        $result = external::delete_template($cat2->id);
        $result = external_api::clean_returnvalue(external::delete_template_returns(), $result);
        $this->assertTrue($result);
        $this->assertFalse($DB->record_exists('tool_lp_template', array('id' => $sys1->id)));
        $this->assertFalse($DB->record_exists('tool_lp_template', array('id' => $cat2->id)));
    }

    /**
     * List templates.
     */
    public function test_list_templates() {
        $syscontextid = context_system::instance()->id;
        $catcontextid = context_coursecat::instance($this->category->id)->id;

        // Creating a few templates.
        $this->setUser($this->creator);
        $sys1 = external::create_template('sys1', 'sysid1', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $syscontextid));
        $sys2 = external::create_template('sys2', 'sysid2', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $syscontextid));
        $cat1 = external::create_template('cat1', 'catid1', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));
        $cat2 = external::create_template('cat2', 'catid2', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));

        // User without permission.
        $this->setUser($this->user);
        assign_capability('tool/lp:templateread', CAP_PROHIBIT, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        try {
            external::list_templates('id', 'ASC', 0, 10, array('contextid' => $syscontextid), 'children');
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        // User with category permissions.
        assign_capability('tool/lp:templateread', CAP_PREVENT, $this->userrole, $syscontextid, true);
        assign_capability('tool/lp:templateread', CAP_ALLOW, $this->userrole, $catcontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $result = external::list_templates('id', 'ASC', 0, 10, array('contextid' => $syscontextid), 'children');
        $result = external_api::clean_returnvalue(external::list_templates_returns(), $result);
        $this->assertCount(2, $result);
        $this->assertEquals($cat1->id, $result[0]['id']);
        $this->assertEquals($cat2->id, $result[1]['id']);

        // User with system permissions.
        assign_capability('tool/lp:templateread', CAP_ALLOW, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $result = external::list_templates('id', 'DESC', 0, 3, array('contextid' => $catcontextid), 'parents');
        $result = external_api::clean_returnvalue(external::list_templates_returns(), $result);
        $this->assertCount(3, $result);
        $this->assertEquals($cat2->id, $result[0]['id']);
        $this->assertEquals($cat1->id, $result[1]['id']);
        $this->assertEquals($sys2->id, $result[2]['id']);
    }

    public function test_count_templates() {
        $syscontextid = context_system::instance()->id;
        $catcontextid = context_coursecat::instance($this->category->id)->id;

        // Creating a few templates.
        $this->setUser($this->creator);
        $sys1 = external::create_template('sys1', 'sysid1', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $syscontextid));
        $sys2 = external::create_template('sys2', 'sysid2', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $syscontextid));
        $cat1 = external::create_template('cat1', 'catid1', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));
        $cat2 = external::create_template('cat2', 'catid2', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));
        $cat3 = external::create_template('cat3', 'catid3', 0, 'description', FORMAT_HTML, true,
            array('contextid' => $catcontextid));

        // User without permission.
        $this->setUser($this->user);
        assign_capability('tool/lp:templateread', CAP_PROHIBIT, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        try {
            external::count_templates(array('contextid' => $syscontextid), 'children');
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
        }

        // User with category permissions.
        assign_capability('tool/lp:templateread', CAP_PREVENT, $this->userrole, $syscontextid, true);
        assign_capability('tool/lp:templateread', CAP_ALLOW, $this->userrole, $catcontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $result = external::count_templates(array('contextid' => $syscontextid), 'children');
        $result = external_api::clean_returnvalue(external::count_templates_returns(), $result);
        $this->assertEquals(3, $result);

        // User with system permissions.
        assign_capability('tool/lp:templateread', CAP_ALLOW, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $result = external::count_templates(array('contextid' => $catcontextid), 'parents');
        $result = external_api::clean_returnvalue(external::count_templates_returns(), $result);
        $this->assertEquals(5, $result);
    }

}
