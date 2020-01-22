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

/**
 * Contains the content_item class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\local\entity;

defined('MOODLE_INTERNAL') || die();

/**
 * The content_item class.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_item {
    /** @var int $id the id. */
    private $id;

    /** @var string $name the name. */
    private $name;

    /** @var title $title the title. */
    private $title;

    /** @var \moodle_url $link the url for the content item's setup page (usually mod/edit.php). */
    private $link;

    /** @var string $icon an html string containing the icon for this item. */
    private $icon;

    /** @var string $help the description/help text for this content item. */
    private $help;

    /** @var int $achetype a module archetype, e.g. MOD_ARCHETYPE_RESOURCE, MOD_ARCHETYPE_OTHER. */
    private $archetype;

    /** @var string $componentname the name of the component from which this content item originates. */
    private $componentname;

    /**
     * The content_item constructor.
     *
     * @param int $id Id number.
     * @param string $name Name of the item, not human readable.
     * @param title $title Human readable title for the item.
     * @param \moodle_url $link The URL to the creation page, with any item specific params
     * @param string $icon HTML containing the icon for the item
     * @param string $help The description of the item.
     * @param int $archetype the archetype for the content item (see MOD_ARCHETYPE_X definitions in lib/moodlelib.php).
     * @param string $componentname the name of the component/plugin with which this content item is associated.
     */
    public function __construct(int $id, string $name, title $title, \moodle_url $link, string $icon, string $help,
            int $archetype, string $componentname) {
        $this->id = $id;
        $this->name = $name;
        $this->title = $title;
        $this->link = $link;
        $this->icon = $icon;
        $this->help = $help;
        $this->archetype = $archetype;
        $this->componentname = $componentname;
    }

    /**
     * Get the name of the component with which this content item is associated.
     *
     * @return string
     */
    public function get_component_name(): string {
        return $this->componentname;
    }

    /**
     * Get the help description of this item.
     *
     * @return string
     */
    public function get_help(): string {
        return $this->help;
    }

    /**
     * Get the archetype of this item.
     *
     * @return int
     */
    public function get_archetype(): int {
        return $this->archetype;
    }

    /**
     * Get the id of this item.
     * @return int
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Get the name of this item.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get the human readable title of this item.
     *
     * @return title
     */
    public function get_title(): title {
        return $this->title;
    }

    /**
     * Get the link to the creation page of this item.
     *
     * @return \moodle_url
     */
    public function get_link(): \moodle_url {
        return $this->link;
    }

    /**
     * Get the icon html for this item.
     *
     * @return string
     */
    public function get_icon(): string {
        return $this->icon;
    }
}
