Shibboleth Authentication for Moodle
-------------------------------------------------------------------------------

Requirements:
- Moodle 1.5 or later. Versions prior to 1.5 may also work with Shibboleth
  authentication (contact  Markus Hagman <hagman@hytti.uku.fi> or Lukas 
  Haemmerle <haemmerle@switch.ch> for further instructions)
- Shibboleth target 1.1 or later. See documentation for your Shibboleth 
  federation on how to set up Shibboleth.
- Modifications to login process by Martin Dougiamas

Moodle Configuration
-------------------------------------------------------------------------------
1. As Moodle admin, go to the "Administrations >> Users >> Authentication 
   Options" and select the "Shibboleth" authentication method from the pop-up. 
2. Fill in the fields of the form. The fields "Username", "First name", 
   "Surname", etc should contain the name of the environment variables of the 
   Shibboleth attributes that you want to map onto the corresponding Moodle 
   variable.
   Especially the "Username" field is of great importance because 
   this attribute is used for the authentication of Shibboleth users.
   The large text field ('Login link') should contain a link to the 
   moodle/auth/shibboleth/ directory. This directory is protected 
   by a .htaccess file and causes the Shibboleth login procedure to start.
   If only users from one Identity Provider use Shibboleth, you also could 
   insert a link to the Identity Provier's Handle Server with a 'target' and a 
   'shire' GET argument so that the users don't have to make the detour over the
   WAYF server.

   Save the changes for the Shibboleth authentication method.

How the Shibboleth authentication works
--------------------------------------------------------------------------------
For a user to get Shibboleth authenticated in Moodle he first must get 
redirected to moodle/auth/shibboleth/login.php . When Shibboleth is active
this happens automatically from the normal login page.
If the user is successfully Shibboleth authenticated he also is authenticated in
Moodle
If the user's Moodle account has not existed yet, it gets automatically created.
To prevent that every Shibboleth user can access your Moodle site you have to
adapt the 'require valid-user' line in your webserver's config  (see step 1) to 
allow only specific users. 
Check the documentation of your Shibboleth federation for further
assistance on this. Basically you have to exchange the 'require valid-user' by 
something more constraining, e.g. 'require affiliation student'.

Unless you check the 'Shibboleth only' option in the configuration, you can use
Shibboleth AND another authentication method (it was tested with manual login 
only). So if there are a few users that don't have a Shibboleth login, you could
create manual account for them and they could use the manual login.

In such cases, users get redirected back to the normal Moodle login page to
login.

--------------------------------------------------------------------------------
In case of problems and questions contact Markus Hagman
<hagman@hytti.uku.fi> or Lukas Haemmerle <haemmerle@switch.ch>
