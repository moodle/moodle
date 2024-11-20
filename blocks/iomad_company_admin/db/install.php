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
// This script is run after the dashboard has been installed.

/**
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_block_iomad_company_admin_install() {
    global $SITE;
    global $DB;

    // Add admin block to default dashboard
    // yes, I know this isn't really what this is for!!
    $systemcontext = context_system::instance();
    $page = new moodle_page();
    $page->set_context( $systemcontext );
    $page->set_pagetype( 'my-index' );
    $page->set_pagelayout( 'mydashboard' );
    $page->blocks->add_region('content');
    $defaultblocks = array(
        'content' => array('iomad_company_admin'),
        );
    $page->blocks->add_blocks($defaultblocks);

    return true;
}
