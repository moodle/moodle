<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml;

use SAML2\Constants;
use Throwable;

/**
 * Class for representing a SAML 2 error.
 *
 * @package SimpleSAMLphp
 */

class Error extends \SimpleSAML\Error\Exception
{
    /**
     * The top-level status code.
     *
     * @var string
     */
    private $status;

    /**
     * The second-level status code, or NULL if no second-level status code is defined.
     *
     * @var string|null
     */
    private $subStatus;

    /**
     * The status message, or NULL if no status message is defined.
     *
     * @var string|null
     */
    private $statusMessage;


    /**
     * Create a SAML 2 error.
     *
     * @param string $status  The top-level status code.
     * @param string|null $subStatus  The second-level status code.
     * Can be NULL, in which case there is no second-level status code.
     * @param string|null $statusMessage  The status message.
     * Can be NULL, in which case there is no status message.
     * @param \Throwable|null $cause  The cause of this exception. Can be NULL.
     */
    public function __construct(
        string $status,
        string $subStatus = null,
        string $statusMessage = null,
        Throwable $cause = null
    ) {
        $st = self::shortStatus($status);
        if ($subStatus !== null) {
            $st .= '/' . self::shortStatus($subStatus);
        }
        if ($statusMessage !== null) {
            $st .= ': ' . $statusMessage;
        }
        parent::__construct($st, 0, $cause);

        $this->status = $status;
        $this->subStatus = $subStatus;
        $this->statusMessage = $statusMessage;
    }


    /**
     * Get the top-level status code.
     *
     * @return string  The top-level status code.
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Get the second-level status code.
     *
     * @return string|null  The second-level status code or NULL if no second-level status code is present.
     */
    public function getSubStatus()
    {
        return $this->subStatus;
    }


    /**
     * Get the status message.
     *
     * @return string|null  The status message or NULL if no status message is present.
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }


    /**
     * Create a SAML2 error from an exception.
     *
     * This function attempts to create a SAML2 error with the appropriate
     * status codes from an arbitrary exception.
     *
     * @param \Throwable $exception  The original exception.
     * @return \SimpleSAML\Error\Exception  The new exception.
     */
    public static function fromException(Throwable $exception): \SimpleSAML\Error\Exception
    {
        if ($exception instanceof \SimpleSAML\Module\saml\Error) {
            // Return the original exception unchanged
            return $exception;

        // TODO: remove this branch in 2.0
        } elseif ($exception instanceof \SimpleSAML\Error\NoPassive) {
            $e = new self(
                Constants::STATUS_RESPONDER,
                Constants::STATUS_NO_PASSIVE,
                $exception->getMessage(),
                $exception
            );
        // TODO: remove this branch in 2.0
        } elseif ($exception instanceof \SimpleSAML\Error\ProxyCountExceeded) {
            $e = new self(
                Constants::STATUS_RESPONDER,
                Constants::STATUS_PROXY_COUNT_EXCEEDED,
                $exception->getMessage(),
                $exception
            );
        } else {
            $e = new self(
                \SAML2\Constants::STATUS_RESPONDER,
                null,
                get_class($exception) . ': ' . $exception->getMessage(),
                $exception
            );
        }

        return $e;
    }


    /**
     * Create a normal exception from a SAML2 error.
     *
     * This function attempts to reverse the operation of the fromException() function.
     * If it is unable to create a more specific exception, it will return the current
     * object.
     *
     * @see \SimpleSAML\Module\saml\Error::fromException()
     *
     * @return \SimpleSAML\Error\Exception  An exception representing this error.
     */
    public function toException()
    {
        $e = null;

        switch ($this->status) {
            case Constants::STATUS_RESPONDER:
                switch ($this->subStatus) {
                    case Constants::STATUS_NO_PASSIVE:
                        $e = new \SimpleSAML\Module\saml\Error\NoPassive(
                            Constants::STATUS_RESPONDER,
                            $this->statusMessage
                        );
                        break;
                }
                break;
        }

        if ($e === null) {
            return $this;
        }

        return $e;
    }


    /**
     * Create a short version of the status code.
     *
     * Remove the 'urn:oasis:names:tc:SAML:2.0:status:'-prefix of status codes
     * if it is present.
     *
     * @param string $status  The status code.
     * @return string  A shorter version of the status code.
     */
    private static function shortStatus(string $status): string
    {
        $t = 'urn:oasis:names:tc:SAML:2.0:status:';
        if (substr($status, 0, strlen($t)) === $t) {
            return substr($status, strlen($t));
        }

        return $status;
    }
}
