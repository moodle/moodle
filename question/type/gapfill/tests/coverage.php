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
 * Configuration for the generation of phpunit coverate report
 * @package    qtype_gapfill
 * @copyright  2021 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
return new class extends phpunit_coverage_info {
    /**
     * Default to cover code in all folders
     * relative to root
     *
     * @var array
     */
    protected $includelistfolders = [
        '.'
    ];

    /**
     * Definitly generate unit test coverage of this file
     *
     * @var array
     */
    protected $includelistfiles = [
        'questiontype.php',
        'qtype_gapfill'
    ];

    /**
     * Don't check unit test coverage of files in these folders.
     * For example it makes little sense to have
     * unit tests for a lang file which has no functions
     *
     * @var array
     */
    protected $excludelistfolders = [
        'db',
        'lang',
        'tests'
    ];

    /**
     * No point in checking coverage of these files
     *
     * @var array
     */
    protected $excludelistfiles = [
        'settings.php',
        'version.php'
    ];
};
