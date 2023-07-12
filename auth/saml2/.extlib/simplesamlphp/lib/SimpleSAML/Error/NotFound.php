<?php

declare(strict_types=1);

namespace SimpleSAML\Error;

use SimpleSAML\Utils;

/**
 * Exception which will show a 404 Not Found error page.
 *
 * This exception can be thrown from within a module page handler. The user will then be shown a 404 Not Found error
 * page.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */

class NotFound extends Error
{
    /**
     * Reason why the given page could not be found.
     */
    private $reason;


    /**
     * Create a new NotFound error
     *
     * @param string $reason  Optional description of why the given page could not be found.
     */
    public function __construct($reason = null)
    {
        assert($reason === null || is_string($reason));

        $url = Utils\HTTP::getSelfURL();

        if ($reason === null) {
            parent::__construct(['NOTFOUND', '%URL%' => $url]);
            $this->message = "The requested page '$url' could not be found.";
        } else {
            parent::__construct(['NOTFOUNDREASON', '%URL%' => $url, '%REASON%' => $reason]);
            $this->message = "The requested page '$url' could not be found. " . $reason;
        }

        $this->reason = $reason;
        $this->httpCode = 404;
    }


    /**
     * Retrieve the reason why the given page could not be found.
     *
     * @return string|null  The reason why the page could not be found.
     */
    public function getReason()
    {
        return $this->reason;
    }


    /**
     * NotFound exceptions don't need to display a backtrace, as they are very simple and the trace is usually trivial,
     * so just log the message without any backtrace at all.
     *
     * @param bool $anonymize Whether to anonymize the trace or not.
     *
     * @return array
     */
    public function format($anonymize = false)
    {
        return [
            $this->getClass() . ': ' . $this->getMessage(),
        ];
    }
}
