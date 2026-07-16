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
 * OAuth2 Setup and Configuration.
 *
 * Note: This class is used during installation and upgrade.
 * It should have minimal dependencies on other Moodle code.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setup {
    /**
     * Configure OAuth2 keys.
     */
    public function configure_keys(): void {
        $this->get_private_key();
        $this->get_encryption_key();
    }

    /**
     * Fetch the OAuth2 Private key.
     *
     * @return string
     */
    public function get_private_key(): string {
        if ($content = $this->retrieve_from_cache('oauth2_private_key.pem')) {
            return $content;
        }

        $content = $this->retrieve_from_config('oauth2_private_key');

        if (empty($content)) {
            $keydata = openssl_pkey_new([
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
                'private_key_bits' => 2048,
            ]);

            $content = '';
            openssl_pkey_export($keydata, $content);

            set_config('oauth2_private_key', $content, null);
        }

        $this->store_in_shared_cache('oauth2_private_key.pem', $content);
        $this->store_in_local_cache('oauth2_private_key.pem', $content);

        return $content;
    }

    /**
     * Fetch the OAuth2 Public key.
     *
     * @return string
     */
    public function get_public_key(): string {
        // The public key is only stored in the cache, as it can be derived from the private key.
        if ($content = $this->retrieve_from_cache('oauth2_public_key.pem')) {
            return $content;
        }

        $details = openssl_pkey_get_details(openssl_pkey_get_private($this->get_private_key()));
        $publickey = $details['key'];

        $this->store_in_shared_cache('oauth2_public_key.pem', $publickey);
        $this->store_in_local_cache('oauth2_public_key.pem', $publickey);

        return $publickey;
    }

    /**
     * Fetch the OAuth2 Encryption key.
     *
     * @return string
     */
    public function get_encryption_key(): string {
        if ($content = $this->retrieve_from_cache('oauth2_encryption_key.pem')) {
            return $content;
        }

        $content = $this->retrieve_from_config('oauth2_encryption_key');

        if (empty($content)) {
            $key = \Defuse\Crypto\Key::createNewRandomKey();
            $content = $key->saveToAsciiSafeString();

            set_config('oauth2_encryption_key', $content, null);
        }

        $this->store_in_shared_cache('oauth2_encryption_key.pem', $content);
        $this->store_in_local_cache('oauth2_encryption_key.pem', $content);

        return $content;
    }

    /**
     * Get the local cache location.
     *
     * @param string $filename
     * @return string
     */
    private function get_local_cache_location(string $filename): string {
        $dirpath = make_localcache_directory('oauth2');
        return "{$dirpath}/{$filename}";
    }

    /**
     * Get the shared cache location.
     *
     * @param string $filename
     * @return string
     */
    private function get_shared_cache_location(string $filename): string {
        $dirpath = make_cache_directory('oauth2');
        return "{$dirpath}/{$filename}";
    }

    /**
     * Retrieve a file from the cache directory.
     *
     * @param string $filename
     * @return false|string|null
     */
    private function retrieve_from_cache(string $filename): false|string|null {
        // We cache in the localcachedir, and then the cachedir,
        // to avoid having to read from the database on every request.
        // This allows us to avoid loading the full Moodle environment
        // on every request, which is important for performance.
        $cachelocation = $this->get_local_cache_location($filename);
        if (file_exists($cachelocation)) {
            return file_get_contents($cachelocation);
        }

        $cachelocation = $this->get_shared_cache_location($filename);
        if (file_exists($cachelocation)) {
            $content = file_get_contents($cachelocation);
            $this->store_in_local_cache($filename, $content);

            return $content;
        }

        return null;
    }

    /**
     * Retrieve a property from the database.
     *
     * @param string $property
     * @return string|null
     */
    private function retrieve_from_config(string $property): ?string {
        global $CFG;

        // Not found on disk. Load full Moodle to retrieve from the database.
        if (defined('ABORT_AFTER_CONFIG') && !defined('ABORT_AFTER_CONFIG_CANCEL')) {
            define('ABORT_AFTER_CONFIG_CANCEL', true);
            require("{$CFG->dirroot}/lib/setup.php");
        }

        $value = get_config('core', $property);

        return empty($value) ? null : $value;
    }

    /**
     * Store a file in the cache directory.
     *
     * @param string $filename
     * @param string $data
     * @return void
     */
    private function store_in_local_cache(string $filename, string $data): void {
        $cachelocation = $this->get_local_cache_location($filename);
        $cachelocationtmp = $cachelocation . uniqid();
        file_put_contents($cachelocationtmp, $data);
        rename($cachelocationtmp, $cachelocation);
    }

    /**
     * Store a file in the shared cache directory.
     *
     * @param string $filename
     * @param string $data
     * @return void
     */
    private function store_in_shared_cache(string $filename, string $data): void {
        $cachelocation = $this->get_shared_cache_location($filename);
        $cachelocationtmp = $cachelocation . uniqid();
        file_put_contents($cachelocationtmp, $data);
        rename($cachelocationtmp, $cachelocation);
    }
}
