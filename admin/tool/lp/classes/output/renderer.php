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
 * Renderer class for learning plans
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use renderable;

/**
 * Renderer class for learning plans
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param manage_competency_frameworks_page $page
     *
     * @return string html for the page
     */
    public function render_manage_competency_frameworks_page(manage_competency_frameworks_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/manage_competency_frameworks_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param manage_competencies_page $page
     *
     * @return string html for the page
     */
    public function render_manage_competencies_page(manage_competencies_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/manage_competencies_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param course_competencies_page $page
     *
     * @return string html for the page
     */
    public function render_course_competencies_page(course_competencies_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/course_competencies_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param template_competencies_page $page
     *
     * @return string html for the page
     */
    public function render_template_competencies_page(template_competencies_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/template_competencies_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param manage_templates_page $page
     *
     * @return string html for the page
     */
    public function render_manage_templates_page(manage_templates_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/manage_templates_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param plan_page $page
     * @return bool|string
     */
    public function render_plan_page(plan_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/plan_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param plans_page $page
     * @return bool|string
     */
    public function render_plans_page(plans_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/plans_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param renderable $page
     * @return string
     */
    public function render_related_competencies_section(renderable $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/related_competencies', $data);
    }

    /**
     * Defer to template.
     *
     * @param renderable $page
     * @return string
     */
    public function render_user_competency_summary_in_course(user_competency_summary_in_course $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/user_competency_summary_in_course', $data);
    }

    /**
     * Defer to template.
     *
     * @param renderable $page
     * @return string
     */
    public function render_user_competency_summary_in_plan(user_competency_summary_in_plan $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/user_competency_summary_in_plan', $data);
    }

    /**
     * Render the template plans page.
     *
     * @param  renderable $page
     * @return string
     */
    public function render_template_plans_page(renderable $page) {
        return $page->table->out(50, true);
    }

    /**
     * Render the template cohorts page.
     *
     * @param  renderable $page
     * @return string
     */
    public function render_template_cohorts_page(renderable $page) {
        return $page->table->out(50, true);
    }

    /**
     * Defer to template.
     *
     * @param user_evidence_page $page
     * @return string
     */
    public function render_user_evidence_page(user_evidence_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/user_evidence_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param user_evidence_list_page $page
     * @return string
     */
    public function render_user_evidence_list_page(user_evidence_list_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/user_evidence_list_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param user_competency_course_navigation $nav
     * @return string
     */
    public function render_user_competency_course_navigation(user_competency_course_navigation $nav) {
        $data = $nav->export_for_template($this);
        return parent::render_from_template('tool_lp/user_competency_course_navigation', $data);
    }
}
