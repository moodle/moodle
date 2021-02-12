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
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$capabilities = array(

    'block/iomad_commerce:addinstance' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK
    ),

    'block/iomad_commerce:myaddinstance' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK
    ),

    'block/iomad_commerce:admin_view' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_commerce:add_course' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_commerce:edit_course' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_commerce:hide_course' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_commerce:buyitnow' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM
    ),

    'block/iomad_commerce:delete_course' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    )

);


