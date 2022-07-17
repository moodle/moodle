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
 * File containing tests for generating the version.php file.
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
 * Version.php test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_version_php_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'local_versionphptest',
        'release'   => '0.1.0',
        'version'   => '2016062300',
        'name'      => 'Version.php test',
        'maturity'  => 'MATURITY_ALPHA',
        'requires'  => '2015051100',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'dependencies'  => array(
            array('plugin' => 'mod_forum', 'version' => 'ANY_VERSION'),
            array('plugin' => 'tool_another', 'version' => '2015121200')
        ),
    );

    /**
     * Test creating the version.php file.
     */
    public function test_version_php() {
        global $CFG;

        $logger = new Logger('versionphptest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('version.php', $files);
        $versionfile = $files['version.php'];

        $description = 'Plugin version and other meta-data are defined here';
        $this->assertStringContainsString($description, $versionfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        $this->assertStringContainsString($moodleinternal, $versionfile);

        list($type, $name) = core_component::normalize_component($recipe['component']);
        $fullcomponent = $type.'_'.$name;
        $this->assertStringContainsString("\$plugin->component = '".$fullcomponent."'", $versionfile);

        $this->assertStringContainsString("\$plugin->release = '".$recipe['release']."'", $versionfile);
        $this->assertStringContainsString("\$plugin->version = ".$recipe['version'], $versionfile);
        $this->assertStringContainsString("\$plugin->requires = ".$recipe['requires'], $versionfile);
        $this->assertStringContainsString("\$plugin->maturity = ".$recipe['maturity'], $versionfile);
        $this->assertStringContainsString('$plugin->dependencies', $versionfile);

        $plugin = $recipe['dependencies'][0]['plugin'];
        $version = $recipe['dependencies'][0]['version'];
        $this->assertStringContainsString("'$plugin' => $version", $versionfile);

        $plugin = $recipe['dependencies'][1]['plugin'];
        $version = $recipe['dependencies'][1]['version'];
        $this->assertStringContainsString("'$plugin' => $version", $versionfile);
    }
}
