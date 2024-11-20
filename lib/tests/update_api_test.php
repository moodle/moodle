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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__.'/fixtures/testable_update_api.php');

/**
 * Tests for \core\update\api client.
 *
 * Please note many of these tests heavily depend on the behaviour of the
 * testable_api client. It is important to make sure that the behaviour of the
 * testable_api client perfectly matches the actual behaviour of the live
 * services on the given API version.
 *
 * @package   core
 * @category  test
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_api_test extends \advanced_testcase {

    /**
     * Make sure the $CFG->branch is mapped correctly to the format used by the API.
     */
    public function test_convert_branch_numbering_format(): void {

        /** @var \core\update\testable_api $client */
        $client = \core\update\testable_api::client();

        $this->assertSame('2.9', $client->convert_branch_numbering_format(29));
        $this->assertSame('3.0', $client->convert_branch_numbering_format('30'));
        $this->assertSame('3.1', $client->convert_branch_numbering_format(3.1));
        $this->assertSame('3.1', $client->convert_branch_numbering_format('3.1'));
        $this->assertSame('10.1', $client->convert_branch_numbering_format(101));
        $this->assertSame('10.2', $client->convert_branch_numbering_format('102'));
    }

    /**
     * Getting info about particular plugin version.
     */
    public function test_get_plugin_info(): void {

        $client = \core\update\testable_api::client();

        // The plugin is not found in the plugins directory.
        $this->assertFalse($client->get_plugin_info('non_existing', 2015093000));

        // The plugin is known but there is no such version.
        $info = $client->get_plugin_info('foo_bar', 2014010100);
        $this->assertInstanceOf('\core\update\remote_info', $info);
        $this->assertFalse($info->version);

        // Both plugin and the version are available.
        foreach (array(2015093000 => MATURITY_STABLE, 2015100400 => MATURITY_STABLE,
                2015100500 => MATURITY_BETA) as $version => $maturity) {
            $info = $client->get_plugin_info('foo_bar', $version);
            $this->assertInstanceOf('\core\update\remote_info', $info);
            $this->assertNotEmpty($info->version);
            $this->assertEquals($maturity, $info->version->maturity);
        }
    }

    /**
     * Getting info about the most suitable plugin version for us.
     */
    public function test_find_plugin(): void {

        $client = \core\update\testable_api::client();

        // The plugin is not found in the plugins directory.
        $this->assertFalse($client->find_plugin('non_existing'));

        // The plugin is known but there is no sufficient version.
        $info = $client->find_plugin('foo_bar', 2016010100);
        $this->assertFalse($info->version);

        // Both plugin and the version are available. Of the two available
        // stable versions, the more recent one is returned.
        $info = $client->find_plugin('foo_bar', 2015093000);
        $this->assertInstanceOf('\core\update\remote_info', $info);
        $this->assertEquals(2015100400, $info->version->version);

        // If any version is required, the most recent most mature one is
        // returned.
        $info = $client->find_plugin('foo_bar', ANY_VERSION);
        $this->assertInstanceOf('\core\update\remote_info', $info);
        $this->assertEquals(2015100400, $info->version->version);

        // Less matured versions are returned if needed.
        $info = $client->find_plugin('foo_bar', 2015100500);
        $this->assertInstanceOf('\core\update\remote_info', $info);
        $this->assertEquals(2015100500, $info->version->version);
    }

    /**
     * Validating the pluginfo.php response data.
     */
    public function test_validate_pluginfo_format(): void {

        $client = \core\update\testable_api::client();

        $json = '{"id":127,"name":"Course contents","component":"block_course_contents","source":"https:\/\/github.com\/mudrd8mz\/moodle-block_course_contents","doc":"http:\/\/docs.moodle.org\/20\/en\/Course_contents_block","bugs":"https:\/\/github.com\/mudrd8mz\/moodle-block_course_contents\/issues","discussion":null,"version":{"id":8100,"version":"2015030300","release":"3.0","maturity":200,"downloadurl":"https:\/\/moodle.org\/plugins\/download.php\/8100\/block_course_contents_moodle29_2015030300.zip","downloadmd5":"8d8ae64822f38d278420776f8b42eaa5","vcssystem":"git","vcssystemother":null,"vcsrepositoryurl":"https:\/\/github.com\/mudrd8mz\/moodle-block_course_contents","vcsbranch":"master","vcstag":"v3.0","supportedmoodles":[{"version":2014041100,"release":"2.7"},{"version":2014101000,"release":"2.8"},{"version":2015041700,"release":"2.9"}]}}';

        $data = json_decode($json);
        $this->assertInstanceOf('\core\update\remote_info', $client->validate_pluginfo_format($data));
        $this->assertEquals(json_encode($data), json_encode($client->validate_pluginfo_format($data)));

        // All properties must be present.
        unset($data->version);
        $this->assertFalse($client->validate_pluginfo_format($data));

        $data->version = false;
        $this->assertEquals(json_encode($data), json_encode($client->validate_pluginfo_format($data)));

        // Some properties may be empty.
        $data = json_decode($json);
        $data->version->release = null;
        $this->assertEquals(json_encode($data), json_encode($client->validate_pluginfo_format($data)));

        // Some properties must not be empty.
        $data = json_decode($json);
        $data->version->downloadurl = '';
        $this->assertFalse($client->validate_pluginfo_format($data));

        // Download URL may be http:// or https:// only.
        $data = json_decode($json);
        $data->version->downloadurl = 'ftp://archive.moodle.org/block_course_contents/2014041100.zip';
        $this->assertFalse($client->validate_pluginfo_format($data));
    }
}
