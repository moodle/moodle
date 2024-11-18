![GitHub Workflow Status (branch)](https://img.shields.io/github/actions/workflow/status/catalyst/moodle-auth_iomadsaml2/ci.yml?branch=MOODLE_39_STABLE&label=ci)

https://moodle.org/plugins/auth_iomadsaml2

# 100% Moodle SAML fast, simple, secure

![Churchill quote](/pix/churchill.jpg?raw=true)

* [What is this?](#what-is-this)
* [Why is it better?](#why-is-it-better)
* [How does it work?](#how-does-it-work)
* [Features](#features)
* [Branches](#branches)
* [Installation](#installation)
* [Configuration](#configuration)
* [Testing](#testing)
* [Debugging](#debugging)
* [Gotchas](#gotchas)
* [Other SAML plugins](#other-saml-plugins)
* [Support](#support)
* [Warm thanks](#warm-thanks)


## What is this?

This plugin does SAML authentication and user auto creation with field mapping.


## Why is it better?

* 100% configured in the Moodle GUI - no installation of a whole separate app,
  and no touching of config files or generating certificates.
* Minimal configuration needed, in most cases just copy the IdP metadata in
  and then give the SP metadata to your IdP admin and that's it.
* Fast! - 3 redirects instead of 7
* Supports Single Logout via the HTTP-Redirect binding which many organisations require


## How does it work?

It completely embeds a SimpleSamlPHP instance as an internal dependency, which
is dynamically configured the way it should be by inheriting almost all of its
configuration from Moodle configuration. In the future we should be able to
swap to a different internal SAML implementation and the plugin GUI shouldn't
need to change at all.


## Features

* Dual login vs. forced login for all as an option, with `?saml=off` on the login
  page for manual accounts, and `?saml=on` supported everywhere to deep link and
  force login via SAML if dual auth is on.
* SAML attributes to Moodle user field mapping
* Automatic certificate creation
* Optionally auto create users
* Support for multiple identity providers
* IdP initiated flow / IdP first flow / IdP unsolicited logins, eg:
  http://idp.local/simplesaml/iomadsaml2/idp/SSOService.php?spentityid=http://moodle.local/auth/iomadsaml2/sp/metadata.php&RelayState=http://moodle.local/course/view.php?id=2


Features not yet implemented:

* Enrolment - this should be an enrol plugin and not in an auth plugin
* Role mapping - not yet implemented


## Branches

| Moodle version    | Branch             | PHP  | SimpleSAMLphp |
| ----------------- | ------------------ | ---- | ------------- |
| Moodle 3.9+       | `MOODLE_39_STABLE` | 7.2+ | v1.19.5       |
| Totara 12+        | `MOODLE_39_STABLE` | 7.2+ | v1.19.1       |
| Moodle 3.5 to 3.8 | `MOODLE_35_STABLE` | 7.2+ | v1.18.8       |
| Moodle 2.7 to 3.4 | `27_34STABLE`      | 5.5+ | v1.15.4       |
| Totara up to 11   | `27_34STABLE`      | 5.5+ | v1.15.4       |

## Installation

1. Install the plugin the same as any standard Moodle plugin, either via the
[Moodle plugin directory](https://moodle.org/plugins/auth_iomadsaml2), or you can use
git to clone it into your source:

   ```sh
   git clone git@github.com:catalyst/moodle-auth_iomadsaml2.git auth/iomadsaml2
   ```

2. Then run the Moodle upgrade

3. If your IdP has a publicly available XML descriptor, copy its URL into
   the IOMAD SAML2 auth config settings page. Otherwise copy the XML verbatim into
   the settings textarea instead.

4. If your IdP requires whitelisting each SP, use the links in the settings page
   to download the XML, or you can provide that URL to your IdP administrator.

For most simple setups, this is enough to get authentication working. There are
many more settings to define how to handle new accounts, dual authentication,
and to easily debug the plugin if things are not working.


## Configuration

For setting up a new SAML integration, see the
[Quick Start Guide](https://github.com/catalyst/moodle-auth_iomadsaml2/wiki/Quick-start-Guide).

Most of the configuration is done in the Moodle admin GUI and should be self
explanatory for someone familiar with SAML generally. There are a few extra
configuration items which currently don't have a GUI and should be added
to your Moodle config.php file:

```php
$CFG->auth_iomadsaml2_disco_url = '';
$CFG->auth_iomadsaml2_store = '\\auth_iomadsaml2\\redis_store'; # Use an alternate store
$CFG->auth_iomadsaml2_redis_server = ''; # Required for the redis_store above
```


## Testing

This plugin has been tested against:

* SimpleSamlPHP set up as an IdP
* openidp.feide.no
* testshib.org
* An AAF instance of Shibboleth
* OpenAM (Sun / Oracle)
* Microsoft ADFS
* NetIQ Access Manager

To configure this against testshib you will need a moodle which is publicly
accessible over the internet. Turn on the IOMAD SAML2 plugin and then configure it:

Home ► Site administration ► Plugins ► Authentication ► IOMAD SAML2

1. Set the Idp URL to: https://www.testshib.org/metadata/testshib-providers.xml
2. Set dual auth to Yes
3. Set auto create users to Yes
4. Click on 'Download SP Metadata'
5. Save the settings
6. Upload that file to: https://www.testshib.org/register.html
7. Logout and login, you should see 'TestShib Test IdP' as an alternate login method
   and be able to login via the example credentials.


## Debugging

If you are having any issues, turn on debugging inside the IOMAD SAML2 auth plugin, as well
as turning on the Moodle level debugging. This will give in depth debugging on the SAML
XML and errors, as well as stack traces. Please include this in any GitHub issue you
create if you are having trouble.

There is also a stand-alone test page which authenticates but isn't a 'Moodle' page. All
this page does is echo the SAML attributes which have been provided by the IDP. This can
be very handy for setting up the mappings, e.g. when the IDP might be providing the
right attributes but under an unexpected key name.

```
/auth/iomadsaml2/test.php
```

If you can succesfully do a SAML login using this page then it narrows down where the
issues lie. Some common issues are:

1. You received a valid set of SAML attributes, but the attribute(s) needed are not
   present. For example, often in ADFS configuration you may need to 'release' the username.

2. You have got a valid set of attributes, but the key for the username isn't what
   you expected. Cut and paste the correct key name into the Moodle auth iomadsaml2 config
   page to correctly map the 'idpattr' value.

3. The attribute key name might be a really crazy long looking string. This is common
   with ADFS. If that long string contains certain characters then Moodle will not
   accept it, and this is an issue in Moodle itself and applies to all auth plugins.
   You can add a custom claim in ADFS to rename this attribute to something nicer.
   See: [GitHub issue #124](https://github.com/catalyst/moodle-auth_iomadsaml2/issues/124).

4. If it is bringing across all the attributes properly, but you are getting:
   "You have logged in succesfully as 'xyz' but do not have an account in Moodle"
   then you either need to change your user provisioning process to ensure users are
   created ahead of time, or you need to enable the `autocreate` setting. If you do
   auto create then you need to be very careful that auto-created users, and users
   provisioned via other means, are set up consistently.


## Gotchas

### Bitnami Moodle

We get lots of complaints in many plugins that end up being issues with Bitnami. It does a very
poor job and does not properly configure Moodle with some quite basic things and we strongly
recommend you don't use it at all, not just for SAML issues. In particular it dynamically
detects the domain that Moodle is on, which is not supported by Moodle. `$CFG->wwwroot`
MUST be manually set to a static value in `config.php`.


### Multiple IdPs

When using multiple IdPs the system will force enable the dual login setting. This is so
that a list of possible identity providers will be presented to the user when logging in.

To enable multiple IdPs you can use the 'IdP metadata XML OR public XML URL' configuration
field. An example might look like this:

```
Identity Provider Name https://ssp1.local/simplesaml/iomadsaml2/idp/metadata.php
https://ssp2.local/simplesaml/iomadsaml2/idp/metadata.php
```

If there is any text before the `https` scheme then it will be used as the override name.

It is not be recommended to use the 'IdP label override' configuration option with
multiple IdPs.


### Deep linking saml=on URL parameter

For most use cases, this parameter should work on all supported Moodle versions. However, to make
this paramater force a SAML login redirect, even when users are already logged in as a guest, we
use a [Moodle hook](https://docs.moodle.org/dev/Login_callbacks#after_config) that is only available
in Moodle >= 3.8.

To make guest user redirecting work on moodle 3.7 and below, you will need to backport
the changes from [MDL-66340](https://tracker.moodle.org/browse/MDL-66340).


### OpenAM

If you are getting signature issues with OpenAM then you may need to manually
yank out the contents of the `ds:X509Certificate` element into a file and then
import it into OpenAM's certificate store:

```bash
$ cat moodle.edu.crt
-----BEGIN CERTIFICATE-----
thesuperlongcertificatestringgoeshere=
-----END CERTIFICATE-----
$ keytool -import -trustcacerts -alias moodle.edu -file moodle.edu.crt -keystore keystore.jks
```

Then follow the prompts and restart OpenAM.


### Certificate Locking

It is possible to lock the certificates in the admin UI which prevents inadvertent
overwriting of them. They can also be unlocked in the UI. If you really want to
protect them, `chown` the files so that your webserver user cannot modify them at all.

These certificates are located in the `$CFG->dataroot/iomadsaml2` directory.

To manually unlock the certificates please restore the write permissions to the required files.
```bash
$ cd $CFG->dataroot/iomadsaml2
$ chmod 0660 site.example.crt
$ chmod 0660 site.example.pem
```


### Windows configuration for OpenSSL

Some environments, particularly Windows-based, may not provide an OpenSSL
configuration file at the default location, producing errors like the
following when regenerating certificates:

```
error:02001003:system library:fopen:No such process
error:2006D080:BIO routines:BIO_new_file:no such file
error:0E064002:configuration file routines:CONF_load:system lib
```

You may also see OpenSSL errors in various Moodle screens (including the admin
page) related to the `auth_iomadsaml2` plugin. For example:

```
Warning: openssl_csr_sign(): cannot get CSR from parameter 1 in
C:\path\to\moodle\auth\iomadsaml2\setuplib.php
```

There are two ways to resolve this problem (you only need to do one of these,
the first is probably more sensible):

1. Set the `OPENSSL_CONF` environment variable to point to the full path and
   location of an [`openssl.cnf`](https://www.openssl.org/docs/manmaster/man5/config.html)
   file (e.g. `C:\tools\php73\extras\ssl\openssl.cnf`) and restart Apache.

2. (for PHP versions <= 7.3) Make a copy of that `openssl.cnf` file in the
   location `C:\usr\local\ssl\openssl.cnf`.


### OKTA configuration

Okta has some weird names for settings which are confusing, this may help decipher them:

| Okta name             | Sane name             | Value                                                            |
| --------------------- | --------------------- | ---------------------------------------------------------------- |
| Single sign on URL    | ACS URL               | `https://example.com/auth/iomadsaml2/sp/iomadsaml2-acs.php/example.com`    |
| Audience URI          | Entity ID             | `https://example.com/auth/iomadsaml2/sp/metadata.php`                 |
| Enable Single Log Out | Enable Single Log Out | True                                                             |
| Single Logout URL     | Single Logout URL     | `https://example.com/auth/iomadsaml2/sp/iomadsaml2-logout.php/example.com` |
| Assertion Encryption  | Assertion Encryption  | Encrypted                                                        |

Suggested attribute mappings:

| Name        | Value            |
| ----------- | ---------------- |
| `Login`     | `user.login`     |
| `FirstName` | `user.firstName` |
| `LastName`  | `user.lastName`  |
| `Email`     | `user.email`     |


### Auth Proc Filter Hooks

Other plugins may hook into IOMAD SAML2 and create custom Auth Proc Filters.
Auth Proc Filters allow you to mutate the attributes passed back from the IdP
before Moodle handles them and maps them to profile fields.

Steps to implement the hook:

1. Create a plugin that will implement the hook (e.g `local_hookimplement`)
2. Define the hook function `local_hookimplement_extend_auth_iomadsaml2_proc` in the plugin's `lib.php` file.
3. The function should return an array of SimpleSaml Auth Proc Filters.

Examples:

```php
function local_hookimplement_extend_auth_iomadsaml2_proc() {
   return [
      52 => array(
         'class' => 'core:AttributeMap',
         'oid2name'
      )
   ]
}
```

Custom code:

```php
function local_hookimplement_extend_auth_iomadsaml2_proc() {
   return [
      51 => array(
         'class' => 'core:PHP',
         'code' => '$attributes = update_attributes($attributes)'
      )
   ]
}

function update_attributes($attributes) {
   if (isset($attributes["uid"])) {
      $attributes["uid"] => $attributes["username"];
   }
   return $attributes;
}
```

Multiple IdP filter:

```php
function local_hookimplement_extend_auth_iomadsaml2_proc() {
   return [
      51 => array(
         'class' => 'core:PHP',
         'code' => '$attributes = update_attributes($attributes)'
      ),
   ]
}

function update_attributes($attributes) {
   global $SESSION, $iomadsaml2auth;
    $idps = $iomadsaml2auth->metadataentities;
    foreach ($idps as $idp) {
        foreach ($idp as $key => $value) {
            if ($SESSION->iomadsaml2idp == $key) {
                $alias = $idp[$key]->alias;
            }

            if ($alias == 'idp_alias') {
                $attributes["uid"] = $attributes['username'];
            }
        }
    }
}
```


## Other SAML plugins

The diversity and variable quality and features of SAML moodle plugins is a
reflection of a great need for a solid SAML plugin, but the neglect to do
it properly in core. IOMAD SAML2 is by far the most robust and supported protocol
across the internet and should be fully integrated into Moodle core as both
a Service Provider and as an Identity Provider, and without any external
dependencies to manage.

Here is a quick run down of the alternatives:

### Moodle Core

* `auth/shibboleth` - This requires a separately installed and configured
  Shibboleth install.

    One big issue with this, and the category below, is the extra
    application between Moodle and the IdP, so the login and logout processes have
    more latency due to extra redirects. Latency on potentially slow mobile
    networks is by far the biggest bottleneck for login speed, and the biggest
    complaint by end users in our experience.

* `auth/oauth2`

    OAuth2 has direct support in Moodle.


### Plugins that require SimpleSamlPHP

These are all forks of each other, and unfortunately have diverged quite early
or have no common git history, making it difficult to cross port features or
fixes between them.

* [moodle.org/plugins/auth_saml](https://moodle.org/plugins/auth_saml)

* [moodle.org/plugins/auth_zilink_saml](https://moodle.org/plugins/auth_zilink_saml)

* [github.com/piersharding/moodle-auth_saml](https://github.com/piersharding/moodle-auth_saml)


### Plugins which embed a SAML client library

These are generally much easier to manage and configure as they are standalone.

* [moodle.org/plugins/auth_onelogin_saml](https://moodle.org/plugins/auth_onelogin_saml) - This one uses its own
  embedded SAML library which is great and promising, however it doesn't support
  'back channel logout' which is critical for security in any large organisation.

* This `auth_iomadsaml2` plugin, with an embedded and dynamically configured SimpleSamlPHP
  instance under the hood.


## Support

If you have issues please log them in
[GitHub](https://github.com/catalyst/moodle-auth_iomadsaml2/issues).

Please note our time is limited, so if you need urgent support or want to
sponsor a new feature then please contact
[Catalyst IT Australia](https://www.catalyst-au.net/contact-us).


## Warm thanks

Thanks to the various authors and contributors to the other plugins above.

Thanks to [La Trobe University](https://www.latrobe.edu.au/) in Melbourne for
sponsoring the initial creation of this plugin.

![LaTrobe](/pix/latrobe.png?raw=true)

Thanks to [Centre de gestion informatique de l’éducation (CGIE)](https://portal.education.lu/cgie/)
in Luxembourg for sponsoring the user autocreation and field mapping work.

![CGIE](/pix/cgie.png?raw=true)

This plugin was developed by [Catalyst IT Australia](https://www.catalyst-au.net/).

<img alt="Catalyst IT" src="https://cdn.rawgit.com/CatalystIT-AU/moodle-auth_iomadsaml2/MOODLE_39_STABLE/pix/catalyst-logo.svg" width="400">
