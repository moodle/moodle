MultiAuth module
================

The MultiAuth module provides a method for users to choose between
a list of authentication sources. There is only one authentication
module:

`multiauth:MultiAuth`
: Authenticate the user against a list of authentication sources.


`multiauth:MultiAuth`
---------------------

This module is useful when you want to let the users decide which
authentication source fits better their needs at any scenario. For
example, they can choose the `saml` authentication source in most
cases and then switch to the `admin` authentication source when
'saml' is down for some reason.

To create a MultiAuth authentication source, open
`config/authsources.php` in a text editor, and add an entry for the
authentication source:

    'example-multi' => array(
        'multiauth:MultiAuth',

        /*
         * The available authentication sources.
         * They must be defined in this authsources.php file.
         */
        'sources' => array(
            'example-saml' => array(
                'text' => array(
                    'en' => 'Log in using a SAML SP',
                    'es' => 'Entrar usando un SP SAML',
                ),
                'css-class' => 'SAML',
                'AuthnContextClassRef' => array('urn:oasis:names:tc:SAML:2.0:ac:classes:SmartcardPKI', 'urn:oasis:names:tc:SAML:2.0:ac:classes:MobileTwoFactorContract'),
            ),
            'example-admin' => array(
                'text' => array(
                    'en' => 'Log in using the admin password',
                    'es' => 'Entrar usando la contraseña de administrador',
                ),
                'AuthnContextClassRef' => 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport',
            ),
        ),
    ),

    'example-saml' => array(
        'saml:SP',
        'entityId' => 'my-entity-id',
        'idp' => 'my-idp',
    ),

    'example-admin' => array(
        'core:AdminPassword',
    ),

You should update the name of this authentication source
(`example-multi`), and the authentication sources it references,
to have a name which makes sense to your organization.

The MultiAuth authentication sources only has one option: the
`sources` option, and it is required. It is an array of other
authentication sources defined in the `config/authsources.php`
file. The order in this array does not matter since the user
is the one that decides which one to use.

The keys of the sources array are the identifiers of authentication
sources defined in the authsources.php configuration file. The
values are arrays with information used to create the user
interface that will let the user select the authentication source
he wants. Older versions of the multiauth module did not have
this structure and just have the authsources identifiers as the
values of the sources array. It has been improved in a backwards
compatible fashion so both cases should work.

Each source in the sources array has a key and a value. As
mentioned above the key is the authsource identifier and the value
is another array with optional keys: 'text', 'css-class', 'help', and 'AuthnContextClassRef'.
The text element is another array with localized strings for one
or more languages. These texts will be shown in the selectsource.php
view. Note that you should at least enter the text in the default
language as specified in your config.php file. The css-class
element is a string with the css class that will be applied to
the &lt;li> element in the selectsource.php view. By default the
authtype of the authsource is used as the css class with colons
replaced by dashes. So in the previous example, the css class used
in the 'example-admin' authentication source would be
'core-AdminPassword'. The help element is another array with localized
strings for one or more languages. These texts will be shown in the
selectsource.php view. The AuthnContextClassRef is either a string or
an array of strings containing [context class ref names](https://docs.oasis-open.org/security/saml/v2.0/saml-authn-context-2.0-os.pdf).
If an SP sets AuthnContextClassRef the list of authsources will be
filtered to only those containing context class refs that are part of the list set by the SP.
If a single authsource results from this filtering the user will be taken directly to the
authentication page for that source, and will never be shown the multiauth select page.

It is possible to add the parameter `source` to the calling URL, 
when accessing a service, to allow the user to preselect the
authsource to be used. This can be handy if you support different
authentication types for different types of users and you want the 
users to have a direct link to the service and not want them to 
select the correct authentication source.

For example:

    htttps://example.com/service/?source=saml
    
will take you directly to the SAML authentication source, instead 
of hitting the multiauth select page, but this works only if you 
don't have redirections during the authentication process.

You can also use the multiauth:preselect parameter to the login call:

    $as = new \SimpleSAML\Auth\Simple('my-multiauth-authsource');
    $as->login(array(
        'multiauth:preselect' => 'default-sp',
    ));

Or add the `preselect` option in the filter:

    'example-multi' => array(
        'multiauth:MultiAuth',

        /*
         * The available authentication sources.
         * They must be defined in this authsources.php file.
         */
        'sources' => array(
            'example-saml' => array(
            // ...
            ),
            'example-admin' => array(
            // ...
            ),
        ),
        'preselect' => 'example-saml',
    ),

The order of priority, in case more than one option was used is: 
`source` url parameter, `multiauth:preselect` login state and
`preselect` filter option.
