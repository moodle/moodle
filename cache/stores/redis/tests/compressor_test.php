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
     * @var cachestore_redis
     */
    protected $store = null;

    /**
     * Create a cachestore.
     *
     * @param int      $compressor
     * @param int|null $serializer
     * @return cachestore_redis|null
     */
    public function create_store($compressor, $serializer = null) {
        if (!cachestore_redis::are_requirements_met() || !defined('TEST_CACHESTORE_REDIS_TESTSERVERS')) {
            $this->markTestSkipped('Could not test cachestore_redis. Requirements are not met.');
            return null;
        }

        if (is_null($serializer)) {
            $serializer = Redis::SERIALIZER_PHP;
        }

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
     * Create a cache store.
     */
    public function setUp() {
        parent::setUp();

        $this->store = $this->create_store(cachestore_redis::COMPRESSOR_PHP_GZIP);
    }

    /**
     * Destroy cache store.
     */
    protected function tearDown() {
        parent::tearDown();

        if ($this->store instanceof cachestore_redis) {
            $this->store->purge();
        }
    }

    /**
     * Set a value.
     */
    public function test_it_can_set() {
        if (is_null($this->store)) {
            return;
        }

        $this->store->set('the key', 'the value');
        $expected = gzencode(serialize('the value'));

        // Disable compressor to check stored value.
        $rawstore = $this->create_store(cachestore_redis::COMPRESSOR_NONE, Redis::SERIALIZER_NONE);
        $actual = $rawstore->get('the key'); // Compressor was disabled.

        self::assertSame($expected, $actual);
    }

    /**
     * Set many values.
     */
    public function test_it_can_set_many() {
        if (is_null($this->store)) {
            return;
        }

        // Create values.
        $values = [];
        for ($i = 0; $i < 10; $i++) {
            $values[] = [
                'key'   => "key_{$i}",
                'value' => "value #{$i}",
            ];
        }

        // Store it.
        $this->store->set_many($values);

        // Disable compressor to check stored value.
        $rawstore = $this->create_store(cachestore_redis::COMPRESSOR_NONE, Redis::SERIALIZER_NONE);

        foreach ($values as $value) {
            $expected = gzencode(serialize($value['value']));
            $actual = $rawstore->get($value['key']); // Compressor was disabled.
            self::assertSame($expected, $actual, "Invalid value for key={$value['key']}");
        }
    }

    /**
     * Gets a value.
     */
    public function test_it_can_get() {
        if (is_null($this->store)) {
            return;
        }

        $this->store->set('the key', 'the value');
        $actual = $this->store->get('the key');
        self::assertSame('the value', $actual);
    }

    /**
     * Gets many values.
     */
    public function test_it_can_get_many() {
        if (is_null($this->store)) {
            return;
        }

        // Create values.
        $values = [];
        $keys = [];
        $expected = [];
        for ($i = 0; $i < 10; $i++) {
            $key = "getkey_{$i}";
            $value = "getvalue #{$i}";
            $keys[] = $key;
            $values[] = [
                'key'   => $key,
                'value' => $value,
            ];
            $expected[$key] = $value;
        }

        $this->store->set_many($values);
        $actual = $this->store->get_many($keys);
        self::assertSame($expected, $actual);
    }

    /**
     * It misses a value.
     */
    public function test_it_can_miss_one() {
        if (is_null($this->store)) {
            return;
        }
        $actual = $this->store->get('missme');
        self::assertFalse($actual);
    }

    /**
     * It misses many values.
     */
    public function test_it_can_miss_many() {
        if (is_null($this->store)) {
            return;
        }
        $expected = ['missme' => false, 'missmetoo' => false];
        $actual = $this->store->get_many(array_keys($expected));
        self::assertSame($expected, $actual);
    }

    /**
     * It misses some values.
     */
    public function test_it_can_miss_some() {
        if (is_null($this->store)) {
            return;
        }

        $this->store->set('iamhere', 'youfoundme');

        $expected = ['missme' => false, 'missmetoo' => false, 'iamhere' => 'youfoundme'];
        $actual = $this->store->get_many(array_keys($expected));
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
        if (is_null($this->store)) {
            return;
        }

        $this->store->set($key, $value);
        $actual = $this->store->get($key);
        self::assertEquals($value, $actual, "Failed set/get for: {$key}");
    }

    /**
     * Test it works with different types for many.
     */
    public function test_it_works_with_different_types_for_many() {
        if (is_null($this->store)) {
            return;
        }

        $provider = $this->provider_for_test_it_works_with_different_types();
        $keys = [];
        $values = [];
        $expected = [];
        foreach ($provider as $item) {
            $keys[] = $item[0];
            $values[] = ['key' => $item[0], 'value' => $item[1]];
            $expected[$item[0]] = $item[1];
        }

        $this->store->set_many($values);
        $actual = $this->store->get_many($keys);
        self::assertEquals($expected, $actual);
    }

    /**
     * Test it does not use PHP Redis serialization if compression is on.
     */
    public function test_it_does_not_use_phpredis_serialisation() {
        if (is_null($this->store)) {
            return; // Redis not enabled.
        }

        $this->store->set('my key', 'my value');

        // Create a connection without serialisation or compressor to fetch raw data.
        $rawstore = $this->create_store(cachestore_redis::COMPRESSOR_NONE, Redis::SERIALIZER_NONE);

        $rawdata = $rawstore->get('my key');
        $expected = gzencode(serialize('my value')); // It should not have an extra serialisation.
        self::assertSame($expected, $rawdata);
    }

    /**
     * Provider for serializer tests.
     *
     * @return array
     */
    public function provider_for_test_it_can_use_serializers() {
        if (!class_exists('Redis')) {
            return [];
        }

        $data = [
            ['none', Redis::SERIALIZER_NONE, gzencode('value1'), gzencode('value2')],
            ['php', Redis::SERIALIZER_PHP, gzencode(serialize('value1')), gzencode(serialize('value2'))],
        ];

        if (defined('Redis::SERIALIZER_IGBINARY')) {
            $data[] = [
                'igbinary',
                Redis::SERIALIZER_IGBINARY,
                gzencode(igbinary_serialize('value1')),
                gzencode(igbinary_serialize('value2')),
            ];
        }

        return $data;
    }

    /**
     * Test it can use  serializers with get and set.
     *
     * @dataProvider provider_for_test_it_can_use_serializers
     * @param string $name
     * @param int $serializer
     * @param string $rawexpected1
     * @param string $rawexpected2
     */
    public function test_it_can_use_serializers_getset($name, $serializer, $rawexpected1, $rawexpected2) {
        if (is_null($this->store)) {
            return; // Redis not enabled.
        }

        // Create a connection with the desired serialisation.
        $store = $this->create_store(cachestore_redis::COMPRESSOR_PHP_GZIP, $serializer);

        // Create a connection without serialisation or compressor to fetch raw data.
        $rawstore = $this->create_store(cachestore_redis::COMPRESSOR_NONE, Redis::SERIALIZER_NONE);

        $store->set('key', 'value1');
        $data = $store->get('key');
        $rawdata = $rawstore->get('key');
        self::assertSame('value1', $data, "Invalid serialisation/unserialisation for: {$name}");
        self::assertSame($rawexpected1, $rawdata, "Invalid rawdata for: {$name}");
    }

    /**
     * Test it can use  serializers with get and set many.
     *
     * @dataProvider provider_for_test_it_can_use_serializers
     * @param string $name
     * @param int $serializer
     * @param string $rawexpected1
     * @param string $rawexpected2
     */
    public function test_it_can_use_serializers_getsetmany($name, $serializer, $rawexpected1, $rawexpected2) {
        if (is_null($this->store)) {
            return; // Redis not enabled.
        }

        $many = [
            ['key' => 'key1', 'value' => 'value1'],
            ['key' => 'key2', 'value' => 'value2'],
        ];
        $keys = ['key1', 'key2'];
        $expectations = ['key1' => 'value1', 'key2' => 'value2'];
        $rawexpectations = ['key1' => $rawexpected1, 'key2' => $rawexpected2];

        // Create a connection with the desired serialisation.
        $store = $this->create_store(cachestore_redis::COMPRESSOR_PHP_GZIP, $serializer);
        $store->set_many($many);

        // Create a connection without serialisation or compressor to fetch raw data.
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
