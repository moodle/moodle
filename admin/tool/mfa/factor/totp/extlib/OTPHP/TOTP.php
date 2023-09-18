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

use Assert\Assertion;

final class TOTP extends OTP implements TOTPInterface
{
    /**
     * TOTP constructor.
     *
     * @param string|null $secret
     * @param int         $period
     * @param string      $digest
     * @param int         $digits
     * @param int         $epoch
     */
    protected function __construct($secret, int $period, string $digest, int $digits, int $epoch = 0)
    {
        parent::__construct($secret, $digest, $digits);
        $this->setPeriod($period);
        $this->setEpoch($epoch);
    }

    /**
     * TOTP constructor.
     *
     * @param string|null $secret
     * @param int         $period
     * @param string      $digest
     * @param int         $digits
     * @param int         $epoch
     *
     * @return self
     */
    public static function create($secret = null, int $period = 30, string $digest = 'sha1', int $digits = 6, int $epoch = 0): self
    {
        return new self($secret, $period, $digest, $digits, $epoch);
    }

    /**
     * @param int $period
     */
    protected function setPeriod(int $period)
    {
        $this->setParameter('period', $period);
    }

    /**
     * {@inheritdoc}
     */
    public function getPeriod(): int
    {
        return $this->getParameter('period');
    }

    /**
     * @param int $epoch
     */
    private function setEpoch(int $epoch)
    {
        $this->setParameter('epoch', $epoch);
    }

    /**
     * {@inheritdoc}
     */
    public function getEpoch(): int
    {
        return $this->getParameter('epoch');
    }

    /**
     * {@inheritdoc}
     */
    public function at(int $timestamp): string
    {
        return $this->generateOTP($this->timecode($timestamp));
    }

    /**
     * {@inheritdoc}
     */
    public function now(): string
    {
        return $this->at(time());
    }

    /**
     * If no timestamp is provided, the OTP is verified at the actual timestamp
     * {@inheritdoc}
     */
    public function verify(string $otp, $timestamp = null, $window = null): bool
    {
        $timestamp = $this->getTimestamp($timestamp);

        if (null === $window) {
            return $this->compareOTP($this->at($timestamp), $otp);
        }

        return $this->verifyOtpWithWindow($otp, $timestamp, $window);
    }

    /**
     * @param string $otp
     * @param int    $timestamp
     * @param int    $window
     *
     * @return bool
     */
    private function verifyOtpWithWindow(string $otp, int $timestamp, int $window): bool
    {
        $window = abs($window);

        for ($i = 0; $i <= $window; $i++) {
            $next = (int) $i * $this->getPeriod() + $timestamp;
            $previous = (int) -$i * $this->getPeriod() + $timestamp;
            $valid = $this->compareOTP($this->at($next), $otp) ||
                $this->compareOTP($this->at($previous), $otp);

            if ($valid) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int|null $timestamp
     *
     * @return int
     */
    private function getTimestamp($timestamp): int
    {
        $timestamp = $timestamp ?? time();
        Assertion::greaterOrEqualThan($timestamp, 0, 'Timestamp must be at least 0.');

        return (int) $timestamp;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvisioningUri(): string
    {
        $params = [];
        if (30 !== $this->getPeriod()) {
            $params['period'] = $this->getPeriod();
        }

        if (0 !== $this->getEpoch()) {
            $params['epoch'] = $this->getEpoch();
        }

        return $this->generateURI('totp', $params);
    }

    /**
     * @param int $timestamp
     *
     * @return int
     */
    private function timecode(int $timestamp): int
    {
        return (int) floor(($timestamp - $this->getEpoch()) / $this->getPeriod());
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameterMap(): array
    {
        $v = array_merge(
            parent::getParameterMap(),
            [
                'period' => function ($value) {
                    Assertion::greaterThan((int) $value, 0, 'Period must be at least 1.');

                    return (int) $value;
                },
                'epoch' => function ($value) {
                    Assertion::greaterOrEqualThan((int) $value, 0, 'Epoch must be greater than or equal to 0.');

                    return (int) $value;
                },
            ]
        );

        return $v;
    }

    /**
     * {@inheritdoc}
     */
    protected function filterOptions(array &$options)
    {
        parent::filterOptions($options);

        if (isset($options['epoch']) && 0 === $options['epoch']) {
            unset($options['epoch']);
        }

        ksort($options);
    }
}
