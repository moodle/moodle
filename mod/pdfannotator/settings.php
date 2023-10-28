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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die; // Prevents crashes on misconfigured production server.

if ($ADMIN->fulltree) {
    require_once('constants.php');
    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/usevotes',
            get_string('global_setting_usevotes', 'pdfannotator'), get_string('global_setting_usevotes_desc', 'pdfannotator'), 1));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/useprint',
            get_string('global_setting_useprint', 'pdfannotator'), get_string('global_setting_useprint_desc', 'pdfannotator'), 0));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/useprintcomments',
            get_string('global_setting_useprint_comments', 'pdfannotator'),
        get_string('global_setting_useprint_comments_desc', 'pdfannotator'), 0));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/use_studenttextbox',
            get_string('global_setting_use_studenttextbox', 'pdfannotator'),
            get_string('global_setting_use_studenttextbox_desc', 'pdfannotator'), 0));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/use_studentdrawing',
            get_string('global_setting_use_studentdrawing', 'pdfannotator'),
            get_string('global_setting_use_studentdrawing_desc', 'pdfannotator'), 0));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/use_private_comments',
            get_string('global_setting_use_private_comments', 'pdfannotator'),
            get_string('global_setting_use_private_comments_desc', 'pdfannotator'), 0));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/use_protected_comments',
            get_string('global_setting_use_protected_comments', 'pdfannotator'),
            get_string('global_setting_use_protected_comments_desc', 'pdfannotator'), 0));

    // Define what API to use for converting latex formulas into png.
    $options = array();
    $options[LATEX_TO_PNG_MOODLE] = get_string("global_setting_latexusemoodle", "pdfannotator");
    $options[LATEX_TO_PNG_GOOGLE_API] = get_string("global_setting_latexusegoogle", "pdfannotator");
    $settings->add(new admin_setting_configselect('mod_pdfannotator/latexapi', get_string('global_setting_latexapisetting',
        'pdfannotator'),
        get_string('global_setting_latexapisetting_desc', 'pdfannotator'), LATEX_TO_PNG_MOODLE, $options));
    
    $name = new lang_string('global_setting_attobuttons', 'pdfannotator');
    $desc = new lang_string('global_setting_attobuttons_desc', 'pdfannotator');
    $default = 'collapse = collapse
style1 = bold, italic, underline
list = unorderedlist, orderedlist
insert = link
other = html
style2 = strike, subscript, superscript
font = fontfamily, fontsize
indent = indent, align
extra = equation, matrix, chemistry, charmap
undo = undo, image
screen = fullscreen';
    $setting = new admin_setting_configtextarea('mod_pdfannotator/attobuttons', $name, $desc, $default);
    $settings->add($setting);

    if (isset($CFG->maxbytes)) {

        $name = new lang_string('maximumfilesize', 'pdfannotator');
        $description = new lang_string('configmaxbytes', 'pdfannotator');
    
        $maxbytes = get_config('pdfannotator', 'maxbytes');
        $element = new admin_setting_configselect('mod_pdfannotator/maxbytes',
                                                  $name,
                                                  $description,
                                                  $CFG->maxbytes,
                                                  get_max_upload_sizes($CFG->maxbytes, 0, 0, $maxbytes));
        $settings->add($element);
    }

}
