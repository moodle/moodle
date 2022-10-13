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
 * File containing tests for the 'events' feature.
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
 * Events test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_events_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'local_eventstest',
        'name'      => 'Events test',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'events' => array(
            array(
                'eventname' => 'first_event',
                'extends' => '\core\event\first_event',
            ),
            array(
                'eventname' => 'second_event'
            )
        )
    );

    /**
     * Test creating the events class files.
     */
    public function test_events() {
        $logger = new Logger('eventstest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('classes/event/first_event.php', $files);
        $this->assertArrayHasKey('classes/event/second_event.php', $files);

        $eventfile = $files['classes/event/first_event.php'];

        // Verify the boilerplate.
        $description = 'The first_event event class.';
        $this->assertStringContainsString($description, $eventfile);

        list($type, $pluginname) = \core_component::normalize_component($recipe['component']);
        $this->assertStringContainsString('namespace '.$type.'_'.$pluginname.'\event', $eventfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        $this->assertStringNotContainsString($moodleinternal, $eventfile);

        $classname = 'class '.$recipe['events'][0]['eventname'].' extends '.$recipe['events'][0]['extends'];
        $this->assertStringContainsString($classname, $eventfile);

        $eventfile = $files['classes/event/second_event.php'];

        $classname = 'class '.$recipe['events'][1]['eventname'].' extends \core\event\base';
        $this->assertStringContainsString($classname, $eventfile);
    }
}
