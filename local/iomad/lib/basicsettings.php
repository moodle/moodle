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

// This file is called from all Iomad local plugins that add settings
// to the navigation menu. It ensures that the basic category structure
// is set up, as the order in which local plugins are called cannot be
// relied upon

/**
 * @package   local_iomad
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (is_null($ADMIN->locate('iomad'))) {
    $ADMIN->add( 'root', new admin_category( 'iomad', get_string('iomad', 'local_iomad')));
}

if (is_null($ADMIN->locate('IomadReports'))) {
    $ADMIN->add( 'iomad', new admin_category( 'IomadReports',
              get_string('iomadreports', 'block_iomad_company_admin')));
}

if (is_null($ADMIN->locate('CompanyAdmin'))) {
    $ADMIN->add( 'iomad', new admin_category( 'CompanyAdmin',
              get_string('companymanagement', 'block_iomad_company_admin')));
}

if (is_null($ADMIN->locate('UserAdmin'))) {
    $ADMIN->add( 'iomad', new admin_category( 'UserAdmin',
              get_string('usermanagement', 'block_iomad_company_admin')));
}

if (is_null($ADMIN->locate('CourseAdmin'))) {
    $ADMIN->add( 'iomad', new admin_category( 'CourseAdmin',
              get_string('coursemanagement', 'block_iomad_company_admin')));
}

if (is_null($ADMIN->locate('LicenseAdmin'))) {
    $ADMIN->add( 'iomad', new admin_category( 'LicenseAdmin',
             get_string('licensemanagement', 'block_iomad_company_admin')));
}

if (is_null($ADMIN->locate('CompetencyAdmin'))) {
    $ADMIN->add( 'iomad', new admin_category( 'CompetencyAdmin',
             get_string('competencymanagement', 'block_iomad_company_admin')));
}

if (is_null($ADMIN->locate('ECommerceAdmin'))) {
    $ADMIN->add( 'iomad', new admin_category( 'ECommerceAdmin',
             get_string('blocktitle', 'block_iomad_commerce')));
}
