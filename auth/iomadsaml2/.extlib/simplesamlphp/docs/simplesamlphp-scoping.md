Scoping
========================

<!-- 
    This file is written in Markdown syntax. 
    For more information about how to use the Markdown syntax, read here:
    http://daringfireball.net/projects/markdown/syntax
-->


<!-- {{TOC}} -->

Scoping allows a service provider to specify a list of identity providers in an
authnRequest to a proxying identity provider. This is an indication to the 
proxying identity provider that the service will only deal with the identity
providers specified.

A common use is for a service provider in a hub-and-spoke architecture to 
manage its own discovery service and being able to tell the hub/proxy-IdP which 
(backend-end) identity provider to use. The standard discovery service in 
SimpleSAMLphp will show the intersection of all the known IdPs and the IdPs 
specified in the scoping element. If this intersection only contains one IdP, 
then the request is automatically forwarded to that IdP.

Scoping is a SAML 2.0 specific option.

Options
-------

SimpleSAMLphp supports scoping by allowing the following options:

`ProxyCount`
: Specifies the number of proxying indirections permissible
between the identity provider receiving the request and the identity provider 
who ultimately authenticates the user. A count of zero permits no proxying. If 
ProxyCount is unspecified the number of proxy indirections is not limited.

`IDPList`
: The list of trusted IdPs, i.e. the list of entityIDs for identity providers
that are relevant for a service provider in an authnRequest. 

### Note ###
SimpleSAMLphp does not support specifying the GetComplete option.

Usage
-----

The ProxyCount and IDPList option can be specified in the following places:

- as a state parameter to the authentication source
- in the saml:SP authentication source configuration
- in the saml20-idp-remote metadata
- in the saml20-sp-remote metadata

Example configuration:

    # Add the IDPList
    'IDPList' => [
        'IdPEntityID1',
        'IdPEntityID2',
        'IdPEntityID3',
    ],
    
    # Set ProxyCount
    'ProxyCount' => 2,

RequesterID element
-------------------

To allow an identity provider to identify the original requester and the 
proxying identity providers, SimpleSAMLphp adds the RequesterID element to 
the request and if necessary the scoping element even if explicit scoping is 
not used.

The RequesterId elements are available from the state array as an array, for
instance the authenticate method in an authentication source

    $requesterIDs = $state['saml:RequesterID'];

AuthenticatingAuthority element
-------------------------------

To allow a service provider to identify the authentication authorities that 
were involved in the authentication of the user, SimpleSAMLphp adds the 
AuthenticatingAuthority elements.

The list of authenticating authorities (the AuthenticatingAuthority element) 
can be retrieved as an array from the authentication data.

    # Get the authentication source.
    $as = new \SimpleSAML\Auth\Simple();

    # Get the AuthenticatingAuthority
    $aa = $as->getAuthData('saml:AuthenticatingAuthority');

Support
-------

If you need help to make this work, or want to discuss SimpleSAMLphp with other users of the software, you are fortunate: Around SimpleSAMLphp there is a great Open source community, and you are welcome to join! The forums are open for you to ask questions, contribute answers other further questions, request improvements or contribute with code or plugins of your own.

- [SimpleSAMLphp homepage](https://simplesamlphp.org)
- [List of all available SimpleSAMLphp documentation](https://simplesamlphp.org/docs/)
- [Join the SimpleSAMLphp user's mailing list](https://simplesamlphp.org/lists)

