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
 * Persistent class tests.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

/**
 * Persistent testcase.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_persistent_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_properties_definition() {
        $expected = array(
            'shortname' => array(
                'type' => PARAM_TEXT,
                'default' => '',
                'null' => NULL_NOT_ALLOWED
            ),
            'idnumber' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED
            ),
            'description' => array(
                'type' => PARAM_TEXT,
                'default' => '',
                'null' => NULL_NOT_ALLOWED
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML,
                'null' => NULL_NOT_ALLOWED
            ),
            'parentid' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'null' => NULL_NOT_ALLOWED
            ),
            'path' => array(
                'type' => PARAM_RAW,
                'default' => '',
                'null' => NULL_NOT_ALLOWED
            ),
            'sortorder' => array(
                'type' => PARAM_INT,
                'message' => new lang_string('invalidrequest', 'error'),
                'null' => NULL_NOT_ALLOWED
            ),
            'competencyframeworkid' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'null' => NULL_ALLOWED
            ),
            'id' => array(
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED
            ),
            'timecreated' => array(
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED
            ),
            'timemodified' => array(
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED
            ),
            'usermodified' => array(
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED
            ),
            'ruletype' => array(
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'ruleconfig' => array(
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'ruleoutcome' => array(
                'type' => PARAM_RAW,
                'default' => 0,
                'null' => NULL_NOT_ALLOWED
            ),
            'scaleid' => array(
                'default' => null,
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED
            ),
            'scaleconfiguration' => array(
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED
            )
        );
        $this->assertEquals($expected, core_competency_testable_persistent::properties_definition());
    }

    public function test_to_record() {
        $p = new core_competency_testable_persistent();
        $expected = (object) array(
            'shortname' => '',
            'idnumber' => null,
            'description' => '',
            'descriptionformat' => FORMAT_HTML,
            'parentid' => 0,
            'path' => '',
            'sortorder' => null,
            'competencyframeworkid' => null,
            'id' => 0,
            'timecreated' => 0,
            'timemodified' => 0,
            'usermodified' => 0,
            'ruletype' => null,
            'ruleconfig' => null,
            'ruleoutcome' => 0,
            'scaleid' => null,
            'scaleconfiguration' => null,
        );
        $this->assertEquals($expected, $p->to_record());
    }

    public function test_from_record() {
        $p = new core_competency_testable_persistent();
        $data = (object) array(
            'shortname' => 'ddd',
            'idnumber' => 'abc',
            'description' => 'xyz',
            'descriptionformat' => FORMAT_PLAIN,
            'parentid' => 999,
            'path' => '/a/b/c',
            'sortorder' => 12,
            'competencyframeworkid' => 5,
            'id' => 1,
            'timecreated' => 2,
            'timemodified' => 3,
            'usermodified' => 4,
            'ruletype' => null,
            'ruleconfig' => null,
            'ruleoutcome' => 0,
            'scaleid' => null,
            'scaleconfiguration' => null,
        );
        $p->from_record($data);
        $this->assertEquals($data, $p->to_record());
    }

    public function test_from_record_invalid_param() {
        $p = new core_competency_testable_persistent();
        $data = (object) array(
            'invalidparam' => 'abc'
        );
        $this->setExpectedException('coding_exception');
        $p->from_record($data);
    }

    public function test_validate() {
        $data = (object) array(
            'idnumber' => 'abc',
            'sortorder' => 0
        );
        $p = new core_competency_testable_persistent(0, $data);
        $this->assertFalse(isset($p->beforevalidate));
        $this->assertTrue($p->validate());
        $this->assertTrue(isset($p->beforevalidate));
        $this->assertTrue($p->is_valid());
        $this->assertEquals(array(), $p->get_errors());
        $p->set_descriptionformat(-100);

        $expected = array(
            'descriptionformat' => new lang_string('invaliddata', 'error'),
        );
        $this->assertEquals($expected, $p->validate());
        $this->assertFalse($p->is_valid());
        $this->assertEquals($expected, $p->get_errors());
    }

    public function test_validation_required() {
        $data = (object) array(
            'idnumber' => 'abc'
        );
        $p = new core_competency_testable_persistent(0, $data);
        $expected = array(
            'sortorder' => new lang_string('requiredelement', 'form'),
        );
        $this->assertFalse($p->is_valid());
        $this->assertEquals($expected, $p->get_errors());
    }

    public function test_validation_custom() {
        $data = (object) array(
            'idnumber' => 'abc',
            'sortorder' => 10,
        );
        $p = new core_competency_testable_persistent(0, $data);
        $expected = array(
            'sortorder' => new lang_string('invalidkey', 'error'),
        );
        $this->assertFalse($p->is_valid());
        $this->assertEquals($expected, $p->get_errors());
    }

    public function test_validation_custom_message() {
        $data = (object) array(
            'idnumber' => 'abc',
            'sortorder' => 'abc',
        );
        $p = new core_competency_testable_persistent(0, $data);
        $expected = array(
            'sortorder' => new lang_string('invalidrequest', 'error'),
        );
        $this->assertFalse($p->is_valid());
        $this->assertEquals($expected, $p->get_errors());
    }

    public function test_validation_choices() {
        $data = (object) array(
            'idnumber' => 'abc',
            'sortorder' => 0,
            'descriptionformat' => -100
        );
        $p = new core_competency_testable_persistent(0, $data);
        $expected = array(
            'descriptionformat' => new lang_string('invaliddata', 'error'),
        );
        $this->assertFalse($p->is_valid());
        $this->assertEquals($expected, $p->get_errors());
    }

    public function test_validation_type() {
        $data = (object) array(
            'idnumber' => 'abc',
            'sortorder' => 'NaN'
        );
        $p = new core_competency_testable_persistent(0, $data);
        $this->assertFalse($p->is_valid());
        $this->assertArrayHasKey('sortorder', $p->get_errors());
    }

    public function test_validation_null() {
        $data = (object) array(
            'idnumber' => null,
            'sortorder' => 0,
            'competencyframeworkid' => 'bad!'
        );
        $p = new core_competency_testable_persistent(0, $data);
        $this->assertFalse($p->is_valid());
        $this->assertArrayHasKey('idnumber', $p->get_errors());
        $this->assertArrayHasKey('competencyframeworkid', $p->get_errors());
        $p->set_idnumber('abc');
        $this->assertFalse($p->is_valid());
        $this->assertArrayNotHasKey('idnumber', $p->get_errors());
        $this->assertArrayHasKey('competencyframeworkid', $p->get_errors());
        $p->set_competencyframeworkid(null);
        $this->assertTrue($p->is_valid());
        $this->assertArrayNotHasKey('competencyframeworkid', $p->get_errors());
    }

    public function test_create() {
        global $DB;
        $p = new core_competency_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $this->assertFalse(isset($p->beforecreate));
        $this->assertFalse(isset($p->aftercreate));
        $p->create();
        $record = $DB->get_record(core_competency_testable_persistent::TABLE, array('id' => $p->get_id()), '*', MUST_EXIST);
        $expected = $p->to_record();
        $this->assertTrue(isset($p->beforecreate));
        $this->assertTrue(isset($p->aftercreate));
        $this->assertEquals($expected->sortorder, $record->sortorder);
        $this->assertEquals($expected->idnumber, $record->idnumber);
        $this->assertEquals($expected->id, $record->id);
        $this->assertTrue($p->is_valid()); // Should always be valid after a create.
    }

    public function test_update() {
        global $DB;
        $p = new core_competency_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $p->create();
        $id = $p->get_id();
        $p->set_sortorder(456);
        $p->from_record((object) array('idnumber' => 'def'));
        $this->assertFalse(isset($p->beforeupdate));
        $this->assertFalse(isset($p->afterupdate));
        $p->update();

        $expected = $p->to_record();
        $record = $DB->get_record(core_competency_testable_persistent::TABLE, array('id' => $p->get_id()), '*', MUST_EXIST);
        $this->assertTrue(isset($p->beforeupdate));
        $this->assertTrue(isset($p->afterupdate));
        $this->assertEquals($id, $record->id);
        $this->assertEquals(456, $record->sortorder);
        $this->assertEquals('def', $record->idnumber);
        $this->assertTrue($p->is_valid()); // Should always be valid after an update.
    }

    public function test_read() {
        $p = new core_competency_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $p->create();
        unset($p->beforevalidate);
        unset($p->beforecreate);
        unset($p->aftercreate);

        $p2 = new core_competency_testable_persistent($p->get_id());
        $this->assertEquals($p, $p2);

        $p3 = new core_competency_testable_persistent();
        $p3->set_id($p->get_id());
        $p3->read();
        $this->assertEquals($p, $p3);
    }

    public function test_delete() {
        global $DB;

        $p = new core_competency_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $p->create();
        $this->assertNotEquals(0, $p->get_id());
        $this->assertTrue($DB->record_exists_select(core_competency_testable_persistent::TABLE, 'id = ?', array($p->get_id())));
        $this->assertFalse(isset($p->beforedelete));
        $this->assertFalse(isset($p->afterdelete));

        $p->delete();
        $this->assertFalse($DB->record_exists_select(core_competency_testable_persistent::TABLE, 'id = ?', array($p->get_id())));
        $this->assertEquals(0, $p->get_id());
        $this->assertEquals(true, $p->beforedelete);
        $this->assertEquals(true, $p->afterdelete);
    }

    public function test_has_property() {
        $this->assertFalse(core_competency_testable_persistent::has_property('unknown'));
        $this->assertTrue(core_competency_testable_persistent::has_property('idnumber'));
    }

    public function test_custom_setter_getter() {
        global $DB;

        $path = array(1, 2, 3);
        $json = json_encode($path);

        $p = new core_competency_testable_persistent(0, (object) array('sortorder' => 0, 'idnumber' => 'abc'));
        $p->set_path($path);
        $this->assertEquals($path, $p->get_path());
        $this->assertEquals($json, $p->to_record()->path);

        $p->create();
        $record = $DB->get_record(core_competency_testable_persistent::TABLE, array('id' => $p->get_id()), 'id, path', MUST_EXIST);
        $this->assertEquals($json, $record->path);
    }

    public function test_record_exists() {
        global $DB;
        $this->assertFalse($DB->record_exists(core_competency_testable_persistent::TABLE, array('idnumber' => 'abc')));
        $p = new core_competency_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $p->create();
        $id = $p->get_id();
        $this->assertTrue(core_competency_testable_persistent::record_exists($id));
        $this->assertTrue($DB->record_exists(core_competency_testable_persistent::TABLE, array('idnumber' => 'abc')));
        $p->delete();
        $this->assertFalse(core_competency_testable_persistent::record_exists($id));
    }

    public function test_get_sql_fields() {
        $expected = '' .
            'c.id AS comp_id, ' .
            'c.shortname AS comp_shortname, ' .
            'c.idnumber AS comp_idnumber, ' .
            'c.description AS comp_description, ' .
            'c.descriptionformat AS comp_descriptionformat, ' .
            'c.parentid AS comp_parentid, ' .
            'c.path AS comp_path, ' .
            'c.sortorder AS comp_sortorder, ' .
            'c.competencyframeworkid AS comp_competencyframeworkid, ' .
            'c.ruletype AS comp_ruletype, ' .
            'c.ruleconfig AS comp_ruleconfig, ' .
            'c.ruleoutcome AS comp_ruleoutcome, ' .
            'c.scaleid AS comp_scaleid, ' .
            'c.scaleconfiguration AS comp_scaleconfiguration, ' .
            'c.timecreated AS comp_timecreated, ' .
            'c.timemodified AS comp_timemodified, ' .
            'c.usermodified AS comp_usermodified';
        $this->assertEquals($expected, core_competency_testable_persistent::get_sql_fields('c', 'comp_'));
    }

    /**
     * @expectedException               coding_exception
     * @expectedExceptionMessageRegExp  /The alias .+ exceeds 30 characters/
     */
    public function test_get_sql_fields_too_long() {
        core_competency_testable_persistent::get_sql_fields('c');
    }
}

/**
 * Example persistent class.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_testable_persistent extends \core_competency\persistent {

    const TABLE = 'competency';

    protected static function define_properties() {
        return array(
            'shortname' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            ),
            'idnumber' => array(
                'type' => PARAM_TEXT,
            ),
            'description' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
            'parentid' => array(
                'type' => PARAM_INT,
                'default' => 0
            ),
            'path' => array(
                'type' => PARAM_RAW,
                'default' => ''
            ),
            'sortorder' => array(
                'type' => PARAM_INT,
                'message' => new lang_string('invalidrequest', 'error')
            ),
            'competencyframeworkid' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'null' => NULL_ALLOWED
            ),
            'ruletype' => array(
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'ruleconfig' => array(
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'ruleoutcome' => array(
                'type' => PARAM_RAW,
                'default' => 0
            ),
            'scaleid' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ),
            'scaleconfiguration' => array(
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED
            )
        );
    }

    protected function before_validate() {
        $this->beforevalidate = true;
    }

    protected function before_create() {
        $this->beforecreate = true;
    }

    protected function before_update() {
        $this->beforeupdate = true;
    }

    protected function before_delete() {
        $this->beforedelete = true;
    }

    protected function after_create() {
        $this->aftercreate = true;
    }

    protected function after_update($result) {
        $this->afterupdate = true;
    }

    protected function after_delete($result) {
        $this->afterdelete = true;
    }

    public function get_path() {
        $value = $this->get('path');
        if (!empty($value)) {
            $value = json_decode($value);
        }
        return $value;
    }

    public function set_path($value) {
        if (!empty($value)) {
            $value = json_encode($value);
        }
        $this->set('path', $value);
    }

    protected function validate_sortorder($value) {
        if ($value == 10) {
            return new lang_string('invalidkey', 'error');
        }
        return true;
    }

}