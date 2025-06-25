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

namespace mod_forum;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/forum/lib.php');

use mod_forum\local\exporters\forum as forum_exporter;

/**
 * The discussion forum tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class exporters_forum_test extends \advanced_testcase {

    #[\Override]
    public function setUp(): void {
        parent::setUp();
        // We must clear the subscription caches.
        // This has to be done both before each test, and after in case of other tests using these functions.
        subscriptions::reset_forum_cache();
    }

    #[\Override]
    public function tearDown(): void {
        // We must clear the subscription caches.
        // // This has to be done both before each test, and after in case of other tests using these functions.
        subscriptions::reset_forum_cache();
        parent::tearDown();
    }

    /**
     * Test the export function returns expected values.
     */
    public function test_export(): void {
        global $PAGE;
        $this->resetAfterTest();

        $renderer = $PAGE->get_renderer('core');
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', [
            'course' => $course->id,
            'groupmode' => VISIBLEGROUPS,
            'forcesubscribe' => FORUM_FORCESUBSCRIBE,
        ]);
        $coursemodule = get_coursemodule_from_instance('forum', $forum->id);
        $context = \context_module::instance($coursemodule->id);
        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $forum = $entityfactory->get_forum_from_stdClass($forum, $context, $coursemodule, $course);

        $exporter = new forum_exporter($forum, [
            'legacydatamapperfactory' => \mod_forum\local\container::get_legacy_data_mapper_factory(),
            'urlfactory' => \mod_forum\local\container::get_url_factory(),
            'capabilitymanager' => (\mod_forum\local\container::get_manager_factory())->get_capability_manager($forum),
            'user' => $user,
            'currentgroup' => null,
            'vaultfactory' => \mod_forum\local\container::get_vault_factory()
        ]);

        $exportedforum = $exporter->export($renderer);

        $this->assertEquals($forum->get_id(), $exportedforum->id);
        $this->assertEquals(VISIBLEGROUPS, $exportedforum->state['groupmode']);
        $this->assertEquals(false, $exportedforum->userstate['tracked']);
        $this->assertEquals(false, $exportedforum->userstate['subscribed']);
        $this->assertEquals(false, $exportedforum->capabilities['viewdiscussions']);
        $this->assertEquals(false, $exportedforum->capabilities['create']);
        $this->assertEquals(false, $exportedforum->capabilities['subscribe']);
        $this->assertNotEquals(null, $exportedforum->urls['create']);
        $this->assertNotEquals(null, $exportedforum->urls['markasread']);

        // Enrol the user in the course and check the capabilities and user state.
        $datagenerator->enrol_user($user->id, $course->id);
        $exportedforum = $exporter->export($renderer);
        $this->assertEquals(true, $exportedforum->userstate['subscribed']);
        $this->assertEquals(true, $exportedforum->capabilities['viewdiscussions']);
    }
}
