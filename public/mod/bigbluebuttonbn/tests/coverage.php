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
 * Coverage information for the mod_bigbluebuttonbn component.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class bbb_coverage information for the core subsystem.
 *
 * Note that we had to change the definition of this class due to a bug in local_moodlecheck
 * https://github.com/moodlehq/moodle-local_moodlecheck/issues/50
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class bbb_coverage extends phpunit_coverage_info {
    /** @var array The list of folders relative to the plugin root to includelist in coverage generation. */
    protected $includelistfolders = [
        'classes',
        'backup',
    ];

    /** @var array The list of files relative to the plugin root to includelist in coverage generation. */
    protected $includelistfiles = [
        'lib.php',
    ];

    /** @var array The list of folders relative to the plugin root to excludelist in coverage generation. */
    protected $excludelistfolders = [];

    /** @var array The list of files relative to the plugin root to excludelist in coverage generation. */
    protected $excludelistfiles = [];
}
return new bbb_coverage;
