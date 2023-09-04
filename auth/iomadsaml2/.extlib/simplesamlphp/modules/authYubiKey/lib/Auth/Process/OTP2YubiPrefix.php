<?php

namespace SimpleSAML\Module\authYubiKey\Auth\Process;

/*
 * Copyright (C) 2009  Simon Josefsson <simon@yubico.com>.
 *
 * This file is part of SimpleSAMLphp
 *
 * SimpleSAMLphp is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License
 * as published by the Free Software Foundation; either version 3 of
 * the License, or (at your option) any later version.
 *
 * SimpleSAMLphp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License License along with GNU SASL Library; if not, write to the
 * Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301, USA.
 *
 */

/**
 * A processing filter to replace the 'otp' attribute with an attribute
 * 'yubiPrefix' that contains the static YubiKey prefix.
 *
 * Before:
 *   otp=ekhgjhbctrgnubeeklijcibbgjnbtjlffdnjbhjluvur
 *
 * After:
 *   otp undefined
 *   yubiPrefix=ekhgjhbctrgn
 *
 * You use it by adding it as an authentication filter in config.php:
 *
 *      'authproc.idp' => array(
 *    ...
 *          90 => 'authYubiKey:OTP2YubiPrefix',
 *    ...
 *      );
 *
 */

class OTP2YubiPrefix extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * Filter out YubiKey 'otp' attribute and replace it with
     * a 'yubiPrefix' attribute that leaves out the dynamic part.
     *
     * @param array &$state  The state we should update.
     * @return void
     */
    public function process(&$state)
    {
        assert(is_array($state));
        assert(array_key_exists('Attributes', $state));
        $attributes = $state['Attributes'];

        \SimpleSAML\Logger::debug('OTP2YubiPrefix: enter with attributes: '.implode(',', array_keys($attributes)));

        $otps = $attributes['otp'];
        $otp = $otps['0'];

        $token_size = 32;
        $identity = substr($otp, 0, strlen($otp) - $token_size);

        $attributes['yubiPrefix'] = [$identity];

        \SimpleSAML\Logger::info(
            'OTP2YubiPrefix: otp: '.$otp.' identity: '.$identity.' (otp keys: '.implode(',', array_keys($otps)).')'
        );

        unset($attributes['otp']);

        \SimpleSAML\Logger::debug('OTP2YubiPrefix: leaving with attributes: '.implode(',', array_keys($attributes)));
    }
}
