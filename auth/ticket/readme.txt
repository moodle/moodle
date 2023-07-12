Ticket Authentication

Valery Fremaux (valery@valeisti.fr)

#####################################

The Ticket authentication plugin is intended to provide
an easy way for external users to return back into a
logged in session after having received an asynchronous notification
by email.

The auth plugin will intercept a "ticket" parameter in the CGI environement
and attepts to decode the ticket prior any other other authentication
method.

The ticket is encrypted and contains information about the user account and
a target URL the user is trying to reach.

The ticket has a timelife and will be dropped away with no login after
his lifetime is elapsed.

The plugin provides a library for generating and decoding ticket. Additional
functions to send a notification with ticket attached is provided for convenience.

The Ticket auth plugin is mostly provided for Moodle developers that need having
quick return links in email notifications. There is no straight use for administrators
that can be obtained from the plugin if the library functions are not used in Moodle
code.

Install ticket auth
########################################
Unpack the plugin under auth directory in Moodle codebase.
Enable the ticket in the auth stack and raise it to a quite high position in the stack.

2018070100 / X.X.0004
==================
Adding the "internal" method for encryption, when nothing else works... light XOR based, weak method by the way.