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
     * Test the environment.
     */
    public function test_environment() {
        global $CFG;

        require_once($CFG->libdir.'/environmentlib.php');
        list($envstatus, $environment_results) = check_moodle_environment(normalize_version($CFG->release), ENV_SELECT_RELEASE);

        $this->assertNotEmpty($envstatus);
        foreach ($environment_results as $environment_result) {
            if ($environment_result->part === 'php_setting'
                and $environment_result->info === 'opcache.enable'
                and $environment_result->getLevel() === 'optional'
                and $environment_result->getStatus() === false
            ) {
                $this->markTestSkipped('OPCache extension is not necessary for unit testing.');
                continue;
            }
            $this->assertTrue($environment_result->getStatus(), "Problem detected in environment ($environment_result->part:$environment_result->info), fix all warnings and errors!");
        }
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
}
