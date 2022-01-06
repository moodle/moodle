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
 * Template configuraton file for github actions CI/CD.
 *
 * @package    core
 * @copyright  2020 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This cannot be used out from a github actions workflow, so just exit.
getenv('GITHUB_WORKFLOW') || die; // phpcs:ignore moodle.Files.MoodleInternal.MoodleInternalGlobalState

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = getenv('dbtype');
$CFG->dblibrary = 'native';
$CFG->dbhost    = '127.0.0.1';
$CFG->dbname    = 'test';
$CFG->dbuser    = 'test';
$CFG->dbpass    = 'test';
$CFG->prefix    = 'm_';
$CFG->dboptions = ['dbcollation' => 'utf8mb4_bin'];

$host = 'localhost';
$CFG->wwwroot   = "http://{$host}";
$CFG->dataroot  = realpath(dirname(__DIR__)) . '/moodledata';
$CFG->admin     = 'admin';
$CFG->directorypermissions = 0777;

// Debug options - possible to be controlled by flag in future.
$CFG->debug = (E_ALL | E_STRICT); // DEBUG_DEVELOPER.
$CFG->debugdisplay = 1;
$CFG->debugstringids = 1; // Add strings=1 to url to get string ids.
$CFG->perfdebug = 15;
$CFG->debugpageinfo = 1;
$CFG->allowthemechangeonurl = 1;
$CFG->passwordpolicy = 0;
$CFG->cronclionly = 0;
$CFG->pathtophp = getenv('pathtophp');

$CFG->phpunit_dataroot  = realpath(dirname(__DIR__)) . '/phpunitdata';
$CFG->phpunit_prefix = 't_';

define('TEST_EXTERNAL_FILES_HTTP_URL', 'http://localhost:8080');
define('TEST_EXTERNAL_FILES_HTTPS_URL', 'http://localhost:8080');

define('TEST_SESSION_REDIS_HOST', 'localhost');
define('TEST_CACHESTORE_REDIS_TESTSERVERS', 'localhost');

// TODO: add others (solr, mongodb, memcached, ldap...).

// Too much for now: define('PHPUNIT_LONGTEST', true); // Only leaves a few tests out and they are run later by CI.

require_once(__DIR__ . '/lib/setup.php');
