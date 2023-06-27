<?php

declare(strict_types=1);

namespace SimpleSAML\Auth;

use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Logger;
use SimpleSAML\Session;
use SimpleSAML\Utils;

/**
 * This is a helper class for saving and loading state information.
 *
 * The state must be an associative array. This class will add additional keys to this
 * array. These keys will always start with '\SimpleSAML\Auth\State.'.
 *
 * It is also possible to add a restart URL to the state. If state information is lost, for
 * example because it timed out, or the user loaded a bookmarked page, the loadState function
 * will redirect to this URL. To use this, set $state[\SimpleSAML\Auth\State::RESTART] to this
 * URL.
 *
 * Both the saveState and the loadState function takes in a $stage parameter. This parameter is
 * a security feature, and is used to prevent the user from taking a state saved one place and
 * using it as input a different place.
 *
 * The $stage parameter must be a unique string. To maintain uniqueness, it must be on the form
 * "<classname>.<identifier>" or "<module>:<identifier>".
 *
 * There is also support for passing exceptions through the state.
 * By defining an exception handler when creating the state array, users of the state
 * array can call throwException with the state and the exception. This exception will
 * be passed to the handler defined by the EXCEPTION_HANDLER_URL or EXCEPTION_HANDLER_FUNC
 * elements of the state array. Note that internally this uses the request parameter name
 * defined in EXCEPTION_PARAM, which, for technical reasons, cannot contain a ".".
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */

class State
{
    /**
     * The index in the state array which contains the identifier.
     */
    public const ID = '\SimpleSAML\Auth\State.id';


    /**
     * The index in the cloned state array which contains the identifier of the
     * original state.
     */
    public const CLONE_ORIGINAL_ID = '\SimpleSAML\Auth\State.cloneOriginalId';


    /**
     * The index in the state array which contains the current stage.
     */
    public const STAGE = '\SimpleSAML\Auth\State.stage';


    /**
     * The index in the state array which contains the restart URL.
     */
    public const RESTART = '\SimpleSAML\Auth\State.restartURL';


    /**
     * The index in the state array which contains the exception handler URL.
     */
    public const EXCEPTION_HANDLER_URL = '\SimpleSAML\Auth\State.exceptionURL';


    /**
     * The index in the state array which contains the exception handler function.
     */
    public const EXCEPTION_HANDLER_FUNC = '\SimpleSAML\Auth\State.exceptionFunc';


    /**
     * The index in the state array which contains the exception data.
     */
    public const EXCEPTION_DATA = '\SimpleSAML\Auth\State.exceptionData';


    /**
     * The stage of a state with an exception.
     */
    public const EXCEPTION_STAGE = '\SimpleSAML\Auth\State.exceptionStage';


    /**
     * The URL parameter which contains the exception state id.
     * Note that this does not contain a "." since it's used in the
     * _REQUEST superglobal that does not allow dots.
     */
    public const EXCEPTION_PARAM = '\SimpleSAML\Auth\State_exceptionId';


    /**
     * State timeout.
     */
    private static $stateTimeout = null;


    /**
     * Get the persistent authentication state from the state array.
     *
     * @param array $state The state array to analyze.
     *
     * @return array The persistent authentication state.
     */
    public static function getPersistentAuthData(array $state)
    {
        // save persistent authentication data
        $persistent = [];

        if (array_key_exists('PersistentAuthData', $state)) {
            foreach ($state['PersistentAuthData'] as $key) {
                if (isset($state[$key])) {
                    $persistent[$key] = $state[$key];
                }
            }
        }

        // add those that should always be included
        $mandatory = [
            'Attributes',
            'Expire',
            'LogoutState',
            'AuthInstant',
            'RememberMe',
            'saml:sp:NameID'
        ];
        foreach ($mandatory as $key) {
            if (isset($state[$key])) {
                $persistent[$key] = $state[$key];
            }
        }

        return $persistent;
    }


    /**
     * Retrieve the ID of a state array.
     *
     * Note that this function will not save the state.
     *
     * @param array &$state The state array.
     * @param bool  $rawId Return a raw ID, without a restart URL. Defaults to FALSE.
     *
     * @return string  Identifier which can be used to retrieve the state later.
     */
    public static function getStateId(&$state, $rawId = false)
    {
        assert(is_array($state));
        assert(is_bool($rawId));

        if (!array_key_exists(self::ID, $state)) {
            $state[self::ID] = Utils\Random::generateID();
        }

        $id = $state[self::ID];

        if ($rawId || !array_key_exists(self::RESTART, $state)) {
            // Either raw ID or no restart URL. In any case, return the raw ID.
            return $id;
        }

        // We have a restart URL. Return the ID with that URL.
        return $id . ':' . $state[self::RESTART];
    }


    /**
     * Retrieve state timeout.
     *
     * @return integer  State timeout.
     */
    private static function getStateTimeout(): int
    {
        if (self::$stateTimeout === null) {
            $globalConfig = Configuration::getInstance();
            self::$stateTimeout = $globalConfig->getInteger('session.state.timeout', 60 * 60);
        }

        return self::$stateTimeout;
    }


    /**
     * Save the state.
     *
     * This function saves the state, and returns an id which can be used to
     * retrieve it later. It will also update the $state array with the identifier.
     *
     * @param array  &$state The login request state.
     * @param string $stage The current stage in the login process.
     * @param bool   $rawId Return a raw ID, without a restart URL.
     *
     * @return string  Identifier which can be used to retrieve the state later.
     */
    public static function saveState(&$state, $stage, $rawId = false)
    {
        assert(is_array($state));
        assert(is_string($stage));
        assert(is_bool($rawId));

        $return = self::getStateId($state, $rawId);
        $id = $state[self::ID];

        // Save stage
        $state[self::STAGE] = $stage;

        // Save state
        $serializedState = serialize($state);
        $session = Session::getSessionFromRequest();
        $session->setData('\SimpleSAML\Auth\State', $id, $serializedState, self::getStateTimeout());

        Logger::debug('Saved state: ' . var_export($return, true));

        return $return;
    }


    /**
     * Clone the state.
     *
     * This function clones and returns the new cloned state.
     *
     * @param array $state The original request state.
     *
     * @return array  Cloned state data.
     */
    public static function cloneState(array $state)
    {
        $clonedState = $state;

        if (array_key_exists(self::ID, $state)) {
            $clonedState[self::CLONE_ORIGINAL_ID] = $state[self::ID];
            unset($clonedState[self::ID]);

            Logger::debug('Cloned state: ' . var_export($state[self::ID], true));
        } else {
            Logger::debug('Cloned state with undefined id.');
        }

        return $clonedState;
    }


    /**
     * Retrieve saved state.
     *
     * This function retrieves saved state information. If the state information has been lost,
     * it will attempt to restart the request by calling the restart URL which is embedded in the
     * state information. If there is no restart information available, an exception will be thrown.
     *
     * @param string $id State identifier (with embedded restart information).
     * @param string $stage The stage the state should have been saved in.
     * @param bool   $allowMissing Whether to allow the state to be missing.
     *
     * @throws \SimpleSAML\Error\NoState If we couldn't find the state and there's no URL defined to redirect to.
     * @throws \Exception If the stage of the state is invalid and there's no URL defined to redirect to.
     *
     * @return array|null  State information, or NULL if the state is missing and $allowMissing is true.
     */
    public static function loadState($id, $stage, $allowMissing = false)
    {
        assert(is_string($id));
        assert(is_string($stage));
        assert(is_bool($allowMissing));
        Logger::debug('Loading state: ' . var_export($id, true));

        $sid = self::parseStateID($id);

        $session = Session::getSessionFromRequest();
        $state = $session->getData('\SimpleSAML\Auth\State', $sid['id']);

        if ($state === null) {
            // Could not find saved data
            if ($allowMissing) {
                return null;
            }

            if ($sid['url'] === null) {
                throw new Error\NoState();
            }

            Utils\HTTP::redirectUntrustedURL($sid['url']);
        }

        $state = unserialize($state);
        assert(is_array($state));
        assert(array_key_exists(self::ID, $state));
        assert(array_key_exists(self::STAGE, $state));

        // Verify stage
        if ($state[self::STAGE] !== $stage) {
            /* This could be a user trying to bypass security, but most likely it is just
             * someone using the back-button in the browser. We try to restart the
             * request if that is possible. If not, show an error.
             */

            $msg = 'Wrong stage in state. Was \'' . $state[self::STAGE] .
                '\', should be \'' . $stage . '\'.';

            Logger::warning($msg);

            if ($sid['url'] === null) {
                throw new \Exception($msg);
            }

            Utils\HTTP::redirectUntrustedURL($sid['url']);
        }

        return $state;
    }


    /**
     * Delete state.
     *
     * This function deletes the given state to prevent the user from reusing it later.
     *
     * @param array &$state The state which should be deleted.
     * @return void
     */
    public static function deleteState(&$state)
    {
        assert(is_array($state));

        if (!array_key_exists(self::ID, $state)) {
            // This state hasn't been saved
            return;
        }

        Logger::debug('Deleting state: ' . var_export($state[self::ID], true));

        $session = Session::getSessionFromRequest();
        $session->deleteData('\SimpleSAML\Auth\State', $state[self::ID]);
    }


    /**
     * Throw exception to the state exception handler.
     *
     * @param array                      $state The state array.
     * @param \SimpleSAML\Error\Exception $exception The exception.
     *
     * @throws \SimpleSAML\Error\Exception If there is no exception handler defined, it will just throw the $exception.
     * @return void
     */
    public static function throwException($state, Error\Exception $exception)
    {
        assert(is_array($state));

        if (array_key_exists(self::EXCEPTION_HANDLER_URL, $state)) {
            // Save the exception
            $state[self::EXCEPTION_DATA] = $exception;
            $id = self::saveState($state, self::EXCEPTION_STAGE);

            // Redirect to the exception handler
            Utils\HTTP::redirectTrustedURL(
                $state[self::EXCEPTION_HANDLER_URL],
                [self::EXCEPTION_PARAM => $id]
            );
        } elseif (array_key_exists(self::EXCEPTION_HANDLER_FUNC, $state)) {
            // Call the exception handler
            $func = $state[self::EXCEPTION_HANDLER_FUNC];
            assert(is_callable($func));

            call_user_func($func, $exception, $state);
            assert(false);
        } else {
            /*
             * No exception handler is defined for the current state.
             */
            throw $exception;
        }
        throw new \Exception(); // This should never happen
    }


    /**
     * Retrieve an exception state.
     *
     * @param string|null $id The exception id. Can be NULL, in which case it will be retrieved from the request.
     *
     * @return array|null  The state array with the exception, or NULL if no exception was thrown.
     */
    public static function loadExceptionState($id = null)
    {
        assert(is_string($id) || $id === null);

        if ($id === null) {
            if (!array_key_exists(self::EXCEPTION_PARAM, $_REQUEST)) {
                // No exception
                return null;
            }
            $id = $_REQUEST[self::EXCEPTION_PARAM];
        }

        /** @var array $state */
        $state = self::loadState($id, self::EXCEPTION_STAGE);
        assert(array_key_exists(self::EXCEPTION_DATA, $state));

        return $state;
    }


    /**
     * Get the ID and (optionally) a URL embedded in a StateID, in the form 'id:url'.
     *
     * @param string $stateId The state ID to use.
     *
     * @return array A hashed array with the ID and the URL (if any), in the 'id' and 'url' keys, respectively. If
     * there's no URL in the input parameter, NULL will be returned as the value for the 'url' key.
     *
     * @author Andreas Solberg, UNINETT AS <andreas.solberg@uninett.no>
     * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
     */
    public static function parseStateID($stateId)
    {
        $tmp = explode(':', $stateId, 2);
        $id = $tmp[0];
        $url = null;
        if (count($tmp) === 2) {
            $url = $tmp[1];
        }
        return ['id' => $id, 'url' => $url];
    }
}
