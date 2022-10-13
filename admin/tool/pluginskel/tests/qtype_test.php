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
 * File containing tests for generating a question type module.
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
 * Question type test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_qtype_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'qtype_test',
        'name'      => 'Qtype test',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'qtype_features' => array(
            'base_class' => 'question_graded_automatically',
        ),
        'lang_strings'   => array(
            array('id' => 'pluginnamesummary', 'text' => 'Plugin name summary'),
            array('id' => 'pluginnameadding', 'text' => 'Plugin name when adding'),
            array('id' => 'pluginnameediting', 'text' => 'Plugin name when editing'),
            array('id' => 'pluginname_help', 'text' => 'Help text')
        )
    );

    /** @var string The plugin name, without the frankenstyle prefix. */
    static protected $qtypename;

    /**
     * Sets the $qtypename.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        list($type, $qtypename) = \core_component::normalize_component(self::$recipe['component']);

        self::$qtypename = $qtypename;
    }

    /**
     * Tests creating the basic files.
     */
    public function test_qtype_files() {
        $logger = new Logger('qtypetest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('question.php', $files);
        $this->assertArrayHasKey('questiontype.php', $files);
        $this->assertArrayHasKey('classes/output/renderer.php', $files);

        $editform = 'edit_'.self::$qtypename.'_form.php';
        $this->assertArrayHasKey($editform, $files);
    }

    /**
     * Tests the file question.php.
     */
    public function test_qtype_question_php() {
        $logger = new Logger('qtypetest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('question.php', $files);
        $questionfile = $files['question.php'];

        $description = 'Question definition class for '.self::$qtypename;
        $this->assertStringContainsString($description, $questionfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        $this->assertStringContainsString($moodleinternal, $questionfile);

        $baseclass = $recipe['qtype_features']['base_class'];
        $questionclass = 'class '.$recipe['component'].'_question extends '.$baseclass;
        $this->assertStringContainsString($questionclass, $questionfile);
    }

    /**
     * Tests the file questiontype.php.
     */
    public function test_qtype_questiontype_php() {
        $logger = new Logger('qtypetest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('questiontype.php', $files);
        $questiontypefile = $files['questiontype.php'];

        $description = 'Question type class for '.self::$qtypename.' is defined here.';
        $this->assertStringContainsString($description, $questiontypefile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        $this->assertStringContainsString($moodleinternal, $questiontypefile);

        $questiontypeclass = 'class '.$recipe['component'].' extends question_type';
        $this->assertStringContainsString($questiontypeclass, $questiontypefile);
    }

    /**
     * Tests the file renderer.php.
     */
    public function test_qtype_renderer_php() {
        $logger = new Logger('qtypetest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('classes/output/renderer.php', $files);
        $rendererfile = $files['classes/output/renderer.php'];

        $description = 'The '.self::$qtypename.' question renderer class is defined here.';
        $this->assertStringContainsString($description, $rendererfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        $this->assertStringContainsString($moodleinternal, $rendererfile);

        $rendererclass = 'class '.$recipe['component'].'_renderer extends qtype_renderer';
        $this->assertStringContainsString($rendererclass, $rendererfile);
    }

    /**
     * Tests the file edit_<qtypename>_form.php.
     */
    public function test_qtype_edit_form_php() {
        $logger = new Logger('qtypetest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $editform = 'edit_'.self::$qtypename.'_form.php';
        $this->assertArrayHasKey($editform, $files);
        $editformfile = $files[$editform];

        $description = 'The editing form for '.self::$qtypename.' question type is defined here.';
        $this->assertStringContainsString($description, $editformfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        $this->assertStringContainsString($moodleinternal, $editformfile);

        $editformclass = 'class '.$recipe['component'].'_edit_form extends question_edit_form';
        $this->assertStringContainsString($editformclass, $editformfile);
    }
}
