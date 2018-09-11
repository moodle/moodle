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
 * Simple clock block language strings
 *
 * @package    contrib
 * @subpackage block_simple_clock
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Clock strings...
$string['after_noon'] = 'pm';
$string['before_noon'] = 'am';
$string['clock_separator'] = ':';

// Config strings and help...
$string['clock_title_default'] = 'Clock';
$string['config_clock_visibility'] = 'Visible clocks';
$string['config_clock_visibility_help'] = '<p>With this setting, you can control whether the user will see:</p>
<ul>
    <li>time on the server,</li>
    <li>time on their own machine, or</li>
    <li>both.</li>
</ul>';
$string['config_header'] = 'Show header';
$string['config_header_help'] = '
<p>With this setting, you can control whether the block header, including the title, will be shown.</p>
<p style="background:yellow;border:3px dashed black;padding:10px;"><strong>Note</strong><br />
While "Editing" is turned on, the header of the block will be shown to teachers/administrators.
When "Editing" is turned off, the header will be hidden.
While this setting set to No, students will not see the header, regardless of whether "Editing" is on or off.</p>
';
$string['config_icons'] = 'Show icons';
$string['config_icons_help'] = '
<p>With this setting, you can control whether icons are shown next to each clock label.</p>
<p>The site icon is shown next to the "server" label. The user\'s icon image is shown next to their clock.</p>
';
$string['config_day'] = 'Show day name';
$string['config_day_help'] = '
<p>Showing the day name adds additional information for students who may be in a timezone a day earlier or later.</p>
';
$string['config_seconds'] = 'Show seconds';
$string['config_seconds_help'] = '
<p>Showing seconds will show users the time including seconds on all visible clocks.</p>
<p style="background:yellow;border:3px dashed black;padding:10px;"><strong>Warning</strong><br />
The initial time shown is the time when the page was created.
When this page content arrives at the user\'s browser, the time difference will include a delay
(usually a few seconds). This is often acceptable as any interaction with Moodle will
involve a delay, but the delay will be more evident with seconds shown.</p>
';
$string['config_show_both_clocks'] = 'Show clocks for both server and user';
$string['config_show_server_clock'] = 'Show clock for server only';
$string['config_show_user_clock'] = 'Show clock for user only';
$string['config_title'] = 'Alternate title';
$string['config_title_help'] = '
<p>This setting allows the block title to be changed.</p>
<p>If no alternate is provided, the default title is used.</p>
<p>If the block header is hidden, the title will not appear.</p>
';
$string['config_twenty_four_hour_time'] = 'Show 24hr time';

// Other strings...
$string['day_names'] = 'Sun,Mon,Tue,Wed,Thu,Fri,Sat'; // Preserve this format and don't add spaces.
$string['javascript_disabled'] = 'To allow the clocks to update, enable JavaScript in your browser.';
$string['loading'] = 'Loading...';
$string['pluginname'] = 'Simple Clock';
$string['server'] = 'Server';
$string['simple_clock:myaddinstance'] = 'Add a new Simple Clock block to the My Moodle page';
$string['simple_clock:addinstance'] = 'Add a new Simple Clock block';
$string['you'] = 'You';