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

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use tool_lp\external;

/**
 * External learning plans webservice API tests.
 *
 * @package tool_lp
 * @copyright 2015 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_external_testcase extends externallib_advanced_testcase {

    /** @var stdClass $learningplancreator User with enough permissions to create */
    protected $creator = null;

    /** @var stdClass $learningplanuser User with enough permissions to view */
    protected $user = null;

    /**
     * Setup function - we will create a course and add an assign instance to it.
     */
    protected function setUp() {
        global $DB;

        $this->resetAfterTest(true);

        // Create some users.
        $creator = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();
        $syscontext = context_system::instance();

        $creatorrole = create_role('Creator role', 'creatorrole', 'learning plan creator role description');
        $userrole = create_role('User role', 'userrole', 'learning plan user role description');

        assign_capability('tool/lp:competencymanage', CAP_ALLOW, $creatorrole, $syscontext->id);
        assign_capability('tool/lp:competencyview', CAP_ALLOW, $userrole, $syscontext->id);

        role_assign($creatorrole, $creator->id, $syscontext->id);
        role_assign($userrole, $user->id, $syscontext->id);

        $this->creator = $creator;
        $this->user = $user;
        accesslib_clear_all_caches_for_unit_testing();
    }

    /**
     * Test we can't create a competency framework with only read permissions.
     */
    public function test_create_competency_frameworks_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->user);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
    }

    /**
     * Test we can create a competency framework with manage permissions.
     */
    public function test_create_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname', $result->shortname);
        $this->assertEquals('idnumber', $result->idnumber);
        $this->assertEquals('description', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we cannot create a competency framework with nasty data.
     */
    public function test_create_competency_frameworks_with_nasty_data() {
        $this->setUser($this->creator);
        $this->setExpectedException('invalid_parameter_exception');
        $result = external::create_competency_framework('short<a href="">', 'id;"number', 'de<>\\..scription', FORMAT_HTML, true);
    }

    /**
     * Test we can read a competency framework with manage permissions.
     */
    public function test_read_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
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
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can read a competency framework with read permissions.
     */
    public function test_read_competency_frameworks_with_read_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
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
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can delete a competency framework with manage permissions.
     */
    public function test_delete_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $id = $result->id;
        $result = external::delete_competency_framework($id);
        $result = external_api::clean_returnvalue(external::delete_competency_framework_returns(), $result);

        $this->assertTrue($result);
    }

    /**
     * Test we can delete a competency framework with read permissions.
     */
    public function test_delete_competency_frameworks_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
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
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $result = external::update_competency_framework($result->id, 'shortname2', 'idnumber2', 'description2', FORMAT_PLAIN, false);
        $result = external_api::clean_returnvalue(external::update_competency_framework_returns(), $result);

        $this->assertTrue($result);
    }

    /**
     * Test we can update a competency framework with read permissions.
     */
    public function test_update_competency_frameworks_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $result = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);

        $this->setUser($this->user);
        $result = external::update_competency_framework($result->id, 'shortname2', 'idnumber2', 'description2', FORMAT_PLAIN, false);
    }

    /**
     * Test we can list and count competency frameworks with manage permissions.
     */
    public function test_list_and_count_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $result = external::create_competency_framework('shortname2', 'idnumber2', 'description', FORMAT_HTML, true);
        $result = external::create_competency_framework('shortname3', 'idnumber3', 'description', FORMAT_HTML, true);

        $result = external::count_competency_frameworks(array());
        $result = external_api::clean_returnvalue(external::count_competency_frameworks_returns(), $result);

        $this->assertEquals($result, 3);

        $result = external::list_competency_frameworks(array(), 'shortname', 'ASC', 0, 10);
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
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can list and count competency frameworks with read permissions.
     */
    public function test_list_and_count_competency_frameworks_with_read_permissions() {
        $this->setUser($this->creator);
        $result = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $result = external::create_competency_framework('shortname2', 'idnumber2', 'description', FORMAT_HTML, true);
        $result = external::create_competency_framework('shortname3', 'idnumber3', 'description', FORMAT_HTML, true);

        $this->setUser($this->user);
        $result = external::count_competency_frameworks(array());
        $result = external_api::clean_returnvalue(external::count_competency_frameworks_returns(), $result);

        $this->assertEquals($result, 3);

        $result = external::list_competency_frameworks(array(), 'shortname', 'ASC', 0, 10);
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
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can re-order competency frameworks.
     */
    public function test_reorder_competency_framework() {
        $this->setUser($this->creator);
        $f1 = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $f2 = external::create_competency_framework('shortname2', 'idnumber2', 'description', FORMAT_HTML, true);
        $f3 = external::create_competency_framework('shortname3', 'idnumber3', 'description', FORMAT_HTML, true);
        $f4 = external::create_competency_framework('shortname4', 'idnumber4', 'description', FORMAT_HTML, true);
        $f5 = external::create_competency_framework('shortname5', 'idnumber5', 'description', FORMAT_HTML, true);
        $f6 = external::create_competency_framework('shortname6', 'idnumber6', 'description', FORMAT_HTML, true);

        // This is a move up.
        $result = external::reorder_competency_framework($f5->id, $f2->id);
        $result = external::list_competency_frameworks(array(), 'sortorder', 'ASC', 0, 10);
        $result = external_api::clean_returnvalue(external::list_competency_frameworks_returns(), $result);

        $r1 = (object) $result[0];
        $r2 = (object) $result[1];
        $r3 = (object) $result[2];
        $r4 = (object) $result[3];
        $r5 = (object) $result[4];
        $r6 = (object) $result[5];

        $this->assertEquals($f1->id, $r1->id);
        $this->assertEquals($f5->id, $r2->id);
        $this->assertEquals($f2->id, $r3->id);
        $this->assertEquals($f3->id, $r4->id);
        $this->assertEquals($f4->id, $r5->id);
        $this->assertEquals($f6->id, $r6->id);

        // This is a move down.
        $result = external::reorder_competency_framework($f5->id, $f4->id);
        $result = external::list_competency_frameworks(array(), 'sortorder', 'ASC', 0, 10);
        $result = external_api::clean_returnvalue(external::list_competency_frameworks_returns(), $result);

        $r1 = (object) $result[0];
        $r2 = (object) $result[1];
        $r3 = (object) $result[2];
        $r4 = (object) $result[3];
        $r5 = (object) $result[4];
        $r6 = (object) $result[5];

        $this->assertEquals($f1->id, $r1->id);
        $this->assertEquals($f2->id, $r2->id);
        $this->assertEquals($f3->id, $r3->id);
        $this->assertEquals($f4->id, $r4->id);
        $this->assertEquals($f5->id, $r5->id);
        $this->assertEquals($f6->id, $r6->id);

        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->user);
        $result = external::reorder_competency_framework($f5->id, $f4->id);
    }

    /**
     * Test we can't create a competency with only read permissions.
     */
    public function test_create_competency_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $this->setUser($this->user);
        $competency = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
    }

    /**
     * Test we can create a competency with manage permissions.
     */
    public function test_create_competency_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
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
     * Test we cannot create a competency with nasty data.
     */
    public function test_create_competency_with_nasty_data() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $this->setExpectedException('invalid_parameter_exception');
        $competency = external::create_competency('shortname<a href="">', 'id;"number', 'de<>\\..scription', FORMAT_HTML, true, $framework->id, 0);
    }

    /**
     * Test we can read a competency with manage permissions.
     */
    public function test_read_competencies_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
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
        $this->assertEquals(0, $result->parentid);
    }

    /**
     * Test we can read a competency with read permissions.
     */
    public function test_read_competencies_with_read_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
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
        $this->assertEquals(0, $result->parentid);
    }

    /**
     * Test we can delete a competency with manage permissions.
     */
    public function test_delete_competency_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $id = $result->id;
        $result = external::delete_competency($id);
        $result = external_api::clean_returnvalue(external::delete_competency_returns(), $result);

        $this->assertTrue($result);
    }

    /**
     * Test we can delete a competency with read permissions.
     */
    public function test_delete_competency_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
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
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);

        $result = external::update_competency($result->id, 'shortname2', 'idnumber2', 'description2', FORMAT_HTML, false);
        $result = external_api::clean_returnvalue(external::update_competency_returns(), $result);

        $this->assertTrue($result);
    }

    /**
     * Test we can update a competency with read permissions.
     */
    public function test_update_competency_with_read_permissions() {
        $this->setExpectedException('required_capability_exception');
        $this->setUser($this->creator);
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
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
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = external::create_competency('shortname2', 'idnumber2', 'description2', FORMAT_HTML, true, $framework->id, 0);
        $result = external::create_competency('shortname3', 'idnumber3', 'description3', FORMAT_HTML, true, $framework->id, 0);

        $result = external::count_competencies(array());
        $result = external_api::clean_returnvalue(external::count_competencies_returns(), $result);

        $this->assertEquals($result, 3);

        $result = external::list_competencies(array(), 'shortname', 'ASC', 0, 10);
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
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
        $framework = (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $framework);
        $result = external::create_competency('shortname', 'idnumber', 'description', FORMAT_HTML, true, $framework->id, 0);
        $result = external::create_competency('shortname2', 'idnumber2', 'description2', FORMAT_HTML, true, $framework->id, 0);
        $result = external::create_competency('shortname3', 'idnumber3', 'description3', FORMAT_HTML, true, $framework->id, 0);

        $this->setUser($this->user);

        $result = external::count_competencies(array());
        $result = external_api::clean_returnvalue(external::count_competencies_returns(), $result);

        $this->assertEquals($result, 3);

        $result = external::list_competencies(array(), 'shortname', 'ASC', 0, 10);
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
        $framework = external::create_competency_framework('shortname', 'idnumber', 'description', FORMAT_HTML, true);
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

}
