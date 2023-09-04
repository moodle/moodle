Upgrade notes for SimpleSAMLphp 1.17
====================================

The minimum PHP version required is now PHP 5.5.

All (remaining) classes have been changed to namespaces. There are mappings
from the legacy names so calling code should remain working. Custom code
(e.g. modules) that test for class names, e.g. when catching specific
exceptions, may need to be changed.

The possibility has been reintroduced to omit the NameIdPolicy from SP
AuthnRequests by setting NameIDPolicy to `false`. The prefered way is
to configure it as an array `[ 'Format' => format, 'AllowCreate' => true/false ]`,
which is now also the format used in the `saml:NameIDPolicy` variable
in `$state`.

The code, config and documentation have switched to using the modern PHP
array syntax. This should not have an impact as both will remain working
equally, but the code examples and config templates look slightly different.
The following are equivalent:

    // Old style array syntax
    $config = array(
        'authproc' => array(
            60 => 'class:etc'
        ),
        'other example' => 1
    );

    // Current style array syntax
    $config = [
        'authproc' => [
            60 => 'class:etc'
        ],
        'other example' => 1
    ];
