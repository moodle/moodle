Exception and error handling in SimpleSAMLphp
=============================================

<!--
	This file is written in Markdown syntax.
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->


<!-- {{TOC}} -->

This document describes the way errors and exceptions are handled in authentication sources and authentication processing filters.
The basic goal is to be able to throw an exception during authentication, and then have that exception transported back to the SP in a way that the SP understands.

This means that internal SimpleSAMLphp exceptions must be mapped to transport specific error codes for the various transports that are supported by SimpleSAMLphp.
E.g.: When a `\SimpleSAML\Error\NoPassive` error is thrown by an authentication processing filter in a SAML 2.0 IdP, we want to map that exception to the `urn:oasis:names:tc:SAML:2.0:status:NoPassive` status code.
That status code should then be returned to the SP.


Throwing exceptions
-------------------

How you throw an exception depends on where you want to throw it from.
The simplest case is if you want to throw it during the `authenticate()`-method in an authentication module or during the `process()`-method in a processing filter.
In those methods, you can just throw an exception:

    public function process(&$state) {
        if ($state['something'] === false) {
            throw new \SimpleSAML\Error\Exception('Something is wrong...');
        }
    }

Exceptions thrown at this stage will be caught and delivered to the appropriate error handler.

If you want to throw an exception outside of those methods, i.e. after you have done a redirect, you need to use the `\SimpleSAML\Auth\State::throwException()` function:

    <?php
    $id = $_REQUEST['StateId'];
    $state = \SimpleSAML\Auth\State::loadState($id, 'somestage...');
    \SimpleSAML\Auth\State::throwException($state,
        new \SimpleSAML\Error\Exception('Something is wrong...'));
    ?>

The `\SimpleSAML\Auth\State::throwException` function will then transfer your exception to the appropriate error handler.


### Note

Note that we use the `\SimpleSAML\Error\Exception` class in both cases.
This is because the delivery of the exception may require a redirect to a different web page.
In those cases, the exception needs to be serialized.
The normal `Exception` class in PHP isn't always serializable.

If you throw an exception that isn't a subclass of the `\SimpleSAML\Error\Exception` class, your exception will be converted to an instance of `\SimpleSAML\Error\UnserializableException`.
The `\SimpleSAML\Auth\State::throwException` function does not accept any exceptions that does not subclass the `\SimpleSAML\Error\Exception` class.


Returning specific SAML 2 errors
--------------------------------

By default, all thrown exceptions will be converted to a generic SAML 2 error.
In some cases, you may want to convert the exception to a specific SAML 2 status code.
For example, the `\SimpleSAML\Error\NoPassive` exception should be converted to a SAML 2 status code with the following properties:

* The top-level status code should be `urn:oasis:names:tc:SAML:2.0:status:Responder`.
* The second-level status code should be `urn:oasis:names:tc:SAML:2.0:status:NoPassive`.
* The status message should contain the cause of the exception.

The `\SimpleSAML\Module\saml\Error` class represents SAML 2 errors.
It represents a SAML 2 status code with three elements: the top-level status code, the second-level status code and the status message.
The second-level status code and the status message is optional, and can be `NULL`.

The `\SimpleSAML\Module\saml\Error` class contains a helper function named `fromException`.
The `fromException()` function is used by `www/saml2/idp/SSOService.php` to return SAML 2 errors to the SP.
The function contains a list which maps various exceptions to specific SAML 2 errors.
If it is unable to convert the exception, it will return a generic SAML 2 error describing the original exception in its status message.

To return a specific SAML 2 error, you should:

* Create a new exception class for your error. This exception class must subclass `\SimpleSAML\Error\Exception`.
* Add that exception to the list in `fromException()`.
* Consider adding the exception to `toException()` in the same file. (See the next section.)


### Note

While it is possible to throw SAML 2 errors directly from within authentication sources and processing filters, this practice is discouraged.
Throwing SAML 2 errors will tie your code directly to the SAML 2 protocol, and it may be more difficult to use with other protocols.


Converting SAML 2 errors to normal exceptions
---------------------------------------------

On the SP side, we want to convert SAML 2 errors to SimpleSAMLphp exceptions again.
This is handled by the `toException()` method in `\SimpleSAML\Module\saml\Error`.
The assertion consumer script of the SAML 2 authentication source (`modules/saml2/sp/acs.php`) uses this method.
The result is that generic exceptions are thrown from that authentication source.

For example, `NoPassive` errors will be converted back to instances of `\SimpleSAML\Error\NoPassive`.


Other protocols
---------------

The error handling code has not yet been added to other protocols, but the framework should be easy to adapt for other protocols.
To eventually support other protocols was a goal when designing this framework.


Technical details
-----------------------

This section attempts to describe the internals of the error handling framework.


### `\SimpleSAML\Error\Exception`

The `\SimpleSAML\Error\Exception` class extends the normal PHP `Exception` class.
It makes the exceptions serializable by overriding the `__sleep()` method.
The `__sleep()` method returns all variables in the class which should be serialized when saving the class.

To make sure that the class is serializable, we remove the `$trace` variable from the serialization.
The `$trace` variable contains the full stack trace to the point where the exception was instantiated.
This can be a problem, since the stack trace also contains the parameters to the function calls.
If one of the parameters in unserializable, serialization of the exception will fail.

Since preserving the stack trace can be useful for debugging, we save a variant of the stack trace in the `$backtrace` variable.
This variable can be accessed through the `getBacktrace()` method.
It returns an array with one line of text for each function call in the stack, ending on the point where the exception was created.


#### Note

Since we lose the original `$trace` variable during serialization, PHP will fill it with a new stack trace when the exception is unserialized.
This may be confusing since the new stack trace leads into the `unserialize()` function.
It is therefore recommended to use the getBacktrace() method.


### `\SimpleSAML\Auth\State`

There are two methods in this class that deals with exceptions:

* `throwException($state, $exception)`, which throws an exception.
* `loadExceptionState($id)`, which restores a state containing an exception.


#### `throwException`

This method delivers the exception to the code that initialized the exception handling in the authentication state.
That would be `www/saml2/idp/SSOService.php` for processing filters.
To configure how and where the exception should be delivered, there are two fields in the state-array which can be set:

* `\SimpleSAML\Auth\State::EXCEPTION_HANDLER_FUNC`, in which case the exception will be delivered by a function call to the function specified in that field.
* `\SimpleSAML\Auth\State::EXCEPTION_HANDLER_URL`, in which case the exception will be delivered by a redirect to the URL specified in that field.

If the exception is delivered by a function call, the function will be called with two parameters: The exception and the state array.

If the exception is delivered by a redirect, \SimpleSAML\Auth\State will save the exception in a field in the state array, pass a parameter with the id of the state array to the URL.
The `\SimpleSAML\Auth\State::EXCEPTION_PARAM` constant contains the name of that parameter, while the `\SimpleSAML\Auth\State::EXCEPTION_DATA` constant holds the name of the field where the exception is saved.


#### `loadException`

To retrieve the exception, the application should check for the state parameter in the request, and then retrieve the state array by calling `\SimpleSAML\Auth\State::loadExceptionState()`.
The exception can be located in a field named `\SimpleSAML\Auth\State::EXCEPTION_DATA`.
The following code illustrates this behaviour:

    if (array_key_exists(\SimpleSAML\Auth\State::EXCEPTION_PARAM, $_REQUEST)) {
        $state = \SimpleSAML\Auth\State::loadExceptionState();
        $exception = $state[\SimpleSAML\Auth\State::EXCEPTION_DATA];

        /* Process exception. */
    }


### `\SimpleSAML\Auth\ProcessingChain`

This class requires the caller to add the error handler to the state array before calling the `processState()` function.
Exceptions thrown by the processing filters will be delivered directly to the caller of `processState()` if possible.
However, if one of the filters in the processing chain redirected the user away from the caller, exceptions will be delivered through the error handler saved in the state array.

This is the same behaviour as normal processing filters.
The result will be delivered directly if it is possible, but if not, it will be delivered through a redirect.

The code for handling this becomes something like:

    if (array_key_exists(\SimpleSAML\Auth\State::EXCEPTION_PARAM, $_REQUEST)) {
        $state = \SimpleSAML\Auth\State::loadExceptionState();
        $exception = $state[\SimpleSAML\Auth\State::EXCEPTION_DATA];

        /* Handle exception... */
        [...]
    }

    $procChain = [...];

    $state = [
        'ReturnURL' => \SimpleSAML\Utils\HTTP::getSelfURLNoQuery(),
        \SimpleSAML\Auth\State::EXCEPTION_HANDLER_URL => \SimpleSAML\Utils\HTTP::getSelfURLNoQuery(),
        [...],
    ]

    try {
        $procChain->processState($state);
    } catch (\SimpleSAML\Error\Exception $e) {
        /* Handle exception. */
        [...];
    }


#### Note

An exception which isn't a subclass of `\SimpleSAML\Error\Exception` will be converted to the `\SimpleSAML\Error\UnserializedException` class.
This happens regardless of whether the exception is delivered directly or through the error handler.
This is done to be consistent in what the application receives - now it will always receive the same exception, regardless of whether it is delivered directly or through a redirect.


Custom error show function
--------------------------

Optional custom error show function, called from \SimpleSAML\Error\Error::show, is defined with 'errors.show_function' in config.php.

Example code for this function, which implements the same functionality as \SimpleSAML\Error\Error::show, looks something like:

    public static function show(\SimpleSAML\Configuration $config, array $data) {
        $t = new \SimpleSAML\XHTML\Template($config, 'error.php', 'errors');
        $t->data = array_merge($t->data, $data);
        $t->show();
        exit;
    }

