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
 * Contains class block_rss_client\output\block_renderer_html
 *
 * @package   block_rss_client
 * @copyright 2015 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_rss_client\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for RSS Client block
 *
 * @package   block_rss_client
 * @copyright 2015 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render an RSS Item
     *
     * @param templatable $item
     * @return string|boolean
     */
    public function render_item(\templatable $item) {
        $data = $item->export_for_template($this);

        return $this->render_from_template('block_rss_client/item', $data);
    }

    /**
     * Render an RSS Feed
     *
     * @param templatable $feed
     * @return string|boolean
     */
    public function render_feed(\templatable $feed) {
        $data = $feed->export_for_template($this);

        return $this->render_from_template('block_rss_client/feed', $data);
    }

    /**
     * Render an RSS feeds block
     *
     * @param \templatable $block
     * @return string|boolean
     */
    public function render_block(\templatable $block) {
        $data = $block->export_for_template($this);

        return $this->render_from_template('block_rss_client/block', $data);
    }

    /**
     * Render the block footer
     *
     * @param templatable $footer
     * @return string|boolean
     */
    public function render_footer(\templatable $footer) {
        $data = $footer->export_for_template($this);

        return $this->render_from_template('block_rss_client/footer', $data);
    }

    /**
     * Format a timestamp to use as a published date
     *
     * @param int $timestamp Unix timestamp
     * @return string
     */
    public function format_published_date($timestamp) {
        if (empty($timestamp)) {
            return '';
        } else {
            return strftime(get_string('strftimerecentfull', 'langconfig'), $timestamp);
        }
    }

    /**
     * Format an RSS item title
     *
     * @param string $title
     * @return string
     */
    public function format_title($title) {
        return break_up_long_words($title, 30);
    }

    /**
     * Format an RSS item description
     *
     * @param string $description
     * @return string
     */
    public function format_description($description) {
        $description = format_text($description, FORMAT_HTML, array('para' => false));
        $description = break_up_long_words($description, 30);

        return $description;
    }
}
