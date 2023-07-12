<?php

declare(strict_types=1);

namespace SimpleSAML\Auth;

use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Logger;
use SimpleSAML\Module;
use SimpleSAML\Session;
use SimpleSAML\Utils;

/**
 * This class defines a base class for authentication source.
 *
 * An authentication source is any system which somehow authenticate the user.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */

abstract class Source
{
    /**
     * The authentication source identifier. This identifier can be used to look up this object, for example when
     * returning from a login form.
     *
     * @var string
     */
    protected $authId;


    /**
     * Constructor for an authentication source.
     *
     * Any authentication source which implements its own constructor must call this
     * constructor first.
     *
     * @param array $info Information about this authentication source.
     * @param array &$config Configuration for this authentication source.
     */
    public function __construct($info, &$config)
    {
        assert(is_array($info));
        assert(is_array($config));

        assert(array_key_exists('AuthId', $info));
        $this->authId = $info['AuthId'];
    }


    /**
     * Get sources of a specific type.
     *
     * @param string $type The type of the authentication source.
     *
     * @return Source[]  Array of \SimpleSAML\Auth\Source objects of the specified type.
     * @throws \Exception If the authentication source is invalid.
     */
    public static function getSourcesOfType($type)
    {
        assert(is_string($type));

        $config = Configuration::getConfig('authsources.php');

        $ret = [];

        $sources = $config->getOptions();
        foreach ($sources as $id) {
            $source = $config->getArray($id);

            self::validateSource($source, $id);

            if ($source[0] !== $type) {
                continue;
            }

            $ret[] = self::parseAuthSource($id, $source);
        }

        return $ret;
    }


    /**
     * Retrieve the ID of this authentication source.
     *
     * @return string The ID of this authentication source.
     */
    public function getAuthId()
    {
        return $this->authId;
    }


    /**
     * Process a request.
     *
     * If an authentication source returns from this function, it is assumed to have
     * authenticated the user, and should have set elements in $state with the attributes
     * of the user.
     *
     * If the authentication process requires additional steps which make it impossible to
     * complete before returning from this function, the authentication source should
     * save the state, and at a later stage, load the state, update it with the authentication
     * information about the user, and call completeAuth with the state array.
     *
     * @param array &$state Information about the current authentication.
     * @return void
     */
    abstract public function authenticate(&$state);


    /**
     * Reauthenticate an user.
     *
     * This function is called by the IdP to give the authentication source a chance to
     * interact with the user even in the case when the user is already authenticated.
     *
     * @param array &$state Information about the current authentication.
     * @return void
     */
    public function reauthenticate(array &$state)
    {
        assert(isset($state['ReturnCallback']));

        // the default implementation just copies over the previous authentication data
        $session = Session::getSessionFromRequest();
        $data = $session->getAuthState($this->authId);
        if ($data === null) {
            throw new Error\NoState();
        }

        foreach ($data as $k => $v) {
            $state[$k] = $v;
        }
    }


    /**
     * Complete authentication.
     *
     * This function should be called if authentication has completed. It will never return,
     * except in the case of exceptions. Exceptions thrown from this page should not be caught,
     * but should instead be passed to the top-level exception handler.
     *
     * @param array &$state Information about the current authentication.
     * @return void
     */
    public static function completeAuth(&$state)
    {
        assert(is_array($state));
        assert(array_key_exists('LoginCompletedHandler', $state));

        State::deleteState($state);

        $func = $state['LoginCompletedHandler'];
        assert(is_callable($func));

        call_user_func($func, $state);
        assert(false);
    }


    /**
     * Start authentication.
     *
     * This method never returns.
     *
     * @param string|array $return The URL or function we should direct the user to after authentication. If using a
     * URL obtained from user input, please make sure to check it by calling \SimpleSAML\Utils\HTTP::checkURLAllowed().
     * @param string|null $errorURL The URL we should direct the user to after failed authentication. Can be null, in
     * which case a standard error page will be shown. If using a URL obtained from user input, please make sure to
     * check it by calling \SimpleSAML\Utils\HTTP::checkURLAllowed().
     * @param array $params Extra information about the login. Different authentication requestors may provide different
     * information. Optional, will default to an empty array.
     * @return void
     */
    public function initLogin($return, $errorURL = null, array $params = [])
    {
        assert(is_string($return) || is_array($return));
        assert(is_string($errorURL) || $errorURL === null);

        $state = array_merge($params, [
            '\SimpleSAML\Auth\DefaultAuth.id' => $this->authId, // TODO: remove in 2.0
            '\SimpleSAML\Auth\Source.id' => $this->authId,
            '\SimpleSAML\Auth\DefaultAuth.Return' => $return, // TODO: remove in 2.0
            '\SimpleSAML\Auth\Source.Return' => $return,
            '\SimpleSAML\Auth\DefaultAuth.ErrorURL' => $errorURL, // TODO: remove in 2.0
            '\SimpleSAML\Auth\Source.ErrorURL' => $errorURL,
            'LoginCompletedHandler' => [get_class(), 'loginCompleted'],
            'LogoutCallback' => [get_class(), 'logoutCallback'],
            'LogoutCallbackState' => [
                '\SimpleSAML\Auth\DefaultAuth.logoutSource' => $this->authId, // TODO: remove in 2.0
                '\SimpleSAML\Auth\Source.logoutSource' => $this->authId,
            ],
        ]);

        if (is_string($return)) {
            $state['\SimpleSAML\Auth\DefaultAuth.ReturnURL'] = $return; // TODO: remove in 2.0
            $state['\SimpleSAML\Auth\Source.ReturnURL'] = $return;
        }

        if ($errorURL !== null) {
            $state[State::EXCEPTION_HANDLER_URL] = $errorURL;
        }

        try {
            $this->authenticate($state);
        } catch (Error\Exception $e) {
            State::throwException($state, $e);
        } catch (\Exception $e) {
            $e = new Error\UnserializableException($e);
            State::throwException($state, $e);
        }
        self::loginCompleted($state);
    }


    /**
     * Called when a login operation has finished.
     *
     * This method never returns.
     *
     * @param array $state The state after the login has completed.
     * @return void
     */
    public static function loginCompleted($state)
    {
        assert(is_array($state));
        assert(array_key_exists('\SimpleSAML\Auth\Source.Return', $state));
        assert(array_key_exists('\SimpleSAML\Auth\Source.id', $state));
        assert(array_key_exists('Attributes', $state));
        assert(!array_key_exists('LogoutState', $state) || is_array($state['LogoutState']));

        $return = $state['\SimpleSAML\Auth\Source.Return'];

        // save session state
        $session = Session::getSessionFromRequest();
        $authId = $state['\SimpleSAML\Auth\Source.id'];
        $session->doLogin($authId, State::getPersistentAuthData($state));

        if (is_string($return)) {
            // redirect...
            Utils\HTTP::redirectTrustedURL($return);
        } else {
            call_user_func($return, $state);
        }
        assert(false);
    }


    /**
     * Log out from this authentication source.
     *
     * This function should be overridden if the authentication source requires special
     * steps to complete a logout operation.
     *
     * If the logout process requires a redirect, the state should be saved. Once the
     * logout operation is completed, the state should be restored, and completeLogout
     * should be called with the state. If this operation can be completed without
     * showing the user a page, or redirecting, this function should return.
     *
     * @param array &$state Information about the current logout operation.
     * @return void
     */
    public function logout(&$state)
    {
        assert(is_array($state));
        // default logout handler which doesn't do anything
    }


    /**
     * Complete logout.
     *
     * This function should be called after logout has completed. It will never return,
     * except in the case of exceptions. Exceptions thrown from this page should not be caught,
     * but should instead be passed to the top-level exception handler.
     *
     * @param array &$state Information about the current authentication.
     * @return void
     */
    public static function completeLogout(&$state)
    {
        assert(is_array($state));
        assert(array_key_exists('LogoutCompletedHandler', $state));

        State::deleteState($state);

        $func = $state['LogoutCompletedHandler'];
        assert(is_callable($func));

        call_user_func($func, $state);
        assert(false);
    }


    /**
     * Create authentication source object from configuration array.
     *
     * This function takes an array with the configuration for an authentication source object,
     * and returns the object.
     *
     * @param string $authId The authentication source identifier.
     * @param array  $config The configuration.
     *
     * @return \SimpleSAML\Auth\Source The parsed authentication source.
     * @throws \Exception If the authentication source is invalid.
     */
    private static function parseAuthSource(string $authId, array $config): Source
    {
        self::validateSource($config, $authId);

        $id = $config[0];
        $info = ['AuthId' => $authId];
        $authSource = null;

        unset($config[0]);

        try {
            // Check whether or not there's a factory responsible for instantiating our Auth Source instance
            $factoryClass = Module::resolveClass(
                $id,
                'Auth\Source\Factory',
                '\SimpleSAML\Auth\SourceFactory'
            );

            /** @var SourceFactory $factory */
            $factory = new $factoryClass();
            $authSource = $factory->create($info, $config);
        } catch (\Exception $e) {
            // If not, instantiate the Auth Source here
            $className = Module::resolveClass($id, 'Auth\Source', '\SimpleSAML\Auth\Source');
            $authSource = new $className($info, $config);
        }

        /** @var \SimpleSAML\Auth\Source */
        return $authSource;
    }


    /**
     * Retrieve authentication source.
     *
     * This function takes an id of an authentication source, and returns the
     * AuthSource object. If no authentication source with the given id can be found,
     * NULL will be returned.
     *
     * If the $type parameter is specified, this function will return an
     * authentication source of the given type. If no authentication source or if an
     * authentication source of a different type is found, an exception will be thrown.
     *
     * @param string      $authId The authentication source identifier.
     * @param string|null $type The type of authentication source. If NULL, any type will be accepted.
     *
     * @return \SimpleSAML\Auth\Source|null The AuthSource object, or NULL if no authentication
     *     source with the given identifier is found.
     * @throws \SimpleSAML\Error\Exception If no such authentication source is found or it is invalid.
     */
    public static function getById($authId, $type = null)
    {
        assert(is_string($authId));
        assert($type === null || is_string($type));

        // for now - load and parse config file
        $config = Configuration::getConfig('authsources.php');

        $authConfig = $config->getArray($authId, null);
        if ($authConfig === null) {
            if ($type !== null) {
                throw new Error\Exception(
                    'No authentication source with id ' .
                    var_export($authId, true) . ' found.'
                );
            }
            return null;
        }

        $ret = self::parseAuthSource($authId, $authConfig);

        if ($type === null || $ret instanceof $type) {
            return $ret;
        }

        // the authentication source doesn't have the correct type
        throw new Error\Exception(
            'Invalid type of authentication source ' .
            var_export($authId, true) . '. Was ' . var_export(get_class($ret), true) .
            ', should be ' . var_export($type, true) . '.'
        );
    }


    /**
     * Called when the authentication source receives an external logout request.
     *
     * @param array $state State array for the logout operation.
     * @return void
     */
    public static function logoutCallback($state)
    {
        assert(is_array($state));
        assert(array_key_exists('\SimpleSAML\Auth\Source.logoutSource', $state));

        $source = $state['\SimpleSAML\Auth\Source.logoutSource'];

        $session = Session::getSessionFromRequest();
        if (!$session->isValid($source)) {
            Logger::warning(
                'Received logout from an invalid authentication source ' .
                var_export($source, true)
            );

            return;
        }
        $session->doLogout($source);
    }


    /**
     * Add a logout callback association.
     *
     * This function adds a logout callback association, which allows us to initiate
     * a logout later based on the $assoc-value.
     *
     * Note that logout-associations exists per authentication source. A logout association
     * from one authentication source cannot be called from a different authentication source.
     *
     * @param string $assoc The identifier for this logout association.
     * @param array  $state The state array passed to the authenticate-function.
     * @return void
     */
    protected function addLogoutCallback($assoc, $state)
    {
        assert(is_string($assoc));
        assert(is_array($state));

        if (!array_key_exists('LogoutCallback', $state)) {
            // the authentication requester doesn't have a logout callback
            return;
        }
        $callback = $state['LogoutCallback'];

        if (array_key_exists('LogoutCallbackState', $state)) {
            $callbackState = $state['LogoutCallbackState'];
        } else {
            $callbackState = [];
        }

        $id = strlen($this->authId) . ':' . $this->authId . $assoc;

        $data = [
            'callback' => $callback,
            'state'    => $callbackState,
        ];

        $session = Session::getSessionFromRequest();
        $session->setData(
            '\SimpleSAML\Auth\Source.LogoutCallbacks',
            $id,
            $data,
            Session::DATA_TIMEOUT_SESSION_END
        );
    }


    /**
     * Call a logout callback based on association.
     *
     * This function calls a logout callback based on an association saved with
     * addLogoutCallback(...).
     *
     * This function always returns.
     *
     * @param string $assoc The logout association which should be called.
     * @return void
     */
    protected function callLogoutCallback($assoc)
    {
        assert(is_string($assoc));

        $id = strlen($this->authId) . ':' . $this->authId . $assoc;

        $session = Session::getSessionFromRequest();

        $data = $session->getData('\SimpleSAML\Auth\Source.LogoutCallbacks', $id);
        if ($data === null) {
            // FIXME: fix for IdP-first flow (issue 397) -> reevaluate logout callback infrastructure
            $session->doLogout($this->authId);

            return;
        }

        assert(is_array($data));
        assert(array_key_exists('callback', $data));
        assert(array_key_exists('state', $data));

        $callback = $data['callback'];
        $callbackState = $data['state'];

        $session->deleteData('\SimpleSAML\Auth\Source.LogoutCallbacks', $id);
        call_user_func($callback, $callbackState);
    }


    /**
     * Retrieve list of authentication sources.
     *
     * @return array The id of all authentication sources.
     */
    public static function getSources()
    {
        $config = Configuration::getOptionalConfig('authsources.php');

        return $config->getOptions();
    }


    /**
     * Make sure that the first element of an auth source is its identifier.
     *
     * @param array $source An array with the auth source configuration.
     * @param string $id The auth source identifier.
     *
     * @throws \Exception If the first element of $source is not an identifier for the auth source.
     * @return void
     */
    protected static function validateSource($source, $id)
    {
        if (!array_key_exists(0, $source) || !is_string($source[0])) {
            throw new \Exception(
                'Invalid authentication source \'' . $id .
                '\': First element must be a string which identifies the authentication source.'
            );
        }
    }
}
