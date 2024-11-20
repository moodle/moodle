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
 * Renderer class for Iomad Learning Paths
 *
 * @package    local_iomad_learninpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iomad_learningpath\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

class renderer extends plugin_renderer_base {

    /**
     * Render the learning path manage page
     * @param manage_page $page
     * @return string html for page
     */
    public function render_manage_page($page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_iomad_learningpath/manage_page', $data);
    }

    /**
     * Render the learning path edit path page
     * @param editpath_page $page
     * @return string html for page
     */
    public function render_editpath_page($page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_iomad_learningpath/editpath_page', $data);
    }

    /**
     * Render the learning path edit group page
     * @param editpath_page $page
     * @return string html for page
     */
    public function render_editgroup_page($page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_iomad_learningpath/editgroup_page', $data);
    }

    /**
     * Render the courselist path page
     * @param courselist_page $page
     * @return string html for page
     */
    public function render_courselist_page($page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_iomad_learningpath/courselist_page', $data);
    }

    /**
     * Render the students assignment
     * @param students_page $page
     * @return string html for page
     */
    public function render_students_page($page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_iomad_learningpath/students_page', $data);
    }
}

