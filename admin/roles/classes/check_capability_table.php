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

/**
 * Subclass of core_role_capability_table_base for use on the Check permissions page.
 *
 * We have one additional column, Allowed, which contains yes/no.
 */
class core_role_check_capability_table extends core_role_capability_table_base {
    protected $user;
    protected $fullname;
    protected $contextname;
    protected $stryes;
    protected $strno;
    private $hascap;

    /**
     * Constructor
     * @param object $context the context this table relates to.
     * @param object $user the user we are generating the results for.
     * @param string $contextname $context->get_context_name() - to save recomputing.
     */
    public function __construct($context, $user, $contextname) {
        parent::__construct($context, 'explaincaps');
        $this->user = $user;
        $this->fullname = fullname($user);
        $this->contextname = $contextname;
        $this->stryes = get_string('yes');
        $this->strno = get_string('no');
    }

    protected function add_header_cells() {
        echo '<th>' . get_string('allowed', 'core_role') . '</th>';
    }

    protected function num_extra_columns() {
        return 1;
    }

    protected function get_row_classes($capability) {
        $this->hascap = has_capability($capability->name, $this->context, $this->user->id);
        if ($this->hascap) {
            return array('yes');
        } else {
            return array('no');
        }
    }

    protected function add_row_cells($capability) {
        if ($this->hascap) {
            $result = $this->stryes;
        } else {
            $result = $this->strno;
        }
        $a = new stdClass;
        $a->fullname = $this->fullname;
        $a->capability = $capability->name;
        $a->context = $this->contextname;
        return '<td>' . $result . '</td>';
    }
}
