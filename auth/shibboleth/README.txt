Shibboleth Authentication for Moodle
-------------------------------------------------------------------------------

Requirements:
- Shibboleth target 1.1 or later. See documentation for your Shibboleth
  federation on how to set up Shibboleth.

Changes:
- 11. 2004: Created by Markus Hagman
- 05. 2005: Modifications to login process by Martin Dougiamas
- 05. 2005: Various extensions and fixes by Lukas Haemmerle
- 06. 2005: Adaptions to new field locks and plugin config structures by Martin
            Langhoff and Lukas Haemmerle
- 10. 2005: Added better error messages and moved text to language directories
- 02. 2006: Simplified authentication so that authorization works properly
            Added instructions for IIS
- 11. 2006: User capabilities are now loaded properly as of Moodle 1.7+
- 03. 2007: Adapted authentication method to Moodle 1.8
- 07. 2007: Fixed a but that caused problems with uppercase usernames
- 10. 2007: Removed the requirement for email address, surname and given name
            attributes on request of Markus Hagman
- 11. 2007: Integrated WAYF Service in Moodle

Moodle Configuration with Dual login
-------------------------------------------------------------------------------
1. Protect the directory moodle/auth/shibboleth/index.php with Shibboleth.
   The page index.php in that directory actually logs in a Shibboleth user.
   For Apache you have to define a rule like the following in the Apache config:

--
<Location ~  "/auth/shibboleth/index.php">
        AuthType shibboleth
        ShibRequireSession On
        require valid-user
</Location>
--

   To restrict access to Moodle, replace the access rule 'require valid-user'
   with something that fits your needs, e.g. 'require affiliation student'.

   For IIS you have protect the auth/shibboleth directory directly in the
   RequestMap of the Shibboleth configuration file (shibboleth.xml). See
   
   https://spaces.internet2.edu/display/SHIB/xmlaccesscontrol?topic=XMLAccessControl

2. As Moodle admin, go to the 'Administrations >> Users >> Authentication
   Options' and click on the the 'Shibboleth' settings.

3. Fill in the fields of the form. The fields 'Username', 'First name',
   'Surname', etc. should contain the name of the environment variables of the
   Shibboleth attributes that you want to map onto the corresponding Moodle
   variable (e.g. 'HTTP_SHIB_PERSON_SURNAME' for the person's last name, refer
   the Shibboleth documentation or the documentation of your Shibboleth
   federation for information on which attributes are available).
   Especially the 'Username' field is of great importance because
   this attribute is used for the Moodle authentication of Shibboleth users.

   #############################################################################
   Shibboleth Attributes needed by Moodle:
   For Moodle to work properly Shibboleth should at least provide the attribute
   that is used as username in Moodle. It has to be unique for all Shibboleth 
   Be aware that Moodle converts the username to lowercase. So, the overall
   behaviour of the username will be case-insensitive.
   All attributes used for moodle must obey a certain length, otherwise Moodle 
   cuts off the ends. Consult the Moodle documentation for further information 
   on the maximum lengths for each field in the user profile.
   #############################################################################

4.a  If you want Shibboleth as your only authentication method with an external
     Where Are You From (WAYF) Service , set the 'Alternate Login URL' in the 
     'Common settings' in 'Administrations >> Users >> Authentication Options' 
     to the the URL of the file 'moodle/auth/shibboleth/index.php'. 
     This will enforce Shibboleth login.

4.b If you want to use the Moodle internal WAYF service, you have to activate it
    in the Moodle Shibboleth authentication settings by checking the 
    'Moodle WAYF Service' checkbox and providing a list of entity IDs in the 
    'Identity Providers' textarea together with a name and an optional 
    SessionInitiator URL, which usually is an absolute or relative URL pointing 
    to the same host. If no SessionInitiator URL is given, the default one 
    '/Shibboleth.sso' will be used.
    Also see https://spaces.internet2.edu/display/SHIB/SessionInitiator

    Important Note: If you upgraded from a previous version of Moodle and now
                    want to use the integrated WAYF, you have to make sure that
                    in step 1 only the index.php script in 
                    moodle/auth/shibboleth/ is protected but *not* the other 
                    scripts and especially not the login.php script.

5.  Save the changes for the 'Shibboleth settings'. T

    Important Note: If you went for 4.b (integrated WAYF service), saving the 
                    settings will overwrite the Moodle Alternate Login URL
                    using the Moodle web root URL.

6.  If you want to use Shibboleth in addition to another authentication method
    not using the integrated WAYF service from 4.b, change the 'Instructions' in 
    'Administrations >> Users >> Manage authentication' to contain a link to the
     moodle/auth/shibboleth/index.php file which is protected by
     Shibboleth (see step 1.) and causes the Shibboleth login procedure to start.
     You can also use HTML code in that field, e.g. to include an image as a
     Shibboleth login button.

     Note: As of now you cannot use dual login together with the integrated
           WAYF service provided by Moodle (4.b).

7. Save the authentication changes.

How the Shibboleth authentication works
--------------------------------------------------------------------------------
To get Shibboleth authenticated in Moodle a user basically must access the
Shibboleth-protected page /auth/shibboleth/index.php. If Shibboleth is the only
authentication method (see 4.a), this happens automatically when a user selects
his home organization in the Moodle WAYF service or if the alternate login URL
is configured to be the protected /auth/shibboleth/index.php
Otherwise, the user has to click on the link on the dual login page you 
provided in step 5.b.

Moodle basically checks whether the Shibboleth attribute that you mapped
as the username is present. This attribute should only be present if a user is
Shibboleth authenticated.

If the user's Moodle account has not existed yet, it gets automatically created.

To prevent that every Shibboleth user can access your Moodle site you have to
adapt the 'require valid-user' line in your webserver's config  (see step 1) to
allow only specific users. If you defined some authorization rules in step 1,
these are checked by Shibboleth itself. Only users who met these rules
actually can access /auth/shibboleth/index.php and get logged in.

You can use Shibboleth AND another authentication method (it was tested with
manual login). So, if there are a few users that don't have a Shibboleth
login, you could create manual accounts for them and they could use the manual
login. For other authentication methods you first have to configure them and
then set Shibboleth as your authentication method. Users can log in only via one
authentication method unless they have two accounts in Moodle.

Shibboleth dual login with custom login page
--------------------------------------------------------------------------------
You can create a dual login page that better fits your needs. For this
to work, you have to set up the two authentication methods (e.g. 'Manual 
Accounts' and 'Shibboleth') and specify an alternate login link to your own dual
login page. On that page you basically need a link to the Shibboleth-protected
page ('/auth/shibboleth/index.php') for the Shibboleth login and a
form that sends 'username' and 'password' to moodle/login/index.php. Set this 
web page then als alternate login page.
Consult the Moodle documentation for further instructions and requirements.

How to customize the way the Shibboleth user data is used in Moodle
--------------------------------------------------------------------------------
Among the Shibboleth settings in Moodle there is a field that should contain a
path to a php file that can be used as data manipulation hook.
You can use this if you want to further process the way your Shibboleth
attributes are used in Moodle.

Example 1: Your Shibboleth federation uses an attribute that specifies the
           user's preferred language, but the content of this attribute is not
           compatible with the Moodle data representation, e.g. the Shibboleth
           attribute contains 'German' but Moodle needs a two letter value like
           'de'.
Example 2: The country, city and street are provided in one Shibboleth attribute
           and you want these values to be used in the Moodle user profile. So
           You have to parse the corresponding attribute to fill the user fields.

If you want to use this hook you have to be a skilled PHP programmer. It is
strongly recommended that you take a look at the file
moodle/auth/shibboleth/auth.php, especially the function 'get_userinfo'
where this file is included.
The context of the file is the same as within this login function. So you
can directly edit the object $result.

Example file:

--
<?PHP

    // Set the zip code and the adress
    if ($_SERVER[$this->config->field_map_address] != '')
    {
        // $address contains something like 'SWITCH$Limmatquai 138$CH-8021 Zurich'
        // We want to split this up to get: 
        // institution, street, zipcode, city and country
        $address = $_SERVER[$this->config->field_map_address];
        list($institution, $street, $zip_city) = split('\$', $address);
        ereg(' (.+)',$zip_city, $regs);
        $city = $regs[1];
        
        ereg('(.+)-',$zip_city, $regs);
        $country = $regs[1];
        
        $result["address"] = $street;
        $result["city"] = $city;
        $result["country"] = $country;
        $result["department"] = $institution;
        $result["description"] = "I am a Shibboleth user";

    }

?>
--

--------------------------------------------------------------------------------
In case of problems and questions with Shibboleth authentication, contact
Lukas Haemmerle <lukas.haemmerle@switch.ch> or Markus Hagman <hagman@hytti.uku.fi>
