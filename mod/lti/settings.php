<?php
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file defines the global basiclti administration form
 *
 * @package lti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    $str = '';

    $types = lti_filter_get_types();
    if (!empty($types)) {
        $str .= '<h4 class="main"><a href="'.$CFG->wwwroot.'/mod/lti/typessettings.php?action=add&amp;sesskey='.$USER->sesskey.'">'.get_string('addtype', 'lti').'</a></h4>';
        $str .= '<table>';

        foreach ($types as $type) {
            $str .= '<tr>'.
            '<td>'.$type->name.'</td>'.
            '<td align="center"><a class="editing_update" href="'.$CFG->wwwroot.'/mod/lti/typessettings.php?action=update&amp;id='.$type->id.'&amp;sesskey='.$USER->sesskey.'" title="Update">'.
            '<img class="iconsmall" alt="Update" src="'.$CFG->wwwroot.'/pix/t/edit.gif"/></a>'.'&nbsp;&nbsp;'.
            '<a class="editing_delete" href="'.$CFG->wwwroot.'/mod/lti/typessettings.php?action=delete&amp;id='.$type->id.'&amp;sesskey='.$USER->sesskey.'" title="Delete">'.
            '<img class="iconsmall" alt="Delete" src="'.$CFG->wwwroot.'/pix/t/delete.gif"/>'.
            '</a>'.
            '</td>'.
            '</tr>';

        }
        $str .= '</table>';
    } else {
        $str .= '<center>';
        $str .= '<h4 class="main"><a href="'.$CFG->wwwroot.'/mod/lti/typessettings.php?action=add&amp;sesskey='.$USER->sesskey.'">'.get_string('addtype', 'lti').'</a></h4>';
        $str .= get_string('notypes', 'lti');
        $str .= '</center>';
    }


    $settings->add(new admin_setting_heading('lti_types', get_string('configuredtools', 'lti'), $str));
    
}
