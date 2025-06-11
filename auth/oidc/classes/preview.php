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
 * Update username feature preview table.
 *
 * @package auth_oidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2023 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc;

use core_text;
use core_user;
use csv_import_reader;
use html_table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/csvlib.class.php');

/**
 * Class preview represents the preview table.
 */
class preview extends html_table {
    /** @var csv_import_reader */
    protected $cir;
    /** @var array */
    protected $filecolumns;
    /** @var int */
    protected $previewrows;
    /** @var bool */
    protected $noerror = true;

    /**
     * Preview constructor.
     *
     * @param csv_import_reader $cir
     * @param array $filecolumns
     * @param int $previewrows
     */
    public function __construct(csv_import_reader $cir, array $filecolumns, int $previewrows) {
        parent::__construct();

        $this->cir = $cir;
        $this->filecolumns = $filecolumns;
        $this->previewrows = $previewrows;

        $this->id = 'username_update_preview';
        $this->attributes['class'] = 'generaltable';
        $this->tablealign = 'center';
        $this->header = [];
        $this->data = $this->read_data();

        $this->head[] = get_string('csvline', 'auth_oidc');
        foreach ($filecolumns as $column) {
            $this->head[] = $column;
        }
        $this->head[] = get_string('status');
    }

    /**
     * Read data.
     *
     * @return array
     */
    protected function read_data(): array {
        global $DB;

        $data = [];
        $this->cir->init();
        $linenum = 1;

        while ($linenum <= $this->previewrows && $fields = $this->cir->next()) {
            $hasfatalerror = false;
            $linenum++;
            $rowcols = [];
            $rowcols['line'] = $linenum;
            foreach ($fields as $key => $value) {
                $rowcols[$this->filecolumns[$key]] = s(trim($value));
            }
            $rowcols['status'] = [];

            if (!isset($rowcols['username']) || !isset($rowcols['new_username'])) {
                $rowcols['status'][] = get_string('update_error_incomplete_line', 'auth_oidc');
                $hasfatalerror = true;
            }

            $user = $DB->get_record('user', ['username' => $rowcols['username']]);
            if (!$user) {
                $user = $DB->get_record('user', ['email' => $rowcols['username']]);
                if ($user) {
                    $rowcols['status'][] = get_string('update_warning_email_match', 'auth_oidc');
                } else {
                    $rowcols['status'][] = get_string('update_error_user_not_found', 'auth_oidc');
                }
            } else if ($user->auth != 'oidc') {
                $rowcols['status'][] = get_string('update_error_user_not_oidc', 'auth_oidc');
            }

            $lcnewusername = core_text::strtolower($rowcols['new_username']);
            if ($lcnewusername != core_user::clean_field($lcnewusername, 'username')) {
                $rowcols['status'][] = get_string('update_error_invalid_new_username', 'auth_oidc');
                $hasfatalerror = true;
            }

            $this->noerror = !$hasfatalerror && $this->noerror;
            $rowcols['status'] = join('<br />', $rowcols['status']);
            $data[] = $rowcols;
        }

        if ($fields = $this->cir->next()) {
            $data[] = array_fill(0, count($fields) + 2, '...');
        }
        $this->cir->close();

        return $data;
    }

    /**
     * Get no error.
     *
     * @return bool
     */
    public function get_no_error(): bool {
        return $this->noerror;
    }
}
