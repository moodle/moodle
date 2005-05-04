Shibboleth Authentication for Moodle
-------------------------------------------------------------------------------

Requirements:
- Moodle 1.5 or later. Versions prior to 1.5 may also work with Shibboleth
  authentication (contact Lukas  Haemmerle <haemmerle@switch.ch> for further 
  instructions)
- Shibboleth target 1.1 or later. See documentation for your Shibboleth 
  federation on how to set up Shibboleth.
- Modifications to login process by Martin Dougiamas

Moodle Configuration with Dual login
-------------------------------------------------------------------------------
1. Ensure that the .htaccess file in moodle/auth/shibboleth/ is active.
   It may be possible that you have to change the configuration of your web 
   server to allow .htaccess files to override certain settings.

2. Create a Shibboleth-protected webpage outside the moodle directory. This page
   just needs to redirect the users to moodle/auth/shibboleth/ 
   In PHP this could be a page redirect/index.php which could look like:
--
<?php header("Location: ../moodle/auth/shibboleth/"); ?>
--

   This redirection page has to be Shibboleth protected. You can do this with
   another .htaccess file in directory redirect. The .htaccess file could look
   like this:
   
--
AuthType shibboleth
ShibRequireSession On
require shibboleth
--

   To restrict access to Moodle, replace the access rule 'require valid-user' 
   with something that fits your needs, e.g. 'require affiliation student'.

3. As Moodle admin, go to the 'Administrations >> Users >> Authentication 
   Options' and select the 'Shibboleth' authentication method from the pop-up.
   
4. Fill in the fields of the form. The fields 'Username', 'First name', 
   'Surname', etc should contain the name of the environment variables of the 
   Shibboleth attributes that you want to map onto the corresponding Moodle 
   variable (e.g. 'HTTP_SHIB_PERSON_SURNAME' for the person's last name, refer 
   the Shibboleth documentation or the documentation of your Shibboleth
   federation for information on which attributes are available).
   Especially the 'Username' field is of great importance because 
   this attribute is used for the Moodle authentication of Shibboleth users.
   
   #############################################################################
   Shibboleth Attributes needed by Moodle:
   For Moodle to work properly Shibboleth should at least provide the attributes
   that are used as username, firstname, lastname and email in Moodle.
   The attribute used for the username has to be unique for all Shibboleth user.
   All attributes must obey a certain length, otherwise Moodle cuts off the
   ends. Consult the Moodle documentation for further information on the maximum
   lengths for each field in the user profile.
   #############################################################################

5. The large text field 'Instructions' must contain a link to the 
   moodle/auth/shibboleth/index.php file which is protected by Shibboleth (see
   step 1) and causes the Shibboleth login procedure to start. You also can
   use some HTML elements in that field, e.g. to create your own Shibboleth
   login button.

6. Save the changes for the Shibboleth authentication method.

Moodle Configuration with Shibboleth only login
-------------------------------------------------------------------------------
If you want Shibboleth as your only authentication method, configure Moodle as
described in the dual login section above and do the following steps:

5.a  On the Moodle Shibboleth settings page, set the 'Alternate Login URL' to 
     the URL of the Shibboleth-protected webpage you created in step 2.
     This will enforce Shibboleth login

How the Shibboleth authentication works
--------------------------------------------------------------------------------
For a user to get Shibboleth authenticated in Moodle he first must go to the 
Shibboleth-protected webpage you created. When Shibboleth is the only
authentication method (see above) this happens automatically.
Otherwise the user has to click on the link on the login page you provided in 
step 5.

If the user is successfully Shibboleth authenticated he is redirected to
moodle/auth/shibboleth where he also gets authenticated in Moodle. 
Moodle basically checks whether the Shibboleth attribute that you mapped
as the username is present. This attribute is only present if a user is Shibboleth 
authenticated.

If the user's Moodle account has not existed yet, it gets automatically created.
To prevent that every Shibboleth user can access your Moodle site you have to
adapt the 'require valid-user' line in your webserver's config  (see step 2) to 
allow only specific users. 

You can use Shibboleth AND another authentication method (it was tested with 
manual login only). So if there are a few users that don't have a Shibboleth 
login, you could create manual accounts for them and they could use the manual 
login. For other authentication methods you first have to configure them and 
then set Shibboleth as your authentication method. Users can log in only via one authentication method unless they have two accounts in Moodle.

Shibboleth dual login with custom login page
--------------------------------------------------------------------------------
Of course you can create a dual login page that better fits your needs. For this 
to work you have to set up the two authentication methods (e.g. 'Manual' and 
'Shibboleth', Shibboleth has to be the current authentication method) and 
specify an alternate login link to your own dual login page. On that page you 
basically need a link to the Shibboleth-protected redirection page for the 
Shibboleth login and a form that sends 'username' and 'password' to 
moodle/login/index.php.
Consult the Moodle documentation for further instructions and requirements.

Bugs
--------------------------------------------------------------------------------
The current implementation has not yet been extensively tested. So there may be
bugs. Please send bug reports concerning the Shibboleth part to 
Lukas Haemmerle <haemmerle@switch.ch>

So far there is one bug known concerning Shibboleth although it's not a bug 
caused by the Shibboleth authentication but a general bug.

- If certain user profile fields are locked, users may not be able to update
their user profile at all because Moodle complains that certain locked values
were tried to change. This bug has to do with the disabling of the locked form
fields and will hopefully somewhen get fixed.

--------------------------------------------------------------------------------
In case of problems and questions with Shibboleth authentication, contact 
Lukas Haemmerle <haemmerle@switch.ch> or Markus Hagman <hagman@hytti.uku.fi>
