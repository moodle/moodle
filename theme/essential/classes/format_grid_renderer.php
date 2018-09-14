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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Check if GRID is installed before trying to override it.
if (file_exists("$CFG->dirroot/course/format/grid/renderer.php")) {
    include_once($CFG->dirroot . "/course/format/grid/renderer.php");

    class theme_essential_format_grid_renderer extends format_grid_renderer {
        use \theme_essential\format_renderer_toolbox;

        /**
         * Backwards compatibility method to get 'section0attop' attribute value.
         *
         * @return boolean Value of section0attop.
         */
        private function get_section0attop() {
            if (property_exists($this, 'section0attop')) {
                $reflectionproperty = new ReflectionProperty($this, 'section0attop');
                if ($reflectionproperty->isProtected()) {
                    $settings = $this->courseformat->get_settings();
                    if (!$this->section0attop) {
                        $section0attop = 0;
                    } else if ($settings['setsection0ownpagenogridonesection'] == 2) {
                        $section0attop = 0;
                    } else {
                        $section0attop = 1;
                    }
                    return $section0attop;
                }
            }
            // Grid format fix #24 not implemented.  Assume section 0 is at the top.
            return 1;
        }

        public function get_nav_links($course, $sections, $sectionno) {
            if (!$this->get_section0attop()) {
                $buffer = -1;
            } else {
                $buffer = 0;
            }
            return $this->get_nav_links_content($course, $sections, $sectionno, $buffer);
        }

        public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
            $this->print_single_section_page_content($course, $sections, $mods, $modnames, $modnamesused, $displaysection,
                $this->get_section0attop());
        }
    }
}