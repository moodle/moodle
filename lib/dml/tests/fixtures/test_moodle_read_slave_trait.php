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
 * Read slave helper that exposes selected moodle_read_slave_trait metods
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Srdjan Janković, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../pgsql_native_moodle_database.php');

/**
 * Read slave helper that exposes selected moodle_read_slave_trait metods
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait test_moodle_read_slave_trait {
    // @codingStandardsIgnoreStart
    /**
     * Constructs a mock db driver
     *
     * @param bool $external
     */
    public function __construct($external = false) {
    // @codingStandardsIgnoreEnd
        parent::__construct($external);

        $rw = fopen("php://memory", 'r+');
        fputs($rw, 'rw');

        $ro = fopen("php://memory", 'r+');
        fputs($ro, 'ro');

        $this->wantreadslave = true;
        $this->dbhwrite = $rw;
        $this->dbhreadonly = $ro;
        $this->set_db_handle($this->dbhwrite);

        $this->temptables = new moodle_temptables($this);
    }

    /**
     * Check db handle
     * @param string $id
     * @return bool
     */
    public function db_handle_is($id) {
        $dbh = $this->get_db_handle();
        rewind($dbh);
        return stream_get_contents($dbh) == $id;
    }

    /**
     * Check db handle is rw
     * @return bool
     */
    public function db_handle_is_rw() {
        return $this->db_handle_is('rw');
    }

    /**
     * Check db handle is ro
     * @return bool
     */
    public function db_handle_is_ro() {
        return $this->db_handle_is('ro');
    }

    /**
     * Upgrade to public
     * @return resource
     */
    public function get_db_handle() {
        return parent::get_db_handle();
    }

    /**
     * Upgrade to public
     * @param string $sql
     * @param array $params
     * @param int $type
     * @param array $extrainfo
     */
    public function query_start($sql, array $params = null, $type, $extrainfo = null) {
        return parent::query_start($sql, $params, $type);
    }

    /**
     * Upgrade to public
     * @param mixed $result
     */
    public function query_end($result) {
        $this->set_db_handle($this->dbhwrite);
    }

    /**
     * Upgrade to public
     */
    public function dispose() {
    }
}
