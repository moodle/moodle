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

namespace qbank_managecategories\output;

use context;
use qbank_managecategories\question_categories;
use renderable;
use renderer_base;
use templatable;

/**
 * Output component for the Manage category page.
 *
 * This will return the template context for a page containing a tree of categories for each context in the provided
 * question_categories object, with editing controls.
 *
 * @package   qbank_managecategories
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class categories implements renderable, templatable {
    /**
     * Constructor.
     *
     * @param question_categories $categories Question categories for display.
     */
    public function __construct(
        /** @var question_categories $categories Question categories for display. */
        protected question_categories $categories,
    ) {
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        $categories = [];
        foreach ($this->categories->editlists as $contextid => $list) {
            // Get list elements.
            $context = context::instance_by_id($contextid);
            $itemstab = [];
            if (count($list->items)) {
                foreach ($list->items as $item) {
                    $category = new category($item, $context);
                    $itemstab['items'][] = $category->export_for_template($output);
                }
            }
            if (isset($itemstab['items'])) {
                $ctxlvl = "contextlevel" . $list->context->contextlevel;
                $contextname = $list->context->get_context_name();
                $heading = get_string('questioncatsfor', 'question', $contextname);

                // Get categories context.
                $categories[] = [
                    'ctxlvl' => $ctxlvl,
                    'contextid' => $list->context->id,
                    'contextname' => $contextname,
                    'heading' => $heading,
                    'items' => $itemstab['items'],
                    'categoryid' => $list->categoryid,
                ];
            }
        }
        $data = [
            'categoriesrendered' => $categories,
            'contextid' => $this->categories->contextid,
            'cmid' => $this->categories->cmid,
            'courseid' => $this->categories->courseid,
            'showdescriptions' => get_user_preferences('qbank_managecategories_showdescriptions'),
        ];
        return $data;
    }
}
