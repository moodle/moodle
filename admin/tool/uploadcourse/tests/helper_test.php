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
 * File containing tests for the helper.
 *
 * @package    tool_uploadcourse
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Helper test case.
 *
 * @package    tool_uploadcourse
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_uploadcourse_helper_testcase extends advanced_testcase {

    public function test_generate_shortname() {
        $data = (object) array('fullname' => 'Ah bh Ch 01 02 03', 'idnumber' => 'ID123');

        $this->assertSame($data->fullname, tool_uploadcourse_helper::generate_shortname($data, '%f'));
        $this->assertSame($data->idnumber, tool_uploadcourse_helper::generate_shortname($data, '%i'));
        $this->assertSame('Ah Bh Ch', tool_uploadcourse_helper::generate_shortname($data, '%~8f'));
        $this->assertSame('AH BH CH', tool_uploadcourse_helper::generate_shortname($data, '%+8f'));
        $this->assertSame('id123', tool_uploadcourse_helper::generate_shortname($data, '%-i'));
        $this->assertSame('[Ah bh Ch] = ID123', tool_uploadcourse_helper::generate_shortname($data, '[%8f] = %i'));
        $this->assertSame('0', tool_uploadcourse_helper::generate_shortname($data, '0'));
        $this->assertSame('%unknown', tool_uploadcourse_helper::generate_shortname($data, '%unknown'));

        $this->assertNull(tool_uploadcourse_helper::generate_shortname($data, ''));
        $this->assertNull(tool_uploadcourse_helper::generate_shortname(array(), '%f'));
    }

    public function test_get_course_formats() {
        $result = tool_uploadcourse_helper::get_course_formats();
        $this->assertSame(array_keys(core_component::get_plugin_list('format')), $result);
        // Should be similar as first result, as cached.
        $this->assertSame($result, tool_uploadcourse_helper::get_course_formats());
    }

    public function test_get_enrolment_data() {
        $this->resetAfterTest(true);
        $data = array(
            'enrolment_1' => 'unknown',
            'enrolment_1_foo' => '1',
            'enrolment_1_bar' => '2',
            'enrolment_2' => 'self',
            'enrolment_2_delete' => '1',
            'enrolment_2_foo' => 'a',
            'enrolment_2_bar' => '1',
            'enrolment_3' => 'manual',
            'enrolment_3_disable' => '2',
            'enrolment_3_foo' => 'b',
            'enrolment_3_bar' => '2',
            'enrolment_4' => 'database',
            'enrolment_4_foo' => 'x',
            'enrolment_4_bar' => '3',
            'enrolment_5_test3' => 'test3',
            'enrolment_5_test2' => 'test2',
            'enrolment_5_test1' => 'test1',
            'enrolment_5' => 'flatfile',
        );
        $expected = array(
            'self' => array(
                'delete' => '1',
                'foo' => 'a',
                'bar' => '1',
            ),
            'manual' => array(
                'disable' => '2',
                'foo' => 'b',
                'bar' => '2',
            ),
            'database' => array(
                'foo' => 'x',
                'bar' => '3',
            ),
            'flatfile' => array(
                'test3' => 'test3',
                'test2' => 'test2',
                'test1' => 'test1',
            )
        );
        $this->assertSame(tool_uploadcourse_helper::get_enrolment_data($data), $expected);
    }

    public function test_get_enrolment_plugins() {
        $this->resetAfterTest(true);
        $actual = tool_uploadcourse_helper::get_enrolment_plugins();
        $this->assertSame(array_keys(enrol_get_plugins(false)), array_keys($actual));
        // This should be identical as cached.
        $secondactual = tool_uploadcourse_helper::get_enrolment_plugins();
        $this->assertEquals($actual, $secondactual);
    }

    public function test_get_restore_content_dir() {
        global $CFG;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course((object) array('shortname' => 'Yay'));

        // Creating backup file.
        $bc = new backup_controller(backup::TYPE_1COURSE, $c1->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, 2);
        $bc->execute_plan();
        $result = $bc->get_results();
        $this->assertTrue(isset($result['backup_destination']));
        $c1backupfile = $result['backup_destination']->copy_content_to_temp();
        $bc->destroy();

        // Creating backup file.
        $bc = new backup_controller(backup::TYPE_1COURSE, $c2->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, 2);
        $bc->execute_plan();
        $result = $bc->get_results();
        $this->assertTrue(isset($result['backup_destination']));
        $c2backupfile = $result['backup_destination']->copy_content_to_temp();
        $bc->destroy();

        $oldcfg = isset($CFG->keeptempdirectoriesonbackup) ? $CFG->keeptempdirectoriesonbackup : false;
        $CFG->keeptempdirectoriesonbackup = true;

        // Checking restore dir.
        $dir = tool_uploadcourse_helper::get_restore_content_dir($c1backupfile, null);
        $bcinfo = backup_general_helper::get_backup_information($dir);
        $this->assertEquals($bcinfo->original_course_id, $c1->id);
        $this->assertEquals($bcinfo->original_course_fullname, $c1->fullname);

        // Do it again, it should be the same directory.
        $dir2 = tool_uploadcourse_helper::get_restore_content_dir($c1backupfile, null);
        $this->assertEquals($dir, $dir2);

        // Get the second course.
        $dir = tool_uploadcourse_helper::get_restore_content_dir($c2backupfile, null);
        $bcinfo = backup_general_helper::get_backup_information($dir);
        $this->assertEquals($bcinfo->original_course_id, $c2->id);
        $this->assertEquals($bcinfo->original_course_fullname, $c2->fullname);

        // Checking with a shortname.
        $dir = tool_uploadcourse_helper::get_restore_content_dir(null, $c1->shortname);
        $bcinfo = backup_general_helper::get_backup_information($dir);
        $this->assertEquals($bcinfo->original_course_id, $c1->id);
        $this->assertEquals($bcinfo->original_course_fullname, $c1->fullname);

        // Do it again, it should be the same directory.
        $dir2 = tool_uploadcourse_helper::get_restore_content_dir(null, $c1->shortname);
        $this->assertEquals($dir, $dir2);

        // Get the second course.
        $dir = tool_uploadcourse_helper::get_restore_content_dir(null, $c2->shortname);
        $bcinfo = backup_general_helper::get_backup_information($dir);
        $this->assertEquals($bcinfo->original_course_id, $c2->id);
        $this->assertEquals($bcinfo->original_course_fullname, $c2->fullname);

        // Get a course that does not exist.
        $errors = array();
        $dir = tool_uploadcourse_helper::get_restore_content_dir(null, 'DoesNotExist', $errors);
        $this->assertFalse($dir);
        $this->assertArrayHasKey('coursetorestorefromdoesnotexist', $errors);

        // Trying again without caching. $CFG->keeptempdirectoriesonbackup is required for caching.
        $CFG->keeptempdirectoriesonbackup = false;

        // Checking restore dir.
        $dir = tool_uploadcourse_helper::get_restore_content_dir($c1backupfile, null);
        $dir2 = tool_uploadcourse_helper::get_restore_content_dir($c1backupfile, null);
        $this->assertNotEquals($dir, $dir2);

        // Checking with a shortname.
        $dir = tool_uploadcourse_helper::get_restore_content_dir(null, $c1->shortname);
        $dir2 = tool_uploadcourse_helper::get_restore_content_dir(null, $c1->shortname);
        $this->assertNotEquals($dir, $dir2);

        // Get a course that does not exist.
        $errors = array();
        $dir = tool_uploadcourse_helper::get_restore_content_dir(null, 'DoesNotExist', $errors);
        $this->assertFalse($dir);
        $this->assertArrayHasKey('coursetorestorefromdoesnotexist', $errors);
        $dir2 = tool_uploadcourse_helper::get_restore_content_dir(null, 'DoesNotExist', $errors);
        $this->assertEquals($dir, $dir2);

        $CFG->keeptempdirectoriesonbackup = $oldcfg;
    }

    public function test_get_role_ids() {
        $this->getDataGenerator();
        // Mimic function result.
        $expected = array();
        $roles = get_all_roles();
        foreach ($roles as $role) {
            $expected[$role->shortname] = $role->id;
        }

        $actual = tool_uploadcourse_helper::get_role_ids();
        $this->assertSame($actual, $expected);

        // Check cache.
        $this->assertSame($actual, tool_uploadcourse_helper::get_role_ids());
    }

    public function test_get_role_names() {
        $this->resetAfterTest(true);

        create_role('Villain', 'villain', 'The bad guys');
        $data = array(
            'role_student' => 'Padawan',
            'role_teacher' => 'Guardian',
            'role_editingteacher' => 'Knight',
            'role_manager' => 'Master',
            'role_villain' => 'Jabba the Hutt',
            'role_android' => 'R2D2',
        );

        // Get the role IDs, but need to force the cache reset as a new role is defined.
        $roleids = tool_uploadcourse_helper::get_role_ids(true);

        $expected = array(
            'role_' . $roleids['student'] => 'Padawan',
            'role_' . $roleids['teacher'] => 'Guardian',
            'role_' . $roleids['editingteacher'] => 'Knight',
            'role_' . $roleids['manager'] => 'Master',
            'role_' . $roleids['villain'] => 'Jabba the Hutt',
        );

        $errors = array();
        $actual = tool_uploadcourse_helper::get_role_names($data, $errors);
        $this->assertSame($actual, $expected);
        $this->assertArrayHasKey('invalidroles', $errors);
    }

    /**
     * Test custom field data processing
     */
    public function test_get_custom_course_field_data() {
        global $DB;

        $this->resetAfterTest();

        // Create all the fields!
        $category = $this->get_customfield_generator()->create_category();

        $checkboxfield = $this->create_custom_field($category, 'checkbox', 'mycheckbox');
        $datefield = $this->create_custom_field($category, 'date', 'mydate');
        $selectfield = $this->create_custom_field($category, 'select', 'myselect', ['options' => "Red\nGreen\nBlue"]);
        $textfield = $this->create_custom_field($category, 'text', 'mytext', ['locked' => 1]);
        $textareafield = $this->create_custom_field($category, 'textarea', 'mytextarea');

        $fields = tool_uploadcourse_helper::get_custom_course_fields();
        $this->assertCount(5, $fields);

        $this->assertArrayHasKey($checkboxfield->get('shortname'), $fields);
        $this->assertInstanceOf(customfield_checkbox\field_controller::class, $fields[$checkboxfield->get('shortname')]);

        $this->assertArrayHasKey($datefield->get('shortname'), $fields);
        $this->assertInstanceOf(customfield_date\field_controller::class, $fields[$datefield->get('shortname')]);

        $this->assertArrayHasKey($selectfield->get('shortname'), $fields);
        $this->assertInstanceOf(customfield_select\field_controller::class, $fields[$selectfield->get('shortname')]);

        $this->assertArrayHasKey($textfield->get('shortname'), $fields);
        $this->assertInstanceOf(customfield_text\field_controller::class, $fields[$textfield->get('shortname')]);

        $this->assertArrayHasKey($textareafield->get('shortname'), $fields);
        $this->assertInstanceOf(customfield_textarea\field_controller::class, $fields[$textareafield->get('shortname')]);

        $data = [
            'customfield_mycheckbox' => '1',
            'customfield_mydate' => '2019-10-01',
            'customfield_myselect' => 'Green',
            'customfield_mytext' => 'Hello',
            'customfield_myunknownfield' => 'Goodbye',
        ];

        $expected = [
            'customfield_mycheckbox' => '1',
            'customfield_mydate' => strtotime('2019-10-01'),
            'customfield_myselect' => 2,
            'customfield_mytext' => 'Hello',
        ];

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'manager');
        $this->setUser($user);

        $context = context_course::instance($course->id);

        $this->assertEquals($expected, tool_uploadcourse_helper::get_custom_course_field_data($data, [], $context));

        // Now add our custom textarea field (separately because the value of it's 'itemid' element is unknown).
        $data['customfield_mytextarea'] = 'Something';
        $fields = tool_uploadcourse_helper::get_custom_course_field_data($data, [], $context);
        $this->assertArrayHasKey('customfield_mytextarea_editor', $fields);
        $this->assertArrayHasKey('text', $fields['customfield_mytextarea_editor']);
        $this->assertEquals('Something', $fields['customfield_mytextarea_editor']['text']);

        // Now prohibit the capability to change locked fields for the manager role.
        $managerrole = $DB->get_record('role', ['shortname' => 'manager']);
        role_change_permission($managerrole->id, $context, 'moodle/course:changelockedcustomfields', CAP_PROHIBIT);

        // The locked 'mytext' custom field should not be returned.
        $fields = tool_uploadcourse_helper::get_custom_course_field_data($data, [], $context);
        $this->assertCount(4, $fields);
        $this->assertArrayNotHasKey('customfield_mytext', $fields);
    }

    public function test_increment_idnumber() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course(array('idnumber' => 'C1'));
        $c2 = $this->getDataGenerator()->create_course(array('idnumber' => 'C2'));
        $c3 = $this->getDataGenerator()->create_course(array('idnumber' => 'Yo'));

        $this->assertEquals('C3', tool_uploadcourse_helper::increment_idnumber('C1'));
        $this->assertEquals('Yo_2', tool_uploadcourse_helper::increment_idnumber('Yo'));
        $this->assertEquals('DoesNotExist', tool_uploadcourse_helper::increment_idnumber('DoesNotExist'));
    }

    public function test_increment_shortname() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course(array('shortname' => 'C1'));
        $c2 = $this->getDataGenerator()->create_course(array('shortname' => 'C2'));
        $c3 = $this->getDataGenerator()->create_course(array('shortname' => 'Yo'));

        // FYI: increment_shortname assumes that the course exists, and so increment the shortname immediately.
        $this->assertEquals('C3', tool_uploadcourse_helper::increment_shortname('C1'));
        $this->assertEquals('Yo_2', tool_uploadcourse_helper::increment_shortname('Yo'));
        $this->assertEquals('DoesNotExist_2', tool_uploadcourse_helper::increment_shortname('DoesNotExist'));
    }

    public function test_resolve_category() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_category(array('name' => 'First level'));
        $c2 = $this->getDataGenerator()->create_category(array('name' => 'Second level', 'parent' => $c1->id));
        $c3 = $this->getDataGenerator()->create_category(array('idnumber' => 'C3'));

        $data = array(
            'category' => $c1->id,
            'category_path' => $c1->name . ' / ' . $c2->name,
            'category_idnumber' => $c3->idnumber,
        );

        $this->assertEquals($c1->id, tool_uploadcourse_helper::resolve_category($data));
        unset($data['category']);
        $this->assertEquals($c3->id, tool_uploadcourse_helper::resolve_category($data));
        unset($data['category_idnumber']);
        $this->assertEquals($c2->id, tool_uploadcourse_helper::resolve_category($data));

        // Adding unexisting data.
        $errors = array();
        $data['category_idnumber'] = 1234;
        $this->assertEquals($c2->id, tool_uploadcourse_helper::resolve_category($data, $errors));
        $this->assertArrayHasKey('couldnotresolvecatgorybyidnumber', $errors);
        $errors = array();
        $data['category'] = 1234;
        $this->assertEquals($c2->id, tool_uploadcourse_helper::resolve_category($data, $errors));
        $this->assertArrayHasKey('couldnotresolvecatgorybyid', $errors);
        $errors = array();
        $data['category_path'] = 'Not exist';
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category($data, $errors));
        $this->assertArrayHasKey('couldnotresolvecatgorybypath', $errors);
    }

    public function test_resolve_category_by_idnumber() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_category(array('idnumber' => 'C1'));
        $c2 = $this->getDataGenerator()->create_category(array('idnumber' => 'C2'));

        // Doubled for cache check.
        $this->assertEquals($c1->id, tool_uploadcourse_helper::resolve_category_by_idnumber('C1'));
        $this->assertEquals($c1->id, tool_uploadcourse_helper::resolve_category_by_idnumber('C1'));
        $this->assertEquals($c2->id, tool_uploadcourse_helper::resolve_category_by_idnumber('C2'));
        $this->assertEquals($c2->id, tool_uploadcourse_helper::resolve_category_by_idnumber('C2'));
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category_by_idnumber('DoesNotExist'));
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category_by_idnumber('DoesNotExist'));
    }

    public function test_resolve_category_by_path() {
        $this->resetAfterTest(true);

        $cat1 = $this->getDataGenerator()->create_category(array('name' => 'Cat 1'));
        $cat1_1 = $this->getDataGenerator()->create_category(array('name' => 'Cat 1.1', 'parent' => $cat1->id));
        $cat1_1_1 = $this->getDataGenerator()->create_category(array('name' => 'Cat 1.1.1', 'parent' => $cat1_1->id));
        $cat1_1_2 = $this->getDataGenerator()->create_category(array('name' => 'Cat 1.1.2', 'parent' => $cat1_1->id));
        $cat1_2 = $this->getDataGenerator()->create_category(array('name' => 'Cat 1.2', 'parent' => $cat1->id));

        $cat2 = $this->getDataGenerator()->create_category(array('name' => 'Cat 2'));
        $cat2_1 = $this->getDataGenerator()->create_category(array('name' => 'Cat 2.1', 'parent' => $cat2->id, 'visible' => false));
        $cat2_1_1 = $this->getDataGenerator()->create_category(array('name' => 'Cat 2.1.1', 'parent' => $cat2_1->id));
        $cat2_1_2 = $this->getDataGenerator()->create_category(array('name' => 'Cat 2.1.2', 'parent' => $cat2_1->id));
        $cat2_2 = $this->getDataGenerator()->create_category(array('name' => 'Cat 2.2', 'parent' => $cat2->id));

        $cat3 = $this->getDataGenerator()->create_category(array('name' => 'Cat 3'));
        $cat3_1 = $this->getDataGenerator()->create_category(array('name' => 'Cat 3.1 Doubled', 'parent' => $cat3->id));
        $cat3_1b = $this->getDataGenerator()->create_category(array('name' => 'Cat 3.1 Doubled', 'parent' => $cat3->id));
        $cat3_1_1 = $this->getDataGenerator()->create_category(array('name' => 'Cat 3.1.1', 'parent' => $cat3_1->id));
        $cat3_fakedouble = $this->getDataGenerator()->create_category(array('name' => 'Cat 3.1.1', 'parent' => $cat3->id));

        // Existing categories. Doubled for cache testing.
        $path = array('Cat 1');
        $this->assertEquals($cat1->id, tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEquals($cat1->id, tool_uploadcourse_helper::resolve_category_by_path($path));

        $path = array('Cat 1', 'Cat 1.1', 'Cat 1.1.2');
        $this->assertEquals($cat1_1_2->id, tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEquals($cat1_1_2->id, tool_uploadcourse_helper::resolve_category_by_path($path));

        $path = array('Cat 1', 'Cat 1.2');
        $this->assertEquals($cat1_2->id, tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEquals($cat1_2->id, tool_uploadcourse_helper::resolve_category_by_path($path));

        $path = array('Cat 2');
        $this->assertEquals($cat2->id, tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEquals($cat2->id, tool_uploadcourse_helper::resolve_category_by_path($path));

        // Hidden category.
        $path = array('Cat 2', 'Cat 2.1');
        $this->assertEquals($cat2_1->id, tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEquals($cat2_1->id, tool_uploadcourse_helper::resolve_category_by_path($path));

        // Hidden parent.
        $path = array('Cat 2', 'Cat 2.1', 'Cat 2.1.2');
        $this->assertEquals($cat2_1_2->id, tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEquals($cat2_1_2->id, tool_uploadcourse_helper::resolve_category_by_path($path));

        // Does not exist.
        $path = array('No cat 3', 'Cat 1.2');
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category_by_path($path));

        $path = array('Cat 2', 'Cat 2.x');
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category_by_path($path));

        // Name conflict.
        $path = array('Cat 3', 'Cat 3.1 Doubled');
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category_by_path($path));

        $path = array('Cat 3', 'Cat 3.1 Doubled', 'Cat 3.1.1');
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEmpty(tool_uploadcourse_helper::resolve_category_by_path($path));

        $path = array('Cat 3', 'Cat 3.1.1');
        $this->assertEquals($cat3_fakedouble->id, tool_uploadcourse_helper::resolve_category_by_path($path));
        $this->assertEquals($cat3_fakedouble->id, tool_uploadcourse_helper::resolve_category_by_path($path));
    }

    /**
     * Get custom field plugin generator
     *
     * @return core_customfield_generator
     */
    protected function get_customfield_generator() : core_customfield_generator {
        return $this->getDataGenerator()->get_plugin_generator('core_customfield');
    }

    /**
     * Helper method to create custom course field
     *
     * @param \core_customfield\category_controller $category
     * @param string $type
     * @param string $shortname
     * @param array $configdata
     * @return \core_customfield\field_controller
     */
    protected function create_custom_field(\core_customfield\category_controller $category, string $type, string $shortname,
            array $configdata = []) : \core_customfield\field_controller {

        return $this->get_customfield_generator()->create_field([
            'categoryid' => $category->get('id'),
            'type' => $type,
            'shortname' => $shortname,
            'configdata' => $configdata,
        ]);
    }
}