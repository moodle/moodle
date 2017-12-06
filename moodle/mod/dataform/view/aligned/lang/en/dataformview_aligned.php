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
 * @package dataformview_aligned
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Aligned';
$string['aligned:addinstance'] = 'Add a new Aligned dataformview';
$string['entrytemplate'] = 'Entry template';
$string['entrytemplate_help'] = 'The entry template of the Aligned view is a simplified definition of a table row. It consists of a list of column definitions, each column definition in a new line. The column definition format: fieldpattern|column header (optional)|cell css class (optional). For example, the following definition will display the entries in a headerless table with 3 columns and the specified field patterns in order:
<p>
[[Name]]<br />
[[Email]]<br />
[[Message]]<br />
</p>
The following definition will display the entries in a table with 5 columns and a header row with header titles in the first 3 columns:
<p>
[[Name]]|Name<br />
[[Email]]|Email<br />
[[Message]]|Message<br />
[[EAC:edit]]]<br />
[[EAC:delete]]<br />
</p>.';
