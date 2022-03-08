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

namespace core_question\output;

use moodle_url;
use renderer_base;
use templatable;
use renderable;
use url_select;

/**
 * Rendered HTML elements for tertiary nav for Question bank.
 *
 * Provides the links for question bank tertiary navigation, below
 * are the links provided for the urlselector:
 * Questions, Categories, Import and Export
 * Also "Add category" button is added to tertiary nav for the categories.
 * The "Add category" would take the user to separate page, add category page.
 *
 * @package   core_question
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbank_action_menu implements templatable, renderable {
    /** @var moodle_url */
    private $currenturl;

    /**
     * qbank_actionbar constructor.
     *
     * @param moodle_url $currenturl The current URL.
     */
    public function __construct(moodle_url $currenturl) {
        $this->currenturl = $currenturl;
    }

    /**
     * Provides the data for the template.
     *
     * @param renderer_base $output renderer_base object.
     * @return array data for the template
     */
    public function export_for_template(renderer_base $output): array {
        $questionslink = new moodle_url('/question/edit.php', $this->currenturl->params());
        if (\core\plugininfo\qbank::is_plugin_enabled("qbank_managecategories")) {
            $categorylink = new moodle_url('/question/bank/managecategories/category.php', $this->currenturl->params());
        }
        $importlink = new moodle_url('/question/bank/importquestions/import.php', $this->currenturl->params());
        $exportlink = new moodle_url('/question/bank/exportquestions/export.php', $this->currenturl->params());

        $menu = [
            $questionslink->out(false) => get_string('questions', 'question'),
        ];

        if (\core\plugininfo\qbank::is_plugin_enabled("qbank_managecategories")) {
            $menu[$categorylink->out(false)] = get_string('categories', 'question');
        }
        $menu[$importlink->out(false)] = get_string('import', 'question');
        $menu[$exportlink->out(false)] = get_string('export', 'question');

        $addcategory = null;
        if (strpos($this->currenturl->get_path(), 'category.php') !== false &&
                $this->currenturl->param('edit') === null) {
            $addcategory = $this->currenturl->out(false, ['edit' => 0]);
        }

        $urlselect = new url_select($menu, $this->currenturl->out(false), null, 'questionbankaction');
        $urlselect->set_label(get_string('questionbanknavigation', 'question'), ['class' => 'accesshide']);

        return [
            'questionbankselect' => $urlselect->export_for_template($output),
            'addcategory' => $addcategory
        ];
    }
}
