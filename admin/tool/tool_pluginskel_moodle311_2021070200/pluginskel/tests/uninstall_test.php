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
 * File containing tests for the 'uninstall' feature.
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
 * Uninstall test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_uninstall_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'local_uninstalltest',
        'name'      => 'Uninstall test',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'features'  => array(
            'uninstall' => true
        )
    );

    /**
     * Test creating the README.md file.
     */
    public function test_uninstall() {
        $logger = new Logger('uninstalltest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('db/uninstall.php', $files);
        $uninstallfile = $files['db/uninstall.php'];

        $description = 'Code that is executed before the tables and data are dropped during the plugin uninstallation.';
        $this->assertStringContainsString($description, $uninstallfile);
        $this->assertStringContainsString('function xmldb_'.$recipe['component'].'_uninstall()', $uninstallfile);
    }

    /**
     * Test that activity modules get the install function with the correct name.
     */
    public function test_mod_naming_exception() {
        $logger = new Logger('uninstalltest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = [
            'component' => 'mod_test',
            'name' => 'Uninstall test',
            'features' => [
                'uninstall' => true
            ],
        ];
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $installfile = $files['db/uninstall.php'];

        $this->assertStringContainsString('function xmldb_test_uninstall()', $installfile);
    }
}
