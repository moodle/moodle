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
 * @package    block_rss_client
 * @subpackage backup-moodle2
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that wll be used by the restore_rss_client_block_task
 */

/**
 * Define the complete rss_client  structure for restore
 */
class restore_rss_client_block_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('block', '/block', true);
        $paths[] = new restore_path_element('rss_client', '/block/rss_client');
        $paths[] = new restore_path_element('feed', '/block/rss_client/feeds/feed');

        return $paths;
    }

    public function process_block($data) {
        global $DB;

        $data = (object)$data;
        $feedsarr = array(); // To accumulate feeds

        // For any reason (non multiple, dupe detected...) block not restored, return
        if (!$this->task->get_blockid()) {
            return;
        }

        // Iterate over all the feed elements, creating them if needed
        if (isset($data->rss_client['feeds']['feed'])) {
            foreach ($data->rss_client['feeds']['feed'] as $feed) {
                $feed = (object)$feed;
                // Look if the same feed is available by url and (shared or userid)
                $select = 'url = :url AND (shared = 1 OR userid = :userid)';
                $params = array('url' => $feed->url, 'userid' => $this->task->get_userid());
                // The feed already exists, use it
                if ($feedid = $DB->get_field_select('block_rss_client', 'id', $select, $params, IGNORE_MULTIPLE)) {
                    $feedsarr[] = $feedid;

                // The feed doesn't exist, create it
                } else {
                    $feed->userid = $this->task->get_userid();
                    $feedid = $DB->insert_record('block_rss_client', $feed);
                    $feedsarr[] = $feedid;
                }
            }
        }

        // Adjust the serialized configdata->rssid to the created/mapped feeds
        // Get the configdata
        $configdata = $DB->get_field('block_instances', 'configdata', array('id' => $this->task->get_blockid()));
        // Extract configdata
        $config = unserialize(base64_decode($configdata));
        if (empty($config)) {
            $config = new stdClass();
        }
        // Set array of used rss feeds
        $config->rssid = $feedsarr;
        // Serialize back the configdata
        $configdata = base64_encode(serialize($config));
        // Set the configdata back
        $DB->set_field('block_instances', 'configdata', $configdata, array('id' => $this->task->get_blockid()));
    }
}
