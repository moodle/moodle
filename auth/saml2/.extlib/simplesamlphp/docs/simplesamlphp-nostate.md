Debugging "State Information Lost" errors
=========================================

**"State Information Lost"** (`\SimpleSAML\Error\NoState: NOSTATE`)

This is one of the most common errors that you can encounter when configuring
SimpleSAMLphp. Unfortunately, it is also a generic error that can have many
possible causes. This document will attempt to describe what this error
actually means, and some of the situations that can cause it.

<!-- {{TOC}} -->

What is "state information"?
----------------------------

The "state information" is data that SimpleSAMLphp stores in association with a
request. The request is typically a SAML 2.0 authentication request sent to
the IdP, but it can also be other requests.

This state information is given a random ID, e.g.
"`_2da56e07840b59191d9797442b6b665d67d855cf77`", and is saved in the session of
the user.

What does it mean that it was lost?
-----------------------------------

This means that we tried to load state information with a specified ID, but
were unable to find it in the session of the user.

What can cause it to be lost?
-----------------------------

There are several ways that this can happen, but most of them have to do
with session storage. Here we will outline some generic alternatives, and
possible solutions.

### The domain name changed during authentication

The domain name the IdP sends the response to is configured in the metadata of
the IdP. This means that it may not match up with the domain name the user
accessed. For example we may have the following scenario:

1. The user accesses `https://www.example.org/`. A session is created for the user, and the session cookie is set for the current domain (www.example.org).
1. The user needs to be authenticated. We therefore save some information about the current status in the state array, create a SAML 2.0 authentication request, and send it to the IdP.
1. The user logs in on the IdP. The IdP then sends a response to the SP at `example.org`. However, the metadata for the SP that is registered at the IdP uses `https://example.org/` (without `www`) as the domain the response should be sent to. The authentication response is therefore sent to that domain.
1. The SP (now at `https://example.org/`) tries to load the state information associated with the authentication response it received. But, because the domain name has changed, we do not receive the session cookie of the user. We are therefore unable to find the session of the user. When we attempt to load the state information from the session we are therefore unable to find it. 

There are several ways to solve this. One of the simplest is often to configure
your webserver to only use one domain, and redirect all accesses to the other
domain to the correct domain.

A different solution is to change the session cookie settings, so that they are
set for the "`example.org`" domain. If you are using PHP sessions, you should
change this in `php.ini`. If not, you should change it with the
'`session.cookie.domain`' option in `config/config.php`. In either case, it should
be set to the top-level domain with a "dot" in front of it. E.g.:

	'session.cookie.domain' => '.example.org',

Or in php.ini:

	session.cookie_domain = ".example.org"

Note that if you use PHP sessions, you will also have to make sure that your
application uses the same domain when it sets the cookie. How that is done
depends on your application. (See the section about mismatch between
application PHP session settings and SimpleSAMLphp session settings.)

### Hopping between http and https

If a cookie is set during a HTTPS session, it is not available when the same
URL is later accessed over http. If your site is available over both http and
https, check that you're using https consistently throughout the configuration.
The best and most secure is to make your complete site available on https only,
and redirect any http requests to https.

### Mismatch between PHP session settings for the application and SimpleSAMLphp

If both the application you are trying to add SAML 2.0 support to and
SimpleSAMLphp uses PHP session for session storage, and they don't agree on all
the parameters, you can end up with this error. By default, SimpleSAMLphp uses
the settings from `php.ini`, but these can be overridden in `config/config.php`.

If this is the cause of your error, you have two choices: either change
SimpleSAMLphp to use a different session storage method (e.g. memcache or sql),
or change the session settings to match between the application and
SimpleSAMLphp. In many cases it is simplest to adjust the session storage.

If you decide to make the session settings match, you should change the
settings in `php.ini`. This is to make sure that the settings apply to everything
that uses the default settings. The following options in `php.ini` must match the
settings used by the application:

* `session.save_handler`: This is the method that is used to store the session. The default is "`files`".
* `session.save_path`: This is the location the session files are saved. The default depends on your PHP installation.
* `session.name`: This is the name of the session cookie. The default is "`PHPSESSID`".
* `session.cookie_path`: The path that the session cookie is limited to. The default is "`/`", which means that it is available to all pages on your domain.
* `session.cookie_domain`: This is the domain the session cookie is limited to. The default is unset, which makes the cookie available only to the current domain. 

What those settings should be set to depends on the application. The simplest
way to determine it may be to look for calls to `session_set_cookie_params` in
the application, and look at what parameters it uses.

### Browsers with SameSite=Lax as default

Some browsers, notably Chrome, will default the cookie SameSite attribute to "Lax" if it
is not set. Specifically in the context of SAML this means that cookies will not be sent
when a POST request is performed between websites, which is typical for the SAML WebSSO
flow. The lack of cookies will cause SimpleSAMLphp's session to be lost when receiving an
assertion via the HTTP-POST binding.

To resolve this, you can set the `session.cookie.samesite` attribute in `config.php`
to `None`. Starting with SimpleSAMLphp 1.19, the config template contains a way to
set this dynamically based on the user's browser support for this attribute.
You also need to enable the `session.cookie.secure` setting.

### A generic problem saving sessions

Sometimes the problem is caused by SimpleSAMLphp being unable to load and/or save
sessions. This can be caused by the session settings being incorrect, or by a
failure of some service required by the session storage. For example, if you
are using memcache for session storage, you need to ensure that the memcache
server is running and that the web server is able to connect to it. The same
applies if you are saving the sessions to a SQL database.

You may want to check your web server error log. If the PHP session handler
fails, it may log an error message there.
