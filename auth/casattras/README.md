CAS server (SSO) with user-attribute release
============================================

![Moodle Plugin CI](https://github.com/LafColITS/Moodle-auth_casattras/workflows/Moodle%20Plugin%20CI/badge.svg)

This is an authentication plugin for Moodle that authenticates users via a Central Authentication Service (CAS) server and populates the Moodle user-account's attributes from user-attributes included in the CAS response.

This method does **not** make use of LDAP for user-attribute lookup, allowing its use in situations where there is no LDAP server that includes user information, or there are multiple LDAP servers that include user information.

This authentication method makes use of the attributes returned by the phpCAS library's `phpCAS::getAttributes()` function and which are often returned from modern CAS servers.

Requirements
------------
* Moodle 4.1 (build 2022111800 or later)
*  A CAS server that supports attribute-release via one of...
    1. The SAML 1.1 protocol
    2. The CAS 2.0 protocol with the serviceValidate JSP customized to include attributes
    3. The CAS 3.0 protocol

Installation
------------

1. Download the source for this authentication module and place it in `moodle/auth/casattras/`.
    This can be accomplished with

            cd /path/to/my/moodle/
            git clone https://github.com/middlebury/Moodle-auth_casattras.git auth/casattras

1. Log into Moodle as a site adminstrator. You should be prompted to run a database update to install the plugin.

1. If you are going to configure SSL certificate validation of the CAS server (to prevent man-in-the-middle attacks on the login  response) then save the certificate-authority certificate (CA-cert) to the filesystem where it is readable by Moodle and note its path.

Configuration
-------------
1. Log into Moodle as a site administrator.
1. If you don't already, make sure that you have a **manual** authentication-type admin account that you can log in with.
1. Log in with the **manual** authentication-type admin account to ensure that you won't get locked out while changing around authentication settings.
1. In Moodle, go to *Site Administration* -> *Plugins* -> *Authentication* -> *Manage Authentication*
1. Edit the settings for **CAS server (SSO) with user-attribute release** to fit your CAS server.
1. If configuring CAS server certificate validation, enter the CA-cert path for the "Certificate path" field.
1. Edit the "Data Mapping" fields to match the user-attributes returned by your CAS server.
1. Save the configuration.
1. Disable the built-in **CAS server (SSO)** authentication type. This authentication plugin uses a newer version of phpCAS which would conflict with the built-in **CAS server (SSO)** authentication type, so both cannot be enabled at the same time.
1. Enable the **CAS server (SSO) with user-attribute release** authentication type.

Migration
-------------
The following sample database query would migrate users from the `cas` authentication method to `casattras`:

```sql
UPDATE mdl_user SET auth='casattras' WHERE auth='cas';
```

Author
------
Charles Fulton (fultonc@lafayette.edu)
Adam Franco