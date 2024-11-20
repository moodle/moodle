<?php

declare(strict_types=1);

namespace SimpleSAML\IdP;

use SimpleSAML\Auth;
use SimpleSAML\Error;
use SimpleSAML\IdP;
use SimpleSAML\Logger;
use SimpleSAML\Utils;

/**
 * Class that handles traditional logout.
 *
 * @package SimpleSAMLphp
 */

class TraditionalLogoutHandler implements LogoutHandlerInterface
{
    /**
     * The IdP we are logging out from.
     *
     * @var \SimpleSAML\IdP
     */
    private $idp;


    /**
     * TraditionalLogout constructor.
     *
     * @param \SimpleSAML\IdP $idp The IdP to log out from.
     */
    public function __construct(IdP $idp)
    {
        $this->idp = $idp;
    }


    /**
     * Picks the next SP and issues a logout request.
     *
     * This function never returns.
     *
     * @param array &$state The logout state.
     * @return void
     */
    private function logoutNextSP(array &$state): void
    {
        $association = array_pop($state['core:LogoutTraditional:Remaining']);
        if ($association === null) {
            $this->idp->finishLogout($state);
        }

        $relayState = Auth\State::saveState($state, 'core:LogoutTraditional', true);

        $id = $association['id'];
        Logger::info('Logging out of ' . var_export($id, true) . '.');

        try {
            $idp = IdP::getByState($association);
            $url = call_user_func([$association['Handler'], 'getLogoutURL'], $idp, $association, $relayState);
            Utils\HTTP::redirectTrustedURL($url);
        } catch (\Exception $e) {
            Logger::warning('Unable to initialize logout to ' . var_export($id, true) . '.');
            $this->idp->terminateAssociation($id);
            $state['core:Failed'] = true;

            // Try the next SP
            $this->logoutNextSP($state);
            assert(false);
        }
    }


    /**
     * Start the logout operation.
     *
     * This function never returns.
     *
     * @param array  &$state The logout state.
     * @param string $assocId The association that started the logout.
     * @return void
     */
    public function startLogout(array &$state, $assocId)
    {
        $state['core:LogoutTraditional:Remaining'] = $this->idp->getAssociations();

        $this->logoutNextSP($state);
    }


    /**
     * Continue the logout operation.
     *
     * This function will never return.
     *
     * @param string $assocId The association that is terminated.
     * @param string|null $relayState The RelayState from the start of the logout.
     * @param \SimpleSAML\Error\Exception|null $error The error that occurred during session termination (if any).
     * @return void
     *
     * @throws \SimpleSAML\Error\Exception If the RelayState was lost during logout.
     */
    public function onResponse($assocId, $relayState, Error\Exception $error = null)
    {
        assert(is_string($assocId));
        assert(is_string($relayState) || $relayState === null);

        if ($relayState === null) {
            throw new Error\Exception('RelayState lost during logout.');
        }

        /** @psalm-var array $state */
        $state = Auth\State::loadState($relayState, 'core:LogoutTraditional');

        if ($error === null) {
            Logger::info('Logged out of ' . var_export($assocId, true) . '.');
            $this->idp->terminateAssociation($assocId);
        } else {
            Logger::warning('Error received from ' . var_export($assocId, true) . ' during logout:');
            $error->logWarning();
            $state['core:Failed'] = true;
        }

        $this->logoutNextSP($state);
    }
}
