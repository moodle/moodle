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

namespace qbank_customfields;

/**
 * Class qbank_customfields_question_handler_testcase
 *
 * @package     qbank_customfields
 * @copyright   2021 Catalyst IT Australia Pty Ltd
 * @author      Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class question_handler_test extends \advanced_testcase {

    /**
     * Question setup helper method.
     *
     * @return int The question id.
     * @throws coding_exception
     */
    protected function setup_question(): int {
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $context = \context_module::instance($qbank->cmid);
        $questioncategory = $questiongenerator->create_question_category(['contextid' => $context->id]);
        $questiondata = ['category' => $questioncategory->id, 'idnumber' => 'q1'];
        $question = $questiongenerator->create_question('shortanswer', null, $questiondata);

        return $question->id;

    }

    /**
     * Test custom field data.
     */
    public function test_get_field_data(): void {
        $this->resetAfterTest();
        $fieldgenerator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        $instanceid = $this->setup_question();
        $fieldvalue = 'test field text';

        $categorydata = new \stdClass();
        $categorydata->component = 'qbank_customfields';
        $categorydata->area = 'question';
        $categorydata->name = 'test category';

        $customfieldcatid = $fieldgenerator->create_category($categorydata)->get('id');
        $field = $fieldgenerator->create_field(['categoryid' => $customfieldcatid, 'type' => 'text', 'shortname' => 'f1']);
        $fieldgenerator->add_instance_data($field, $instanceid, $fieldvalue);

        // Get the field data.
        $customfieldhandler = customfield\question_handler::create();
        $fieldinstancedata = $customfieldhandler->get_field_data($field, $instanceid);

        $this->assertEquals($categorydata->name, $fieldinstancedata->get_field()->get_category()->get('name'));
        $this->assertEquals($fieldvalue, $fieldinstancedata->get_value());
    }

    /**
     * Test getting custom field data for table display.
     */
    public function test_display_custom_field_table(): void {
        $this->resetAfterTest();
        $fieldgenerator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        $instanceid = $this->setup_question();
        $fieldvalue = 'test field text';

        $categorydata = new \stdClass();
        $categorydata->component = 'qbank_customfields';
        $categorydata->area = 'question';
        $categorydata->name = 'test category';

        $customfieldcatid = $fieldgenerator->create_category($categorydata)->get('id');
        $field = $fieldgenerator->create_field(['categoryid' => $customfieldcatid, 'type' => 'text', 'shortname' => 'f1']);
        $fieldgenerator->add_instance_data($field, $instanceid, $fieldvalue);

        // Get the field data.
        $customfieldhandler = customfield\question_handler::create();
        $fieldinstancedata = $customfieldhandler->get_field_data($field, $instanceid);
        $output = $customfieldhandler->display_custom_field_table($fieldinstancedata);

        $this->assertStringContainsString($fieldvalue, $output);
    }

    /**
     * Test getting categories and field data for a specific instance.
     */
    public function test_get_categories_fields_data(): void {
        $this->resetAfterTest();
        $fieldgenerator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        $instanceid = $this->setup_question();
        $field1value = 'first field text';
        $field2value = 'second field text';
        $field3value = 'third field text';

        $categorydata = new \stdClass();
        $categorydata->component = 'qbank_customfields';
        $categorydata->area = 'question';
        $categorydata->name = 'test category';

        $customfieldcat1id = $fieldgenerator->create_category($categorydata)->get('id');
        $categorydata->name = 'test category two';
        $customfieldcat2id = $fieldgenerator->create_category($categorydata)->get('id');

        $field1 = $fieldgenerator->create_field(['categoryid' => $customfieldcat1id, 'type' => 'text', 'shortname' => 'f1']);
        $fieldgenerator->add_instance_data($field1, $instanceid, $field1value);
        $field2 = $fieldgenerator->create_field(['categoryid' => $customfieldcat1id, 'type' => 'text', 'shortname' => 'f2']);
        $fieldgenerator->add_instance_data($field2, $instanceid, $field2value);

        $field3 = $fieldgenerator->create_field(['categoryid' => $customfieldcat2id, 'type' => 'text', 'shortname' => 'f3']);
        $fieldgenerator->add_instance_data($field3, $instanceid, $field3value);

        // Get the field data.
        $customfieldhandler = customfield\question_handler::create();
        $outputdata = $customfieldhandler->get_categories_fields_data($instanceid);

        $this->assertEquals($field1value, $outputdata['test category'][0]['value']);
        $this->assertEquals($field2value, $outputdata['test category'][1]['value']);
        $this->assertEquals($field3value, $outputdata['test category two'][0]['value']);

        // While we're here, lets test the rendering of this data.
        $outputhtml = $customfieldhandler->display_custom_categories_fields($outputdata);

        $this->assertStringContainsString($field1value, $outputhtml);
        $this->assertStringContainsString($field2value, $outputhtml);
        $this->assertStringContainsString($field3value, $outputhtml);
    }
}
