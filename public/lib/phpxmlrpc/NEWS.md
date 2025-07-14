## XML-RPC for PHP version 4.11.1 - 2025/1/17

* fixed: removed one warning emitted by the Server on php 8.4 and later (issue #125, thanks @ziegenberg)


## XML-RPC for PHP version 4.11.0 - 2024/9/7

* new: added new Client option `Client::OPT_EXTRA_HEADERS`, useful to set custom HTTP headers

* improved: compatibility with not-yet-released PHP version 8.4


## XML-RPC for PHP version 4.10.4 - 2024/06/27

* fixed: Response returned from the library in case of HttpException did not have set the correct `status_code` member var


## XML-RPC for PHP version 4.10.3 - 2024/04/24

* fixed: avoid emitting warnings when parsing some classes of malformed XML (issue #116)

* fixed: the library will now return a Fault `Response` object with error code 2 whenever parsing some xml responses
  which do not conform to the specification, namely those having both `fault` and `params` elements inside `methodResponse`


## XML-RPC for PHP version 4.10.2 - 2024/04/14

* fixed: allow `Server` subclasses to use their own Parser to determine the Request's charset

* fixed: the Server would not swallow and log php warnings generated from end user's method handler functions unless
  debug mode was set to 2 or higher. It now does that always.

* fixed: the library will now return a Fault `Response` object whenever parsing some xml responses which do not conform
  to the specification, namely the for following cases:

  - a `methodResponse` element without either `fault` or `params`
  - a `methodResponse` element with a `params` child which does not have a single `param`

* improved: test on PHP 8.3 as part of CI


## XML-RPC for PHP version 4.10.1 - 2023/02/22

* fixed: class autoloading got broken in rel 4.10.0 for users of the legacy API (issue #111)

* fixed: let the Server create Response objects whose class can be overridden by subclasses (this is required by the
  json-rpc server now that the `xml_header` method has been moved to the `Request` object)

* fixed: let the Client create Requests whose class can be overridden by subclasses, within the `_try_multicall` method,
  which is called from `multicall`

* fixed: declare the library not to be compatible with old versions of 'phpxmlrpc/extras' and 'phpxmlrpc/jsonrpc'


## XML-RPC for PHP version 4.10.0 - 2023/02/11

* changed: the minimum php version required has been increased to 5.4

* changed: dropped support for parsing cookie headers which follow the obsolete Cookie2 specification

* new: it is now possible to make the library generate warning messages whenever a deprecated feature is used, such as
  calling deprecated methods, using deprecated method parameters, or reading/writing deprecated object properties.
  This is disabled by default, and can be enabled by setting `PhpXmlRpc\PhpXmlRpc::xmlrpc_silence_deprecations = false`.
  Note that the deprecation warnings will be by default added to the php error log, and not be displayed on screen.
  If you prefer them to be handled in some other way, you should take over the Logger, as described below here

* new: allow to specify other charsets than the canonical three (UTF-8, ISO-8859-1, ASCII), when mbstring is
  available, both for outgoing and incoming data (issue #42).

  For outgoing data, this can be set in `$client->request_charset_encoding` and `$server->response_charset_encoding`.
  The library will then transcode the data fed to it by the application into the desired charset when serializing
  it for transmission.

  For incoming data, this can be set using `PhpXmlRpc::$internal_encoding`. The library will then transcode the data
  received from 3rd parties into the desired charset when handling it back to the application.

  An example of using this feature has been added to demo file `windowscharset.php`

* new: allow the library to pass to the application DateTime objects instead of string for all _received_ dateTime.iso8601
  xml-rpc values. This includes both client-side, for data within the `$response->value()`, and server-side, for data
  passed to xml-rpc method handlers, and works for both 'xmlrpcvals' and 'phpvals' modes.
  In order to enable this, you should set `PhpXmlRpc\PhpXmlRpc::$xmlrpc_return_datetimes = true`.

  NB: since the xml-rpc spec mandates that no Timezone is used on the wire for dateTime values, the DateTime objects
  created by the library will be set to the default php timezone, set using the 'date.timezone' ini setting.

  NB: if the received strings are not parseable as dates, NULL will be returned instead of an object, but that can
  be avoided by setting `PhpXmlRpc\PhpXmlRpc::$xmlrpc_reject_invalid_values = true`, see below.

* improved: be more strict in the `Response` constructor and in `Request::addParam`: both of those will now generate
  an error message in the log if passed unexpected values

* improved: be more strict in the data accepted as valid for dateTime xml-rpc values. Clearly invalid dates such as a
  month '13', day '32' or hour '25' will cause an error message to be logged or the value to be rejected, depending
  on configuration

* improved: be more strict in the data accepted as valid for 'float' and 'int' xml-rpc values. If you need to allow
  different formats for numbers, you can set a custom value to `PhpXmlRpc\PhpXmlRpc::$xmlrpc_double_format` and
  `PhpXmlRpc\PhpXmlRpc::$xmlrpc_int_format`

* new: allow the library to be stricter in parsing the received xml: by setting
  `PhpXmlRpc\PhpXmlRpc::$xmlrpc_reject_invalid_values = true`, incoming xml which has data not conforming to the expected
  format for value elements of type date, int, float, double, base64 and methodname will be rejected instead of passed
  on to the application. The same will apply for elements of type struct-member which miss either the name or the value

* new: it is now possible to tell the library to allow non-standard formats for received datetime value, such as f.e.
  datetimes with a timezone specifier, by setting a custom value to `PhpXmlRpc\PhpXmlRpc::$xmlrpc_datetime_format`
  (issue #46).

* new: it is now possible to tell the library to allow non-standard formats for received int and float values, as well
  as for methdoname elements. See the api docs for `PhpXmlRpc\PhpXmlRpc` static variables.

* fixed: when a server is configured with its default value of 'xmlrpcvals' for `$functions_parameters_type`, and
  a method handler in the dispatch was defined with `'parameters_type' = 'phpvals'`, the handler would be passed a
  Request object instead of plain php values.

* fixed: made sure all debug output goes through the logger at response parsing time (there was one printf call left)

* fixed: `Client::send` will now return an error Response when it is requested to use an auth method that it does not
  support, instead of logging an error message and continuing with another auth schema. The returned error code is 20

* fixed: when calling `Client::multicall()` with `$client->return_type = 'xml'`, the code would be always falling back to
  non-multicall requests

* fixed: support calling `Client::setSSLVersion()` for the case of not using curl transport

* fixed: receiving integers which use the '<EX:I8>' xml tag

* fixed: setting/retrieving the php value from a Value object using array notation would fail if the object was created
  using `i4` then accessed using `int`, eg: `$v = new Value(1, 'i4'); $v[$v->scalrtyp()] = 2;`

* fixed: setting values to deprecated Response property `cookies` would trigger a PHP notice, ex:
  `$response->_cookies['name'] = ['value' => 'something'];` (introduced in 4.6.0)

* fixed: made deprecated method `Value::structEach` work again with php 8.0 and later

* new: method `PhpXmlRpc::useInteropFaults()` can be used to make the library change the error codes it generates to
  match the spec described at https://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php

* new: both `Request` and `Response` acquired methods `getPayload` and `getContentType`

* new: method `Response::valueType()`

* new: method `Client::getUrl()`

* new: method `Server::setDispatchMap()`

* new: added methods `getOption`, `setOption`, `setOptions` and `getOptions` to both Client and Server, meant to replace
  direct access to _all public properties_ as well as the `$timeout` argument in calls to `Client::send` and `Client::multicall`

* new: by using `Client::setOption('extracurlopts')`, it is possible to pass in protocol=specific options for when
  using the Socket http transport. The value has to be an array with key being 'socket' or 'ssl', and the value an array
  (see https://www.php.net/manual/en/context.socket.php and https://www.php.net/manual/en/context.ssl.php)

* new: it is now possible to inject a custom logger into helper classes `Charset`, `Http`, `XMLParser`, inching a step
  closer to supporting DIC patterns (issue #78)

* new: method `PhpXmlRpc::setLogger()`, to simplify injecting a custom logger into all classes of the library in one step

* improved: the Client will automatically try to use cURL for requests using Digest/NTLM auth, unless told explicitly
  told not to do so via option 'use_curl'

* improved: the Client is more verbose in logging issues when trying to compress a Request for sending

* improved: the `Logger` class now sports methods adhering to Psr\Log\LoggerInterface

* improved: limit the size of incoming data which will be used in error responses and logged error messages, making
  it slightly harder to carry out DOS attacks against the library

* new: passing value -1 to `$client->setDebug` will avoid storing the full http response data in the returned Response
  object when executing `call`. This could be useful in reducing memory usage for big responses

* new: when calling `Wrapper::wrapXmlrpcMethod` and `wrapXmlrpcServer`, it is possible to pass 'throw_on_fault' as option
  to argument `$extraOptions`. This will make the generated function throw on http errors and xml-rpc faults instead of
  returning a Response object

* new: when calling `Wrapper::wrapXmlrpcMethod`, `wrapXmlrpcServer`, `wrapPhpFunction` and `wrapPhpClass` it is possible
  to pass 'encode_nulls' as option to argument `$extraOptions`. This will make the generated code emit a '<nil/>'
  xml-rpc element for php null values, instead of emitting an empty-string xml-rpc element

* new: methods `Wrapper::holdObject()` and `Wrapper::getheldObject()`, allowing flexibility in storing object instances
  for code-generation scenarios involving `Wrapper::wrapPhpClass` and `Wrapper::wrapPhpFunction`

* improved: all `Value` methods now follow snakeCase convention

* improved: all the Exceptions thrown by the library are now `\PhpXmlRpc\Exception` or subclasses thereof

* improved: all the Client's `setSomething()` methods now return the client object, allowing for usage of fluent style
  calling. The same applies to `Request::setDebug`

* improved: when calling `Client::multicall()`, the returned `Response` objects did not have any data in their `httpResponse`

* new: method `Helper\Date::iso8601Encode` now accepts a DateTime input beside a timestamp

* new: in the dispatch map, it is now possible to set different exception handling modes for each exposed xml-rpc method

* new: method `Server::add_to_map` is deprecated in favour of `addToMap`. It has also acquired new parameters:
  `$parametersType = false, $exceptionHandling = false`

* improved: the `XMLParser` accepts more options in its constructor (see phpdocs for details)

* improved: removed usage of `extension_loaded` in favour of `function_exists` when checking for mbstring. This allows
  for mbstring functions to be polyfilled

* improved: the code generated by the various code-generating methods of `Wrapper` are formatted better, and include
  more phpdoc blocks too

* improved: made the `Wrapper` and `Client` classes easy to subclass for use by the PhpJsonRpc library

* improved: added the library version number to the debugger title line

* improved: the debugger will now sport the "load method synopsis" button when interacting with json-rpc servers

* improved: added an example Symfony Client and Server to the demo files (using Symfony 6 / PHP 8 syntax)

* improved: added to the `taskfile` command an option to automatically set up the git hooks for development

* improved: made sure the test container and gha test runners have at least one locale with comma as decimal separator

* BC notes:

  *NB* Given the considerable amount of API changes in this release, a set of tables listing every change has been
  added in doc/api_changes_v4.10.md; a textual description follows.

  Besides what can be inferred from the changes listed above, for library users:

  - the data passed to the application is not encoded anymore in UTF-8 when setting `PhpXmlRpc::$internal_encoding`
    to a custom character set and the mbstring extension is enabled. It will be encoded instead in the specified character
    set. We expect this to affect few users, as setting `PhpXmlRpc::$internal_encoding` to a custom character set did
    not make a lot of sense beforehand
  - the regular expression used to check if incoming int and double values are valid has been tightened. That can be
    tweaked via use of `PhpXmlRpc\PhpXmlRpc::$xmlrpc_double_format` and `PhpXmlRpc\PhpXmlRpc::$xmlrpc_int_format`
  - the regular expression used to check if incoming datetime values are valid has been tightened to reject clearly
    invalid dates. It has been widened as well, to allow leap seconds. That can be tweaked via use of
    `PhpXmlRpc\PhpXmlRpc::$xmlrpc_datetime_format`
  - a regular expression has been introduced to check incoming 'methodname' elements. In the default configuration it
    will trigger error messages in the logs, but not reject the calls. It can be tweaked via use of
    `PhpXmlRpc\PhpXmlRpc::$xmlrpc_methodname_format`
  - an error message will now be generated if, in incoming data, a STRUCT element has no NAME
  - parameters `$timeout` and `$method` are now considered deprecated in `Client::send()` and `Client::multicall()`
  - Client properties `$errno` and `$errstring` are now deprecated
  - direct access to all properties of Client and Server is now deprecated and should be replaced by calls to
    `setOption` / `getOption`. The same applies to the following "setter" methods of the Client: `setSSLVerifyPeer`,
    `setSSLVerifyHost`, `setSSLVersion`, `setRequestCompression`, `setCurlOptions`, `setUseCurl`, `setUserAgent`
  - direct access to `Wrapper::$objHolder` is now deprecated
  - the code generated by the debugger when using "Generate stub for method call" will throw on errors instead of
    returning a Response object

  For library extenders:

  - if you subclassed the `Server` class, and dynamically inject/manipulate the dispatch map, be aware that the server
    will now validate the methodname from the received request as soon as possible during the xml parsing phase, via
    a new method `methodNameCallback`. You might want to reimplement it and f.e. make it a NOOP to avoid such validation
  - new method `Response::xml_header` has replaced `Server::xml_header`. Take care if you had overridden the server
    version - you might need to override `Server::service`
  - the `$options` argument passed to `XMLParser::parse` will now contain both options intended to be passed down to
    the php xml parser, and further options used to tweak the parsing results. If you have subclassed `XMLParser`
    and reimplemented the `parse` methods, or wholesale replaced it, you will have to adapt your code: both for that,
    and for making sure that it sets `$this->current_parsing_options['xmlrpc_null_extension']` from
    `PhpXmlRpc::$xmlrpc_null_extension`
  - also, if you had reimplemented `XMLParser::parse`, be warned that:
    - you should return `$this->_xh` instead of void
    - the callers now treat differently results when `_xh['isf'] > 3`
  - `Client` protected methods `sendPayloadSocket`, `sendPayloadCURL` and `prepareCurlHandle` are now deprecated. They
    have been replaced by `sendViaSocket`, `sendViaCURL` and `createCurlHandle` respectively
  - if you subclassed the `Client` class, take care of new static variables `$requestClass` and `$responseClass`,
    which should be used to instantiate requests and responses
  - if you had been somehow interacting with private method `Client::_try_multicall`, be warned its returned data has
    changed: it now returns a Response for the cases in which it previously returned false, and an array of Response
    objects for the cases in which it previously returned a string
  - if you replaced the `Logger` class, take care that you will have to implement methods `error`, `warning` and `debug`
    (all is ok if you subclassed it)
  - calling method `Value::serializeData` is now deprecated
  - traits have been introduced for all classes dealing with Logger, XMLParser and CharsetEncoder; method `setCharsetEncoder`
    is now static
  - new methods in helper classes: `Charset::knownCharsets`, `Http::parseAcceptHeader`, `XMLParser::truncateValueForLog`
  - protected property `Server::$accepted_charset_encodings` is now deprecated
  - exception `\PhpXmlRpc\Exception\PhpXmlRpcException` is deprecated. Use `\PhpXmlRpc\Exception` instead


## XML-RPC for PHP version 4.9.5 - 2023/01/11

* improved: revised all demo files. Showcase more features in client demos; isolate better testsuite functions in
  server demos and make sure they are not active unless triggered by running the tests; add demos for code-generation
  for both clients and servers

* improved: added cli command `taskfile`, which can be used to download the demo files or the visualeditor component for
  the debugger (requires bash, curl and a smattering of other common unix/linux/macos? tools)

* improved: for php 7 and up, catch php Errors besides Exceptions thrown by method handler functions (ie. server-side)

* fixed: when using the Exception or Error thrown by a method handler function to build the xml-rpc response, override
  fault Code 0, as it breaks response serialization


## XML-RPC for PHP version 4.9.4 - 2023/1/7

* improved: updated the user's manual to be inline with the version4 API and modern coding practices.
  The manual is now bundled in the default distribution tarball, and is easily viewable as html, provided you can
  serve it using a webserver. It is also available as pdf at https://gggeek.github.io/phpxmlrpc/doc-4/phpxmlrpc_manual.pdf

* improved: automated the process of creating the github release when pushing a release-tag to GitHub; also add a tarball
  of the demo files as release asset, and automatically update both http://gggeek.github.io and the code on altervista.org

* improved: added a pre-push git hook script, to avoid pushing tagged versions with inconsistent version tags in code.
  To install it, execute `composer run-script setup-git-hooks` (NB: it is only useful for developers of this library,
  not for the developers simply using it)

* fixed: the value for error 'no_http2' has been switched from 15 to 19 to avoid a collision


## XML-RPC for PHP version 4.9.3 - 2022/12/20

* improved: avoid stalling the webserver when using the debugger with the php cli-webserver and testing the demo
  server within the same install

* improved: allow installation of the jsxmlrpc library within the debugger folder via composer or npm to enable the
  visual-editing capabilities of the debugger, as this works well when the debugger is used as web-root (target usage
  scenario being f.e. using the php cli-webserver to run the debugger)


## XML-RPC for PHP version 4.9.2 - 2022-12-18

* security fix: removed the possibility of an XSS attack in the debugger.
  Since the debugger is not designed to be exposed to end users but only to the developers using this library, and in
  the default configuration it is not exposed to requests from the web, the severity of this issue can be considered low.

* improved: the debugger now uses jsxmlrpc lib version 0.6. It loads it from a cdn rather than locally.
  It also can make use of a 2nd constant to help telling it where the visual-editor form the jsxmlrpc lib is located,
  in case its path on disk relative to the debugger and its url relative to the web root do not match.


## XML-RPC for PHP version 4.9.1 - 2022-12-12

* fixed: php warnings on php 8.2. This includes preferring usage of mbstring for converting between Latin1 and UTF-8

* improved: CI tests now also run on php 8.2


## XML-RPC for PHP version 4.9.0 - 2022/11/28

* security fix: hardened the `Client::send()` method against misuse of the `$method` argument (issue #81).
  Abusing its value, it was possible to force the client to _access local files_ or _connect to undesired urls_ instead
  of the intended target server's url (the one used in the Client constructor).

  This weakness only affects installations where all the following conditions apply, at the same time:

  - the xmlrpc Client is used, ie. not xmlrpc servers
  - untrusted data (eg. data from remote users) is used as value for the `$method` argument of method `Client::send()`,
    in conjunction with conditions which trigger usage of curl as http transport (ie. either using the https, http11 or
    http2 protocols, or calling `Client::setUseCurl()` beforehand)
  - either have set the Clients `return_type` property to 'xml', or make the resulting Response's object `httpResponse`
    member, which is intended to be used for debugging purposes only, available to 3rd parties, eg. by displaying it to
    the end user or serializing it in some storage (note that the same data can also be accessed via magic property
    `Response::raw_data`, and in the Request's `httpResponse` member)

  This is most likely a very uncommon usage scenario, and as such the severity of this issue can be considered low.

  If it is not possible to upgrade to this release of the library at this time, a proactive security measure, to avoid
  the Client accessing any local file on the server which hosts it, is to add the following call to your code:

      $client->setCurlOptions([CURLOPT_PROTOCOLS, CURLPROTO_HTTPS|CURLPROTO_HTTP]);

* security fix: hardened the `Wrapper::buildClientWrapperCode` method's code generation against _code injection_ via
  usage of a malevolent `$client` argument (issue #80).

  In order for this weakness to be exploited, the following conditions have to apply, at the same time:

  - method `Wrapper::buildClientWrapperCode`, or any methods which depend on it, such as `Wrapper::wrapXmlrpcServer`,
    `Wrapper::wrapXmlrpcMethod` or `Wrapper::buildWrapMethodSource` must be in use. Note that they are _not_ used by
    default in either the Client or Server classes provided by the library; the developer has to specifically make use
    of them in his/her own code
  - the `$client` argument to either of those methods should have been built with malicious data, ie. data controlled
    by a 3rd party, passed to its constructor call

  This is most likely an uncommon usage scenario, and as such the severity of this issue can be considered low.

  *NB* the graphical debugger which is shipped as part of the library is vulnerable to this, when used with the option
  "Generate stub for method call" selected. In that case, the debugger will _display_ but not _execute_ the
  malicious code, which would have to be provided via carefully crafted values for the "Address" and "Path" inputs.

  The attack scenario in this case is that a developer copies into his/her own source code the php snippet generated
  by the debugger, in a situation where the debugger is used with "Address"/"Path" input values supplied by a 3rd party.
  The malicious payload in the "Address"/"Path" input values should be easily recognized as suspicious by any barely
  proficient developer, as it resembles a bog-standard injection attack.
  It goes without saying that a responsible developer should not blindly copy and paste into his/her own code anything
  generated by a 3rd party tool, such as the phpxmlrpc debugger, without giving it at least a cursory scan.

* fixed: a php warning on php 8 when parsing responses which do not have a Content-Type header (issue #104)

* fixed: added a missing html-escaping call in demo file `introspect.php`

* fixed: decoding of responses with latin-1 charset declared in the xml prolog but not in http headers, when on php 5.4, 5.5

* fixed: DateTimeInterface is not present in php 5.4 (error introduced in ver. 4.8.1)

* fixed: use of uninitialized var when accessing nonexisting member of legacy class `xmlrpc_server` - thanks SonarQube

* new: the Client class now supports making calls which follow http redirections (issue #77). For that to work, use this code:

      $client->setUseCurl(\PhpXmlRpc\Client::USE_CURL_ALWAYS);
      $client->setCurlOptions([CURLOPT_FOLLOWLOCATION => true, CURLOPT_POSTREDIR => 3]);

* new: allow users of the library to get more fine-grained information about errors in parsing received responses by
  overriding the integer value of `PhpXmlRpc::$xmlrpcerr['invalid_xml']`, `PhpXmlRpc::$xmlrpcerr['xml_not_compliant']`,
  `PhpXmlRpc::$xmlrpcerr['xml_parsing_error']` and the equivalent `PhpXmlRpc::$xmlrpcstr` strings (feature req. #101)

* improved: added the HTTP/2 protocol to the debugger

* improved: CI tests now run on php versions 5.4 and 5.5, besides all more recent ones

* improved: the test container for local testing now defaults to php 7.4 on ubuntu 20 focal


## XML-RPC for PHP version 4.8.1 - 2022/11/10

* improved: remove warnings with php 8.1 due to usage of `strftime` (issue #103)

* improved: cast correctly php objects sporting `DateTimeInterface` to phpxmlrpc datetime values


## XML-RPC for PHP version 4.8.0 - 2022/6/20

* fixed: the `benchmark.php` file had seen some tests accidentally dropped

* improved: added method `Client::prepareCurlHandle`, to make it easier to send multiple requests in parallel when using
  curl and the server does not support `system.multicall`. See new demo file `parallel.php` for how this can be done.

* fixed: error 'Class "PhpXmlRpc\Exception\PhpXmlrpcException" not found' when including `xmlrpc.inc` and on php 8.1
  (might also happen on other php versions) (issue #99)


## XML-RPC for PHP version 4.7.2 - 2022/5/25

* modified the strings used to tell the client to use http/2: to avoid users mistaking 'http2' for the preferred value,
  we switched to using `h2` and `h2c`

* improved: the `benchmark.php` file does now also test calls using https and http/2 protocols


## XML-RPC for PHP version 4.7.1 - 2022/5/25

* fixed: http/2 on non-https requests (known as h2c) works in either "prior-knowledge" mode or "upgrade" mode.
  Given the fact that "upgrade" mode is not compatible with POST requests, we switched to using "prior-knowledge" mode
  for requests sent with the `h2c` argument passed to the client's constructor or `send` method.
  NB: this means that requests sent with `h2c` are only compatible with servers and proxies known to be http/2 compliant.


## XML-RPC for PHP version 4.7.0 - 2022/5/25

* new: HTTP/2 is supported by both the Client and Server components (with the php cURL extension being required to use
  it client-side) (issue #94).
  To force the client to use http/2 over tls or http/2 over tcp requests, pass `h2` or `h2c` as 3rd argument to `Client::send`.


## XML-RPC for PHP version 4.6.1 - 2022/2/15

* fixed: one php warning with php 8 and up (issue #97)


## XML-RPC for PHP version 4.6.0 - 2021/12/9

* fixed: compatibility with php 8.1

* improved: when encoding utf-8 text into us-ascii xml, use character entity references for characters number 0-31
  (ascii non printable characters), as we were already doing when encoding ISO-8859-1 text into us-ascii xml

* new: method `Server::getDispatchMap()`. Useful for non-child classes which want to f.e. introspect the server

* new: increase flexibility in class composition by adopting a Dependency Injection (...ish) pattern:
  it is now possible to swap out the Logger, XMLParser and Charset classes with similar ones of your own making.
  Example code:

      // 1. create an instance of a custom character encoder
      // $myCharsetEncoder = ...
      // 2. then use it while serializing a Request:
      Request::setCharsetEncoder($myCharsetEncoder);
      $request->serialize($funkyCharset);

* new: method `XMLParser::parse()` acquired a 4th argument

* new: method `Wrapper::wrapPhpClass` allows to customize the names of the phpxmlrpc methods by stripping the original
  class name and accompanying namespace and replace it with a user-defined prefix, via option `replace_class_name`

* new: `Response` constructor gained a 4th argument

* deprecated: properties `Response::hdrs`, `Response::_cookies`, `Response::raw_data`. Use `Response::httpResponse()` instead.
  That method returns an array which also holds the http response's status code - useful in case of http errors.

* deprecated: method `Request::createPayload`. Use `Request::serialize` instead

* deprecated: property `Request::httpResponse`

* improved: `Http::parseResponseHeaders` now throws a more specific exception in case of http errors

* improved: Continuous Integration is now running on Github Actions instead of Travis


## XML-RPC for PHP version 4.5.2 - 2021/1/11

* improved: better phpdocs in the php code generated by the Wrapper class

* improved: debugger favicon and page title when used from the phpjsonrpc library

* fixed: allow `Encoder::decode` to properly support different target character sets for polyfill-xmlrpc decode functions

* improved: allow usage of 'epivals' for the 'parameters_type' member of methods definitions in the Server dispatch map


## XML-RPC for PHP version 4.5.1 - 2021/1/3

* improved: made it easier to subclass the Helper\Charset class by allowing `instance` to use late static binding

* fixed: reinstated access to xmlrpc_server->dmap (for users of the v3 API)

* fixed: method `xmlrpc_encode_entitites` (for users of the v3 API)

* improved: split the code of the demo server in multiple files, describing better the purpose of each


## XML-RPC for PHP version 4.5.0 - 2020/12/31

* new: it is now possible to control the precision used when serializing DOUBLE values via usage of
  `PhpXmlRpc::$xmlpc_double_precision`

* fixed: `Encoder::encode` would not correctly encode DateTime and DateTimeImmutable objects

* improvements to the `Helper\Date` class in rejecting invalid date strings

* improvements to the `Wrapper` class in identifying required arguments types from source code phpdoc: support 'array[]',
  'DateTime' and 'DateTimeImmutable'

* improvements to the support of the XMLRPC extension emulation (now provided by the phpxmlrpc/polyfill-xmlrpc package)

* minor improvements to the `Charset` helper: it now loads character set conversion tables on demand, leading to
  slightly lower memory usage and faster execution time when using UTF-8 everywhere.
  NB: take care if you have subclassed it!

* new method: `Server::isSyscall` - mostly of use to Server subclasses and friend classes such as introspectors

* internal method `XMLParser::xmlrpc_ee` now accepts 3 states for its 3rd parameter instead of a bool

* improvements in the inline phpdoc: tagged many methods and class member as reserved for internal usage only

* minor improvements in the debugger to allow easier integration of phpxmlrpc/jsonrpc and friends

* reorganized the test suite to be more manageable

* removed obsolete files from the 'extras' folder; updated and moved to the 'demo' folders the perl and python
  client scripts; moved benchmark.php and verify_compat.php to the 'extras' folder


## XML-RPC for PHP version 4.4.3 - 2020/12/17

* fixed: compatibility with PHP 8.0 (fixes to the debugger, to the server's 'system.methodHelp' method and to the
  PhpXmlRpc\Wrapper class).
  Note that method `Value::structeach` has not been removed from the API, but it is _not_ supported when running
  on PHP 8.0 or later - in that case it will always throw an Error.

* improvements to the test stack: it is now possible to run it via Docker besides Travis; avoid using _any_ external
  server when running tests; run Travis tests also on php 8.0; bump PHPUnit versions in use


## XML-RPC for PHP version 4.4.2 - 2020/3/4

* fixed: `client->setCookie()` bug: cookie values that contain spaces are now properly encoded in a way that gets them
  decoded back to spaces on the receiving end if the server running on php 7.4 (or does RFC-compliant cookie decoding).
  Beforehand we were encoding spaces to '+' characters.


## XML-RPC for PHP version 4.4.1 - 2019/7/29

* fixed: allow handling huge xml messages (>=10MB) (issue #71)

* improved: make it easier to overtake the library's usage of `error_log`


## XML-RPC for PHP version 4.3.2 - 2019/5/27

* fixed: remove one php 7.2 warning when using the v3 api

* improved: the Travis tests are now run with all php versions from 5.6 to 7.3. We dropped tests with php 5.3, 5.4 and 5.5


## XML-RPC for PHP version 4.3.1 - 2018/1/20

* fixed: error when using https in non-curl mode

* fixed: compatibility of tests with php 7.2

* fixed: html injection in sample code

* fixed: warnings emitted by the *legacy* server in xmlrpcs.inc

* fixed: encoding of php variables of type 'resource' when using xmlrpc_encode in php-compatibility mode

* fixed: bad html tag in sample code

* improved: text of error messages


## XML-RPC for PHP version 4.3.0 - 2017/11/6

* fixed: compatibility with Basic/Digest/NTLM auth when using client in cURL mode (issue #58)

* improved: added unit tests for Basic and Digest http auth. Also improved tests suite

* new: allow to force usage of curl for http 1.0 calls, as well as plain socket for https calls, via the method
  `Client::setUseCurl()`


## XML-RPC for PHP version 4.2.2 - 2017/10/15

* fixed: compatibility with Lighttpd target servers when using client in cURL mode and request body size > 1024 bytes
  (issue #56)


## XML-RPC for PHP version 4.2.1 - 2017/9/3

* fixed: compatibility with php 7.2 (issue #55)


## XML-RPC for PHP version 4.2.0 - 2017/6/30

* improved: allow also `DateTimeImmutable` objects to be detected as a date when encoding


## XML-RPC for PHP version 4.1.1 - 2016/10/1

* fixed: error in server class: undefined function php_xmlrpc_encode (only triggered when not using the compatibility
  shim with old versions)


## XML-RPC for PHP version 4.1.0 - 2016/6/26

* improved: Added support for receiving `<I8>` and `<EX:I8>` integers, sending `<I8>`

  If php is compiled in 32 bit mode, and an i8 int is received from a 3rd party, and error will be emitted.
  Integers sent from the library to 3rd parties can be encoded using the i8 tag, but default to using 'int' by default;
  the developer will have to create values as i8 explicitly if needed.
  The library does *not* check if an outgoing integer is too big to fit in 4 bytes and convert it to an i8 automatically.


## XML-RPC for PHP version 4.0.1 - 2016/3/27

* improved: all the API documentation has been moved out of the manual and into the source code phpdoc comments

* fixed: when the internal character set is set to UTF-8 and the client sends requests (or the server responses), too
  many characters were encoded as numeric entities, whereas some, like åäö, needed not to be

* fixed: the 'valtyp' property of Response was not present in all cases; the ValType property had been added by error
  and has been removed


## XML-RPC for PHP version 4.0.0 - 2016/1/20

This release does away with the past and starts a transition to modern-world php.

Code has been heavily refactored, taking care to preserve backwards compatibility as much as possible,
but some breackage is to be expected.

The minimum required php version has been increased to 5.3, even though we strongly urge you to use more recent versions.

PLEASE READ CAREFULLY THE NOTES BELOW to insure a smooth upgrade.

* new: introduction of namespaces and full OOP.

  All php classes have been renamed and moved to separate files.
  Class autoloading can now be done in accord with the PSR-4 standard.
  All global variables and global functions have been removed.
  Iterating over xmlrpc value objects is now easier thank to support for ArrayAccess and Traversable interfaces.

  Backward compatibility is maintained via `lib/xmlrpc.inc`, `lib/xmlrpcs.inc` and `lib/xmlrpc_wrappers.inc`.
  For more details, head on to doc/api_changes_v4.md

* changed: the default character encoding delivered from the library to your code is now utf-8.
  It can be changed at any time setting a value to `PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding`

* improved: the library now accepts requests/responses sent using other character sets than UTF-8/ISO-8859-1/ASCII.
  This only works when the mbstring php extension is enabled.

* improved: no need to call anymore `$client->setSSLVerifyHost(2)` to silence a curl warning when using https
  with recent curl builds

* improved: the xmlrpcval class now supports the interfaces Countable and IteratorAggregate

* improved: a specific option allows users to decide the version of SSL to use for https calls.
  This is useful f.e. for the testing suite, when the server target of calls has no proper ssl certificate,
  and the cURL extension has been compiled with GnuTLS (such as on Travis VMs)

* improved: the function `wrap_php_function()` now can be used to wrap closures (it is now a method btw)

* improved: all wrap_something() functions now return a closure by default instead of a function name

* improved: debug messages are not html-escaped any more when executing from the command line

* improved: the library is now tested using Travis ( https://travis-ci.org/ ).
  Tests are executed using all php versions from 5.3 to 7.2; code-coverage information
  is generated using php 5.6 and uploaded to both Code Coverage and Scrutinizer online services

* improved: phpunit is now installed via Composer, not bundled anymore

* improved: when phpunit is used to generate code-coverage data, the code executed server-side is accounted for

* improved: the test suite has basic checks for the debugger and demo files

* improved: more tests in the test suite

* fixed: the server would not reset the user-set debug messages between subsequent `service()` calls

* fixed: the server would not reset previous php error handlers when an exception was thrown by user code and
  exception_handling set to 2

* fixed: the server would fail to decode a request with ISO-8859-1 payload and character set declaration in the xml
  prolog only

* fixed: the client would fail to decode a response with ISO-8859-1 payload and character set declaration in the xml
  prolog only

* fixed: the function `decode_xml()` would not decode an xml with character set declaration in the xml prolog

* fixed: the client can now successfully call methods using ISO-8859-1 or UTF-8 characters in their name

* fixed: the debugger would fail sending a request with ISO-8859-1 payload (it missed the character set declaration).
  It would have a hard time coping with ISO-8859-1 in other fields, such as e.g. the remote method name

* fixed: the debugger would generate a bad payload via the 'load method synopsis' button for signatures containing NULL
  or undefined parameters

* fixed: the debugger would generate a bad payload via the 'load method synopsis' button for methods with multiple
  signatures

* improved: the debugger is displayed using UTF-8, making it more useful to debug any kind of service

* improved: echo all debug messages even when there are characters in them which php deems to be in a wrong encoding;
  previously those messages would just disappear (this is visible e.g. in the debugger)

* changed: debug info handling

    - at debug level 1, the rebuilt php objects are not dumped to screen (server-side already did that)
    - at debug level 1, curl communication info are not dumped to screen
    - at debug level 1, the tests echo payloads of failures; at debug level 2 all payloads

* improved: makefiles have been replaced with a php_based pakefile

* improved: the source for the manual is stored in AsciiDoc format, which can be displayed natively by GitHub
  with nice html formatting. Also, the HTML version generated by hand and bundled in tarballs is much nicer
  to look at than previous versions

* improved: all PHP code is now formatted according to the PSR-2 standard


## XML-RPC for PHP version 3.0.0 - 2014/6/15

This release corrects all bugs that have been reported and successfully reproduced since
version 3.0.0 beta.

The requirements have increased to php 5.1.0 - which is still way older than what you should be running for any serious
purpose, really.

It also is the first release to be installable via Composer.

See the Changelog file or the pdf docs for a complete list of changes.


## XML-RPC for PHP version 3.0.0 beta - 2009/09/05

This is the first release of the library to only support PHP 5.
Some legacy code has been removed, and support for features such as exceptions and DateTime objects introduced.

The "beta" tag is meant to indicate the fact that the refactoring has been more widespread than in precedent releases
and that more changes are likely to be introduced with time - the library is still considered to be production quality.

* improved: removed all usage of php functions deprecated in php 5.3, usage of assign-by-ref when creating new objects
  etc...
* improved: add support for the <ex:nil/> tag used by the apache library, both in input and output
* improved: add support for DateTime objects in both in php_xmlrpc_encode and as parameter for constructor of xmlrpcval
* improved: add support for timestamps as parameter for constructor of xmlrpcval
* improved: add option 'dates_as_objects' to php_xmlrpc_decode to return dateTime objects for xmlrpc datetimes
* improved: add new method SetCurlOptions to xmrlpc_client to allow extra flexibility in tweaking http config, such as
  explicitly binding to an ip address
* improved: add new method setUserAgent to xmrlpc_client to allow having different user-agent http headers
* improved: add a new member variable in server class to allow fine-tuning of the encoding of returned values when the
  server is in 'phpvals' mode
* improved: allow servers in 'xmlrpcvals' mode to also register plain php functions by defining them in the dispatch map
  with an added option
* improved: catch exceptions thrown during execution of php functions exposed as methods by the server
* fixed: bad encoding if the same object is encoded twice using php_xmlrpc_encode


## XML-RPC for PHP version 2.2.2 - 2009/03/16

This release corrects all bugs that have been reported and successfully reproduced since version 2.2.1.
Regardless of the intimidating message about dropping PHP 4 support, it still does support that ancient, broken and
insecure platform.

* fixed: php warning when receiving 'false' in a bool value
* fixed: improve robustness of the debugger when parsing weird results from non-compliant servers
* fixed: format floating point values using the correct decimal separator even when php locale is set to one that uses
  comma
* fixed: use feof() to test if socket connections are to be closed instead of the number of bytes read (rare bug when
  communicating with some servers)
* fixed: be more tolerant in detection of charset in http headers
* fixed: fix encoding of UTF-8 chars outside the BMP plane
* fixed: fix detection of zlib.output_compression
* improved: allow the add_to_map server method to add docs for single params too
* improved: added the possibility to wrap for exposure as xmlrpc methods plain php class methods, object methods and
  even whole classes


## XML-RPC for PHP version 2.2.1 - 2008/03/06

This release corrects all bugs that have been reported and successfully reproduced.
It is the last release of the library that will support PHP 4.

* fixed: work around bug in php 5.2.2 which broke support of HTTP_RAW_POST_DATA
* fixed: is_dir parameter of setCaCertificate() method is reversed
* fixed: a php warning in xmlrpc_client creator method
* fixed: parsing of '1e+1' as valid float
* fixed: allow errorlevel 3 to work when prev. error handler was a static method
* fixed: usage of client::setcookie() for multiple cookies in non-ssl mode
* improved: support for CP1252 charset is not part or the library but almost possible
* improved: more info when curl is enabled and debug mode is on


## XML-RPC for PHP version 2.2 - 2007/02/25

This release corrects a couple of bugs and adds a few minor features.

* fixed: debugger errors on php installs with magic_quotes_gpc on
* fixed: support for https connections via proxy
* fixed: wrap_xmlrpc_method() generated code failed to properly encode php objects
* improved: slightly faster encoding of data which is internally UTF-8
* improved: debugger always generates a 'null' id for jsonrpc if user omits it
* new: debugger can take advantage of a graphical value builder (it has to be downloaded separately, as part of
  jsxmlrpc package)
* new: support for the <NIL/> xmlrpc extension
* new: server support for the system.getCapabilities xmlrpc extension
* new: wrap_xmlrpc_method() accepts two new options: debug and return_on_fault


## XML-RPC for PHP version 2.1 - 2006/08/28

This release corrects quite a few bugs and adds some interesting new features.
There is a minor security enhancement and overall speedup too.

It has been tested with PHP 4.0.5 up to 4.4.4 and 5.1.5.
Please note that 404pl1 is NOT supported, and has not been since 2.0.

*** PLEASE READ CAREFULLY BELOW ***

CHANGES THAT MIGHT AFFECT DEPLOYED APPLICATIONS:

The wrap_php_function and wrap_xmlrpc_method functions have been moved out of the base library file xmlrpc.inc into a
file of their own: xmlrpc_wrappers.inc.
You will have to include() / require() it in your scripts if you have been using those functions.

For increased security, the automatic rebuilding of php object instances out of received xmlrpc structs in
wrap_xmlrpc_method() has been disabled (but it can be optionally reenabled).

The constructor of xmlrpcval() values has seen major changes, and it will not throw a php warning anymore when invoked
using an unknown xmlrpc type: the error will only be written to php error log. Also, new xmlrpcval('true', 'boolean')
is not supported anymore.

MAJOR IMPROVEMENTS:

The new function php_xmlrpc_decode_xml() will take the xml representation of either an xmlrpc request, response or
single value and return the corresponding php-xmlrpc object instance.

Both wrap_php_function() and wrap_xmlrpc_method() functions accept many more options to fine tune their behaviour,
including one to return the php code to be saved and later used as standalone php script.

A new function wrap_xmlrpc_server() has been added, to wrap all (or some) of the methods exposed by a remote xmlrpc
server into a php class.

Lib internals have been modified to provide better support for grafting extra functionality on top of it. Stay tuned for
future releases of the EXTRAS package.

Last but not least a new file has been added: verify_compat.php, to help users diagnose the level of compliance of the
current php install with the library.

CHANGELOG IN DETAIL:

* fixed bug 1311927: client not playing nice with some proxy/firewall on ports != 80
* fixed bug 1334340: all ereg_ functions have been replaced with corresponding preg_
* fixed bug: wrong handling of 'deflate' http encoding, both server and client side
* fixed bug: sending compressed responses when php output compression is enabled was not working
* fixed bug: addarray() and addstruct() where not returning 1 when adding data to already initialized values
* fixed bug: non-ascii chars used in struct element names where not being encoded correctly
* restored compatibility with php 4.0.5 (for those poor souls still stuck on it)
* server->service() now returns either the payload or xmlrpcresp instance
* server->add_to_map() now accepts methods with no param definitions
* added new function: php_xmlrpc_decode_xml()
* added new function: wrap_xmlrpc_server()
* major improvements and security enhancements to wrap_php_function() and wrap_xmlrpc_method()
* documentation for single parameters of exposed methods can be added to the dispatch map (and turned into html docs in
  conjunction with a future release of the extras package)
* full response payload is saved into xmlrpcresp object for further debugging
* stricter parsing of incoming xmlrpc messages: two more invalid cases are now detected (double data element inside
  array and struct/array after scalar inside value element)
* debugger can now generate code that wraps a remote method into php function (works for jsonrpc, too)
* debugger has better support for being activated via a single GET call (for integration into other tools?)
* more logging of errors in a lot of situations
* javadoc documentation of lib files almost complete
* the usual amount of new testcases in the testsuite
* many performance tweaks and code cleanups
* added foundation for emulating the API of the xmlrpc extension (extras package needed)


## XML-RPC for PHP version 2.0 - 2006/04/24

I'm pleased to announce ## XML-RPC for PHP version 2.0, final.

With respect to the last release candidate, this release corrects a few small bugs and adds a couple of new features:
more authentication options (digest and ntlm for servers, ntlm for proxies, and some https custom certificates stuff);
all the examples have been reviewed and some demo files added, including a ready-made xmlrpc proxy (useful e.g. for
ajax calls, when the xmlrpc client is a browser); the server logs more warning messages for incorrect situations; both
client and server are more tolerant of commonly-found mistakes.
The debugger has been upgraded to reflect the new client capabilities.

In greater detail:

* fixed bug: method xmlrpcval::structmemexists($value) would not work
* fixed bug: wrap_xmlrpc_method would fail if invoked with a client object that has return_type=phpvals
* fixed bug: in case of call to client::multicall without fallback and server error
* fixed bug: recursive serialization of xmlrpcvals loosing specified UTF-8 charset
* fixed bug: serializing to ISO-8859-1 with php 5 would raise an error if non-ascii chars where found when decoding
* new: client can use NTLM and Digest authentication methods for https and http 1.1 connections; authentication to
  proxy can be set to NTLM, too
* new: server tolerates user functions returning a single xmlrpcval object instead of an xmlrpcresp
* new: server does more checks for presence and correct return type of user coded method handling functions, and logs
  inconsistencies to php error log
* new: client method SetCaCertificate($cert, $is_dir) to validate server against
* new: both server and client tolerate receiving 'true' and 'false' for bool values (which btw are not valid according
  to the xmlrpc spec)


## XML-RPC for PHP version 2.0RC3 - 2006/01/22

This release corrects a few bugs and adds some interesting new features.
It has been tested with PHP up to 4.4.2 and 5.1.2.

* fixed bug: server not recognizing clients that declare support for http compression
* fixed bug: serialization of new xmlrpcval (8, 'string') when internal encoding
  set to UTF-8
* fixed bug: serialization of new xmlrpcval ('hello', 'int') would produce
  invalid xml-rpc
* new: let the server accept 'class::method' syntax in the dispatch map
* new: php_xmlrpc_decode() can decode xmlrpcmessage objects
* new: both client and server can specify a charset to be used for serializing
  values instead of the default 'US-ASCII+xml-entities-for-other-characters'.
  Values allowed: ISO-8859-1 and UTF-8
* new: the server object can register 'plain' php functions instead of functions
  that accept a single parameter of type xmlrpcmsg. Faster, uses less memory
  (but comes with minor drawbacks as well, read the manual for more details)
* new: client::setDebug(2) can be used to have the request payload printed to
  screen before being sent
* new: server::service($data) lets user parse data other than POST body, for
  easier testing / subclassing
* changed: framework-generated debug messages are sent back by the server base64
  encoded, to avoid any charset/xml compatibility problem
* other minor fixes

The usual refactoring of a lot of (private) methods has taken place, with new
parameters added to some functions.
Javadoc documentation has been improved a lot.
The HTML documentation has been shuffled around a bit, hoping to give it a more
logical organization.

The experimental support for the JSON protocol has been removed, and will be
packaged as a separate download with some extra very interesting stuff (human
readable auto-generated documentation, anyone?).


## XML-RPC for PHP version 2.0RC2 - 2005/11/22

This release corrects a few bugs and adds basically one new method for better
HTTPS support:

* fixed two bugs that prevented xmlrpc calls to take place over https
* fixed two bugs that prevented proper recognition of xml character set
  when it was declared inside the xml prologue
* added xmlrpc_client::setKey($key, $keypass) method, to allow using client
  side certificates for https connections
* fixed bug that prevented proper serialization of string xmlrpcvals when
  $xmlrpc_internalencoding was set to UTF-8
* fixed bug in xmlrpc_server::echoInput() (and marked method as deprecated)
* correctly set cookies/http headers into xmlrpcresp objects even when the
  send() method call fails for some reason
* added a benchmark file in the testsuite directory

A couple of (private/protected) methods have been refactored, as well as a
couple of extra parameters added to some (private) functions - this has no
impact on the public API and should be of interest primarily to people extending
/ subclassing the lib.

There is also new, PARTIAL support for the JSON-RPC protocol, implemented in
two files in the extras dir (more info about json-rpc at http://json-rpc.org)


## XML-RPC for PHP version 2.0RC1 - 2005/10/03

I'm pleased to announce ## XML-RPC for PHP version 2.0, release candidate 1.

This release introduces so many new features it is almost impossible to list them
here, making the library finally on pair with, if not more advanced than, any other
similar offer (e.g. the PEAR XMLRPC package or the Incutio IXR library).
No, really, trust me.

The minimum supported PHP version is now 4.2 - natively - or 4.0.4pl1 - by usage of
a couple of compatibility classes (code taken from PEAR php_compat package).

The placement of files and directories in the distribution has been deeply modified,
in the hope of making it more clear, now that the file count has increased.
I hope you find it easy.

Support for "advanced" HTTP features such as cookies, proxies and keep-alives has
been added at last.

It is now much easier to convert between xmlrpcval objects and php values, and
in fact php_xmlrpc_encode and php_xmlrpc_decode are now the recommended methods
for all cases, except when encoding base64 data.

Two new (experimental) functions have been added, allowing automagic conversion
of a php function into an xmlrpc method to be exposed and vice-versa.

PHP objects can be now automatically serialized as xmlrpc struct values and
correctly deserialized on the other end of the transmission, provided that the
same class definition is present on both sides and no object members are of
type resource.

A lot of the existing class methods have been overloaded with extra parameters
or new functionality, and a few added ex-novo, making usage easier than ever.

A complete debugger solution is included in the distribution. It needs a web server
to run (a freely available version of the same debugger is accessible online, it
can be found at http://phpxmlrpc.sourceforge.net).

For a more detailed list of changes, please read carefully chapter 2 of the
included documentation, or, even better, take a look at the source code, which
is commented in javadoc style quite a bit.


## XML-RPC for PHP version 1.2.1 - 2005/09

This release restores compatibility with PHP3, which had been broken in release 1.2.
The only other changes are some minor documentation updates and removal of unused
files that had been erroneously packed in 1.2.

## XML-RPC for PHP version 1.2 - 2005/08/14

This removes all use of eval(), which is a potential security problem.
All users are encouraged to upgrade as soon as possible.
As of this release we are no longer php3-compatible.


## XML-RPC for PHP version 1.1.1 - 2005/06/30

This is a security vulnerability fix release.
All users are invited to upgrade as soon as possible.


## XML-RPC for PHP version 1.1 - 2005/05/03

I'm pleased to announce ## XML-RPC for PHP version 1.1
It's taken two years to get to this point, but here we are, finally.

This is a bugfix and maintenance release. No major new features have been added.
All known bugs have been ironed out, unless fixing would have meant breaking
the API.
The code has been tested with PHP 3, 4 and 5, even tough PHP 4 is the main
development platform (and some warnings will be emitted when running PHP5).

Noteworthy changes include:

 * do not clash any more with the EPI xmlrpc extension bundled with PHP 4 and 5
 * fixed the unicode/charset problems that have been plaguing the lib for years
 * proper parsing of int and float values prepended with zeroes or the '+' char
 * accept float values in exponential notation
 * configurable http user-agent string
 * use the same timeout on client socket reads as used for connecting
 * more explicative error messages in xmlrpcresponse in many cases
 * much more tolerant parsing of malformed http responses from xmlrpc servers
 * fixed memleak that prevented the client to be used in never-ending scripts
 * parse bigger xmlrpc messages without crashing (1MB in size or more)
 * be tolerant to xmlrpc responses generated on public servers that add
   javascript advertising at the end of hosted content
 * the lib generates quite a few less PHP warnings during standard operation

This is the last release that will support PHP 3.
The next release will include better support for PHP 5 and (possibly) a slew of
new features.

The changelog is available at:
http://cvs.sourceforge.net/viewcvs.py/phpxmlrpc/xmlrpc/ChangeLog?view=markup

Please report bugs to the XML-RPC PHP mailing list or to the sourceforge project
pages at http://sourceforge.net/projects/phpxmlrpc/


## XML-RPC for PHP version 1.0

I'm pleased to announce ## XML-RPC for PHP version 1.0 (final). It's taken
two years to get to the 1.0 point, but here we are, finally.  The major change
is re-licensing with the BSD open source license, a move from the custom
license previously used.

After this release I expect to move the project to SourceForge and find
another primary maintainer for the code.  More details will follow to the
mailing list.

It can be downloaded from http://xmlrpc.usefulinc.com/php.html

Comprehensive documentation is available in the distribution, but you
can also browse it at http://xmlrpc.usefulinc.com/doc/

Bugfixes in this release include:

 * Small fixes and tidying up.

New features include:

 * experimental support for SSL via the curl extensions to PHP.  Needs
   PHP 4.0.2 or greater, but not PHP 4.0.6 which has broken SSL support.

The changelog is available at: http://xmlrpc.usefulinc.com/ChangeLog.txt

Please report bugs to the XML-RPC PHP mailing list, of which more details are
available at http://xmlrpc.usefulinc.com/list.html, or to
<xmlrpc@usefulinc.com>.


## XML-RPC for PHP version 1.0 beta 9

I'm pleased to announce ## XML-RPC for PHP version 1.0 beta 9. This is
largely a bugfix release.

It can be downloaded from http://xmlrpc.usefulinc.com/php.html

Comprehensive documentation is available in the distribution, but you
can also browse it at http://xmlrpc.usefulinc.com/doc/

Bugfixes in this release include:

 * Fixed string handling bug where characters between a </string>
   and </value> tag were not ignored.

 * Added in support for PHP's native boolean type.

New features include:

 * new getval() method (experimental only) which has support for
   recreating nested arrays.
 * fledgling unit test suite
 * server.php has support for basic interop test suite

The changelog is available at: http://xmlrpc.usefulinc.com/ChangeLog.txt

Please test this as hard as possible and report bugs to the XML-RPC PHP
mailing list, of which more details are available at
http://xmlrpc.usefulinc.com/list.html, or to <xmlrpc@usefulinc.com>.


## XML-RPC for PHP version 1.0 beta 8

I'm pleased to announce ## XML-RPC for PHP version 1.0 beta 8.

This release fixes several bugs and adds a couple of new helper
functions. The most critical change in this release is that you can no
longer print debug info in comments inside a server method -- you must
now use the new xmlrpc_debugmsg() function.

It can be downloaded from http://xmlrpc.usefulinc.com/php.html

Comprehensive documentation is available in the distribution, but you
can also browse it at http://xmlrpc.usefulinc.com/doc/

Bugfixes in this release include:

 * fixed whitespace handling in values
 * correct sending of Content-length from the server

New features include:

 * xmlrpc_debugmsg() method allows sending of debug info in comments in
   the return payload from a server

 * xmlrpc_encode() and xmlrpc_decode() translate between xmlrpcval
   objects and PHP language arrays. They aren't suitable for all
   datatypes, but can speed up coding in simple scenarios. Thanks to Dan
   Libby for these.

The changelog is available at: http://xmlrpc.usefulinc.com/ChangeLog.txt

Please test this as hard as possible and report bugs to the XML-RPC PHP
mailing list, of which more details are available at
http://xmlrpc.usefulinc.com/list.html, or to <xmlrpc@usefulinc.com>.


## XML-RPC for PHP version 1.0 beta 7

I'm pleased to announce ## XML-RPC for PHP version 1.0 beta 7. This is
fixes some critical bugs that crept in. If it shows itself to be stable
then it'll become the 1.0 release.

It can be downloaded from http://xmlrpc.usefulinc.com/php.html

Comprehensive documentation is available in the distribution, but you
can also browse it at http://xmlrpc.usefulinc.com/doc/

Bugfixes in this release include:

 * Passing of booleans should now work as expected
 * Dollar signs and backslashes in strings should pass OK
 * addScalar() now works properly to append to array vals

New features include:

 * Added support for HTTP Basic authorization through the
   xmlrpc_client::setCredentials method.

 * Added test script and method for verifying correct passing of
   booleans

The changelog is available at: http://xmlrpc.usefulinc.com/ChangeLog.txt

Please test this as hard as possible and report bugs to the XML-RPC PHP
mailing list, of which more details are available at
http://xmlrpc.usefulinc.com/list.html, or to <xmlrpc@usefulinc.com>.


## XML-RPC for PHP version 1.0 beta 6

I'm pleased to announce ## XML-RPC for PHP version 1.0 beta 6. This is the
final beta before the 1.0 release.

It can be downloaded from http://xmlrpc.usefulinc.com/php.html

Comprehensive documentation is available in the distribution, but you
can also browse it at http://xmlrpc.usefulinc.com/doc/

New features in this release include:

 * Perl and Python test programs for the demo server
 * Proper fault generation on a non-"200 OK" response from a remote host
 * Bugfixed base64 decoding
 * ISO8601 helper routines for translation to and from UNIX timestamps
 * reorganization of code to allow eventual integration of alternative
   transports

The changelog is available at: http://xmlrpc.usefulinc.com/ChangeLog.txt

Please test this as hard as possible and report bugs to the XML-RPC PHP
mailing list, of which more details are available at
http://xmlrpc.usefulinc.com/list.html, or to <xmlrpc@usefulinc.com>.
