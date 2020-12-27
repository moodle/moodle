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
 * This is the external API for blogs.
 *
 * @package    core_blog
 * @copyright  2018 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_blog;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir .'/externallib.php');
require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->dirroot .'/blog/locallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use external_warnings;
use context_system;
use context_course;
use moodle_exception;
use core_blog\external\post_exporter;

/**
 * This is the external API for blogs.
 *
 * @copyright  2018 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Validate access to the blog and the filters to apply when listing entries.
     *
     * @param  array  $rawwsfilters array containing the filters in WS format
     * @return array  context, filters to apply and the calculated courseid and user
     * @since  Moodle 3.6
     */
    protected static function validate_access_and_filters($rawwsfilters) {
        global $CFG;

        if (empty($CFG->enableblogs)) {
            throw new moodle_exception('blogdisable', 'blog');
        }

        // Init filters.
        $filterstype = array(
            'courseid' => PARAM_INT,
            'groupid' => PARAM_INT,
            'userid' => PARAM_INT,
            'tagid' => PARAM_INT,
            'tag' => PARAM_NOTAGS,
            'cmid' => PARAM_INT,
            'entryid' => PARAM_INT,
            'search' => PARAM_RAW
        );
        $filters = array(
            'courseid' => null,
            'groupid' => null,
            'userid' => null,
            'tagid' => null,
            'tag' => null,
            'cmid' => null,
            'entryid' => null,
            'search' => null
        );

        foreach ($rawwsfilters as $filter) {
            $name = trim($filter['name']);
            if (!isset($filterstype[$name])) {
                throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
            }
            $filters[$name] = clean_param($filter['value'], $filterstype[$name]);
        }

        // Do not overwrite here the filters, blog_get_headers and blog_listing will take care of that.
        list($courseid, $userid) = blog_validate_access($filters['courseid'], $filters['cmid'], $filters['groupid'],
            $filters['entryid'], $filters['userid']);

        if ($courseid && $courseid != SITEID) {
            $context = context_course::instance($courseid);
            self::validate_context($context);
        } else {
            $context = context_system::instance();
            if ($CFG->bloglevel == BLOG_GLOBAL_LEVEL) {
                // Everybody can see anything - no login required unless site is locked down using forcelogin.
                if ($CFG->forcelogin) {
                    self::validate_context($context);
                }
            } else {
                self::validate_context($context);
            }
        }
        // Courseid and userid may not be the same that the ones in $filters.
        return array($context, $filters, $courseid, $userid);
    }

    /**
     * Returns description of get_entries() parameters.
     *
     * @return external_function_parameters
     * @since  Moodle 3.6
     */
    public static function get_entries_parameters() {
        return new external_function_parameters(
            array(
                'filters' => new external_multiple_structure (
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHA,
                                'The expected keys (value format) are:
                                tag      PARAM_NOTAGS blog tag
                                tagid    PARAM_INT    blog tag id
                                userid   PARAM_INT    blog author (userid)
                                cmid    PARAM_INT    course module id
                                entryid  PARAM_INT    entry id
                                groupid  PARAM_INT    group id
                                courseid PARAM_INT    course id
                                search   PARAM_RAW    search term
                                '
                            ),
                            'value' => new external_value(PARAM_RAW, 'The value of the filter.')
                        )
                    ), 'Parameters to filter blog listings.', VALUE_DEFAULT, array()
                ),
                'page' => new external_value(PARAM_INT, 'The blog page to return.', VALUE_DEFAULT, 0),
                'perpage' => new external_value(PARAM_INT, 'The number of posts to return per page.', VALUE_DEFAULT, 10),
            )
        );
    }

    /**
     * Return blog entries.
     *
     * @param array $filters the parameters to filter the blog listing
     * @param int $page the blog page to return
     * @param int $perpage the number of posts to return per page
     * @return array with the blog entries and warnings
     * @since  Moodle 3.6
     */
    public static function get_entries($filters = array(), $page = 0, $perpage = 10) {
        global $PAGE;

        $warnings = array();
        $params = self::validate_parameters(self::get_entries_parameters(),
            array('filters' => $filters, 'page' => $page, 'perpage' => $perpage));

        list($context, $filters, $courseid, $userid) = self::validate_access_and_filters($params['filters']);

        $PAGE->set_context($context); // Needed by internal APIs.
        $output = $PAGE->get_renderer('core');

        // Get filters.
        $blogheaders = blog_get_headers($filters['courseid'], $filters['groupid'], $filters['userid'], $filters['tagid'],
            $filters['tag'], $filters['cmid'], $filters['entryid'], $filters['search']);
        $bloglisting = new \blog_listing($blogheaders['filters']);

        $page  = $params['page'];
        $limit = empty($params['perpage']) ? get_user_preferences('blogpagesize', 10) : $params['perpage'];
        $start = $page * $limit;
        $entries = $bloglisting->get_entries($start, $limit);
        $totalentries = $bloglisting->count_entries();

        $exportedentries = array();
        foreach ($entries as $entry) {
            $exporter = new post_exporter($entry, array('context' => $context));
            $exportedentries[] = $exporter->export($output);
        }
        return array(
            'warnings' => $warnings,
            'entries' => $exportedentries,
            'totalentries' => $totalentries,
        );
    }

    /**
     * Returns description of get_entries() result value.
     *
     * @return external_description
     * @since  Moodle 3.6
     */
    public static function get_entries_returns() {
        return new external_single_structure(
            array(
                'entries' => new external_multiple_structure(
                    post_exporter::get_read_structure()
                ),
                'totalentries' => new external_value(PARAM_INT, 'The total number of entries found.'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of view_entries() parameters.
     *
     * @return external_function_parameters
     * @since  Moodle 3.6
     */
    public static function view_entries_parameters() {
        return new external_function_parameters(
            array(
                'filters' => new external_multiple_structure (
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHA,
                                'The expected keys (value format) are:
                                tag      PARAM_NOTAGS blog tag
                                tagid    PARAM_INT    blog tag id
                                userid   PARAM_INT    blog author (userid)
                                cmid     PARAM_INT    course module id
                                entryid  PARAM_INT    entry id
                                groupid  PARAM_INT    group id
                                courseid PARAM_INT    course id
                                search   PARAM_RAW    search term
                                '
                            ),
                            'value' => new external_value(PARAM_RAW, 'The value of the filter.')
                        )
                    ), 'Parameters used in the filter of view_entries.', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Trigger the blog_entries_viewed event.
     *
     * @param array $filters the parameters used in the filter of get_entries
     * @return array with status result and warnings
     * @since  Moodle 3.6
     */
    public static function view_entries($filters = array()) {

        $warnings = array();
        $params = self::validate_parameters(self::view_entries_parameters(), array('filters' => $filters));

        list($context, $filters, $courseid, $userid) = self::validate_access_and_filters($params['filters']);

        $eventparams = array(
            'other' => array('entryid' => $filters['entryid'], 'tagid' => $filters['tagid'], 'userid' => $userid,
                'modid' => $filters['cmid'], 'groupid' => $filters['groupid'], 'search' => $filters['search']
            )
        );
        if (!empty($userid)) {
            $eventparams['relateduserid'] = $userid;
        }
        $eventparams['other']['courseid'] = ($courseid === SITEID) ? 0 : $courseid;
        $event = \core\event\blog_entries_viewed::create($eventparams);
        $event->trigger();

        return array(
            'warnings' => $warnings,
            'status' => true,
        );
    }

    /**
     * Returns description of view_entries() result value.
     *
     * @return external_description
     * @since  Moodle 3.6
     */
    public static function view_entries_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings(),
            )
        );
    }
}
