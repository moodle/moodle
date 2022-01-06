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
 * Block LP renderer.
 *
 * @package    block_lp
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lp\output;
defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use renderable;

/**
 * Block LP renderer class.
 *
 * @package    block_lp
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     * @param renderable $page
     * @return string
     */
    public function render_competencies_to_review_page(renderable $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_lp/competencies_to_review_page', $data);
    }

    /**
     * Defer to template.
     * @param renderable $page
     * @return string
     */
    public function render_plans_to_review_page(renderable $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_lp/plans_to_review_page', $data);
    }

    /**
     * Defer to template.
     * @param renderable $summary
     * @return string
     */
    public function render_summary(renderable $summary) {
        $data = $summary->export_for_template($this);
        return parent::render_from_template('block_lp/summary', $data);
    }

}
