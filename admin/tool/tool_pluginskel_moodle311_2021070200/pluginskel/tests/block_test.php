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
 * File containing tests for generating a block plugin type.
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
 * Blocks test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_block_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'block_test',
        'name'      => 'Block test',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'features'  => array(
            'settings' => true,
        ),
        'block_features' => array(
            'instance_allow_multiple' => true,
            'edit_form' => true,
            'applicable_formats' => array(
                array('page' => 'all', 'allowed' => false),
                array('page' => 'course-view', 'allowed' => true),
                array('page' => 'course-view-social', 'allowed' => false)
            ),
            'backup_moodle2' => array(
                'restore_task' => true,
                'restore_stepslib' => true,
                'backup_stepslib' => true,
                'settingslib' => true,
                'backup_elements' => array(
                    array('name' => 'elt'),
                ),
                'restore_elements' => array(
                    array('name' => 'node', 'path' => '/path/to/file')
                )
            ),
        ),
         'capabilities' => array(
            array(
                'name' => 'addinstance',
                'title' => 'Add new block instance',
                'riskbitmask' => 'RISK_XSS | RISK_XSS',
                'captype' => 'write',
                'contextlevel' => 'CONTEXT_BLOCK',
                'archetypes' => array(
                    array(
                        'role' => 'student',
                        'permission' => 'CAP_ALLOW'
                    ),
                    array(
                        'role' => 'editingteacher',
                        'permission' => 'CAP_ALLOW'
                    )
                ),
                'clonepermissionsfrom' => 'moodle/site:manageblocks'
            )
        ),
    );

    /** @var string The plugin files path relative the Moodle root. */
    static protected $relpath;

    /** @var string The plugin name, without the frankenstyle prefix. */
    static protected $blockname;

    /**
     * Sets the the $modname.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        list($type, $blockname) = \core_component::normalize_component(self::$recipe['component']);

        $plugintypes = \core_component::get_plugin_types();
        $root = substr($plugintypes[$type], strlen($CFG->dirroot));

        self::$blockname = $blockname;
        self::$relpath = $root.'/'.$blockname;
    }

    /**
     * Tests creating the block_<blockname>.php file.
     */
    public function test_block_block_file() {
        $logger = new Logger('blocktest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $filename = $recipe['component'].'.php';
        $this->assertArrayHasKey($filename, $files);
        $blockfile = $files[$filename];

        list($type, $blockname) = \core_component::normalize_component($recipe['component']);
        $description = 'Block '.$blockname.' is defined here.';
        $this->assertStringContainsString($description, $blockfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        // The block file is not internal.
        $this->assertStringNotContainsString($moodleinternal, $blockfile);

        // The block file should not include the config.php file.
        $this->assertNotRegExp('/require.+config\.php/', $blockfile);

        $classdefinition = 'class '.$recipe['component'].' extends block_base';
        $this->assertStringContainsString($classdefinition, $blockfile);

        $init = 'public function init()';
        $this->assertStringContainsString($init, $blockfile);

        $getcontent = 'public function get_content()';
        $this->assertStringContainsString($getcontent, $blockfile);

        $specialization = 'public function specialization()';
        $this->assertStringContainsString($specialization, $blockfile);

        $allowmultiple = 'function instance_allow_multiple()';
        $this->assertStringContainsString($allowmultiple, $blockfile);

        $hasconfig = 'public function has_config()';
        $this->assertStringContainsString($hasconfig, $blockfile);

        $applicableformats = 'public function applicable_formats()';
        $this->assertStringContainsString($applicableformats, $blockfile);

        $allformat = "'all' => false,";
        $this->assertStringContainsString($allformat, $blockfile);

        $courseviewformat = "'course-view' => true,";
        $this->assertStringContainsString($courseviewformat, $blockfile);

        $courseviewsocialformat = "'course-view-social' => false,";
        $this->assertStringContainsString($courseviewsocialformat, $blockfile);
    }

    /**
     * Tests creating the edit_form.php file.
     */
    public function test_block_edit_form_feature() {
        $logger = new Logger('blocktest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('edit_form.php', $files);
        $editformfile = $files['edit_form.php'];

        list($type, $blockname) = \core_component::normalize_component($recipe['component']);
        $description = 'Form for editing '.$blockname.' block instances.';
        $this->assertStringContainsString($description, $editformfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        // The edit_form file is not internal.
        $this->assertStringNotContainsString($moodleinternal, $editformfile);

        // The edit_form file should not include the config.php file.
        $this->assertNotRegExp('/require.+config\.php/', $editformfile);

        $classdefinition = 'class '.$recipe['component'].'_edit_form extends block_edit_form';
        $this->assertStringContainsString($classdefinition, $editformfile);
    }

    /**
     * Tests creating the backup/moodle2/backup_<blockname>_block_task.class.php file.
     */
    public function test_backup_feature_backup_task() {
        $logger = new Logger('blocktest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $blockname = self::$blockname;

        $files = $manager->get_files_content();

        $filename = 'backup/moodle2/backup_'.$blockname.'_block_task.class.php';
        $this->assertArrayHasKey($filename, $files);
        $taskfile = $files[$filename];

        // Verify the boilerplate.
        $description = 'The task that provides all the steps to perform a complete backup is defined here.';
        $this->assertStringContainsString($description, $taskfile);

        $this->assertRegExp('/\* @category\s+backup/', $taskfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $taskfile);

        $settingslibpath = self::$relpath.'/backup/moodle2/backup_'.$blockname.'_settingslib.php';
        $this->assertStringContainsString('require_once($CFG->dirroot.'.'\'/'.$settingslibpath.'\')', $taskfile);

        $stepslibpath = self::$relpath.'/backup/moodle2/backup_'.$blockname.'_stepslib.php';
        $this->assertStringContainsString('require_once($CFG->dirroot.'.'\'/'.$stepslibpath.'\')', $taskfile);

        $classdefinition = 'class backup_'.$blockname.'_block_task extends backup_block_task';
        $this->assertStringContainsString($classdefinition, $taskfile);
    }

    /**
     * Tests creating the backup/moodle2/backup_<blockname>_settingslib.php file.
     */
    public function test_backup_feature_settingslib() {
        $logger = new Logger('blocktest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $blockname = self::$blockname;

        $files = $manager->get_files_content();
        $filename = 'backup/moodle2/backup_'.$blockname.'_settingslib.php';
        $this->assertArrayHasKey($filename, $files);
        $settingslibfile = $files[$filename];

        // Verify the boilerplate.
        $description = 'Plugin custom settings are defined here.';
        $this->assertStringContainsString($description, $settingslibfile);

        $this->assertRegExp('/\* @category\s+backup/', $settingslibfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $settingslibfile);
    }

    /**
     * Tests creating the backup/moodle2/backup_<blockname>_stepslib.php file.
     */
    public function test_backup_feature_stepslib() {
        $logger = new Logger('blocktest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $blockname = self::$blockname;

        $files = $manager->get_files_content();
        $filename = 'backup/moodle2/backup_'.$blockname.'_stepslib.php';
        $this->assertArrayHasKey($filename, $files);
        $stepslibfile = $files[$filename];

        // Verify the boilerplate.
        $description = 'Backup steps for '.$recipe['component'].' are defined here.';
        $this->assertStringContainsString($description, $stepslibfile);

        $this->assertRegExp('/\* @category\s+backup/', $stepslibfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $stepslibfile);

        $classdefinition = 'class backup_'.$blockname.'_block_structure_step extends backup_block_structure_step';
        $this->assertStringContainsString($classdefinition, $stepslibfile);

        $elementname = $recipe['block_features']['backup_moodle2']['backup_elements'][0]['name'];
        $nestedelement = '$'.$elementname.' = new backup_nested_element(\''.$elementname.'\', $attributes, $finalelements)';
        $this->assertStringContainsString($nestedelement, $stepslibfile);
    }

    /**
     * Tests creating the backup/moodle2/restore_<blockname>_restore_task.class.php file.
     */
    public function test_backup_feature_restore_task() {
        $logger = new Logger('blocktest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $blockname = self::$blockname;

        $files = $manager->get_files_content();
        $filename = 'backup/moodle2/restore_'.$blockname.'_block_task.class.php';
        $this->assertArrayHasKey($filename, $files);
        $restorefile = $files[$filename];

        // Verify the boilerplate.
        $description = 'The task that provides a complete restore of '.$recipe['component'].' is defined here.';
        $this->assertStringContainsString($description, $restorefile);

        $this->assertRegExp('/\* @category\s+backup/', $restorefile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $restorefile);

        $stepslibpath = self::$relpath.'/backup/moodle2/restore_'.$blockname.'_stepslib.php';
        $this->assertStringContainsString('require_once($CFG->dirroot.'.'\'/'.$stepslibpath.'\')', $restorefile);

        $classdefinition = 'class restore_'.$blockname.'_block_task extends restore_block_task';
        $this->assertStringContainsString($classdefinition, $restorefile);
    }

    /**
     * Tests creating the backup/moodle2/restore_<blockname>_stepslib.php file.
     */
    public function test_backup_feature_restore_stepslib() {
        $logger = new Logger('blocktest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $blockname = self::$blockname;

        $files = $manager->get_files_content();
        $filename = 'backup/moodle2/restore_'.$blockname.'_stepslib.php';
        $this->assertArrayHasKey($filename, $files);
        $stepslibfile = $files[$filename];

        // Verify the boilerplate.
        $description = 'All the steps to restore '.$recipe['component'].' are defined here.';
        $this->assertStringContainsString($description, $stepslibfile);

        $this->assertRegExp('/\* @category\s+backup/', $stepslibfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $stepslibfile);

        $classdefinition = 'class restore_'.$blockname.'_block_structure_step extends restore_structure_step';
        $this->assertStringContainsString($classdefinition, $stepslibfile);

        $element = $recipe['block_features']['backup_moodle2']['restore_elements'][0]['name'];
        $path = $recipe['block_features']['backup_moodle2']['restore_elements'][0]['path'];
        $elementpath = "\$paths[] = new restore_path_element('".$element."', '".$path."')";
        $this->assertStringContainsString($elementpath, $stepslibfile);

        $processfunction = 'protected function process_'.$element.'($data)';
        $this->assertStringContainsString($processfunction, $stepslibfile);
    }
}
