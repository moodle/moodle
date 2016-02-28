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
                            'official' => new external_value(PARAM_INT, 'whether this flag is official', VALUE_OPTIONAL),
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
        require_once($CFG->dirroot.'/tag/lib.php');

        // Validate and normalize parameters.
        $tags = self::validate_parameters(self::update_tags_parameters(), array('tags' => $tags));

        $systemcontext = context_system::instance();
        $canmanage = has_capability('moodle/tag:manage', $systemcontext);
        $canedit = has_capability('moodle/tag:edit', $systemcontext);
        $warnings = array();

        if (empty($CFG->usetags)) {
            throw new moodle_exception('tagsaredisabled', 'tag');
        }

        $PAGE->set_context(null); // Ensure page context is set.
        $renderer = $PAGE->get_renderer('core');
        foreach ($tags['tags'] as $tag) {
            $tag = (array)$tag;
            if (array_key_exists('rawname', $tag)) {
                $tag['rawname'] = clean_param($tag['rawname'], PARAM_TAG);
                if (empty($tag['rawname'])) {
                    unset($tag['rawname']);
                } else {
                    $tag['name'] = core_text::strtolower($tag['rawname']);
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
            if (!$tagobject = $DB->get_record('tag', array('id' => $tag['id']))) {
                $warnings[] = array(
                    'item' => $tag['id'],
                    'warningcode' => 'tagnotfound',
                    'message' => get_string('tagnotfound', 'error')
                );
                continue;
            }
            // First check if new tag name is allowed.
            if (!empty($tag['name']) && ($existing = $DB->get_record('tag', array('name' => $tag['name']), 'id'))) {
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
                $tag['tagtype'] = $tag['official'] ? 'official' : 'default';
                unset($tag['official']);
            }
            $tag['timemodified'] = time();
            $DB->update_record('tag', $tag);

            foreach ($tag as $key => $value) {
                $tagobject->$key = $value;
            }

            $event = \core\event\tag_updated::create(array(
                'objectid' => $tagobject->id,
                'relateduserid' => $tagobject->userid,
                'context' => context_system::instance(),
                'other' => array(
                    'name' => $tagobject->name,
                    'rawname' => $tagobject->rawname
                )
            ));
            $event->trigger();
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
        require_once($CFG->dirroot.'/tag/lib.php');

        // Validate and normalize parameters.
        $tags = self::validate_parameters(self::get_tags_parameters(), array('tags' => $tags));

        require_login(null, false, null, false, true);

        $systemcontext = context_system::instance();
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
                    unset($rv->official);
                }
                unset($rv->flag);
                unset($rv->changetypeurl);
                unset($rv->changeflagurl);
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
                        'name' => new external_value(PARAM_TAG, 'name'),
                        'rawname' => new external_value(PARAM_RAW, 'tag raw name (may contain capital letters)'),
                        'description' => new external_value(PARAM_RAW, 'tag description'),
                        'descriptionformat' => new external_format_value(PARAM_INT, 'tag description format'),
                        'flag' => new external_value(PARAM_INT, 'flag', VALUE_OPTIONAL),
                        'official' => new external_value(PARAM_INT, 'whether this flag is official', VALUE_OPTIONAL),
                        'viewurl' => new external_value(PARAM_URL, 'URL to view'),
                        'changetypeurl' => new external_value(PARAM_URL, 'URL to change type (official or not)', VALUE_OPTIONAL),
                        'changeflagurl' => new external_value(PARAM_URL, 'URL to set or reset flag', VALUE_OPTIONAL),
                    ), 'information about one tag')
                ),
                'warnings' => new external_warnings()
            )
        );
    }
}
