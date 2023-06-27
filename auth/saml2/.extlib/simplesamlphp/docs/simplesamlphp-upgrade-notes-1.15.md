Upgrade notes for SimpleSAMLphp 1.15
====================================

The minimum required PHP version is now 5.4. The dependency on mcrypt has been
dropped.

A new templating system based on Twig has been introduced. The old templating
system is still available but should be considered deprecated. Custom themes
may need to be updated to include Twig-style templates as well. See the
[theming documentation](simplesamlphp-theming).

A new internationalization system based on Gettext has been introduced. While
old templates can use either the old or the new system (refer to the
"language.i18n.backend" configuration option for more information on how to
choose the internationalization backend), new Twig templates can only use the
new Gettext internationalization system.

The integrated _Auth Memcookie_ support is now deprecated and will no longer
be available starting in SimpleSAMLphp 2.0. Please use the new
[memcookie module](https://github.com/simplesamlphp/simplesamlphp-module-memcookie)
instead.

The option to specify a SAML certificate by its fingerprint, `certFingerprint`
has been deprecated and will be removed in a future release. Please use the
full certificate in `certData` instead.

The `core:AttributeRealm` authproc filter has been deprecated.
Please use `core:ScopeFromAttribute`, which is a generalised version of this.

simpleSAMLphp will now send the eduPersonTargetedID attribute in the correct
NameID XML form, instead of the incorrect simple string. It will also refuse
to parse an assertion with an eduPersonTargetedID in 'string' format.
