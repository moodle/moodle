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
 * The forum module cron trait.
 *
 * @package    mod_forum
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

trait mod_forum_tests_cron_trait {
    /**
     * Run the main cron task to queue all tasks, and ensure that posts
     * were sent to the correct users.
     *
     * @param   \stdClass[] $expectations The list of users, along with their expected count of messages and digests.
     */
    protected function queue_tasks_and_assert($expectations = []) {
        global $DB;

        // Note, we cannot use expectOutputRegex because it only allows for a single RegExp.
        ob_start();
        \core\cron::setup_user();
        $cron = new \mod_forum\task\cron_task();
        $cron->execute();
        $output = ob_get_contents();
        ob_end_clean();

        $uniqueusers = 0;
        foreach ($expectations as $expect) {
            $expect->digests = isset($expect->digests) ? $expect->digests : 0;
            $expect->messages = isset($expect->messages) ? $expect->messages : 0;
            $expect->mentioned = isset($expect->mentioned) ? $expect->mentioned : false;
            if ($expect->digests || $expect->messages) {
                $expect->mentioned = true;
            }
            if (!$expect->mentioned) {
                $this->assertDoesNotMatchRegularExpression("/Queued 0 for {$expect->userid}/", $output);
            } else {
                $uniqueusers++;
                $this->assertMatchesRegularExpression(
                        "/Queued {$expect->digests} digests and {$expect->messages} messages for {$expect->userid}/",
                        $output
                    );
            }
        }

        if (empty($expectations)) {
            $this->assertMatchesRegularExpression("/No posts found./", $output);
        } else {
            $this->assertMatchesRegularExpression("/Unique users: {$uniqueusers}/", $output);
        }

        // Update the forum queue for digests.
        $DB->execute("UPDATE {forum_queue} SET timemodified = timemodified - 1");
    }

    /**
     * Run any send_user_notifications tasks for the specified user, and
     * ensure that the posts specified were sent.
     *
     * @param   \stdClass   $user
     * @param   \stdClass[] $posts
     * @param   bool        $ignoreemptyposts
     */
    protected function send_notifications_and_assert($user, $posts = [], $ignoreemptyposts = false) {
        ob_start();
        $this->runAdhocTasks(\mod_forum\task\send_user_notifications::class, $user->id);
        $output = ob_get_contents();
        ob_end_clean();

        if (empty($posts) && !$ignoreemptyposts) {
            $this->assertEquals('', $output);
        } else {
            $this->assertMatchesRegularExpression("/Sending messages to {$user->username}/", $output);
            foreach ($posts as $post) {
                $this->assertMatchesRegularExpression("/Post {$post->id} sent/", $output);
            }
            $count = count($posts);
            $this->assertMatchesRegularExpression("/Sent {$count} messages with 0 failures/", $output);
        }
    }

    /**
     * Run any send_user_digests tasks for the specified user, and
     * ensure that the posts specified were sent.
     *
     * @param   \stdClass   $user
     * @param   \stdClass[] $fullposts
     * @param   \stdClass[] $shortposts
     */
    protected function send_digests_and_assert($user, $fullposts = [], $shortposts = []) {
        ob_start();
        $this->runAdhocTasks(\mod_forum\task\send_user_digests::class, $user->id);
        $output = ob_get_contents();
        ob_end_clean();

        if (empty($shortposts) && empty($fullposts)) {
            $this->assertEquals('', $output);
            $this->assertMatchesRegularExpression("/Digest sent with 0 messages./", $output);
        } else {
            $this->assertMatchesRegularExpression("/Sending forum digests for {$user->username}/", $output);
            foreach ($fullposts as $post) {
                $this->assertMatchesRegularExpression("/Adding post {$post->id} in format 1/", $output);
            }
            foreach ($shortposts as $post) {
                $this->assertMatchesRegularExpression("/Adding post {$post->id} in format 2/", $output);
            }
            $count = count($fullposts) + count($shortposts);
            $this->assertMatchesRegularExpression("/Digest sent with {$count} messages./", $output);
        }
    }
}
