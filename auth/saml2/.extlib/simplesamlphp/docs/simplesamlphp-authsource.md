Creating authentication sources
===============================

All authentication sources are located in the `lib/Auth/Source/` directory in a module, and the class name is `\SimpleSAML\Module\<module>\Auth\Source\<name>`.
The authentication source must extend the `\SimpleSAML\Auth\Source` class or one of its subclasses.

The "entry point" of an authentication source is the `authenticate()`-function.
Once that function is called, the authentication module can do whatever it wishes to do.
There are only two requirements:

- Never show any pages to the user directly from within the `authenticate()`-function.
  (This will lead to problems if the user decides to reload the page.)

- Return control to SimpleSAMLphp after authenticating the user.
  If the module is able to authenticate the user without doing any redirects, it should just update the state-array and return.
  If the module does a redirect, it must call `\SimpleSAML\Auth\Source::completeAuth()` with the updated state array.

Everything else is up to the module.
If the module needs to redirect the user, for example because it needs to show the user a page asking for credentials, it needs to save the state array.
For that we have the `\SimpleSAML\Auth\State` class.
This is only a convenience class, and you are not required to use it (but its use is encouraged, since it handles some potential pitfalls).


Saving state
------------

The `\SimpleSAML\Auth\State` class has two functions that you should use:
`saveState($state, $stage)`, and `loadState($id, $stage)`.
The `$stage` parameter must be an unique identifier for the current position in the authentication.
It is used to prevent a malicious user from taking a state you save in one location, and give it to a different location.

The `saveState()`-function returns an id, which you should pass to the `loadState()`-function later.


Username/password authentication
--------------------------------

Since username/password authentication is quite a common operation, a base class has been created for this.
This is the `\SimpleSAML\Module\core\Auth\UserPassBase` class, which is can be found as `modules/core/lib/Auth/UserPassBase.php`.

The only function you need to implement is the `login($username, $password)`-function.
This function receives the username and password the user entered, and is expected to return the attributes of that user.
If the username or password is incorrect, it should throw an error saying so:

    throw new \SimpleSAML\Error\Error('WRONGUSERPASS');

"[Implementing custom username/password authentication](./simplesamlphp-customauth)" describes how to implement username/password authentication using that base class.


Generic rules & requirements
----------------------------

-  
    Must be derived from the `\SimpleSAML\Auth\Source`-class.

    **Rationale**:
     - Deriving all authentication sources from a single base class allows us extend all authentication sources by extending the base class.

-  
    If a constructor is implemented, it must first call the parent constructor, passing along all parameters, before accessing any of the parameters.
    In general, only the $config parameter should be accessed when implementing the authentication source.

    **Rationale**:
     - PHP doesn't automatically call any parent constructor, so it needs to be done manually.
     - The `$info`-array is used to provide information to the `\SimpleSAML\Auth\Source` base class, and therefore needs to be included.
     - Including the `$config`-array makes it possible to add generic configuration options that are valid for all authentication sources.

-  
    The `authenticate(&$state)`-function must be implemented.
    If this function completes, it is assumed that the user is authenticated, and that the `$state`-array has been updated with the user's attributes.

    **Rationale**:
     - Allowing the `authenticate()`-function to return after updating the `$state`-array enables us to do authentication without redirecting the user.
       This can be used if the authentication doesn't require user input, for example if the authentication can be done based on the IP-address of the user.

-  
    If the `authenticate`-function does not return, it must at a later time call `\SimpleSAML\Auth\Source::completeAuth` with the new state array.
    The state array must be an update of the array passed to the `authenticate`-function.

    **Rationale**:
     - Preserving the same state array allows us to save information in that array before the authentication starts, and restoring it when authentication completes.

-  
    No pages may be shown to the user from the `authenticate()`-function.
    Instead, the state should be saved, and the user should be redirected to a new page.


    **Rationale**:
     - The `authenticate()`-function is called in the context of a different PHP page.
       If the user reloads that page, unpredictable results may occur.

-  
    No state information about any authentication should be stored in the authentication source object.
    It must instead be stored in the state array.
    Any changes to variables in the authentication source object may be lost.

    **Rationale**:
     - This saves us from having to save the entire authentication object between requests.
       Instead, we can recreate it from the configuration.

-  
    The authentication source object must be serializable.
    It may be serialized between being constructed and the call to the `authenticate()`-function.
    This means that, for example, no database connections should be created in the constructor and later used in the `authenticate()`-function.

    **Rationale**:
     - If parsing the configuration and creating the authentication object is shown to be a bottleneck, we can cache an initialized authentication source.
