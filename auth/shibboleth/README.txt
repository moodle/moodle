Shibboleth authentication for Moodle
-------------------------------------------------------------------------------
Requirements
- Shibboleth target 1.1 or later

SHIBBOLET Target configuration
-------------------------------------------------------------------------------

#### 1. Only shibboleth users are allowed use this Moodle

Just add shibboleth protection against Moodle directory.

#### 2. Shibboleth and manually added users are able to use Moodle


You need to use lazy sessions for Moodle directory. Lazy session can be
turned on by adding lines below to your .htaccess file in Moodle directory:

## Shibboleth lazy session
AuthType shibboleth
ShibRequireSession Off
require shibboleth

Lazy session allows users to access Moodle directory without having to
authenticate against shibboleth. When user authenticates against Shibboleth
the attributes which shibboleth provide get accessible ($_SERVER). These
attributes are used by Moodle to determine users identity.

For envoking shibboleth session:

1. make a directory for example moodle-proxy (in place where it's accessible
from web)
2. create index.php and add lines below (redirect to your moodle):
<?
header("Location:https://my.domain.com/moodle/login/index.php");
exit;
?>
3. Add .htaccess file in this directory which contains:

## Shibboleth authentication required
AuthType shibboleth
ShibRequireSession On
require valid-user



MOODLE Authentication options
-------------------------------------------------------------------------------

Shibboleth origin url: 
This is were you put the Shibboleth WAYF url address or the Shibboleth Origin
login url if WAYF is not used. If user selects shibboleth authentication
method he/she is redirected there to authenticate.

Username, First name, Surname, Email address:
The fields in authentication options are filled with the names of the
shibboleth attributes that your server provides for example:

$_SERVER['HTTP_SHIB_PNAME'] is the defined attribute in shibboleth target
configuration for username use HTTP_SHIB_PNAME in Username field at
authentication options
