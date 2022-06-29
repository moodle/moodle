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
 * @package assignment_offline
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * backup subplugin class that provides the necessary information
 * needed to backup one assignment->offline subplugin.
 *
 * Note: Offline assignments really haven't any special subplugin
 * information to backup/restore, hence code below is skipped (return false)
 * but it's a good example of sublugins supported at different
 * elements (assignment and submission) and conditions
 */
class backup_assignment_offline_subplugin extends backup_subplugin {

    /**
     * Returns the subplugin information to attach at assignment element
     */
    protected function define_assignment_subplugin_structure() {

        return false; // This subplugin backup is only one example. Skip it.

        /**
         * Any activity sublugins is always rooted by one backup_subplugin_element()
         * Those elements have some unique characteristics:
         *  - They are, basically, backup_nested_elements
         *  - They cannot have attributes
         *  - They don't have XML representations (only their final/child elements have
         *  - They are able to specify one condition in order to decide if the subplugin
         *    must be processed or no (usually we'll put the "type" condition here, but some
         *    activities, may prefer not to use any condition, see workshop)
         */

        /**
         * Here we are defining the information that will be attached, within the "assignment" element
         * when assignments of type "offline" are sent to backup, so we define the backup_subplugin_element
         * as not having any final element (null) and with the condition of the '/assignment/assignmenttype'
         * being 'offline' (that will be checked on execution)
         *
         * Note that, while, we allow direct "injection" of final_elements at the "assignment" level (without
         * any nesting, we usually pass 'null', and later enclose the real subplugin information into deeper
         * levels (get_recommended_name() and 'config' in the example below). That will make things
         * on restore easier, as far as subplugin information will be clearly separated from module information.
         */
        $subplugin = $this->get_subplugin_element(null, '/assignment/assignmenttype', 'offline');

        /**
         * Here we define the real structure the subplugin is going to generate - see note above. Obviously the
         * example below hasn't sense at all, we are exporting the whole config table that is 100% unrelated
         * with assignments. Take it as just one example. The only important bit is that it's highly recommended to
         * use some exclusive name in the main nested element (something that won't conflict with other subplugins/parts).
         * So we are using 'subplugin_assignment_offline_assignment' as name here (the type of the subplugin, the name of the
         * subplugin and the name of the connection point). get_recommended_name() will help, in any case ;-)
         *
         * All the code below is 100% standard backup structure code, so you define the structure, the sources,
         * annotations... whatever you need
         */
        $assassoff = new backup_nested_element($this->get_recommended_name());
        $config = new backup_nested_element('config', null, array('name', 'value'));

        $subplugin->add_child($assassoff);
        $assassoff->add_child($config);

        $config->set_source_table('config', array('id' => '/assignment/id'));

        return $subplugin; // And we return the root subplugin element
    }

    /**
     * Returns the subplugin information to attach at submission element
     */
    protected function define_submission_subplugin_structure() {

        return false; // This subplugin backup is only one example. Skip it.

        // remember this has not XML representation
        $subplugin = $this->get_subplugin_element(null, '/assignment/assignmenttype', 'offline');

        // type of the subplugin, name of the subplugin and name of the connection point (recommended)
        $asssuboff = new backup_nested_element($this->get_recommended_name());
        // Why 'submission_config' name? Because it must be unique in the hierarchy and we
        // already are using 'config' above withing the same file
        $config = new backup_nested_element('submission_config', null, array('name', 'value'));

        $subplugin->add_child($asssuboff);
        $asssuboff->add_child($config);

        $config->set_source_table('config', array('id' => backup::VAR_PARENTID));

        return $subplugin; // And we return the root subplugin element
    }
}
