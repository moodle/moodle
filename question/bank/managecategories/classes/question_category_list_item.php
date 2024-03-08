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

use moodle_url;

/**
 * An item in a list of question categories.
 *
 * @package    qbank_managecategories
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_category_list_item extends \list_item {

    /**
     * Override set_icon_html function.
     *
     * @param bool $first Is the first on the list.
     * @param bool $last Is the last on the list.
     * @param \list_item $lastitem Last item.
     */
    public function set_icon_html($first, $last, $lastitem): void {
        global $CFG;
        $category = $this->item;
        $url = new moodle_url('/question/bank/managecategories/category.php',
            ($this->parentlist->pageurl->params() + ['edit' => $category->id]));
        $this->icons['edit'] = $this->image_icon(get_string('editthiscategory', 'question'), $url, 'edit');
        parent::set_icon_html($first, $last, $lastitem);
        $toplevel = ($this->parentlist->parentitem === null);// This is a top level item.
        if (($this->parentlist->nextlist !== null) && $last && $toplevel && (count($this->parentlist->items) > 1)) {
            $url = new moodle_url($this->parentlist->pageurl,
                [
                    'movedowncontext' => $this->id,
                    'tocontext' => $this->parentlist->nextlist->context->id,
                    'sesskey' => sesskey()
                ]
            );
            $this->icons['down'] = $this->image_icon(
                    get_string('shareincontext', 'question',
                        $this->parentlist->nextlist->context->get_context_name()), $url, 'down');
        }
        if (($this->parentlist->lastlist !== null) && $first && $toplevel && (count($this->parentlist->items) > 1)) {
            $url = new moodle_url($this->parentlist->pageurl,
                [
                    'moveupcontext' => $this->id,
                    'tocontext' => $this->parentlist->lastlist->context->id,
                    'sesskey' => sesskey()
                ]
            );
            $this->icons['up'] = $this->image_icon(
                    get_string('shareincontext', 'question',
                        $this->parentlist->lastlist->context->get_context_name()), $url, 'up');
        }
    }

    /**
     * Override item_html function.
     *
     * @param array $extraargs
     * @return string Item html.
     * @throws \moodle_exception
     */
    public function item_html($extraargs = []): string {
        global $PAGE, $OUTPUT;
        $str = $extraargs['str'];
        $category = $this->item;

        // Each section adds html to be displayed as part of this list item.
        $nodeparent = $PAGE->settingsnav->find('questionbank', \navigation_node::TYPE_CONTAINER);

        // The category URL is based on the node action.
        $questionbankurl = new moodle_url($nodeparent->action->out_omit_querystring(),
            $this->parentlist->pageurl->params());
        $questionbankurl->param('cat', $category->id . ',' . $category->contextid);

        $categoryname = format_string($category->name, true, ['context' => $this->parentlist->context]);
        $idnumber = null;
        if ($category->idnumber !== null && $category->idnumber !== '') {
            $idnumber = $category->idnumber;
        }
        $questioncount = ' (' . $category->questioncount . ')';
        $categorydesc = format_text($category->info, $category->infoformat,
            ['context' => $this->parentlist->context, 'noclean' => true]);

        // Don't allow delete if this is the top category, or the last editable category in this context.
        $deleteurl = null;
        if ($category->parent && !helper::question_is_only_child_of_top_category_in_context($category->id)) {
            $deleteurl = new moodle_url($this->parentlist->pageurl, ['delete' => $this->id, 'sesskey' => sesskey()]);
        }

        // Render each question category.
        $data =
            [
                'questionbankurl' => $questionbankurl,
                'categoryname' => $categoryname,
                'idnumber' => $idnumber,
                'questioncount' => $questioncount,
                'categorydesc' => $categorydesc,
                'deleteurl' => $deleteurl,
                'deletetitle' => $str->delete
            ];

        return $OUTPUT->render_from_template(helper::PLUGINNAME . '/listitem', $data);
    }
}
