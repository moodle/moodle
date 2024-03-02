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

use core_external\external_api;
use core_external\external_value;
use core_external\external_format_value;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_function_parameters;
use core_external\external_warnings;

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
     * @return \core_external\external_description
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
     * @return \core_external\external_description
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
                        'descriptionformat' => new external_format_value('description'),
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
     * @return \core_external\external_description
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


    /**
     * Parameters for function get_tagindex_per_area()
     *
     * @return external_function_parameters
     * @since  Moodle 3.7
     */
    public static function get_tagindex_per_area_parameters() {
        return new external_function_parameters(
            array(
                'tagindex' => new external_single_structure(array(
                    'id' => new external_value(PARAM_INT, 'tag id', VALUE_OPTIONAL, 0),
                    'tag' => new external_value(PARAM_TAG, 'tag name', VALUE_OPTIONAL, ''),
                    'tc' => new external_value(PARAM_INT, 'tag collection id', VALUE_OPTIONAL, 0),
                    'ta' => new external_value(PARAM_INT, 'tag area id', VALUE_OPTIONAL, 0),
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
     * Returns the tag index per multiple areas if requested.
     *
     * @param array $params Tag index required information.
     * @throws moodle_exception
     * @since  Moodle 3.7
     */
    public static function get_tagindex_per_area($params) {
        global $CFG, $PAGE;
        // Validate and normalize parameters.
        $tagindex = self::validate_parameters(
            self::get_tagindex_per_area_parameters(), array('tagindex' => $params));
        $params = $tagindex['tagindex'] + array(    // Force defaults.
            'id' => 0,
            'tag' => '',
            'tc' => 0,
            'ta' => 0,
            'excl' => 0,
            'from' => 0,
            'ctx' => 0,
            'rec' => 1,
            'page' => 0,
        );

        if (empty($CFG->usetags)) {
            throw new moodle_exception('tagsaredisabled', 'tag');
        }

        if (!empty($params['tag'])) {
            if (empty($params['tc'])) {
                // Tag name specified but tag collection was not. Try to guess it.
                $tags = core_tag_tag::guess_by_name($params['tag'], '*');
                if (count($tags) > 1) {
                    // It is in more that one collection, do not display.
                    throw new moodle_exception('Tag is in more that one collection, please indicate one.');
                } else if (count($tags) == 1) {
                    $tag = reset($tags);
                }
            } else {
                if (!$tag = core_tag_tag::get_by_name($params['tc'], $params['tag'], '*')) {
                    // Not found in collection.
                    throw new moodle_exception('notagsfound', 'tag');
                }
            }
        } else if (!empty($params['id'])) {
            $tag = core_tag_tag::get($params['id'], '*');
        }

        if (empty($tag)) {
            throw new moodle_exception('notagsfound', 'tag');
        }

        // Login to the course / module if applicable.
        $context = !empty($params['ctx']) ? context::instance_by_id($params['ctx']) : context_system::instance();
        self::validate_context($context);

        $tag = core_tag_tag::get_by_name($params['tc'], $tag->name, '*', MUST_EXIST);
        $tagareas = core_tag_collection::get_areas($params['tc']);
        $tagareaid = $params['ta'];

         $exclusivemode = 0;
        // Find all areas in this collection and their items tagged with this tag.
        if ($tagareaid) {
            $tagareas = array($tagareas[$tagareaid]);
        }
        if (!$tagareaid && count($tagareas) == 1) {
            // Automatically set "exclusive" mode for tag collection with one tag area only.
            $params['excl'] = 1;
        }

        $renderer = $PAGE->get_renderer('core');
        $result = array();
        foreach ($tagareas as $ta) {
            $tagindex = $tag->get_tag_index($ta, $params['excl'], $params['from'], $params['ctx'], $params['rec'], $params['page']);
            if (!empty($tagindex->hascontent)) {
                $result[] = $tagindex->export_for_template($renderer);
            }
        }
        return $result;
    }

    /**
     * Return structure for get_tagindex_per_area
     *
     * @return \core_external\external_description
     * @since  Moodle 3.7
     */
    public static function get_tagindex_per_area_returns() {
        return new external_multiple_structure(
            self::get_tagindex_returns()
        );
    }

    /**
     * Returns description of get_tag_areas() parameters.
     *
     * @return external_function_parameters
     * @since  Moodle 3.7
     */
    public static function get_tag_areas_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Retrieves existing tag areas.
     *
     * @return array an array of warnings and objects containing the plugin information
     * @throws moodle_exception
     * @since  Moodle 3.7
     */
    public static function get_tag_areas() {
        global $CFG, $PAGE;

        if (empty($CFG->usetags)) {
            throw new moodle_exception('tagsaredisabled', 'tag');
        }

        $context = context_system::instance();
        self::validate_context($context);
        $PAGE->set_context($context); // Needed by internal APIs.
        $output = $PAGE->get_renderer('core');

        $areas = core_tag_area::get_areas();
        $exportedareas = array();
        foreach ($areas as $itemtype => $component) {
            foreach ($component as $area) {
                // Move optional fields not part of the DB table to otherdata.
                $locked = false;
                if (isset($area->locked)) {
                    $locked = $area->locked;
                    unset($area->locked);
                }
                $exporter = new \core_tag\external\tag_area_exporter($area, array('locked' => $locked));
                $exportedareas[] = $exporter->export($output);
            }
        }

        return array(
            'areas' => $exportedareas,
            'warnings' => array(),
        );
    }

    /**
     * Returns description of get_tag_areas() result value.
     *
     * @return \core_external\external_description
     * @since  Moodle 3.7
     */
    public static function get_tag_areas_returns() {
        return new external_single_structure(
            array(
                'areas' => new external_multiple_structure(
                    \core_tag\external\tag_area_exporter::get_read_structure()
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of get_tag_collections() parameters.
     *
     * @return external_function_parameters
     * @since  Moodle 3.7
     */
    public static function get_tag_collections_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Retrieves existing tag collections.
     *
     * @return array an array of warnings and tag collections
     * @throws moodle_exception
     * @since  Moodle 3.7
     */
    public static function get_tag_collections() {
        global $CFG, $PAGE;

        if (empty($CFG->usetags)) {
            throw new moodle_exception('tagsaredisabled', 'tag');
        }

        $context = context_system::instance();
        self::validate_context($context);
        $PAGE->set_context($context); // Needed by internal APIs.
        $output = $PAGE->get_renderer('core');

        $collections = core_tag_collection::get_collections();
        $exportedcollections = array();
        foreach ($collections as $collection) {
            $exporter = new \core_tag\external\tag_collection_exporter($collection);
            $exportedcollections[] = $exporter->export($output);
        }

        return array(
            'collections' => $exportedcollections,
            'warnings' => array(),
        );
    }

    /**
     * Returns description of get_tag_collections() result value.
     *
     * @return \core_external\external_description
     * @since  Moodle 3.7
     */
    public static function get_tag_collections_returns() {
        return new external_single_structure(
            array(
                'collections' => new external_multiple_structure(
                    \core_tag\external\tag_collection_exporter::get_read_structure()
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of get_tag_cloud() parameters.
     *
     * @return external_function_parameters
     * @since  Moodle 3.7
     */
    public static function get_tag_cloud_parameters() {
        return new external_function_parameters(
            array(
                'tagcollid' => new external_value(PARAM_INT, 'Tag collection id.', VALUE_DEFAULT, 0),
                'isstandard' => new external_value(PARAM_BOOL, 'Whether to return only standard tags.', VALUE_DEFAULT, false),
                'limit' => new external_value(PARAM_INT, 'Maximum number of tags to retrieve.', VALUE_DEFAULT, 150),
                'sort' => new external_value(PARAM_ALPHA, 'Sort order for display
                    (id, name, rawname, count, flag, isstandard, tagcollid).', VALUE_DEFAULT, 'name'),
                'search' => new external_value(PARAM_RAW, 'Search string.', VALUE_DEFAULT, ''),
                'fromctx' => new external_value(PARAM_INT, 'Context id where this tag cloud is displayed.', VALUE_DEFAULT, 0),
                'ctx' => new external_value(PARAM_INT, 'Only retrieve tag instances in this context.', VALUE_DEFAULT, 0),
                'rec' => new external_value(PARAM_INT, 'Retrieve tag instances in the $ctx context and it\'s children.',
                    VALUE_DEFAULT, 1),
            )
        );
    }

    /**
     * Retrieves a tag cloud for display.
     *
     * @param int $tagcollid tag collection id
     * @param bool $isstandard return only standard tags
     * @param int $limit maximum number of tags to retrieve, tags are sorted by the instance count
     *            descending here regardless of $sort parameter
     * @param string $sort sort order for display, default 'name' - tags will be sorted after they are retrieved
     * @param string $search search string
     * @param int $fromctx context id where this tag cloud is displayed
     * @param int $ctx only retrieve tag instances in this context
     * @param int $rec retrieve tag instances in the $ctx context and it's children (default 1)
     * @return array an array of warnings and tag cloud information and items
     * @throws moodle_exception
     * @since  Moodle 3.7
     */
    public static function get_tag_cloud($tagcollid = 0, $isstandard = false, $limit = 150, $sort = 'name',
            $search = '', $fromctx = 0, $ctx = 0, $rec = 1) {
        global $CFG, $PAGE;

        $params = self::validate_parameters(self::get_tag_cloud_parameters(),
            array(
                'tagcollid' => $tagcollid,
                'isstandard' => $isstandard,
                'limit' => $limit,
                'sort' => $sort,
                'search' => $search,
                'fromctx' => $fromctx,
                'ctx' => $ctx,
                'rec' => $rec,
            )
        );

        if (empty($CFG->usetags)) {
            throw new moodle_exception('tagsaredisabled', 'tag');
        }

        $context = context_system::instance();
        self::validate_context($context);
        $PAGE->set_context($context); // Needed by internal APIs.
        $output = $PAGE->get_renderer('core');

        $tagcloud = core_tag_collection::get_tag_cloud($params['tagcollid'], $params['isstandard'], $params['limit'],
            $params['sort'], $params['search'], $params['fromctx'], $params['ctx'], $params['rec']);

        $result = $tagcloud->export_for_template($output);
        $result->warnings = array();

        return (array) $result;
    }

    /**
     * Returns description of get_tag_cloud() result value.
     *
     * @return \core_external\external_description
     * @since  Moodle 3.7
     */
    public static function get_tag_cloud_returns() {
        return new external_single_structure(
            array(
                'tags' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_TAG, 'Tag name.'),
                            'viewurl' => new external_value(PARAM_RAW, 'URL to view the tag index.'),
                            'flag' => new external_value(PARAM_BOOL, 'Whether the tag is flagged as inappropriate.',
                                VALUE_OPTIONAL),
                            'isstandard' => new external_value(PARAM_BOOL, 'Whether is a standard tag or not.', VALUE_OPTIONAL),
                            'count' => new external_value(PARAM_INT, 'Number of tag instances.', VALUE_OPTIONAL),
                            'size' => new external_value(PARAM_INT, 'Proportional size to display the tag.', VALUE_OPTIONAL),
                        ), 'Tags.'
                    )
                ),
                'tagscount' => new external_value(PARAM_INT, 'Number of tags returned.'),
                'totalcount' => new external_value(PARAM_INT, 'Total count of tags.'),
                'warnings' => new external_warnings(),
            )
        );
    }
}
