SimpleSAMLphp Identity Provider Advanced Topics
===============================================

[TOC]

AJAX iFrame Single Log-Out
--------------------------

If you have read about the AJAX iFrame Single Log-Out approach at Andreas' blog and want to enable it, edit your saml20-idp-hosted.php metadata, and add this configuration line for the IdP:

	'logouttype' => 'iframe',


Attribute Release Consent
-------------------------

The attribute release consent is documented in a [separate document](/docs/contrib_modules/consent/consent.html).


Support for bookmarking the login page
--------------------------------------

Most SAML software crash fatally when users bookmark the login page and return later on when the cached session information is lost. This is natural as the login page happens in the middle of a SAML transaction, and the SAML software needs some references to the original request in order to be able to produce the SAML Response.

SimpleSAMLphp has implemented a graceful fallback to tackle this situation. When SimpleSAMLphp is not able to lookup a session during the login process, it falls back to the *IdP-first flow*, described in the next section, where the reference to the request is not needed.

What happens in the IdP-first flow is that a *SAML unsolicited response* is sent directly to the SP. An *unsolicited response* is a SAML Response with no reference to a SAML Request (no `InReplyTo` field). 

When a SimpleSAMLphp IdP falls back to IdP-first flow, the `RelayState` parameter sent by the SP in the SAML request is also lost. The RelayState information contain a reference key for the SP to lookup where to send the user after successful authentication. The SimpleSAMLphp Service Provider supports configuring a static URL to redirect the user after a unsolicited response is received. See more information about the `RelayState` parameter in the next section: *IdP-first flow*.


IdP-first flow
--------------

If you do not want to start the SSO flow at the SP, you may use the IdP-first setup. To do this, redirect the user to the SSOService endpoint on the IdP with a `spentityid` parameter that matches the SP EntityID that the user should be authenticated for.

Here is an example of such a URL:

	https://idp.example.org/simplesaml/saml2/idp/SSOService.php?spentityid=urn:mace:feide.no:someservice

You can also add a `RelayState` parameter to the IdP-first URL:

	https://idp.example.org/simplesaml/saml2/idp/SSOService.php?spentityid=urn:mace:feide.no:someservice&RelayState=https://sp.example.org/somepage

The `RelayState` parameter is often used to carry the URL the SP should redirect to after authentication. It is also possible to specify the Assertion
Consumer URL with the `ConsumerURL` parameter.

For compatibility with certain SPs, SimpleSAMLphp will also accept the
`providerId`, `target` and `shire` parameters as aliases for `spentityid`,
`RelayState` and `ConsumerURL`, respectively.


### IdP first with SAML 1.1

A SAML 1.1 SP does not send an authentication request to the IdP, but instead triggers IdP initiated authentication directly.
If you want to do it manually, you can access the following URL:

	https://idp.example.org/simplesaml/shib13/idp/SSOService.php?providerId=urn:mace:feide.no:someservice&shire=https://sp.example.org/acs-endpoint&target=https://sp.example.org/somepage

The parameters are as follows:

`providerID`
:   The entityID of the SP.
    This parameter is required.

`shire`
:   The AssertionConsumerService endpoint of the SP.
    This parameter is required.

`target`
:   The target parameter the SP should receive with the authentication response.
    This is often the page the user should be sent to after authentication.
    This parameter is optional for the IdP, but must be specified if the SP you are targeting is running SimpleSAMLphp.

:   *Note*: This parameter must be sent as `target` (with lowercase letters) when starting the authentication, while it is sent as `TARGET` (with uppercase letters) in the authentication response.


IdP-initiated logout
--------------------

IdP-initiated logout can be initiated by visiting the URL:

    https://idp.example.org/simplesaml/saml2/idp/SingleLogoutService.php?ReturnTo=<URL to return to after logout>

It will send a logout request to each SP, and afterwards return the user to the URL specified in the `ReturnTo` parameter. Bear in mind that IdPs might disallow redirecting to URLs other than those of their own for security reasons, so in order to get the redirection to work, it might be necessary to ask the IdP to whitelist the URL we are planning to redirect to.
