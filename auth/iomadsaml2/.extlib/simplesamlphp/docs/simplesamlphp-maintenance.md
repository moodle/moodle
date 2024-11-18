SimpleSAMLphp Maintenance
=========================

<!--
	This file is written in Markdown syntax.
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->


<!-- {{TOC}} -->

SimpleSAMLphp news and documentation
------------------------------------

Please check the following sources of information to stay up to date with regard to SimpleSAMLphp:

 * [SimpleSAMLphp documentation](http://simplesamlphp.org/docs)
 * [SimpleSAMLphp homepage](https://simplesamlphp.org)
 * [SimpleSAMLphp mailing lists](https://simplesamlphp.org/lists)
 * [SimpleSAMLphp in twitter](https://twitter.com/simplesamlphp)

## Session management

SimpleSAMLphp has an abstraction layer for session management. That means it is possible to choose between different kind of session stores, as well as write new session store plugins.

The `store.type` configuration option in `config.php` allows you to select which method SimpleSAMLphp should use to store the session information. Currently, three session handlers are included in the distribution:

  * `phpsession` uses the built in session management in PHP. This is the default, and is simplest to use. It will not work in a load-balanced environment in most configurations.
  * `memcache` uses the memcache software to cache sessions in memory. Sessions can be distributed and replicated among several memcache servers, enabling both load-balancing and fail-over.
  * `sql` stores the session in an SQL database.
  * `redis` stores the session in Redis.

    'store.type' => 'phpsession',

### Configuring PHP sessions

To use the PHP session handler, set the `store.type` configuration option in `config.php`:

    'store.type' => 'phpsession',

Keep in mind that **PHP does not allow two sessions to be open at the same time**. This means if you are using PHP sessions both in your
application and in SimpleSAMLphp at the same time, **they need to have different names**. When using the PHP session handler in
SimpleSAMLphp, it is configured with different options than for other session handlers:

    'session.phpsession.cookiename' => null,
    'session.phpsession.savepath' => null,
    'session.phpsession.httponly' => true,

Make sure to set `session.phpsession.cookiename` to a name different than the one in use by any other applications. If you are using
SimpleSAMLphp as an Identity Provider, or any other applications using it are not using the default session name, you can use the default
settings by leaving these options unset or setting them to `null`.

If you need to restore your session's application after calling SimpleSAMLphp, you can do it by calling the `cleanup()` method of the
`\SimpleSAML\Session` class, like described [here](simplesamlphp-sp#section_6).

### Configuring memcache

To use the memcache session handler, set the `store.type` parameter in `config.php`:

    'store.type' => 'memcache',

memcache allows you to store multiple redundant copies of sessions on different memcache servers.

The configuration parameter `memcache_store.servers` is an array of server groups. Every data item will be mirrored in every server group.

Each server group is an array of servers. The data items will be load-balanced between all servers in each server group.

Each server is an array of parameters for the server. The following options are available:

`hostname`
:   Host name or ip address where the memcache server runs, or specify other transports like *unix:///path/ssp.sock* to
    use UNIX domain sockets. In that case, port will be ignored and forced to *0*.

    This is the only required option.

`port`
:   Port number of the memcache server. If not set, the `memcache.default_port` ini setting is used. This is 11211 by
    default.

    The port will be forced to *0* when a UNIX domain socket is specified in *hostname*.

`weight`
:   Weight of this server in this server group.
    [http://php.net/manual/en/function.Memcache-addServer.php](http://php.net/manual/en/function.Memcache-addServer.php)
    has more information about the weight option.

`timeout`
:   Timeout for this server. By default, the timeout is 3
    seconds.


Here are two examples of configuration of memcache session handling:

**Example&nbsp;1.&nbsp;Example of redundant configuration with load balancing**

Example of redundant configuration with load balancing: This configuration makes it possible to lose both servers in the a-group or both servers in the b-group without losing any sessions. Note that sessions will be lost if one server is lost from both the a-group and the b-group.

    'memcache_store.servers' => [
      [
        ['hostname' => 'mc_a1'],
        ['hostname' => 'mc_a2'],
      ],
      [
        ['hostname' => 'mc_b1'],
        ['hostname' => 'mc_b2'],
      ],
    ],

**Example&nbsp;2.&nbsp;Example of simple configuration with only one memcache server**

Example of simple configuration with only one memcache server, running on the same computer as the web server: Note that all sessions will be lost if the memcache server crashes.

    'memcache_store.servers' => [
      [
        ['hostname' => 'localhost'],
      ],
    ],

The expiration value (`memcache_store.expires`) is the duration for which data should be retained in memcache. Data are dropped from the memcache servers when this time expires. The time will be reset every time the data is written to the memcache servers.

This value should always be larger than the `session.duration` option. Not doing this may result in the session being deleted from the memcache servers while it is still in use.

Set this value to 0 if you don't want data to expire.

#### Note

The oldest data will always be deleted if the memcache server runs
out of storage space.

**Example&nbsp;3.&nbsp;Example of configuration setting for session expiration**

Here is an example of this configuration parameter:

    'memcache_store.expires' =>  36 * (60*60), // 36 hours.

#### Memcache PHP configuration

Configure memcache to not do internal failover. This parameter is
configured in `php.ini`.

    memcache.allow_failover = Off

#### Environmental configuration

Setup a firewall restricting access to the memcache server.

Because SimpleSAMLphp uses a timestamp to check which session is most recent in a fail-over setup, it is very important to run synchronized clocks on all web servers where you run SimpleSAMLphp.


### Configuring SQL storage

To store session to a SQL database, set the `store.type` option to `sql`.
SimpleSAMLphp uses [PDO](http://www.php.net/manual/en/book.pdo.php) when accessing the database server, so the database source is configured as with a DSN.
The DSN is stored in the `store.sql.dsn` option. See the [PDO driver manual](http://www.php.net/manual/en/pdo.drivers.php) for the DSN syntax used by the different databases.
Username and password for accessing the database can be configured in the `store.sql.username` and `store.sql.password` options.

The required tables are created automatically. If you are storing data from multiple separate SimpleSAMLphp installations in the same database, you can use the `store.sql.prefix` option to prevent conflicts.

### Configuring Redis storage

To store sessions in Redis, set the `store.type` option to `redis`.

By default SimpleSAMLphp will attempt to connect to Redis on the `localhost` at port `6379`. These can be configured via the `store.redis.host` and `store.redis.port` options, respectively. You may also set a key prefix with the `store.redis.prefix` option. For Redis instances that [require authentication](https://redis.io/commands/auth), use the `store.redis.password` option.

## Metadata storage

Several metadata storage backends are available by default, including `flatfile`, `serialize`, `mdq` and
[`pdo`](https://simplesamlphp.org/docs/stable/simplesamlphp-metadata-pdostoragehandler). Here you have an
example configuration of different metadata sources in use at the same time:

```
'metadata.sources' => [
    ['type' => 'flatfile'],
    ['type' => 'flatfile', 'directory' => 'metadata/metarefresh-kalmar'],
    ['type' => 'serialize', 'directory' => 'metadata/metarefresh-ukaccess'],
],
```

You may also implement your own metadata storage handler, in a very similar way to how you would implement
your own session handler. Your class **must** extend the `\SimpleSAML\Metadata\MetaDataStorageSource` class
and override the methods needed to change the backend used. This class **must** also be located in the
`lib/MetadataStore/` directory of your custom module.

Bear in mind that **your class name must follow the PSR-0 autoloading standard**. This means it needs to be
named in a particular way, with the use of namespaces being the preferred convention. For example, if your
module is named _mymodule_ and your class is named _MyMetadataHandler_, you should define it like this: 

```
<?php
namespace SimpleSAML\Module\mymodule\MetadataStore;

class MyMetadataHandler extends \SimpleSAML\Metadata\MetaDataStorageSource
{
    ...
```

If you would like to see an example of how a custom handler could be implemented in your own module, take
a look at the [cassandrastore](https://github.com/feideconnect/simplesamlphp-module-cassandrastore) module.

## Logging and statistics

SimpleSAMLphp supports standard `syslog` logging. As an
alternative, you may log to flat files.

## Apache configuration

Basic Apache configuration is described in [SimpleSAMLphp Installation](simplesamlphp-install#section_6).
However, your IdP or SP is most likely a valuable website that you want to configure securely. Here are some checks.

* Make sure you use HTTPS with a proper certificate. The best way is to not
  serve anything over plain HTTP, except for a possible redirect to https.
* Configure your TLS/SSL to be secure. Mozilla has an easy way to generate
  [Recommended Server Configurations](https://wiki.mozilla.org/Security/Server_Side_TLS#Recommended_Server_Configurations).
  Verify your SSL settings, e.g. with the [SSLLabs SSLtest](https://www.ssllabs.com/ssltest/).
* In your Apache configuration, add headers that further secure your site.
  A good check with hints on what to add is [Mozilla Observatory](https://observatory.mozilla.org/).

## PHP configuration

Secure cookies (if you run HTTPS).

Turn off PHPSESSID in query string.

## Getting ready for production

Here are some checkpoints

 1. Remove all entities in metadata files that you do not trust. It is easy to forget about some of the entities that were used for test.
 2. If you during testing have been using a certificate that has been exposed (notably: the one found in the SimpleSAMLphp distribution): Obtain and install a new one.
 3. Make sure you have installed the latest security upgrades for your OS.
 4. Make sure to use HTTPS rather than HTTP.
 5. Block access to your servers on anything except port 443. SimpleSAMLphp only uses plain HTTP(S), so there is no need to open ports for SOAP or other communication.


## Error handling, error reporting and metadata reporting

SimpleSAMLphp supports allowing the user when encountering errors to send an e-mail to the administrator. You can turn off this feature in the config.php file.


## Multi-language support

To add support for a new language, add your new language to the `language.available` configuration parameter in `config.php`:

	/*
	 * Languages available and which language is default
	 */
	'language.available' => ['en', 'no', 'da', 'es', 'xx'],
	'language.default'   => 'en',

Please use the standardized two-character
[language codes as specified in ISO-639-1](http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes).

You also can set the default language. You should ensure that the default language is complete, as it is used as a fallback when a text is not available in the language selected by the user.

All strings that can be localized are found in the files `dictionaries/`. Add a new entry for each string, with your language code, like this:

    'user_pass_header' => [
        'en' => 'Enter your username and password',
        'no' => 'Skriv inn brukernavn og passord',
        'xx' => 'Pooa jujjique jamba',
      ],

You can translate as many of the texts as you would like; a full translation is not required unless you want to make this the default language. From the end users point of view, it looks best if all text fragments used in a given screen or form is in one single language.

## Customizing the web frontend with themes

Documentation on theming is moved [to a separate document](simplesamlphp-theming).


Support
-------

If you need help to make this work, or want to discuss SimpleSAMLphp with other users of the software, you are fortunate: Around SimpleSAMLphp there is a great Open source community, and you are welcome to join! The forums are open for you to ask questions, contribute answers other further questions, request improvements or contribute with code or plugins of your own.

-  [SimpleSAMLphp homepage](https://simplesamlphp.org)
-  [List of all available SimpleSAMLphp documentation](http://simplesamlphp.org/docs/)
-  [Join the SimpleSAMLphp user's mailing list](https://simplesamlphp.org/lists)
