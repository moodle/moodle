<?php
/**
 * Copyright 2015-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Crypt_Blowfish
 */

/**
 * PBKDF2 (Password-Based Key Derivation Function 2) implementation (RFC
 * 2898; PKCS #5 v2.0).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Crypt_Blowfish
 * @link      https://defuse.ca/php-pbkdf2.htm pbkdf2 code released to the
 *            public domain.
 */
class Horde_Crypt_Blowfish_Pbkdf2
{
    /**
     * Hash algorithm used to create key.
     *
     * @var string
     */
    public $hashAlgo;

    /**
     * Number of iterations to use.
     *
     * @var integer
     */
    public $iterations;

    /**
     * Salt.
     *
     * @var string
     */
    public $salt;

    /**
     * The derived key.
     *
     * @var string
     */
    protected $_key;

    /**
     * Constructor.
     *
     * @param string $pass        The password.
     * @param string $key_length  Length of the derived key (in bytes).
     * @param array $opts         Additional options:
     *   - algo: (string) Hash algorithm.
     *   - i_count: (integer) Iteration count.
     *   - salt: (string) The salt to use.
     */
    public function __construct($pass, $key_length, array $opts = array())
    {
        $this->iterations = isset($opts['i_count'])
            ? $opts['i_count']
            : 16384;

        if (($key_length <= 0) || ($this->iterations <= 0)) {
            throw new InvalidArgumentException('Invalid arguments');
        }

        $this->hashAlgo = isset($opts['algo'])
            ? $opts['algo']
            : 'SHA256';

        /* Nice to have, but salt does not need to be cryptographically
         * secure random value. */
        $this->salt = isset($opts['salt'])
            ? $opts['salt']
            : (function_exists('openssl_random_pseudo_bytes')
                  ? openssl_random_pseudo_bytes($key_length)
                  : substr(hash('sha512', new Horde_Support_Randomid(), true), 0, $key_length));

        if (function_exists('hash_pbkdf2')) {
            $this->_key = hash_pbkdf2(
                $this->hashAlgo,
                $pass,
                $this->salt,
                $this->iterations,
                $key_length,
                true
            );
            return;
        }

        $hash_length = strlen(hash($this->hashAlgo, '', true));
        $block_count = ceil($key_length / $hash_length);

        $hash = '';
        for ($i = 1; $i <= $block_count; ++$i) {
            // $i encoded as 4 bytes, big endian.
            $last = $this->salt . pack('N', $i);
            for ($j = 0; $j < $this->iterations; $j++) {
                $last = hash_hmac($this->hashAlgo, $last, $pass, true);
                if ($j) {
                    $xorsum ^= $last;
                } else {
                    $xorsum = $last;
                }
            }
            $hash .= $xorsum;
        }

        $this->_key = substr($hash, 0, $key_length);
    }

    /**
     */
    public function __toString()
    {
        return $this->_key;
    }

}
