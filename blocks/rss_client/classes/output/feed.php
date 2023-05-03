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
 * Contains class block_rss_client\output\feed
 *
 * @package   block_rss_client
 * @copyright 2015 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_rss_client\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to help display an RSS Feed
 *
 * @package   block_rss_client
 * @copyright 2015 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feed implements \renderable, \templatable {

    /**
     * The feed's title
     *
     * @var string
     */
    protected $title = null;

    /**
     * An array of renderable feed items
     *
     * @var array
     */
    protected $items = array();

    /**
     * The channel image
     *
     * @var channel_image
     */
    protected $image = null;

    /**
     * Whether or not to show the title
     *
     * @var boolean
     */
    protected $showtitle;

    /**
     * Whether or not to show the channel image
     *
     * @var boolean
     */
    protected $showimage;

    /**
     * Contructor
     *
     * @param string $title The title of the RSS feed
     * @param boolean $showtitle Whether to show the title
     * @param boolean $showimage Whether to show the channel image
     */
    public function __construct($title, $showtitle = true, $showimage = true) {
        $this->title = $title;
        $this->showtitle = $showtitle;
        $this->showimage = $showimage;
    }

    /**
     * Export this for use in a mustache template context.
     *
     * @see templatable::export_for_template()
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $data = array(
            'title' => $this->showtitle ? $this->title : null,
            'image' => null,
            'items' => array(),
        );

        if ($this->showimage && $this->image) {
            $data['image'] = $this->image->export_for_template($output);
        }

        foreach ($this->items as $item) {
            $data['items'][] = $item->export_for_template($output);
        }

        return $data;
    }

    /**
     * Set the feed title
     *
     * @param string $title
     * @return \block_rss_client\output\feed
     */
    public function set_title($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the feed title
     *
     * @return string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Add an RSS item
     *
     * @param \block_rss_client\output\item $item
     */
    public function add_item(item $item) {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Set the RSS items
     *
     * @param array $items An array of renderable RSS items
     */
    public function set_items(array $items) {
        $this->items = $items;

        return $this;
    }

    /**
     * Get the RSS items
     *
     * @return array An array of renderable RSS items
     */
    public function get_items() {
        return $this->items;
    }

    /**
     * Set the channel image
     *
     * @param \block_rss_client\output\channel_image $image
     */
    public function set_image(channel_image $image) {
        $this->image = $image;
    }

    /**
     * Get the channel image
     *
     * @return channel_image
     */
    public function get_image() {
        return $this->image;
    }

    /**
     * Set showtitle
     *
     * @param boolean $showtitle
     * @return \block_rss_client\output\feed
     */
    public function set_showtitle($showtitle) {
        $this->showtitle = boolval($showtitle);

        return $this;
    }

    /**
     * Get showtitle
     *
     * @return boolean
     */
    public function get_showtitle() {
        return $this->showtitle;
    }

    /**
     * Set showimage
     *
     * @param boolean $showimage
     * @return \block_rss_client\output\feed
     */
    public function set_showimage($showimage) {
        $this->showimage = boolval($showimage);

        return $this;
    }

    /**
     * Get showimage
     *
     * @return boolean
     */
    public function get_showimage() {
        return $this->showimage;
    }
}
