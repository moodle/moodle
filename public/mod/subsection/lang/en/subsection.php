<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     mod_subsection
 * @category    string
 * @copyright   2023 Amaia Anabitarte <amaia@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['cleandescriptionsdetail'] = 'Subsection pages and descriptions are no longer supported in Moodle 5.2.
<p>This site has <strong>{$a->count} subsection descriptions</strong> that are no longer visible to users.</p>
<p>You can choose to:
<ul>
  <li><strong>Migrate these descriptions to Text and Media areas</strong>.<br/> The Text and media areas will be displayed at the top of each subsection.<br/>{$a->migratelink}<br/><br/>
  </li>
  <li><strong>Delete these descriptions permanently</strong>.<br/> This will completely erase descriptions from the database.<br/>{$a->deletelink}
  </li>
</ul>
</p>';
$string['deleteconfirmbutton'] = 'Delete all descriptions';
$string['deleteconfirmtext'] = 'This will permanently delete {$a} subsection descriptions from the database.<br/><br/>You can\'t undo this. Are you sure you want to delete all descriptions?';
$string['deleteconfirmtitle'] = 'Delete all subsection descriptions?';
$string['deletelinktext'] = 'Delete descriptions';
$string['descriptionsdeletedpending'] = 'Subsection descriptions waiting to be deleted: <strong>{$a}</strong>';
$string['descriptionsdeletedsuccess'] = '<strong>The removal task for all subsection descriptions has been created</strong>. This task will run in the background and may take a few minutes.';
$string['descriptionsmigratedpending'] = 'Subsection descriptions waiting to be migrated: <strong>{$a}</strong>';
$string['descriptionsmigratedsuccess'] = '<strong>The migration task for all subsection descriptions has been created</strong>. This task will run in the background and may take a few minutes.';
$string['invalidaction'] = 'Invalid action specified.';
$string['migrateconfirmbutton'] = 'Migrate all descriptions';
$string['migrateconfirmtext'] = 'This will migrate {$a} subsection descriptions to Text and Media areas.<br/><br/>You can\'t undo this. Are you sure you want to migrate all descriptions?';
$string['migrateconfirmtitle'] = 'Migrate all subsection descriptions?';
$string['migratelinktext'] = 'Migrate descriptions';
$string['modulename'] = 'Subsection';
$string['modulenameplural'] = 'Subsections';
$string['pluginadministration'] = 'Subsection administration';
$string['pluginname'] = 'Subsection';
$string['privacy:metadata'] = 'Subsection does not store any personal data';
$string['quickcreatename'] = 'New subsection';
$string['subsection:addinstance'] = 'Add subsection';
$string['subsection:view'] = 'View subsection';
$string['subsectionname'] = 'Name';
