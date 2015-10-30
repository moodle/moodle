This directory contains authentication modules.

Each of these modules describes a different way to
check that a user has provided a correct

   - username, and
   - password.

Even when external forms of authentication are being used, Moodle still
maintains the internal "user" table with all the associated information about
that user such as name, email address and so on.


Multiauthentication in Moodle 1.8
-------------------------------------

The active methods are set by the admin on the Configuration page. Multiple
authentication plugins can now be used and ordered in a fail-through sequence.
One plugin can be selected for interactive login as well (which will need to be
part of the enabled plugin sequence).


email - authentication by email  (DEFAULT METHOD)

    - user fills out form with email address
    - email sent to user with link
    - user clicks on link in email to confirm
    - user account is created
    - user can log in


none  - no authentication at all .. very insecure!!

    - user logs in using ANY username and password
    - if the username doesn't already exist then
      a new account is created
    - when user tries to access a course they
      are forced to set up their account details


nologin  - user can not log in, login as is possible

    - this plugin can be used to prevent normal user login


manual - internal authentication only

    - user logs in using username and password
    - no way for user to make their own account


ldap  - Uses an external LDAP server

    - user logs in using username and password
    - these are checked against an LDAP server
    - if correct, user is logged in
    - optionally, info is copied from the LDAP
      database to the Moodle user database

    (see the ldap/README for more details on config etc...)


imap  - Uses an external IMAP server

    - user logs in using username and password
    - these are checked against an IMAP server
    - if correct, user is logged in
    - if the username doesn't already exist then
      a new account is created


pop3  - Uses an external POP3 server

    - user logs in using username and password
    - these are checked against a POP3 server
    - if correct, user is logged in
    - if the username doesn't already exist then
      a new account is created


nntp  - Uses an external NNTP server

    - user logs in using username and password
    - these are checked against an NNTP server
    - if correct, user is logged in
    - if the username doesn't already exist then
      a new account is created


db  - Uses an external database to check username/password

    - user logs in using username and password
    - these are checked against an external database
    - if correct, user is logged in
    - if the username doesn't already exist then
      a new Moodle account is created


--------------------------------------------------------------------------------

Authentication API
------------------


AUTHENTICATION PLUGINS
----------------------
Each authentication plugin is now contained in a subfolder as a class definition
in the auth.php file. For instance, the LDAP authentication plugin is the class
called auth_plugin_ldap defined in:

   /auth/ldap/auth.php

To instantiate the class, there is a function in lib/moodlelib called
get_auth_plugin() that does the work for you:

   $ldapauth = get_auth_plugin('ldap');

Auth plugin classes are pretty basic and should be extending auth_plugin_base class.
They contain the same functions that were previously in each plugin's lib.php file,
but refactored to become class methods, and tweaked to reference the plugin's instantiated
config to get at the settings, rather than the global $CFG variable.

When creating new plugins you can either extend the abstract auth_plugin_base class
(defined in lib/authlib.php) or create a new one and implement all methods from
auth_plugin_base.

The new plugin architecture allows creating of more advanced types such as custom SSO
without the need to patch login and logout pages (see *_hook() methods in existing plugins).

Configuration
-----------------

All auth plugins must have a config property that contains the name value pairs
from the config_plugins table. This is populated using the get_config() function
in the constructor. The settings keys have also had the "auth_" prefix, as well
as the auth plugin name, trimmed. For instance, what used to be

   echo $CFG->auth_ldapversion;

is now accessed as

   echo $ldapauth->config->version;

Authentication settings have been moved to the config_plugins database table,
with the plugin field set to "auth/foo" (for instance, "auth/ldap").


Method Names
-----------------

When the functions from lib.php were ported to methods in auth.php, the "auth_"
prefix was dropped. For instance, calls to

   auth_user_login($user, $pass);

now become

   $ldapauth->user_login($user, $pass);

this also avoids having to worry about which auth/lib file to include since
Moodle takes care of it for you when you create an instance with
get_auth_plugin().

The basic class defines all applicable methods that moodle uses, you can find
more information in lib/authlib.php file.


Upgrading from Moodle 1.7
-----------------------------

Moodle will upgrade the old auth settings (in $CFG->auth_foobar where foo is the
auth plugin and bar is the setting) to the new style in the config_plugin
database table.



Upgrading from Moodle 1.8
------------------------------

user_activate() method was removed from public API because it was used only from user_confirm() in LDAP
