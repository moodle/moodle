Adding Enhanced Client or Proxy (ECP) Profile support to the IdP
===============================================================

This document describes the necessary steps to enable support for the [SAML V2.0 Enhanced Client or Proxy Profile Version 2.0](http://docs.oasis-open.org/security/saml/Post2.0/saml-ecp/v2.0/cs01/saml-ecp-v2.0-cs01.pdf) on a simpleSAMLphp Identity Provider (IdP).

The SAML V2.0 Enhanced Client or Proxy (ECP) profile is a SSO profile for use with HTTP, and clients with the capability to directly contact a principal's identity provider(s) without requiring discovery and redirection by the service provider, as in the case of a browser. It is particularly useful for desktop or server-side HTTP clients.

Limitations
-----------
* Authentication must be done via [HTTP Basic authentication](https://developer.mozilla.org/en-US/docs/Web/HTTP/Authentication#Basic_authentication_scheme).
* Authentication must not require user interaction (e.g. auth filters that require user input).
* Channel Bindings are unsupported.
* "Holder of Key" Subject Confirmation is unsupported.

This feature has been tested to work with Microsoft Office 365, but other service providers may require features of the ECP profile that are currently unsupported!

Enabling ECP Profile on the IdP
-----------------------------------

To enable the IdP to send ECP assertions you must add the `saml20.ecp` option to the `saml20-idp-hosted` metadata file:

    $metadata['__DYNAMIC:1__'] = [
        [....]
        'auth' => 'example-userpass',
        'saml20.ecp' => true,
    ];

Note: authentication filters that require interaction with the user will not work with ECP.

Add new metadata to SPs
-----------------------

After enabling the ECP Profile your IdP metadata will change. An additional ECP `SingleSignOnService` endpoint is added.
You therefore need to update the metadata for your IdP at your SPs.
The `saml20-idp-remote` metadata for simpleSAMLphp SPs should contain something like the following code:

	'SingleSignOnService' =>
	  array (
	    0 =>
	    array (
	      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
	      'Location' => 'https://idp.example.org/simplesaml/saml2/idp/SSOService.php',
	    ),
	    1 =>
	    array (
	      'index' => 0,
	      'Location' => 'https://didp.example.org/simplesaml/saml2/idp/SSOService.php',
	      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
	    ),
	  ),

SP metadata on the IdP
----------------------

A SP using the ECP Profile must have an `AssertionConsumerService` endpoint supporting that profile.
This means that you have to use the complex endpoint format in `saml20-sp-remote` metadata.
In general, this should look like the following code:

	'AssertionConsumerService' =>
	  array (
	    0 =>
	    array (
	      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
	      'Location' => 'https://sp.example.org/Shibboleth.sso/SAML2/POST',
	      'index' => 1,
	    ),
	    1 =>
	    array (
	      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:PAOS',
	      'Location' => 'https://sp.example.org/ECP',
	      'index' => 2,
	    ),
	  ),

