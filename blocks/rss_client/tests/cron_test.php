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

namespace block_rss_client;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../moodleblock.class.php');
require_once(__DIR__ . '/../block_rss_client.php');

/**
 * PHPunit tests for rss client cron.
 *
 * @package    block_rss_client
 * @copyright  2015 Universit of Nottingham
 * @author     Neill Magill <neill.magill@nottingham.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron_test extends \advanced_testcase {
    /**
     * Test that when a record has a skipuntil time that is greater
     * than the current time the attempt is skipped.
     */
    public function test_skip() {
        global $DB, $CFG;
        $this->resetAfterTest();
        // Create a RSS feed record with a skip until time set to the future.
        $record = (object) array(
            'userid' => 1,
            'title' => 'Skip test feed',
            'preferredtitle' => '',
            'description' => 'A feed to test the skip time.',
            'shared' => 0,
            'url' => 'http://example.com/rss',
            'skiptime' => 330,
            'skipuntil' => time() + 300,
        );
        $DB->insert_record('block_rss_client', $record);

        $task = new \block_rss_client\task\refreshfeeds();
        ob_start();

        // Silence SimplePie php notices.
        $errorlevel = error_reporting($CFG->debug & ~E_USER_NOTICE);
        $task->execute();
        error_reporting($errorlevel);

        $cronoutput = ob_get_clean();
        $this->assertStringContainsString('skipping until ' . userdate($record->skipuntil), $cronoutput);
        $this->assertStringContainsString('0 feeds refreshed (took ', $cronoutput);
    }

    /**
     * Data provider for skip time tests.
     *
     * @return  array
     */
    public function skip_time_increase_provider(): array {
        return [
            'Never failed' => [
                'skiptime' => 0,
                'skipuntil' => 0,
                'newvalue' => MINSECS * 5,
            ],
            'Failed before' => [
                // This should just double the time.
                'skiptime' => 330,
                'skipuntil' => time(),
                'newvalue' => 660,
            ],
            'Near max' => [
                'skiptime' => \block_rss_client\task\refreshfeeds::CLIENT_MAX_SKIPTIME - 5,
                'skipuntil' => time(),
                'newvalue' => \block_rss_client\task\refreshfeeds::CLIENT_MAX_SKIPTIME,
            ],
        ];
    }

    /**
     * Test that when a feed has an error the skip time is increased correctly.
     *
     * @dataProvider    skip_time_increase_provider
     */
    public function test_error($skiptime, $skipuntil, $newvalue) {
        global $DB, $CFG;
        $this->resetAfterTest();

        require_once("{$CFG->libdir}/simplepie/moodle_simplepie.php");

        $time = time();
        // A record that has failed before.
        $record = (object) [
            'userid' => 1,
            'title' => 'Skip test feed',
            'preferredtitle' => '',
            'description' => 'A feed to test the skip time.',
            'shared' => 0,
            'url' => 'http://example.com/rss',
            'skiptime' => $skiptime,
            'skipuntil' => $skipuntil,
        ];
        $record->id = $DB->insert_record('block_rss_client', $record);

        // Run the scheduled task and have it fail.
        $task = $this->getMockBuilder(\block_rss_client\task\refreshfeeds::class)
            ->onlyMethods(['fetch_feed'])
            ->getMock();

        $piemock = $this->getMockBuilder(\moodle_simplepie::class)
            ->onlyMethods(['error'])
            ->getMock();

        $piemock->method('error')
            ->willReturn(true);

        $task->method('fetch_feed')
            ->willReturn($piemock);

        // Run the cron and capture its output.
        $this->expectOutputRegex("/.*Error: could not load\/find the RSS feed - skipping for {$newvalue} seconds.*/");
        $task->execute();
    }
}
