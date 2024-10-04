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

namespace core_blog\external;

use core_external\external_api;
use core_external\external_format_value;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use context_system;
use context_course;
use context_module;
use moodle_exception;

/**
 * This is the external method for updating a blog post entry.
 *
 * @package    core_blog
 * @copyright  2024 Juan Leyva <juan@moodle.com>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_entry extends external_api {

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'entryid' => new external_value(PARAM_INT, 'Blog entry id to update'),
            'subject' => new external_value(PARAM_TEXT, 'Blog subject'),
            'summary' => new external_value(PARAM_RAW, 'Blog post content'),
            'summaryformat' => new external_format_value('summary'),
            'options' => new external_multiple_structure (
                new external_single_structure(
                    [
                        'name' => new external_value(PARAM_ALPHANUM,
                            'The allowed keys (value format) are:
                            inlineattachmentsid (int); the draft file area id for inline attachments. Default to 0.
                            attachmentsid (int); the draft file area id for attachments. Default to 0.
                            publishstate (str); the publish state of the entry (draft, site or public). Default to site.
                            courseassoc (int); the course id to associate the entry with. Default to 0.
                            modassoc (int); the module id to associate the entry with. Default to 0.
                            tags (str); the tags to associate the entry with, comma separated. Default to empty.'),
                        'value' => new external_value(PARAM_RAW, 'the value of the option (validated inside the function)'),
                    ]
                ), 'Optional settings', VALUE_DEFAULT, []
            ),
        ]);
    }

    /**
     * Update the indicated glossary entry.
     *
     * @param  int $entryid The entry to update
     * @param string $subject    the glossary subject
     * @param string $summary the subject summary
     * @param int $summaryformat the subject summary format
     * @param array  $options    additional settings
     * @return array with result and warnings
     * @throws moodle_exception
     */
    public static function execute(int $entryid, string $subject, string $summary, int $summaryformat,
            array $options = []): array {

        global $DB, $CFG;
        require_once($CFG->dirroot . '/blog/lib.php');
        require_once($CFG->dirroot . '/blog/locallib.php');

        $params = self::validate_parameters(self::execute_parameters(), compact('entryid', 'subject', 'summary',
            'summaryformat', 'options'));

        if (empty($CFG->enableblogs)) {
            throw new moodle_exception('blogdisable', 'blog');
        }

        if (!$entry = new \blog_entry($params['entryid'])) {
            throw new moodle_exception('wrongentryid', 'blog');
        }

        if (!blog_user_can_edit_entry($entry)) {
            throw new \moodle_exception('cannoteditentryorblog', 'blog');
        }

        // Prepare the entry object.
        $entrydata = new \stdClass();
        $entrydata->id = $entry->id;
        $entrydata->subject = $params['subject'];
        $entrydata->summary_editor = [
            'text' => $params['summary'],
            'format' => $params['summaryformat'],
        ];
        $entrydata->publishstate = $entry->publishstate;
        $entrydata->courseassoc = $entry->courseassoc;
        $entrydata->modassoc = $entry->modassoc;
        $entrydata->tags = \core_tag_tag::get_item_tags_array('core', 'post', $entry->id);

        // Options.
        foreach ($params['options'] as $option) {
            $name = trim($option['name']);
            switch ($name) {
                case 'inlineattachmentsid':
                    $entrydata->summary_editor['itemid'] = clean_param($option['value'], PARAM_INT);
                    break;
                case 'attachmentsid':
                    $entrydata->attachment_filemanager = clean_param($option['value'], PARAM_INT);
                    break;
                case 'publishstate':
                    $entrydata->publishstate = clean_param($option['value'], PARAM_ALPHA);
                    $applicable = \blog_entry::get_applicable_publish_states();
                    if (empty($applicable[$entrydata->publishstate])) {
                        throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
                    }
                    break;
                case 'courseassoc':
                case 'modassoc':
                    $entrydata->{$name} = clean_param($option['value'], PARAM_INT);
                    if (!$CFG->useblogassociations) {
                        throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
                    }
                    break;
                case 'tags':
                    $entrydata->tags = clean_param($option['value'], PARAM_TAGLIST);
                    // Convert to the expected format.
                    $entrydata->tags = explode(',', $entrydata->tags);
                    break;
                default:
                    throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
            }
        }

        $context = context_system::instance();

        // Validate course association. We need to convert the course id to context.
        if (isset($entrydata->courseassoc)) {
            $entrydata->courseid = $entrydata->courseassoc;

            if (!empty($entrydata->courseid)) {
                $coursecontext = context_course::instance($entrydata->courseassoc);

                $entrydata->courseassoc = $coursecontext->id;   // Convert to context.
                $context = $coursecontext;
            }
        }

        // Validate mod association.
        if (isset($entrydata->modassoc)) {
            $entrydata->coursemoduleid = $entrydata->modassoc;

            if (!empty($entrydata->coursemoduleid)) {
                $modcontext = context_module::instance($entrydata->modassoc);
                if (!empty($coursecontext) && $coursecontext->id != $modcontext->get_course_context(true)->id) {
                    throw new moodle_exception('errorinvalidparam', 'webservice', '', 'modassoc');
                }
                $entrydata->modassoc = $modcontext->id; // Convert to context.
                $context = $modcontext;
            }
        }

        // Validate context. It might be upated because of the new association.
        self::validate_context($context);

        [$summaryoptions, $attachmentoptions] = blog_get_editor_options($entrydata);

        $entry->edit((array) $entrydata, null, $summaryoptions, $attachmentoptions);

        return [
            'status' => true,
            'warnings' => [],
        ];
    }

    /**
     * Return.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'The update result, true if everything went well.'),
            'warnings' => new external_warnings(),
        ]);
    }
}
