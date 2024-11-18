<?php

declare(strict_types=1);

namespace SimpleSAML\Utils;

use SimpleSAML\Configuration;
use SimpleSAML\Error;

/**
 * A class for cryptography-related functions.
 *
 * @package SimpleSAMLphp
 */

class Crypto
{
    /**
     * Decrypt data using AES-256-CBC and the key provided as a parameter.
     *
     * @param string $ciphertext The HMAC of the encrypted data, the IV used and the encrypted data, concatenated.
     * @param string $secret The secret to use to decrypt the data.
     *
     * @return string The decrypted data.
     * @throws \InvalidArgumentException If $ciphertext is not a string.
     * @throws Error\Exception If the openssl module is not loaded.
     *
     * @see \SimpleSAML\Utils\Crypto::aesDecrypt()
     */
    private static function aesDecryptInternal(string $ciphertext, string $secret): string
    {
        /** @var int $len */
        $len = mb_strlen($ciphertext, '8bit');
        if ($len < 48) {
            throw new \InvalidArgumentException(
                'Input parameter "$ciphertext" must be a string with more than 48 characters.'
            );
        }
        if (!function_exists("openssl_decrypt")) {
            throw new Error\Exception("The openssl PHP module is not loaded.");
        }

        // derive encryption and authentication keys from the secret
        $key  = openssl_digest($secret, 'sha512');

        $hmac = mb_substr($ciphertext, 0, 32, '8bit');
        $iv   = mb_substr($ciphertext, 32, 16, '8bit');
        $msg  = mb_substr($ciphertext, 48, $len - 48, '8bit');

        // authenticate the ciphertext
        if (self::secureCompare(hash_hmac('sha256', $iv . $msg, substr($key, 64, 64), true), $hmac)) {
            $plaintext = openssl_decrypt(
                $msg,
                'AES-256-CBC',
                substr($key, 0, 64),
                defined('OPENSSL_RAW_DATA') ? OPENSSL_RAW_DATA : 1,
                $iv
            );

            if ($plaintext !== false) {
                return $plaintext;
            }
        }

        throw new Error\Exception("Failed to decrypt ciphertext.");
    }


    /**
     * Decrypt data using AES-256-CBC and the system-wide secret salt as key.
     *
     * @param string $ciphertext The HMAC of the encrypted data, the IV used and the encrypted data, concatenated.
     *
     * @return string The decrypted data.
     * @throws \InvalidArgumentException If $ciphertext is not a string.
     * @throws Error\Exception If the openssl module is not loaded.
     *
     * @author Andreas Solberg, UNINETT AS <andreas.solberg@uninett.no>
     * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
     */
    public static function aesDecrypt($ciphertext)
    {
        return self::aesDecryptInternal($ciphertext, Config::getSecretSalt());
    }


    /**
     * Encrypt data using AES-256-CBC and the key provided as a parameter.
     *
     * @param string $data The data to encrypt.
     * @param string $secret The secret to use to encrypt the data.
     *
     * @return string An HMAC of the encrypted data, the IV and the encrypted data, concatenated.
     * @throws \InvalidArgumentException If $data is not a string.
     * @throws Error\Exception If the openssl module is not loaded.
     *
     * @see \SimpleSAML\Utils\Crypto::aesEncrypt()
     */
    private static function aesEncryptInternal(string $data, string $secret): string
    {
        if (!function_exists("openssl_encrypt")) {
            throw new Error\Exception('The openssl PHP module is not loaded.');
        }

        // derive encryption and authentication keys from the secret
        $key = openssl_digest($secret, 'sha512');

        // generate a random IV
        $iv = openssl_random_pseudo_bytes(16);

        // encrypt the message
        /** @var string|false $ciphertext */
        $ciphertext = openssl_encrypt(
            $data,
            'AES-256-CBC',
            substr($key, 0, 64),
            defined('OPENSSL_RAW_DATA') ? OPENSSL_RAW_DATA : 1,
            $iv
        );

        if ($ciphertext === false) {
            throw new Error\Exception("Failed to encrypt plaintext.");
        }

        // return the ciphertext with proper authentication
        return hash_hmac('sha256', $iv . $ciphertext, substr($key, 64, 64), true) . $iv . $ciphertext;
    }


    /**
     * Encrypt data using AES-256-CBC and the system-wide secret salt as key.
     *
     * @param string $data The data to encrypt.
     *
     * @return string An HMAC of the encrypted data, the IV and the encrypted data, concatenated.
     * @throws \InvalidArgumentException If $data is not a string.
     * @throws Error\Exception If the openssl module is not loaded.
     *
     * @author Andreas Solberg, UNINETT AS <andreas.solberg@uninett.no>
     * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
     */
    public static function aesEncrypt($data)
    {
        return self::aesEncryptInternal($data, Config::getSecretSalt());
    }


    /**
     * Convert data from DER to PEM encoding.
     *
     * @param string $der Data encoded in DER format.
     * @param string $type The type of data we are encoding, as expressed by the PEM header. Defaults to "CERTIFICATE".
     * @return string The same data encoded in PEM format.
     * @see RFC7648 for known types and PEM format specifics.
     */
    public static function der2pem($der, $type = 'CERTIFICATE')
    {
        return "-----BEGIN " . $type . "-----\n" .
            chunk_split(base64_encode($der), 64, "\n") .
            "-----END " . $type . "-----\n";
    }


    /**
     * Load a private key from metadata.
     *
     * This function loads a private key from a metadata array. It looks for the following elements:
     * - 'privatekey': Name of a private key file in the cert-directory.
     * - 'privatekey_pass': Password for the private key.
     *
     * It returns and array with the following elements:
     * - 'PEM': Data for the private key, in PEM-format.
     * - 'password': Password for the private key.
     *
     * @param \SimpleSAML\Configuration $metadata The metadata array the private key should be loaded from.
     * @param bool                      $required Whether the private key is required. If this is true, a
     * missing key will cause an exception. Defaults to false.
     * @param string                    $prefix The prefix which should be used when reading from the metadata
     * array. Defaults to ''.
     * @param bool                      $full_path Whether the filename found in the configuration contains the
     * full path to the private key or not. Default to false.
     *
     * @return array|NULL Extracted private key, or NULL if no private key is present.
     * @throws \InvalidArgumentException If $required is not boolean or $prefix is not a string.
     * @throws Error\Exception If no private key is found in the metadata, or it was not possible to load
     *     it.
     *
     * @author Andreas Solberg, UNINETT AS <andreas.solberg@uninett.no>
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     */
    public static function loadPrivateKey(Configuration $metadata, $required = false, $prefix = '', $full_path = false)
    {
        if (!is_bool($required) || !is_string($prefix) || !is_bool($full_path)) {
            throw new \InvalidArgumentException('Invalid input parameters.');
        }

        $file = $metadata->getString($prefix . 'privatekey', null);
        if ($file === null) {
            // no private key found
            if ($required) {
                throw new Error\Exception('No private key found in metadata.');
            } else {
                return null;
            }
        }

        if (!$full_path) {
            $file = Config::getCertPath($file);
        }

        $data = @file_get_contents($file);
        if ($data === false) {
            throw new Error\Exception('Unable to load private key from file "' . $file . '"');
        }

        $ret = [
            'PEM' => $data,
            'password' => $metadata->getString($prefix . 'privatekey_pass', null),
        ];

        return $ret;
    }


    /**
     * Get public key or certificate from metadata.
     *
     * This function implements a function to retrieve the public key or certificate from a metadata array.
     *
     * It will search for the following elements in the metadata:
     * - 'certData': The certificate as a base64-encoded string.
     * - 'certificate': A file with a certificate or public key in PEM-format.
     * - 'certFingerprint': The fingerprint of the certificate. Can be a single fingerprint, or an array of multiple
     * valid fingerprints. (deprecated)
     *
     * This function will return an array with these elements:
     * - 'PEM': The public key/certificate in PEM-encoding.
     * - 'certData': The certificate data, base64 encoded, on a single line. (Only present if this is a certificate.)
     * - 'certFingerprint': Array of valid certificate fingerprints. (Deprecated. Only present if this is a
     *   certificate.)
     *
     * @param \SimpleSAML\Configuration $metadata The metadata.
     * @param bool                      $required Whether the public key is required. If this is TRUE, a missing key
     *     will cause an exception. Default is FALSE.
     * @param string                    $prefix The prefix which should be used when reading from the metadata array.
     *     Defaults to ''.
     *
     * @return array|NULL Public key or certificate data, or NULL if no public key or certificate was found.
     * @throws \InvalidArgumentException If $metadata is not an instance of \SimpleSAML\Configuration, $required is not
     *     boolean or $prefix is not a string.
     * @throws Error\Exception If no public key is found in the metadata, or it was not possible to load
     *     it.
     *
     * @author Andreas Solberg, UNINETT AS <andreas.solberg@uninett.no>
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     * @author Lasse Birnbaum Jensen
     */
    public static function loadPublicKey(Configuration $metadata, $required = false, $prefix = '')
    {
        if (!is_bool($required) || !is_string($prefix)) {
            throw new \InvalidArgumentException('Invalid input parameters.');
        }

        $keys = $metadata->getPublicKeys(null, false, $prefix);
        if (!empty($keys)) {
            foreach ($keys as $key) {
                if ($key['type'] !== 'X509Certificate') {
                    continue;
                }
                if ($key['signing'] !== true) {
                    continue;
                }
                $certData = $key['X509Certificate'];
                $pem = "-----BEGIN CERTIFICATE-----\n" .
                    chunk_split($certData, 64) .
                    "-----END CERTIFICATE-----\n";
                $certFingerprint = strtolower(sha1(base64_decode($certData)));

                return [
                    'certData'        => $certData,
                    'PEM'             => $pem,
                    'certFingerprint' => [$certFingerprint],
                ];
            }
            // no valid key found
        } elseif ($metadata->hasValue($prefix . 'certFingerprint')) {
            // we only have a fingerprint available
            $fps = $metadata->getArrayizeString($prefix . 'certFingerprint');

            // normalize fingerprint(s) - lowercase and no colons
            foreach ($fps as &$fp) {
                assert(is_string($fp));
                $fp = strtolower(str_replace(':', '', $fp));
            }

            /*
             * We can't build a full certificate from a fingerprint, and may as well return an array with only the
             * fingerprint(s) immediately.
             */
            return ['certFingerprint' => $fps];
        }

        // no public key/certificate available
        if ($required) {
            throw new Error\Exception('No public key / certificate found in metadata.');
        } else {
            return null;
        }
    }


    /**
     * Convert from PEM to DER encoding.
     *
     * @param string $pem Data encoded in PEM format.
     * @return string The same data encoded in DER format.
     * @throws \InvalidArgumentException If $pem is not encoded in PEM format.
     * @see RFC7648 for PEM format specifics.
     */
    public static function pem2der($pem)
    {
        $pem   = trim($pem);
        $begin = "-----BEGIN ";
        $end   = "-----END ";
        $lines = explode("\n", $pem);
        $last  = count($lines) - 1;

        if (strpos($lines[0], $begin) !== 0) {
            throw new \InvalidArgumentException("pem2der: input is not encoded in PEM format.");
        }
        unset($lines[0]);
        if (strpos($lines[$last], $end) !== 0) {
            throw new \InvalidArgumentException("pem2der: input is not encoded in PEM format.");
        }
        unset($lines[$last]);

        return base64_decode(implode($lines));
    }


    /**
     * This function hashes a password with a given algorithm.
     *
     * @param string $password The password to hash.
     * @param string|null $algorithm @deprecated The hashing algorithm, uppercase, optionally
     *     prepended with 'S' (salted). See hash_algos() for a complete list of hashing algorithms.
     * @param string|null $salt @deprecated An optional salt to use.
     *
     * @return string The hashed password.
     * @throws \InvalidArgumentException If the input parameter is not a string.
     * @throws Error\Exception If the algorithm specified is not supported.
     *
     * @see hash_algos()
     *
     * @author Dyonisius Visser, TERENA <visser@terena.org>
     * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
     */
    public static function pwHash($password, $algorithm = null, $salt = null)
    {
        if (!is_null($algorithm)) {
            // @deprecated Old-style
            if (!is_string($algorithm) || !is_string($password)) {
                throw new \InvalidArgumentException('Invalid input parameters.');
            }
            // hash w/o salt
            if (in_array(strtolower($algorithm), hash_algos(), true)) {
                $alg_str = '{' . str_replace('SHA1', 'SHA', $algorithm) . '}'; // LDAP compatibility
                $hash = hash(strtolower($algorithm), $password, true);
                return $alg_str . base64_encode($hash);
            }
            // hash w/ salt
            if ($salt === null) {
                // no salt provided, generate one
                // default 8 byte salt, but 4 byte for LDAP SHA1 hashes
                $bytes = ($algorithm == 'SSHA1') ? 4 : 8;
                $salt = openssl_random_pseudo_bytes($bytes);
            }

            if ($algorithm[0] == 'S' && in_array(substr(strtolower($algorithm), 1), hash_algos(), true)) {
                $alg = substr(strtolower($algorithm), 1); // 'sha256' etc
                $alg_str = '{' . str_replace('SSHA1', 'SSHA', $algorithm) . '}'; // LDAP compatibility
                $hash = hash($alg, $password . $salt, true);
                return $alg_str . base64_encode($hash . $salt);
            }
            throw new Error\Exception('Hashing algorithm \'' . strtolower($algorithm) . '\' is not supported');
        } else {
            if (!is_string($password)) {
                throw new \InvalidArgumentException('Invalid input parameter.');
            } elseif (!is_string($hash = password_hash($password, PASSWORD_DEFAULT))) {
                throw new \InvalidArgumentException('Error while hashing password.');
            }
            return $hash;
        }
    }


    /**
     * Compare two strings securely.
     *
     * This method checks if two strings are equal in constant time, avoiding timing attacks. Use it every time we need
     * to compare a string with a secret that shouldn't be leaked, i.e. when verifying passwords, one-time codes, etc.
     *
     * @param string $known A known string.
     * @param string $user A user-provided string to compare with the known string.
     *
     * @return bool True if both strings are equal, false otherwise.
     */
    public static function secureCompare($known, $user)
    {
        return hash_equals($known, $user);
    }


    /**
     * This function checks if a password is valid
     *
     * @param string $hash The password as it appears in password file, optionally prepended with algorithm.
     * @param string $password The password to check in clear.
     *
     * @return boolean True if the hash corresponds with the given password, false otherwise.
     * @throws \InvalidArgumentException If the input parameters are not strings.
     * @throws Error\Exception If the algorithm specified is not supported.
     *
     * @author Dyonisius Visser, TERENA <visser@terena.org>
     */
    public static function pwValid($hash, $password)
    {
        if (!is_string($hash) || !is_string($password)) {
            throw new \InvalidArgumentException('Invalid input parameters.');
        }

        // Prior to PHP 7.4 password_get_info() would set the algo to 0 instead of NULL when it's not detected
        $info = password_get_info($password);
        if ($info['algo'] !== null && $info['algo'] !== 0) {
            throw new Error\Exception("Cannot use a hash value for authentication.");
        }

        if (password_verify($password, $hash)) {
            return true;
        }
        // return $hash === $password

        // @deprecated remove everything below this line for 2.0
        // match algorithm string (e.g. '{SSHA256}', '{MD5}')
        if (preg_match('/^{(.*?)}(.*)$/', $hash, $matches)) {
            // LDAP compatibility
            $alg = preg_replace('/^(S?SHA)$/', '${1}1', $matches[1]);

            // hash w/o salt
            if (in_array(strtolower($alg), hash_algos(), true)) {
                return self::secureCompare($hash, self::pwHash($password, $alg));
            }

            // hash w/ salt
            if ($alg[0] === 'S' && in_array(substr(strtolower($alg), 1), hash_algos(), true)) {
                $php_alg = substr(strtolower($alg), 1);

                // get hash length of this algorithm to learn how long the salt is
                $hash_length = strlen(hash($php_alg, '', true));
                $salt = substr(base64_decode($matches[2]), $hash_length);
                return self::secureCompare($hash, self::pwHash($password, $alg, $salt));
            }
            throw new Error\Exception('Hashing algorithm \'' . strtolower($alg) . '\' is not supported');
        } else {
            return $hash === $password;
        }
    }
}
