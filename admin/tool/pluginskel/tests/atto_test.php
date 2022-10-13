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
 * File containing tests for generating an atto plugin type.
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
 * Atto test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_atto_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'atto_test',
        'name'      => 'Atto test',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'features'  => array(
            'settings' => true,
            'settings' => true,
        ),
        'atto_features' => array(
            'strings_for_js' => array(
                array('id' => 'stringone', 'text' => 'String one text'),
            ),
            'params_for_js' => array(
                array('name' => 'paramone', 'value' => 'val', 'default' => '')
            ),
        ),
    );

    /** @var string The plugin type. */
    protected static $plugintype;

    /** @var string The plugin name. */
    protected static $pluginname;

    /**
     * Sets the the $plugintype.
     */
    public static function setUpBeforeClass(): void {
        list($type, $name) = \core_component::normalize_component(self::$recipe['component']);
        self::$plugintype = $type;
        self::$pluginname = $name;
    }


    /**
     * Tests creating the button.js file.
     */
    public function test_atto_button_js() {
        $logger = new Logger('attotest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('yui/src/button/js/button.js', $files);
        $buttonjsfile = $files['yui/src/button/js/button.js'];

        $description = 'The Atto plugin '.self::$pluginname.' is defined here.';
        $this->assertStringContainsString($description, $buttonjsfile);

        $namespace = "Y.namespace('M.".$recipe['component']."').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin";
        $this->assertStringContainsString($namespace, $buttonjsfile);

        $paramname = $recipe['atto_features']['params_for_js'][0]['name'];
        $default = $recipe['atto_features']['params_for_js'][0]['default'];
        $attrs = '/ATTRS: {\s+'.$paramname.': {\s+value: \''.$default.'\'/';
        $this->assertRegExp($attrs, $buttonjsfile);
    }

    /**
     * Tests creating the button.js file.
     */
    public function test_atto_build_json() {
        $logger = new Logger('attotest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('yui/src/button/build.json', $files);
        $buildfile = $files['yui/src/button/build.json'];

        $name = 'moodle-'.$recipe['component'].'-button';
        $this->assertStringContainsString($name, $buildfile);
    }

    /**
     * Tests creating the button.json file.
     */
    public function test_atto_button_json() {
        $logger = new Logger('attotest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('yui/src/button/meta/button.json', $files);
        $buttonfile = $files['yui/src/button/meta/button.json'];

        $name = 'moodle-'.$recipe['component'].'-button';
        $this->assertStringContainsString($name, $buttonfile);
    }

    /**
     * Tests creating the lib.php file.
     */
    public function test_atto_lib_php() {
        $logger = new Logger('attotest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('lib.php', $files);
        $libfile = $files['lib.php'];

        $stringsforjs = 'function '.$recipe['component'].'_strings_for_js()';
        $this->assertStringContainsString($stringsforjs, $libfile);

        $id = $recipe['atto_features']['strings_for_js'][0]['id'];
        $strings = '/\$PAGE->requires_strings_for_js\(array\(\s+\''.$id.'\',\s+\)\)/';
        $this->assertRegExp($strings, $libfile);

        $paramsforjs = 'function '.$recipe['component'].'_params_for_js($elementid, $options, $foptions)';
        $this->assertStringContainsString($paramsforjs, $libfile);

        $paramname = $recipe['atto_features']['params_for_js'][0]['name'];
        $value = $recipe['atto_features']['params_for_js'][0]['value'];
        $params = '/return array\(\s+\''.$paramname.'\' => \''.$value.'\',\s+\);/';
        $this->assertRegExp($params, $libfile);
    }

    /**
     * Tests creating the lang strings.
     */
    public function test_atto_lang_strings() {
        $logger = new Logger('attotest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('lang/en/'.$recipe['component'].'.php', $files);
        $langfile = $files['lang/en/'.$recipe['component'].'.php'];

        $langstring = $recipe['atto_features']['strings_for_js'][0];
        $this->assertStringContainsString("\$string['".$langstring['id']."'] = '".$langstring['text']."';", $langfile);
    }
}
