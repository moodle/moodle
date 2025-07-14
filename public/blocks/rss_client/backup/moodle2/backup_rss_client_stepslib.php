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
 * Define all the backup steps that wll be used by the backup_rss_client_block_task
 */

/**
 * Define the complete forum structure for backup, with file and id annotations
 */
class backup_rss_client_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
        global $DB;

        // Get the block
        $block = $DB->get_record('block_instances', array('id' => $this->task->get_blockid()));
        // Extract configdata
        $config = unserialize_object(base64_decode($block->configdata));
        // Get array of used rss feeds
        if (!empty($config->rssid)) {
            $feedids = $config->rssid;
            // Get the IN corresponding query
            list($in_sql, $in_params) = $DB->get_in_or_equal($feedids);
            // Define all the in_params as sqlparams
            foreach ($in_params as $key => $value) {
                $in_params[$key] = backup_helper::is_sqlparam($value);
            }
        }

        // Define each element separated

        $rss_client = new backup_nested_element('rss_client', array('id'), null);

        $feeds = new backup_nested_element('feeds');

        $feed = new backup_nested_element('feed', array('id'), array(
            'title', 'preferredtitle', 'description', 'shared',
            'url'));

        // Build the tree

        $rss_client->add_child($feeds);
        $feeds->add_child($feed);

        // Define sources

        $rss_client->set_source_array(array((object)array('id' => $this->task->get_blockid())));

        // Only if there are feeds
        if (!empty($config->rssid)) {
            $feed->set_source_sql("
                SELECT *
                  FROM {block_rss_client}
                 WHERE id $in_sql", $in_params);
        }

        // Annotations (none)

        // Return the root element (rss_client), wrapped into standard block structure
        return $this->prepare_block_structure($rss_client);
    }
}
