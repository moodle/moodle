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
 * Contains class block_rss_client\output\block
 *
 * @package   block_rss_client
 * @copyright 2015 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_rss_client\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to help display an RSS Feeds block
 *
 * @package   block_rss_client
 * @copyright 2016 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block implements \renderable, \templatable {

    /**
     * An array of renderable feeds
     *
     * @var array
     */
    protected $feeds;

    /**
     * Contruct
     *
     * @param array $feeds An array of renderable feeds
     */
    public function __construct(array $feeds = array()) {
        $this->feeds = $feeds;
    }

    /**
     * Prepare data for use in a template
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $data = array('feeds' => array());

        foreach ($this->feeds as $feed) {
            $data['feeds'][] = $feed->export_for_template($output);
        }

        return $data;
    }

    /**
     * Add a feed
     *
     * @param \block_rss_client\output\feed $feed
     * @return \block_rss_client\output\block
     */
    public function add_feed(feed $feed) {
        $this->feeds[] = $feed;

        return $this;
    }

    /**
     * Set the feeds
     *
     * @param array $feeds
     * @return \block_rss_client\output\block
     */
    public function set_feeds(array $feeds) {
        $this->feeds = $feeds;

        return $this;
    }

    /**
     * Get feeds
     *
     * @return array
     */
    public function get_feeds() {
        return $this->feeds;
    }
}
