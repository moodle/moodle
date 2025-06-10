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
 * Strings for component 'feedback', language 'en_us', version '4.1'.
 *
 * @package     feedback
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['depending_help'] = 'It is possible to show an item depending on the value of another item.<br />
<strong>Here is an example.</strong><br />
<ul>
<li>First, create an item on which another item will depend on.</li>
<li>Next, add a pagebreak.</li>
<li>Then add the items dependant on the value of the item created before. Choose the item from the list labelled "Dependence item" and write the required value in the textbox labelled "Dependence value".</li>
</ul>
<strong>The item structure should look like this.</strong>
<ol>
<li>Item Q: Do you have a car? A: yes/no</li>
<li>Pagebreak</li>
<li>Item Q: What color is your car?<br />
(this item depends on item 1 with value = yes)</li>
<li>Item Q: Why don\'t you have a car?<br />
(this item depends on item 1 with value = no)</li>
<li> ... other items</li>
</ol>';
$string['do_not_analyse_empty_submits'] = 'Do not analyze empty submits';
