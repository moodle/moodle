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

use core\output\action_link;
use core\output\pix_icon;
use core\output\renderable;
use stdClass;

/**
 * Interface to a subpanel implementation.
 *
 * @package    core
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class subpanel extends action_link implements renderable {
    /**
     * The subpanel content.
     * @var renderable
     */
    protected $subpanel;

    /**
     * The number of instances of this action menu link (and its subclasses).
     * @var int
     */
    protected static $instance = 1;

    /**
     * Constructor.
     * @param string $text the text to display
     * @param renderable $subpanel the subpanel content
     * @param array|null $attributes an optional array of attributes
     * @param pix_icon|null $icon an optional icon
     */
    public function __construct(
        $text,
        renderable $subpanel,
        ?array $attributes = null,
        ?pix_icon $icon = null
    ) {
        $this->text = $text;
        $this->subpanel = $subpanel;
        if (empty($attributes['id'])) {
            $attributes['id'] = \html_writer::random_id('action_menu_submenu');
        }
        $this->attributes = (array) $attributes;
        $this->icon = $icon;
    }

    /**
     * Export this object for template rendering.
     * @param \renderer_base $output the output renderer
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output): stdClass {
        $data = parent::export_for_template($output);
        $data->instance = self::$instance++;
        $data->subpanelcontent = $output->render($this->subpanel);
        // The menu trigger icon collides with the subpanel item icon. Unlike regular menu items,
        // subpanel items usually does not use icons. To prevent the collision, subpanels use a diferent
        // context variable for item icon.
        $data->itemicon = $data->icon;
        unset($data->icon);
        return $data;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(subpanel::class, \core\output\local\action_menu\subpanel::class);
