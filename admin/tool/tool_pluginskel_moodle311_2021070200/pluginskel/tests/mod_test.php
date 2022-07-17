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
 * File containing tests for generating an activity module.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use tool_pluginskel\local\util\manager;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/setuplib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/pluginskel/vendor/autoload.php');

/**
 * Activity module test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_mod_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'mod_demo',
        'name'      => 'Activity module demo',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'features'  => array(
            'settings' => true,
            'upgrade' => true,
            'uninstall' => true,
            'install' => true,
        ),
        'mod_features' => array(
            'gradebook' => true,
            'file_area' => true,
            'navigation' => true,
            'backup_moodle2' => array(
                'settingslib' => true,
                'backup_elements' => array(
                    array('name' => 'node'),
                ),
                'restore_elements' => array(
                    array('name' => 'node', 'path' => '/path/to/file')
                )
            )
        ),
        'events' => array(
            array(
                'eventname' => 'course_module_instance_list_viewed',
                'extends' => '\core\event\course_module_instance_list_viewed'
            ),
            array(
                'eventname' => 'course_module_viewed',
                'extends' => '\core\event\course_module_viewed'
            ),
        ),
        'observers' => array(
            array(
                'eventname' => '\mod_demo\event\course_module_instance_list_viewed',
                'callback' => '\mod_demo\observer::course_module_instance_list_viewed'
            ),
            array(
                'eventname' => '\mod_demo\event\course_module_viewed',
                'callback' => '\mod_demo\observer::course_module_viewed'
            )
        ),
        'capabilities' => array(
            array(
                'name' => 'addinstance',
                'title' => 'Add new test instance',
                'riskbitmask' => 'RISK_XSS',
                'captype' => 'write',
                'contextlevel' => 'CONTEXT_COURSE',
                'archetypes' => array(
                    array(
                        'role' => 'manager',
                        'permission' => 'CAP_ALLOW'
                    ),
                    array(
                        'role' => 'editingteacher',
                        'permission' => 'CAP_ALLOW'
                    )
                ),
                'clonepermissionsfrom' => 'moodle/course:manageactivities'
            ),
            array(
                'name' => 'view',
                'title' => 'View test',
                'captype' => 'read',
                'contextlevel' => 'CONTEXT_MODULE',
                'archetypes' => array(
                    array(
                        'role' => 'guest',
                        'permission' => 'CAP_ALLOW'
                    ),
                    array(
                        'role' => 'student',
                        'permission' => 'CAP_ALLOW'
                    ),
                    array(
                        'role' => 'teacher',
                        'permission' => 'CAP_ALLOW'
                    ),
                    array(
                        'role' => 'editingteacher',
                        'permission' => 'CAP_ALLOW'
                    ),
                ),
                'clonepermissionsfrom' => 'moodle/course:manageactivities'
            )
        ),

    );

    /** @var string The plugin files path relative the Moodle root. */
    static protected $relpath;

    /** @var string The plugin name, without the frankenstyle prefix. */
    static protected $modname;

    /**
     * Sets the $relpath and the $modname.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        list($type, $modname) = \core_component::normalize_component(self::$recipe['component']);

        $plugintypes = \core_component::get_plugin_types();
        $root = substr($plugintypes[$type], strlen($CFG->dirroot));

        self::$modname = $modname;
        self::$relpath = $root.'/'.$modname;
    }

    /**
     * Tests creating the basic files.
     */
    public function test_mod_files() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('lib.php', $files);
        $this->assertArrayHasKey('mod_form.php', $files);
        $this->assertArrayHasKey('view.php', $files);
        $this->assertArrayHasKey('index.php', $files);
    }

    /**
     * Tests the file lib.php.
     */
    public function test_lib_php() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('lib.php', $files);
        $libfile = $files['lib.php'];

        $description = 'Library of interface functions and constants.';
        $this->assertStringContainsString($description, $libfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        $this->assertStringContainsString($moodleinternal, $libfile);

        $addinstance = 'function demo_add_instance($moduleinstance, $mform = null)';
        $this->assertStringContainsString($addinstance, $libfile);

        $updateinstance = 'function demo_update_instance($moduleinstance, $mform = null)';
        $this->assertStringContainsString($updateinstance, $libfile);

        $deleteinstance = 'function demo_delete_instance($id)';
        $this->assertStringContainsString($deleteinstance, $libfile);
    }

    /**
     * Tests the file mod_form.php.
     */
    public function test_mod_form_php() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('mod_form.php', $files);
        $modformfile = $files['mod_form.php'];

        $description = 'The main '.$recipe['component'].' configuration form.';
        $this->assertStringContainsString($description, $modformfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        $this->assertStringContainsString($moodleinternal, $modformfile);

        $formclass = 'class mod_demo_mod_form extends moodleform_mod';
        $this->assertStringContainsString($formclass, $modformfile);
    }

    /**
     * Tests the file view.php.
     */
    public function test_view_php() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('view.php', $files);
        $viewfile = $files['view.php'];

        $description = 'Prints an instance of mod_demo.';
        $this->assertStringContainsString($description, $viewfile);

        $requireconfig = "require(__DIR__.'/../../config.php')";
        $this->assertStringContainsString($requireconfig, $viewfile);

        $requirelogin = 'require_login($course, true, $cm)';
        $this->assertStringContainsString($requirelogin, $viewfile);

        $seturl = "\$PAGE->set_url('/mod/demo/view.php', array('id' => \$cm->id))";
        $this->assertStringContainsString($seturl, $viewfile);

        $header = 'echo $OUTPUT->header()';
        $this->assertStringContainsString($header, $viewfile);

        $footer = 'echo $OUTPUT->footer()';
        $this->assertStringContainsString($footer, $viewfile);
    }

    /**
     * Tests the file index.php.
     */
    public function test_index_php() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('index.php', $files);
        $indexfile = $files['index.php'];

        $description = 'Display information about all the mod_demo modules in the requested course.';
        $this->assertStringContainsString($description, $indexfile);

        $requireconfig = "require(__DIR__.'/../../config.php')";
        $this->assertStringContainsString($requireconfig, $indexfile);

        $course = "\$DB->get_record('course', array('id' => \$id), '*', MUST_EXIST)";
        $this->assertStringContainsString($course, $indexfile);

        $requirecourselogin = 'require_course_login($course)';
        $this->assertStringContainsString($requirecourselogin, $indexfile);

        $seturl = "\$PAGE->set_url('/mod/demo/index.php', array('id' => \$id))";
        $this->assertStringContainsString($seturl, $indexfile);

        $header = 'echo $OUTPUT->header()';
        $this->assertStringContainsString($header, $indexfile);

        $footer = 'echo $OUTPUT->footer()';
        $this->assertStringContainsString($footer, $indexfile);
    }

    /**
     * Tests creating the 'gradebook' feature.
     */
    public function test_gradebook_feature() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('lib.php', $files);
        $this->assertArrayHasKey('grade.php', $files);
        $this->assertArrayHasKey('mod_form.php', $files);

        $modformfile = $files['mod_form.php'];

        $standardgradingelements = '$this->standard_grading_coursemodule_elements()';
        $this->assertStringContainsString($standardgradingelements, $modformfile);

        $libfile = $files['lib.php'];

        $this->assertRegExp('/case FEATURE_GRADE_HAS_GRADE:\s+return true/', $libfile);

        $scaleused = 'function demo_scale_used($moduleinstanceid, $scaleid)';
        $this->assertStringContainsString($scaleused, $libfile);

        $scaleusedanywhere = 'function demo_scale_used_anywhere($scaleid)';
        $this->assertStringContainsString($scaleusedanywhere, $libfile);

        $gradeitemupdate = 'function demo_grade_item_update($moduleinstance, $reset=false)';
        $this->assertStringContainsString($gradeitemupdate, $libfile);

        $gradeitemdelete = 'function demo_grade_item_delete($moduleinstance)';
        $this->assertStringContainsString($gradeitemdelete, $libfile);

        $updategrades = 'function demo_update_grades($moduleinstance, $userid = 0)';
        $this->assertStringContainsString($updategrades, $libfile);

        $gradefile = $files['grade.php'];

        $description = 'Redirect the user to the appropiate submission related page.';
        $this->assertStringContainsString($description, $gradefile);
    }

    /**
     * Tests creating the 'file_area' feature.
     */
    public function test_file_area_feature() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('lib.php', $files);
        $libfile = $files['lib.php'];

        $getfileareas = 'function demo_get_file_areas($course, $cm, $context)';
        $this->assertStringContainsString($getfileareas, $libfile);

        $getfileinfo = 'function demo_get_file_info('.
            '$browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename)';
        $this->assertStringContainsString($getfileinfo, $libfile);

        $pluginfile = 'function demo_pluginfile('.
            '$course, $cm, $context, $filearea, $args, $forcedownload, $options = array())';
        $this->assertStringContainsString($pluginfile, $libfile);
    }

    /**
     * Tests creating the 'navigation' feature.
     */
    public function test_navigation_feature() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('lib.php', $files);
        $libfile = $files['lib.php'];

        $extendnavigationargs = '$demonode, $course, $module, $cm';
        $extendnavigation = 'function demo_extend_navigation('.$extendnavigationargs.')';
        $this->assertStringContainsString($extendnavigation, $libfile);

        $extendsettingsargs = '$settingsnav, $demonode = null';
        $extendsettings = 'function demo_extend_settings_navigation('.$extendsettingsargs.')';
        $this->assertStringContainsString($extendsettings, $libfile);
    }

    /**
     * Tests creating the backup/moodle2/backup_<modname>_activity_task.class.php file.
     */
    public function test_backup_feature_activity_task_class() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $recipe['mod_features']['backup_moodle2']['settingslib'] = false;
        $manager->load_recipe($recipe);
        $manager->make();

        $modname = self::$modname;

        $files = $manager->get_files_content();

        $filename = 'backup/moodle2/backup_'.$modname.'_activity_task.class.php';
        $this->assertArrayHasKey($filename, $files);
        $taskfile = $files[$filename];

        // Verify the boilerplate.
        $description = 'The task that provides all the steps to perform a complete backup is defined here.';
        $this->assertStringContainsString($description, $taskfile);

        $this->assertRegExp('/\* @category\s+backup/', $taskfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $taskfile);

        $settingslibpath = self::$relpath.'/backup/moodle2/backup_'.$modname.'_settingslib.php';
        $this->assertStringNotContainsString('require_once($CFG->dirroot.'.'\'/'.$settingslibpath.'\')', $taskfile);

        $stepslibpath = self::$relpath.'/backup/moodle2/backup_'.$modname.'_stepslib.php';
        $this->assertStringContainsString('require_once($CFG->dirroot.'.'\'/'.$stepslibpath.'\')', $taskfile);

        $classdefinition = 'class backup_'.$modname.'_activity_task extends backup_activity_task';
        $this->assertStringContainsString($classdefinition, $taskfile);

        $stepdefinition = "\$this->add_step(new backup_".$modname."_activity_structure_step('"
            .$modname."_structure', '".$modname.".xml')";
        $this->assertStringContainsString($stepdefinition, $taskfile);
    }

    /**
     * Tests creating the backup/moodle2/backup_<modname>_settingslib.php file.
     */
    public function test_backup_feature_settingslib() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $modname = self::$modname;

        $files = $manager->get_files_content();
        $filename = 'backup/moodle2/backup_'.$modname.'_settingslib.php';
        $this->assertArrayHasKey($filename, $files);
        $settingslibfile = $files[$filename];

        // Verify the boilerplate.
        $description = 'Plugin custom settings are defined here.';
        $this->assertStringContainsString($description, $settingslibfile);
        $this->assertRegExp('/\* @category\s+backup/', $settingslibfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $settingslibfile);

        $filename = 'backup/moodle2/backup_'.$modname.'_activity_task.class.php';
        $taskfile = $files[$filename];

        $settingslibpath = self::$relpath.'/backup/moodle2/backup_'.$modname.'_settingslib.php';
        $this->assertStringContainsString('require_once($CFG->dirroot.'.'\'/'.$settingslibpath.'\')', $taskfile);
    }

    /**
     * Tests creating the backup/moodle2/backup_<modname>_stepslib.php file.
     */
    public function test_backup_feature_stepslib() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $modname = self::$modname;

        $files = $manager->get_files_content();
        $filename = 'backup/moodle2/backup_'.$modname.'_stepslib.php';
        $this->assertArrayHasKey($filename, $files);
        $stepslibfile = $files[$filename];

        // Verify the boilerplate.
        $description = 'Backup steps for mod_demo are defined here.';
        $this->assertStringContainsString($description, $stepslibfile);

        $this->assertRegExp('/\* @category\s+backup/', $stepslibfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $stepslibfile);

        $classdefinition = 'class backup_'.$modname.'_activity_structure_step extends backup_activity_structure_step';
        $this->assertStringContainsString($classdefinition, $stepslibfile);

        $element = $recipe['mod_features']['backup_moodle2']['backup_elements'][0]['name'];
        $nestedelement = '$'.$element.' = new backup_nested_element(\''.$element.'\', $attributes, $finalelements)';
        $this->assertStringContainsString($nestedelement, $stepslibfile);
    }

    /**
     * Tests creating the backup/moodle2/restore_<modname>_activity_task.class.php file.
     */
    public function test_backup_feature_restore_activity_task() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $modname = self::$modname;

        $files = $manager->get_files_content();
        $filename = 'backup/moodle2/restore_'.$modname.'_activity_task.class.php';
        $this->assertArrayHasKey($filename, $files);
        $restorefile = $files[$filename];

        // Verify the boilerplate.
        $description = 'The task that provides a complete restore of mod_demo is defined here.';
        $this->assertStringContainsString($description, $restorefile);

        $this->assertRegExp('/\* @category\s+backup/', $restorefile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $restorefile);

        $stepslibpath = self::$relpath.'/backup/moodle2/restore_'.$modname.'_stepslib.php';
        $this->assertStringContainsString('require_once($CFG->dirroot.'.'\'/'.$stepslibpath.'\')', $restorefile);

        $classdefinition = 'class restore_'.$modname.'_activity_task extends restore_activity_task';
        $this->assertStringContainsString($classdefinition, $restorefile);

        $stepdefinition = "\$this->add_step(new restore_".$modname."_activity_structure_step('".
            $modname."_structure', '".$modname.".xml')";
        $this->assertStringContainsString($stepdefinition, $restorefile);
    }

    /**
     * Tests creating the backup/moodle2/restore_stepslib.php file.
     */
    public function test_restore_stepslib() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $modname = self::$modname;

        $files = $manager->get_files_content();
        $filename = 'backup/moodle2/restore_'.$modname.'_stepslib.php';
        $this->assertArrayHasKey($filename, $files);
        $stepslibfile = $files[$filename];

        // Verify the boilerplate.
        $description = 'All the steps to restore mod_demo are defined here.';
        $this->assertStringContainsString($description, $stepslibfile);

        $this->assertRegExp('/\* @category\s+backup/', $stepslibfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $stepslibfile);

        $classdefinition = 'class restore_'.$modname.'_activity_structure_step extends restore_activity_structure_step';
        $this->assertStringContainsString($classdefinition, $stepslibfile);

        $element = $recipe['mod_features']['backup_moodle2']['restore_elements'][0]['name'];
        $path = $recipe['mod_features']['backup_moodle2']['restore_elements'][0]['path'];
        $elementpath = "\$paths[] = new restore_path_element('".$element."', '".$path."')";
        $this->assertStringContainsString($elementpath, $stepslibfile);

        $processfunction = 'protected function process_'.$element.'($data)';
        $this->assertStringContainsString($processfunction, $stepslibfile);
    }

    /**
     * Test that the db/install.xml was correctly generated.
     */
    public function test_install_xml() {
        $logger = new Logger('modtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $filename = 'db/install.xml';
        $this->assertArrayHasKey($filename, $files);
        $xml = $files[$filename];

        $this->assertStringContainsString('<XMLDB PATH="mod/demo/db"', $xml);
        $this->assertStringContainsString('<TABLES>', $xml);
        $this->assertStringContainsString('<FIELD NAME="id" TYPE="int"', $xml);
        $this->assertStringContainsString('<FIELD NAME="course" TYPE="int"', $xml);
        $this->assertStringContainsString('<FIELD NAME="name" TYPE="char"', $xml);
        $this->assertStringContainsString('<FIELD NAME="timemodified" TYPE="int"', $xml);
        $this->assertStringContainsString('<FIELD NAME="intro" TYPE="text"', $xml);
        $this->assertStringContainsString('<FIELD NAME="introformat" TYPE="int"', $xml);
    }
}
