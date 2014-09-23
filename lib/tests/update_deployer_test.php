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
 * Unit tests for the update deployer.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Test cases for {@link \core\update\deployer} class.
 */
class core_update_deployer_testcase extends advanced_testcase {

    public function test_magic_setters() {
        $deployer = testable_available_update_deployer::instance();
        $value = new moodle_url('/');
        $deployer->set_returnurl($value);
        $this->assertSame($deployer->get_returnurl(), $value);
    }

    public function test_prepare_authorization() {
        global $CFG;

        $deployer = testable_available_update_deployer::instance();
        list($passfile, $password) = $deployer->prepare_authorization();
        $filename = $CFG->phpunit_dataroot.'/mdeploy/auth/'.$passfile;
        $this->assertFileExists($filename);
        $stored = file($filename, FILE_IGNORE_NEW_LINES);
        $this->assertCount(2, $stored);
        $this->assertGreaterThan(23, strlen($stored[0]));
        $this->assertSame($stored[0], $password);
        $this->assertLessThan(60, time() - (int)$stored[1]);
    }
}


/**
 * Modified version of {@link \core\update\checker} suitable for testing.
 */
class testable_available_update_checker extends \core\update\checker {

    /** @var replaces the default DB table storage for the fetched response */
    protected $fakeresponsestorage;
    /** @var int stores the fake recentfetch value */
    public $fakerecentfetch = -1;
    /** @var int stores the fake value of time() */
    public $fakecurrenttimestamp = -1;

    /**
     * Factory method for this class.
     *
     * @return testable_available_update_checker the singleton instance
     */
    public static function instance() {
        global $CFG;

        if (is_null(self::$singletoninstance)) {
            self::$singletoninstance = new self();
        }
        return self::$singletoninstance;
    }

    protected function validate_response($response) {
    }

    protected function store_response($response) {
        $this->fakeresponsestorage = $response;
    }

    protected function restore_response($forcereload = false) {
        $this->recentfetch = time();
        $this->recentresponse = $this->decode_response($this->get_fake_response());
    }

    public function compare_responses(array $old, array $new) {
        return parent::compare_responses($old, $new);
    }

    public function is_same_release($remote, $local=null) {
        return parent::is_same_release($remote, $local);
    }

    protected function load_current_environment($forcereload=false) {
    }

    public function fake_current_environment($version, $release, $branch, array $plugins) {
        $this->currentversion = $version;
        $this->currentrelease = $release;
        $this->currentbranch = $branch;
        $this->currentplugins = $plugins;
    }

    public function get_last_timefetched() {
        if ($this->fakerecentfetch == -1) {
            return parent::get_last_timefetched();
        } else {
            return $this->fakerecentfetch;
        }
    }

    private function get_fake_response() {
        $fakeresponse = array(
            'status' => 'OK',
            'provider' => 'https://download.moodle.org/api/1.0/updates.php',
            'apiver' => '1.0',
            'timegenerated' => time(),
            'forversion' => '2012010100.00',
            'forbranch' => '2.3',
            'ticket' => sha1('No, I am not going to mention the word "frog" here. Oh crap. I just did.'),
            'updates' => array(
                'core' => array(
                    array(
                        'version' => 2012060103.00,
                        'release' => '2.3.3 (Build: 20121201)',
                        'maturity' => 200,
                        'url' => 'https://download.moodle.org/',
                        'download' => 'https://download.moodle.org/download.php/MOODLE_23_STABLE/moodle-2.3.3-latest.zip',
                    ),
                    array(
                        'version' => 2012120100.00,
                        'release' => '2.4dev (Build: 20121201)',
                        'maturity' => 50,
                        'url' => 'https://download.moodle.org/',
                        'download' => 'https://download.moodle.org/download.php/MOODLE_24_STABLE/moodle-2.4.0-latest.zip',
                    ),
                ),
                'mod_foo' => array(
                    array(
                        'version' => 2012030501,
                        'requires' => 2012010100,
                        'maturity' => 200,
                        'release' => '1.1',
                        'url' => 'http://moodle.org/plugins/blahblahblah/',
                        'download' => 'http://moodle.org/plugins/download.php/blahblahblah',
                    ),
                    array(
                        'version' => 2012030502,
                        'requires' => 2012010100,
                        'maturity' => 100,
                        'release' => '1.2 beta',
                        'url' => 'http://moodle.org/plugins/',
                    ),
                ),
            ),
        );

        return json_encode($fakeresponse);
    }

    protected function cron_current_timestamp() {
        if ($this->fakecurrenttimestamp == -1) {
            return parent::cron_current_timestamp();
        } else {
            return $this->fakecurrenttimestamp;
        }
    }

    protected function cron_mtrace($msg, $eol = PHP_EOL) {
    }

    protected function cron_autocheck_enabled() {
        return true;
    }

    protected function cron_execution_offset() {
        // Autofetch should run by the first cron after 01:42 AM.
        return 42 * MINSECS;
    }

    protected function cron_execute() {
        throw new testable_available_update_checker_cron_executed('Cron executed!');
    }
}


/**
 * Exception used to detect {@link \core\update\checker::cron_execute()} calls.
 */
class testable_available_update_checker_cron_executed extends Exception {
}

/**
 * Modified {@link \core\update\deployer} suitable for testing purposes.
 */
class testable_available_update_deployer extends \core\update\deployer {
}
