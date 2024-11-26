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

namespace theme_adaptable\output\navigation;

use core\output\custom_menu;
use core\output\renderer_base;

/**
 * Adaptable theme.
 * Primary navigation renderable
 *
 * @package    theme_adaptable
 * @category   navigation
 * @copyright  2021 onwards Peter Dias
 * @copyright  2023 G J Barnard.  Based upon work done by Peter Dias.
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class primary extends \core\navigation\output\primary {
    /** @var moodle_page $page the moodle page that the navigation belongs to */
    private $page = null;

    /**
     * primary constructor.
     * @param \moodle_page $page
     */
    public function __construct($page) {
        $this->page = $page;
        parent::__construct($page);
    }

    /**
     * Combine the various menus into a standardized output.
     *
     * @param renderer_base|null $output
     * @return array
     */
    public function export_for_template(?renderer_base $output = null): array {
        if (!$output) {
            $output = $this->page->get_renderer('core');
        }

        $menu = $output->navigation_menu_content();
        $primarynodes = [];
        foreach ($menu->get_children() as $node) {
            $url = $node->get_url();
            $target = '';
            if (is_object($url) && (get_class($url) == 'core\url')) {
                $target = $url->get_param('helptarget');
                if ($target != null) {
                    $url->remove_params('helptarget');
                    $node->set_url($url);
                }
            }
            $thenode = $node->export_for_template($output);
            if (!empty($target)) {
                $thenode->target = $target;
            }
            $primarynodes[] = $thenode;
        }
        $mobileprimarynav = array_merge($primarynodes, $this->get_custom_menu($output));

        return [
            'mobileprimarynav' => $mobileprimarynav,
        ];
    }
}
