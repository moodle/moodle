<?php
/**
 * Copyright 2005-2008 Matthew Fonda <mfonda@php.net>
 * Copyright 2008 Philippe Jausions <jausions@php.net>
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Matthew Fonda <mfonda@php.net>
 * @author   Philippe Jausions <jausions@php.net>
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Crypt_Blowfish
 */

/**
 * Mcrypt driver for blowfish encryption.
 *
 * @author    Matthew Fonda <mfonda@php.net>
 * @author    Philippe Jausions <jausions@php.net>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2005-2008 Matthew Fonda
 * @copyright 2008 Philippe Jausions
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Crypt_Blowfish
 */
class Horde_Crypt_Blowfish_Mcrypt extends Horde_Crypt_Blowfish_Base
{
    /**
     * Mcrypt resource.
     *
     * @var resource
     */
    private $_mcrypt;

    /**
     */
    public static function supported()
    {
        return PHP_VERSION_ID < 70100 && extension_loaded('mcrypt');
    }

    /**
     */
    public function __construct($cipher)
    {
        parent::__construct($cipher);

        $this->_mcrypt = mcrypt_module_open(MCRYPT_BLOWFISH, '', $cipher, '');
    }

    /**
     */
    public function encrypt($text)
    {
        mcrypt_generic_init($this->_mcrypt, $this->key, empty($this->iv) ? str_repeat('0', Horde_Crypt_Blowfish::IV_LENGTH) : $this->iv);
        $out = mcrypt_generic($this->_mcrypt, $this->_pad($text));
        mcrypt_generic_deinit($this->_mcrypt);

        return $out;
    }

    /**
     */
    public function decrypt($text)
    {
        mcrypt_generic_init($this->_mcrypt, $this->key, empty($this->iv) ? str_repeat('0', Horde_Crypt_Blowfish::IV_LENGTH) : $this->iv);
        $out = mdecrypt_generic($this->_mcrypt, $this->_pad($text, true));
        mcrypt_generic_deinit($this->_mcrypt);

        return $this->_unpad($out);
    }

    /**
     */
    public function setIv($iv = null)
    {
        $this->iv = is_null($iv)
            ? mcrypt_create_iv(Horde_Crypt_Blowfish::IV_LENGTH, MCRYPT_RAND)
            : $iv;
    }

}
