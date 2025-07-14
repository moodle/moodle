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

use core\exception\coding_exception;
use core\lang_string;
use core\output\local\action_menu\subpanel;
use core\output\action_menu\link as action_menu_link;
use core\output\action_menu\filler as action_menu_filler;
use moodle_page;
use stdClass;

/**
 * An action menu.
 *
 * This action menu component takes a series of primary and secondary actions.
 * The primary actions are displayed permanently and the secondary attributes are displayed within a drop
 * down menu.
 *
 * @package core
 * @category output
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_menu implements renderable, templatable {
    /**
     * Top right alignment.
     */
    const TL = 1;

    /**
     * Top right alignment.
     */
    const TR = 2;

    /**
     * Top right alignment.
     */
    const BL = 3;

    /**
     * Top right alignment.
     */
    const BR = 4;

    /**
     * The instance number. This is unique to this instance of the action menu.
     * @var int
     */
    protected $instance = 0;

    /**
     * An array of primary actions. Please use {@see action_menu::add_primary_action()} to add actions.
     * @var array
     */
    protected $primaryactions = [];

    /**
     * An array of secondary actions. Please use {@see action_menu::add_secondary_action()} to add actions.
     * @var array
     */
    protected $secondaryactions = [];

    /**
     * An array of attributes added to the container of the action menu.
     * Initialised with defaults during construction.
     * @var array
     */
    public $attributes = [];
    /**
     * An array of attributes added to the container of the primary actions.
     * Initialised with defaults during construction.
     * @var array
     */
    public $attributesprimary = [];
    /**
     * An array of attributes added to the container of the secondary actions.
     * Initialised with defaults during construction.
     * @var array
     */
    public $attributessecondary = [];

    /**
     * The string to use next to the icon for the action icon relating to the secondary (dropdown) menu.
     * @var array
     */
    public $actiontext = null;

    /**
     * The string to use for the accessible label for the menu.
     * @var array
     */
    public $actionlabel = null;

    /**
     * An icon to use for the toggling the secondary menu (dropdown).
     * @var pix_icon
     */
    public $actionicon;

    /**
     * Any text to use for the toggling the secondary menu (dropdown).
     * @var string
     */
    public $menutrigger = '';

    /**
     * An array of attributes added to the trigger element of the secondary menu.
     * @var array
     */
    public $triggerattributes = [];

    /**
     * Any extra classes for toggling to the secondary menu.
     * @var string
     */
    public $triggerextraclasses = '';

    /**
     * Place the action menu before all other actions.
     * @var bool
     */
    public $prioritise = false;

    /**
     * Dropdown menu alignment class.
     * @var string
     */
    public $dropdownalignment = '';

    /**
     * Constructs the action menu with the given items.
     *
     * @param array $actions An array of actions (action_menu_link|pix_icon|string).
     */
    public function __construct(array $actions = []) {
        static $initialised = 0;
        $this->instance = $initialised;
        $initialised++;

        $this->attributes = [
            'id' => 'action-menu-' . $this->instance,
            'class' => 'moodle-actionmenu',
            'data-enhance' => 'moodle-core-actionmenu',
        ];
        $this->attributesprimary = [
            'id' => 'action-menu-' . $this->instance . '-menubar',
            'class' => 'menubar',
        ];
        $this->attributessecondary = [
            'id' => 'action-menu-' . $this->instance . '-menu',
            'class' => 'menu',
            'data-rel' => 'menu-content',
            'aria-labelledby' => 'action-menu-toggle-' . $this->instance,
            'role' => 'menu',
        ];
        $this->dropdownalignment = 'dropdown-menu-end';
        foreach ($actions as $action) {
            $this->add($action);
        }
    }

    /**
     * Sets the label for the menu trigger.
     *
     * @param string $label The text
     */
    public function set_action_label($label) {
        $this->actionlabel = $label;
    }

    /**
     * Sets the menu trigger text.
     *
     * @param string $trigger The text
     * @param string $extraclasses Extra classes to style the secondary menu toggle.
     */
    public function set_menu_trigger($trigger, $extraclasses = '') {
        $this->menutrigger = $trigger;
        $this->triggerextraclasses = $extraclasses;
    }

    /**
     * Classes for the trigger menu
     */
    const DEFAULT_KEBAB_TRIGGER_CLASSES = 'btn btn-icon d-flex no-caret';

    /**
     * Setup trigger as in the kebab menu.
     *
     * @param string|null $triggername
     * @param core_renderer|null $output
     * @param string|null $extraclasses extra classes for the trigger {@see self::set_menu_trigger()}
     * @throws coding_exception
     */
    public function set_kebab_trigger(
        ?string $triggername = null,
        ?core_renderer $output = null,
        ?string $extraclasses = ''
    ) {
        global $OUTPUT;
        if (empty($output)) {
            $output = $OUTPUT;
        }
        $label = $triggername ?? get_string('actions');
        $triggerclasses = self::DEFAULT_KEBAB_TRIGGER_CLASSES . ' ' . $extraclasses;
        $icon = $output->pix_icon('i/menu', $label);
        $this->set_menu_trigger($icon, $triggerclasses);
    }

    /**
     * Return true if there is at least one visible link in the menu.
     *
     * @return bool
     */
    public function is_empty() {
        return !count($this->primaryactions) && !count($this->secondaryactions);
    }

    /**
     * Initialises JS required fore the action menu.
     * The JS is only required once as it manages all action menu's on the page.
     *
     * @param moodle_page $page
     */
    public function initialise_js(moodle_page $page) {
        static $initialised = false;
        if (!$initialised) {
            $page->requires->yui_module('moodle-core-actionmenu', 'M.core.actionmenu.init');
            $initialised = true;
        }
    }

    /**
     * Adds an action to this action menu.
     *
     * @param action_link|pix_icon|subpanel|string $action
     */
    public function add($action) {

        if ($action instanceof subpanel) {
            $this->add_secondary_subpanel($action);
        } else if ($action instanceof action_link) {
            if ($action->primary) {
                $this->add_primary_action($action);
            } else {
                $this->add_secondary_action($action);
            }
        } else if ($action instanceof pix_icon) {
            $this->add_primary_action($action);
        } else {
            $this->add_secondary_action($action);
        }
    }

    /**
     * Adds a secondary subpanel.
     * @param subpanel $subpanel
     */
    public function add_secondary_subpanel(subpanel $subpanel) {
        $this->secondaryactions[] = $subpanel;
    }

    /**
     * Adds a primary action to the action menu.
     *
     * @param action_menu_link|action_link|pix_icon|string $action
     */
    public function add_primary_action($action) {
        if ($action instanceof action_link || $action instanceof pix_icon) {
            $action->attributes['role'] = 'menuitem';
            $action->attributes['tabindex'] = '-1';
            if ($action instanceof action_menu_link) {
                $action->actionmenu = $this;
            }
        }
        $this->primaryactions[] = $action;
    }

    /**
     * Adds a secondary action to the action menu.
     *
     * @param action_link|pix_icon|string $action
     */
    public function add_secondary_action($action) {
        if ($action instanceof action_link || $action instanceof pix_icon) {
            $action->attributes['role'] = 'menuitem';
            $action->attributes['tabindex'] = '-1';
            if ($action instanceof action_menu_link) {
                $action->actionmenu = $this;
            }
        }
        $this->secondaryactions[] = $action;
    }

    /**
     * Returns the primary actions ready to be rendered.
     *
     * @param null|core_renderer $output The renderer to use for getting icons.
     * @return array
     */
    public function get_primary_actions(?core_renderer $output = null) {
        global $OUTPUT;
        if ($output === null) {
            $output = $OUTPUT;
        }
        $pixicon = $this->actionicon;
        $linkclasses = ['toggle-display'];

        $title = '';
        if (!empty($this->menutrigger)) {
            $pixicon = '<b class="caret"></b>';
            $linkclasses[] = 'textmenu';
        } else {
            $title = new lang_string('actionsmenu', 'moodle');
            $this->actionicon = new pix_icon(
                't/edit_menu',
                '',
                'moodle',
                ['class' => 'iconsmall actionmenu', 'title' => '']
            );
            $pixicon = $this->actionicon;
        }
        if ($pixicon instanceof renderable) {
            $pixicon = $output->render($pixicon);
            if ($pixicon instanceof pix_icon && isset($pixicon->attributes['alt'])) {
                $title = $pixicon->attributes['alt'];
            }
        }
        $string = '';
        if ($this->actiontext) {
            $string = $this->actiontext;
        }
        $label = '';
        if ($this->actionlabel) {
            $label = $this->actionlabel;
        } else {
            $label = $title;
        }
        $actions = $this->primaryactions;
        $attributes = [
            'class' => implode(' ', $linkclasses),
            'title' => $title,
            'aria-label' => $label,
            'id' => 'action-menu-toggle-' . $this->instance,
            'role' => 'menuitem',
            'tabindex' => '-1',
        ];
        $link = html_writer::link('#', $string . $this->menutrigger . $pixicon, $attributes);
        if ($this->prioritise) {
            array_unshift($actions, $link);
        } else {
            $actions[] = $link;
        }
        return $actions;
    }

    /**
     * Returns the secondary actions ready to be rendered.
     * @return array
     */
    public function get_secondary_actions() {
        return $this->secondaryactions;
    }

    /**
     * Sets the selector that should be used to find the owning node of this menu.
     * @param string $selector A CSS/YUI selector to identify the owner of the menu.
     */
    public function set_owner_selector($selector) {
        $this->attributes['data-owner'] = $selector;
    }

    /**
     * @deprecated since Moodle 4.0, use action_menu::set_menu_left().
     */
    #[\core\attribute\deprecated('action_menu::set_menu_left', since: '4.0', mdl: 'MDL-72466', final: true)]
    public function set_alignment(): void {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * Returns a string to describe the alignment.
     *
     * @param int $align One of action_menu::TL, action_menu::TR, action_menu::BL, action_menu::BR.
     * @return string
     */
    protected function get_align_string($align) {
        switch ($align) {
            case self::TL:
                return 'tl';
            case self::TR:
                return 'tr';
            case self::BL:
                return 'bl';
            case self::BR:
                return 'br';
            default:
                return 'tl';
        }
    }

    /**
     * Aligns the left corner of the dropdown.
     *
     */
    public function set_menu_left() {
        $this->dropdownalignment = 'dropdown-menu-start';
    }

    /**
     * @deprecated since Moodle 4.3, use set_boundary() method instead.
     */
    #[\core\attribute\deprecated('action_menu::set_boundary', since: '4.3', mdl: 'MDL-77375', final: true)]
    public function set_constraint(): void {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * Set the overflow constraint boundary of the dropdown menu.
     * @see https://getbootstrap.com/docs/4.6/components/dropdowns/#options The 'boundary' option in the Bootstrap documentation
     *
     * @param string $boundary Accepts the values of 'viewport', 'window', or 'scrollParent'.
     * @throws coding_exception
     */
    public function set_boundary(string $boundary) {
        if (!in_array($boundary, ['viewport', 'window', 'scrollParent'])) {
            throw new coding_exception("HTMLElement reference boundaries are not supported." .
                "Accepted boundaries are 'viewport', 'window', or 'scrollParent'.", DEBUG_DEVELOPER);
        }

        $this->triggerattributes['data-boundary'] = $boundary;
    }

    /**
     * @deprecated since Moodle 3.2, use a list of action_icon instead.
     */
    #[\core\attribute\deprecated('Use a list of action_icons instead', since: '3.2', mdl: 'MDL-55904', final: true)]
    public function do_not_enhance() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * Returns true if this action menu will be enhanced.
     *
     * @return bool
     */
    public function will_be_enhanced() {
        return isset($this->attributes['data-enhance']);
    }

    /**
     * Sets nowrap on items. If true menu items should not wrap lines if they are longer than the available space.
     *
     * This property can be useful when the action menu is displayed within a parent element that is either floated
     * or relatively positioned.
     * In that situation the width of the menu is determined by the width of the parent element which may not be large
     * enough for the menu items without them wrapping.
     * This disables the wrapping so that the menu takes on the width of the longest item.
     *
     * @param bool $value If true nowrap gets set, if false it gets removed. Defaults to true.
     */
    public function set_nowrap_on_items($value = true) {
        $class = 'nowrap-items';
        if (!empty($this->attributes['class'])) {
            $pos = strpos($this->attributes['class'], $class);
            if ($value === true && $pos === false) {
                // The value is true and the class has not been set yet. Add it.
                $this->attributes['class'] .= ' ' . $class;
            } else if ($value === false && $pos !== false) {
                // The value is false and the class has been set. Remove it.
                $this->attributes['class'] = substr($this->attributes['class'], $pos, strlen($class));
            }
        } else if ($value) {
            // The value is true and the class has not been set yet. Add it.
            $this->attributes['class'] = $class;
        }
    }

    /**
     * Add classes to the action menu for an easier styling.
     *
     * @param string $class The class to add to attributes.
     */
    public function set_additional_classes(string $class = '') {
        if (!empty($this->attributes['class'])) {
            $this->attributes['class'] .= " " . $class;
        } else {
            $this->attributes['class'] = $class;
        }
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        // Assign a role of menubar to this action menu when:
        // - it contains 2 or more primary actions; or
        // - if it contains a primary action and secondary actions.
        if (count($this->primaryactions) > 1 || (!empty($this->primaryactions) && !empty($this->secondaryactions))) {
            $this->attributes['role'] = 'menubar';
        }
        $attributes = $this->attributes;

        $data->instance = $this->instance;

        $data->classes = isset($attributes['class']) ? $attributes['class'] : '';
        unset($attributes['class']);

        $data->attributes = array_map(function ($key, $value) {
            return [ 'name' => $key, 'value' => $value ];
        }, array_keys($attributes), $attributes);

        $data->primary = $this->export_primary_actions_for_template($output);
        $data->secondary = $this->export_secondary_actions_for_template($output);
        $data->dropdownalignment = $this->dropdownalignment;

        return $data;
    }

    /**
     * Export the primary actions for the template.
     * @param renderer_base $output
     * @return stdClass
     */
    protected function export_primary_actions_for_template(renderer_base $output): stdClass {
        $attributes = $this->attributes;
        $attributesprimary = $this->attributesprimary;

        $primary = new stdClass();
        $primary->title = '';
        $primary->prioritise = $this->prioritise;

        $primary->classes = isset($attributesprimary['class']) ? $attributesprimary['class'] : '';
        unset($attributesprimary['class']);

        $primary->attributes = array_map(function ($key, $value) {
            return ['name' => $key, 'value' => $value];
        }, array_keys($attributesprimary), $attributesprimary);
        $primary->triggerattributes = array_map(function ($key, $value) {
            return ['name' => $key, 'value' => $value];
        }, array_keys($this->triggerattributes), $this->triggerattributes);

        $actionicon = $this->actionicon;
        if (!empty($this->menutrigger)) {
            $primary->menutrigger = $this->menutrigger;
            $primary->triggerextraclasses = $this->triggerextraclasses;
            if ($this->actionlabel) {
                $primary->title = $this->actionlabel;
            } else if ($this->actiontext) {
                $primary->title = $this->actiontext;
            } else {
                $primary->title = strip_tags($this->menutrigger);
            }
        } else {
            $primary->title = get_string('actionsmenu');
            $iconattributes = ['class' => 'iconsmall actionmenu', 'title' => $primary->title];
            $actionicon = new pix_icon('t/edit_menu', '', 'moodle', $iconattributes);
        }

        // If the menu trigger is within the menubar, assign a role of menuitem. Otherwise, assign as a button.
        $primary->triggerrole = 'button';
        if (isset($attributes['role']) && $attributes['role'] === 'menubar') {
            $primary->triggerrole = 'menuitem';
        }

        if ($actionicon instanceof pix_icon) {
            $primary->icon = $actionicon->export_for_pix();
            if (!empty($actionicon->attributes['alt'])) {
                $primary->title = $actionicon->attributes['alt'];
            }
        } else {
            $primary->iconraw = $actionicon ? $output->render($actionicon) : '';
        }

        $primary->actiontext = $this->actiontext ? (string) $this->actiontext : '';
        $primary->items = array_map(function ($item) use ($output) {
            $data = (object) [];
            if ($item instanceof action_menu_link) {
                $data->actionmenulink = $item->export_for_template($output);
            } else if ($item instanceof action_menu_filler) {
                $data->actionmenufiller = $item->export_for_template($output);
            } else if ($item instanceof action_link) {
                $data->actionlink = $item->export_for_template($output);
            } else if ($item instanceof pix_icon) {
                $data->pixicon = $item->export_for_template($output);
            } else {
                $data->rawhtml = ($item instanceof renderable) ? $output->render($item) : $item;
            }
            return $data;
        }, $this->primaryactions);
        return $primary;
    }

    /**
     * Export the secondary actions for the template.
     * @param renderer_base $output
     * @return stdClass
     */
    protected function export_secondary_actions_for_template(renderer_base $output): stdClass {
        $attributessecondary = $this->attributessecondary;
        $secondary = new stdClass();
        $secondary->classes = isset($attributessecondary['class']) ? $attributessecondary['class'] : '';
        unset($attributessecondary['class']);

        $secondary->attributes = array_map(function ($key, $value) {
            return ['name' => $key, 'value' => $value];
        }, array_keys($attributessecondary), $attributessecondary);
        $secondary->items = array_map(function ($item) use ($output) {
            $data = (object) [
                'simpleitem' => true,
            ];
            if ($item instanceof action_menu_link) {
                $data->actionmenulink = $item->export_for_template($output);
                $data->simpleitem = false;
            } else if ($item instanceof action_menu_filler) {
                $data->actionmenufiller = $item->export_for_template($output);
                $data->simpleitem = false;
            } else if ($item instanceof subpanel) {
                $data->subpanel = $item->export_for_template($output);
                $data->simpleitem = false;
            } else if ($item instanceof action_link) {
                $data->actionlink = $item->export_for_template($output);
            } else if ($item instanceof pix_icon) {
                $data->pixicon = $item->export_for_template($output);
            } else {
                $data->rawhtml = ($item instanceof renderable) ? $output->render($item) : $item;
            }
            return $data;
        }, $this->secondaryactions);
        return $secondary;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(action_menu::class, \action_menu::class);
