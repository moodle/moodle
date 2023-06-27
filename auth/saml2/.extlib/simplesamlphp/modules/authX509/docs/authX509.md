Using the X509 authentication source with SimpleSAMLphp
=======================================================

The authX509 module provides X509 authentication with certificate
validation. For now there is only one authentication source:

* authX509userCert Validate against LDAP userCertificate attribute

More validation schemes (OCSP, CRL, local list) might be added later.

Configuring Apache
------------------

This module assumes that the server requests a client certificate, and
stores it in the environment variable SSL_CLIENT_CERT. This can be achieved
with such a configuration:

    SSLEngine on
    SSLCertificateFile /etc/openssl/certs/server.crt
    SSLCertificateKeyFile /etc/openssl/private/server.key
    SSLCACertificateFile /etc/openssl/certs/ca.crt
    SSLVerifyClient require
    SSLVerifyDepth 2
    SSLOptions +ExportCertData

Note that SSLVerifyClient can be set to optional if you want to support
both certificate and plain login authentication at the same time (more on
this later).

If your server or your client (or both!) have TLS renegotiation disabled
as a workaround for CVE-2009-3555, then the configuration directive above
must not appear in a &lt;Directory&gt;, &lt;Location&gt;, or in a name-based
&lt;VirtualHost&gt;. You can only use them server-wide, or in &lt;VirtualHost&gt;s
with different IP address/port combinations.


Setting up the authX509 module
------------------------------

The first thing you need to do is to enable the module:

    touch modules/authX509/enable

Then you must add it as an authentication source. Here is an
example authsources.php entry:

    'x509' => array(
        'authX509:X509userCert',
        'hostname' => 'ldaps://ldap.example.net',
        'enable_tls' => false,
        'attributes' => array('cn', 'uid', 'mail', 'ou', 'sn'),
        'search.enable' => true,
        'search.attributes' => array('uid', 'mail'),
        'search.base' => 'dc=example,dc=net',
        'authX509:x509attributes' => array('UID' => 'uid'),
        'authX509:ldapusercert' => array('userCertificate;binary'),
    ),

The configuration is the same as for the LDAP module, except for
two options:

* x509attributes is used to map a certificate subject attribute to
                 an LDAP attribute. It is used to find the certificate
                 owner in LDAP from the certificate subject. If multiple
                 mappings are provided, any mapping will match (this
                 is a logical OR). Default is array('UID' => 'uid').
* ldapusercert   the LDAP attribute in which the user certificate will
                 be found. Default is userCertificate;binary. This can
                 be set to NULL to avoid looking up the certificate in
                 LDAP.


Uploading certificate in LDAP
-----------------------------

Certificates are usually stored in LDAP as DER, in binary. Here is
how to convert from PEM to DER:

    openssl x509 -in cert.pem -inform PEM -outform DER -out cert.der

Here is some LDIF to upload the certificate in the directory:

    dn: uid=jdoe,dc=example,dc=net
    changetype: modify
    add: userCertificate;binary
    userCertificate;binary:< file:///path/to/cert.der


Supporting both certificate and login authentication
====================================================

In your Apache configuration, set SSLVerifyClient to optional. Then you
can hack your metadata/saml20-idp-hosted.php file that way:

    $auth_source = empty($_SERVER['SSL_CLIENT_CERT']) ? 'ldap' : 'x509';
    $metadata = array(
        '__DYNAMIC:1__' => array(
            'host'          =>      '__DEFAULT__',
            'privatekey'    =>      'server.key',
            'certificate'   =>      'server.crt',
            'auth'          =>       $auth_source,
            'authority'     =>      'login',
            'userid.attribute' =>   'uid',
            'logouttype'    =>      'iframe',
            'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri',
    )

Checking certificate expiry
===========================

To issue warnings to users whose certificate is about to expire, configure an authproc filter.

Example:

     10 => array(
         'class' => 'authX509:ExpiryWarning',
         'warndaysbefore' => '30',
         'renewurl' => 'https://myca.com/renew',
     ),

Parameter `warndaysbefore` specifies the number of days the user's certificate needs to be valid before a warning is
issued. The default is 30.

Parameter `renewurl` specifies the URL of your Certification Authority. If specified, the user is suggested to renew the
certificate immediately.
