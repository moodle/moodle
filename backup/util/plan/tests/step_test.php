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
 * @package    core_backup
 * @category   phpunit
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/fixtures/plan_fixtures.php');


/*
 * step tests (all)
 */
class backup_step_testcase extends advanced_testcase {

    protected $moduleid;  // course_modules id used for testing
    protected $sectionid; // course_sections id used for testing
    protected $courseid;  // course id used for testing
    protected $userid;      // user record used for testing

    protected function setUp() {
        global $DB, $CFG;
        parent::setUp();

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', array('course'=>$course->id), array('section'=>3));
        $coursemodule = $DB->get_record('course_modules', array('id'=>$page->cmid));

        $this->moduleid  = $coursemodule->id;
        $this->sectionid = $DB->get_field("course_sections", 'id', array("section"=>$coursemodule->section, "course"=>$course->id));
        $this->courseid  = $coursemodule->course;
        $this->userid = 2; // admin

        // Disable all loggers
        $CFG->backup_error_log_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $CFG->backup_database_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
    }

    /**
     * test base_step class
     */
    function test_base_step() {

        $bp = new mock_base_plan('planname'); // We need one plan
        $bt = new mock_base_task('taskname', $bp); // We need one task
        // Instantiate
        $bs = new mock_base_step('stepname', $bt);
        $this->assertTrue($bs instanceof base_step);
        $this->assertEquals($bs->get_name(), 'stepname');
    }

    /**
     * test backup_step class
     */
    function test_backup_step() {

        // We need one (non interactive) controller for instatiating plan
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        // We need one plan
        $bp = new backup_plan($bc);
        // We need one task
        $bt = new mock_backup_task('taskname', $bp);
        // Instantiate step
        $bs = new mock_backup_step('stepname', $bt);
        $this->assertTrue($bs instanceof backup_step);
        $this->assertEquals($bs->get_name(), 'stepname');

        $bc->destroy();
    }

    /**
     * test restore_step class, decrypt method
     */
    public function test_restore_step_decrypt() {

        $this->resetAfterTest(true);

        if (!function_exists('openssl_encrypt')) {
            $this->markTestSkipped('OpenSSL extension is not loaded.');

        } else if (!function_exists('hash_hmac')) {
            $this->markTestSkipped('Hash extension is not loaded.');

        } else if (!in_array(backup::CIPHER, openssl_get_cipher_methods())) {
            $this->markTestSkipped('Expected cipher not available: ' . backup::CIPHER);
        }

        $bt = new mock_restore_task_basepath('taskname');
        $bs = new mock_restore_structure_step('steptest', null, $bt);
        $this->assertTrue(method_exists($bs, 'decrypt'));

        // Let's prepare a string for being decrypted.
        $secret = 'This is a secret message that nobody else will be able to read but me 💩  ';
        $key = hash('md5', 'Moodle rocks and this is not secure key, who cares, it is a test');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(backup::CIPHER));
        $message = $iv . openssl_encrypt($secret, backup::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $message, $key, true);
        $crypt = base64_encode($hmac . $message);

        // Running it without a key configured, returns null.
        $this->assertNull($bs->decrypt($crypt));

        // Store the key into config.
        set_config('backup_encryptkey', base64_encode($key), 'backup');

        // Verify decrypt works and returns original.
        $this->assertSame($secret, $bs->decrypt($crypt));

        // Finally, test the integrity failure detection is working.
        // (this can be caused by changed hmac, key or message, in
        // this case we are just forcing it via changed hmac).
        $hmac = md5($message);
        $crypt = base64_encode($hmac . $message);
        $this->assertNull($bs->decrypt($crypt));
    }

    /**
     * test backup_structure_step class
     */
    function test_backup_structure_step() {
        global $CFG;

        $file = $CFG->tempdir . '/test/test_backup_structure_step.txt';
        // Remove the test dir and any content
        @remove_dir(dirname($file));
        // Recreate test dir
        if (!check_dir_exists(dirname($file), true, true)) {
            throw new moodle_exception('error_creating_temp_dir', 'error', dirname($file));
        }

        // We need one (non interactive) controller for instatiating plan
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        // We need one plan
        $bp = new backup_plan($bc);
        // We need one task with mocked basepath
        $bt = new mock_backup_task_basepath('taskname');
        $bp->add_task($bt);
        // Instantiate backup_structure_step (and add it to task)
        $bs = new mock_backup_structure_step('steptest', basename($file), $bt);
        // Execute backup_structure_step
        $bs->execute();

        // Test file has been created
        $this->assertTrue(file_exists($file));

        // Some simple tests with contents
        $contents = file_get_contents($file);
        $this->assertTrue(strpos($contents, '<?xml version="1.0"') !== false);
        $this->assertTrue(strpos($contents, '<test id="1">') !== false);
        $this->assertTrue(strpos($contents, '<field1>value1</field1>') !== false);
        $this->assertTrue(strpos($contents, '<field2>value2</field2>') !== false);
        $this->assertTrue(strpos($contents, '</test>') !== false);

        $bc->destroy();

        unlink($file); // delete file

        // Remove the test dir and any content
        @remove_dir(dirname($file));
    }


    /**
     * Verify the add_plugin_structure() backup method behavior and created structures.
     */
    public function test_backup_structure_step_add_plugin_structure() {
        // Create mocked task, step and element.
        $bt = new mock_backup_task_basepath('taskname');
        $bs = new mock_backup_structure_step('steptest', null, $bt);
        $el = new backup_nested_element('question', array('id'), array('one', 'two', 'qtype'));
        // Wrong plugintype.
        try {
            $bs->add_plugin_structure('fakeplugin', $el, true);
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_step_exception);
            $this->assertEquals('incorrect_plugin_type', $e->errorcode);
        }
        // Correct plugintype qtype call (@ 'question' level).
        $bs->add_plugin_structure('qtype', $el, false);
        $ch = $el->get_children();
        $this->assertEquals(1, count($ch));
        $og = reset($ch);
        $this->assertTrue($og instanceof backup_optigroup);
        $ch = $og->get_children();
        $this->assertTrue(array_key_exists('optigroup_qtype_calculatedsimple_question', $ch));
        $this->assertTrue($ch['optigroup_qtype_calculatedsimple_question'] instanceof backup_plugin_element);
    }

    /**
     * Verify the add_subplugin_structure() backup method behavior and created structures.
     */
    public function test_backup_structure_step_add_subplugin_structure() {
        // Create mocked task, step and element.
        $bt = new mock_backup_task_basepath('taskname');
        $bs = new mock_backup_structure_step('steptest', null, $bt);
        $el = new backup_nested_element('workshop', array('id'), array('one', 'two', 'qtype'));
        // Wrong plugin type.
        try {
            $bs->add_subplugin_structure('fakesubplugin', $el, true, 'fakeplugintype', 'fakepluginname');
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_step_exception);
            $this->assertEquals('incorrect_plugin_type', $e->errorcode);
        }
        // Wrong plugin type.
        try {
            $bs->add_subplugin_structure('fakesubplugin', $el, true, 'mod', 'fakepluginname');
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_step_exception);
            $this->assertEquals('incorrect_plugin_name', $e->errorcode);
        }
        // Wrong plugin not having subplugins.
        try {
            $bs->add_subplugin_structure('fakesubplugin', $el, true, 'mod', 'page');
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_step_exception);
            $this->assertEquals('plugin_missing_subplugins_php_file', $e->errorcode);
        }
        // Wrong BC (defaulting to mod and modulename) use not having subplugins.
        try {
            $bt->set_modulename('page');
            $bs->add_subplugin_structure('fakesubplugin', $el, true);
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_step_exception);
            $this->assertEquals('plugin_missing_subplugins_php_file', $e->errorcode);
        }
        // Wrong subplugin type.
        try {
            $bs->add_subplugin_structure('fakesubplugin', $el, true, 'mod', 'workshop');
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_step_exception);
            $this->assertEquals('incorrect_subplugin_type', $e->errorcode);
        }
        // Wrong BC subplugin type.
        try {
            $bt->set_modulename('workshop');
            $bs->add_subplugin_structure('fakesubplugin', $el, true);
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_step_exception);
            $this->assertEquals('incorrect_subplugin_type', $e->errorcode);
        }
        // Correct call to workshopform subplugin (@ 'workshop' level).
        $bs->add_subplugin_structure('workshopform', $el, true, 'mod', 'workshop');
        $ch = $el->get_children();
        $this->assertEquals(1, count($ch));
        $og = reset($ch);
        $this->assertTrue($og instanceof backup_optigroup);
        $ch = $og->get_children();
        $this->assertTrue(array_key_exists('optigroup_workshopform_accumulative_workshop', $ch));
        $this->assertTrue($ch['optigroup_workshopform_accumulative_workshop'] instanceof backup_subplugin_element);

        // Correct BC call to workshopform subplugin (@ 'assessment' level).
        $el = new backup_nested_element('assessment', array('id'), array('one', 'two', 'qtype'));
        $bt->set_modulename('workshop');
        $bs->add_subplugin_structure('workshopform', $el, true);
        $ch = $el->get_children();
        $this->assertEquals(1, count($ch));
        $og = reset($ch);
        $this->assertTrue($og instanceof backup_optigroup);
        $ch = $og->get_children();
        $this->assertTrue(array_key_exists('optigroup_workshopform_accumulative_assessment', $ch));
        $this->assertTrue($ch['optigroup_workshopform_accumulative_assessment'] instanceof backup_subplugin_element);

        // TODO: Add some test covering a non-mod subplugin once we have some implemented in core.
    }

    /**
     * Verify the add_plugin_structure() restore method behavior and created structures.
     */
    public function test_restore_structure_step_add_plugin_structure() {
        // Create mocked task, step and element.
        $bt = new mock_restore_task_basepath('taskname');
        $bs = new mock_restore_structure_step('steptest', null, $bt);
        $el = new restore_path_element('question', '/some/path/to/question');
        // Wrong plugintype.
        try {
            $bs->add_plugin_structure('fakeplugin', $el);
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof restore_step_exception);
            $this->assertEquals('incorrect_plugin_type', $e->errorcode);
        }
        // Correct plugintype qtype call (@ 'question' level).
        $bs->add_plugin_structure('qtype', $el);
        $patheles = $bs->get_pathelements();
        // Verify some well-known qtype plugin restore_path_elements have been added.
        $keys = array(
            '/some/path/to/question/plugin_qtype_calculated_question/answers/answer',
            '/some/path/to/question/plugin_qtype_calculated_question/dataset_definitions/dataset_definition',
            '/some/path/to/question/plugin_qtype_calculated_question/calculated_options/calculated_option',
            '/some/path/to/question/plugin_qtype_essay_question/essay',
            '/some/path/to/question/plugin_qtype_random_question',
            '/some/path/to/question/plugin_qtype_truefalse_question/answers/answer');
        foreach ($keys as $key) {
            // Verify the element exists.
            $this->assertArrayHasKey($key, $patheles);
            // Verify the element is a restore_path_element.
            $this->assertTrue($patheles[$key] instanceof restore_path_element);
            // Check it has a processing object.
            $po = $patheles[$key]->get_processing_object();
            $this->assertTrue($po instanceof restore_plugin);
        }
    }

    /**
     * Verify the add_subplugin_structure() restore method behavior and created structures.
     */
    public function test_restore_structure_step_add_subplugin_structure() {
        // Create mocked task, step and element.
        $bt = new mock_restore_task_basepath('taskname');
        $bs = new mock_restore_structure_step('steptest', null, $bt);
        $el = new restore_path_element('workshop', '/path/to/workshop');
        // Wrong plugin type.
        try {
            $bs->add_subplugin_structure('fakesubplugin', $el, 'fakeplugintype', 'fakepluginname');
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof restore_step_exception);
            $this->assertEquals('incorrect_plugin_type', $e->errorcode);
        }
        // Wrong plugin type.
        try {
            $bs->add_subplugin_structure('fakesubplugin', $el, 'mod', 'fakepluginname');
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof restore_step_exception);
            $this->assertEquals('incorrect_plugin_name', $e->errorcode);
        }
        // Wrong plugin not having subplugins.
        try {
            $bs->add_subplugin_structure('fakesubplugin', $el, 'mod', 'page');
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof restore_step_exception);
            $this->assertEquals('plugin_missing_subplugins_php_file', $e->errorcode);
        }
        // Wrong BC (defaulting to mod and modulename) use not having subplugins.
        try {
            $bt->set_modulename('page');
            $bs->add_subplugin_structure('fakesubplugin', $el);
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof restore_step_exception);
            $this->assertEquals('plugin_missing_subplugins_php_file', $e->errorcode);
        }
        // Wrong subplugin type.
        try {
            $bs->add_subplugin_structure('fakesubplugin', $el, 'mod', 'workshop');
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof restore_step_exception);
            $this->assertEquals('incorrect_subplugin_type', $e->errorcode);
        }
        // Wrong BC subplugin type.
        try {
            $bt->set_modulename('workshop');
            $bs->add_subplugin_structure('fakesubplugin', $el);
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof restore_step_exception);
            $this->assertEquals('incorrect_subplugin_type', $e->errorcode);
        }
        // Correct call to workshopform subplugin (@ 'workshop' level).
        $bt = new mock_restore_task_basepath('taskname');
        $bs = new mock_restore_structure_step('steptest', null, $bt);
        $el = new restore_path_element('workshop', '/path/to/workshop');
        $bs->add_subplugin_structure('workshopform', $el, 'mod', 'workshop');
        $patheles = $bs->get_pathelements();
        // Verify some well-known workshopform subplugin restore_path_elements have been added.
        $keys = array(
            '/path/to/workshop/subplugin_workshopform_accumulative_workshop/workshopform_accumulative_dimension',
            '/path/to/workshop/subplugin_workshopform_comments_workshop/workshopform_comments_dimension',
            '/path/to/workshop/subplugin_workshopform_numerrors_workshop/workshopform_numerrors_map',
            '/path/to/workshop/subplugin_workshopform_rubric_workshop/workshopform_rubric_config');
        foreach ($keys as $key) {
            // Verify the element exists.
            $this->assertArrayHasKey($key, $patheles);
            // Verify the element is a restore_path_element.
            $this->assertTrue($patheles[$key] instanceof restore_path_element);
            // Check it has a processing object.
            $po = $patheles[$key]->get_processing_object();
            $this->assertTrue($po instanceof restore_subplugin);
        }

        // Correct BC call to workshopform subplugin (@ 'assessment' level).
        $bt = new mock_restore_task_basepath('taskname');
        $bs = new mock_restore_structure_step('steptest', null, $bt);
        $el = new restore_path_element('assessment', '/a/assessment');
        $bt->set_modulename('workshop');
        $bs->add_subplugin_structure('workshopform', $el);
        $patheles = $bs->get_pathelements();
        // Verify some well-known workshopform subplugin restore_path_elements have been added.
        $keys = array(
            '/a/assessment/subplugin_workshopform_accumulative_assessment/workshopform_accumulative_grade',
            '/a/assessment/subplugin_workshopform_comments_assessment/workshopform_comments_grade',
            '/a/assessment/subplugin_workshopform_numerrors_assessment/workshopform_numerrors_grade',
            '/a/assessment/subplugin_workshopform_rubric_assessment/workshopform_rubric_grade');
        foreach ($keys as $key) {
            // Verify the element exists.
            $this->assertArrayHasKey($key, $patheles);
            // Verify the element is a restore_path_element.
            $this->assertTrue($patheles[$key] instanceof restore_path_element);
            // Check it has a processing object.
            $po = $patheles[$key]->get_processing_object();
            $this->assertTrue($po instanceof restore_subplugin);
        }

        // TODO: Add some test covering a non-mod subplugin once we have some implemented in core.
    }

    /**
     * wrong base_step class tests
     */
    function test_base_step_wrong() {

        // Try to pass one wrong task
        try {
            $bt = new mock_base_step('teststep', new stdclass());
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_step_exception);
            $this->assertEquals($e->errorcode, 'wrong_base_task_specified');
        }
    }

    /**
     * wrong backup_step class tests
     */
    function test_backup_test_wrong() {

        // Try to pass one wrong task
        try {
            $bt = new mock_backup_step('teststep', new stdclass());
            $this->assertTrue(false, 'backup_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_step_exception);
            $this->assertEquals($e->errorcode, 'wrong_backup_task_specified');
        }
    }
}
