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

namespace cachestore_file;

use cache_definition;
use cache_store;
use cachestore_file;

/**
 * Async purge support test for File cache.
 *
 * @package   cachestore_file
 * @copyright Catalyst IT Europe Ltd 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jackson D'Souza <jackson.dsouza@catalyst-eu.net>
 * @coversDefaultClass \cachestore_file
 */
class asyncpurge_test extends \advanced_testcase {

    /**
     * Testing Asynchronous file store cache purge
     *
     * @covers ::initialise
     * @covers ::set
     * @covers ::get
     * @covers ::purge
     */
    public function test_cache_async_purge() {
        $this->resetAfterTest(true);

        // Cache definition.
        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_file', 'phpunit_test');

        // Extra config, set async purge = true.
        $extraconfig = ['asyncpurge' => true, 'filecacherev' => time()];
        $configuration = array_merge(cachestore_file::unit_test_configuration(), $extraconfig);
        $name = 'File async test';

        // Create file cache store.
        $cache = new cachestore_file($name, $configuration);

        // Initialise file cache store.
        $cache->initialise($definition);
        $cache->set('foo', 'bar');
        $this->assertSame('bar', $cache->get('foo'));

        // Purge this file cache store.
        $cache->purge();

        // Purging file cache store shouldn't purge the data but create a new cache revision directory.
        $this->assertSame('bar', $cache->get('foo'));
        $cache->set('foo', 'bar 2');
        $this->assertSame('bar 2', $cache->get('foo'));
    }

    /**
     * Testing Adhoc Cron - deletes old cache revision directory
     *
     * @covers \cachestore_file\task
     */
    public function test_cache_async_purge_cron() {
        global $CFG, $USER;

        $this->resetAfterTest(true);

        $tmpdir = realpath($CFG->tempdir);
        $directorypath = '/cachefile_store';
        $cacherevdir = $tmpdir . $directorypath;

        // Create cache revision directory.
        mkdir($cacherevdir, $CFG->directorypermissions, true);

        // Create / execute adhoc task to delete cache revision directory.
        $asynctask = new cachestore_file\task\asyncpurge();
        $asynctask->set_custom_data(['path' => $cacherevdir]);
        $asynctask->set_userid($USER->id);
        \core\task\manager::queue_adhoc_task($asynctask);
        $asynctask->execute();

        // Check if cache revision directory has been deleted.
        $this->assertDirectoryDoesNotExist($cacherevdir);
    }
}
