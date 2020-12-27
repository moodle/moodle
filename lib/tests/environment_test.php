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
 * Moodle environment test.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Do standard environment.xml tests.
 */
class core_environment_testcase extends advanced_testcase {

    /**
     * Test the environment check status.
     */
    public function test_environment_check_status() {
        global $CFG;
        require_once($CFG->libdir.'/environmentlib.php');

        $results = check_moodle_environment(normalize_version($CFG->release), ENV_SELECT_RELEASE);

        // The first element of the results array contains the environment check status.
        $status = reset($results);
        $this->assertTrue($status);
    }

    /**
     * Data provider for Moodle environment check tests.
     *
     * @return array
     */
    public function environment_provider() {
        global $CFG;
        require_once($CFG->libdir.'/environmentlib.php');

        $results = check_moodle_environment(normalize_version($CFG->release), ENV_SELECT_RELEASE);
        // The second element of the results array contains the list of environment results.
        $environmentresults = end($results);
        return array_map(function($result) {
            return [$result];
        }, $environmentresults);
    }

    /**
     * Test the environment.
     *
     * @dataProvider environment_provider
     * @param environment_results $result
     */
    public function test_environment($result) {
        $sslmessages = ['ssl/tls configuration not supported', 'invalid ssl/tls configuration'];

        if ($result->part === 'php_setting'
                && $result->info === 'opcache.enable'
                && $result->getLevel() === 'optional'
                && $result->getStatus() === false) {
            $this->markTestSkipped('OPCache extension is not necessary for unit testing.');
        }

        if ($result->part === 'custom_check'
                && $result->getLevel() === 'optional'
                && $result->getStatus() === false) {
            if (in_array($result->info, $sslmessages)) {
                $this->markTestSkipped('Up-to-date TLS libraries are not necessary for unit testing.');
            }
            if ($result->info === 'php not 64 bits' && PHP_INT_SIZE == 4) {
                // If we're on a 32-bit system, skip 64-bit check. 32-bit PHP has PHP_INT_SIZE set to 4.
                $this->markTestSkipped('64-bit check is not necessary for unit testing.');
            }
        }
        $info = "{$result->part}:{$result->info}";
        $this->assertTrue($result->getStatus(), "Problem detected in environment ($info), fix all warnings and errors!");
    }

    /**
     * Test the get_list_of_environment_versions() function.
     */
    public function test_get_list_of_environment_versions() {
        global $CFG;
        require_once($CFG->libdir.'/environmentlib.php');
        // Build a sample xmlised environment.xml.
        $xml = <<<END
<COMPATIBILITY_MATRIX>
    <MOODLE version="1.9">
        <PHP_EXTENSIONS>
            <PHP_EXTENSION name="xsl" level="required" />
        </PHP_EXTENSIONS>
    </MOODLE>
    <MOODLE version="2.5">
        <PHP_EXTENSIONS>
            <PHP_EXTENSION name="xsl" level="required" />
        </PHP_EXTENSIONS>
    </MOODLE>
    <MOODLE version="2.6">
        <PHP_EXTENSIONS>
            <PHP_EXTENSION name="xsl" level="required" />
        </PHP_EXTENSIONS>
    </MOODLE>
    <MOODLE version="2.7">
        <PHP_EXTENSIONS>
            <PHP_EXTENSION name="xsl" level="required" />
        </PHP_EXTENSIONS>
    </MOODLE>
    <PLUGIN name="block_test">
        <PHP_EXTENSIONS>
            <PHP_EXTENSION name="xsl" level="required" />
        </PHP_EXTENSIONS>
    </PLUGIN>
</COMPATIBILITY_MATRIX>
END;
        $environemt = xmlize($xml);
        $versions = get_list_of_environment_versions($environemt);
        $this->assertCount(5, $versions);
        $this->assertContains('1.9', $versions);
        $this->assertContains('2.5', $versions);
        $this->assertContains('2.6', $versions);
        $this->assertContains('2.7', $versions);
        $this->assertContains('all', $versions);
    }

    /**
     * Test the environment_verify_plugin() function.
     */
    public function test_verify_plugin() {
        global $CFG;
        require_once($CFG->libdir.'/environmentlib.php');
        // Build sample xmlised environment file fragments.
        $plugin1xml = <<<END
<PLUGIN name="block_testcase">
    <PHP_EXTENSIONS>
        <PHP_EXTENSION name="xsl" level="required" />
    </PHP_EXTENSIONS>
</PLUGIN>
END;
        $plugin1 = xmlize($plugin1xml);
        $plugin2xml = <<<END
<PLUGIN>
    <PHP_EXTENSIONS>
        <PHP_EXTENSION name="xsl" level="required" />
    </PHP_EXTENSIONS>
</PLUGIN>
END;
        $plugin2 = xmlize($plugin2xml);
        $this->assertTrue(environment_verify_plugin('block_testcase', $plugin1['PLUGIN']));
        $this->assertFalse(environment_verify_plugin('block_testcase', $plugin2['PLUGIN']));
        $this->assertFalse(environment_verify_plugin('mod_someother', $plugin1['PLUGIN']));
        $this->assertFalse(environment_verify_plugin('mod_someother', $plugin2['PLUGIN']));
    }

    /**
     * Test the restrict_php_version() function returns true if the current
     * PHP version is greater than the restricted version
     */
    public function test_restrict_php_version_greater_than_restricted_version() {
        global $CFG;
        require_once($CFG->libdir.'/environmentlib.php');

        $result = new environment_results('php');
        $delimiter = '.';
        // Get the current PHP version.
        $currentversion = explode($delimiter, normalize_version(phpversion()));
        // Lets drop back one major version to ensure we trip the restriction.
        $currentversion[0]--;
        $restrictedversion = implode($delimiter, $currentversion);

        // Make sure the status is true before the test to see it flip to false.
        $result->setStatus(true);

        $this->assertTrue(restrict_php_version($result, $restrictedversion),
            'restrict_php_version returns true if the current version exceeds the restricted version');
    }

    /**
     * Test the restrict_php_version() function returns true if the current
     * PHP version is equal to the restricted version
     */
    public function test_restrict_php_version_equal_to_restricted_version() {
        global $CFG;
        require_once($CFG->libdir.'/environmentlib.php');

        $result = new environment_results('php');
        // Get the current PHP version.
        $currentversion = normalize_version(phpversion());

        // Make sure the status is true before the test to see it flip to false.
        $result->setStatus(true);

        $this->assertTrue(restrict_php_version($result, $currentversion),
            'restrict_php_version returns true if the current version is equal to the restricted version');
    }

    /**
     * Test the restrict_php_version() function returns false if the current
     * PHP version is less than the restricted version
     */
    public function test_restrict_php_version_less_than_restricted_version() {
        global $CFG;
        require_once($CFG->libdir.'/environmentlib.php');

        $result = new environment_results('php');
        $delimiter = '.';
        // Get the current PHP version.
        $currentversion = explode($delimiter, normalize_version(phpversion()));
        // Lets increase the major version to ensure don't trip the restriction.
        $currentversion[0]++;
        $restrictedversion = implode($delimiter, $currentversion);

        // Make sure the status is true before the test to see it flip to false.
        $result->setStatus(true);

        $this->assertFalse(restrict_php_version($result, $restrictedversion),
            'restrict_php_version returns false if the current version is less than the restricted version');
    }
}
