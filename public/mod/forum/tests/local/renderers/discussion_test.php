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

namespace mod_forum\local\renderers;

use mod_forum\local\container;

/**
 * Tests for the discussion renderer.
 *
 * @package    mod_forum
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(discussion::class)]
final class discussion_test extends \advanced_testcase {
    /**
     * The previous/next discussion lookup must only run once per page view.
     *
     * discuss.php renders the header navigation buttons and then calls render() on the same renderer, and both need
     * the neighbours. Rendering the buttons first must not cost more database reads than rendering alone.
     */
    public function test_navigation_buttons_and_render_look_up_neighbours_once(): void {
        global $DB, $USER;
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_and_enrol($course, 'student');
        $forum = $generator->create_module('forum', ['course' => $course->id]);
        $forumgenerator = $generator->get_plugin_generator('mod_forum');
        $discussions = [];
        foreach (range(1, 3) as $i) {
            $discussions[] = $forumgenerator->create_discussion([
                'course' => $course->id,
                'forum' => $forum->id,
                'userid' => $user->id,
                'timemodified' => time() + $i,
            ]);
        }
        $this->setUser($user);

        $vaultfactory = container::get_vault_factory();
        $forumentity = $vaultfactory->get_forum_vault()->get_from_id($forum->id);
        $discussionentity = $vaultfactory->get_discussion_vault()->get_from_id($discussions[1]->id);
        $postvault = $vaultfactory->get_post_vault();
        $firstpost = $postvault->get_from_id($discussionentity->get_first_post_id());
        $replies = $postvault->get_replies_to_post($USER, $firstpost, false, 'created ASC');
        $getrenderer = fn() => container::get_renderer_factory()
            ->get_discussion_renderer($forumentity, $discussionentity, FORUM_MODE_THREADED);

        // Warm up the caches unrelated to the neighbour lookup so they don't skew the read counts.
        $getrenderer()->render($USER, $firstpost, $replies);

        $renderer = $getrenderer();
        $before = $DB->perf_get_reads();
        $renderer->render($USER, $firstpost, $replies);
        $rendercost = $DB->perf_get_reads() - $before;

        $renderer = $getrenderer();
        $before = $DB->perf_get_reads();
        $buttons = $renderer->get_discussion_navigation_buttons();
        $renderer->render($USER, $firstpost, $replies);
        $this->assertSame($rendercost, $DB->perf_get_reads() - $before);

        // The neighbours are still resolved: the middle discussion has both a previous and a next one.
        $this->assertCount(2, $buttons['navbuttons']);
        $this->assertFalse($buttons['navbuttons'][0]['disabled']);
        $this->assertFalse($buttons['navbuttons'][1]['disabled']);
        $this->assertSame('prev', $buttons['navbuttons'][0]['type']);
        $this->assertStringEndsWith('d=' . $discussions[0]->id, $buttons['navbuttons'][0]['url']);
        $this->assertSame('next', $buttons['navbuttons'][1]['type']);
        $this->assertStringEndsWith('d=' . $discussions[2]->id, $buttons['navbuttons'][1]['url']);
    }

    /**
     * The neighbour links HTML is still exported, and reuses the neighbours already looked up.
     */
    public function test_neighbour_links_html_reuses_cached_neighbours(): void {
        global $DB;
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_and_enrol($course, 'student');
        $forum = $generator->create_module('forum', ['course' => $course->id]);
        $forumgenerator = $generator->get_plugin_generator('mod_forum');
        $discussions = [];
        foreach (range(1, 3) as $i) {
            $discussions[] = $forumgenerator->create_discussion([
                'course' => $course->id,
                'forum' => $forum->id,
                'userid' => $user->id,
                'timemodified' => time() + $i,
            ]);
        }
        $this->setUser($user);

        $vaultfactory = container::get_vault_factory();
        $forumentity = $vaultfactory->get_forum_vault()->get_from_id($forum->id);
        $discussionentity = $vaultfactory->get_discussion_vault()->get_from_id($discussions[1]->id);
        $renderer = container::get_renderer_factory()
            ->get_discussion_renderer($forumentity, $discussionentity, FORUM_MODE_NESTED_V2);

        $method = new \ReflectionMethod($renderer, 'get_neighbour_links_html');
        $html = $method->invoke($renderer);
        $this->assertStringContainsString('d=' . $discussions[0]->id, $html);
        $this->assertStringContainsString('d=' . $discussions[2]->id, $html);

        // A second call must not hit the database again.
        $before = $DB->perf_get_reads();
        $this->assertSame($html, $method->invoke($renderer));
        $this->assertSame($before, $DB->perf_get_reads());
    }
}
