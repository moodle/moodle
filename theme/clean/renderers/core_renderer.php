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
 * Clean core renderers.
 *
 * @package    theme_clean
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_clean_core_renderer extends theme_bootstrapbase_core_renderer {

    /**
     * Wrapper for header elements.
     *
     * We override this renderer helper in order to inject the logo into the theme.
     *
     * @param string $heading Heading to be used for the main header.
     * @return string HTML to display the main header.
     */
    public function full_header($heading = null) {
        if (!empty($this->page->theme->settings->logo)) {
            return $this->full_header(html_writer::tag('div', '', array('class' => 'logo')));
        }
        return $this->full_header();
    }

}
