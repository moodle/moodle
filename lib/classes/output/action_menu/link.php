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

namespace core\output\action_menu;

use core\output\action_menu;
use core\output\action_link;
use core\output\pix_icon;
use core\output\renderable;
use core\output\renderer_base;
use moodle_url;
use stdClass;

/**
 * An action menu action
 *
 * @package core
 * @category output
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class link extends action_link implements renderable {
    /**
     * True if this is a primary action. False if not.
     * @var bool
     */
    public $primary = true;

    /**
     * The action menu this link has been added to.
     * @var action_menu
     */
    public $actionmenu = null;

    /**
     * The number of instances of this action menu link (and its subclasses).
     *
     * @var int
     * @deprecated since Moodle 4.4.
     */
    protected static $instance = 1;

    /**
     * Constructs the object.
     *
     * @param moodle_url $url The URL for the action.
     * @param pix_icon|null $icon The icon to represent the action.
     * @param string $text The text to represent the action.
     * @param bool $primary Whether this is a primary action or not.
     * @param array $attributes Any attribtues associated with the action.
     */
    public function __construct(moodle_url $url, ?pix_icon $icon, $text, $primary = true, array $attributes = []) {
        parent::__construct($url, $text, null, $attributes, $icon);
        $this->primary = (bool)$primary;
        $this->add_class('menu-action');
        $this->attributes['role'] = 'menuitem';
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = parent::export_for_template($output);

        // Ignore what the parent did with the attributes, except for ID and class.
        $data->attributes = [];
        $attributes = $this->attributes;
        unset($attributes['id']);
        unset($attributes['class']);

        // Handle text being a renderable.
        if ($this->text instanceof renderable) {
            $data->text = $this->render($this->text);
        }

        $data->showtext = (!$this->icon || $this->primary === false);

        $data->icon = null;
        if ($this->icon) {
            $icon = $this->icon;
            if ($this->primary || !$this->actionmenu->will_be_enhanced()) {
                $attributes['title'] = $data->text;
            }
            $data->icon = $icon ? $icon->export_for_pix() : null;
        }

        $data->disabled = !empty($attributes['disabled']);
        unset($attributes['disabled']);

        $data->attributes = array_map(function ($key, $value) {
            return [
                'name' => $key,
                'value' => $value,
            ];
        }, array_keys($attributes), $attributes);

        return $data;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(link::class, \action_menu_link::class);
