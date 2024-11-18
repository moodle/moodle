SimpleSAMLphp SP API reference
==============================

<!-- {{TOC}} -->

This document describes the \SimpleSAML\Auth\Simple API.
This is the preferred API for integrating SimpleSAMLphp with other applications.

### Note on PHP sessions and SimpleSAMLphp API calls

Some SimpleSAMLphp calls replace the current active PHP session. If you previously started a session and wish to write to it, then you must cleanup the SimpleSAMLphp session before you can write to your session. If you do not need to modify your own session, then you can leave the cleanup call out; however, forgetting to call cleanup is a common source of hard to find bugs.

    session_start();
    // ...
    $auth = new \SimpleSAML\Auth\Simple('default-sp');
    $auth->isAuthenticated(); // Replaces our session with the SimpleSAMLphp one
    // $_SESSION['key'] = 'value'; // This would save to the SimpleSAMLphp session which isn't what we want
    SimpleSAML_Session::getSessionFromRequest()->cleanup(); // Reverts to our PHP session
    // Save to our session
    $_SESSION['key'] = 'value';

Constructor
-----------

    new \SimpleSAML\Auth\Simple(string $authSource)

The constructor initializes a \SimpleSAML\Auth\Simple object.

### Parameters

It has a single parameter, which is the ID of the authentication source that should be used.
This authentication source must exist in `config/authsources.php`.

### Example

    $auth = new \SimpleSAML\Auth\Simple('default-sp');


`isAuthenticated`
-----------------

    bool isAuthenticated()

Check whether the user is authenticated with this authentication source.
`TRUE` is returned if the user is authenticated, `FALSE` if not.

### Example

    if (!$auth->isAuthenticated()) {
        SimpleSAML_Session::getSessionFromRequest()->cleanup();
        /* Show login link. */
        print('<a href="/login">Login</a>');
    }

`requireAuth`
-------------

    void requireAuth(array $params = [])

Make sure that the user is authenticated.
This function will only return if the user is authenticated.
If the user isn't authenticated, this function will start the authentication process.

### Parameters

`$params` is an associative array with named parameters for this function.
See the documentation for the `login`-function for a description of the parameters.


### Example 1

    $auth->requireAuth();
    SimpleSAML_Session::getSessionFromRequest()->cleanup();
    print("Hello, authenticated user!");

### Example 2

    /*
     * Return the user to the frontpage after authentication, don't post
     * the current POST data.
     */
    $auth->requireAuth([
        'ReturnTo' => 'https://sp.example.org/',
        'KeepPost' => FALSE,
    ]);
    SimpleSAML_Session::getSessionFromRequest()->cleanup();
    print("Hello, authenticated user!");


`login`
-------------

    void login(array $params = [])

Start a login operation.
This function will always start a new authentication process.

### Parameters

The following global parameters are supported:

`ErrorURL` (`string`)

:   A URL to a page which will receive errors that may occur during authentication.

`KeepPost` (`bool`)

:   If set to `TRUE`, the current POST data will be submitted again after authentication.
    The default is `TRUE`.

`ReturnTo` (`string`)

:   The URL the user should be returned to after authentication.
    The default is to return the user to the current page.

`ReturnCallback` (`array`)

:   The function we should call when the user finishes authentication.

The [`saml:SP`](./saml:sp) authentication source also defines some parameters.


### Example

    # Send a passive authentication request.
    $auth->login([
        'isPassive' => TRUE,
        'ErrorURL' => 'https://.../error_handler.php',
    ]);
    SimpleSAML_Session::getSessionFromRequest()->cleanup();

`logout`
--------

    void logout(mixed $params = NULL)

Log the user out.
After logging out, the user will either be redirected to another page, or a function will be called.
This function never returns.

### Parameters

`$params`
:   Parameters for the logout operation.
    This can either be a simple string, in which case it is interpreted as the URL the user should be redirected to after logout, or an associative array with logout parameters.
    If this parameter isn't specified, we will redirect the user to the current URL after logout.

    If the parameter is an an array, it can have the following options:

    - `ReturnTo`: The URL the user should be returned to after logout.
    - `ReturnCallback`: The function that should be called after logout.
    - `ReturnStateParam`: The parameter we should return the state in when redirecting.
    - `ReturnStateStage`: The stage the state array should be saved with.

    The `ReturnState` parameters allow access to the result of the logout operation after it completes.

### Example 1

Logout, and redirect to the specified URL.

    $auth->logout('https://sp.example.org/logged_out.php');
    SimpleSAML_Session::getSessionFromRequest()->cleanup();

### Example 2

Same as the previous, but check the result of the logout operation afterwards.

    $auth->logout([
        'ReturnTo' => 'https://sp.example.org/logged_out.php',
        'ReturnStateParam' => 'LogoutState',
        'ReturnStateStage' => 'MyLogoutState',
    ]);
    SimpleSAML_Session::getSessionFromRequest()->cleanup();

And in logged_out.php:

    $state = \SimpleSAML\Auth\State::loadState((string)$_REQUEST['LogoutState'], 'MyLogoutState');
    $ls = $state['saml:sp:LogoutStatus']; /* Only works for SAML SP */
    if ($ls['Code'] === 'urn:oasis:names:tc:SAML:2.0:status:Success' && !isset($ls['SubCode'])) {
        /* Successful logout. */
        echo("You have been logged out.");
    } else {
        /* Logout failed. Tell the user to close the browser. */
        echo("We were unable to log you out of all your sessions. To be completely sure that you are logged out, you need to close your web browser.");
    }


`getAttributes`
---------------

    array getAttributes()

Retrieve the attributes of the current user.
If the user isn't authenticated, an empty array will be returned.

The attributes will be returned as an associative array with the name of the attribute as the key and the value as an array of one or more strings:

    [
        'uid' => ['testuser'],
        'eduPersonAffiliation' => ['student', 'member'],
    ]


### Example

    $attrs = $auth->getAttributes();
    if (!isset($attrs['displayName'][0])) {
        throw new Exception('displayName attribute missing.');
    }
    $name = $attrs['displayName'][0];

    print('Hello, ' . htmlspecialchars($name));


`getAuthData`
---------------

    mixed getAuthData(string $name)

Retrieve the specified authentication data for the current session.
NULL is returned if the user isn't authenticated.

The available authentication data depends on the module used for authentication.
See the [`saml:SP`](./saml:sp) reference for information about available SAML authentication data.

### Example

    $idp = $auth->getAuthData('saml:sp:IdP');
    $nameID = $auth->getAuthData('saml:sp:NameID')->getValue();
    printf('You are %s, logged in from %s', htmlspecialchars($nameID), htmlspecialchars($idp));


`getLoginURL`
-------------

    string getLoginURL(string $returnTo = NULL)

Retrieve a URL that can be used to start authentication.

### Parameters

`$returnTo`

:   The URL the user should be returned to after authentication.
    The default is the current page.

### Example

    $url = $auth->getLoginURL();

    print('<a href="' . htmlspecialchars($url) . '">Login</a>');

### Note

The URL returned by this function is static, and will not change.
You can easily create your own links without using this function.
The URL should be:

     .../simplesaml/module.php/core/as_login.php?AuthId=<authentication source>&ReturnTo=<return URL>


`getLogoutURL`
--------------

    string getLogoutURL(string $returnTo = NULL)

Retrieve a URL that can be used to trigger logout.

### Parameters

`$returnTo`

:   The URL the user should be returned to after logout.
    The default is the current page.

### Example

    $url = $auth->getLogoutURL();

    print('<a href="' . htmlspecialchars($url) . '">Logout</a>');

### Note

The URL returned by this function is static, and will not change.
You can easily create your own links without using this function.
The URL should be:

     .../simplesaml/module.php/core/as_logout.php?AuthId=<authentication source>&ReturnTo=<return URL>
