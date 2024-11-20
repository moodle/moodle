<?php
// This file is part of the pimenko theme for Moodle
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
 * Theme pimenko renderer file.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_pimenko\output;

use coding_exception;
use moodle_exception;
use plugin_renderer_base;
use theme_config;
use stdClass;

final class carousel_renderer extends plugin_renderer_base {
    public $themeconf;
    public $slides;
    public $layout;

    /**
     * Render a carousel.
     *
     * @return string
     * @throws moodle_exception
     */
    public function output(): string {
        $this->load_items();
        $template = new stdClass();
        $template->slides = $this->slides;
        $template->layout = 'centered';
        return $this->render_from_template(
                'theme_pimenko/carousel',
                $template
        );
    }

    /**
     * @return bool
     * @throws coding_exception
     */
    private function load_items(): bool {
        $this->themeconf = $theme = theme_config::load('pimenko');
        $imagenr = $this->themeconf->settings->slideimagenr;
        for ($i = 1; $i <= $imagenr; $i++) {
            $slide = new stdClass();
            // We need to count from 0 or indicator will not work.
            $slide->slidenum = $i - 1;
            $slide->active = '';
            if ($i == 1) {
                $slide->active = 'active';
            }
            $image = "slideimage{$i}";
            $caption = "slidecaption{$i}";
            if ($this->themeconf->setting_file_url(
                    $image,
                    $image
            )) {
                $slide->image = $this->themeconf->setting_file_url(
                        $image,
                        $image
                );
                $slide->caption = format_text($this->themeconf->settings->$caption);
                $this->slides[] = $slide;
            }
        }
        return true;
    }
}
