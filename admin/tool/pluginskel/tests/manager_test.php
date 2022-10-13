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
 * Provides the {@see tool_pluginskel_manager_testcase} class.
 *
 * @package     tool_pluginskel
 * @category    test
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use tool_pluginskel\local\util\manager;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/pluginskel/vendor/autoload.php');

/**
 * Test case for {@see \tool_pluginskel\local\util\manager} class.
 *
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_manager_testcase extends advanced_testcase {

    /**
     * Return a base recipe for a plugin.
     *
     * @return array
     */
    protected function get_base_recipe() {
        return [
            'component' => 'local_foobar',
            'name' => 'Foo bar',
            'copyright' => '2018 David Mudrák <david@moodle.com>',
        ];
    }

    /**
     * Test adding strings to the language file.
     */
    public function test_add_lang_string() {

        $logger = new Logger('privacyprovidertest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = $this->get_base_recipe();

        $manager->load_recipe($recipe);
        $manager->add_lang_string('foobar', '<h1>Foo bar!</h1>'."\n".'Say {$a} or {$a->foo} here');
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('lang/en/local_foobar.php', $files);

        $langfile = $files['lang/en/local_foobar.php'];

        // The pluginname string is added implicitly.
        $this->assertStringContainsString("\$string['pluginname'] = 'Foo bar';", $langfile);

        // The foobar string has been added explicitly.
        $this->assertStringContainsString("\$string['foobar'] = '<h1>Foo bar!</h1>\n".'Say {$a} or {$a->foo} here\';', $langfile);
    }
}
