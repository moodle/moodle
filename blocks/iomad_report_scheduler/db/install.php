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
// This script is run after the report_scheduler block has been installed.

function xmldb_block_iomad_report_scheduler_install() {
    /*    global $SITE;

    // If it doesn't exist add the iomad_report_scheduler block to the front page.
    $page = new moodle_page();
    $page->set_course( $SITE );
    $page->set_pagetype( 'site-index' );
    $page->blocks->add_regions( array(BLOCK_POS_RIGHT) );
    $page->blocks->add_block( 'iomad_report_scheduler', BLOCK_POS_RIGHT, 0, false );
    */
    return true;
}
