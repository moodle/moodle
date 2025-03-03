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
 * MathJAX filter settings
 *
 * @package    filter_mathjaxloader
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $item = new admin_setting_heading('filter_mathjaxloader/localinstall',
                                      new lang_string('localinstall', 'filter_mathjaxloader'),
                                      new lang_string('localinstall_help', 'filter_mathjaxloader'));
    $settings->add($item);

    $item = new admin_setting_configtext('filter_mathjaxloader/httpsurl',
                                         new lang_string('httpsurl', 'filter_mathjaxloader'),
                                         new lang_string('httpsurl_help', 'filter_mathjaxloader'),
                                         'https://cdn.jsdelivr.net/npm/mathjax@3.2.2/es5/tex-mml-chtml.js',
                                         PARAM_RAW);
    $settings->add($item);

    $item = new admin_setting_configcheckbox('filter_mathjaxloader/texfiltercompatibility',
                                             new lang_string('texfiltercompatibility', 'filter_mathjaxloader'),
                                             new lang_string('texfiltercompatibility_help', 'filter_mathjaxloader'),
                                             0);
    $settings->add($item);

    $item = new admin_setting_configtextarea('filter_mathjaxloader/mathjaxconfig',
                                             new lang_string('mathjaxsettings','filter_mathjaxloader'),
                                             new lang_string('mathjaxsettings_desc', 'filter_mathjaxloader'),
                                             '');

    $settings->add($item);

    $item = new admin_setting_configtext('filter_mathjaxloader/additionaldelimiters',
                                         new lang_string('additionaldelimiters', 'filter_mathjaxloader'),
                                         new lang_string('additionaldelimiters_help', 'filter_mathjaxloader'),
                                         '',
                                         PARAM_RAW);
    $settings->add($item);

}
