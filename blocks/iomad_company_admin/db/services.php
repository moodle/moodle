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
 * @package    Block IOMAD Company Admin
 * @copyright  2017 onwards E-Learn Design Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Define an Iomad service
$services = array(
    'iomadservice' => array(
        'functions' => array(
            'block_iomad_company_admin_create_companies',
            'block_iomad_company_admin_get_companies',
            'block_iomad_company_admin_edit_companies',
        ),
        'requiredcapability' => '',
        'restrictusers' => 1,
        'enabled' => 1,
    )
);

// Define the web service funtions
$functions = array(
    'block_iomad_company_admin_create_companies' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'create_companies',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Create new Iomad companies',
        'type' => 'write',
    ),
    'block_iomad_company_admin_get_companies' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'get_companies',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Get all Iomad companies',
        'type' => 'read',
    ),
    'block_iomad_company_admin_edit_companies' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'edit_companies',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Edit Iomad companies',
        'type' => 'write',
    ),
);
