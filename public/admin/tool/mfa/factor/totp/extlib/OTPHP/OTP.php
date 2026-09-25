<?php

declare(strict_types=1);

namespace OTPHP;

use function array_key_exists;
use function chr;
use function count;
use Exception;
use function in_array;
use function is_int;
use function is_string;
use OTPHP\Exception\InvalidLabelException;
use OTPHP\Exception\InvalidParameterException;
use OTPHP\Exception\ParameterNotFoundException;
use OTPHP\Exception\SecretDecodingException;
use ParagonIE\ConstantTime\Base32;
use function sprintf;
use const STR_PAD_LEFT;
use function strlen;

/**
 * @readonly
 */
abstract class OTP implements OTPInterface
{
    private const DEFAULT_SECRET_SIZE = 64;

    /**
     * Minimum digest size, in bytes, required by the RFC 4226 dynamic truncation.
     *
     * The truncation reads four bytes starting at an offset taken from the low
     * nibble of the last digest byte, i.e. an offset in the range [0, 15]. It
     * therefore reads up to index "offset + 3" = 18 and needs at least 19 bytes.
     * A shorter digest (e.g. MD5, 16 bytes) makes the truncation read past the
     * end of the hash, collapsing the output to a small, secret-independent set
     * of values. Such algorithms are also outside RFC 4226/6238 and are not
     * interoperable with authenticator apps. See {@see self::generateOTP()}.
     */
    private const MINIMUM_DIGEST_SIZE = 19;

    /**
     * @var array<non-empty-string, mixed>
     */
    private array $parameters = [];

    /**
     * @var non-empty-string|null
     */
    private null|string $issuer = null;

    /**
     * @var non-empty-string|null
     */
    private null|string $label = null;

    private bool $issuer_included_as_parameter = true;

    /**
     * @param non-empty-string $secret
     */
    protected function __construct(string $secret)
    {
        $this->setSecret($secret);
    }

    public function getQrCodeUri(string $uri, string $placeholder): string
    {
        $provisioning_uri = urlencode($this->getProvisioningUri());

        return str_replace($placeholder, $provisioning_uri, $uri);
    }

    /**
     * @param 0|positive-int $input
     */
    public function at(int $input): string
    {
        return $this->generateOTP($input);
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    public function getParameters(): array
    {
        $parameters = $this->parameters;

        if ($this->getIssuer() !== null && $this->isIssuerIncludedAsParameter() === true) {
            $parameters['issuer'] = $this->getIssuer();
        }

        return $parameters;
    }

    public function getSecret(): string
    {
        $value = $this->getParameter('secret');
        (is_string($value) && $value !== '') || throw new InvalidParameterException(
            'Invalid "secret" parameter.',
            'secret',
            $value
        );

        return $value;
    }

    public function getLabel(): null|string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->setParameter('label', $label);
    }

    public function withLabel(string $label): static
    {
        $otp = clone $this;
        $otp->setParameter('label', $label);

        return $otp;
    }

    public function getIssuer(): null|string
    {
        return $this->issuer;
    }

    public function setIssuer(string $issuer): void
    {
        $this->setParameter('issuer', $issuer);
    }

    public function withIssuer(string $issuer): static
    {
        $otp = clone $this;
        $otp->setParameter('issuer', $issuer);

        return $otp;
    }

    public function isIssuerIncludedAsParameter(): bool
    {
        return $this->issuer_included_as_parameter;
    }

    public function setIssuerIncludedAsParameter(bool $issuer_included_as_parameter): void
    {
        $this->issuer_included_as_parameter = $issuer_included_as_parameter;
    }

    public function withIssuerIncludedAsParameter(bool $issuer_included_as_parameter): static
    {
        $otp = clone $this;
        $otp->issuer_included_as_parameter = $issuer_included_as_parameter;

        return $otp;
    }

    public function getDigits(): int
    {
        $value = $this->getParameter('digits');
        (is_int($value) && $value >= 1 && $value <= self::MAX_DIGITS) || throw new InvalidParameterException(
            'Invalid "digits" parameter.',
            'digits',
            $value
        );

        return $value;
    }

    public function getDigest(): string
    {
        $value = $this->getParameter('algorithm');
        (is_string($value) && $value !== '') || throw new InvalidParameterException(
            'Invalid "algorithm" parameter.',
            'algorithm',
            $value
        );

        return $value;
    }

    public function hasParameter(string $parameter): bool
    {
        return array_key_exists($parameter, $this->parameters);
    }

    public function getParameter(string $parameter): mixed
    {
        if ($this->hasParameter($parameter)) {
            return $this->getParameters()[$parameter];
        }

        throw new ParameterNotFoundException(sprintf('Parameter "%s" does not exist', $parameter), $parameter);
    }

    public function setParameter(string $parameter, mixed $value): void
    {
        $map = $this->getParameterMap();

        if (array_key_exists($parameter, $map) === true) {
            $callback = $map[$parameter];
            $value = $callback($value);
        }

        if (in_array($parameter, ['label', 'issuer'], true)) {
            $this->{$parameter} = $value;
        } else {
            $this->parameters[$parameter] = $value;
        }
    }

    public function withParameter(string $parameter, mixed $value): static
    {
        $otp = clone $this;
        $otp->setParameter($parameter, $value);

        return $otp;
    }

    public function setSecret(string $secret): void
    {
        $this->setParameter('secret', $secret);
    }

    public function withSecret(string $secret): static
    {
        $otp = clone $this;
        $otp->setParameter('secret', $secret);

        return $otp;
    }

    public function setDigits(int $digits): void
    {
        $this->setParameter('digits', $digits);
    }

    public function withDigits(int $digits): static
    {
        $otp = clone $this;
        $otp->setParameter('digits', $digits);

        return $otp;
    }

    public function setDigest(string $digest): void
    {
        $this->setParameter('algorithm', $digest);
    }

    public function withDigest(string $digest): static
    {
        $otp = clone $this;
        $otp->setParameter('algorithm', $digest);

        return $otp;
    }

    /**
     * @param positive-int|null $secretSize
     *
     * @return non-empty-string
     */
    final protected static function generateSecret(?int $secretSize = null): string
    {
        $secretSize ??= self::DEFAULT_SECRET_SIZE;
        $secretSize > 0 || throw new InvalidParameterException(
            'Secret size must be at least 1.',
            'secretSize',
            $secretSize
        );

        return Base32::encodeUpper(random_bytes($secretSize));
    }

    /**
     * The OTP at the specified input.
     *
     * @param 0|positive-int $input
     *
     * @return non-empty-string
     */
    protected function generateOTP(int $input): string
    {
        $hash = hash_hmac($this->getDigest(), $this->intToByteString($input), $this->getDecodedSecret(), true);
        $unpacked = unpack('C*', $hash);
        $unpacked !== false || throw new InvalidParameterException('Invalid data.', 'hash', $hash);
        /** @var list<int> $hmac */
        $hmac = array_values($unpacked);

        $offset = ($hmac[count($hmac) - 1] & 0xF);
        $code = ($hmac[$offset] & 0x7F) << 24 | ($hmac[$offset + 1] & 0xFF) << 16 | ($hmac[$offset + 2] & 0xFF) << 8 | ($hmac[$offset + 3] & 0xFF);
        $otp = $code % (10 ** $this->getDigits());

        return str_pad((string) $otp, $this->getDigits(), '0', STR_PAD_LEFT);
    }

    /**
     * @param array<non-empty-string, mixed> $options
     */
    protected function filterOptions(array &$options): void
    {
        foreach ([
            'algorithm' => 'sha1',
            'period' => 30,
            'digits' => 6,
        ] as $key => $default) {
            if (isset($options[$key]) && $default === $options[$key]) {
                unset($options[$key]);
            }
        }

        ksort($options);
    }

    /**
     * @param non-empty-string $type
     * @param array<non-empty-string, mixed> $options
     *
     * @return non-empty-string
     */
    protected function generateURI(string $type, array $options): string
    {
        $options = [...$options, ...$this->getParameters()];
        $this->filterOptions($options);
        $params = str_replace(['+', '%7E'], ['%20', '~'], http_build_query($options, '', '&'));

        return sprintf('otpauth://%s/%s?%s', $type, rawurlencode($this->buildProvisioningUriLabel()), $params);
    }

    /**
     * @param non-empty-string $safe
     * @param non-empty-string $user
     */
    protected function compareOTP(string $safe, string $user): bool
    {
        return hash_equals($safe, $user);
    }

    /**
     * @return array<non-empty-string, callable>
     */
    protected function getParameterMap(): array
    {
        return [
            'label' => function (string $value): string {
                $value !== '' || throw new InvalidLabelException('Label must not be empty.', 'label', $value);
                $this->validateLabel($value);

                return $value;
            },
            'secret' => static fn (string $value): string => strtoupper(trim($value, '=')),
            'algorithm' => static function (string $value): string {
                $value = strtolower($value);
                in_array($value, hash_hmac_algos(), true) || throw new InvalidParameterException(
                    sprintf('The "%s" digest is not supported.', $value),
                    'algorithm',
                    $value
                );
                $size = strlen(hash($value, '', true));
                $size >= self::MINIMUM_DIGEST_SIZE || throw new InvalidParameterException(
                    sprintf(
                        'The "%s" digest produces a %d-byte hash which is too short for the RFC 4226 dynamic truncation; at least %d bytes are required.',
                        $value,
                        $size,
                        self::MINIMUM_DIGEST_SIZE
                    ),
                    'algorithm',
                    $value
                );

                return $value;
            },
            'digits' => static function ($value): int {
                $value = (int) $value;
                ($value >= 1 && $value <= self::MAX_DIGITS) || throw new InvalidParameterException(
                    sprintf('Digits must be between 1 and %d.', self::MAX_DIGITS),
                    'digits',
                    $value
                );

                return $value;
            },
            'issuer' => function (string $value): string {
                $value !== '' || throw new InvalidLabelException('Issuer must not be empty.', 'issuer', $value);
                $this->hasColon($value) === false || throw new InvalidLabelException(
                    'Issuer must not contain a colon.',
                    'issuer',
                    $value
                );

                return $value;
            },
        ];
    }

    /**
     * @return non-empty-string
     */
    private function getDecodedSecret(): string
    {
        try {
            $decoded = Base32::decodeUpper($this->getSecret());
        } catch (Exception) {
            throw new SecretDecodingException('Unable to decode the secret. Is it correctly base32 encoded?');
        }
        $decoded !== '' || throw new SecretDecodingException('The decoded secret must not be empty.');

        return $decoded;
    }

    private function intToByteString(int $int): string
    {
        $result = [];
        while ($int !== 0) {
            $result[] = chr($int & 0xFF);
            $int >>= 8;
        }

        return str_pad(implode('', array_reverse($result)), 8, "\000", STR_PAD_LEFT);
    }

    /**
     * @return non-empty-string
     */
    private function buildProvisioningUriLabel(): string
    {
        $issuer = $this->getIssuer();
        $label = $this->getLabel();

        return match (true) {
            $issuer === null && $label === null => throw new InvalidLabelException(
                'The label is not set. Either label or issuer must be set.',
                'label'
            ),
            $label !== null && $this->hasColon($label) => throw new InvalidLabelException(
                'Label must not contain a colon.',
                'label',
                $label
            ),
            $issuer !== null && $label !== null => $issuer . ':' . $label,
            $issuer !== null => $issuer,
            default => $label,
        };
    }

    /**
     * Validates a label according to Google Authenticator spec:
     * label = accountname / issuer (":" / "%3A") *"%20" accountname
     * Neither issuer nor account name may themselves contain a colon.
     *
     * Valid examples:
     * - alice@gmail.com
     * - Provider1:Alice%20Smith
     * - Big%20Corporation%3A%20alice%40bigco.com
     *
     * @param non-empty-string $value
     */
    private function validateLabel(string $value): void
    {
        // Check for colon separators (literal or URL-encoded)
        $hasLiteralColon = str_contains($value, ':');
        $hasEncodedColon = str_contains($value, '%3A') || str_contains($value, '%3a');

        if (! $hasLiteralColon && ! $hasEncodedColon) {
            // Simple label (account name only) - no colons allowed anywhere
            return;
        }

        // Label contains a separator - validate issuer:account format
        // Split by literal or encoded colon
        $parts = match (true) {
            $hasLiteralColon => explode(':', $value, 2),
            default => preg_split('/%3[Aa]/', $value, 2),
        };

        if ($parts === false || count($parts) !== 2) {
            throw new InvalidLabelException('Label must not contain a colon.', 'label', $value);
        }

        [$issuerPart, $accountPart] = $parts;

        // Remove leading %20 (spaces) from account part per spec: *"%20" accountname
        $accountPart = ltrim($accountPart, '%20');

        // Validate that neither part contains additional colons
        if ($this->hasColon($issuerPart) || $this->hasColon($accountPart)) {
            throw new InvalidLabelException(
                'Neither issuer nor account name in label may contain a colon.',
                'label',
                $value
            );
        }
    }

    /**
     * @param non-empty-string $value
     */
    private function hasColon(string $value): bool
    {
        $colons = [':', '%3A', '%3a'];
        foreach ($colons as $colon) {
            if (str_contains($value, $colon)) {
                return true;
            }
        }

        return false;
    }
}
