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
 * Core cache definitions.
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 * It contains the components that are requried in order to use caching.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$definitions = array(
    'string' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'component' => 'core',
        'area' => 'string',
        'persistent' => true,
        'persistentmaxsize' => 3
    ),
    'databasemeta' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'requireidentifiers' => array(
            'dbfamily'
        ),
        'persistent' => true,
        'persistentmaxsize' => 2
    ),
    'config' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'persistent' => true
    ),
    // Event invalidation cache
    'eventinvalidation' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'persistent' => true,
        'requiredataguarantee' => true
    )
);
