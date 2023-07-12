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
 * Tiles course format, inline css output class
 *
 * @package format_tiles
 * @copyright 2018 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace format_tiles\output;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot .'/course/format/lib.php');

/**
 * Prepares data for echoing inline css via template to provide custom colour for tiles
 *
 * @package format_tiles
 * @copyright 2018 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class inline_css_output implements \renderable, \templatable {
    /**
     * Course object
     * @var
     */
    private $course;

    /**
     * Whether we have a mobile user agent.
     * @var
     */
    private $ismobile;

    /**
     * Whether site admin has enabled photo tiles.
     * @var
     */
    private $allowphototiles;

    /**
     * Whether we are using JS nav or not.
     * @var bool
     */
    private $usejsnav;

    /**
     * course_output constructor
     * @param \stdClass $course the course DB object.
     * @param bool $ismobile if the user is on mobile.
     * @param bool $usejsnav are we using JS navigation.
     * @param bool $allowphototiles are we allowing photo tiles.
     */
    public function __construct($course, $ismobile, $usejsnav, $allowphototiles) {
        $this->course = $course;
        $this->ismobile = $ismobile;
        $this->usejsnav = $usejsnav;
        $this->allowphototiles = $allowphototiles;
    }
    /**
     * Export the data for the mustache template.
     * @see format_tiles_width_template_data()
     * @param \renderer_base $output
     * @return array|\stdClass
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_for_template(\renderer_base $output) {

        // TODO avoid using inline CSS for tile colours - better to use a class so that themes can override.
        // Would involve injecting our dynamic values into theme CSS.
        // Our styles.css gets in via outputlib.php get_css_files()).
        // See also styles.php theme_styles_generate_and_store().
        $basecolour = $this->get_tile_base_colour($this->course);
        $outputdata = array(
            'base_colour' => $this->rgbacolour($basecolour),
            'tile_light_colour' => $this->rgbacolour($basecolour, 0.05),
            'tile_hover_colour' => get_config('format_tiles', 'hovercolour'),
            'custom_css' => get_config('format_tiles', 'customcss'),
            'button_hover_colour' => $this->rgbacolour($basecolour, 0.1),
        );
        if ($this->allowphototiles) {
            $outputdata['allowphototiles'] = 1;
            $outputdata['photo_tile_text_bg'] = $this->rgbacolour(
                $basecolour,
                1.0 - (float)get_config('format_tiles', 'phototiletitletransarency')
            );

            // The best values here vary by theme and browser, so mostly come from admin setting.
            // If the site admin sets background opacity to solid then it doesn't matter if the lines overlap.
            $outputdata['phototilefontsize'] = 20;
            $outputdata['phototiletextpadding'] = (float)get_config('format_tiles', 'phototitletitlepadding') / 10;
            $outputdata['phototiletextlineheight'] = (float)get_config('format_tiles', 'phototitletitlelineheight') / 10;
        }

        if ($this->course->courseusebarforheadings != 0 && $this->course->courseusebarforheadings != 'standard') {
            // Will be 1 or 0 for use or not use now.
            // (Legacy values could be 'standard' for not use, or a colour for use, but in that case treat as 'use').
            $outputdata['shade_heading_bar'] = true;
        }
        $outputdata['ismobile'] = $this->ismobile;

        if ($this->usejsnav && !$this->ismobile) {
            // See the PHP doc for the template_data function below for what this is doing.
            $widthdata = format_tiles_width_template_data($this->course->id);
            foreach ($widthdata as $k => $v) {
                $outputdata[$k] = $v;
            }
        }
        return $outputdata;
    }

    /**
     * Convert hex colour from plugin settings admin page to RGBA
     * so that can add transparency to it when used as background
     * @param string $hex the colour in hex form e.g. #979797
     * @param int $opacity
     * @return string rgba colour
     */
    private function rgbacolour($hex, $opacity = 1) {
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        return 'rgba(' . $r . ',' . $g . ',' . $b . ', ' . $opacity . ')';
    }

    /**
     * get the colour which should be used as the base course for this course
     * (Can depend on theme, plugin and/or course settings
     * @param \stdClass $course the course object
     * @return mixed|string the hex colour
     * @throws \dml_exception
     */
    private function get_tile_base_colour($course) {
        global $PAGE;
        // Get tile colours to echo in CSS.
        $basecolour = '';

        if (!(get_config('format_tiles', 'followthemecolour'))) {
            if (!$basecolour = $course->basecolour) {
                // If no course tile colour is set, use plugin default colour.
                $basecolour = get_config('format_tiles', 'tilecolour1');
            }
        }

        // We are following theme's main colour so find out what it is.
        if (!$basecolour || !preg_match('/^#[a-f0-9]{6}$/i', $basecolour)) {
            // If boost theme is in use, it uses "brandcolor" so try to get that if current theme has it.
            $basecolour = get_config('theme_' . $PAGE->theme->name, 'brandcolor');
            if (!$basecolour) {
                // If not got a colour yet, look where essential theme stores its brand color and try that.
                $basecolour = get_config('theme_' . $PAGE->theme->name, 'themecolor');
            }
        }
        if (!$basecolour || !preg_match('/^#[a-f0-9]{6}$/i', $basecolour)) {
            // If still no colour set, use a default colour.
            $basecolour = '#1670CC';
        }
        return $basecolour;
    }
}
