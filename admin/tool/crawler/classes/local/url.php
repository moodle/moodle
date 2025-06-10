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
 * Url class model for a url record, CRUD operations and the url table
 *
 * @package    tool_crawler
 * @author     Kristian Ringer <kristianringer@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_crawler\local;

use tool_crawler\robot\crawler;

defined('MOODLE_INTERNAL') || die();

/**
 * url class.
 *
 * @package    tool_crawler
 * @author     Kristian Ringer <kristianringer@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class url extends \core\persistent {
    /** Table name for the persistent. */
    const TABLE = 'tool_crawler_url';
    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'url' => array(
                'type' => PARAM_TEXT,
            ),
            'urlhash' => array(
                'type' => PARAM_TEXT,
            ),
            'externalurl' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'lastcrawled' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'needscrawl' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'httpcode' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'mimetype' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'title' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'downloadduration' => array(
                'type' => PARAM_FLOAT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'filesize' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'filesizestatus' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'redirect' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'courseid' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'contextid' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'cmid' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'ignoreduserid' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'ignoredtime' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'httpmsg' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'errormsg' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'priority' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => '0',
            ),
            'urllevel' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => '2',
            ),
        );
    }

    /**
     * Create the hashed field before inserting or updating a record
     * This runs as the persistent object is created.
     */
    protected function before_validate() {
        $url = $this->get('url');
        $this->set('urlhash', self::hash_url($url));
    }

    /**
     * Reset a node to be recrawled
     *
     * @param integer $nodeid node id
     */
    public static function reset_for_recrawl($nodeid) {

        global $DB;

        if ($node = new url($nodeid)) {

            $time = crawler::get_config()->crawlstart;

            // Mark all nodes that link to this as needing a recrawl.
            if ($DB->get_dbfamily() == 'mysql') {
                $DB->execute("UPDATE {tool_crawler_url} u
                          INNER JOIN {tool_crawler_edge} e ON e.a = u.id
                                 SET needscrawl = ?,
                                     lastcrawled = null,
                                     priority = ?
                               WHERE e.b = ?", [$time, TOOL_CRAWLER_PRIORITY_HIGH, $nodeid]);
            } else {
                $DB->execute("UPDATE {tool_crawler_url} u
                                 SET needscrawl = ?,
                                     lastcrawled = null,
                                     priority = ?
                                FROM {tool_crawler_edge} e
                               WHERE e.a = u.id
                                 AND e.b = ?", [$time, TOOL_CRAWLER_PRIORITY_HIGH, $nodeid]);
            }
            // Delete all edges that point to this node.
            $DB->delete_records('tool_crawler_edge', ['b' => $nodeid]);
            // Delete the 'to' node as it may be completely wrong.
            $DB->delete_records('tool_crawler_url', array('id' => $nodeid) );
        }
    }

    /**
     * Many URLs are in the queue now (more will probably be added)
     *
     * @return int size of queue
     */
    public function get_queue_size() {
        global $DB;

        return $DB->get_field_sql("
                SELECT COUNT(*)
                  FROM {tool_crawler_url}
                 WHERE lastcrawled IS NULL
                    OR lastcrawled < needscrawl");
    }

    /**
     * How many URLs have been processed off the queue
     *
     * @return int size of processes list
     */
    public function get_processed() {
        global $DB;

        return $DB->get_field_sql("
                SELECT COUNT(*)
                  FROM {tool_crawler_url}
                 WHERE lastcrawled >= ?",
                                  array(crawler::get_config()->crawlstart));
    }

    /**
     * Hash a url
     * @param string $url the url to hash
     * @return string the hashed url
     */
    public static function hash_url($url) {
        return sha1($url);
    }
}
