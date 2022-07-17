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
 * File containing tests for the 'templates' feature.
 *
 * @package     tool_pluginskel
 * @category    test
 * @copyright   2021 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Monolog\Logger;
use Monolog\Handler\TestHandler;
use tool_pluginskel\local\util\manager;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/pluginskel/vendor/autoload.php');

/**
 * Templates test class.
 */
class tool_pluginskel_templates_testcase extends advanced_testcase {

    /**
     * Tests creating a mustache template file.
     */
    public function test_templates() {

        $logger = new Logger('templatestest');
        $log = new TestHandler();
        $logger->pushHandler($log);
        $manager = manager::instance($logger);

        $recipe = [
            'component' => 'tool_demo',
            'templates' => [
                'index_page',
                'welcome_widget',
            ],
        ];

        $manager->load_recipe($recipe);
        $manager->make();

        $this->assertFalse($log->hasWarningRecords(), 'Unexpected warnings: ' . json_encode($log->getRecords()));

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('templates/index_page.mustache', $files);
        $this->assertArrayHasKey('templates/welcome_widget.mustache', $files);

        $this->assertStringContainsString('@template tool_demo/index_page', $files['templates/index_page.mustache']);
        $this->assertStringContainsString('@template tool_demo/welcome_widget', $files['templates/welcome_widget.mustache']);
    }
}
