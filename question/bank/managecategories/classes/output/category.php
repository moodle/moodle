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

use action_menu;
use action_menu_link;
use context;
use core\plugininfo\qbank;
use core_question\category_manager;
use moodle_url;
use pix_icon;
use qbank_managecategories\helper;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Output component for a single category
 *
 * @package   qbank_managecategories
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category implements renderable, templatable {
    /**
     * @var bool Can this category be reordered?
     */
    protected bool $canreorder;

    /**
     * Constructor
     *
     * @param stdClass $category The record of category we are rendering
     * @param context $context The context the category belongs to.
     * @param int $cmid The cmid of the course module context the category belongs to (optional).
     * @param int $courseid The course ID of the course context the category belongs to (optional).
     */
    public function __construct(
        /** @var stdClass $category The record of category we are rendering */
        protected stdClass $category,
        /** @var context $context The context the category belongs to. */
        protected context $context,
        /** @var int $cmid The cmid of the course module context the category belongs to. */
        protected int $cmid = 0,
        /** @var int $courseid The course ID of the course context the category belongs to. */
        protected int $courseid = 0,
    ) {
        $manager = new category_manager();
        $this->canreorder = !$manager->is_only_child_of_top_category_in_context($this->category->id);
    }

    /**
     * Get the canreorder flag.
     *
     * @return bool
     */
    public function get_canreorder(): bool {
        return $this->canreorder;
    }

    /**
     * Create the template data for a category, and call recursively for child categories.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        global $PAGE;
        $canmanagecategory = has_capability('moodle/question:managecategory', $this->context);
        $params = $PAGE->url->params();
        $cmid = $params['cmid'] ?? $this->cmid;
        $courseid = $params['courseid'] ?? $this->courseid;

        // Each section adds html to be displayed as part of this list item.
        $questionbankurl = new moodle_url('/question/edit.php', $params);
        $questionbankurl->param('cat', helper::combine_id_context($this->category));
        $categoryname = format_string($this->category->name, true, ['context' => $this->context]);
        $idnumber = null;
        if ($this->category->idnumber !== null && $this->category->idnumber !== '') {
            $idnumber = $this->category->idnumber;
        }
        $categorydesc = format_text(
            $this->category->info,
            $this->category->infoformat,
            ['context' => $this->context, 'noclean' => true],
        );
        $menu = new action_menu();
        $menu->attributes['draggable'] = 'false';
        $menu->set_kebab_trigger();
        $menu->prioritise = true;

        // Don't allow movement if only subcat.
        if ($canmanagecategory) {
            // This item display a modal for moving a category.
            // Move category modal.
            $menu->add(new action_menu_link(
                new \moodle_url('#'),
                new pix_icon(
                    't/move',
                    get_string('move'),
                    'moodle',
                    [
                        'class' => 'iconsmall',
                    ]
                ),
                get_string('move'),
                false,
                [
                    'class' => 'show-when-movable', // Don't allow moving when this is the only category in the context.
                    'data-categoryid' => $this->category->id,
                    'data-actiontype' => 'move',
                    'data-contextid' => (int) $this->category->contextid,
                    'data-categoryname' => $categoryname,
                    'title' => get_string('movecategory', 'qbank_managecategories', $categoryname),
                ]
            ));

            $thiscontext = (int) $this->category->contextid;
            $editurl = new moodle_url('#');
            $menu->add(new action_menu_link(
                $editurl,
                new pix_icon('t/edit', 'edit'),
                get_string('editsettings'),
                false,
                [
                    'data-action' => 'addeditcategory',
                    'data-actiontype' => 'edit',
                    'data-contextid' => $thiscontext,
                    'data-categoryid' => $this->category->id,
                    'data-cmid' => $cmid,
                    'data-courseid' => $courseid,
                    'data-questioncount' => $this->category->questioncount,
                ]
            ));
            // Sets up delete link.
            $deleteurl = new moodle_url(
                '/question/bank/managecategories/category.php',
                ['delete' => $this->category->id, 'sesskey' => sesskey()]
            );
            if ($courseid !== 0) {
                $deleteurl->param('courseid', $courseid);
            } else {
                $deleteurl->param('cmid', $cmid);
            }
            $menu->add(new action_menu_link(
                $deleteurl,
                new pix_icon('t/delete', 'delete'),
                get_string('delete'),
                false,
                [
                    'class' => 'text-danger show-when-movable', // Don't allow deletion when this is the only category in context.
                    'data-confirmation' => 'modal',
                    'data-confirmation-type' => 'delete',
                    'data-confirmation-title-str' => json_encode(['delete', 'core']),
                    'data-confirmation-yes-button-str' => json_encode(['delete', 'core']),
                    'data-confirmation-content-str' => json_encode([
                        'confirmdelete',
                        'qbank_managecategories',
                        $this->category->name,
                    ]),
                ],
            ));
        }

        // Sets up export to XML link.
        if (qbank::is_plugin_enabled('qbank_exportquestions')) {
            $exporturl = new moodle_url(
                '/question/bank/exportquestions/export.php',
                ['cat' => helper::combine_id_context($this->category)]
            );
            if ($courseid !== 0) {
                $exporturl->param('courseid', $courseid);
            } else {
                $exporturl->param('cmid', $cmid);
            }

            $menu->add(new action_menu_link(
                $exporturl,
                new pix_icon('t/download', 'download'),
                get_string('exportasxml', 'question'),
                false,
            ));
        }

        $children = [];
        if (!empty($this->category->children)) {
            foreach ($this->category->children as $child) {
                $childcategory = new category($child, $this->context);
                $children[] = $childcategory->export_for_template($output);
            }
        }
        $itemdata = [
            'categoryid' => $this->category->id,
            'contextid' => $this->category->contextid,
            'questionbankurl' => $questionbankurl->out(false),
            'categoryname' => $categoryname,
            'idnumber' => $idnumber,
            'questioncount' => $this->category->questioncount,
            'categorydesc' => $categorydesc,
            'editactionmenu' => $menu->export_for_template($output),
            'draghandle' => $canmanagecategory && $this->canreorder,
            'haschildren' => !empty($children),
            'children' => $children,
            'parent' => $this->category->parent,
            'sortorder' => $this->category->sortorder,
            'newchildtooltip' => get_string('newchild', 'qbank_managecategories', $categoryname),
        ];
        return $itemdata;
    }



}
