`saml:ExpectedAuthnContextClassRef`
===================

SP side attribute filter to validate AuthnContextClassRef.

This filter checks the AuthnContextClassRef in the authentication response, and accepts or denies the access depending on the provided strength measure of authentication from IdP.

You can list the accepted authentitcation context values in the Service Provider configuration file.
If the given AuthnContextClassRef does not match any accepted value, the user will be redirected to an error page. It's useful to harmonize the SP's requested AuthnContextClassRef (another authproc filter), but you can accept more authentication strength measures than you requested for.

Examples
--------

    'authproc.sp' => array(
      91 => array(
        'class' => 'saml:ExpectedAuthnContextClassRef',
        'accepted' => array(
          'urn:oasis:names:tc:SAML:2.0:post:ac:classes:nist-800-63:3',
          'urn:oasis:names:tc:SAML:2.0:ac:classes:Password',
        ),
      ),
    ),
