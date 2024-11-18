<?php

declare(strict_types=1);

namespace SimpleSAML\Auth;

use SimpleSAML\Utils;

error_reporting(E_ALL ^ E_DEPRECATED);

/**
 * A class that generates and verifies time-limited tokens.
 *
 * @deprecated  This class was deprecated in 1.18 and will be removed in a future release
 */

class TimeLimitedToken
{
    /**
     * @var string
     */
    protected $secretSalt;

    /**
     * @var int
     */
    protected $lifetime;

    /**
     * @var int
     */
    protected $skew;

    /**
     * @var string
     */
    protected $algo;


    /**
     * Create a new time-limited token.
     *
     * @param int $lifetime Token lifetime in seconds. Defaults to 900 (15 min).
     * @param string $secretSalt A random and unique salt per installation. Defaults to the salt in the configuration.
     * @param int $skew The allowed time skew (in seconds) to correct clock deviations. Defaults to 1 second.
     * @param string $algo The hash algorithm to use to generate the tokens. Defaults to SHA-256.
     *
     * @throws \InvalidArgumentException if the given parameters are invalid.
     */
    public function __construct($lifetime = 900, $secretSalt = null, $skew = 1, $algo = 'sha256')
    {
        if ($secretSalt === null) {
            $secretSalt = Utils\Config::getSecretSalt();
        }

        if (!in_array($algo, hash_algos(), true)) {
            throw new \InvalidArgumentException('Invalid hash algorithm "' . $algo . '"');
        }

        $this->secretSalt = $secretSalt;
        $this->lifetime = $lifetime;
        $this->skew = $skew;
        $this->algo = $algo;
    }


    /**
     * Add some given data to the current token. This data will be needed later too for token validation.
     *
     * This mechanism can be used to provide context for a token, such as a user identifier of the only subject
     * authorised to use it. Note also that multiple data can be added to the token. This means that upon validation,
     * not only the same data must be added, but also in the same order.
     *
     * @param string $data The data to incorporate into the current token.
     * @return void
     */
    public function addVerificationData($data)
    {
        $this->secretSalt .= '|' . $data;
    }


    /**
     * Calculates a token value for a given offset.
     *
     * @param int $offset The offset to use.
     * @param int|null $time The time stamp to which the offset is relative to. Defaults to the current time.
     *
     * @return string The token for the given time and offset.
     */
    private function calculateTokenValue(int $offset, int $time = null): string
    {
        if ($time === null) {
            $time = time();
        }
        // a secret salt that should be randomly generated for each installation
        return hash(
            $this->algo,
            $offset . ':' . floor(($time - $offset) / ($this->lifetime + $this->skew)) . ':' . $this->secretSalt
        );
    }


    /**
     * Generates a token that contains an offset and a token value, using the current offset.
     *
     * @return string A time-limited token with the offset respect to the beginning of its time slot prepended.
     */
    public function generate()
    {
        $time = time();
        $current_offset = ($time - $this->skew) % ($this->lifetime + $this->skew);
        return dechex($current_offset) . '-' . $this->calculateTokenValue($current_offset, $time);
    }


    /**
     * @see generate
     * @deprecated This method will be removed in SSP 2.0. Use generate() instead.
     * @return string
     */
    public function generate_token()
    {
        return $this->generate();
    }


    /**
     * Validates a token by calculating the token value for the provided offset and comparing it.
     *
     * @param string $token The token to validate.
     *
     * @return bool True if the given token is currently valid, false otherwise.
     */
    public function validate($token)
    {
        $splittoken = explode('-', $token);
        if (count($splittoken) !== 2) {
            return false;
        }
        $offset = intval(hexdec($splittoken[0]));
        $value = $splittoken[1];
        return ($this->calculateTokenValue($offset) === $value);
    }


    /**
     * @see validate
     * @deprecated This method will be removed in SSP 2.0. Use validate() instead.
     * @param string $token
     * @return bool
     */
    public function validate_token($token)
    {
        return $this->validate($token);
    }
}
