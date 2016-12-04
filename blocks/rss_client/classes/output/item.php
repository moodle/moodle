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
 * Class to help display an RSS Item
 *
 * @package   block_rss_client
 * @copyright 2015 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item implements \renderable, \templatable {

    /**
     * The unique id of the item
     *
     * @var string
     */
    protected $id;

    /**
     * The link to the item
     *
     * @var \moodle_url
     */
    protected $link;

    /**
     * The title of the item
     *
     * @var string
     */
    protected $title;

    /**
     * The description of the item
     *
     * @var string
     */
    protected $description;

    /**
     * The item's permalink
     *
     * @var \moodle_url
     */
    protected $permalink;

    /**
     * The publish date of the item in Unix timestamp format
     *
     * @var int
     */
    protected $timestamp;

    /**
     * Whether or not to show the item's description
     *
     * @var string
     */
    protected $showdescription;

    /**
     * Contructor
     *
     * @param string $id The id of the RSS item
     * @param \moodle_url $link The URL of the RSS item
     * @param string $title The title pf the RSS item
     * @param string $description The description of the RSS item
     * @param \moodle_url $permalink The permalink of the RSS item
     * @param int $timestamp The Unix timestamp that represents the published date
     * @param boolean $showdescription Whether or not to show the description
     */
    public function __construct($id, \moodle_url $link, $title, $description, \moodle_url $permalink, $timestamp,
            $showdescription = true) {
        $this->id               = $id;
        $this->link             = $link;
        $this->title            = $title;
        $this->description      = $description;
        $this->permalink        = $permalink;
        $this->timestamp        = $timestamp;
        $this->showdescription  = $showdescription;
    }

    /**
     * Export context for use in mustache templates
     *
     * @see templatable::export_for_template()
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $data = array(
            'id'            => $this->id,
            'permalink'     => clean_param($this->permalink, PARAM_URL),
            'datepublished' => $output->format_published_date($this->timestamp),
            'link'          => clean_param($this->link, PARAM_URL),
        );

        // If the item does not have a title, create one from the description.
        $title = $this->title;
        if (!$title) {
            $title = strip_tags($this->description);
            $title = \core_text::substr($title, 0, 20) . '...';
        }

        // Allow the renderer to format the title and description.
        $data['title']          = $output->format_title($title);
        $data['description']    = $this->showdescription ? $output->format_description($this->description) : null;

        return $data;
    }

    /**
     * Set id
     *
     * @param string $id
     * @return \block_rss_client\output\item
     */
    public function set_id($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Set link
     *
     * @param \moodle_url $link
     * @return \block_rss_client\output\item
     */
    public function set_link(\moodle_url $link) {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return \moodle_url
     */
    public function get_link() {
        return $this->link;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return \block_rss_client\output\item
     */
    public function set_title($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return \block_rss_client\output\item
     */
    public function set_description($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Set permalink
     *
     * @param string $permalink
     * @return \block_rss_client\output\item
     */
    public function set_permalink($permalink) {
        $this->permalink = $permalink;

        return $this;
    }

    /**
     * Get permalink
     *
     * @return string
     */
    public function get_permalink() {
        return $this->permalink;
    }

    /**
     * Set timestamp
     *
     * @param int $timestamp
     * @return \block_rss_client\output\item
     */
    public function set_timestamp($timestamp) {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return string
     */
    public function get_timestamp() {
        return $this->timestamp;
    }

    /**
     * Set showdescription
     *
     * @param boolean $showdescription
     * @return \block_rss_client\output\item
     */
    public function set_showdescription($showdescription) {
        $this->showdescription = boolval($showdescription);

        return $this;
    }

    /**
     * Get showdescription
     *
     * @return boolean
     */
    public function get_showdescription() {
        return $this->showdescription;
    }
}
