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

namespace core\output;

/**
 * Stores tabs list
 *
 * Example how to print a single line tabs:
 * $rows = array(
 *    new tabobject(...),
 *    new tabobject(...)
 * );
 * echo $OUTPUT->tabtree($rows, $selectedid);
 *
 * Multiple row tabs may not look good on some devices but if you want to use them
 * you can specify ->subtree for the active tabobject.
 *
 * @copyright 2013 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.5
 * @package core
 * @category output
 */
class tabtree extends tabobject {
    /**
     * Constuctor
     *
     * It is highly recommended to call constructor when list of tabs is already
     * populated, this way you ensure that selected and inactive tabs are located
     * and attribute level is set correctly.
     *
     * @param array $tabs array of tabs, each of them may have it's own ->subtree
     * @param string|null $selected which tab to mark as selected, all parent tabs will
     *     automatically be marked as activated
     * @param array|string|null $inactive list of ids of inactive tabs, regardless of
     *     their level. Note that you can as weel specify tabobject::$inactive for separate instances
     */
    public function __construct($tabs, $selected = null, $inactive = null) {
        $this->subtree = $tabs;
        if ($selected !== null) {
            $this->set_selected($selected);
        }
        if ($inactive !== null) {
            if (is_array($inactive)) {
                foreach ($inactive as $id) {
                    if ($tab = $this->find($id)) {
                        $tab->inactive = true;
                    }
                }
            } else if ($tab = $this->find($inactive)) {
                $tab->inactive = true;
            }
        }
        $this->set_level(0);
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output) {
        $tabs = [];
        $secondrow = false;

        foreach ($this->subtree as $tab) {
            $tabs[] = $tab->export_for_template($output);
            if (!empty($tab->subtree) && ($tab->level == 0 || $tab->selected || $tab->activated)) {
                $secondrow = new tabtree($tab->subtree);
            }
        }

        return (object) [
            'tabs' => $tabs,
            'secondrow' => $secondrow ? $secondrow->export_for_template($output) : false,
        ];
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(tabtree::class, \tabtree::class);
