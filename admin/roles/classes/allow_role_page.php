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
 * Base class for allow matrices.
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for managing the data in the grid of checkboxes on the role allow
 * allow/overrides/switch editing pages (allow.php).
 */
abstract class core_role_allow_role_page {
    protected $tablename;
    protected $targetcolname;
    protected $roles;
    protected $allowed = null;

    /**
     * Constructor.
     *
     * @param string $tablename the table where our data is stored.
     * @param string $targetcolname the name of the target role id column.
     */
    public function __construct($tablename, $targetcolname) {
        $this->tablename = $tablename;
        $this->targetcolname = $targetcolname;
        $this->load_required_roles();
    }

    /**
     * Load information about all the roles we will need information about.
     */
    protected function load_required_roles() {
        // Get all roles.
        $this->roles = role_fix_names(get_all_roles(), context_system::instance(), ROLENAME_ORIGINAL);
    }

    /**
     * Update the data with the new settings submitted by the user.
     */
    public function process_submission() {
        global $DB;

        $context = context_system::instance();
        $this->load_current_settings();

        // Delete all records, then add back the ones that should be allowed.
        $DB->delete_records($this->tablename);
        foreach ($this->roles as $fromroleid => $notused) {
            foreach ($this->roles as $targetroleid => $alsonotused) {
                $isallowed = $this->allowed[$fromroleid][$targetroleid];
                if (optional_param('s_' . $fromroleid . '_' . $targetroleid, false, PARAM_BOOL)) {
                    $this->set_allow($fromroleid, $targetroleid);
                    // Only trigger events if this role allow relationship did not exist and the checkbox element
                    // has been submitted.
                    if (!$isallowed) {
                        $eventclass = $this->get_eventclass();
                        $eventclass::create([
                            'context' => $context,
                            'objectid' => $fromroleid,
                            'other' => ['targetroleid' => $targetroleid, 'allow' => true]
                        ])->trigger();
                    }
                } else if ($isallowed) {
                    // When the user has deselect an existing role allow checkbox but it is in the list of roles
                    // allowances.
                    $eventclass = $this->get_eventclass();
                    $eventclass::create([
                        'context' => $context,
                        'objectid' => $fromroleid,
                        'other' => ['targetroleid' => $targetroleid, 'allow' => false]
                    ])->trigger();
                }
            }
        }
    }

    /**
     * Set one allow in the database.
     * @param int $fromroleid
     * @param int $targetroleid
     */
    protected abstract function set_allow($fromroleid, $targetroleid);

    /**
     * Load the current allows from the database.
     */
    public function load_current_settings() {
        global $DB;
        // Load the current settings.
        $this->allowed = array();
        foreach ($this->roles as $role) {
            // Make an array $role->id => false. This is probably too clever for its own good.
            $this->allowed[$role->id] = array_combine(array_keys($this->roles), array_fill(0, count($this->roles), false));
        }
        $rs = $DB->get_recordset($this->tablename);
        foreach ($rs as $allow) {
            $this->allowed[$allow->roleid][$allow->{$this->targetcolname}] = true;
        }
        $rs->close();
    }

    /**
     * Is target allowed?
     *
     * @param integer $targetroleid a role id.
     * @return boolean whether the user should be allowed to select this role as a target role.
     */
    protected function is_allowed_target($targetroleid) {
        return true;
    }

    /**
     * Returns structure that can be passed to print_table,
     * containing one cell for each checkbox.
     * @return html_table a table
     */
    public function get_table() {
        $table = new html_table();
        $table->tablealign = 'center';
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->width = '90%';
        $table->align = array('left');
        $table->head = array('&#xa0;');
        $table->colclasses = array('');

        // Add role name headers.
        foreach ($this->roles as $targetrole) {
            $table->head[] = $targetrole->localname;
            $table->align[] = 'left';
            if ($this->is_allowed_target($targetrole->id)) {
                $table->colclasses[] = '';
            } else {
                $table->colclasses[] = 'dimmed_text';
            }
        }

        // Now the rest of the table.
        foreach ($this->roles as $fromrole) {
            $row = array($fromrole->localname);
            foreach ($this->roles as $targetrole) {
                $checked = '';
                $disabled = '';
                if ($this->allowed[$fromrole->id][$targetrole->id]) {
                    $checked = 'checked="checked" ';
                }
                if (!$this->is_allowed_target($targetrole->id)) {
                    $disabled = 'disabled="disabled" ';
                }
                $name = 's_' . $fromrole->id . '_' . $targetrole->id;
                $tooltip = $this->get_cell_tooltip($fromrole, $targetrole);
                $row[] = '<input type="checkbox" name="' . $name . '" id="' . $name .
                    '" title="' . $tooltip . '" value="1" ' . $checked . $disabled . '/>' .
                    '<label for="' . $name . '" class="accesshide">' . $tooltip . '</label>';
            }
            $table->data[] = $row;
        }

        return $table;
    }

    /**
     * Snippet of text displayed above the table, telling the admin what to do.
     * @return string
     */
    public abstract function get_intro_text();

    /**
     * Returns the allow class respective event class name.
     * @return string
     */
    protected abstract function get_eventclass();
}
