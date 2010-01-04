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
 * Code for the submissions allocation support is defined here
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Allocators are responsible for assigning submissions to reviewers for assessments
 *
 * The task of the allocator is to assign the correct number of submissions to reviewers
 * for assessment. Several allocation methods are expected and they can be combined. For
 * example, teacher can allocate several submissions manually (by 'manual' allocator) and
 * then let the other submissions being allocated randomly (by 'random' allocator).
 * Allocation is actually done by creating an initial assessment record in the
 * workshop_assessments table.
 */
interface workshop_allocator {

    /**
     * Initialize the allocator and eventually process submitted data
     *
     * This method is called soon after the allocator is constructed and before any output
     * is generated. Therefore is may process any data submitted and do other tasks.
     * It should not generate any output. However if it does so, the output is rendered
     * using the method {@link moodle_mod_workshop_renderer::allocation_init_result()}
     *
     * @throws moodle_exception
     * @return void|string
     */
    public function init();

    /**
     * Print HTML to be displayed as the user interface
     *
     * If a form is part of the UI, the caller should have called $PAGE->set_url(...)
     *
     * @param stdclass $wsoutput workshop module renderer can be used
     * @return string HTML code to be echoed
     */
    public function ui();
}
