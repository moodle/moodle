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
 * Activity Chooser footer data class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\local\entity;

/**
 * A class to represent the Activity Chooser footer data.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_chooser_footer {

    /** @var string $footerjspath The path to the plugin JS file to dynamically import later. */
    protected $footerjspath;

    /** @var string $footertemplate The rendered template for the footer. */
    protected $footertemplate;

    /** @var string $carouseltemplate The rendered template for the footer. */
    protected $carouseltemplate;

    /**
     * Constructor method.
     *
     * @param string $footerjspath JS file to dynamically import later.
     * @param string $footertemplate Footer template that has been rendered.
     * @param string|null $carouseltemplate Carousel template that may have been rendered.
     */
    public function __construct(string $footerjspath, string $footertemplate, ?string $carouseltemplate = '') {
        $this->footerjspath = $footerjspath;
        $this->footertemplate = $footertemplate;
        $this->carouseltemplate = $carouseltemplate;
    }

    /**
     *  Get the footer JS file path for this plugin.
     *
     * @return string The JS file to call functions from.
     */
    public function get_footer_js_file(): string {
        return $this->footerjspath;
    }

    /**
     * Get the footer rendered template for this plugin.
     *
     * @return string The template that has been rendered for the chooser footer.
     */
    public function get_footer_template(): string {
        return $this->footertemplate;
    }

    /**
     * Get the carousel rendered template for this plugin.
     *
     * @return string The template that has been rendered for the chooser carousel.
     */
    public function get_carousel_template(): string {
        return $this->carouseltemplate;
    }
}
