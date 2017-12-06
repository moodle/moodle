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
 * Provides the classes used internally in the plugin
 *
 * @package     report_overviewstats
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Need to load the base classes first so we can extend them
require_once($CFG->dirroot.'/report/overviewstats/classes/chart.php');
require_once($CFG->dirroot.'/report/overviewstats/classes/manager.php');

// Load all classes files (to be replaced by autoloading in Moodle 2.6
$classfiles = new DirectoryIterator($CFG->dirroot.'/report/overviewstats/classes/');
foreach ($classfiles as $classfile) {
    if ($classfile->isDot()) {
        continue;
    }
    if ($classfile->isLink()) {
        throw new coding_exception('Unexpected symlink in report/overviewstats/classes/');
    }
    if ($classfile->isFile() and substr($classfile->getFilename(), -4) === '.php') {
        require_once($classfile->getPathname());
    }
}
