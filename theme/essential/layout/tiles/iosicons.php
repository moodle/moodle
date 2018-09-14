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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if (\theme_essential\toolbox::get_setting('iphoneicon')) {
    $iphoneicon = \theme_essential\toolbox::get_setting('iphoneicon', 'format_file_url');
} else {
    $iphoneicon = $OUTPUT->pix_url('homeicon/iphone', 'theme');
}
if (\theme_essential\toolbox::get_setting('ipadicon')) {
    $ipadicon = \theme_essential\toolbox::get_setting('ipadicon', 'format_file_url');
} else {
    $ipadicon = $OUTPUT->pix_url('homeicon/ipad', 'theme');
}
if (\theme_essential\toolbox::get_setting('iphoneretinaicon')) {
    $iphoneretinaicon = \theme_essential\toolbox::get_setting('iphoneretinaicon', 'format_file_url');
} else {
    $iphoneretinaicon = $OUTPUT->pix_url('homeicon/iphone_retina', 'theme');
}
if (\theme_essential\toolbox::get_setting('ipadretinaicon')) {
    $ipadretinaicon = \theme_essential\toolbox::get_setting('ipadretinaicon', 'format_file_url');
} else {
    $ipadretinaicon = $OUTPUT->pix_url('homeicon/ipad_retina', 'theme');
}
?>

<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $iphoneicon ?>"/>
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $ipadicon ?>"/>
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $iphoneretinaicon ?>"/>
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $ipadretinaicon ?>"/>