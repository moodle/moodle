<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Auth\Process;

use SimpleSAML\Configuration;
use SimpleSAML\Session;
use SimpleSAML\SessionHandler;

/**
 * Extend IdP session and cookies.
 */
class ExtendIdPSession extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * @param array &$state
     * @return void
     */
    public function process(&$state)
    {
        assert(is_array($state));

        if (empty($state['Expire']) || empty($state['Authority'])) {
            return;
        }

        $now = time();
        $delta = $state['Expire'] - $now;

        $globalConfig = Configuration::getInstance();
        $sessionDuration = $globalConfig->getInteger('session.duration', 28800); // 8*60*60

        // Extend only if half of session duration already passed
        if ($delta >= ($sessionDuration * 0.5)) {
            return;
        }

        // Update authority expire time
        $session = Session::getSessionFromRequest();
        $session->setAuthorityExpire($state['Authority']);

        // Update session cookies duration

        // If remember me is active
        $rememberMeExpire = $session->getRememberMeExpire();
        if (
            !empty($state['RememberMe'])
            && $rememberMeExpire !== null
            && $globalConfig->getBoolean('session.rememberme.enable', false)
        ) {
            $session->setRememberMeExpire();
            return;
        }

        // Or if session lifetime is more than zero
        $sessionHandler = SessionHandler::getSessionHandler();
        $cookieParams = $sessionHandler->getCookieParams();
        if ($cookieParams['lifetime'] > 0) {
            $session->updateSessionCookies();
        }
    }
}
