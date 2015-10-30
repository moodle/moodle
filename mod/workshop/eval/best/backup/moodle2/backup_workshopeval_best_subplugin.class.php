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
 * @package    workshopeval
 * @subpackage best
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Provides the information to backup grading evaluation method 'Comparison with the best assessment'
 *
 * This evaluator just stores a single integer value - the recently used comparison
 * strictness factor. It adds its XML data to workshop tag.
 */
class backup_workshopeval_best_subplugin extends backup_subplugin {

    /**
     * Returns the subplugin information to attach to workshop element
     */
    protected function define_workshop_subplugin_structure() {

        // create XML elements
        $subplugin = $this->get_subplugin_element(); // virtual optigroup element
        $subplugin_wrapper = new backup_nested_element($this->get_recommended_name());
        $subplugin_table_settings = new backup_nested_element('workshopeval_best_settings', null, array('comparison'));

        // connect XML elements into the tree
        $subplugin->add_child($subplugin_wrapper);
        $subplugin_wrapper->add_child($subplugin_table_settings);

        // set source to populate the data
        $subplugin_table_settings->set_source_table('workshopeval_best_settings', array('workshopid' => backup::VAR_ACTIVITYID));

        return $subplugin;
    }
}
