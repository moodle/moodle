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
 * File containing tests for the theme plugin type.
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
 * Theme test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_theme_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'theme_test',
        'name'      => 'Theme test',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'theme_features'  => array(
            'all_layouts' => true,
            'doctype' => 'html5',
            'parents' => array(
                array('base_theme' => 'base'),
            ),
            'stylesheets' => array(
                array('name' => 'stylesheet'),
            ),
            'custom_layouts' => array(
                array('name' => 'layout'),
            ),
        ),
        'lang_strings' => array(
            array('id' => 'choosereadme', 'text' => 'Theme test')
        )
    );

    /**
     * Test creating the config.php file.
     */
    public function test_theme_config_php() {
        $logger = new Logger('themetest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('config.php', $files);
        $configfile = $files['config.php'];

        // Verify the boilerplate.
        $description = 'The configuration for '.$recipe['component'].' is defined here.';
        $this->assertStringContainsString($description, $configfile);

        $doctype = "\$THEME->doctype = '".$recipe['theme_features']['doctype']."'";
        $this->assertStringContainsString($doctype, $configfile);

        $basetheme = $recipe['theme_features']['parents'][0]['base_theme'];
        $parents = '/\$THEME->parents = array\(\s+\''.$basetheme.'\',\s+\)/';
        $this->assertRegExp($parents, $configfile);

        $stylesheetname = $recipe['theme_features']['stylesheets'][0]['name'];
        $stylesheets = '/\$THEME->sheets = array\(\s*\''.$stylesheetname.'\',\s*\);/';
        $this->assertRegExp($stylesheets, $configfile);

        $layouts = '$THEME->layouts = array(';
        $this->assertStringContainsString($layouts, $configfile);
    }

    /**
     * Test creating the feature files.
     */
    public function test_theme_feature_files() {
        $logger = new Logger('themetest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $layoutfile = 'layout/'.$recipe['theme_features']['custom_layouts'][0]['name'].'.php';
        $this->assertArrayHasKey($layoutfile, $files);

        $stylesheetfile = 'styles/'.$recipe['theme_features']['stylesheets'][0]['name'].'.css';
        $this->assertArrayHasKey($stylesheetfile, $files);
    }
}
