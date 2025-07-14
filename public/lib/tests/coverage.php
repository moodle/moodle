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

defined('MOODLE_INTERNAL') || die();

/**
 * Coverage information for the core subsystem.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Coverage information for the core subsystem.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
return new class extends phpunit_coverage_info {
    /** @var array The list of folders relative to the plugin root to include in coverage generation. */
    protected $includelistfolders = [
        // This is a legacy hangup which relates to parts of the file storage API being placed in the wrong location.
        'filestorage',
    ];

    /** @var array The list of files relative to the plugin root to include in coverage generation. */
    protected $includelistfiles = [
        'accesslib.php',
        'adminlib.php',
        'authlib.php',
        'badgeslib.php',
        'blocklib.php',
        'boxlib.php',
        'clilib.php',
        'completionlib.php',
        'conditionlib.php',
        'configonlylib.php',
        'cronlib.php',
        'csslib.php',
        'customcheckslib.php',
        'datalib.php',
        'ddllib.php',
        'deprecatedlib.php',
        'dmllib.php',
        'dtllib.php',
        'editorlib.php',
        'enrollib.php',
        'environmentlib.php',
        'externallib.php',
        'filelib.php',
        'filterlib.php',
        'flickrlib.php',
        'formslib.php',
        'gdlib.php',
        'gradelib.php',
        'graphlib.php',
        'grouplib.php',
        'installlib.php',
        'jslib.php',
        'ldaplib.php',
        'licenselib.php',
        'listlib.php',
        'mathslib.php',
        'messagelib.php',
        'modinfolib.php',
        'moodlelib.php',
        'myprofilelib.php',
        'navigationlib.php',
        'oauthlib.php',
        'outputlib.php',
        'pagelib.php',
        'pdflib.php',
        'phpminimumversionlib.php',
        'plagiarismlib.php',
        'portfoliolib.php',
        'questionlib.php',
        'resourcelib.php',
        'rsslib.php',
        'searchlib.php',
        'sessionlib.php',
        'setuplib.php',
        'statslib.php',
        'tablelib.php',
        'upgradelib.php',
        'webdavlib.php',
        'weblib.php',
        'xsendfilelib.php',
    ];

    /** @var array The list of folders relative to the plugin root to exclude from coverage generation. */
    protected $excludelistfolders = [
        'filestorage/tests',
    ];
};
