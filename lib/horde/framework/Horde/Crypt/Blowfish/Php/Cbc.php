<?php
/**
 * Copyright 2005-2008 Matthew Fonda <mfonda@php.net>
 * Copyright 2008 Philippe Jausions <jausions@php.net>
 * Copyright 2012-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2005-2008 Matthew Fonda
 * @copyright 2008 Philippe Jausions
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Crypt_Blowfish
 */

/**
 * PHP implementation of the Blowfish algorithm in CBC mode.
 *
 * @author    Matthew Fonda <mfonda@php.net>
 * @author    Philippe Jausions <jausions@php.net>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2005-2008 Matthew Fonda
 * @copyright 2008 Philippe Jausions
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Crypt_Blowfish
 */
class Horde_Crypt_Blowfish_Php_Cbc extends Horde_Crypt_Blowfish_Php_Base
{
    /**
     */
    public function encrypt($text, $iv)
    {
        $cipherText = '';
        $len = strlen($text);

        list(, $Xl, $Xr) = unpack('N2', substr($text, 0, 8) ^ $iv);
        $this->_encipher($Xl, $Xr);
        $cipherText .= pack('N2', $Xl, $Xr);

        for ($i = 8; $i < $len; $i += 8) {
            list(, $Xl, $Xr) = unpack('N2', substr($text, $i, 8) ^ substr($cipherText, $i - 8, 8));
            $this->_encipher($Xl, $Xr);
            $cipherText .= pack('N2', $Xl, $Xr);
        }

        return $cipherText;
    }

    /**
     */
    public function decrypt($text, $iv)
    {
        $plainText = '';
        $len = strlen($text);

        list(, $Xl, $Xr) = unpack('N2', substr($text, 0, 8));
        $this->_decipher($Xl, $Xr);
        $plainText .= (pack('N2', $Xl, $Xr) ^ $iv);

        for ($i = 8; $i < $len; $i += 8) {
            list(, $Xl, $Xr) = unpack('N2', substr($text, $i, 8));
            $this->_decipher($Xl, $Xr);
            $plainText .= (pack('N2', $Xl, $Xr) ^ substr($text, $i - 8, 8));
        }

        return $plainText;
    }

}
