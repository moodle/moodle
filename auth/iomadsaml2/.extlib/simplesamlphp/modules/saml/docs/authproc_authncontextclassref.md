`saml:AuthnContextClassRef`
===========================

IDP-side filter for setting the `AuthnContextClassRef` element in the authentication response.

Examples
--------

    'authproc.idp' => array(
      92 => array(
        'class' => 'saml:AuthnContextClassRef',
        'AuthnContextClassRef' => 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport',
      ),
    ),
