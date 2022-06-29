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

namespace core_badges\output;

use renderable;
use renderer_base;
use moodle_page;
use navigation_node;
use templatable;

/**
 * Abstract class for the badges tertiary navigation. The class initialises the page and type class variables.
 *
 * @package   core_badges
 * @copyright 2021 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_action_bar implements renderable, templatable {
    /** @var moodle_page $page The context we are operating within. */
    protected $page;
    /** @var int $type The badge type. */
    protected $type;

    /**
     * standard_action_bar constructor.
     *
     * @param moodle_page $page
     * @param int $type
     */
    public function __construct(moodle_page $page, int $type) {
        $this->type = $type;
        $this->page = $page;
    }

    /**
     * The template that this tertiary nav should use.
     *
     * @return string
     */
    abstract public function get_template(): string;

    /**
     * Gets additional third party navigation nodes for display.
     *
     * @param renderer_base $output  The output
     * @return array All that sweet third party navigation action.
     */
    public function get_third_party_nav_action(renderer_base $output): array {
        $badgenode = $this->page->settingsnav->find('coursebadges', navigation_node::TYPE_CONTAINER);
        if (!$badgenode) {
            return [];
        }
        $leftovernodes = [];
        foreach ($badgenode->children as $key => $value) {
            if (array_search($value->key, $this->expected_items()) === false) {
                $leftovernodes[] = $value;
            }
        }
        $result = \core\navigation\views\secondary::create_menu_element($leftovernodes);

        if ($result == false) {
            return [];
        } else {
            $data ['thirdpartybutton'] = true;
            if (count($result) == 1) {
                // Return a button.
                $link = key($result);
                $text = current($result);
                $data['thirdpartynodes'] = ['link' => $link, 'text' => $text];
            } else {
                // Return a url_select.
                $selectobject = new \url_select($result, $this->page->url, get_string('othernavigation', 'badges'));
                $data['thirdpartynodes'] = $selectobject->export_for_template($output);
                $data['thirdpartybutton'] = false;
            }
        }

        return $data;
    }

    /**
     * Expected navigation node keys for badges.
     *
     * @return array default badge navigation node keys.
     */
    protected function expected_items(): array {
        return ['coursebadges', 'newbadge'];
    }
}
