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
 * Class preview
 *
 * @package     tool_uploaduser
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_uploaduser;

defined('MOODLE_INTERNAL') || die();

use tool_uploaduser\local\field_value_validators;

require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/uploaduser/locallib.php');

/**
 * Display the preview of a CSV file
 *
 * @package     tool_uploaduser
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preview extends \html_table {

    /** @var \csv_import_reader  */
    protected $cir;
    /** @var array */
    protected $filecolumns;
    /** @var int */
    protected $previewrows;
    /** @var bool */
    protected $noerror = true; // Keep status of any error.

    /**
     * preview constructor.
     *
     * @param \csv_import_reader $cir
     * @param array $filecolumns
     * @param int $previewrows
     * @throws \coding_exception
     */
    public function __construct(\csv_import_reader $cir, array $filecolumns, int $previewrows) {
        parent::__construct();
        $this->cir = $cir;
        $this->filecolumns = $filecolumns;
        $this->previewrows = $previewrows;

        $this->id = "uupreview";
        $this->attributes['class'] = 'table generaltable table-hover';
        $this->tablealign = 'center';
        $this->summary = get_string('uploaduserspreview', 'tool_uploaduser');
        $this->head = array();
        $this->data = $this->read_data();

        $this->head[] = get_string('uucsvline', 'tool_uploaduser');
        foreach ($filecolumns as $column) {
            $this->head[] = $column;
        }
        $this->head[] = get_string('status');

    }

    /**
     * Read data
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    protected function read_data() {
        global $DB, $CFG;

        // Track whether values for profile fields defined as unique have already been used.
        $profilefieldvalues = [];

        $data = array();
        $this->cir->init();
        $linenum = 1; // Column header is first line.
        while ($linenum <= $this->previewrows and $fields = $this->cir->next()) {
            $linenum++;
            $rowcols = array();
            $rowcols['line'] = $linenum;
            foreach ($fields as $key => $field) {
                $rowcols[$this->filecolumns[$key]] = s(trim($field));
            }
            $rowcols['status'] = array();

            if (isset($rowcols['username'])) {
                $stdusername = \core_user::clean_field($rowcols['username'], 'username');
                if ($rowcols['username'] !== $stdusername) {
                    $rowcols['status'][] = get_string('invalidusernameupload');
                }
                if ($userid = $DB->get_field('user', 'id',
                        ['username' => $stdusername, 'mnethostid' => $CFG->mnet_localhost_id])) {
                    $rowcols['username'] = \html_writer::link(
                        new \moodle_url('/user/profile.php', ['id' => $userid]), $rowcols['username']);
                }
            } else {
                $rowcols['status'][] = get_string('missingusername');
            }

            if (isset($rowcols['email'])) {
                if (!validate_email($rowcols['email'])) {
                    $rowcols['status'][] = get_string('invalidemail');
                }

                $select = $DB->sql_like('email', ':email', false, true, false, '|');
                $params = array('email' => $DB->sql_like_escape($rowcols['email'], '|'));
                if ($DB->record_exists_select('user', $select , $params)) {
                    $rowcols['status'][] = get_string('useremailduplicate', 'error');
                }
            }

            if (isset($rowcols['theme'])) {
                list($status, $message) = field_value_validators::validate_theme($rowcols['theme']);
                if ($status !== 'normal' && !empty($message)) {
                    $rowcols['status'][] = $message;
                }
            }

            // Check if rowcols have custom profile field with correct data and update error state.
            $this->noerror = uu_check_custom_profile_data($rowcols, $profilefieldvalues) && $this->noerror;
            $rowcols['status'] = implode('<br />', $rowcols['status']);
            $data[] = $rowcols;
        }
        if ($fields = $this->cir->next()) {
            $data[] = array_fill(0, count($fields) + 2, '...');
        }
        $this->cir->close();

        return $data;
    }

    /**
     * Getter for noerror
     *
     * @return bool
     */
    public function get_no_error() {
        return $this->noerror;
    }
}