<?php

declare(strict_types=1);

namespace SimpleSAML\IdP;

use SimpleSAML\Error;
use SimpleSAML\IdP;

/**
 * Interface that all logout handlers must implement.
 *
 * @package SimpleSAMLphp
 */

interface LogoutHandlerInterface
{
    /**
     * Initialize this logout handler.
     *
     * @param \SimpleSAML\IdP $idp The IdP we are logging out from.
     */
    public function __construct(IdP $idp);


    /**
     * Start a logout operation.
     *
     * This function must never return.
     *
     * @param array &$state The logout state.
     * @param string $assocId The association that started the logout.
     * @return void
     */
    public function startLogout(array &$state, $assocId);


    /**
     * Handles responses to our logout requests.
     *
     * This function will never return.
     *
     * @param string $assocId The association that is terminated.
     * @param string|null $relayState The RelayState from the start of the logout.
     * @param \SimpleSAML\Error\Exception|null $error The error that occurred during session termination (if any).
     * @return void
     */
    public function onResponse($assocId, $relayState, Error\Exception $error = null);
}
