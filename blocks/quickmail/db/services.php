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
 * ************************************************************************
 *                            QuickMail
 * ************************************************************************
 * @package    block - Quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Update by David Lowe
 */

defined('MOODLE_INTERNAL') || die();

// We defined the web service functions to install.
$functions = array (
    'block_quickmail_qm_ajax' => array (
        'classname' => 'block_quickmail_external',
        'methodname' => 'qm_ajax',
        'classpath' => 'blocks/quickmail/externallib.php',
        'description' => 'Simple PUT to update sent messages',
        'type' => 'write',
        'ajax' => true
    ),
);

// We define the services to install as pre-build services.
// A pre-build service is not editable by administrator.
$services = array (
    'Quickmail Service' => array (
        'functions' => array (
            'block_quickmail_qm_ajax'
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);
