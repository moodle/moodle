Shibboleth Authentication for Moodle
-------------------------------------------------------------------------------

Requirements:
- Moodle 1.5 or later
- Shibboleth target 1.1 or later. See documentation for your Shibboleth 
  federation on how to set up Shibboleth.

Changes:
- 11. 2004: Created by Markus Hagman
- 05. 2005: Modifications to login process by Martin Dougiamas
- 05. 2005: Various extensions and fixes by Lukas Haemmerle
- 06. 2005: Adaptions to new field locks and plugin config structures by Marting
            Langhoff and Lukas Haemmerle
- 10. 2005: Added better error messages and moved text to language directories

Moodle Configuration with Dual login
-------------------------------------------------------------------------------
1. Ensure that the .htaccess file in moodle/auth/shibboleth/ is active
   It may be possible that you have to change the configuration of your web 
   server to allow .htaccess files to override certain settings. Alternatively,
   you also could define the rules from the .htaccess file in the web server
   configuration file.

2. Protect the file moodle/auth/shibboleth/shib-protected.php with Shibboleth.
   This page just needs to redirect the users to moodle/auth/shibboleth/ 
   For Apache you have to define a rule like the following:

--
<Location ~  "/auth/shibboleth/shib-protected.php">
        AuthType shibboleth
        ShibRequireSession On
        require valid-user
</Location>
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
   moodle/auth/shibboleth/shib-protected.php file which is protected by 
   Shibboleth (see step 2) and causes the Shibboleth login procedure to start. 
   You also coudl use HTML code in that field, e.g. to create your own 
   Shibboleth login button.

6. Save the changes for the Shibboleth authentication method.

Moodle Configuration with Shibboleth only login
-------------------------------------------------------------------------------
If you want Shibboleth as your only authentication method, configure Moodle as
described in the dual login section above and do the following steps:

5.a  On the Moodle Shibboleth settings page, set the 'Alternate Login URL' to 
     the URL of the file 'moodle/auth/shibboleth/shib-protected.php'
     This will enforce Shibboleth login.

How the Shibboleth authentication works
--------------------------------------------------------------------------------
For a user to get Shibboleth authenticated in Moodle he first must go to the 
Shibboleth-protected page shib-protected.php. If Shibboleth authentication is
enabled this happens automatically when a user wants to login.
Otherwise the user has to click on the link on the login page you provided in 
step 5.

If the user is successfully Shibboleth authenticated he is redirected to
moodle/auth/shibboleth where he also gets authenticated in Moodle. 
Moodle basically checks whether the Shibboleth attribute that you mapped
as the username is present. This attribute is only present if a user is 
Shibboleth authenticated.

If the user's Moodle account has not existed yet, it gets automatically created.
Unless the user's firstname, last name and email address is provided, the user 
is automatically redirected to the edit profile page by Moodle.

To prevent that every Shibboleth user can access your Moodle site you have to
adapt the 'require valid-user' line in your webserver's config  (see step 2) to 
allow only specific users. 

You can use Shibboleth AND another authentication method (it was tested with 
manual login only). So if there are a few users that don't have a Shibboleth 
login, you could create manual accounts for them and they could use the manual 
login. For other authentication methods you first have to configure them and 
then set Shibboleth as your authentication method. Users can log in only via one 
authentication method unless they have two accounts in Moodle.

Shibboleth dual login with custom login page
--------------------------------------------------------------------------------
Of course you can create a dual login page that better fits your needs. For this 
to work you have to set up the two authentication methods (e.g. 'Manual' and 
'Shibboleth', Shibboleth has to be the current authentication method) and 
specify an alternate login link to your own dual login page. On that page you 
basically need a link to the Shibboleth-protected page
('moodle/auth/shibboleth/shib-protected.php') for the Shibboleth login and a 
form that sends 'username' and 'password' to moodle/login/index.php.
Consult the Moodle documentation for further instructions and requirements.

How to customize the way the Shibboleth user data is used in ILIAS
--------------------------------------------------------------------------------
Among the Shibboleth settings in Moodle there is a field that should contain a
path to a php file that can be used as data manipulation API.
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

If you want to use this API you have to be a skilled PHP programmer. It is 
strongly recommended that you take a look at the file 
moodle/auth/shibboleth/lib.php, especially the function 'auth_get_userinfo' 
where this API file is included. 
The context of the API file is the same as within this login function. So you
can directly edit the object $result.

Example file:

--
<?PHP

    // Set the zip code and the adress
    if ($_SERVER[$pluginconfig->field_map_address] != '')
    {
        // $address contains something like 'SWITCH$Limmatquai 138$CH-8021 Zurich'
        // We want to split this up to get: 
        // institution, street, zipcode, city and country
        $address = $_SERVER[$pluginconfig->field_map_address];
        list($institution, $street, $zip_city) = split('\$', $address);
        ereg(' (.+)',$zip_city, $regs);
        $city = $regs[1];
        
        ereg('(.+)-',$zip_city, $regs);
        $country = $regs[1];
        
        $result["address"] = $street;
        $result["city"] = $city;
        $result["country"] = $country;
        $result["department"] = $institution;
    }
?>
--

Bugs
--------------------------------------------------------------------------------
Please send bug reports concerning the Shibboleth part to 
Lukas Haemmerle <haemmerle@switch.ch>

--------------------------------------------------------------------------------
In case of problems and questions with Shibboleth authentication, contact 
Lukas Haemmerle <haemmerle@switch.ch> or Markus Hagman <hagman@hytti.uku.fi>
