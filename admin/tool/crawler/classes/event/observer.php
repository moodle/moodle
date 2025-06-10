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
 * Event observers used in tool_crawler
 *
 * @package    tool_crawler
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_crawler\event;

use context;
use core\event\course_module_updated;
use core\event\course_updated;
use tool_crawler\robot\crawler;

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer for tool_crawler.
 */
class observer {
    /**
     * Triggered via course_updated event.
     *
     * @param \core\event\course_updated $event
     */
    public static function course_updated(course_updated $event) {
        global $CFG;
        $crawler = new crawler();
        $localurl = self::get_url_from_contextid($event->contextid);
        return $crawler->mark_for_crawl($CFG->wwwroot, $localurl, $event->objectid, TOOL_CRAWLER_PRIORITY_HIGH);
    }
    /**
     * When a course_module is updated, queue up that page for recrawling again immediately
     *
     * @param \core\event\course_module_updated $event
     * @return boolean
     */
    public static function course_module_updated(course_module_updated $event) {
        global $CFG;
        $crawler = new crawler();
        $localurl = self::get_url_from_contextid($event->contextid);
        return $crawler->mark_for_crawl($CFG->wwwroot, $localurl, null, TOOL_CRAWLER_PRIORITY_HIGH);
    }
    /**
     * Get the local url from a context id
     *
     * @param integer $contextid
     * @return string
     */
    public static function get_url_from_contextid($contextid) {
        $context = context::instance_by_id($contextid, IGNORE_MISSING);
        return $context->get_url()->out_as_local_url();
    }
}
