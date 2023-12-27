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

namespace core;

use ReflectionClass;
use mysqli;
use moodle_database, mysqli_native_moodle_database;
use moodle_exception;

/**
 * Test specific features of the MySql dml.
 *
 * @package core
 * @category test
 * @copyright 2023 Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers  \mysqli_native_moodle_database
 */
class mysqli_native_moodle_database_test extends \advanced_testcase {

    /**
     * Set up.
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();
        // Skip tests if not using Postgres.
        if (!($DB instanceof mysqli_native_moodle_database)) {
            $this->markTestSkipped('MySql-only test');
        }
    }

    /**
     * SSL connection helper.
     *
     * @param bool|null $compress
     * @param string|null $ssl
     * @return mysqli
     * @throws moodle_exception
     */
    public function new_connection(?bool $compress = false, ?string $ssl = null): mysqli {
        global $DB;

        // Open new connection.
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = [];
        }

        $cfg->dboptions['clientcompress'] = $compress;
        $cfg->dboptions['ssl'] = $ssl;

        // Get a separate disposable db connection handle with guaranteed 'readonly' config.
        $db2 = moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $db2->raw_connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);

        $reflector = new ReflectionClass($db2);
        $rp = $reflector->getProperty('mysqli');
        $rp->setAccessible(true);
        return $rp->getValue($db2);
    }

    /**
     * Test client compression helper.
     *
     * @param mysqli $mysqli
     * @return array
     */
    public function connection_status($mysqli): array {
        $mysqli->query("SELECT * FROM INFORMATION_SCHEMA.TABLES");

        $stats = [];
        foreach ($mysqli->query('SHOW SESSION STATUS')->fetch_all(MYSQLI_ASSOC) as $r) {
            $stats[$r['Variable_name']] = $r['Value'];
        }
        return $stats;
    }

    /**
     * Test client compression.
     *
     * @return void
     */
    public function test_client_compression(): void {
        $mysqli = $this->new_connection();
        $stats = $this->connection_status($mysqli);
        $this->assertEquals('OFF', $stats['Compression']);
        $sent = $stats['Bytes_sent'];

        $mysqlic = $this->new_connection(true);
        $stats = $this->connection_status($mysqlic);
        $this->assertEquals('ON', $stats['Compression']);
        $sentc = $stats['Bytes_sent'];

        $this->assertLessThan($sent, $sentc);
    }

    /**
     * Test SSL connection.
     *
     * Well as much as we can, mysqli does not reliably report connect errors.
     * @return void
     */
    public function test_ssl_connection(): void {
        try {
            $mysqli = $this->new_connection(false, 'require');
            // Either connect ...
            $this->assertNotNull($mysqli);
        } catch (moodle_exception $e) {
            // ... or fail.
            // Unfortunately we cannot be sure with the error string.
            $this->markTestIncomplete('SSL not supported?');
        }

        try {
            $mysqli = $this->new_connection(false, 'verify-full');
            // Either connect ...
            $this->assertNotNull($mysqli);
        } catch (moodle_exception $e) {
            // ... or fail with invalid cert.
            // Same as above, but we cannot really expect properly signed cert, so ignore.
        }

        $this->expectException(moodle_exception::class);
        $this->new_connection(false, 'invalid-mode');
    }
}
