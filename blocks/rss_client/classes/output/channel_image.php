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
 * Contains class block_rss_client\output\channel_image
 *
 * @package   block_rss_client
 * @copyright 2016 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_rss_client\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to display RSS channel images
 *
 * @package   block_rss_client
 * @copyright 2016 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class channel_image implements \renderable, \templatable {

    /**
     * The URL location of the image
     *
     * @var string
     */
    protected $url;

    /**
     * The title of the image
     *
     * @var string
     */
    protected $title;

    /**
     * The URL of the image link
     *
     * @var string
     */
    protected $link;

    /**
     * Contructor
     *
     * @param \moodle_url $url The URL location of the image
     * @param string $title The title of the image
     * @param \moodle_url $link The URL of the image link
     */
    public function __construct(\moodle_url $url, $title, \moodle_url $link = null) {
        $this->url      = $url;
        $this->title    = $title;
        $this->link     = $link;
    }

    /**
     * Export this for use in a mustache template context.
     *
     * @see templatable::export_for_template()
     * @param renderer_base $output
     * @return array The data for the template
     */
    public function export_for_template(\renderer_base $output) {
        return array(
            'url'   => clean_param($this->url, PARAM_URL),
            'title' => $this->title,
            'link'  => clean_param($this->link, PARAM_URL),
        );
    }

    /**
     * Set the URL
     *
     * @param \moodle_url $url
     * @return \block_rss_client\output\channel_image
     */
    public function set_url(\moodle_url $url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the URL
     *
     * @return \moodle_url
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * Set the title
     *
     * @param string $title
     * @return \block_rss_client\output\channel_image
     */
    public function set_title($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Set the link
     *
     * @param \moodle_url $link
     * @return \block_rss_client\output\channel_image
     */
    public function set_link($link) {
        $this->link = $link;

        return $this;
    }

    /**
     * Get the link
     *
     * @return \moodle_url
     */
    public function get_link() {
        return $this->link;
    }
}
