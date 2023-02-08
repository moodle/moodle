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
 * Wiki module external API.
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/wiki/lib.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');

/**
 * Wiki module external functions.
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class mod_wiki_external extends external_api {

    /**
     * Describes the parameters for get_wikis_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_wikis_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course ID'), 'Array of course ids.', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of wikis in a provided list of courses,
     * if no list is provided all wikis that the user can view will be returned.
     *
     * @param array $courseids The courses IDs.
     * @return array Containing a list of warnings and a list of wikis.
     * @since Moodle 3.1
     */
    public static function get_wikis_by_courses($courseids = array()) {

        $returnedwikis = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_wikis_by_courses_parameters(), array('courseids' => $courseids));

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the wikis in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $wikis = get_all_instances_in_courses('wiki', $courses);

            foreach ($wikis as $wiki) {

                $context = context_module::instance($wiki->coursemodule);

                // Entry to return.
                $module = array();

                // First, we return information that any user can see in (or can deduce from) the web interface.
                $module['id'] = $wiki->id;
                $module['coursemodule'] = $wiki->coursemodule;
                $module['course'] = $wiki->course;
                $module['name']  = external_format_string($wiki->name, $context->id);

                $viewablefields = [];
                if (has_capability('mod/wiki:viewpage', $context)) {
                    $options = array('noclean' => true);
                    list($module['intro'], $module['introformat']) =
                        external_format_text($wiki->intro, $wiki->introformat, $context->id, 'mod_wiki', 'intro', null, $options);
                    $module['introfiles'] = external_util::get_area_files($context->id, 'mod_wiki', 'intro', false, false);

                    $viewablefields = array('firstpagetitle', 'wikimode', 'defaultformat', 'forceformat', 'editbegin', 'editend',
                                            'section', 'visible', 'groupmode', 'groupingid');
                }

                // Check additional permissions for returning optional private settings.
                if (has_capability('moodle/course:manageactivities', $context)) {
                    $additionalfields = array('timecreated', 'timemodified');
                    $viewablefields = array_merge($viewablefields, $additionalfields);
                }

                foreach ($viewablefields as $field) {
                    $module[$field] = $wiki->{$field};
                }

                // Check if user can add new pages.
                $module['cancreatepages'] = wiki_can_create_pages($context);

                $returnedwikis[] = $module;
            }
        }

        $result = array();
        $result['wikis'] = $returnedwikis;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_wikis_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_wikis_by_courses_returns() {

        return new external_single_structure(
            array(
                'wikis' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Wiki ID.'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module ID.'),
                            'course' => new external_value(PARAM_INT, 'Course ID.'),
                            'name' => new external_value(PARAM_RAW, 'Wiki name.'),
                            'intro' => new external_value(PARAM_RAW, 'Wiki intro.', VALUE_OPTIONAL),
                            'introformat' => new external_format_value('Wiki intro format.', VALUE_OPTIONAL),
                            'introfiles' => new external_files('Files in the introduction text', VALUE_OPTIONAL),
                            'timecreated' => new external_value(PARAM_INT, 'Time of creation.', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'Time of last modification.', VALUE_OPTIONAL),
                            'firstpagetitle' => new external_value(PARAM_RAW, 'First page title.', VALUE_OPTIONAL),
                            'wikimode' => new external_value(PARAM_TEXT, 'Wiki mode (individual, collaborative).', VALUE_OPTIONAL),
                            'defaultformat' => new external_value(PARAM_TEXT, 'Wiki\'s default format (html, creole, nwiki).',
                                                                            VALUE_OPTIONAL),
                            'forceformat' => new external_value(PARAM_INT, '1 if format is forced, 0 otherwise.',
                                                                            VALUE_OPTIONAL),
                            'editbegin' => new external_value(PARAM_INT, 'Edit begin.', VALUE_OPTIONAL),
                            'editend' => new external_value(PARAM_INT, 'Edit end.', VALUE_OPTIONAL),
                            'section' => new external_value(PARAM_INT, 'Course section ID.', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT, '1 if visible, 0 otherwise.', VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode.', VALUE_OPTIONAL),
                            'groupingid' => new external_value(PARAM_INT, 'Group ID.', VALUE_OPTIONAL),
                            'cancreatepages' => new external_value(PARAM_BOOL, 'True if user can create pages.'),
                        ), 'Wikis'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for view_wiki.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function view_wiki_parameters() {
        return new external_function_parameters (
            array(
                'wikiid' => new external_value(PARAM_INT, 'Wiki instance ID.')
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $wikiid The wiki instance ID.
     * @return array of warnings and status result.
     * @since Moodle 3.1
     */
    public static function view_wiki($wikiid) {

        $params = self::validate_parameters(self::view_wiki_parameters(),
                                            array(
                                                'wikiid' => $wikiid
                                            ));
        $warnings = array();

        // Get wiki instance.
        if (!$wiki = wiki_get_wiki($params['wikiid'])) {
            throw new moodle_exception('incorrectwikiid', 'wiki');
        }

        // Permission validation.
        list($course, $cm) = get_course_and_cm_from_instance($wiki, 'wiki');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Check if user can view this wiki.
        // We don't use wiki_user_can_view because it requires to have a valid subwiki for the user.
        if (!has_capability('mod/wiki:viewpage', $context)) {
            throw new moodle_exception('cannotviewpage', 'wiki');
        }

        // Trigger course_module_viewed event and completion.
        wiki_view($wiki, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the view_wiki return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function view_wiki_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true if success.'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for view_page.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function view_page_parameters() {
        return new external_function_parameters (
            array(
                'pageid' => new external_value(PARAM_INT, 'Wiki page ID.'),
            )
        );
    }

    /**
     * Trigger the page viewed event and update the module completion status.
     *
     * @param int $pageid The page ID.
     * @return array of warnings and status result.
     * @since Moodle 3.1
     * @throws moodle_exception if page is not valid.
     */
    public static function view_page($pageid) {

        $params = self::validate_parameters(self::view_page_parameters(),
                                            array(
                                                'pageid' => $pageid
                                            ));
        $warnings = array();

        // Get wiki page.
        if (!$page = wiki_get_page($params['pageid'])) {
            throw new moodle_exception('incorrectpageid', 'wiki');
        }

        // Get wiki instance.
        if (!$wiki = wiki_get_wiki_from_pageid($params['pageid'])) {
            throw new moodle_exception('incorrectwikiid', 'wiki');
        }

        // Permission validation.
        list($course, $cm) = get_course_and_cm_from_instance($wiki, 'wiki');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Check if user can view this wiki.
        if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
            throw new moodle_exception('incorrectsubwikiid', 'wiki');
        }
        if (!wiki_user_can_view($subwiki, $wiki)) {
            throw new moodle_exception('cannotviewpage', 'wiki');
        }

        // Trigger page_viewed event and completion.
        wiki_page_view($wiki, $page, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the view_page return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function view_page_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true if success.'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_subwikis.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_subwikis_parameters() {
        return new external_function_parameters (
            array(
                'wikiid' => new external_value(PARAM_INT, 'Wiki instance ID.')
            )
        );
    }

    /**
     * Returns the list of subwikis the user can see in a specific wiki.
     *
     * @param int $wikiid The wiki instance ID.
     * @return array Containing a list of warnings and a list of subwikis.
     * @since Moodle 3.1
     */
    public static function get_subwikis($wikiid) {
        global $USER;

        $warnings = array();

        $params = self::validate_parameters(self::get_subwikis_parameters(), array('wikiid' => $wikiid));

        // Get wiki instance.
        if (!$wiki = wiki_get_wiki($params['wikiid'])) {
            throw new moodle_exception('incorrectwikiid', 'wiki');
        }

        // Validate context and capabilities.
        list($course, $cm) = get_course_and_cm_from_instance($wiki, 'wiki');
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/wiki:viewpage', $context);

        $returnedsubwikis = wiki_get_visible_subwikis($wiki, $cm, $context);
        foreach ($returnedsubwikis as $subwiki) {
            $subwiki->canedit = wiki_user_can_edit($subwiki);
        }

        $result = array();
        $result['subwikis'] = $returnedsubwikis;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_subwikis return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_subwikis_returns() {
        return new external_single_structure(
            array(
                'subwikis' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Subwiki ID.'),
                            'wikiid' => new external_value(PARAM_INT, 'Wiki ID.'),
                            'groupid' => new external_value(PARAM_RAW, 'Group ID.'),
                            'userid' => new external_value(PARAM_INT, 'User ID.'),
                            'canedit' => new external_value(PARAM_BOOL, 'True if user can edit the subwiki.'),
                        ), 'Subwikis'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_subwiki_pages.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_subwiki_pages_parameters() {
        return new external_function_parameters (
            array(
                'wikiid' => new external_value(PARAM_INT, 'Wiki instance ID.'),
                'groupid' => new external_value(PARAM_INT, 'Subwiki\'s group ID, -1 means current group. It will be ignored'
                                        . ' if the wiki doesn\'t use groups.', VALUE_DEFAULT, -1),
                'userid' => new external_value(PARAM_INT, 'Subwiki\'s user ID, 0 means current user. It will be ignored'
                                        .' in collaborative wikis.', VALUE_DEFAULT, 0),
                'options' => new external_single_structure(
                            array(
                                    'sortby' => new external_value(PARAM_ALPHA,
                                            'Field to sort by (id, title, ...).', VALUE_DEFAULT, 'title'),
                                    'sortdirection' => new external_value(PARAM_ALPHA,
                                            'Sort direction: ASC or DESC.', VALUE_DEFAULT, 'ASC'),
                                    'includecontent' => new external_value(PARAM_INT,
                                            'Include each page contents or just the contents size.', VALUE_DEFAULT, 1),
                            ), 'Options', VALUE_DEFAULT, array()),
            )
        );
    }

    /**
     * Returns the list of pages from a specific subwiki.
     *
     * @param int $wikiid The wiki instance ID.
     * @param int $groupid The group ID. If not defined, use current group.
     * @param int $userid The user ID. If not defined, use current user.
     * @param array $options Several options like sort by, sort direction, ...
     * @return array Containing a list of warnings and a list of pages.
     * @since Moodle 3.1
     */
    public static function get_subwiki_pages($wikiid, $groupid = -1, $userid = 0, $options = array()) {

        $returnedpages = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_subwiki_pages_parameters(),
                                            array(
                                                'wikiid' => $wikiid,
                                                'groupid' => $groupid,
                                                'userid' => $userid,
                                                'options' => $options
                                                )
            );

        // Get wiki instance.
        if (!$wiki = wiki_get_wiki($params['wikiid'])) {
            throw new moodle_exception('incorrectwikiid', 'wiki');
        }
        list($course, $cm) = get_course_and_cm_from_instance($wiki, 'wiki');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Determine groupid and userid to use.
        list($groupid, $userid) = self::determine_group_and_user($cm, $wiki, $params['groupid'], $params['userid']);

        // Get subwiki and validate it.
        $subwiki = wiki_get_subwiki_by_group_and_user_with_validation($wiki, $groupid, $userid);

        if ($subwiki === false) {
            throw new moodle_exception('cannotviewpage', 'wiki');
        } else if ($subwiki->id != -1) {

            $options = $params['options'];

            // Set sort param.
            $sort = get_safe_orderby([
                'id' => 'id',
                'title' => 'title',
                'timecreated' => 'timecreated',
                'timemodified' => 'timemodified',
                'pageviews' => 'pageviews',
                'default' => 'title',
            ], $options['sortby'], $options['sortdirection'], false);

            $pages = wiki_get_page_list($subwiki->id, $sort);
            $caneditpages = wiki_user_can_edit($subwiki);
            $firstpage = wiki_get_first_page($subwiki->id);

            foreach ($pages as $page) {
                $retpage = array(
                        'id' => $page->id,
                        'subwikiid' => $page->subwikiid,
                        'title' => external_format_string($page->title, $context->id),
                        'timecreated' => $page->timecreated,
                        'timemodified' => $page->timemodified,
                        'timerendered' => $page->timerendered,
                        'userid' => $page->userid,
                        'pageviews' => $page->pageviews,
                        'readonly' => $page->readonly,
                        'caneditpage' => $caneditpages,
                        'firstpage' => $page->id == $firstpage->id,
                        'tags' => \core_tag\external\util::get_item_tags('mod_wiki', 'wiki_pages', $page->id),
                    );

                // Refresh page cached content if needed.
                if ($page->timerendered + WIKI_REFRESH_CACHE_TIME < time()) {
                    if ($content = wiki_refresh_cachedcontent($page)) {
                        $page = $content['page'];
                    }
                }
                list($cachedcontent, $contentformat) = external_format_text(
                            $page->cachedcontent, FORMAT_HTML, $context->id, 'mod_wiki', 'attachments', $subwiki->id);

                if ($options['includecontent']) {
                    // Return the page content.
                    $retpage['cachedcontent'] = $cachedcontent;
                    $retpage['contentformat'] = $contentformat;
                } else {
                    // Return the size of the content.
                    $retpage['contentsize'] = strlen($cachedcontent);
                    // TODO: Remove this block of code once PHP 8.0 is the min version supported.
                    // For PHP < 8.0, if strlen() was overloaded, calculate
                    // the bytes using mb_strlen(..., '8bit').
                    if (PHP_VERSION_ID < 80000) {
                        if (function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2)) {
                            $retpage['contentsize'] = mb_strlen($cachedcontent, '8bit');
                        }
                    }
                }

                $returnedpages[] = $retpage;
            }
        }

        $result = array();
        $result['pages'] = $returnedpages;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_subwiki_pages return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_subwiki_pages_returns() {

        return new external_single_structure(
            array(
                'pages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Page ID.'),
                            'subwikiid' => new external_value(PARAM_INT, 'Page\'s subwiki ID.'),
                            'title' => new external_value(PARAM_RAW, 'Page title.'),
                            'timecreated' => new external_value(PARAM_INT, 'Time of creation.'),
                            'timemodified' => new external_value(PARAM_INT, 'Time of last modification.'),
                            'timerendered' => new external_value(PARAM_INT, 'Time of last renderization.'),
                            'userid' => new external_value(PARAM_INT, 'ID of the user that last modified the page.'),
                            'pageviews' => new external_value(PARAM_INT, 'Number of times the page has been viewed.'),
                            'readonly' => new external_value(PARAM_INT, '1 if readonly, 0 otherwise.'),
                            'caneditpage' => new external_value(PARAM_BOOL, 'True if user can edit the page.'),
                            'firstpage' => new external_value(PARAM_BOOL, 'True if it\'s the first page.'),
                            'cachedcontent' => new external_value(PARAM_RAW, 'Page contents.', VALUE_OPTIONAL),
                            'contentformat' => new external_format_value('cachedcontent', VALUE_OPTIONAL),
                            'contentsize' => new external_value(PARAM_INT, 'Size of page contents in bytes (doesn\'t include'.
                                                                            ' size of attached files).', VALUE_OPTIONAL),
                            'tags' => new external_multiple_structure(
                                \core_tag\external\tag_item_exporter::get_read_structure(), 'Tags', VALUE_OPTIONAL
                            ),
                        ), 'Pages'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_page_contents.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_page_contents_parameters() {
        return new external_function_parameters (
            array(
                'pageid' => new external_value(PARAM_INT, 'Page ID.')
            )
        );
    }

    /**
     * Get a page contents.
     *
     * @param int $pageid The page ID.
     * @return array of warnings and page data.
     * @since Moodle 3.1
     */
    public static function get_page_contents($pageid) {

        $params = self::validate_parameters(self::get_page_contents_parameters(),
                                            array(
                                                'pageid' => $pageid
                                            )
            );
        $warnings = array();

        // Get wiki page.
        if (!$page = wiki_get_page($params['pageid'])) {
            throw new moodle_exception('incorrectpageid', 'wiki');
        }

        // Get wiki instance.
        if (!$wiki = wiki_get_wiki_from_pageid($params['pageid'])) {
            throw new moodle_exception('incorrectwikiid', 'wiki');
        }

        // Permission validation.
        $cm = get_coursemodule_from_instance('wiki', $wiki->id, $wiki->course);
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Check if user can view this wiki.
        if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
            throw new moodle_exception('incorrectsubwikiid', 'wiki');
        }
        if (!wiki_user_can_view($subwiki, $wiki)) {
            throw new moodle_exception('cannotviewpage', 'wiki');
        }

        $returnedpage = array();
        $returnedpage['id'] = $page->id;
        $returnedpage['wikiid'] = $wiki->id;
        $returnedpage['subwikiid'] = $page->subwikiid;
        $returnedpage['groupid'] = $subwiki->groupid;
        $returnedpage['userid'] = $subwiki->userid;
        $returnedpage['title'] = $page->title;
        $returnedpage['tags'] = \core_tag\external\util::get_item_tags('mod_wiki', 'wiki_pages', $page->id);

        // Refresh page cached content if needed.
        if ($page->timerendered + WIKI_REFRESH_CACHE_TIME < time()) {
            if ($content = wiki_refresh_cachedcontent($page)) {
                $page = $content['page'];
            }
        }

        list($returnedpage['cachedcontent'], $returnedpage['contentformat']) = external_format_text(
                            $page->cachedcontent, FORMAT_HTML, $context->id, 'mod_wiki', 'attachments', $subwiki->id);
        $returnedpage['caneditpage'] = wiki_user_can_edit($subwiki);

        // Get page version.
        $version = wiki_get_current_version($page->id);
        if (!empty($version)) {
            $returnedpage['version'] = $version->version;
        }

        $result = array();
        $result['page'] = $returnedpage;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_page_contents return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_page_contents_returns() {
        return new external_single_structure(
            array(
                'page' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'Page ID.'),
                        'wikiid' => new external_value(PARAM_INT, 'Page\'s wiki ID.'),
                        'subwikiid' => new external_value(PARAM_INT, 'Page\'s subwiki ID.'),
                        'groupid' => new external_value(PARAM_INT, 'Page\'s group ID.'),
                        'userid' => new external_value(PARAM_INT, 'Page\'s user ID.'),
                        'title' => new external_value(PARAM_RAW, 'Page title.'),
                        'cachedcontent' => new external_value(PARAM_RAW, 'Page contents.'),
                        'contentformat' => new external_format_value('cachedcontent', VALUE_OPTIONAL),
                        'caneditpage' => new external_value(PARAM_BOOL, 'True if user can edit the page.'),
                        'version' => new external_value(PARAM_INT, 'Latest version of the page.', VALUE_OPTIONAL),
                        'tags' => new external_multiple_structure(
                            \core_tag\external\tag_item_exporter::get_read_structure(), 'Tags', VALUE_OPTIONAL
                        ),
                    ), 'Page'
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_subwiki_files.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_subwiki_files_parameters() {
        return new external_function_parameters (
            array(
                'wikiid' => new external_value(PARAM_INT, 'Wiki instance ID.'),
                'groupid' => new external_value(PARAM_INT, 'Subwiki\'s group ID, -1 means current group. It will be ignored'
                                        . ' if the wiki doesn\'t use groups.', VALUE_DEFAULT, -1),
                'userid' => new external_value(PARAM_INT, 'Subwiki\'s user ID, 0 means current user. It will be ignored'
                                        .' in collaborative wikis.', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Returns the list of files from a specific subwiki.
     *
     * @param int $wikiid The wiki instance ID.
     * @param int $groupid The group ID. If not defined, use current group.
     * @param int $userid The user ID. If not defined, use current user.
     * @return array Containing a list of warnings and a list of files.
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_subwiki_files($wikiid, $groupid = -1, $userid = 0) {

        $returnedfiles = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_subwiki_files_parameters(),
                                            array(
                                                'wikiid' => $wikiid,
                                                'groupid' => $groupid,
                                                'userid' => $userid
                                                )
            );

        // Get wiki instance.
        if (!$wiki = wiki_get_wiki($params['wikiid'])) {
            throw new moodle_exception('incorrectwikiid', 'wiki');
        }
        list($course, $cm) = get_course_and_cm_from_instance($wiki, 'wiki');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Determine groupid and userid to use.
        list($groupid, $userid) = self::determine_group_and_user($cm, $wiki, $params['groupid'], $params['userid']);

        // Get subwiki and validate it.
        $subwiki = wiki_get_subwiki_by_group_and_user_with_validation($wiki, $groupid, $userid);

        // Get subwiki based on group and user.
        if ($subwiki === false) {
            throw new moodle_exception('cannotviewfiles', 'wiki');
        } else if ($subwiki->id != -1) {
            // The subwiki exists, let's get the files.
            $returnedfiles = external_util::get_area_files($context->id, 'mod_wiki', 'attachments', $subwiki->id);
        }

        $result = array();
        $result['files'] = $returnedfiles;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_subwiki_pages return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_subwiki_files_returns() {

        return new external_single_structure(
            array(
                'files' => new external_files('Files'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Utility function for determining the groupid and userid to use.
     *
     * @param stdClass $cm The course module.
     * @param stdClass $wiki The wiki.
     * @param int $groupid Group ID. If not defined, use current group.
     * @param int $userid User ID. If not defined, use current user.
     * @return array Array containing the courseid and userid.
     * @since  Moodle 3.1
     */
    protected static function determine_group_and_user($cm, $wiki, $groupid = -1, $userid = 0) {
        global $USER;

        $currentgroup = groups_get_activity_group($cm);
        if ($currentgroup === false) {
            // Activity doesn't use groups.
            $groupid = 0;
        } else if ($groupid == -1) {
            // Use current group.
            $groupid = !empty($currentgroup) ? $currentgroup : 0;
        }

        // Determine user.
        if ($wiki->wikimode == 'collaborative') {
            // Collaborative wikis don't use userid in subwikis.
            $userid = 0;
        } else if (empty($userid)) {
            // Use current user.
            $userid = $USER->id;
        }

        return array($groupid, $userid);
    }

    /**
     * Describes the parameters for get_page_for_editing.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_page_for_editing_parameters() {
        return new external_function_parameters (
            array(
                'pageid' => new external_value(PARAM_INT, 'Page ID to edit.'),
                'section' => new external_value(PARAM_RAW, 'Section page title.', VALUE_DEFAULT, null),
                'lockonly' => new external_value(PARAM_BOOL, 'Just renew lock and not return content.', VALUE_DEFAULT, false)
            )
        );
    }

    /**
     * Locks and retrieves info of page-section to be edited.
     *
     * @param int $pageid The page ID.
     * @param string $section Section page title.
     * @param boolean $lockonly If true: Just renew lock and not return content.
     * @return array of warnings and page data.
     * @since Moodle 3.1
     */
    public static function get_page_for_editing($pageid, $section = null, $lockonly = false) {
        global $USER;

        $params = self::validate_parameters(self::get_page_for_editing_parameters(),
                                            array(
                                                'pageid' => $pageid,
                                                'section' => $section,
                                                'lockonly' => $lockonly
                                            )
            );

        $warnings = array();

        // Get wiki page.
        if (!$page = wiki_get_page($params['pageid'])) {
            throw new moodle_exception('incorrectpageid', 'wiki');
        }

        // Get wiki instance.
        if (!$wiki = wiki_get_wiki_from_pageid($params['pageid'])) {
            throw new moodle_exception('incorrectwikiid', 'wiki');
        }

        // Get subwiki instance.
        if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
            throw new moodle_exception('incorrectsubwikiid', 'wiki');
        }

        // Permission validation.
        $cm = get_coursemodule_from_instance('wiki', $wiki->id, $wiki->course);
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        if (!wiki_user_can_edit($subwiki)) {
            throw new moodle_exception('cannoteditpage', 'wiki');
        }

        if (!wiki_set_lock($params['pageid'], $USER->id, $params['section'], true)) {
            throw new moodle_exception('pageislocked', 'wiki');
        }

        $version = wiki_get_current_version($page->id);
        if (empty($version)) {
            throw new moodle_exception('versionerror', 'wiki');
        }

        $pagesection = array();
        $pagesection['version'] = $version->version;

        // Content requested to be returned.
        if (!$lockonly) {
            if (!is_null($params['section'])) {
                $content = wiki_parser_proxy::get_section($version->content, $version->contentformat, $params['section']);
            } else {
                $content = $version->content;
            }

            $pagesection['content'] = $content;
            $pagesection['contentformat'] = $version->contentformat;
        }

        $result = array();
        $result['pagesection'] = $pagesection;
        $result['warnings'] = $warnings;
        return $result;

    }

    /**
     * Describes the get_page_for_editing return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_page_for_editing_returns() {
        return new external_single_structure(
            array(
                'pagesection' => new external_single_structure(
                    array(
                        'content' => new external_value(PARAM_RAW, 'The contents of the page-section to be edited.',
                            VALUE_OPTIONAL),
                        'contentformat' => new external_value(PARAM_TEXT, 'Format of the original content of the page.',
                            VALUE_OPTIONAL),
                        'version' => new external_value(PARAM_INT, 'Latest version of the page.'),
                        'warnings' => new external_warnings()
                    )
                )
            )
        );
    }

    /**
     * Describes the parameters for new_page.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function new_page_parameters() {
        return new external_function_parameters (
            array(
                'title' => new external_value(PARAM_TEXT, 'New page title.'),
                'content' => new external_value(PARAM_RAW, 'Page contents.'),
                'contentformat' => new external_value(PARAM_TEXT, 'Page contents format. If an invalid format is provided, default
                    wiki format is used.', VALUE_DEFAULT, null),
                'subwikiid' => new external_value(PARAM_INT, 'Page\'s subwiki ID.', VALUE_DEFAULT, null),
                'wikiid' => new external_value(PARAM_INT, 'Page\'s wiki ID. Used if subwiki does not exists.', VALUE_DEFAULT,
                    null),
                'userid' => new external_value(PARAM_INT, 'Subwiki\'s user ID. Used if subwiki does not exists.', VALUE_DEFAULT,
                    null),
                'groupid' => new external_value(PARAM_INT, 'Subwiki\'s group ID. Used if subwiki does not exists.', VALUE_DEFAULT,
                    null)
            )
        );
    }

    /**
     * Creates a new page.
     *
     * @param string $title New page title.
     * @param string $content Page contents.
     * @param int $contentformat Page contents format. If an invalid format is provided, default wiki format is used.
     * @param int $subwikiid The Subwiki ID where to store the page.
     * @param int $wikiid Page\'s wiki ID. Used if subwiki does not exists.
     * @param int $userid Subwiki\'s user ID. Used if subwiki does not exists.
     * @param int $groupid Subwiki\'s group ID. Used if subwiki does not exists.
     * @return array of warnings and page data.
     * @since Moodle 3.1
     */
    public static function new_page($title, $content, $contentformat = null, $subwikiid = null, $wikiid = null, $userid = null,
        $groupid = null) {
        global $USER;

        $params = self::validate_parameters(self::new_page_parameters(),
                                            array(
                                                'title' => $title,
                                                'content' => $content,
                                                'contentformat' => $contentformat,
                                                'subwikiid' => $subwikiid,
                                                'wikiid' => $wikiid,
                                                'userid' => $userid,
                                                'groupid' => $groupid
                                            )
            );

        $warnings = array();

        // Get wiki and subwiki instances.
        if (!empty($params['subwikiid'])) {
            if (!$subwiki = wiki_get_subwiki($params['subwikiid'])) {
                throw new moodle_exception('incorrectsubwikiid', 'wiki');
            }

            if (!$wiki = wiki_get_wiki($subwiki->wikiid)) {
                throw new moodle_exception('incorrectwikiid', 'wiki');
            }

            // Permission validation.
            $cm = get_coursemodule_from_instance('wiki', $wiki->id, $wiki->course);
            $context = context_module::instance($cm->id);
            self::validate_context($context);

        } else {
            if (!$wiki = wiki_get_wiki($params['wikiid'])) {
                throw new moodle_exception('incorrectwikiid', 'wiki');
            }

            // Permission validation.
            $cm = get_coursemodule_from_instance('wiki', $wiki->id, $wiki->course);
            $context = context_module::instance($cm->id);
            self::validate_context($context);

            // Determine groupid and userid to use.
            list($groupid, $userid) = self::determine_group_and_user($cm, $wiki, $params['groupid'], $params['userid']);

            // Get subwiki and validate it.
            $subwiki = wiki_get_subwiki_by_group_and_user_with_validation($wiki, $groupid, $userid);

            if ($subwiki === false) {
                // User cannot view page.
                throw new moodle_exception('cannoteditpage', 'wiki');
            } else if ($subwiki->id < 0) {
                // Subwiki needed to check edit permissions.
                if (!wiki_user_can_edit($subwiki)) {
                    throw new moodle_exception('cannoteditpage', 'wiki');
                }

                // Subwiki does not exists and it can be created.
                $swid = wiki_add_subwiki($wiki->id, $groupid, $userid);
                if (!$subwiki = wiki_get_subwiki($swid)) {
                    throw new moodle_exception('incorrectsubwikiid', 'wiki');
                }
            }
        }

        // Subwiki needed to check edit permissions.
        if (!wiki_user_can_edit($subwiki)) {
            throw new moodle_exception('cannoteditpage', 'wiki');
        }

        if ($page = wiki_get_page_by_title($subwiki->id, $params['title'])) {
            throw new moodle_exception('pageexists', 'wiki');
        }

        // Ignore invalid formats and use default instead.
        if (!$params['contentformat'] || $wiki->forceformat) {
            $params['contentformat'] = $wiki->defaultformat;
        } else {
            $formats = wiki_get_formats();
            if (!in_array($params['contentformat'], $formats)) {
                $params['contentformat'] = $wiki->defaultformat;
            }
        }

        $newpageid = wiki_create_page($subwiki->id, $params['title'], $params['contentformat'], $USER->id);

        if (!$page = wiki_get_page($newpageid)) {
            throw new moodle_exception('incorrectpageid', 'wiki');
        }

        // Save content.
        $save = wiki_save_page($page, $params['content'], $USER->id);

        if (!$save) {
            throw new moodle_exception('savingerror', 'wiki');
        }

        $result = array();
        $result['pageid'] = $page->id;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the new_page return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function new_page_returns() {
        return new external_single_structure(
            array(
                'pageid' => new external_value(PARAM_INT, 'New page id.'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for edit_page.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function edit_page_parameters() {
        return new external_function_parameters (
            array(
                'pageid' => new external_value(PARAM_INT, 'Page ID.'),
                'content' => new external_value(PARAM_RAW, 'Page contents.'),
                'section' => new external_value(PARAM_RAW, 'Section page title.', VALUE_DEFAULT, null)
            )
        );
    }

    /**
     * Edit a page contents.
     *
     * @param int $pageid The page ID.
     * @param string $content Page contents.
     * @param int $section Section to be edited.
     * @return array of warnings and page data.
     * @since Moodle 3.1
     */
    public static function edit_page($pageid, $content, $section = null) {
        global $USER;

        $params = self::validate_parameters(self::edit_page_parameters(),
                                            array(
                                                'pageid' => $pageid,
                                                'content' => $content,
                                                'section' => $section
                                            )
            );
        $warnings = array();

        // Get wiki page.
        if (!$page = wiki_get_page($params['pageid'])) {
            throw new moodle_exception('incorrectpageid', 'wiki');
        }

        // Get wiki instance.
        if (!$wiki = wiki_get_wiki_from_pageid($params['pageid'])) {
            throw new moodle_exception('incorrectwikiid', 'wiki');
        }

        // Get subwiki instance.
        if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
            throw new moodle_exception('incorrectsubwikiid', 'wiki');
        }

        // Permission validation.
        $cm = get_coursemodule_from_instance('wiki', $wiki->id, $wiki->course);
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        if (!wiki_user_can_edit($subwiki)) {
            throw new moodle_exception('cannoteditpage', 'wiki');
        }

        if (wiki_is_page_section_locked($page->id, $USER->id, $params['section'])) {
            throw new moodle_exception('pageislocked', 'wiki');
        }

        // Save content.
        if (!is_null($params['section'])) {
            $version = wiki_get_current_version($page->id);
            $content = wiki_parser_proxy::get_section($version->content, $version->contentformat, $params['section'], false);
            if (!$content) {
                throw new moodle_exception('invalidsection', 'wiki');
            }

            $save = wiki_save_section($page, $params['section'], $params['content'], $USER->id);
        } else {
            $save = wiki_save_page($page, $params['content'], $USER->id);
        }

        wiki_delete_locks($page->id, $USER->id, $params['section']);

        if (!$save) {
            throw new moodle_exception('savingerror', 'wiki');
        }

        $result = array();
        $result['pageid'] = $page->id;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the edit_page return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function edit_page_returns() {
        return new external_single_structure(
            array(
                'pageid' => new external_value(PARAM_INT, 'Edited page id.'),
                'warnings' => new external_warnings()
            )
        );
    }

}
