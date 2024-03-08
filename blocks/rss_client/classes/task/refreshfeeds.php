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
 * Task for updating RSS feeds for rss client block
 *
 * @package   block_rss_client
 * @author    Farhan Karmali <farhan6318@gmail.com>
 * @copyright Farhan Karmali 2018
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_rss_client\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Task for updating RSS feeds for rss client block
 *
 * @package   block_rss_client
 * @author    Farhan Karmali <farhan6318@gmail.com>
 * @copyright Farhan Karmali 2018
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class refreshfeeds extends \core\task\scheduled_task {

    /** The maximum time in seconds that cron will wait between attempts to retry failing RSS feeds. */
    const CLIENT_MAX_SKIPTIME = HOURSECS * 12;

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('refreshfeedstask', 'block_rss_client');
    }

    /**
     * This task goes through all the feeds. If the feed has a skipuntil value
     * that is less than the current time cron will attempt to retrieve it
     * with the cache duration set to 0 in order to force the retrieval of
     * the item and refresh the cache.
     *
     * If a feed fails then the skipuntil time of that feed is set to be
     * later than the next expected task time. The amount of time will
     * increase each time the fetch fails until the maximum is reached.
     *
     * If a feed that has been failing is successfully retrieved it will
     * go back to being handled as though it had never failed.
     *
     * Task should therefore process requests for permanently broken RSS
     * feeds infrequently, and temporarily unavailable feeds will be tried
     * less often until they become available again.
     */
    public function execute() {
        global $CFG, $DB;
        require_once("{$CFG->libdir}/simplepie/moodle_simplepie.php");

        // We are going to measure execution times.
        $starttime = microtime();
        $starttimesec = time();

        // Fetch all site feeds.
        $rs = $DB->get_recordset('block_rss_client');
        $counter = 0;
        mtrace('');
        foreach ($rs as $rec) {
            mtrace('    ' . $rec->url . ' ', '');

            // Skip feed if it failed recently.
            if ($starttimesec < $rec->skipuntil) {
                mtrace('skipping until ' . userdate($rec->skipuntil));
                continue;
            }

            $feed = $this->fetch_feed($rec->url);

            if ($feed->error()) {
                // Skip this feed (for an ever-increasing time if it keeps failing).
                $rec->skiptime = $this->calculate_skiptime($rec->skiptime);
                $rec->skipuntil = time() + $rec->skiptime;
                $DB->update_record('block_rss_client', $rec);
                mtrace("Error: could not load/find the RSS feed - skipping for {$rec->skiptime} seconds.");
            } else {
                mtrace ('ok');
                // It worked this time, so reset the skiptime.
                if ($rec->skiptime > 0) {
                    $rec->skiptime = 0;
                    $rec->skipuntil = 0;
                    $DB->update_record('block_rss_client', $rec);
                }
                // Only increase the counter when a feed is sucesfully refreshed.
                $counter ++;
            }
        }
        $rs->close();

        // Show times.
        mtrace($counter . ' feeds refreshed (took ' . microtime_diff($starttime, microtime()) . ' seconds)');
    }

    /**
     * Fetch a feed for the specified URL.
     *
     * @param   string  $url The URL to fetch
     * @return  \moodle_simplepie
     */
    protected function fetch_feed(string $url): \moodle_simplepie {
        // Fetch the rss feed, using standard simplepie caching so feeds will be renewed only if cache has expired.
        \core_php_time_limit::raise(60);

        $feed = new \moodle_simplepie();

        // Set timeout for longer than normal to be agressive at fetching feeds if possible..
        $feed->set_timeout(40);
        $feed->set_cache_duration(0);
        $feed->set_feed_url($url);
        $feed->init();

        return $feed;
    }

    /**
     * Calculates a new skip time for a record based on the current skip time.
     *
     * @param   int     $currentskip The current skip time of a record.
     * @return  int     The newly calculated skip time.
     */
    protected function calculate_skiptime(int $currentskip): int {
        // If the feed has never failed, then the initial skiptime will be 0. We use a default of 5 minutes in this case.
        // If the feed has previously failed then we double that time.
        $newskiptime = max(MINSECS * 5, ($currentskip * 2));

        // Max out at the CLIENT_MAX_SKIPTIME.
        return min($newskiptime, self::CLIENT_MAX_SKIPTIME);
    }
}
