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
 * Strings for tool_behat
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['aim'] = 'This administration tool helps developers and test writers to create .feature files describing Moodle\'s functionalities and run them automatically.';
$string['allavailablesteps'] = 'All the available steps definitions';
$string['giveninfo'] = 'Given. Processes to set up the environment';
$string['infoheading'] = 'Info';
$string['installinfo'] = 'Read {$a} for installation and tests execution info';
$string['moreinfoin'] = 'More info in {$a}';
$string['newstepsinfo'] = 'Read {$a} for info about how to add new steps definitions';
$string['newtestsinfo'] = 'Read {$a} for info about how to write new tests';
$string['nostepsdefinitions'] = 'There aren\'t steps definitions matching this filters';
$string['pluginname'] = 'Acceptance testing';
$string['runclitool'] = 'To list the steps definitions you need to run the Behat CLI tool to create the $CFG->behat_dataroot directory. Go to your moodle dirroot and run "{$a}"';
$string['stepsdefinitionscomponent'] = 'Area';
$string['stepsdefinitionscontains'] = 'Contains';
$string['stepsdefinitionsfilters'] = 'Steps definitions';
$string['stepsdefinitionstype'] = 'Type';
$string['theninfo'] = 'Then. Checkings to ensure the outcomes are the expected ones';
$string['viewsteps'] = 'Filter';
$string['wheninfo'] = 'When. Actions that provokes an event';
$string['wrongbehatsetup'] = 'Something is wrong with behat setup, ensure:<ul>
<li>You ran "php admin/tool/behat/cli/init.php" from your moodle root directory</li>
<li>vendor/bin/behat file has execution permissions</li></ul>';
