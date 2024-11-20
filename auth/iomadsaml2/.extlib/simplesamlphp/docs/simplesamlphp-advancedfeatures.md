SimpleSAMLphp Advanced Features
===============================

[TOC]

SimpleSAMLphp documentation
---------------------------

This document is part of the SimpleSAMLphp documentation suite.

- [List of all SimpleSAMLphp documentation](http://simplesamlphp.org/docs)


This document assumes that you already have a installation of
SimpleSAMLphp running, configured and working. This is the next
step :)


Bridging between protocols
--------------------------

A bridge between two protocols is built using both an IdP and an SP, connected together.
To let a SAML 2.0 SP talk to a SAML 1.1 IdP, you build a SimpleSAMLphp bridge from a SAML 2.0 IdP and a SAML 1.1 SP.
The SAML 2.0 SP talks to the SAML 2.0 IdP, which hands the request over to the SAML 1.1 SP, which forwards it to the SAML 1.1 IdP.

If you have followed the instructions for setting up an SP, and have configured an authentication source, all you need to do is to add that authentication source to the IdP.

**Example of bridge configuration**

In `metadata/saml20-idp-hosted.php`:

    'auth' => 'default-sp',

In `config/authsources.php`:

    'default-sp' => [
        'saml:SP',
    ],



Attribute control
-----------------

Filtering, mapping, etc can be performed by using existing or create new *Authentication Processing Filters*. For more information, read:

  * [Authentication Processing Filters in SimpleSAMLphp](simplesamlphp-authproc)



Automatic update of SAML 2.0 Metadata XML from HTTPS
----------------------------------------------------

The `metarefresh` module is the preferred method for doing this.
Please see the [metarefresh documentation](/docs/contrib_modules/metarefresh/simplesamlphp-automated_metadata).



Using simpleSAMLphp on a web server requiring the use of a web proxy
--------------------------------------------------------------------

Some modules in simpleSAMLphp may require fetching HTTP/HTTPS content from external websites (e.g. the metarefresh module needs to fetch the metadata from an external source).

simpleSAMLphp can be configured to send HTTP/S requests via such a proxy. The proxy can be configured in the config/config.php option "proxy". Should the proxy require authentication, this can be configured with "proxy.auth".

The default is not to use a proxy ('proxy' = null) and no username and password are used ('proxy.auth' = false).



Auth MemCookie
--------------

[Auth MemCookie](http://authmemcookie.sourceforge.net/) support is deprecated in the standard code base of SimpleSAMLphp
 and will no longer be available starting in SimpleSAMLphp 2.0. Please use the new
 [memcookie module](https://github.com/simplesamlphp/simplesamlphp-module-memcookie) instead.



Metadata signing
----------------

SimpleSAMLphp supports signing of the metadata it generates. Metadata signing is configured by four options:

- `metadata.sign.enable`: Whether metadata signing should be enabled or not. Set to `TRUE` to enable metadata signing. Defaults to `FALSE`.
- `metadata.sign.privatekey`: Name of the file with the private key which should be used to sign the metadata. This file must exist in in the `cert` directory.
- `metadata.sign.privatekey_pass`: Passphrase which should be used to open the private key. This parameter is optional, and should be left out if the private key is unencrypted.
- `metadata.sign.certificate`: Name of the file with the certificate which matches the private key. This file must exist in in the `cert` directory.
- `metadata.sign.algorithm`: The algorithm to use when signing metadata for this entity. Defaults to RSA-SHA256. Possible values:

    * `http://www.w3.org/2000/09/xmldsig#rsa-sha1`
       *Note*: the use of SHA1 is **deprecated** and will be disallowed in the future.
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha256`
      The default.
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha384`
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha512`

These options can be configured globally in the `config/config.php`-file, or per SP/IdP by adding them to the hosted metadata for the SP/IdP. The configuration in the metadata for the SP/IdP takes precedence over the global configuration.

There is also an additional fallback for the private key and the certificate. If `metadata.sign.privatekey` and `metadata.sign.certificate` isn't configured, SimpleSAMLphp will use the `privatekey`, `privatekey_pass` and `certificate` options in the metadata for the SP/IdP.




Session checking function
-------------------------

Optional session checking function, called on session init and loading, defined with 'session.check_function' in config.php.

Example code for the function with GeoIP country check:


    public static function checkSession($session, $init = FALSE) {
        $data_type = 'example:check_session';
        $data_key = 'remote_addr';

        $remote_addr = NULL;
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $remote_addr = (string)$_SERVER['REMOTE_ADDR'];
        }

        if ($init) {
            $session->setData($data_type, $data_key, $remote_addr, \SimpleSAML\Session::DATA_TIMEOUT_SESSION_END);
            return;
        }

        if (!function_exists('geoip_country_code_by_name')) {
            SimpleSAML\Logger::warning('geoip php module required.');
            return TRUE;
        }

        $stored_remote_addr = $session->getData($data_type, $data_key);
        if ($stored_remote_addr === NULL) {
            SimpleSAML\Logger::warning('Stored data not found.');
            return FALSE;
        }

        $country_a = geoip_country_code_by_name($remote_addr);
        $country_b = geoip_country_code_by_name($stored_remote_addr);

        if ($country_a === $country_b) {
            if ($stored_remote_addr !== $remote_addr) {
                $session->setData($data_type, $data_key, $remote_addr, \SimpleSAML\Session::DATA_TIMEOUT_SESSION_END);
            }

            return TRUE;
        }

        return FALSE;
    }




Support
-------

If you need help to make this work, or want to discuss
SimpleSAMLphp with other users of the software, you are fortunate:
Around SimpleSAMLphp there is a great Open source community, and
you are welcome to join! The forums are open for you to ask
questions, contribute answers other further questions, request
improvements or contribute with code or plugins of your own.

-  [SimpleSAMLphp homepage](https://simplesamlphp.org)
-  [List of all available SimpleSAMLphp documentation](https://simplesamlphp.org/docs/)
-  [Join the SimpleSAMLphp user's mailing list](https://simplesamlphp.org/lists)




