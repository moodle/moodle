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
 * Interface class.
 *
 * @package   mod_dataform
 * @copyright 2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_dataform\interfaces;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface for dataform rss support
 *
 * The interface that is implemented by any dataformview plugin which supports rss.
 * It forces inheriting classes to define methos that are called from the dataform
 * rss lib.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface rss {

    /**
     * Returns content stamp for current content (e.g. composed of included entrt ids).
     *
     * @return null|string Null if there is no new content, or a content stamp string
     */
    public function get_content_stamp();

    /**
     * Returns rss items to publish.
     *
     * @return array of objects {title, descrition, pubdate, link to the entry}
     */
    public function get_rss_items();

    /**
     * Returns the title for the RSS Feed.
     *
     * @return null|string the standard header for the RSS feed
     */
    public function get_rss_header_title();

    /**
     * Returns the link for the origin of the RSS feed.
     *
     * @return null|string the standard header for the RSS feed
     */
    public function get_rss_header_link();

    /**
     * Returns the description of the contents of the RSS feed.
     *
     * @return null|string the standard header for the RSS feed
     */
    public function get_rss_header_description();

}
