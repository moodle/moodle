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
 * iomad - admin settings
 *
 * @package    iomad
 * @copyright  2011 onwards E-Learn Design Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once( 'menu.php' );

// Set up the $USER->company variable if its not already done.
if (empty($USER->company) && !empty($USER)) {
    require_once($CFG->dirroot."/local/iomad/lib/iomad.php");
    iomad::load_company();
}

// Basic navigation settings
require($CFG->dirroot . '/local/iomad/lib/basicsettings.php');

$ADMIN->add( 'iomad', new admin_externalpage( 'Dashboard', get_string('name', 'local_iomad_dashboard'),
    new moodle_url('/local/iomad_dashboard/index.php'), 'local/iomad_dashboard:view'));

// Get all the links from the iomad_admin_menu.
$adminmenu = new iomad_admin_menu();
$menus = $adminmenu->getmenu();
foreach ($menus as $tag => $menu) {
    if (substr($menu['url'], 0, 1) == '/') {
        $url = new moodle_url( $menu['url'] );
    } else {
        $url = new moodle_url( '/blocks/iomad_company_admin/'.$menu['url'] );
    }
    $ADMIN->add( $menu['category'], new admin_externalpage( $tag, $menu['name'], $url,
                 $menu['cap']));
}
