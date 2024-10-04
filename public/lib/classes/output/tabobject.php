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

use moodle_url;

/**
 * Stores one tab
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @copyright Marina Glancy
 */
class tabobject implements renderable, templatable {
    /** @var string unique id of the tab in this tree, it is used to find selected and/or inactive tabs */
    public $id;
    /** @var moodle_url|string link */
    public $link;
    /** @var string text on the tab */
    public $text;
    /** @var string title under the link, by defaul equals to text */
    public $title;
    /** @var bool whether to display a link under the tab name when it's selected */
    public $linkedwhenselected = false;
    /** @var bool whether the tab is inactive */
    public $inactive = false;
    /** @var bool indicates that this tab's child is selected */
    public $activated = false;
    /** @var bool indicates that this tab is selected */
    public $selected = false;
    /** @var array stores children tabobjects */
    public $subtree = [];
    /** @var int level of tab in the tree, 0 for root (instance of tabtree), 1 for the first row of tabs */
    public $level = 1;

    /**
     * Constructor
     *
     * @param string $id unique id of the tab in this tree, it is used to find selected and/or inactive tabs
     * @param string|moodle_url $link
     * @param string $text text on the tab
     * @param string $title title under the link, by defaul equals to text
     * @param bool $linkedwhenselected whether to display a link under the tab name when it's selected
     */
    public function __construct($id, $link = null, $text = '', $title = '', $linkedwhenselected = false) {
        $this->id = $id;
        $this->link = $link;
        $this->text = $text;
        $this->title = $title ? $title : $text;
        $this->linkedwhenselected = $linkedwhenselected;
    }

    /**
     * Travels through tree and finds the tab to mark as selected, all parents are automatically marked as activated
     *
     * @param string $selected the id of the selected tab (whatever row it's on),
     *    if null marks all tabs as unselected
     * @return bool whether this tab is selected or contains selected tab in its subtree
     */
    protected function set_selected($selected) {
        if ((string)$selected === (string)$this->id) {
            $this->selected = true;
            // This tab is selected. No need to travel through subtree.
            return true;
        }
        foreach ($this->subtree as $subitem) {
            if ($subitem->set_selected($selected)) {
                // This tab has child that is selected. Mark it as activated. No need to check other children.
                $this->activated = true;
                return true;
            }
        }
        return false;
    }

    /**
     * Travels through tree and finds a tab with specified id
     *
     * @param string $id
     * @return tabtree|null
     */
    public function find($id) {
        if ((string)$this->id === (string)$id) {
            return $this;
        }
        foreach ($this->subtree as $tab) {
            if ($obj = $tab->find($id)) {
                return $obj;
            }
        }
        return null;
    }

    /**
     * Allows to mark each tab's level in the tree before rendering.
     *
     * @param int $level
     */
    protected function set_level($level) {
        $this->level = $level;
        foreach ($this->subtree as $tab) {
            $tab->set_level($level + 1);
        }
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return object
     */
    public function export_for_template(renderer_base $output) {
        if ($this->inactive || ($this->selected && !$this->linkedwhenselected) || $this->activated) {
            $link = null;
        } else {
            $link = $this->link;
        }
        $active = $this->activated || $this->selected;

        return (object) [
            'id' => $this->id,
            'link' => is_object($link) ? $link->out(false) : $link,
            'text' => $this->text,
            'title' => $this->title,
            'inactive' => !$active && $this->inactive,
            'active' => $active,
            'level' => $this->level,
        ];
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(tabobject::class, \tabobject::class);
