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
 * Contains class core_tag_external
 *
 * @package    core_tag
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/webservice/externallib.php");

/**
 * Tags-related web services
 *
 * @package    core_tag
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_external extends external_api {

    /**
     * Parameters for function update_tags()
     *
     * @return external_function_parameters
     */
    public static function update_tags_parameters() {
        return new external_function_parameters(
            array(
                'tags' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'tag id'),
                            'rawname' => new external_value(PARAM_RAW, 'tag raw name (may contain capital letters)',
                                    VALUE_OPTIONAL),
                            'description' => new external_value(PARAM_RAW, 'tag description', VALUE_OPTIONAL),
                            'descriptionformat' => new external_value(PARAM_INT, 'tag description format', VALUE_OPTIONAL),
                            'flag' => new external_value(PARAM_INT, 'flag', VALUE_OPTIONAL),
                            'official' => new external_value(PARAM_INT,
                                '(deprecated, use isstandard) whether this flag is standard', VALUE_OPTIONAL),
                            'isstandard' => new external_value(PARAM_INT, 'whether this flag is standard', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    /**
     * Update tags
     *
     * @param array $tags
     */
    public static function update_tags($tags) {
        global $CFG, $PAGE, $DB;

        // Validate and normalize parameters.
        $tags = self::validate_parameters(self::update_tags_parameters(), array('tags' => $tags));

        $systemcontext = context_system::instance();
        $canmanage = has_capability('moodle/tag:manage', $systemcontext);
        $canedit = has_capability('moodle/tag:edit', $systemcontext);
        $warnings = array();

        if (empty($CFG->usetags)) {
            throw new moodle_exception('tagsaredisabled', 'tag');
        }

        $renderer = $PAGE->get_renderer('core');
        foreach ($tags['tags'] as $tag) {
            $tag = (array)$tag;
            if (array_key_exists('rawname', $tag)) {
                $tag['rawname'] = clean_param($tag['rawname'], PARAM_TAG);
                if (empty($tag['rawname'])) {
                    unset($tag['rawname']);
                }
            }
            if (!$canmanage) {
                // User without manage capability can not change any fields except for descriptions.
                $tag = array_intersect_key($tag, array('id' => 1,
                    'description' => 1, 'descriptionformat' => 1));
            }
            if (!$canedit) {
                // User without edit capability can not change description.
                $tag = array_diff_key($tag,
                        array('description' => 1, 'descriptionformat' => 1));
            }
            if (count($tag) <= 1) {
                $warnings[] = array(
                    'item' => $tag['id'],
                    'warningcode' => 'nothingtoupdate',
                    'message' => get_string('nothingtoupdate', 'core_tag')
                );
                continue;
            }
            if (!$tagobject = core_tag_tag::get($tag['id'], '*')) {
                $warnings[] = array(
                    'item' => $tag['id'],
                    'warningcode' => 'tagnotfound',
                    'message' => get_string('tagnotfound', 'error')
                );
                continue;
            }
            // First check if new tag name is allowed.
            if (!empty($tag['rawname']) && ($existing = core_tag_tag::get_by_name($tagobject->tagcollid, $tag['rawname']))) {
                if ($existing->id != $tag['id']) {
                    $warnings[] = array(
                        'item' => $tag['id'],
                        'warningcode' => 'namesalreadybeeingused',
                        'message' => get_string('namesalreadybeeingused', 'core_tag')
                    );
                    continue;
                }
            }
            if (array_key_exists('official', $tag)) {
                // Parameter 'official' deprecated and replaced with 'isstandard'.
                $tag['isstandard'] = $tag['official'] ? 1 : 0;
                unset($tag['official']);
            }
            if (isset($tag['flag'])) {
                if ($tag['flag']) {
                    $tagobject->flag();
                } else {
                    $tagobject->reset_flag();
                }
                unset($tag['flag']);
            }
            unset($tag['id']);
            if (count($tag)) {
                $tagobject->update($tag);
            }
        }
        return array('warnings' => $warnings);
    }

    /**
     * Return structure for update_tag()
     *
     * @return external_description
     */
    public static function update_tags_returns() {
        return new external_single_structure(
            array(
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Parameters for function get_tags()
     *
     * @return external_function_parameters
     */
    public static function get_tags_parameters() {
        return new external_function_parameters(
            array(
                'tags' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'tag id'),
                        )
                    )
                )
            )
        );
    }

    /**
     * Get tags by their ids
     *
     * @param array $tags
     */
    public static function get_tags($tags) {
        global $CFG, $PAGE, $DB;

        // Validate and normalize parameters.
        $tags = self::validate_parameters(self::get_tags_parameters(), array('tags' => $tags));

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        $canmanage = has_capability('moodle/tag:manage', $systemcontext);
        $canedit = has_capability('moodle/tag:edit', $systemcontext);

        $return = array();
        $warnings = array();

        if (empty($CFG->usetags)) {
            throw new moodle_exception('tagsaredisabled', 'tag');
        }

        $renderer = $PAGE->get_renderer('core');
        foreach ($tags['tags'] as $tag) {
            $tag = (array)$tag;
            if (!$tagobject = $DB->get_record('tag', array('id' => $tag['id']))) {
                $warnings[] = array(
                    'item' => $tag['id'],
                    'warningcode' => 'tagnotfound',
                    'message' => get_string('tagnotfound', 'error')
                );
                continue;
            }
            $tagoutput = new \core_tag\output\tag($tagobject);
            // Do not return some information to users without permissions.
            $rv = $tagoutput->export_for_template($renderer);
            if (!$canmanage) {
                if (!$canedit) {
                    unset($rv->isstandard);
                    unset($rv->official);
                }
                unset($rv->flag);
            }
            $return[] = $rv;
        }
        return array('tags' => $return, 'warnings' => $warnings);
    }

    /**
     * Return structure for get_tag()
     *
     * @return external_description
     */
    public static function get_tags_returns() {
        return new external_single_structure(
            array(
                'tags' => new external_multiple_structure( new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'tag id'),
                        'tagcollid' => new external_value(PARAM_INT, 'tag collection id'),
                        'name' => new external_value(PARAM_TAG, 'name'),
                        'rawname' => new external_value(PARAM_RAW, 'tag raw name (may contain capital letters)'),
                        'description' => new external_value(PARAM_RAW, 'tag description'),
                        'descriptionformat' => new external_format_value(PARAM_INT, 'tag description format'),
                        'flag' => new external_value(PARAM_INT, 'flag', VALUE_OPTIONAL),
                        'official' => new external_value(PARAM_INT,
                            'whether this flag is standard (deprecated, use isstandard)', VALUE_OPTIONAL),
                        'isstandard' => new external_value(PARAM_INT, 'whether this flag is standard', VALUE_OPTIONAL),
                        'viewurl' => new external_value(PARAM_URL, 'URL to view'),
                    ), 'information about one tag')
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Parameters for function get_tagindex()
     *
     * @return external_function_parameters
     */
    public static function get_tagindex_parameters() {
        return new external_function_parameters(
            array(
                'tagindex' => new external_single_structure(array(
                    'tag' => new external_value(PARAM_TAG, 'tag name'),
                    'tc' => new external_value(PARAM_INT, 'tag collection id'),
                    'ta' => new external_value(PARAM_INT, 'tag area id'),
                    'excl' => new external_value(PARAM_BOOL, 'exlusive mode for this tag area', VALUE_OPTIONAL, 0),
                    'from' => new external_value(PARAM_INT, 'context id where the link was displayed', VALUE_OPTIONAL, 0),
                    'ctx' => new external_value(PARAM_INT, 'context id where to search for items', VALUE_OPTIONAL, 0),
                    'rec' => new external_value(PARAM_INT, 'search in the context recursive', VALUE_OPTIONAL, 1),
                    'page' => new external_value(PARAM_INT, 'page number (0-based)', VALUE_OPTIONAL, 0),
                ), 'parameters')
            )
        );
    }

    /**
     * Get tags by their ids
     *
     * @param array $params
     */
    public static function get_tagindex($params) {
        global $PAGE;
        // Validate and normalize parameters.
        $tagindex = self::validate_parameters(
                self::get_tagindex_parameters(), array('tagindex' => $params));
        $params = $tagindex['tagindex'] + array(
            'excl' => 0,
            'from' => 0,
            'ctx' => 0,
            'rec' => 1,
            'page' => 0
        );

        // Login to the course / module if applicable.
        $context = $params['ctx'] ? context::instance_by_id($params['ctx']) : context_system::instance();
        self::validate_context($context);

        $tag = core_tag_tag::get_by_name($params['tc'], $params['tag'], '*', MUST_EXIST);
        $tagareas = core_tag_collection::get_areas($params['tc']);
        $tagindex = $tag->get_tag_index($tagareas[$params['ta']], $params['excl'], $params['from'],
                $params['ctx'], $params['rec'], $params['page']);
        $renderer = $PAGE->get_renderer('core');
        return $tagindex->export_for_template($renderer);
    }

    /**
     * Return structure for get_tag()
     *
     * @return external_description
     */
    public static function get_tagindex_returns() {
        return new external_single_structure(
            array(
                'tagid' => new external_value(PARAM_INT, 'tag id'),
                'ta' => new external_value(PARAM_INT, 'tag area id'),
                'component' => new external_value(PARAM_COMPONENT, 'component'),
                'itemtype' => new external_value(PARAM_NOTAGS, 'itemtype'),
                'nextpageurl' => new external_value(PARAM_URL, 'URL for the next page', VALUE_OPTIONAL),
                'prevpageurl' => new external_value(PARAM_URL, 'URL for the next page', VALUE_OPTIONAL),
                'exclusiveurl' => new external_value(PARAM_URL, 'URL for exclusive link', VALUE_OPTIONAL),
                'exclusivetext' => new external_value(PARAM_TEXT, 'text for exclusive link', VALUE_OPTIONAL),
                'title' => new external_value(PARAM_RAW, 'title'),
                'content' => new external_value(PARAM_RAW, 'title'),
                'hascontent' => new external_value(PARAM_INT, 'whether the content is present'),
                'anchor' => new external_value(PARAM_TEXT, 'name of anchor', VALUE_OPTIONAL),
            ), 'tag index'
        );
    }
}
