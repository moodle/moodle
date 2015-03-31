<?php
/**
 * Copyright 2005-2008 Matthew Fonda <mfonda@php.net>
 * Copyright 2012-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2005-2008 Matthew Fonda
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Crypt_Blowfish
 */

/**
 * Provides blowfish encryption/decryption, with or without a secret key,
 * for PHP strings.
 *
 * @author    Matthew Fonda <mfonda@php.net>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2005-2008 Matthew Fonda
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Crypt_Blowfish
 *
 * @property string $cipher  The cipher block mode ('ecb' or 'cbc').
 * @property string $key  The encryption key in use.
 * @property mixed $iv  The initialization vector (false if using 'ecb').
 */
class Horde_Crypt_Blowfish
{
    // Constants for 'ignore' parameter of constructor.
    const IGNORE_OPENSSL = 1;
    const IGNORE_MCRYPT = 2;

    // Block size for Blowfish
    const BLOCKSIZE = 8;

    // Maximum key size for Blowfish
    const MAXKEYSIZE = 56;

    // IV Length for CBC
    const IV_LENGTH = 8;

    /**
     * Blowfish crypt driver.
     *
     * @var Horde_Crypt_Blowfish_Base
     */
    protected $_crypt;

    /**
     * Constructor.
     *
     * @param string $key  Encryption key.
     * @param array $opts  Additional options:
     *   - cipher: (string) Either 'ecb' or 'cbc'.
     *   - ignore: (integer) A mask of drivers to ignore (IGNORE_* constants).
     *   - iv: (string) IV to use.
     */
    public function __construct($key, array $opts = array())
    {
        $opts = array_merge(array(
            'cipher' => 'ecb',
            'ignore' => 0,
            'iv' => null
        ), $opts);

        if (!($opts['ignore'] & self::IGNORE_OPENSSL) &&
            Horde_Crypt_Blowfish_Openssl::supported()) {
            $this->_crypt = new Horde_Crypt_Blowfish_Openssl($opts['cipher']);
        } elseif (!($opts['ignore'] & self::IGNORE_MCRYPT) &&
                  Horde_Crypt_Blowfish_Mcrypt::supported()) {
            $this->_crypt = new Horde_Crypt_Blowfish_Mcrypt($opts['cipher']);
        } else {
            $this->_crypt = new Horde_Crypt_Blowfish_Php($opts['cipher']);
        }

        $this->setKey($key, $opts['iv']);
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'cipher':
        case 'key':
        case 'iv':
            return $this->_crypt->$name;
        }
    }

    /**
     * Encrypts a string.
     *
     * @param string $text  The string to encrypt.
     *
     * @return string  The ciphertext.
     * @throws Horde_Crypt_Blowfish_Exception
     */
    public function encrypt($text)
    {
        if (!is_string($text)) {
            throw new Horde_Crypt_Blowfish_Exception('Data to encrypt must be a string.');
        }

        return $this->_crypt->encrypt($text);
    }

    /**
     * Decrypts a string.
     *
     * @param string $text  The string to decrypt.
     *
     * @return string  The plaintext.
     * @throws Horde_Crypt_Blowfish_Exception
     */
    public function decrypt($text)
    {
        if (!is_string($text)) {
            throw new Horde_Crypt_Blowfish_Exception('Data to decrypt must be a string.');
        }

        return $this->_crypt->decrypt($text);
    }

    /**
     * Sets the secret key.
     *
     * The key must be non-zero, and less than or equal to MAXKEYSIZE
     * characters (bytes) in length.
     *
     * @param string $key  Key must be non-empty and less than MAXKEYSIZE
     *                     bytes in length.
     * @param string $iv   The initialization vector to use. Only needed for
     *                     'cbc' cipher. If null, an IV is automatically
     *                     generated.
     *
     * @throws Horde_Crypt_Blowfish_Exception
     */
    public function setKey($key, $iv = null)
    {
        if (!is_string($key)) {
            throw new Horde_Crypt_Blowfish_Exception('Encryption key must be a string.');
        }

        $len = strlen($key);
        if (($len > self::MAXKEYSIZE) || ($len == 0)) {
            throw new Horde_Crypt_Blowfish_Exception(sprintf('Encryption key must be less than %d characters (bytes) and non-zero. Supplied key length: %d', self::MAXKEYSIZE, $len));
        }

        $this->_crypt->key = $key;

        switch ($this->_crypt->cipher) {
        case 'cbc':
            if (is_null($iv)) {
                if (is_null($this->iv)) {
                    $this->_crypt->setIv();
                }
            } else {
                $iv = substr($iv, 0, self::IV_LENGTH);
                if (($len = strlen($iv)) < self::IV_LENGTH) {
                    $iv .= str_repeat(chr(0), self::IV_LENGTH - $len);
                }
                $this->_crypt->setIv($iv);
            }
            break;

        case 'ecb':
            $this->iv = false;
            break;
        }
    }

}
