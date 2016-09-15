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
 * Language strings for media embedding.
 * @package core
 * @subpackage media
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['audioextensions'] = 'Audio: {$a}';
$string['defaultwidth'] = 'Default width';
$string['defaultwidthdesc'] = 'Default width of video or other embedded object if no width is specified and player is not able to pick the real video width';
$string['defaultheight'] = 'Default height';
$string['defaultheightdesc'] = 'Default height of video or other embedded object if no height is specified and player is not able to pick the real video height';
$string['extensions'] = 'Extensions: {$a}';
$string['managemediaplayers'] = 'Manage media players';
$string['mediaformats'] = 'Available players';
$string['mediaformats_desc'] = 'When players are enabled in these settings, files can be embedded using the media filter (if enabled) or using a File or URL resources with the Embed option. When not enabled, these formats are not embedded and users can manually download or follow links to these resources.

Where two players support the same format, enabling both increases compatibility across different devices such as mobile phones. It is possible to increase compatibility further by providing multiple files in different formats for a single audio or video clip.';
$string['supports'] = 'Supports';
$string['videoextensions'] = 'Video: {$a}';

// Deprecated since Moodle 3.2.
$string['mediasettings'] = 'Media embedding';
$string['legacyheading'] = 'Legacy media players';
$string['legacyheading_desc'] = 'These players are not frequently used on the Web and require browser plugins that are less widely installed.';
