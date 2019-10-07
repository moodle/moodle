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
 * Class provider_test
 *
 * @package     core_customfield
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;
use core_customfield\privacy\provider;

/**
 * Class provider_test
 *
 * @package     core_customfield
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_customfield_privacy_testcase extends provider_testcase {

    /** @var stdClass[]  */
    private $courses = [];
    /** @var \core_customfield\category_controller[] */
    private $cfcats = [];
    /** @var \core_customfield\field_controller[] */
    private $cffields = [];

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass() {
        $handler = core_course\customfield\course_handler::create();
        $handler->delete_all();
    }

    /**
     * Set up
     */
    public function setUp() {
        $this->resetAfterTest();

        $this->cfcats[1] = $this->get_generator()->create_category();
        $this->cfcats[2] = $this->get_generator()->create_category();
        $this->cffields[11] = $this->get_generator()->create_field(
            ['categoryid' => $this->cfcats[1]->get('id'), 'type' => 'checkbox']);
        $this->cffields[12] = $this->get_generator()->create_field(
            ['categoryid' => $this->cfcats[1]->get('id'), 'type' => 'date']);
        $this->cffields[13] = $this->get_generator()->create_field(
            ['categoryid' => $this->cfcats[1]->get('id'),
            'type' => 'select', 'configdata' => ['options' => "a\nb\nc"]]);
        $this->cffields[14] = $this->get_generator()->create_field(
            ['categoryid' => $this->cfcats[1]->get('id'), 'type' => 'text']);
        $this->cffields[15] = $this->get_generator()->create_field(
            ['categoryid' => $this->cfcats[1]->get('id'), 'type' => 'textarea']);
        $this->cffields[21] = $this->get_generator()->create_field(
            ['categoryid' => $this->cfcats[2]->get('id')]);
        $this->cffields[22] = $this->get_generator()->create_field(
            ['categoryid' => $this->cfcats[2]->get('id')]);

        $this->courses[1] = $this->getDataGenerator()->create_course();
        $this->courses[2] = $this->getDataGenerator()->create_course();
        $this->courses[3] = $this->getDataGenerator()->create_course();

        $this->get_generator()->add_instance_data($this->cffields[11], $this->courses[1]->id, 1);
        $this->get_generator()->add_instance_data($this->cffields[12], $this->courses[1]->id, 1546300800);
        $this->get_generator()->add_instance_data($this->cffields[13], $this->courses[1]->id, 2);
        $this->get_generator()->add_instance_data($this->cffields[14], $this->courses[1]->id, 'Hello1');
        $this->get_generator()->add_instance_data($this->cffields[15], $this->courses[1]->id,
            ['text' => '<p>Hi there</p>', 'format' => FORMAT_HTML]);

        $this->get_generator()->add_instance_data($this->cffields[21], $this->courses[1]->id, 'hihi1');

        $this->get_generator()->add_instance_data($this->cffields[14], $this->courses[2]->id, 'Hello2');

        $this->get_generator()->add_instance_data($this->cffields[21], $this->courses[2]->id, 'hihi2');

        $this->setUser($this->getDataGenerator()->create_user());
    }

    /**
     * Get generator
     * @return core_customfield_generator
     */
    protected function get_generator() : core_customfield_generator {
        return $this->getDataGenerator()->get_plugin_generator('core_customfield');
    }

    /**
     * Test for provider::get_metadata()
     */
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('core_customfield');
        $collection = provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test for provider::get_customfields_data_contexts
     */
    public function test_get_customfields_data_contexts() {
        global $DB;
        list($sql, $params) = $DB->get_in_or_equal([$this->courses[1]->id, $this->courses[2]->id], SQL_PARAMS_NAMED);
        $r = provider::get_customfields_data_contexts('core_course', 'course', '=0',
            $sql, $params);
        $this->assertEquals([context_course::instance($this->courses[1]->id)->id,
            context_course::instance($this->courses[2]->id)->id],
            $r->get_contextids(), '', 0, 10, true);
    }

    /**
     * Test for provider::get_customfields_configuration_contexts()
     */
    public function test_get_customfields_configuration_contexts() {
        $r = provider::get_customfields_configuration_contexts('core_course', 'course');
        $this->assertEquals([context_system::instance()->id], $r->get_contextids());
    }

    /**
     * Test for provider::export_customfields_data()
     */
    public function test_export_customfields_data() {
        global $USER, $DB;
        // Hack one of the fields so it has an invalid field type.
        $invalidfieldid = $this->cffields[21]->get('id');
        $DB->update_record('customfield_field', ['id' => $invalidfieldid, 'type' => 'invalid']);

        $context = context_course::instance($this->courses[1]->id);
        $contextlist = new approved_contextlist($USER, 'core_customfield', [$context->id]);
        provider::export_customfields_data($contextlist, 'core_course', 'course', '=0', '=:i', ['i' => $this->courses[1]->id]);
        /** @var core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);

        // Make sure that all and only data for the course1 was exported.
        // There is no way to fetch all data from writer as array so we need to fetch one-by-one for each data id.
        $invaldfieldischecked = false;
        foreach ($DB->get_records('customfield_data', []) as $dbrecord) {
            $data = $writer->get_data(['Custom fields data', $dbrecord->id]);
            if ($dbrecord->instanceid == $this->courses[1]->id) {
                $this->assertEquals($dbrecord->fieldid, $data->fieldid);
                $this->assertNotEmpty($data->fieldtype);
                $this->assertNotEmpty($data->fieldshortname);
                $this->assertNotEmpty($data->fieldname);
                $invaldfieldischecked = $invaldfieldischecked ?: ($data->fieldid == $invalidfieldid);
            } else {
                $this->assertEmpty($data);
            }
        }

        // Make sure field with was checked in this test.
        $this->assertTrue($invaldfieldischecked);
    }

    /**
     * Test for provider::delete_customfields_data()
     */
    public function test_delete_customfields_data() {
        global $USER, $DB;
        $approvedcontexts = new approved_contextlist($USER, 'core_course', [context_course::instance($this->courses[1]->id)->id]);
        provider::delete_customfields_data($approvedcontexts, 'core_course', 'course');
        $this->assertEmpty($DB->get_records('customfield_data', ['instanceid' => $this->courses[1]->id]));
        $this->assertNotEmpty($DB->get_records('customfield_data', ['instanceid' => $this->courses[2]->id]));
    }

    /**
     * Test for provider::delete_customfields_configuration()
     */
    public function test_delete_customfields_configuration() {
        global $USER, $DB;
        // Remember the list of fields in the category 2 before we delete it.
        $catid1 = $this->cfcats[1]->get('id');
        $catid2 = $this->cfcats[2]->get('id');
        $fids2 = $DB->get_fieldset_select('customfield_field', 'id', 'categoryid=?', [$catid2]);
        $this->assertNotEmpty($fids2);
        list($fsql, $fparams) = $DB->get_in_or_equal($fids2, SQL_PARAMS_NAMED);
        $this->assertNotEmpty($DB->get_records_select('customfield_data', 'fieldid ' . $fsql, $fparams));

        // A little hack here, modify customfields configuration so they have different itemids.
        $DB->update_record('customfield_category', ['id' => $catid2, 'itemid' => 1]);
        $contextlist = new approved_contextlist($USER, 'core_course', [context_system::instance()->id]);
        provider::delete_customfields_configuration($contextlist, 'core_course', 'course', '=:i', ['i' => 1]);

        // Make sure everything for category $catid2 is gone but present for $catid1.
        $this->assertEmpty($DB->get_records('customfield_category', ['id' => $catid2]));
        $this->assertEmpty($DB->get_records_select('customfield_field', 'id ' . $fsql, $fparams));
        $this->assertEmpty($DB->get_records_select('customfield_data', 'fieldid ' . $fsql, $fparams));

        $this->assertNotEmpty($DB->get_records('customfield_category', ['id' => $catid1]));
        $fids1 = $DB->get_fieldset_select('customfield_field', 'id', 'categoryid=?', [$catid1]);
        list($fsql1, $fparams1) = $DB->get_in_or_equal($fids1, SQL_PARAMS_NAMED);
        $this->assertNotEmpty($DB->get_records_select('customfield_field', 'id ' . $fsql1, $fparams1));
        $this->assertNotEmpty($DB->get_records_select('customfield_data', 'fieldid ' . $fsql1, $fparams1));
    }

    /**
     * Test for provider::delete_customfields_configuration_for_context()
     */
    public function test_delete_customfields_configuration_for_context() {
        global $USER, $DB;
        // Remember the list of fields in the category 2 before we delete it.
        $catid1 = $this->cfcats[1]->get('id');
        $catid2 = $this->cfcats[2]->get('id');
        $fids2 = $DB->get_fieldset_select('customfield_field', 'id', 'categoryid=?', [$catid2]);
        $this->assertNotEmpty($fids2);
        list($fsql, $fparams) = $DB->get_in_or_equal($fids2, SQL_PARAMS_NAMED);
        $this->assertNotEmpty($DB->get_records_select('customfield_data', 'fieldid ' . $fsql, $fparams));

        // A little hack here, modify customfields configuration so they have different contexts.
        $context = context_user::instance($USER->id);
        $DB->update_record('customfield_category', ['id' => $catid2, 'contextid' => $context->id]);
        provider::delete_customfields_configuration_for_context('core_course', 'course', $context);

        // Make sure everything for category $catid2 is gone but present for $catid1.
        $this->assertEmpty($DB->get_records('customfield_category', ['id' => $catid2]));
        $this->assertEmpty($DB->get_records_select('customfield_field', 'id ' . $fsql, $fparams));
        $this->assertEmpty($DB->get_records_select('customfield_data', 'fieldid ' . $fsql, $fparams));

        $this->assertNotEmpty($DB->get_records('customfield_category', ['id' => $catid1]));
        $fids1 = $DB->get_fieldset_select('customfield_field', 'id', 'categoryid=?', [$catid1]);
        list($fsql1, $fparams1) = $DB->get_in_or_equal($fids1, SQL_PARAMS_NAMED);
        $this->assertNotEmpty($DB->get_records_select('customfield_field', 'id ' . $fsql1, $fparams1));
        $this->assertNotEmpty($DB->get_records_select('customfield_data', 'fieldid ' . $fsql1, $fparams1));
    }

    /**
     * Test for provider::delete_customfields_data_for_context()
     */
    public function test_delete_customfields_data_for_context() {
        global $DB;
        provider::delete_customfields_data_for_context('core_course', 'course',
            context_course::instance($this->courses[1]->id));
        $fids2 = $DB->get_fieldset_select('customfield_field', 'id', '1=1', []);
        list($fsql, $fparams) = $DB->get_in_or_equal($fids2, SQL_PARAMS_NAMED);
        $fparams['course1'] = $this->courses[1]->id;
        $fparams['course2'] = $this->courses[2]->id;
        $this->assertEmpty($DB->get_records_select('customfield_data', 'instanceid = :course1 AND fieldid ' . $fsql, $fparams));
        $this->assertNotEmpty($DB->get_records_select('customfield_data', 'instanceid = :course2 AND fieldid ' . $fsql, $fparams));
    }
}
