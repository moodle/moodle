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
 * Redis cache test.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file.
 *
 * define('TEST_CACHESTORE_REDIS_TESTSERVERS', '127.0.0.1');
 *
 * @package   cachestore_redis
 * @copyright 2018 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../tests/fixtures/stores.php');
require_once(__DIR__.'/../lib.php');

/**
 * Redis cache test - compressor settings.
 *
 * @package   cachestore_redis
 * @author    Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright 2018 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_redis_compressor_test extends advanced_testcase {

    /**
     * Test set up
     */
    public function setUp() {
        if (!cachestore_redis::are_requirements_met() || !defined('TEST_CACHESTORE_REDIS_TESTSERVERS')) {
            $this->markTestSkipped('Could not test cachestore_redis. Requirements are not met.');
        }

        parent::setUp();
    }

    /**
     * Create a cachestore.
     *
     * @param int $compressor
     * @param int $serializer
     * @return cachestore_redis
     */
    public function create_store($compressor, $serializer) {
        /** @var cache_definition $definition */
        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_redis', 'phpunit_test');
        $config = cachestore_redis::unit_test_configuration();
        $config['compressor'] = $compressor;
        $config['serializer'] = $serializer;
        $store = new cachestore_redis('Test', $config);
        $store->initialise($definition);

        return $store;
    }

    /**
     * It misses a value.
     */
    public function test_it_can_miss_one() {
        $store = $this->create_store(cachestore_redis::COMPRESSOR_PHP_GZIP, Redis::SERIALIZER_PHP);

        self::assertFalse($store->get('missme'));
    }

    /**
     * It misses many values.
     */
    public function test_it_can_miss_many() {
        $store = $this->create_store(cachestore_redis::COMPRESSOR_PHP_GZIP, Redis::SERIALIZER_PHP);

        $expected = ['missme' => false, 'missmetoo' => false];
        $actual = $store->get_many(array_keys($expected));
        self::assertSame($expected, $actual);
    }

    /**
     * It misses some values.
     */
    public function test_it_can_miss_some() {
        $store = $this->create_store(cachestore_redis::COMPRESSOR_PHP_GZIP, Redis::SERIALIZER_PHP);
        $store->set('iamhere', 'youfoundme');

        $expected = ['missme' => false, 'missmetoo' => false, 'iamhere' => 'youfoundme'];
        $actual = $store->get_many(array_keys($expected));
        self::assertSame($expected, $actual);
    }

    /**
     * A provider for test_works_with_different_types
     *
     * @return array
     */
    public function provider_for_test_it_works_with_different_types() {
        $object = new stdClass();
        $object->field = 'value';

        return [
            ['string', 'Abc Def'],
            ['string_empty', ''],
            ['string_binary', gzencode('some binary data')],
            ['int', 123],
            ['int_zero', 0],
            ['int_negative', -100],
            ['int_huge', PHP_INT_MAX],
            ['float', 3.14],
            ['boolean_true', true],
            // Boolean 'false' is not tested as it is not allowed in Moodle.
            ['array', [1, 'b', 3.4]],
            ['array_map', ['a' => 'b', 'c' => 'd']],
            ['object_stdClass', $object],
            ['null', null],
        ];
    }

    /**
     * It works with different types.
     *
     * @dataProvider provider_for_test_it_works_with_different_types
     * @param string $key
     * @param mixed $value
     */
    public function test_it_works_with_different_types($key, $value) {
        $store = $this->create_store(cachestore_redis::COMPRESSOR_PHP_GZIP, Redis::SERIALIZER_PHP);
        $store->set($key, $value);

        self::assertEquals($value, $store->get($key), "Failed set/get for: {$key}");
    }

    /**
     * Test it works with different types for many.
     */
    public function test_it_works_with_different_types_for_many() {
        $store = $this->create_store(cachestore_redis::COMPRESSOR_PHP_GZIP, Redis::SERIALIZER_PHP);

        $provider = $this->provider_for_test_it_works_with_different_types();
        $keys = [];
        $values = [];
        $expected = [];
        foreach ($provider as $item) {
            $keys[] = $item[0];
            $values[] = ['key' => $item[0], 'value' => $item[1]];
            $expected[$item[0]] = $item[1];
        }
        $store->set_many($values);
        $actual = $store->get_many($keys);
        self::assertEquals($expected, $actual);
    }

    /**
     * Provider for set/get combination tests.
     *
     * @return array
     */
    public function provider_for_tests_setget() {
        $data = [
            ['none, none',
                Redis::SERIALIZER_NONE, cachestore_redis::COMPRESSOR_NONE,
                'value1', 'value2'],
            ['none, gzip',
                Redis::SERIALIZER_NONE, cachestore_redis::COMPRESSOR_PHP_GZIP,
                gzencode('value1'), gzencode('value2')],
            ['php, none',
                Redis::SERIALIZER_PHP, cachestore_redis::COMPRESSOR_NONE,
                serialize('value1'), serialize('value2')],
            ['php, gzip',
                Redis::SERIALIZER_PHP, cachestore_redis::COMPRESSOR_PHP_GZIP,
                gzencode(serialize('value1')), gzencode(serialize('value2'))],
        ];

        if (defined('Redis::SERIALIZER_IGBINARY')) {
            $data[] = [
                'igbinary, none',
                    Redis::SERIALIZER_IGBINARY, cachestore_redis::COMPRESSOR_NONE,
                    igbinary_serialize('value1'), igbinary_serialize('value2'),
            ];
            $data[] = [
                'igbinary, gzip',
                    Redis::SERIALIZER_IGBINARY, cachestore_redis::COMPRESSOR_PHP_GZIP,
                    gzencode(igbinary_serialize('value1')), gzencode(igbinary_serialize('value2')),
            ];
        }

        if (extension_loaded('zstd')) {
            $data[] = [
                'none, zstd',
                Redis::SERIALIZER_NONE, cachestore_redis::COMPRESSOR_PHP_ZSTD,
                zstd_compress('value1'), zstd_compress('value2'),
            ];
            $data[] = [
                'php, zstd',
                Redis::SERIALIZER_PHP, cachestore_redis::COMPRESSOR_PHP_ZSTD,
                zstd_compress(serialize('value1')), zstd_compress(serialize('value2')),
            ];

            if (defined('Redis::SERIALIZER_IGBINARY')) {
                $data[] = [
                    'igbinary, zstd',
                    Redis::SERIALIZER_IGBINARY, cachestore_redis::COMPRESSOR_PHP_ZSTD,
                    zstd_compress(igbinary_serialize('value1')), zstd_compress(igbinary_serialize('value2')),
                ];
            }
        }

        return $data;
    }

    /**
     * Test we can use get and set with all combinations.
     *
     * @dataProvider provider_for_tests_setget
     * @param string $name
     * @param int $serializer
     * @param int $compressor
     * @param string $rawexpected1
     * @param string $rawexpected2
     */
    public function test_it_can_use_getset($name, $serializer, $compressor, $rawexpected1, $rawexpected2) {
        // Create a connection with the desired serialisation.
        $store = $this->create_store($compressor, $serializer);
        $store->set('key', 'value1');

        // Disable compressor and serializer to check the actual stored value.
        $rawstore = $this->create_store(cachestore_redis::COMPRESSOR_NONE, Redis::SERIALIZER_NONE);

        $data = $store->get('key');
        $rawdata = $rawstore->get('key');
        self::assertSame('value1', $data, "Invalid serialisation/unserialisation for: {$name}");
        self::assertSame($rawexpected1, $rawdata, "Invalid rawdata for: {$name}");
    }

    /**
     * Test we can use get and set many with all combinations.
     *
     * @dataProvider provider_for_tests_setget
     * @param string $name
     * @param int $serializer
     * @param int $compressor
     * @param string $rawexpected1
     * @param string $rawexpected2
     */
    public function test_it_can_use_getsetmany($name, $serializer, $compressor, $rawexpected1, $rawexpected2) {
        $many = [
            ['key' => 'key1', 'value' => 'value1'],
            ['key' => 'key2', 'value' => 'value2'],
        ];
        $keys = ['key1', 'key2'];
        $expectations = ['key1' => 'value1', 'key2' => 'value2'];
        $rawexpectations = ['key1' => $rawexpected1, 'key2' => $rawexpected2];

        // Create a connection with the desired serialisation.
        $store = $this->create_store($compressor, $serializer);
        $store->set_many($many);

        // Disable compressor and serializer to check the actual stored value.
        $rawstore = $this->create_store(cachestore_redis::COMPRESSOR_NONE, Redis::SERIALIZER_NONE);

        $data = $store->get_many($keys);
        $rawdata = $rawstore->get_many($keys);
        foreach ($keys as $key) {
            self::assertSame($expectations[$key],
                             $data[$key],
                             "Invalid serialisation/unserialisation for {$key} with serializer {$name}");
            self::assertSame($rawexpectations[$key],
                             $rawdata[$key],
                             "Invalid rawdata for {$key} with serializer {$name}");
        }
    }
}
