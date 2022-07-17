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
 * File containing tests for generating PHPUnit tests.
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
 * PHPUnit tests test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_phpunit_tests_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'local_test',
        'name'      => 'PHPUnit tests test',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'phpunit_tests' => array(
            array('classname' => 'first'),
            array('classname' => 'local_test_second_testcase'),
        )
    );

    /**
     * Tests creating the test files.
     */
    public function test_phpunit_test_files() {
        $logger = new Logger('phpunitteststest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('tests/first_test.php', $files);
        $firsttest = $files['tests/first_test.php'];

        $description = 'The first test class.';
        $this->assertStringContainsString($description, $firsttest);

        $classdefinition = 'class local_test_first_testcase extends advanced_testcase {';
        $this->assertStringContainsString($classdefinition, $firsttest);

        $this->assertArrayHasKey('tests/second_test.php', $files);
        $secondtest = $files['tests/second_test.php'];

        $classdefinition = 'class local_test_second_testcase extends advanced_testcase {';
        $this->assertStringContainsString($classdefinition, $secondtest);
    }
}
