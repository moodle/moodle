<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace OTPHP;

interface TOTPInterface extends OTPInterface
{
    /**
     * @return string Return the TOTP at the current time
     */
    public function now(): string;

    /**
     * @return int Get the period of time for OTP generation (a non-null positive integer, in second)
     */
    public function getPeriod(): int;
}
