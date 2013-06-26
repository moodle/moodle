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
 * Library code used by the roles administration interfaces.
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class core_role_define_role_table_basic extends core_role_define_role_table_advanced {
    protected $stradvmessage;
    protected $strallow;

    public function __construct($context, $roleid) {
        parent::__construct($context, $roleid);
        $this->displaypermissions = array(CAP_ALLOW => $this->allpermissions[CAP_ALLOW]);
        $this->stradvmessage = get_string('useshowadvancedtochange', 'core_role');
        $this->strallow = $this->strperms[$this->allpermissions[CAP_ALLOW]];
    }

    protected function print_show_hide_advanced_button() {
        echo '<div class="advancedbutton">';
        echo '<input type="submit" name="toggleadvanced" value="' . get_string('showadvanced', 'form') . '" />';
        echo '</div>';
    }

    protected function add_permission_cells($capability) {
        $perm = $this->permissions[$capability->name];
        $permname = $this->allpermissions[$perm];
        $defaultperm = $this->allpermissions[$this->parentpermissions[$capability->name]];
        echo '<td class="' . $permname . '">';
        if ($perm == CAP_ALLOW || $perm == CAP_INHERIT) {
            $checked = '';
            if ($perm == CAP_ALLOW) {
                $checked = 'checked="checked" ';
            }
            echo '<input type="hidden" name="' . $capability->name . '" value="' . CAP_INHERIT . '" />';
            echo '<label><input type="checkbox" name="' . $capability->name .
                '" value="' . CAP_ALLOW . '" ' . $checked . '/> ' . $this->strallow . '</label>';
        } else {
            echo '<input type="hidden" name="' . $capability->name . '" value="' . $perm . '" />';
            echo $this->strperms[$permname] . '<span class="note">' . $this->stradvmessage . '</span>';
        }
        echo '</td>';
    }
}
