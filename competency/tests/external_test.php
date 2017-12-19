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
 * External tests.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use core_competency\api;
use core_competency\course_competency_settings;
use core_competency\external;
use core_competency\invalid_persistent_exception;
use core_competency\plan;
use core_competency\plan_competency;
use core_competency\related_competency;
use core_competency\template;
use core_competency\template_competency;
use core_competency\user_competency;
use core_competency\user_competency_plan;

/**
 * External testcase.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_external_testcase extends externallib_advanced_testcase {

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

    /** @var stdClass $scale1 Scale */
    protected $scale1 = null;

    /** @var stdClass $scale2 Scale */
    protected $scale2 = null;

    /** @var stdClass $scale3 Scale */
    protected $scale3 = null;

    /** @var stdClass $scale4 Scale */
    protected $scale4 = null;

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
        $othercategory = $this->getDataGenerator()->create_category();
        $catcreator = $this->getDataGenerator()->create_user();

        $syscontext = context_system::instance();
        $catcontext = context_coursecat::instance($category->id);
        $othercatcontext = context_coursecat::instance($othercategory->id);

        // Fetching default authenticated user role.
        $userroles = get_archetype_roles('user');
        $this->assertCount(1, $userroles);
        $authrole = array_pop($userroles);

        // Reset all default authenticated users permissions.
        unassign_capability('moodle/competency:competencygrade', $authrole->id);
        unassign_capability('moodle/competency:competencymanage', $authrole->id);
        unassign_capability('moodle/competency:competencyview', $authrole->id);
        unassign_capability('moodle/competency:planmanage', $authrole->id);
        unassign_capability('moodle/competency:planmanagedraft', $authrole->id);
        unassign_capability('moodle/competency:planmanageown', $authrole->id);
        unassign_capability('moodle/competency:planview', $authrole->id);
        unassign_capability('moodle/competency:planviewdraft', $authrole->id);
        unassign_capability('moodle/competency:planviewown', $authrole->id);
        unassign_capability('moodle/competency:planviewowndraft', $authrole->id);
        unassign_capability('moodle/competency:templatemanage', $authrole->id);
        unassign_capability('moodle/competency:templateview', $authrole->id);
        unassign_capability('moodle/cohort:manage', $authrole->id);
        unassign_capability('moodle/competency:coursecompetencyconfigure', $authrole->id);

        // Creating specific roles.
        $this->creatorrole = create_role('Creator role', 'creatorrole', 'learning plan creator role description');
        $this->userrole = create_role('User role', 'userrole', 'learning plan user role description');

        assign_capability('moodle/competency:competencymanage', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('moodle/competency:competencycompetencyconfigure', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('moodle/competency:competencyview', CAP_ALLOW, $this->userrole, $syscontext->id);
        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('moodle/competency:planmanagedraft', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('moodle/competency:planviewdraft', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('moodle/competency:templatemanage', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('moodle/competency:competencygrade', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('moodle/cohort:manage', CAP_ALLOW, $this->creatorrole, $syscontext->id);
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $this->userrole, $syscontext->id);
        assign_capability('moodle/competency:planviewown', CAP_ALLOW, $this->userrole, $syscontext->id);
        assign_capability('moodle/competency:planviewowndraft', CAP_ALLOW, $this->userrole, $syscontext->id);

        role_assign($this->creatorrole, $creator->id, $syscontext->id);
        role_assign($this->creatorrole, $catcreator->id, $catcontext->id);
        role_assign($this->userrole, $user->id, $syscontext->id);
        role_assign($this->userrole, $catuser->id, $catcontext->id);

        $this->creator = $creator;
        $this->catcreator = $catcreator;
        $this->user = $user;
        $this->catuser = $catuser;
        $this->category = $category;
        $this->othercategory = $othercategory;

        $this->scale1 = $this->getDataGenerator()->create_scale(array("scale" => "value1, value2"));
        $this->scale2 = $this->getDataGenerator()->create_scale(array("scale" => "value3, value4"));
        $this->scale3 = $this->getDataGenerator()->create_scale(array("scale" => "value5, value6"));
        $this->scale4 = $this->getDataGenerator()->create_scale(array("scale" => "value7, value8"));

        $this->scaleconfiguration1 = '[{"scaleid":"'.$this->scale1->id.'"},' .
                '{"name":"value1","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"value2","id":2,"scaledefault":0,"proficient":1}]';
        $this->scaleconfiguration2 = '[{"scaleid":"'.$this->scale2->id.'"},' .
                '{"name":"value3","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"value4","id":2,"scaledefault":0,"proficient":1}]';
        $this->scaleconfiguration3 = '[{"scaleid":"'.$this->scale3->id.'"},' .
                '{"name":"value5","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"value6","id":2,"scaledefault":0,"proficient":1}]';
        $this->scaleconfiguration4 = '[{"scaleid":"'.$this->scale4->id.'"},'.
                '{"name":"value8","id":1,"scaledefault":1,"proficient":0},' .
                '{"name":"value8","id":2,"scaledefault":0,"proficient":1}]';
        accesslib_clear_all_caches_for_unit_testing();
    }


    protected function create_competency_framework($number = 1, $system = true) {
        $scalename = 'scale' . $number;
        $scalepropname = 'scaleconfiguration' . $number;
        $framework = array(
            'shortname' => 'shortname' . $number,
            'idnumber' => 'idnumber' . $number,
            'description' => 'description' . $number,
            'descriptionformat' => FORMAT_HTML,
            'scaleid' => $this->$scalename->id,
            'scaleconfiguration' => $this->$scalepropname,
            'visible' => true,
            'contextid' => $system ? context_system::instance()->id : context_coursecat::instance($this->category->id)->id
        );
        $result = external::create_competency_framework($framework);
        return (object) external_api::clean_returnvalue(external::create_competency_framework_returns(), $result);
    }

    protected function create_plan($number, $userid, $templateid, $status, $duedate) {
        $plan = array(
            'name' => 'name' . $number,
            'description' => 'description' . $number,
            'descriptionformat' => FORMAT_HTML,
            'userid' => $userid,
            'templateid' => empty($templateid) ? null : $templateid,
            'status' => $status,
            'duedate' => $duedate
        );
        $result = external::create_plan($plan);
        return (object) external_api::clean_returnvalue(external::create_plan_returns(), $result);
    }

    protected function create_template($number, $system) {
        $template = array(
            'shortname' => 'shortname' . $number,
            'description' => 'description' . $number,
            'descriptionformat' => FORMAT_HTML,
            'duedate' => 0,
            'visible' => true,
            'contextid' => $system ? context_system::instance()->id : context_coursecat::instance($this->category->id)->id
        );
        $result = external::create_template($template);
        return (object) external_api::clean_returnvalue(external::create_template_returns(), $result);
    }

    protected function update_template($templateid, $number) {
        $template = array(
            'id' => $templateid,
            'shortname' => 'shortname' . $number,
            'description' => 'description' . $number,
            'descriptionformat' => FORMAT_HTML,
            'visible' => true
        );
        $result = external::update_template($template);
        return external_api::clean_returnvalue(external::update_template_returns(), $result);
    }

    protected function update_plan($planid, $number, $userid, $templateid, $status, $duedate) {
        $plan = array(
            'id' => $planid,
            'name' => 'name' . $number,
            'description' => 'description' . $number,
            'descriptionformat' => FORMAT_HTML,
            'userid' => $userid,
            'templateid' => $templateid,
            'status' => $status,
            'duedate' => $duedate
        );
        $result = external::update_plan($plan);
        return external_api::clean_returnvalue(external::update_plan_returns(), $result);
    }

    protected function update_competency_framework($id, $number = 1, $system = true) {
        $scalename = 'scale' . $number;
        $scalepropname = 'scaleconfiguration' . $number;
        $framework = array(
            'id' => $id,
            'shortname' => 'shortname' . $number,
            'idnumber' => 'idnumber' . $number,
            'description' => 'description' . $number,
            'descriptionformat' => FORMAT_HTML,
            'scaleid' => $this->$scalename->id,
            'scaleconfiguration' => $this->$scalepropname,
            'visible' => true,
            'contextid' => $system ? context_system::instance()->id : context_coursecat::instance($this->category->id)->id
        );
        $result = external::update_competency_framework($framework);
        return external_api::clean_returnvalue(external::update_competency_framework_returns(), $result);
    }

    protected function create_competency($number, $frameworkid) {
        $competency = array(
            'shortname' => 'shortname' . $number,
            'idnumber' => 'idnumber' . $number,
            'description' => 'description' . $number,
            'descriptionformat' => FORMAT_HTML,
            'competencyframeworkid' => $frameworkid
        );
        $result = external::create_competency($competency);
        return (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);
    }

    protected function update_competency($id, $number) {
        $competency = array(
            'id' => $id,
            'shortname' => 'shortname' . $number,
            'idnumber' => 'idnumber' . $number,
            'description' => 'description' . $number,
            'descriptionformat' => FORMAT_HTML
        );
        $result = external::update_competency($competency);
        return external_api::clean_returnvalue(external::update_competency_returns(), $result);
    }

    /**
     * Test we can't create a competency framework with only read permissions.
     *
     * @expectedException required_capability_exception
     */
    public function test_create_competency_frameworks_with_read_permissions() {
        $this->setUser($this->user);

        $result = $this->create_competency_framework(1, true);
    }

    /**
     * Test we can't create a competency framework with only read permissions.
     *
     * @expectedException required_capability_exception
     */
    public function test_create_competency_frameworks_with_read_permissions_in_category() {
        $this->setUser($this->catuser);
        $result = $this->create_competency_framework(1, false);
    }

    /**
     * Test we can create a competency framework with manage permissions.
     */
    public function test_create_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = $this->create_competency_framework(1, true);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals($this->scale1->id, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can create a competency framework with manage permissions.
     */
    public function test_create_competency_frameworks_with_manage_permissions_in_category() {
        $this->setUser($this->catcreator);
        $result = $this->create_competency_framework(1, false);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->catcreator->id, $result->usermodified);
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals($this->scale1->id, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);

        try {
            $result = $this->create_competency_framework(1, true);
            $this->fail('User cannot create a framework at system level.');
        } catch (required_capability_exception $e) {
            // All good.
        }
    }

    /**
     * Test we cannot create a competency framework with nasty data.
     *
     * @expectedException invalid_parameter_exception
     */
    public function test_create_competency_frameworks_with_nasty_data() {
        $this->setUser($this->creator);
        $framework = array(
            'shortname' => 'short<a href="">',
            'idnumber' => 'id;"number',
            'description' => 'de<>\\..scription',
            'descriptionformat' => FORMAT_HTML,
            'scaleid' => $this->scale1->id,
            'scaleconfiguration' => $this->scaleconfiguration1,
            'visible' => true,
            'contextid' => context_system::instance()->id
        );
        $result = external::create_competency_framework($framework);
    }

    /**
     * Test we can read a competency framework with manage permissions.
     */
    public function test_read_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = $this->create_competency_framework(1, true);

        $id = $result->id;
        $result = external::read_competency_framework($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals($this->scale1->id, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can read a competency framework with manage permissions.
     */
    public function test_read_competency_frameworks_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $insystem = $this->create_competency_framework(1, true);
        $incat = $this->create_competency_framework(2, false);

        $this->setUser($this->catcreator);
        $id = $incat->id;
        $result = external::read_competency_framework($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname2', $result->shortname);
        $this->assertEquals('idnumber2', $result->idnumber);
        $this->assertEquals('description2', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals($this->scale2->id, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration2, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);

        try {
            $id = $insystem->id;
            $result = external::read_competency_framework($id);
            $result = (object) external_api::clean_returnvalue(external::read_competency_framework_returns(), $result);
            $this->fail('User cannot read a framework at system level.');
        } catch (required_capability_exception $e) {
            // All good.
        }
    }

    /**
     * Test we can read a competency framework with read permissions.
     */
    public function test_read_competency_frameworks_with_read_permissions() {
        $this->setUser($this->creator);
        $result = $this->create_competency_framework(1, true);

        // Switch users to someone with less permissions.
        $this->setUser($this->user);
        $id = $result->id;
        $result = external::read_competency_framework($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals($this->scale1->id, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);
    }
    /**
     * Test we can read a competency framework with read permissions.
     */
    public function test_read_competency_frameworks_with_read_permissions_in_category() {
        $this->setUser($this->creator);

        $insystem = $this->create_competency_framework(1, true);
        $incat = $this->create_competency_framework(2, false);

        // Switch users to someone with less permissions.
        $this->setUser($this->catuser);
        $id = $incat->id;
        $result = external::read_competency_framework($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_framework_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname2', $result->shortname);
        $this->assertEquals('idnumber2', $result->idnumber);
        $this->assertEquals('description2', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals($this->scale2->id, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration2, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);

        // Switching to user with no permissions.
        try {
            $result = external::read_competency_framework($insystem->id);
            $this->fail('Current user cannot should not be able to read the framework.');
        } catch (required_capability_exception $e) {
            // All good.
        }
    }

    /**
     * Test we can delete a competency framework with manage permissions.
     */
    public function test_delete_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = $this->create_competency_framework(1, true);

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

        $insystem = $this->create_competency_framework(1, true);
        $incat = $this->create_competency_framework(2, false);

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
            // All good.
        }
    }

    /**
     * Test we can delete a competency framework with read permissions.
     *
     * @expectedException required_capability_exception
     */
    public function test_delete_competency_frameworks_with_read_permissions() {
        $this->setUser($this->creator);
        $result = $this->create_competency_framework(1, true);

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
        $result = $this->create_competency_framework(1, true);

        $result = $this->update_competency_framework($result->id, 2, true);

        $this->assertTrue($result);
    }

    /**
     * Test we can update a competency framework with manage permissions.
     */
    public function test_update_competency_frameworks_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $insystem = $this->create_competency_framework(1, true);
        $incat = $this->create_competency_framework(2, false);

        $this->setUser($this->catcreator);
        $id = $incat->id;

        $result = $this->update_competency_framework($incat->id, 3, false);

        $this->assertTrue($result);

        try {
            $result = $this->update_competency_framework($insystem->id, 4, true);
            $this->fail('Current user should not be able to update the framework.');
        } catch (required_capability_exception $e) {
            // All good.
        }
    }

    public function test_update_framework_scale() {
        $this->setUser($this->creator);
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $s1 = $this->getDataGenerator()->create_scale();

        $f1 = $lpg->create_framework(array('scaleid' => $s1->id));
        $f2 = $lpg->create_framework(array('scaleid' => $s1->id));
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f2->get('id')));

        $this->assertEquals($s1->id, $f1->get('scaleid'));

        // Make the scale of f2 being used.
        $lpg->create_user_competency(array('userid' => $this->user->id, 'competencyid' => $c2->get('id')));

        // Changing the framework where the scale is not used.
        $result = $this->update_competency_framework($f1->get('id'), 3, true);

        $f1 = new \core_competency\competency_framework($f1->get('id'));
        $this->assertEquals($this->scale3->id, $f1->get('scaleid'));

        // Changing the framework where the scale is used.
        try {
            $result = $this->update_competency_framework($f2->get('id'), 4, true);
            $this->fail('The scale cannot be changed once used.');
        } catch (\core\invalid_persistent_exception $e) {
            $this->assertRegexp('/scaleid/', $e->getMessage());
        }
    }

    /**
     * Test we can update a competency framework with read permissions.
     *
     * @expectedException required_capability_exception
     */
    public function test_update_competency_frameworks_with_read_permissions() {
        $this->setUser($this->creator);
        $result = $this->create_competency_framework(1, true);

        $this->setUser($this->user);
        $result = $this->update_competency_framework($result->id, 2, true);
    }

    /**
     * Test we can list and count competency frameworks with manage permissions.
     */
    public function test_list_and_count_competency_frameworks_with_manage_permissions() {
        $this->setUser($this->creator);
        $result = $this->create_competency_framework(1, true);
        $result = $this->create_competency_framework(2, true);
        $result = $this->create_competency_framework(3, true);
        $result = $this->create_competency_framework(4, false);

        $result = external::count_competency_frameworks(array('contextid' => context_system::instance()->id), 'self');
        $result = external_api::clean_returnvalue(external::count_competency_frameworks_returns(), $result);

        $this->assertEquals($result, 3);

        $result = external::list_competency_frameworks('shortname', 'ASC', 0, 10,
            array('contextid' => context_system::instance()->id), 'self', false);
        $result = external_api::clean_returnvalue(external::list_competency_frameworks_returns(), $result);

        $this->assertEquals(count($result), 3);
        $result = (object) $result[0];

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals($this->scale1->id, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);
    }

    public function test_list_competency_frameworks_with_query() {
        $this->setUser($this->creator);
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework1 = $lpg->create_framework(array(
            'shortname' => 'shortname_beetroot',
            'idnumber' => 'idnumber_cinnamon',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => true,
            'contextid' => context_system::instance()->id
        ));
        $framework2 = $lpg->create_framework(array(
            'shortname' => 'shortname_citrus',
            'idnumber' => 'idnumber_beer',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => true,
            'contextid' => context_system::instance()->id
        ));

        // Search on both ID number and shortname.
        $result = external::list_competency_frameworks('shortname', 'ASC', 0, 10,
            array('contextid' => context_system::instance()->id), 'self', false, 'bee');
        $result = external_api::clean_returnvalue(external::list_competency_frameworks_returns(), $result);
        $this->assertCount(2, $result);
        $f = (object) array_shift($result);
        $this->assertEquals($framework1->get('id'), $f->id);
        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get('id'), $f->id);

        // Search on ID number.
        $result = external::list_competency_frameworks('shortname', 'ASC', 0, 10,
            array('contextid' => context_system::instance()->id), 'self', false, 'beer');
        $result = external_api::clean_returnvalue(external::list_competency_frameworks_returns(), $result);
        $this->assertCount(1, $result);
        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get('id'), $f->id);

        // Search on shortname.
        $result = external::list_competency_frameworks('shortname', 'ASC', 0, 10,
            array('contextid' => context_system::instance()->id), 'self', false, 'cinnamon');
        $result = external_api::clean_returnvalue(external::list_competency_frameworks_returns(), $result);
        $this->assertCount(1, $result);
        $f = (object) array_shift($result);
        $this->assertEquals($framework1->get('id'), $f->id);

        // No match.
        $result = external::list_competency_frameworks('shortname', 'ASC', 0, 10,
            array('contextid' => context_system::instance()->id), 'self', false, 'pwnd!');
        $result = external_api::clean_returnvalue(external::list_competency_frameworks_returns(), $result);
        $this->assertCount(0, $result);
    }

    /**
     * Test we can list and count competency frameworks with read permissions.
     */
    public function test_list_and_count_competency_frameworks_with_read_permissions() {
        $this->setUser($this->creator);
        $result = $this->create_competency_framework(1, true);
        $result = $this->create_competency_framework(2, true);
        $result = $this->create_competency_framework(3, true);
        $result = $this->create_competency_framework(4, false);

        $this->setUser($this->user);
        $result = external::count_competency_frameworks(array('contextid' => context_system::instance()->id), 'self');
        $result = external_api::clean_returnvalue(external::count_competency_frameworks_returns(), $result);
        $this->assertEquals($result, 3);

        $result = external::list_competency_frameworks('shortname', 'ASC', 0, 10,
            array('contextid' => context_system::instance()->id), 'self', false);
        $result = external_api::clean_returnvalue(external::list_competency_frameworks_returns(), $result);

        $this->assertEquals(count($result), 3);
        $result = (object) $result[0];

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals($this->scale1->id, $result->scaleid);
        $this->assertEquals($this->scaleconfiguration1, $result->scaleconfiguration);
        $this->assertEquals(true, $result->visible);
    }

    /**
     * Test we can't create a competency with only read permissions.
     *
     * @expectedException required_capability_exception
     */
    public function test_create_competency_with_read_permissions() {
        $framework = $this->getDataGenerator()->get_plugin_generator('core_competency')->create_framework();
        $this->setUser($this->user);
        $competency = $this->create_competency(1, $framework->get('id'));
    }

    /**
     * Test we can create a competency with manage permissions.
     */
    public function test_create_competency_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = $this->create_competency_framework(1, true);
        $competency = $this->create_competency(1, $framework->id);

        $this->assertGreaterThan(0, $competency->timecreated);
        $this->assertGreaterThan(0, $competency->timemodified);
        $this->assertEquals($this->creator->id, $competency->usermodified);
        $this->assertEquals('shortname1', $competency->shortname);
        $this->assertEquals('idnumber1', $competency->idnumber);
        $this->assertEquals('description1', $competency->description);
        $this->assertEquals(FORMAT_HTML, $competency->descriptionformat);
        $this->assertEquals(0, $competency->parentid);
        $this->assertEquals($framework->id, $competency->competencyframeworkid);
    }


    /**
     * Test we can create a competency with manage permissions.
     */
    public function test_create_competency_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $insystem = $this->create_competency_framework(1, true);
        $incat = $this->create_competency_framework(2, false);

        $this->setUser($this->catcreator);

        $competency = $this->create_competency(1, $incat->id);

        $this->assertGreaterThan(0, $competency->timecreated);
        $this->assertGreaterThan(0, $competency->timemodified);
        $this->assertEquals($this->catcreator->id, $competency->usermodified);
        $this->assertEquals('shortname1', $competency->shortname);
        $this->assertEquals('idnumber1', $competency->idnumber);
        $this->assertEquals('description1', $competency->description);
        $this->assertEquals(FORMAT_HTML, $competency->descriptionformat);
        $this->assertEquals(0, $competency->parentid);
        $this->assertEquals($incat->id, $competency->competencyframeworkid);

        try {
            $competency = $this->create_competency(2, $insystem->id);
            $this->fail('User should not be able to create a competency in system context.');
        } catch (required_capability_exception $e) {
            // All good.
        }
    }

    /**
     * Test we cannot create a competency with nasty data.
     *
     * @expectedException invalid_parameter_exception
     */
    public function test_create_competency_with_nasty_data() {
        $this->setUser($this->creator);
        $framework = $this->create_competency_framework(1, true);
        $competency = array(
            'shortname' => 'shortname<a href="">',
            'idnumber' => 'id;"number',
            'description' => 'de<>\\..scription',
            'descriptionformat' => FORMAT_HTML,
            'competencyframeworkid' => $framework->id,
            'sortorder' => 0
        );
        $result = external::create_competency($competency);
        $result = (object) external_api::clean_returnvalue(external::create_competency_returns(), $result);
    }

    /**
     * Test we can read a competency with manage permissions.
     */
    public function test_read_competencies_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = $this->create_competency_framework(1, true);
        $competency = $this->create_competency(1, $framework->id);

        $id = $competency->id;
        $result = external::read_competency($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(0, $result->parentid);
        $this->assertEquals($framework->id, $result->competencyframeworkid);
    }

    /**
     * Test we can read a competency with manage permissions.
     */
    public function test_read_competencies_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $sysframework = $this->create_competency_framework(1, true);
        $insystem = $this->create_competency(1, $sysframework->id);

        $catframework = $this->create_competency_framework(2, false);
        $incat = $this->create_competency(2, $catframework->id);

        $this->setUser($this->catcreator);
        $id = $incat->id;
        $result = external::read_competency($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname2', $result->shortname);
        $this->assertEquals('idnumber2', $result->idnumber);
        $this->assertEquals('description2', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(0, $result->parentid);
        $this->assertEquals($catframework->id, $result->competencyframeworkid);

        try {
            external::read_competency($insystem->id);
            $this->fail('User should not be able to read a competency in system context.');
        } catch (required_capability_exception $e) {
            // All good.
        }
    }

    /**
     * Test we can read a competency with read permissions.
     */
    public function test_read_competencies_with_read_permissions() {
        $this->setUser($this->creator);
        $framework = $this->create_competency_framework(1, true);
        $competency = $this->create_competency(1, $framework->id);

        // Switch users to someone with less permissions.
        $this->setUser($this->user);
        $id = $competency->id;
        $result = external::read_competency($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(0, $result->parentid);
        $this->assertEquals($framework->id, $result->competencyframeworkid);
    }

    /**
     * Test we can read a competency with read permissions.
     */
    public function test_read_competencies_with_read_permissions_in_category() {
        $this->setUser($this->creator);
        $sysframework = $this->create_competency_framework(1, true);
        $insystem = $this->create_competency(1, $sysframework->id);
        $catframework = $this->create_competency_framework(2, false);
        $incat = $this->create_competency(2, $catframework->id);

        // Switch users to someone with less permissions.
        $this->setUser($this->catuser);
        $id = $incat->id;
        $result = external::read_competency($id);
        $result = (object) external_api::clean_returnvalue(external::read_competency_returns(), $result);

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname2', $result->shortname);
        $this->assertEquals('idnumber2', $result->idnumber);
        $this->assertEquals('description2', $result->description);
        $this->assertEquals(FORMAT_HTML, $result->descriptionformat);
        $this->assertEquals(0, $result->parentid);
        $this->assertEquals($catframework->id, $result->competencyframeworkid);

        try {
            external::read_competency($insystem->id);
            $this->fail('User should not be able to read a competency in system context.');
        } catch (required_capability_exception $e) {
            // All good.
        }
    }

    /**
     * Test we can delete a competency with manage permissions.
     */
    public function test_delete_competency_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = $this->create_competency_framework(1, true);
        $result = $this->create_competency(1, $framework->id);

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

        $sysframework = $this->create_competency_framework(1, true);
        $insystem = $this->create_competency(1, $sysframework->id);
        $catframework = $this->create_competency_framework(2, false);
        $incat = $this->create_competency(2, $catframework->id);

        $this->setUser($this->catcreator);
        $id = $incat->id;
        $result = external::delete_competency($id);
        $result = external_api::clean_returnvalue(external::delete_competency_returns(), $result);

        $this->assertTrue($result);

        try {
            $result = external::delete_competency($insystem->id);
            $this->fail('User should not be able to delete a competency in system context.');
        } catch (required_capability_exception $e) {
            // All good.
        }
    }

    /**
     * Test we can delete a competency with read permissions.
     *
     * @expectedException required_capability_exception
     */
    public function test_delete_competency_with_read_permissions() {
        $this->setUser($this->creator);
        $framework = $this->create_competency_framework(1, true);
        $result = $this->create_competency(1, $framework->id);

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
        $framework = $this->create_competency_framework(1, true);
        $result = $this->create_competency(1, $framework->id);

        $result = $this->update_competency($result->id, 2);

        $this->assertTrue($result);
    }

    /**
     * Test we can update a competency with manage permissions.
     */
    public function test_update_competency_with_manage_permissions_in_category() {
        $this->setUser($this->creator);

        $sysframework = $this->create_competency_framework(1, true);
        $insystem = $this->create_competency(1, $sysframework->id);
        $catframework = $this->create_competency_framework(2, false);
        $incat = $this->create_competency(2, $catframework->id);

        $this->setUser($this->catcreator);

        $result = $this->update_competency($incat->id, 2);

        $this->assertTrue($result);

        try {
            $result = $this->update_competency($insystem->id, 3);
            $this->fail('User should not be able to update a competency in system context.');
        } catch (required_capability_exception $e) {
            // All good.
        }
    }

    /**
     * Test we can update a competency with read permissions.
     *
     * @expectedException required_capability_exception
     */
    public function test_update_competency_with_read_permissions() {
        $this->setUser($this->creator);
        $framework = $this->create_competency_framework(1, true);
        $result = $this->create_competency(1, $framework->id);

        $this->setUser($this->user);
        $result = $this->update_competency($result->id, 2);
    }

    /**
     * Test count competencies with filters.
     */
    public function test_count_competencies_with_filters() {
        $this->setUser($this->creator);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $f1 = $lpg->create_framework();
        $f2 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'shortname' => 'A'));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $f2->get('id')));
        $c5 = $lpg->create_competency(array('competencyframeworkid' => $f2->get('id')));

        $result = external::count_competencies(array(array('column' => 'competencyframeworkid', 'value' => $f2->get('id'))));
        $result = external_api::clean_returnvalue(external::count_competencies_returns(), $result);
        $this->assertEquals(2, $result);

        $result = external::count_competencies(array(array('column' => 'competencyframeworkid', 'value' => $f1->get('id'))));
        $result = external_api::clean_returnvalue(external::count_competencies_returns(), $result);
        $this->assertEquals(3, $result);

        $result = external::count_competencies(array(array('column' => 'shortname', 'value' => 'A')));
        $result = external_api::clean_returnvalue(external::count_competencies_returns(), $result);
        $this->assertEquals(1, $result);
    }

    /**
     * Test we can list and count competencies with manage permissions.
     */
    public function test_list_and_count_competencies_with_manage_permissions() {
        $this->setUser($this->creator);
        $framework = $this->create_competency_framework(1, true);
        $result = $this->create_competency(1, $framework->id);
        $result = $this->create_competency(2, $framework->id);
        $result = $this->create_competency(3, $framework->id);

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
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
    }

    /**
     * Test we can list and count competencies with read permissions.
     */
    public function test_list_and_count_competencies_with_read_permissions() {
        $this->setUser($this->creator);
        $framework = $this->create_competency_framework(1, true);
        $result = $this->create_competency(1, $framework->id);
        $result = $this->create_competency(2, $framework->id);
        $result = $this->create_competency(3, $framework->id);

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
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
    }

    /**
     * Test we can search for competencies.
     */
    public function test_search_competencies_with_read_permissions() {
        $this->setUser($this->creator);
        $framework = $this->create_competency_framework(1, true);
        $result = $this->create_competency(1, $framework->id);
        $result = $this->create_competency(2, $framework->id);
        $result = $this->create_competency(3, $framework->id);

        $this->setUser($this->user);

        $result = external::search_competencies('short', $framework->id);
        $result = external_api::clean_returnvalue(external::search_competencies_returns(), $result);

        $this->assertEquals(count($result), 3);
        $result = (object) $result[0];

        $this->assertGreaterThan(0, $result->timecreated);
        $this->assertGreaterThan(0, $result->timemodified);
        $this->assertEquals($this->creator->id, $result->usermodified);
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals('idnumber1', $result->idnumber);
        $this->assertEquals('description1', $result->description);
    }

    /**
     * Test plans creation and updates.
     */
    public function test_create_and_update_plans() {
        $syscontext = context_system::instance();

        $this->setUser($this->creator);
        $plan0 = $this->create_plan(1, $this->creator->id, 0, plan::STATUS_ACTIVE, 0);

        $this->setUser($this->user);

        try {
            $plan1 = $this->create_plan(2, $this->user->id, 0, plan::STATUS_DRAFT, 0);
            $this->fail('Exception expected due to not permissions to create draft plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        assign_capability('moodle/competency:planmanageowndraft', CAP_ALLOW, $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($this->user);

        $plan2 = $this->create_plan(3, $this->user->id, 0, plan::STATUS_DRAFT, 0);

        // Basic update on the plan.
        $this->assertNotEquals('Updated plan 2 name', $plan2->name);
        $plan2 = external::update_plan(['id' => $plan2->id, 'name' => 'Updated plan 2 name']);
        $this->assertEquals('Updated plan 2 name', $plan2->name);

        try {
            $plan3 = $this->create_plan(4, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
            $this->fail('Exception expected due to not permissions to create active plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
        try {
            $plan3 = $this->update_plan($plan2->id, 4, $this->user->id, 0, plan::STATUS_COMPLETE, 0);
            $this->fail('We cannot complete a plan using api::update_plan().');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }

        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $plan3 = $this->create_plan(4, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        try {
            $plan4 = $this->create_plan(6, $this->creator->id, 0, plan::STATUS_COMPLETE, 0);
            $this->fail('Plans cannot be created as complete.');
        } catch (coding_exception $e) {
            $this->assertRegexp('/A plan cannot be created as complete./', $e->getMessage());
        }

        try {
            $plan0 = $this->update_plan($plan0->id, 1, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        unassign_capability('moodle/competency:planmanageown', $this->userrole, $syscontext->id);
        unassign_capability('moodle/competency:planmanageowndraft', $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            // Cannot be updated even if they created it.
            $this->update_plan($plan2->id, 1, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
            $this->fail('The user can not update their own plan without permissions.');
        } catch (required_capability_exception $e) {
            $this->assertRegexp('/Manage learning plans./', $e->getMessage());
        }
    }

    /**
     * Test complete plan.
     */
    public function test_complete_plan() {
        $syscontext = context_system::instance();

        $this->setUser($this->creator);

        $this->setUser($this->user);

        assign_capability('moodle/competency:planmanageowndraft', CAP_ALLOW, $this->userrole, $syscontext->id);
        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($this->user);

        $plan = $this->create_plan(1, $this->user->id, 0, plan::STATUS_ACTIVE, 0);

        $result = external::complete_plan($plan->id);
        $this->assertTrue($result);
    }

    /**
     * Test reopen plan.
     */
    public function test_reopen_plan() {
        $syscontext = context_system::instance();

        $this->setUser($this->creator);

        $this->setUser($this->user);

        assign_capability('moodle/competency:planmanageowndraft', CAP_ALLOW, $this->userrole, $syscontext->id);
        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($this->user);

        $plan = $this->create_plan(1, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        external::complete_plan($plan->id);

        $result = external::reopen_plan($plan->id);
        $this->assertTrue($result);
    }

    /**
     * Test that we can read plans.
     */
    public function test_read_plans() {
        global $OUTPUT;
        $this->setUser($this->creator);

        $syscontext = context_system::instance();

        $plan1 = $this->create_plan(1, $this->user->id, 0, plan::STATUS_DRAFT, 0);
        $plan2 = $this->create_plan(2, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        $plan3 = $this->create_plan(3, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        external::complete_plan($plan3->id);
        $plan3 = (object) external::read_plan($plan3->id);

        $data = external::read_plan($plan1->id);
        $this->assertEquals((array)$plan1, external::read_plan($plan1->id));
        $data = external::read_plan($plan2->id);
        $this->assertEquals((array)$plan2, external::read_plan($plan2->id));
        $data = external::read_plan($plan3->id);
        $this->assertEquals((array)$plan3, external::read_plan($plan3->id));

        $this->setUser($this->user);

        // The normal user can not edit these plans.
        $plan1->canmanage = false;
        $plan2->canmanage = false;
        $plan3->canmanage = false;
        $plan1->canbeedited = false;
        $plan2->canbeedited = false;
        $plan3->canbeedited = false;
        $plan1->canrequestreview = true;
        $plan2->canrequestreview = true;
        $plan3->canrequestreview = true;
        $plan1->canreview = false;
        $plan2->canreview = false;
        $plan3->canreview = false;
        $plan1->iscompleteallowed = false;
        $plan2->iscompleteallowed = false;
        $plan3->iscompleteallowed = false;
        $plan1->isrequestreviewallowed = true;
        $plan2->isrequestreviewallowed = true;
        $plan3->isrequestreviewallowed = true;
        $plan1->isapproveallowed = false;
        $plan2->isapproveallowed = false;
        $plan3->isapproveallowed = false;
        $plan1->isunapproveallowed = false;
        $plan2->isunapproveallowed = false;
        $plan3->isunapproveallowed = false;
        $plan3->isreopenallowed = false;
        $plan1->commentarea['canpost'] = false;
        $plan1->commentarea['canview'] = true;

        // Prevent the user from seeing their own non-draft plans.
        assign_capability('moodle/competency:plancommentown', CAP_PROHIBIT, $this->userrole, $syscontext->id, true);
        assign_capability('moodle/competency:planviewown', CAP_PROHIBIT, $this->userrole, $syscontext->id, true);
        assign_capability('moodle/competency:planviewowndraft', CAP_ALLOW, $this->userrole, $syscontext->id, true);
        accesslib_clear_all_caches_for_unit_testing();

        $this->assertEquals((array)$plan1, external::read_plan($plan1->id));

        try {
            external::read_plan($plan2->id);
            $this->fail('Exception expected due to not permissions to read plan');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
        try {
            external::read_plan($plan3->id);
            $this->fail('Exception expected due to not permissions to read plan');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Allow user to see their plan.
        assign_capability('moodle/competency:plancommentown', CAP_ALLOW, $this->userrole, $syscontext->id, true);
        assign_capability('moodle/competency:planviewown', CAP_ALLOW, $this->userrole, $syscontext->id, true);
        assign_capability('moodle/competency:planmanageowndraft', CAP_PROHIBIT, $this->userrole, $syscontext->id, true);
        accesslib_clear_all_caches_for_unit_testing();

        $plan1->commentarea['canpost'] = true;
        $plan1->commentarea['canview'] = true;
        $plan2->commentarea['canpost'] = true;
        $plan2->isrequestreviewallowed = false;
        $plan3->commentarea['canpost'] = true;
        $plan3->isrequestreviewallowed = false;
        $plan1->commentarea['canpostorhascomments'] = true;
        $plan2->commentarea['canpostorhascomments'] = true;
        $plan3->commentarea['canpostorhascomments'] = true;

        $this->assertEquals((array)$plan1, external::read_plan($plan1->id));
        $this->assertEquals((array)$plan2, external::read_plan($plan2->id));
        $this->assertEquals((array)$plan3, external::read_plan($plan3->id));

        // Allow use to manage their own draft plan.
        assign_capability('moodle/competency:planviewown', CAP_PROHIBIT, $this->userrole, $syscontext->id, true);
        assign_capability('moodle/competency:planmanageown', CAP_PROHIBIT, $this->userrole, $syscontext->id, true);
        assign_capability('moodle/competency:planmanageowndraft', CAP_ALLOW, $this->userrole, $syscontext->id, true);
        accesslib_clear_all_caches_for_unit_testing();

        $plan1->canmanage = true;
        $plan1->canbeedited = true;
        $plan1->canrequestreview = true;
        $plan1->isrequestreviewallowed = true;
        $this->assertEquals((array)$plan1, external::read_plan($plan1->id));
        try {
            external::read_plan($plan2->id);
            $this->fail('Exception expected due to not permissions to read plan');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
        try {
            external::read_plan($plan3->id);
            $this->fail('Exception expected due to not permissions to read plan');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Allow use to manage their plan.
        assign_capability('moodle/competency:planviewown', CAP_PROHIBIT, $this->userrole, $syscontext->id, true);
        assign_capability('moodle/competency:planmanageowndraft', CAP_PROHIBIT, $this->userrole, $syscontext->id, true);
        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $this->userrole, $syscontext->id, true);
        accesslib_clear_all_caches_for_unit_testing();

        $plan1->canmanage = false;
        $plan1->canbeedited = false;
        $plan1->canrequestreview = true;
        $plan1->canreview = true;
        $plan1->isrequestreviewallowed = true;
        $plan1->isapproveallowed = true;
        $plan1->iscompleteallowed = false;

        $plan2->canmanage = true;
        $plan2->canbeedited = true;
        $plan2->canreview = true;
        $plan2->iscompleteallowed = true;
        $plan2->isunapproveallowed = true;

        $plan3->canmanage = true;
        $plan3->canreview = true;
        $plan3->isreopenallowed = true;

        $this->assertEquals((array)$plan1, external::read_plan($plan1->id));
        $this->assertEquals((array)$plan2, external::read_plan($plan2->id));
        $this->assertEquals((array)$plan3, external::read_plan($plan3->id));
    }

    public function test_delete_plans() {
        $this->setUser($this->creator);

        $syscontext = context_system::instance();

        $plan1 = $this->create_plan(1, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        $plan2 = $this->create_plan(2, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        $plan3 = $this->create_plan(3, $this->creator->id, 0, plan::STATUS_ACTIVE, 0);

        $this->assertTrue(external::delete_plan($plan1->id));

        unassign_capability('moodle/competency:planmanage', $this->creatorrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            external::delete_plan($plan2->id);
            $this->fail('Exception expected due to not permissions to manage plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        $this->setUser($this->user);

        // Can not delete plans created by other users.
        try {
            external::delete_plan($plan2->id);
            $this->fail('Exception expected due to not permissions to manage plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $this->userrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->assertTrue(external::delete_plan($plan2->id));

        // Can not delete plans created for other users.
        try {
            external::delete_plan($plan3->id);
            $this->fail('Exception expected due to not permissions to manage plans');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        $plan4 = $this->create_plan(4, $this->user->id, 0, plan::STATUS_ACTIVE, 0);
        $this->assertTrue(external::delete_plan($plan4->id));
    }

    public function test_delete_plan_removes_relations() {
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $user = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $framework = $lpg->create_framework();
        $comp1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $comp3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $pc1 = $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $comp1->get('id')));
        $pc2 = $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $comp2->get('id')));
        $pc3 = $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $comp3->get('id')));

        // Complete the plan to generate user_competency_plan entries.
        api::complete_plan($plan);

        // Confirm the data we have.
        $this->assertEquals(3, plan_competency::count_records(array('planid' => $plan->get('id'))));
        $this->assertEquals(3, user_competency_plan::count_records(array('planid' => $plan->get('id'), 'userid' => $user->id)));

        // Delete the plan now.
        api::delete_plan($plan->get('id'));
        $this->assertEquals(0, plan_competency::count_records(array('planid' => $plan->get('id'))));
        $this->assertEquals(0, user_competency_plan::count_records(array('planid' => $plan->get('id'), 'userid' => $user->id)));
    }

    public function test_list_plan_competencies() {
        $this->setUser($this->creator);

        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $f1 = $lpg->create_framework();
        $f2 = $lpg->create_framework();

        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1c = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2a = $lpg->create_competency(array('competencyframeworkid' => $f2->get('id')));
        $c2b = $lpg->create_competency(array('competencyframeworkid' => $f2->get('id')));

        $tpl = $lpg->create_template();
        $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $c1a->get('id')));
        $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $c1c->get('id')));
        $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $c2b->get('id')));

        $plan = $lpg->create_plan(array('userid' => $this->user->id, 'templateid' => $tpl->get('id')));

        $uc1a = $lpg->create_user_competency(array('userid' => $this->user->id, 'competencyid' => $c1a->get('id'),
            'status' => user_competency::STATUS_IN_REVIEW, 'reviewerid' => $this->creator->id));
        $uc1b = $lpg->create_user_competency(array('userid' => $this->user->id, 'competencyid' => $c1b->get('id')));
        $uc2b = $lpg->create_user_competency(array('userid' => $this->user->id, 'competencyid' => $c2b->get('id'),
            'grade' => 2, 'proficiency' => 1));
        $ux1a = $lpg->create_user_competency(array('userid' => $this->creator->id, 'competencyid' => $c1a->get('id')));

        $result = external::list_plan_competencies($plan->get('id'));
        $result = external::clean_returnvalue(external::list_plan_competencies_returns(), $result);

        $this->assertCount(3, $result);
        $this->assertEquals($c1a->get('id'), $result[0]['competency']['id']);
        $this->assertEquals($this->user->id, $result[0]['usercompetency']['userid']);
        $this->assertArrayNotHasKey('usercompetencyplan', $result[0]);
        $this->assertEquals($c1c->get('id'), $result[1]['competency']['id']);
        $this->assertEquals($this->user->id, $result[1]['usercompetency']['userid']);
        $this->assertArrayNotHasKey('usercompetencyplan', $result[1]);
        $this->assertEquals($c2b->get('id'), $result[2]['competency']['id']);
        $this->assertEquals($this->user->id, $result[2]['usercompetency']['userid']);
        $this->assertArrayNotHasKey('usercompetencyplan', $result[2]);
        $this->assertEquals(user_competency::STATUS_IN_REVIEW, $result[0]['usercompetency']['status']);
        $this->assertEquals(null, $result[1]['usercompetency']['grade']);
        $this->assertEquals(2, $result[2]['usercompetency']['grade']);
        $this->assertEquals(1, $result[2]['usercompetency']['proficiency']);

        // Check the return values when the plan status is complete.
        $completedplan = $lpg->create_plan(array('userid' => $this->user->id, 'templateid' => $tpl->get('id'),
                'status' => plan::STATUS_COMPLETE));

        $uc1a = $lpg->create_user_competency_plan(array('userid' => $this->user->id, 'competencyid' => $c1a->get('id'),
                'planid' => $completedplan->get('id')));
        $uc1b = $lpg->create_user_competency_plan(array('userid' => $this->user->id, 'competencyid' => $c1c->get('id'),
                'planid' => $completedplan->get('id')));
        $uc2b = $lpg->create_user_competency_plan(array('userid' => $this->user->id, 'competencyid' => $c2b->get('id'),
                'planid' => $completedplan->get('id'), 'grade' => 2, 'proficiency' => 1));
        $ux1a = $lpg->create_user_competency_plan(array('userid' => $this->creator->id, 'competencyid' => $c1a->get('id'),
                'planid' => $completedplan->get('id')));

        $result = external::list_plan_competencies($completedplan->get('id'));
        $result = external::clean_returnvalue(external::list_plan_competencies_returns(), $result);

        $this->assertCount(3, $result);
        $this->assertEquals($c1a->get('id'), $result[0]['competency']['id']);
        $this->assertEquals($this->user->id, $result[0]['usercompetencyplan']['userid']);
        $this->assertArrayNotHasKey('usercompetency', $result[0]);
        $this->assertEquals($c1c->get('id'), $result[1]['competency']['id']);
        $this->assertEquals($this->user->id, $result[1]['usercompetencyplan']['userid']);
        $this->assertArrayNotHasKey('usercompetency', $result[1]);
        $this->assertEquals($c2b->get('id'), $result[2]['competency']['id']);
        $this->assertEquals($this->user->id, $result[2]['usercompetencyplan']['userid']);
        $this->assertArrayNotHasKey('usercompetency', $result[2]);
        $this->assertEquals(null, $result[1]['usercompetencyplan']['grade']);
        $this->assertEquals(2, $result[2]['usercompetencyplan']['grade']);
        $this->assertEquals(1, $result[2]['usercompetencyplan']['proficiency']);
    }

    public function test_add_competency_to_template() {
        $this->setUser($this->creator);

        $syscontext = context_system::instance();

        // Create a template.
        $template = $this->create_template(1, true);

        // Create a competency.
        $framework = $this->create_competency_framework(1, true);
        $competency = $this->create_competency(1, $framework->id);

        // Add the competency.
        external::add_competency_to_template($template->id, $competency->id);

        // Check that it was added.
        $this->assertEquals(1, external::count_competencies_in_template($template->id));

        // Unassign capability.
        unassign_capability('moodle/competency:templatemanage', $this->creatorrole, $syscontext->id);
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
        $syscontext = context_system::instance();
        $this->setUser($this->creator);
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        // Create a template.
        $template = $this->create_template(1, true);

        // Create a competency.
        $framework = $lpg->create_framework();
        $competency = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Add the competency.
        external::add_competency_to_template($template->id, $competency->get('id'));

        // Check that it was added.
        $this->assertEquals(1, external::count_competencies_in_template($template->id));

        // Check that we can remove the competency.
        external::remove_competency_from_template($template->id, $competency->get('id'));

        // Check that it was removed.
        $this->assertEquals(0, external::count_competencies_in_template($template->id));

        // Unassign capability.
        unassign_capability('moodle/competency:templatemanage', $this->creatorrole, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Check we can not remove the competency now.
        try {
            external::add_competency_to_template($template->id, $competency->get('id'));
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
        $onehour = time() + 60 * 60;

        // Create a template.
        $template = $this->create_template(1, true);

        // Create a competency framework.
        $framework = $this->create_competency_framework(1, true);

        // Create multiple competencies.
        $competency1 = $this->create_competency(1, $framework->id);
        $competency2 = $this->create_competency(2, $framework->id);
        $competency3 = $this->create_competency(3, $framework->id);
        $competency4 = $this->create_competency(4, $framework->id);

        // Add the competencies.
        external::add_competency_to_template($template->id, $competency1->id);
        external::add_competency_to_template($template->id, $competency2->id);
        external::add_competency_to_template($template->id, $competency3->id);
        external::add_competency_to_template($template->id, $competency4->id);

        // Test if removing competency from template don't create sortorder holes.
        external::remove_competency_from_template($template->id, $competency3->id);
        $templcomp4 = template_competency::get_record(array(
            'templateid' => $template->id,
            'competencyid' => $competency4->id
        ));

        $this->assertEquals(2, $templcomp4->get('sortorder'));

        // This is a move up.
        external::reorder_template_competency($template->id, $competency4->id, $competency2->id);
        $result = external::list_competencies_in_template($template->id);
        $result = external_api::clean_returnvalue(external::list_competencies_in_template_returns(), $result);

        $r1 = (object) $result[0];
        $r2 = (object) $result[1];
        $r3 = (object) $result[2];

        $this->assertEquals($competency1->id, $r1->id);
        $this->assertEquals($competency4->id, $r2->id);
        $this->assertEquals($competency2->id, $r3->id);

        // This is a move down.
        external::reorder_template_competency($template->id, $competency1->id, $competency4->id);
        $result = external::list_competencies_in_template($template->id);
        $result = external_api::clean_returnvalue(external::list_competencies_in_template_returns(), $result);

        $r1 = (object) $result[0];
        $r2 = (object) $result[1];
        $r3 = (object) $result[2];

        $this->assertEquals($competency4->id, $r1->id);
        $this->assertEquals($competency1->id, $r2->id);
        $this->assertEquals($competency2->id, $r3->id);

        $this->expectException('required_capability_exception');
        $this->setUser($this->user);
        external::reorder_template_competency($template->id, $competency1->id, $competency2->id);
    }

    /**
     * Test we can duplicate learning plan template.
     */
    public function test_duplicate_learning_plan_template() {
        $this->setUser($this->creator);

        $syscontext = context_system::instance();
        $onehour = time() + 60 * 60;

        // Create a template.
        $template = $this->create_template(1, true);

        // Create a competency framework.
        $framework = $this->create_competency_framework(1, true);

        // Create multiple competencies.
        $competency1 = $this->create_competency(1, $framework->id);
        $competency2 = $this->create_competency(2, $framework->id);
        $competency3 = $this->create_competency(3, $framework->id);

        // Add the competencies.
        external::add_competency_to_template($template->id, $competency1->id);
        external::add_competency_to_template($template->id, $competency2->id);
        external::add_competency_to_template($template->id, $competency3->id);

        // Duplicate the learning plan template.
        $duplicatedtemplate = external::duplicate_template($template->id);

        $result = external::list_competencies_in_template($template->id);
        $resultduplicated = external::list_competencies_in_template($duplicatedtemplate->id);

        $this->assertEquals(count($result), count($resultduplicated));
        $this->assertContains($template->shortname, $duplicatedtemplate->shortname);
        $this->assertEquals($duplicatedtemplate->description, $template->description);
        $this->assertEquals($duplicatedtemplate->descriptionformat, $template->descriptionformat);
        $this->assertEquals($duplicatedtemplate->visible, $template->visible);
    }

    /**
     * Test that we can return scale values for a scale with the scale ID.
     */
    public function test_get_scale_values() {
        global $DB;

        $this->setUser($this->creator);

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
                'name' => 'Poor'
            ), array(
                'id' => 2,
                'name' => 'Not good'
            ), array(
                'id' => 3,
                'name' => 'Okay'
            ), array(
                'id' => 4,
                'name' => 'Fine'
            ), array(
                'id' => 5,
                'name' => 'Excellent'
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
            $result = $this->create_template(1, true);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        // A user without permission in a category.
        $this->setUser($this->catuser);
        try {
            $result = $this->create_template(1, false);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        // A user with permissions in the system.
        $this->setUser($this->creator);
        $result = $this->create_template(1, true);
        $this->assertEquals('shortname1', $result->shortname);
        $this->assertEquals($syscontextid, $result->contextid);
        $this->assertNotEmpty($result->id);

        $result = $this->create_template(2, false);
        $this->assertEquals('shortname2', $result->shortname);
        $this->assertEquals($catcontextid, $result->contextid);
        $this->assertNotEmpty($result->id);

        // A user with permissions in the category.
        $this->setUser($this->catcreator);
        try {
            $result = $this->create_template(3, true);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        $result = $this->create_template(3, false);
        $this->assertEquals('shortname3', $result->shortname);
        $this->assertEquals($catcontextid, $result->contextid);
        $this->assertNotEmpty($result->id);
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
        $systemplate = $this->create_template(1, true);
        $cattemplate = $this->create_template(2, false);

        // User without permissions to read in system.
        assign_capability('moodle/competency:templateview', CAP_PROHIBIT, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($this->user);
        $this->assertFalse(has_capability('moodle/competency:templateview', context_system::instance()));
        try {
            external::read_template($systemplate->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }
        try {
            external::read_template($cattemplate->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        // User with permissions to read in a category.
        assign_capability('moodle/competency:templateview', CAP_PREVENT, $this->userrole, $syscontextid, true);
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $this->userrole, $catcontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertFalse(has_capability('moodle/competency:templateview', context_system::instance()));
        $this->assertTrue(has_capability('moodle/competency:templateview', context_coursecat::instance($this->category->id)));
        try {
            external::read_template($systemplate->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        $result = external::read_template($cattemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($cattemplate->id, $result['id']);
        $this->assertEquals('shortname2', $result['shortname']);
        $this->assertEquals('description2', $result['description']);
        $this->assertEquals(FORMAT_HTML, $result['descriptionformat']);
        $this->assertEquals(1, $result['visible']);
        $this->assertEquals(0, $result['duedate']);
        $this->assertEquals(userdate(0), $result['duedateformatted']);

        // User with permissions to read in the system.
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertTrue(has_capability('moodle/competency:templateview', context_system::instance()));
        $result = external::read_template($systemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($systemplate->id, $result['id']);
        $this->assertEquals('shortname1', $result['shortname']);
        $this->assertEquals('description1', $result['description']);
        $this->assertEquals(FORMAT_HTML, $result['descriptionformat']);
        $this->assertEquals(true, $result['visible']);
        $this->assertEquals(0, $result['duedate']);
        $this->assertEquals(userdate(0), $result['duedateformatted']);

        $result = external::read_template($cattemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($cattemplate->id, $result['id']);
        $this->assertEquals('shortname2', $result['shortname']);
        $this->assertEquals('description2', $result['description']);
        $this->assertEquals(FORMAT_HTML, $result['descriptionformat']);
        $this->assertEquals(true, $result['visible']);
        $this->assertEquals(0, $result['duedate']);
        $this->assertEquals(userdate(0), $result['duedateformatted']);
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
        $systemplate = $this->create_template(1, true);
        $cattemplate = $this->create_template(2, false);

        // Trying to update in a without permissions.
        $this->setUser($this->user);
        try {
            $this->update_template($systemplate->id, 3);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        try {
            $this->update_template($cattemplate->id, 3);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        // User with permissions to update in category.
        $this->setUser($this->catcreator);
        try {
            $this->update_template($systemplate->id, 3);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        $result = $this->update_template($cattemplate->id, 3);
        $this->assertTrue($result);
        $result = external::read_template($cattemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($cattemplate->id, $result['id']);
        $this->assertEquals('shortname3', $result['shortname']);
        $this->assertEquals("description3", $result['description']);
        $this->assertEquals(FORMAT_HTML, $result['descriptionformat']);
        $this->assertEquals(true, $result['visible']);
        $this->assertEquals(0, $result['duedate']);
        $this->assertEquals(userdate(0), $result['duedateformatted']);

        // User with permissions to update in the system.
        $this->setUser($this->creator);
        $result = $this->update_template($systemplate->id, 4);
        $this->assertTrue($result);
        $result = external::read_template($systemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($systemplate->id, $result['id']);
        $this->assertEquals('shortname4', $result['shortname']);
        $this->assertEquals('description4', $result['description']);
        $this->assertEquals(FORMAT_HTML, $result['descriptionformat']);
        $this->assertEquals(true, $result['visible']);
        $this->assertEquals(0, $result['duedate']);
        $this->assertEquals(userdate(0), $result['duedateformatted']);

        $result = $this->update_template($cattemplate->id, 5);
        $this->assertTrue($result);
        $result = external::read_template($cattemplate->id);
        $result = external_api::clean_returnvalue(external::read_template_returns(), $result);
        $this->assertEquals($cattemplate->id, $result['id']);
        $this->assertEquals('shortname5', $result['shortname']);
        $this->assertEquals('description5', $result['description']);
        $this->assertEquals(FORMAT_HTML, $result['descriptionformat']);
        $this->assertEquals(1, $result['visible']);
        $this->assertEquals(0, $result['duedate']);
        $this->assertEquals(userdate(0), $result['duedateformatted']);
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
        $sys1 = $this->create_template(1, true);
        $cat1 = $this->create_template(2, false);
        $cat2 = $this->create_template(3, false);
        $this->assertTrue($DB->record_exists(template::TABLE, array('id' => $sys1->id)));
        $this->assertTrue($DB->record_exists(template::TABLE, array('id' => $cat1->id)));
        $this->assertTrue($DB->record_exists(template::TABLE, array('id' => $cat2->id)));

        // User without permissions.
        $this->setUser($this->user);
        try {
            external::delete_template($sys1->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }
        try {
            external::delete_template($cat1->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        // User with category permissions.
        $this->setUser($this->catcreator);
        try {
            external::delete_template($sys1->id);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        $result = external::delete_template($cat1->id);
        $result = external_api::clean_returnvalue(external::delete_template_returns(), $result);
        $this->assertTrue($result);
        $this->assertFalse($DB->record_exists(template::TABLE, array('id' => $cat1->id)));

        // User with system permissions.
        $this->setUser($this->creator);
        $result = external::delete_template($sys1->id);
        $result = external_api::clean_returnvalue(external::delete_template_returns(), $result);
        $this->assertTrue($result);
        $result = external::delete_template($cat2->id);
        $result = external_api::clean_returnvalue(external::delete_template_returns(), $result);
        $this->assertTrue($result);
        $this->assertFalse($DB->record_exists(template::TABLE, array('id' => $sys1->id)));
        $this->assertFalse($DB->record_exists(template::TABLE, array('id' => $cat2->id)));
    }

    /**
     * List templates.
     */
    public function test_list_templates() {
        $syscontextid = context_system::instance()->id;
        $catcontextid = context_coursecat::instance($this->category->id)->id;

        // Creating a few templates.
        $this->setUser($this->creator);
        $sys1 = $this->create_template(1, true);
        $sys2 = $this->create_template(2, true);
        $cat1 = $this->create_template(3, false);
        $cat2 = $this->create_template(4, false);

        // User without permission.
        $this->setUser($this->user);
        assign_capability('moodle/competency:templateview', CAP_PROHIBIT, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        try {
            external::list_templates('id', 'ASC', 0, 10, array('contextid' => $syscontextid), 'children', false);
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        // User with category permissions.
        assign_capability('moodle/competency:templateview', CAP_PREVENT, $this->userrole, $syscontextid, true);
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $this->userrole, $catcontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $result = external::list_templates('id', 'ASC', 0, 10, array('contextid' => $syscontextid), 'children', false);
        $result = external_api::clean_returnvalue(external::list_templates_returns(), $result);
        $this->assertCount(2, $result);
        $this->assertEquals($cat1->id, $result[0]['id']);
        $this->assertEquals($cat2->id, $result[1]['id']);

        // User with system permissions.
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $result = external::list_templates('id', 'DESC', 0, 3, array('contextid' => $catcontextid), 'parents', false);
        $result = external_api::clean_returnvalue(external::list_templates_returns(), $result);
        $this->assertCount(3, $result);
        $this->assertEquals($cat2->id, $result[0]['id']);
        $this->assertEquals($cat1->id, $result[1]['id']);
        $this->assertEquals($sys2->id, $result[2]['id']);
    }

    /**
     * List templates using competency.
     */
    public function test_list_templates_using_competency() {
        $this->setUser($this->creator);

        // Create a template.
        $template1 = $this->create_template(1, true);
        $template2 = $this->create_template(2, true);
        $template3 = $this->create_template(3, true);
        $template4 = $this->create_template(4, true);

        // Create a competency.
        $framework = $this->create_competency_framework(1, true);
        $competency1 = $this->create_competency(1, $framework->id);
        $competency2 = $this->create_competency(2, $framework->id);

        // Add the competency.
        external::add_competency_to_template($template1->id, $competency1->id);
        external::add_competency_to_template($template2->id, $competency1->id);
        external::add_competency_to_template($template3->id, $competency1->id);

        external::add_competency_to_template($template4->id, $competency2->id);

        $listcomp1 = external::list_templates_using_competency($competency1->id);
        $listcomp2 = external::list_templates_using_competency($competency2->id);

        // Test count_templates_using_competency.
        $counttempcomp1 = external::count_templates_using_competency($competency1->id);
        $counttempcomp2 = external::count_templates_using_competency($competency2->id);

        $comptemp1 = $listcomp1[0];
        $comptemp2 = $listcomp1[1];
        $comptemp3 = $listcomp1[2];

        $comptemp4 = $listcomp2[0];

        $this->assertCount(3, $listcomp1);
        $this->assertCount(1, $listcomp2);
        $this->assertEquals(3, $counttempcomp1);
        $this->assertEquals(1, $counttempcomp2);
        $this->assertEquals($template1->id, $comptemp1->id);
        $this->assertEquals($template2->id, $comptemp2->id);
        $this->assertEquals($template3->id, $comptemp3->id);
        $this->assertEquals($template4->id, $comptemp4->id);
    }

    public function test_count_templates() {
        $syscontextid = context_system::instance()->id;
        $catcontextid = context_coursecat::instance($this->category->id)->id;

        // Creating a few templates.
        $this->setUser($this->creator);
        $sys1 = $this->create_template(1, true);
        $sys2 = $this->create_template(2, true);
        $cat1 = $this->create_template(3, false);
        $cat2 = $this->create_template(4, false);
        $cat3 = $this->create_template(5, false);

        // User without permission.
        $this->setUser($this->user);
        assign_capability('moodle/competency:templateview', CAP_PROHIBIT, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        try {
            external::count_templates(array('contextid' => $syscontextid), 'children');
            $this->fail('Invalid permissions');
        } catch (required_capability_exception $e) {
            // All good.
        }

        // User with category permissions.
        assign_capability('moodle/competency:templateview', CAP_PREVENT, $this->userrole, $syscontextid, true);
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $this->userrole, $catcontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $result = external::count_templates(array('contextid' => $syscontextid), 'children');
        $result = external_api::clean_returnvalue(external::count_templates_returns(), $result);
        $this->assertEquals(3, $result);

        // User with system permissions.
        assign_capability('moodle/competency:templateview', CAP_ALLOW, $this->userrole, $syscontextid, true);
        accesslib_clear_all_caches_for_unit_testing();
        $result = external::count_templates(array('contextid' => $catcontextid), 'parents');
        $result = external_api::clean_returnvalue(external::count_templates_returns(), $result);
        $this->assertEquals(5, $result);
    }

    /**
     * Test that we can add related competencies.
     *
     * @return void
     */
    public function test_add_related_competency() {
        global $DB;
        $this->setUser($this->creator);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $framework2 = $lpg->create_framework();
        $competency1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $competency2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $competency3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $competency4 = $lpg->create_competency(array('competencyframeworkid' => $framework2->get('id')));

        // The lower one always as competencyid.
        $result = external::add_related_competency($competency1->get('id'), $competency2->get('id'));
        $result = external_api::clean_returnvalue(external::add_related_competency_returns(), $result);
        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists_select(
            related_competency::TABLE, 'competencyid = :cid AND relatedcompetencyid = :rid',
            array(
                'cid' => $competency1->get('id'),
                'rid' => $competency2->get('id')
            )
        ));
        $this->assertFalse($DB->record_exists_select(
            related_competency::TABLE, 'competencyid = :cid AND relatedcompetencyid = :rid',
            array(
                'cid' => $competency2->get('id'),
                'rid' => $competency1->get('id')
            )
        ));

        $result = external::add_related_competency($competency3->get('id'), $competency1->get('id'));
        $result = external_api::clean_returnvalue(external::add_related_competency_returns(), $result);
        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists_select(
            related_competency::TABLE, 'competencyid = :cid AND relatedcompetencyid = :rid',
            array(
                'cid' => $competency1->get('id'),
                'rid' => $competency3->get('id')
            )
        ));
        $this->assertFalse($DB->record_exists_select(
            related_competency::TABLE, 'competencyid = :cid AND relatedcompetencyid = :rid',
            array(
                'cid' => $competency3->get('id'),
                'rid' => $competency1->get('id')
            )
        ));

        // We can not allow a duplicate relation, not even in the other direction.
        $this->assertEquals(1, $DB->count_records_select(related_competency::TABLE,
            'competencyid = :cid AND relatedcompetencyid = :rid',
            array('cid' => $competency1->get('id'), 'rid' => $competency2->get('id'))));
        $this->assertEquals(0, $DB->count_records_select(related_competency::TABLE,
            'competencyid = :cid AND relatedcompetencyid = :rid',
            array('rid' => $competency1->get('id'), 'cid' => $competency2->get('id'))));
        $result = external::add_related_competency($competency2->get('id'), $competency1->get('id'));
        $result = external_api::clean_returnvalue(external::add_related_competency_returns(), $result);
        $this->assertTrue($result);
        $this->assertEquals(1, $DB->count_records_select(related_competency::TABLE,
            'competencyid = :cid AND relatedcompetencyid = :rid',
            array('cid' => $competency1->get('id'), 'rid' => $competency2->get('id'))));
        $this->assertEquals(0, $DB->count_records_select(related_competency::TABLE,
            'competencyid = :cid AND relatedcompetencyid = :rid',
            array('rid' => $competency1->get('id'), 'cid' => $competency2->get('id'))));

        // Check that we cannot create links across frameworks.
        try {
            external::add_related_competency($competency1->get('id'), $competency4->get('id'));
            $this->fail('Exception expected due mis-use of shared competencies');
        } catch (invalid_persistent_exception $e) {
            // Yay!
        }

        // User without permission.
        $this->setUser($this->user);

        // Check we can not add the related competency now.
        try {
            external::add_related_competency($competency1->get('id'), $competency3->get('id'));
            $this->fail('Exception expected due to not permissions to manage template competencies');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

    }

    /**
     * Test that we can remove related competencies.
     *
     * @return void
     */
    public function test_remove_related_competency() {
        $this->setUser($this->creator);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $rc1 = $lpg->create_related_competency(array('competencyid' => $c1->get('id'), 'relatedcompetencyid' => $c2->get('id')));
        $rc2 = $lpg->create_related_competency(array('competencyid' => $c2->get('id'), 'relatedcompetencyid' => $c3->get('id')));

        $this->assertEquals(2, related_competency::count_records());

        // Returns false when the relation does not exist.
        $result = external::remove_related_competency($c1->get('id'), $c3->get('id'));
        $result = external_api::clean_returnvalue(external::remove_related_competency_returns(), $result);
        $this->assertFalse($result);

        // Returns true on success.
        $result = external::remove_related_competency($c2->get('id'), $c3->get('id'));
        $result = external_api::clean_returnvalue(external::remove_related_competency_returns(), $result);
        $this->assertTrue($result);
        $this->assertEquals(1, related_competency::count_records());

        // We don't need to specify competencyid and relatedcompetencyid in the right order.
        $result = external::remove_related_competency($c2->get('id'), $c1->get('id'));
        $result = external_api::clean_returnvalue(external::remove_related_competency_returns(), $result);
        $this->assertTrue($result);
        $this->assertEquals(0, related_competency::count_records());
    }

    /**
     * Test that we can search and include related competencies.
     *
     * @return void
     */
    public function test_search_competencies_including_related() {
        $this->setUser($this->creator);

        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c5 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // We have 1-2, 1-3, 2-4, and no relation between 2-3 nor 1-4 nor 5.
        $rc12 = $lpg->create_related_competency(array('competencyid' => $c1->get('id'), 'relatedcompetencyid' => $c2->get('id')));
        $rc13 = $lpg->create_related_competency(array('competencyid' => $c1->get('id'), 'relatedcompetencyid' => $c3->get('id')));
        $rc24 = $lpg->create_related_competency(array('competencyid' => $c2->get('id'), 'relatedcompetencyid' => $c4->get('id')));

        $result = external::search_competencies('comp', $framework->get('id'), true);
        $result = external_api::clean_returnvalue(external::search_competencies_returns(), $result);

        $this->assertCount(5, $result);

    }

    /**
     * Test that we can add competency to plan if we have the right capability.
     *
     * @return void
     */
    public function test_add_competency_to_plan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $usermanage = $dg->create_user();
        $user = $dg->create_user();

        $syscontext = context_system::instance();

        // Creating specific roles.
        $managerole = $dg->create_role(array(
            'name' => 'User manage',
            'shortname' => 'manage'
        ));

        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $managerole, $syscontext->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $managerole, $syscontext->id);

        $dg->role_assign($managerole, $usermanage->id, $syscontext->id);

        $this->setUser($usermanage);
        $plan = array (
            'userid' => $usermanage->id,
            'status' => \core_competency\plan::STATUS_ACTIVE
        );
        $pl1 = $lpg->create_plan($plan);
        $framework = $lpg->create_framework();
        $competency = $lpg->create_competency(
                array('competencyframeworkid' => $framework->get('id'))
                );
        $this->assertTrue(external::add_competency_to_plan($pl1->get('id'), $competency->get('id')));

        // A competency cannot be added to plan based on template.
        $template = $lpg->create_template();
        $plan = array (
            'userid' => $usermanage->id,
            'status' => \core_competency\plan::STATUS_ACTIVE,
            'templateid' => $template->get('id')
        );
        $pl2 = $lpg->create_plan($plan);
        try {
            external::add_competency_to_plan($pl2->get('id'), $competency->get('id'));
            $this->fail('A competency cannot be added to plan based on template');
        } catch (coding_exception $ex) {
            $this->assertTrue(true);
        }

        // User without capability cannot add competency to a plan.
        $this->setUser($user);
        try {
            external::add_competency_to_plan($pl1->get('id'), $competency->get('id'));
            $this->fail('User without capability cannot add competency to a plan');
        } catch (required_capability_exception $ex) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test that we can add competency to plan if we have the right capability.
     *
     * @return void
     */
    public function test_remove_competency_from_plan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $usermanage = $dg->create_user();
        $user = $dg->create_user();

        $syscontext = context_system::instance();

        // Creating specific roles.
        $managerole = $dg->create_role(array(
            'name' => 'User manage',
            'shortname' => 'manage'
        ));

        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $managerole, $syscontext->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $managerole, $syscontext->id);

        $dg->role_assign($managerole, $usermanage->id, $syscontext->id);

        $this->setUser($usermanage);
        $plan = array (
            'userid' => $usermanage->id,
            'status' => \core_competency\plan::STATUS_ACTIVE
        );
        $pl1 = $lpg->create_plan($plan);
        $framework = $lpg->create_framework();
        $competency = $lpg->create_competency(
                array('competencyframeworkid' => $framework->get('id'))
                );
        $lpg->create_plan_competency(
                array(
                    'planid' => $pl1->get('id'),
                    'competencyid' => $competency->get('id')
                    )
                );
        $this->assertTrue(external::remove_competency_from_plan($pl1->get('id'), $competency->get('id')));
        $this->assertCount(0, $pl1->get_competencies());
    }

    /**
     * Test that we can add competency to plan if we have the right capability.
     *
     * @return void
     */
    public function test_reorder_plan_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $usermanage = $dg->create_user();
        $user = $dg->create_user();

        $syscontext = context_system::instance();

        // Creating specific roles.
        $managerole = $dg->create_role(array(
            'name' => 'User manage',
            'shortname' => 'manage'
        ));

        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $managerole, $syscontext->id);
        assign_capability('moodle/competency:planview', CAP_ALLOW, $managerole, $syscontext->id);

        $dg->role_assign($managerole, $usermanage->id, $syscontext->id);

        $this->setUser($usermanage);
        $plan = array (
            'userid' => $usermanage->id,
            'status' => \core_competency\plan::STATUS_ACTIVE
        );
        $pl1 = $lpg->create_plan($plan);
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c5 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        $lpg->create_plan_competency(array('planid' => $pl1->get('id'), 'competencyid' => $c1->get('id'), 'sortorder' => 1));
        $lpg->create_plan_competency(array('planid' => $pl1->get('id'), 'competencyid' => $c2->get('id'), 'sortorder' => 2));
        $lpg->create_plan_competency(array('planid' => $pl1->get('id'), 'competencyid' => $c3->get('id'), 'sortorder' => 3));
        $lpg->create_plan_competency(array('planid' => $pl1->get('id'), 'competencyid' => $c4->get('id'), 'sortorder' => 4));
        $lpg->create_plan_competency(array('planid' => $pl1->get('id'), 'competencyid' => $c5->get('id'), 'sortorder' => 5));

        // Test if removing competency from plan don't create sortorder holes.
        external::remove_competency_from_plan($pl1->get('id'), $c4->get('id'));
        $plancomp5 = plan_competency::get_record(array(
            'planid' => $pl1->get('id'),
            'competencyid' => $c5->get('id')
        ));

        $this->assertEquals(3, $plancomp5->get('sortorder'));

        $this->assertTrue(external::reorder_plan_competency($pl1->get('id'), $c2->get('id'), $c5->get('id')));
        $this->assertTrue(external::reorder_plan_competency($pl1->get('id'), $c3->get('id'), $c1->get('id')));
        $plancompetencies = plan_competency::get_records(array('planid' => $pl1->get('id')), 'sortorder', 'ASC');
        $plcmp1 = $plancompetencies[0];
        $plcmp2 = $plancompetencies[1];
        $plcmp3 = $plancompetencies[2];
        $plcmp4 = $plancompetencies[3];

        $this->assertEquals($plcmp1->get('competencyid'), $c3->get('id'));
        $this->assertEquals($plcmp2->get('competencyid'), $c1->get('id'));
        $this->assertEquals($plcmp3->get('competencyid'), $c5->get('id'));
        $this->assertEquals($plcmp4->get('competencyid'), $c2->get('id'));
    }

    /**
     * Test resolving sortorder when we creating competency.
     */
    public function test_fix_sortorder_when_creating_competency() {
        $this->resetAfterTest(true);
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();

        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'sortorder' => 20));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'sortorder' => 1));

        $this->assertEquals(0, $c1->get('sortorder'));
        $this->assertEquals(1, $c2->get('sortorder'));
        $this->assertEquals(2, $c3->get('sortorder'));
    }

    /**
     * Test resolving sortorder when we delete competency.
     */
    public function test_fix_sortorder_when_delete_competency() {
        $this->resetAfterTest(true);
        $this->setUser($this->creator);
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $framework = $lpg->create_framework();

        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2a = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'parentid' => $c2->get('id')));
        $c2b = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'parentid' => $c2->get('id')));
        $c2c = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'parentid' => $c2->get('id')));
        $c2d = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'parentid' => $c2->get('id')));

        $this->assertEquals(0, $c1->get('sortorder'));
        $this->assertEquals(1, $c2->get('sortorder'));
        $this->assertEquals(0, $c2a->get('sortorder'));
        $this->assertEquals(1, $c2b->get('sortorder'));
        $this->assertEquals(2, $c2c->get('sortorder'));
        $this->assertEquals(3, $c2d->get('sortorder'));

        $result = external::delete_competency($c1->get('id'));
        $result = external_api::clean_returnvalue(external::delete_competency_returns(), $result);

        $c2->read();
        $c2a->read();
        $c2b->read();
        $c2c->read();
        $c2d->read();

        $this->assertEquals(0, $c2->get('sortorder'));
        $this->assertEquals(0, $c2a->get('sortorder'));
        $this->assertEquals(1, $c2b->get('sortorder'));
        $this->assertEquals(2, $c2c->get('sortorder'));
        $this->assertEquals(3, $c2d->get('sortorder'));

        $result = external::delete_competency($c2b->get('id'));
        $result = external_api::clean_returnvalue(external::delete_competency_returns(), $result);

        $c2->read();
        $c2a->read();
        $c2c->read();
        $c2d->read();

        $this->assertEquals(0, $c2->get('sortorder'));
        $this->assertEquals(0, $c2a->get('sortorder'));
        $this->assertEquals(1, $c2c->get('sortorder'));
        $this->assertEquals(2, $c2d->get('sortorder'));
    }

    /**
     * Test resolving sortorder when moving a competency.
     */
    public function test_fix_sortorder_when_moving_competency() {
        $this->resetAfterTest(true);
        $this->setUser($this->creator);
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $framework = $lpg->create_framework();

        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'parentid' => $c1->get('id')));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'parentid' => $c1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2a = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'parentid' => $c2->get('id')));
        $c2b = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'parentid' => $c2->get('id')));

        $this->assertEquals(0, $c1->get('sortorder'));
        $this->assertEquals(0, $c1a->get('sortorder'));
        $this->assertEquals(1, $c1b->get('sortorder'));
        $this->assertEquals(1, $c2->get('sortorder'));
        $this->assertEquals(0, $c2a->get('sortorder'));
        $this->assertEquals(1, $c2b->get('sortorder'));

        $result = external::set_parent_competency($c2a->get('id'), $c1->get('id'));
        $result = external_api::clean_returnvalue(external::set_parent_competency_returns(), $result);

        $c1->read();
        $c1a->read();
        $c1b->read();
        $c2->read();
        $c2a->read();
        $c2b->read();

        $this->assertEquals(0, $c1->get('sortorder'));
        $this->assertEquals(0, $c1a->get('sortorder'));
        $this->assertEquals(1, $c1b->get('sortorder'));
        $this->assertEquals(2, $c2a->get('sortorder'));
        $this->assertEquals(1, $c2->get('sortorder'));
        $this->assertEquals(0, $c2b->get('sortorder'));

        // Move a root node.
        $result = external::set_parent_competency($c2->get('id'), $c1b->get('id'));
        $result = external_api::clean_returnvalue(external::set_parent_competency_returns(), $result);

        $c1->read();
        $c1a->read();
        $c1b->read();
        $c2->read();
        $c2a->read();
        $c2b->read();

        $this->assertEquals(0, $c1->get('sortorder'));
        $this->assertEquals(0, $c1a->get('sortorder'));
        $this->assertEquals(1, $c1b->get('sortorder'));
        $this->assertEquals(0, $c2->get('sortorder'));
        $this->assertEquals(0, $c2b->get('sortorder'));
        $this->assertEquals(2, $c2a->get('sortorder'));
    }

    public function test_grade_competency() {
        global $CFG;

        $this->setUser($this->creator);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $evidence = external::grade_competency($this->user->id, $c1->get('id'), 1, 'Evil note');

        $this->assertEquals('The competency rating was manually set.', $evidence->description);
        $this->assertEquals('A', $evidence->gradename);
        $this->assertEquals('Evil note', $evidence->note);

        $this->setUser($this->user);

        $this->expectException('required_capability_exception');
        $evidence = external::grade_competency($this->user->id, $c1->get('id'), 1);
    }

    public function test_grade_competency_in_course() {
        global $CFG;

        $this->setUser($this->creator);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $course = $dg->create_course(['fullname' => 'Evil course']);
        $dg->enrol_user($this->creator->id, $course->id, 'editingteacher');
        $dg->enrol_user($this->user->id, $course->id, 'student');
        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $lpg->create_course_competency(['courseid' => $course->id, 'competencyid' => $c1->get('id')]);

        $evidence = external::grade_competency_in_course($course->id, $this->user->id, $c1->get('id'), 1, 'Evil note');

        $this->assertEquals('The competency rating was manually set in the course \'Course: Evil course\'.', $evidence->description);
        $this->assertEquals('A', $evidence->gradename);
        $this->assertEquals('Evil note', $evidence->note);

        $this->setUser($this->user);

        $this->expectException('required_capability_exception');
        $evidence = external::grade_competency_in_course($course->id, $this->user->id, $c1->get('id'), 1);
    }

    public function test_grade_competency_in_plan() {
        global $CFG;

        $this->setUser($this->creator);

        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $f1 = $lpg->create_framework();

        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));

        $tpl = $lpg->create_template();
        $lpg->create_template_competency(array('templateid' => $tpl->get('id'), 'competencyid' => $c1->get('id')));

        $plan = $lpg->create_plan(array('userid' => $this->user->id, 'templateid' => $tpl->get('id'), 'name' => 'Evil'));

        $uc = $lpg->create_user_competency(array('userid' => $this->user->id, 'competencyid' => $c1->get('id')));

        $evidence = external::grade_competency_in_plan($plan->get('id'), $c1->get('id'), 1, 'Evil note');

        $this->assertEquals('The competency rating was manually set in the learning plan \'Evil\'.', $evidence->description);
        $this->assertEquals('A', $evidence->gradename);
        $this->assertEquals('Evil note', $evidence->note);

        $this->setUser($this->user);

        $this->expectException('required_capability_exception');
        $evidence = external::grade_competency_in_plan($plan->get('id'), $c1->get('id'), 1);
    }

    /**
     * Test update course competency settings.
     */
    public function test_update_course_competency_settings() {
        $this->resetAfterTest(true);

        $dg = $this->getDataGenerator();

        $course = $dg->create_course();
        $roleid = $dg->create_role();
        $noobroleid = $dg->create_role();
        $context = context_course::instance($course->id);
        $compmanager = $this->getDataGenerator()->create_user();
        $compnoob = $this->getDataGenerator()->create_user();

        assign_capability('moodle/competency:coursecompetencyconfigure', CAP_ALLOW, $roleid, $context->id, true);
        assign_capability('moodle/competency:coursecompetencyview', CAP_ALLOW, $roleid, $context->id, true);
        assign_capability('moodle/competency:coursecompetencyview', CAP_ALLOW, $noobroleid, $context->id, true);

        role_assign($roleid, $compmanager->id, $context->id);
        role_assign($noobroleid, $compnoob->id, $context->id);
        $dg->enrol_user($compmanager->id, $course->id, $roleid);
        $dg->enrol_user($compnoob->id, $course->id, $noobroleid);

        $this->setUser($compmanager);

        // Start the test.
        $result = external::update_course_competency_settings($course->id, array('pushratingstouserplans' => true));

        $settings = course_competency_settings::get_by_courseid($course->id);

        $this->assertTrue((bool)$settings->get('pushratingstouserplans'));

        $result = external::update_course_competency_settings($course->id, array('pushratingstouserplans' => false));

        $settings = course_competency_settings::get_by_courseid($course->id);

        $this->assertFalse((bool)$settings->get('pushratingstouserplans'));
        $this->setUser($compnoob);

        $this->expectException('required_capability_exception');
        $result = external::update_course_competency_settings($course->id, array('pushratingstouserplans' => true));
    }

    /**
     * Test that we can list competencies with a filter.
     *
     * @return void
     */
    public function test_list_competencies_with_filter() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));
        $c5 = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Test if removing competency from plan don't create sortorder holes.
        $filters = [];
        $sort = 'id';
        $order = 'ASC';
        $skip = 0;
        $limit = 0;
        $result = external::list_competencies($filters, $sort, $order, $skip, $limit);
        $this->assertCount(5, $result);

        $result = external::list_competencies($filters, $sort, $order, 2, $limit);
        $this->assertCount(3, $result);
        $result = external::list_competencies($filters, $sort, $order, 2, 2);
        $this->assertCount(2, $result);

        $filter = $result[0]->shortname;
        $filters[0] = ['column' => 'shortname', 'value' => $filter];
        $result = external::list_competencies($filters, $sort, $order, $skip, $limit);
        $this->assertCount(1, $result);
        $this->assertEquals($filter, $result[0]->shortname);
    }

}
