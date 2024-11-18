`core:LanguageAdaptor`
======================

SimpleSAMLphp has built in language support, and stores the preferred language in a cookie.

Identity systems also often has a specific attribute that indicates what language is understood by the user.
MACE defines an attribute with preferred language: `preferredLanguage`.
[Read more about the preferredLanguage attribute defined by MACE](https://tools.ietf.org/html/rfc2798#section-2.7).

The LanguageAdaptor brings these two concepts together.
If executed early at the IdP it will check if the `preferredLanguage` attribute is among the users attributes, and if it is, SimpleSAMLphp will use that language in the user interface.
**Notice that** the login page itself is to early to be influenced by the user attributes, because the IdP does not know any user attributes before the user logs in.
In contrast, the consent module will be presented in the correct language based on user attribute.

The LanguageAdaptor also works the other way around.
If the user does not have the `preferredLanguage` attribute, the user interface for the user will be set to the default for the installation.
If this language is not correct for the user, the user may click to switch language on the login page (or any other UI page in SimpleSAMLphp).
SimpleSAMLphp then stores the preferred language in a cookie.
Now, the LanguageAdaptor will read the preferred language from the cookie and add a user attribute with the preferred language, that is sent to the service provider.

The name of the attribute can be changed from the default by adding the `attributename` option.


Examples
--------

Default attribute (`preferredLanguage`):

    'authproc' => array(
        50 => array(
            'class' => 'core:LanguageAdaptor',
        ),
    ),

Custon attribute:

    'authproc' => array(
        50 => array(
            'class' => 'core:LanguageAdaptor',
            'attributename' => 'lang',
        ),
    ),
