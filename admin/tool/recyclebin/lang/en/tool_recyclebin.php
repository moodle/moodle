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
 * English strings for tool_recyclebin.
 *
 * @package    tool_recyclebin
 * @copyright  2015 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['alertdeleted'] = '\'{$a->name}\' has been deleted.';
$string['alertemptied'] = 'Recycle bin has been emptied.';
$string['alertrestored'] = '\'{$a->name}\' has been restored.';
$string['autohide'] = 'Auto hide';
$string['autohide_desc'] = 'Automatically hides the recycle bin link when the bin is empty.';
$string['categorybinenable'] = 'Enable category recycle bin';
$string['categorybinexpiry'] = 'Course lifetime';
$string['categorybinexpiry_desc'] = 'How long should a deleted course remain in the recycle bin?';
$string['coursebinenable'] = 'Enable course recycle bin';
$string['coursebinexpiry'] = 'Item lifetime';
$string['coursebinexpiry_desc'] = 'How long should a deleted item remain in the recycle bin?';
$string['datedeleted'] = 'Date deleted';
$string['deleteall'] = 'Delete all';
$string['deleteallconfirm'] = 'Are you sure you want to delete all items from the recycle bin?';
$string['deleteconfirm'] = 'Are you sure you want to delete the selected item from the recycle bin?';
$string['deleteexpirywarning'] = 'Contents will be permanently deleted after {$a}.';
$string['eventitemcreated'] = 'Item created';
$string['eventitemcreated_desc'] = 'Item created with ID {$a->objectid}.';
$string['eventitemdeleted'] = 'Item deleted';
$string['eventitemdeleted_desc'] = 'Item with ID {$a->objectid} deleted.';
$string['eventitemrestored'] = 'Item restored';
$string['eventitemrestored_desc'] = 'Item with ID {$a->objectid} restored.';
$string['invalidcontext'] = 'Invalid context supplied.';
$string['noitemsinbin'] = 'There are no items in the recycle bin.';
$string['notenabled'] = 'Sorry, but the recycle bin has been disabled by the administrator.';
$string['pluginname'] = 'Recycle bin';
$string['taskcleanupcategorybin'] = 'Cleanup category recycle bin';
$string['taskcleanupcoursebin'] = 'Cleanup course recycle bin';
$string['recyclebin:deleteitems'] = 'Delete recycle bin items';
$string['recyclebin:restoreitems'] = 'Restore recycle bin items';
$string['recyclebin:viewitems'] = 'View recycle bin items';
$string['privacy:metadata'] = 'The Recycle bin plugin does not store any personal data.';
