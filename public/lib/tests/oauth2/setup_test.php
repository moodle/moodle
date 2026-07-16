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

namespace core\oauth2;

/**
 * Unit tests for the OAuth2 setup class.
 *
 * @package    core
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(setup::class)]
final class setup_test extends \advanced_testcase {
    /**
     * Purge the caches and config.
     *
     * Remove any cached key files from both the shared and local cache directories, and unset
     * the related config values that are populated automatically during installation.
     */
    private function purge_key_caches(): void {
        $this->resetAfterTest(true);

        foreach (['oauth2_private_key.pem', 'oauth2_public_key.pem', 'oauth2_encryption_key.pem'] as $filename) {
            $localpath = make_localcache_directory('oauth2') . "/{$filename}";
            if (file_exists($localpath)) {
                unlink($localpath);
            }

            $sharedpath = make_cache_directory('oauth2') . "/{$filename}";
            if (file_exists($sharedpath)) {
                unlink($sharedpath);
            }
        }

        unset_config('oauth2_private_key');
        unset_config('oauth2_encryption_key');
    }

    /**
     * A new private key is generated, persisted to config, and cached on disk when none exists yet.
     */
    public function test_get_private_key_generates_new_key_when_none_exists(): void {
        $this->purge_key_caches();
        $this->assertEmpty(get_config('core', 'oauth2_private_key'));

        $key = (new setup())->get_private_key();

        $this->assertNotEmpty($key);
        $this->assertNotFalse(openssl_pkey_get_private($key));
        $this->assertEquals($key, get_config('core', 'oauth2_private_key'));
        $this->assertFileExists(make_localcache_directory('oauth2') . '/oauth2_private_key.pem');
        $this->assertFileExists(make_cache_directory('oauth2') . '/oauth2_private_key.pem');
    }

    /**
     * The key is not regenerated on subsequent calls; the same value is returned.
     */
    public function test_get_private_key_is_stable_across_calls(): void {
        $this->purge_key_caches();
        $instance = new setup();

        $first = $instance->get_private_key();
        $second = $instance->get_private_key();
        $third = (new setup())->get_private_key();

        $this->assertEquals($first, $second);
        $this->assertEquals($first, $third);
    }

    /**
     * When a value already exists in config, but no cache file exists yet, it is used rather than
     * generating a new key, and it is then written into both cache locations.
     */
    public function test_get_private_key_uses_existing_config_value(): void {
        $this->purge_key_caches();
        $keydata = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ]);
        $expected = '';
        openssl_pkey_export($keydata, $expected);
        set_config('oauth2_private_key', $expected, null);

        $key = (new setup())->get_private_key();

        $this->assertEquals($expected, $key);
        $this->assertFileExists(make_localcache_directory('oauth2') . '/oauth2_private_key.pem');
        $this->assertFileExists(make_cache_directory('oauth2') . '/oauth2_private_key.pem');
    }

    /**
     * When a local cache file already exists, it is returned directly without consulting config.
     */
    public function test_get_private_key_reads_from_local_cache_first(): void {
        $this->purge_key_caches();
        $expected = "not a real key, but this is all that is needed to prove the cache is used";
        $path = make_localcache_directory('oauth2') . '/oauth2_private_key.pem';
        file_put_contents($path, $expected);

        $key = (new setup())->get_private_key();

        $this->assertEquals($expected, $key);
        $this->assertEmpty(get_config('core', 'oauth2_private_key'));
    }

    /**
     * When only the shared cache file exists, it is used, and it is then copied into the local cache.
     */
    public function test_get_private_key_reads_from_shared_cache_and_populates_local_cache(): void {
        $this->purge_key_caches();
        $expected = "not a real key, but this is all that is needed to prove the cache is used";
        $path = make_cache_directory('oauth2') . '/oauth2_private_key.pem';
        file_put_contents($path, $expected);

        $key = (new setup())->get_private_key();

        $this->assertEquals($expected, $key);
        $this->assertEquals(
            $expected,
            file_get_contents(make_localcache_directory('oauth2') . '/oauth2_private_key.pem'),
        );
    }

    /**
     * The public key is derived from the private key, and is cached on disk.
     */
    public function test_get_public_key_is_derived_from_private_key(): void {
        $this->purge_key_caches();
        $instance = new setup();

        $publickey = $instance->get_public_key();

        $details = openssl_pkey_get_details(openssl_pkey_get_private($instance->get_private_key()));
        $this->assertEquals($details['key'], $publickey);
        $this->assertFileExists(make_localcache_directory('oauth2') . '/oauth2_public_key.pem');
        $this->assertFileExists(make_cache_directory('oauth2') . '/oauth2_public_key.pem');

        // The public key is never stored in config, only cached on disk.
        $this->assertFalse(get_config('core', 'oauth2_public_key'));
    }

    /**
     * The public key is read straight from the cache when it is already present, avoiding
     * re-derivation from the private key.
     */
    public function test_get_public_key_reads_from_cache_first(): void {
        $this->purge_key_caches();

        $expected = "not a real key, but this is all that is needed to prove the cache is used";
        $path = make_localcache_directory('oauth2') . '/oauth2_public_key.pem';
        file_put_contents($path, $expected);

        $publickey = (new setup())->get_public_key();

        $this->assertEquals($expected, $publickey);
        // No private key should have been generated as a result.
        $this->assertEmpty(get_config('core', 'oauth2_private_key'));
    }

    /**
     * A new encryption key is generated, persisted to config, and cached on disk when none exists yet.
     */
    public function test_get_encryption_key_generates_new_key_when_none_exists(): void {
        $this->purge_key_caches();
        $this->assertEmpty(get_config('core', 'oauth2_encryption_key'));

        $key = (new setup())->get_encryption_key();

        $this->assertNotEmpty($key);
        // The generated value must be a valid Defuse ascii-safe key.
        $this->assertInstanceOf(
            \Defuse\Crypto\Key::class,
            \Defuse\Crypto\Key::loadFromAsciiSafeString($key),
        );
        $this->assertEquals($key, get_config('core', 'oauth2_encryption_key'));
        $this->assertFileExists(make_localcache_directory('oauth2') . '/oauth2_encryption_key.pem');
        $this->assertFileExists(make_cache_directory('oauth2') . '/oauth2_encryption_key.pem');
    }

    /**
     * The encryption key is not regenerated on subsequent calls; the same value is returned.
     */
    public function test_get_encryption_key_is_stable_across_calls(): void {
        $this->purge_key_caches();

        $instance = new setup();

        $first = $instance->get_encryption_key();
        $second = $instance->get_encryption_key();
        $third = (new setup())->get_encryption_key();

        $this->assertEquals($first, $second);
        $this->assertEquals($first, $third);
    }

    /**
     * When a value already exists in config, but no cache file exists yet, it is used rather than
     * generating a new key.
     */
    public function test_get_encryption_key_uses_existing_config_value(): void {
        $this->purge_key_caches();

        $expected = \Defuse\Crypto\Key::createNewRandomKey()->saveToAsciiSafeString();
        set_config('oauth2_encryption_key', $expected, null);

        $key = (new setup())->get_encryption_key();

        $this->assertEquals($expected, $key);
        $this->assertFileExists(make_localcache_directory('oauth2') . '/oauth2_encryption_key.pem');
        $this->assertFileExists(make_cache_directory('oauth2') . '/oauth2_encryption_key.pem');
    }

    /**
     * When a local cache file already exists, it is returned directly without consulting config.
     */
    public function test_get_encryption_key_reads_from_local_cache_first(): void {
        $this->purge_key_caches();

        $expected = "not a real key, but this is all that is needed to prove the cache is used";
        $path = make_localcache_directory('oauth2') . '/oauth2_encryption_key.pem';
        file_put_contents($path, $expected);

        $key = (new setup())->get_encryption_key();

        $this->assertEquals($expected, $key);
        $this->assertEmpty(get_config('core', 'oauth2_encryption_key'));
    }

    /**
     * configure_keys() generates and caches both the private and encryption keys.
     */
    public function test_configure_keys_configures_both_keys(): void {
        $this->purge_key_caches();

        (new setup())->configure_keys();

        $this->assertNotEmpty(get_config('core', 'oauth2_private_key'));
        $this->assertNotEmpty(get_config('core', 'oauth2_encryption_key'));
        $this->assertFileExists(make_localcache_directory('oauth2') . '/oauth2_private_key.pem');
        $this->assertFileExists(make_localcache_directory('oauth2') . '/oauth2_encryption_key.pem');
    }
}
