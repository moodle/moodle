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

namespace qbank_managecategories;

use context;
use core\context\module;
use core\exception\coding_exception;
use moodle_url;

/**
 * Builds a tree for categories for rendering the category management page.
 *
 * @package    qbank_managecategories
 * @copyright  2024 Catalyst IT Europe Ltd.
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_categories {
    /**
     * @var moodle_url Object representing url for this page
     */
    public moodle_url $pageurl;

    /**
     * @var ?int cmid.
     */
    public ?int $cmid;

    /**
     * @var ?int courseid.
     */
    public ?int $courseid;

    /**
     * @var ?int The context ID of the current page.
     */
    public ?int $contextid;

    /**
     * @var array An array containing a tree of categories for each context.
     */
    #[\core\attribute\deprecated(
        'editlist',
        5.2,
        'Multiple contexts are no longer supported.',
        'MDL-87264'
    )]
    public array $editlists;

    /**
     * @var \stdClass The tree of categories for the current context.
     */
    public \stdClass $editlist;

    /**
     * Constructor.
     *
     * @param moodle_url $pageurl base URL of the display categories page. Used for redirects.
     * @param context[] $contexts Deprecated since Moodle 5.2, do not use anymore.
     * @param ?int $cmid course module id for the current page.
     * @param ?int $courseid Deprecated since Moodle 5.2, do not use anymore.
     * @param ?int $thiscontext Deprecated since Moodle 5.2, do not use anymore.
     */
    public function __construct(
        moodle_url $pageurl,
        ?array $contexts = null,
        ?int $cmid = null,
        ?int $courseid = null,
        ?int $thiscontext = null,
    ) {
        global $DB;
        if (!is_null($contexts)) {
            debugging(
                'The contexts argument has been deprecated. Multiple contexts are no longer supported. Please remove the '
                    . 'argument from your calls.',
                DEBUG_DEVELOPER,
            );
        }
        if (!is_null($courseid)) {
            debugging(
                'The courseid argument has been deprecated. The course will be found from the cmid. Please remove the '
                    . 'argument from your calls.',
                DEBUG_DEVELOPER,
            );
        }
        if (!is_null($thiscontext)) {
            debugging(
                'The thiscontext argument has been deprecated. The context will be found from the cmid. Please remove the '
                    . 'argument from your calls.',
                DEBUG_DEVELOPER,
            );
        }

        $this->cmid = $cmid;
        $context = module::instance($this->cmid);

        [$course] = get_course_and_cm_from_cmid($cmid);
        $this->courseid = $course->id;

        $this->pageurl = $pageurl;
        $this->contextid = $context->id;

        $topcategory = $DB->get_record(
            'question_categories',
            ['parent' => 0, 'contextid' => $this->contextid],
        );

        $items = helper::get_categories_for_contexts($context->id);
        // Create an ordered tree with children correctly nested under parents.
        foreach ($items as $item) {
            if (array_key_exists((int) $item->parent, $items)) {
                $item->parentitem = $items[$item->parent];
                $items[$item->parent]->children[$item->id] = $item;
            }
        }
        foreach ($items as $item) {
            if (isset($item->children)) {
                foreach ($item->children as $children) {
                    unset($items[$children->id]);
                }
            }
        }
        $this->editlist = (object) [
            'items' => $items,
            'context' => $context,
            'categoryid' => $topcategory->id,
        ];
    }
}
