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
    public array $editlists;

    /**
     * Constructor.
     *
     * @param moodle_url $pageurl base URL of the display categories page. Used for redirects.
     * @param context[] $contexts contexts where the current user can edit categories.
     * @param ?int $cmid course module id for the current page.
     * @param ?int $courseid course id for the current page.
     * @param ?int $thiscontext The context ID of the current page.
     */
    public function __construct(
        moodle_url $pageurl,
        array $contexts,
        ?int $cmid = null,
        ?int $courseid = null,
        ?int $thiscontext = null,
    ) {
        global $DB;

        $this->cmid = $cmid;
        $this->courseid = $courseid;

        $this->pageurl = $pageurl;
        $this->contextid = $thiscontext;

        $contextids = array_map(fn($context) => $context->id, $contexts);
        [$insql, $params] = $DB->get_in_or_equal($contextids);
        $topcategories = $DB->get_records_select_menu(
            'question_categories',
            'parent = 0 AND contextid ' . $insql,
            $params,
            fields: 'contextid, id'
        );
        foreach ($contexts as $context) {
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
            $this->editlists[$context->id] = (object) [
                'items' => $items,
                'context' => $context,
                'categoryid' => $topcategories[$context->id],
            ];
        }
    }
}
