<?php

declare(strict_types=1);

namespace OTPHP;

use function is_int;
use OTPHP\Exception\InvalidParameterException;
use Psr\Clock\ClockInterface;

/**
 * @readonly
 *
 * @see \OTPHP\Test\TOTPTest
 */
final class TOTP extends OTP implements TOTPInterface
{
    private readonly ClockInterface $clock;

    public function __construct(string $secret, ?ClockInterface $clock = null)
    {
        parent::__construct($secret);
        if ($clock === null) {
            trigger_deprecation(
                'spomky-labs/otphp',
                '11.3.0',
                'The parameter "$clock" will become mandatory in 12.0.0. Please set a valid PSR Clock implementation instead of "null".'
            );
            $clock = new InternalClock();
        }

        $this->clock = $clock;
    }

    public static function create(
        null|string $secret = null,
        int $period = self::DEFAULT_PERIOD,
        string $digest = self::DEFAULT_DIGEST,
        int $digits = self::DEFAULT_DIGITS,
        int $epoch = self::DEFAULT_EPOCH,
        ?ClockInterface $clock = null,
        ?int $secretSize = null
    ): self {
        $totp = $secret !== null
            ? self::createFromSecret($secret, $clock)
            : self::generate($clock, $secretSize)
        ;
        $totp->setPeriod($period);
        $totp->setDigest($digest);
        $totp->setDigits($digits);
        $totp->setEpoch($epoch);

        return $totp;
    }

    public static function createFromSecret(string $secret, ?ClockInterface $clock = null): self
    {
        $totp = new self($secret, $clock);
        $totp->setPeriod(self::DEFAULT_PERIOD);
        $totp->setDigest(self::DEFAULT_DIGEST);
        $totp->setDigits(self::DEFAULT_DIGITS);
        $totp->setEpoch(self::DEFAULT_EPOCH);

        return $totp;
    }

    /**
     * @param positive-int|null $secretSize
     */
    public static function generate(?ClockInterface $clock = null, ?int $secretSize = null): self
    {
        return self::createFromSecret(self::generateSecret($secretSize), $clock);
    }

    public function getPeriod(): int
    {
        $value = $this->getParameter('period');
        (is_int($value) && $value > 0) || throw new InvalidParameterException(
            'Invalid "period" parameter.',
            'period',
            $value
        );

        return $value;
    }

    public function getEpoch(): int
    {
        $value = $this->getParameter('epoch');
        (is_int($value) && $value >= 0) || throw new InvalidParameterException(
            'Invalid "epoch" parameter.',
            'epoch',
            $value
        );

        return $value;
    }

    public function expiresIn(): int
    {
        $period = $this->getPeriod();

        return $period - (($this->clock->now()->getTimestamp() - $this->getEpoch()) % $period);
    }

    /**
     * The OTP at the specified input.
     *
     * @param 0|positive-int $input
     */
    public function at(int $input): string
    {
        return $this->generateOTP($this->timecode($input));
    }

    public function now(): string
    {
        $timestamp = $this->clock->now()
            ->getTimestamp();
        $timestamp >= 0 || throw new InvalidParameterException(
            'The timestamp must return a positive integer.',
            'timestamp',
            $timestamp
        );

        return $this->at($timestamp);
    }

    /**
     * If no timestamp is provided, the OTP is verified at the actual timestamp. When used, the leeway parameter will
     * allow time drift. The passed value is in seconds.
     *
     * @param 0|positive-int $timestamp
     * @param null|0|positive-int $leeway
     */
    public function verify(string $otp, null|int $timestamp = null, null|int $leeway = null): bool
    {
        $timestamp ??= $this->clock->now()
            ->getTimestamp();
        $timestamp >= 0 || throw new InvalidParameterException(
            'Timestamp must be at least 0.',
            'timestamp',
            $timestamp
        );

        if ($leeway === null) {
            return $this->compareOTP($this->at($timestamp), $otp);
        }

        $leeway = abs($leeway);
        $leeway < $this->getPeriod() || throw new InvalidParameterException(
            'The leeway must be lower than the TOTP period',
            'leeway',
            $leeway
        );
        $timestampMinusLeeway = $timestamp - $leeway;
        $timestampMinusLeeway >= 0 || throw new InvalidParameterException(
            'The timestamp must be greater than or equal to the leeway.',
            'timestamp',
            $timestamp
        );

        return $this->compareOTP($this->at($timestampMinusLeeway), $otp)
            || $this->compareOTP($this->at($timestamp), $otp)
            || $this->compareOTP($this->at($timestamp + $leeway), $otp);
    }

    public function getProvisioningUri(): string
    {
        $params = [];
        if ($this->getPeriod() !== 30) {
            $params['period'] = $this->getPeriod();
        }

        if ($this->getEpoch() !== 0) {
            $params['epoch'] = $this->getEpoch();
        }

        return $this->generateURI('totp', $params);
    }

    public function setPeriod(int $period): void
    {
        $this->setParameter('period', $period);
    }

    public function withPeriod(int $period): self
    {
        $otp = clone $this;
        $otp->setParameter('period', $period);

        return $otp;
    }

    public function setEpoch(int $epoch): void
    {
        $this->setParameter('epoch', $epoch);
    }

    public function withEpoch(int $epoch): self
    {
        $otp = clone $this;
        $otp->setParameter('epoch', $epoch);

        return $otp;
    }

    /**
     * @return array<non-empty-string, callable>
     */
    protected function getParameterMap(): array
    {
        return [
            ...parent::getParameterMap(),
            'period' => static function ($value): int {
                (int) $value > 0 || throw new InvalidParameterException('Period must be at least 1.', 'period', $value);

                return (int) $value;
            },
            'epoch' => static function ($value): int {
                (int) $value >= 0 || throw new InvalidParameterException(
                    'Epoch must be greater than or equal to 0.',
                    'epoch',
                    $value
                );

                return (int) $value;
            },
        ];
    }

    /**
     * @param array<non-empty-string, mixed> $options
     */
    protected function filterOptions(array &$options): void
    {
        parent::filterOptions($options);

        if (isset($options['epoch']) && $options['epoch'] === 0) {
            unset($options['epoch']);
        }

        ksort($options);
    }

    /**
     * @param 0|positive-int $timestamp
     *
     * @return 0|positive-int
     */
    private function timecode(int $timestamp): int
    {
        $timecode = (int) floor(($timestamp - $this->getEpoch()) / $this->getPeriod());
        $timecode >= 0 || throw new InvalidParameterException(
            'Timecode must be at least 0. The timestamp must be greater than or equal to the epoch.',
            'timecode',
            $timecode
        );

        return $timecode;
    }
}
