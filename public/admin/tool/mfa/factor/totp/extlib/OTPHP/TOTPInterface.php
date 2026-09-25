<?php

declare(strict_types=1);

namespace OTPHP;

interface TOTPInterface extends OTPInterface
{
    public const DEFAULT_PERIOD = 30;

    public const DEFAULT_EPOCH = 0;

    /**
     * Create a new TOTP object.
     *
     * If the secret is null, a random 64 bytes secret will be generated.
     *
     * @param null|non-empty-string $secret
     * @param positive-int $period
     * @param non-empty-string $digest
     * @param positive-int $digits
     *
     * @deprecated Deprecated since v11.1, use ::createFromSecret or ::generate instead
     */
    public static function create(
        null|string $secret = null,
        int $period = self::DEFAULT_PERIOD,
        string $digest = self::DEFAULT_DIGEST,
        int $digits = self::DEFAULT_DIGITS
    ): self;

    /**
     * @deprecated Deprecated since v11.4, use {@see self::withPeriod()} instead
     */
    public function setPeriod(int $period): void;

    public function withPeriod(int $period): self;

    /**
     * @deprecated Deprecated since v11.4, use {@see self::withEpoch()} instead
     */
    public function setEpoch(int $epoch): void;

    public function withEpoch(int $epoch): self;

    /**
     * Return the TOTP at the current time.
     *
     * @return non-empty-string
     */
    public function now(): string;

    /**
     * Get the period of time for OTP generation (a non-null positive integer, in second).
     */
    public function getPeriod(): int;

    public function expiresIn(): int;

    public function getEpoch(): int;
}
