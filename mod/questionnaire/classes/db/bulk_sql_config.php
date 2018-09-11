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

namespace mod_questionnaire\db;

defined('MOODLE_INTERNAL') || die();

/**
 * For bulk sql operations on useresponses.
 *
 * @package    mod_questionnaire
 * @copyright  2015 Guy Thomas <gthomas@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bulk_sql_config  {

    /**
     * @var string $table
     */
    public $table = '';

    /**
     * @var string $tablealias
     */
    public $tablealias = '';

    /**
     * @var bool $usechoiceid
     */
    protected $usechoiceid = false;

    /**
     * @var bool $useresponse
     */
    protected $useresponse = false;

    /**
     * @var bool $userank
     */
    protected $userank = false;

    /**
     * @param string $table
     * @param string $tablealias
     * @param bool $usechoiceid
     * @param bool $useresponse
     * @param bool $userank
     */
    public function __construct($table, $tablealias, $usechoiceid = false, $useresponse = false, $userank = false) {
        $this->table = $table;
        $this->tablealias = $tablealias;
        $this->usechoiceid = $usechoiceid;
        $this->useresponse = $useresponse;
        $this->userank = $userank;
    }

    /**
     * Fields that need to be included for extra select.
     * @return array
     */
    public function get_extra_select() {
        return [
            'choice_id' => $this->usechoiceid,
            'response' => $this->useresponse,
            'rank' => $this->userank
        ];
    }
}