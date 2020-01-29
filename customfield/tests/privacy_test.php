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

    /**
     * Generate data.
     *
     * @return array
     */
    protected function generate_test_data(): array {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');
        $cfcats[1] = $generator->create_category();
        $cfcats[2] = $generator->create_category();
        $cffields[11] = $generator->create_field(
            ['categoryid' => $cfcats[1]->get('id'), 'type' => 'checkbox']);
        $cffields[12] = $generator->create_field(
            ['categoryid' => $cfcats[1]->get('id'), 'type' => 'date']);
        $cffields[13] = $generator->create_field(
            ['categoryid' => $cfcats[1]->get('id'),
            'type' => 'select', 'configdata' => ['options' => "a\nb\nc"]]);
        $cffields[14] = $generator->create_field(
            ['categoryid' => $cfcats[1]->get('id'), 'type' => 'text']);
        $cffields[15] = $generator->create_field(
            ['categoryid' => $cfcats[1]->get('id'), 'type' => 'textarea']);
        $cffields[21] = $generator->create_field(
            ['categoryid' => $cfcats[2]->get('id')]);
        $cffields[22] = $generator->create_field(
            ['categoryid' => $cfcats[2]->get('id')]);

        $courses[1] = $this->getDataGenerator()->create_course();
        $courses[2] = $this->getDataGenerator()->create_course();
        $courses[3] = $this->getDataGenerator()->create_course();

        $generator->add_instance_data($cffields[11], $courses[1]->id, 1);
        $generator->add_instance_data($cffields[12], $courses[1]->id, 1546300800);
        $generator->add_instance_data($cffields[13], $courses[1]->id, 2);
        $generator->add_instance_data($cffields[14], $courses[1]->id, 'Hello1');
        $generator->add_instance_data($cffields[15], $courses[1]->id,
            ['text' => '<p>Hi there</p>', 'format' => FORMAT_HTML]);

        $generator->add_instance_data($cffields[21], $courses[1]->id, 'hihi1');

        $generator->add_instance_data($cffields[14], $courses[2]->id, 'Hello2');

        $generator->add_instance_data($cffields[21], $courses[2]->id, 'hihi2');

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        return [
            'user' => $user,
            'cfcats' => $cfcats,
            'cffields' => $cffields,
            'courses' => $courses,
        ];
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
        [
            'cffields' => $cffields,
            'cfcats' => $cfcats,
            'courses' => $courses,
        ] = $this->generate_test_data();

        list($sql, $params) = $DB->get_in_or_equal([$courses[1]->id, $courses[2]->id], SQL_PARAMS_NAMED);
        $r = provider::get_customfields_data_contexts('core_course', 'course', '=0',
            $sql, $params);
        $this->assertEquals([context_course::instance($courses[1]->id)->id,
            context_course::instance($courses[2]->id)->id],
            $r->get_contextids(), '', 0, 10, true);
    }

    /**
     * Test for provider::get_customfields_configuration_contexts()
     */
    public function test_get_customfields_configuration_contexts() {
        $this->generate_test_data();

        $r = provider::get_customfields_configuration_contexts('core_course', 'course');
        $this->assertEquals([context_system::instance()->id], $r->get_contextids());
    }

    /**
     * Test for provider::export_customfields_data()
     */
    public function test_export_customfields_data() {
        global $USER, $DB;
        $this->resetAfterTest();
        [
            'cffields' => $cffields,
            'cfcats' => $cfcats,
            'courses' => $courses,
        ] = $this->generate_test_data();

        // Hack one of the fields so it has an invalid field type.
        $invalidfieldid = $cffields[21]->get('id');
        $DB->update_record('customfield_field', ['id' => $invalidfieldid, 'type' => 'invalid']);

        $context = context_course::instance($courses[1]->id);
        $contextlist = new approved_contextlist($USER, 'core_customfield', [$context->id]);
        provider::export_customfields_data($contextlist, 'core_course', 'course', '=0', '=:i', ['i' => $courses[1]->id]);
        /** @var core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);

        // Make sure that all and only data for the course1 was exported.
        // There is no way to fetch all data from writer as array so we need to fetch one-by-one for each data id.
        $invaldfieldischecked = false;
        foreach ($DB->get_records('customfield_data', []) as $dbrecord) {
            $data = $writer->get_data(['Custom fields data', $dbrecord->id]);
            if ($dbrecord->instanceid == $courses[1]->id) {
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
        $this->resetAfterTest();
        [
            'cffields' => $cffields,
            'cfcats' => $cfcats,
            'courses' => $courses,
        ] = $this->generate_test_data();

        $approvedcontexts = new approved_contextlist($USER, 'core_course', [context_course::instance($courses[1]->id)->id]);
        provider::delete_customfields_data($approvedcontexts, 'core_course', 'course');
        $this->assertEmpty($DB->get_records('customfield_data', ['instanceid' => $courses[1]->id]));
        $this->assertNotEmpty($DB->get_records('customfield_data', ['instanceid' => $courses[2]->id]));
    }

    /**
     * Test for provider::delete_customfields_configuration()
     */
    public function test_delete_customfields_configuration() {
        global $USER, $DB;
        $this->resetAfterTest();
        [
            'cffields' => $cffields,
            'cfcats' => $cfcats,
            'courses' => $courses,
        ] = $this->generate_test_data();

        // Remember the list of fields in the category 2 before we delete it.
        $catid1 = $cfcats[1]->get('id');
        $catid2 = $cfcats[2]->get('id');
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
        $this->resetAfterTest();
        [
            'cffields' => $cffields,
            'cfcats' => $cfcats,
            'courses' => $courses,
        ] = $this->generate_test_data();

        // Remember the list of fields in the category 2 before we delete it.
        $catid1 = $cfcats[1]->get('id');
        $catid2 = $cfcats[2]->get('id');
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
        $this->resetAfterTest();
        [
            'cffields' => $cffields,
            'cfcats' => $cfcats,
            'courses' => $courses,
        ] = $this->generate_test_data();

        provider::delete_customfields_data_for_context('core_course', 'course',
            context_course::instance($courses[1]->id));
        $fids2 = $DB->get_fieldset_select('customfield_field', 'id', '1=1', []);
        list($fsql, $fparams) = $DB->get_in_or_equal($fids2, SQL_PARAMS_NAMED);
        $fparams['course1'] = $courses[1]->id;
        $fparams['course2'] = $courses[2]->id;
        $this->assertEmpty($DB->get_records_select('customfield_data', 'instanceid = :course1 AND fieldid ' . $fsql, $fparams));
        $this->assertNotEmpty($DB->get_records_select('customfield_data', 'instanceid = :course2 AND fieldid ' . $fsql, $fparams));
    }
}
