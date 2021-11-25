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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$capabilities = array(

    'block/iomad_microlearning:addinstance' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK
    ),

    'block/iomad_microlearning:myaddinstance' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK
    ),

    'block/iomad_microlearning:view' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_microlearning:thread_clone' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_microlearning:edit_threads' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_microlearning:import_threads' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_microlearning:edit_nuggets' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_microlearning:thread_delete' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_microlearning:thread_view' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_microlearning:assign_threads' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_microlearning:manage_groups' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_microlearning:importgroupfromcsv' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

);


