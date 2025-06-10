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
 * Tests for APCu cache.
 *
 * @package   local_aws_sdk
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_aws_sdk;
use Aws\Credentials\Credentials;
use local_aws_sdk\aws_apcu_cache;
use local_aws_sdk\aws_sdk;

/**
 * Tests for APCu cache.
 *
 * @package   local_aws_sdk
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aws_apcu_cache_test extends \advanced_testcase {

    const TEST_KEY = 'phpunit_local_aws_sdk_test';

    protected function setUp(): void {
        if (!extension_loaded('apcu')) {
            $this->markTestSkipped('The APCu extension is not loaded');
        }
        if (!ini_get('apc.enabled')) {
            $this->markTestSkipped('The APCu extension is not enabled');
        }
        if (!ini_get('apc.enable_cli')) {
            $this->markTestSkipped('The APCu extension is not enabled for CLI');
        }
    }

    protected function tearDown(): void {
        apcu_delete(self::TEST_KEY);
    }

    /**
     * @param mixed $value
     * @dataProvider value_provider
     */
    public function test_cache($value) {
        $cache = new aws_apcu_cache();
        $cache->set(self::TEST_KEY, $value);
        $this->assertDebuggingNotCalled();

        $result = $cache->get(self::TEST_KEY);
        $this->assertNotNull($result);

        if ($value instanceof Credentials) {
            $this->assertInstanceOf(Credentials::class, $result);
            $this->assertSame($value->toArray(), $result->toArray());
        } else {
            $this->assertSame($value, $result);
        }

        $cache->remove(self::TEST_KEY);
        $this->assertNull($cache->get(self::TEST_KEY));
    }

    public function value_provider() {
        aws_sdk::autoload();

        return [
            ['Hodor value'],
            [100],
            [['Foo', 'Bar', 100]],
            [100.01],
            [new Credentials('key', 'secret', 'token', 100)],
        ];
    }
}
