<?PHP // $Id$ 
      // auth.php - created with Moodle 1.2 development (2004010800)


$string['auth_dbdescription'] = '&#51060; &#48169;&#48277;&#51008; &#50808;&#48512;&#51032; &#45936;&#51060;&#53552;&#48288;&#51060;&#49828;&#47484; &#44396;&#52629;&#51004;&#47196; &#54616;&#50668; &#49324;&#50857;&#51088;&#51032; &#51060;&#47492;&#44284; &#48708;&#48128;&#48264;&#54840;&#47484; &#54869;&#51064; &#54633;&#45768;&#45796;. &#47564;&#50557; &#49352;&#47196; &#44032;&#51077;&#54620; &#44228;&#51221;&#51060;&#46972;&#47732; &#50668;&#47084; &#54637;&#47785;(fields)&#51032; &#51221;&#48372;&#44032; Moodle&#49324;&#51032; &#45936;&#51060;&#53552;&#48288;&#51060;&#49828;&#47196; &#48373;&#49324;&#46104;&#50612; &#51656; &#49688; &#51080;&#49845;&#45768;&#45796;. 
';
$string['auth_dbextrafields'] = 'These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <B>external database fields</B> that you specify here. <P>If you leave these blank, then defaults will be used.<P>In either case, the user will be able to edit all of these fields after they log in.';
$string['auth_dbfieldpass'] = '&#51060;&#47492;&#51060; &#51080;&#45716; &#54637;&#47785;&#50644; &#48708;&#48128;&#48264;&#54840;&#47484; &#54252;&#54632;&#54616;&#44256; &#51080;&#49845;&#45768;&#45796;.';
$string['auth_dbfielduser'] = '&#51060;&#47492;&#51060; &#51080;&#45716; &#54637;&#47785;&#50644; &#49324;&#50857;&#51088;&#51032; &#51060;&#47492;&#51012; &#54252;&#54632;&#54616;&#44256; &#51080;&#49845;&#45768;&#45796;. ';
$string['auth_dbhost'] = '&#54840;&#49828;&#53944; &#52980;&#54504;&#53552; &#45936;&#51060;&#53552; &#49436;&#48260;';
$string['auth_dbname'] = '&#45936;&#51060;&#53552;&#48288;&#51060;&#49828;&#51032; &#51060;&#47492;';
$string['auth_dbpass'] = '&#48708;&#48128;&#48264;&#54840;&#45716; &#50526;&#50640; &#51080;&#45716; &#49324;&#50857;&#51088; &#49457;&#47749;&#44284; &#50672;&#44288;&#46104;&#50612;&#51080;&#49845;&#45768;&#45796;';
$string['auth_dbpasstype'] = '&#48708;&#48128;&#48264;&#54840; &#54637;&#47785;&#51060; &#51060;&#50857;&#46120;&#51012; &#54252;&#47607;(&#54805;&#49885;)&#51004;&#47196; &#47749;&#44592; &#54633;&#45768;&#45796;. MD5&#54805;&#49885;&#51032; &#50516;&#54840;&#54868;&#45716; PostNuke&#50752; &#44057;&#51008; &#51064;&#53552;&#45367;&#49324;&#51032; &#50629;&#47924; &#50672;&#44208;&#50640; &#50976;&#50857;&#54633;&#45768;&#45796;.
';
$string['auth_dbtable'] = '&#45936;&#51060;&#53552;&#48288;&#51060;&#49828;&#50504;&#51032; &#47785;&#47197;&#51032; &#51060;&#47492;';
$string['auth_dbtitle'] = '&#50808;&#48512;&#51032; &#45936;&#51060;&#53552;&#48288;&#51060;&#49828;&#47484; &#49324;&#50857;&#54633;&#45768;&#45796;';
$string['auth_dbtype'] = '&#45936;&#51060;&#53552;&#48288;&#51060;&#49828; &#51333;&#47448; (&#49464;&#48512;&#51201;&#51064; &#44163;&#51012; &#50896;&#54616;&#49884;&#47732;  <A HREF=../lib/adodb/readme.htm#drivers>ADOdb &#47928;&#49436;</A> &#47484; &#48372;&#49464;&#50836;)';
$string['auth_dbuser'] = '&#45936;&#51060;&#53552;&#48288;&#51060;&#49828;&#47196; &#44032;&#44592;&#50948;&#54620; &#49324;&#50857;&#51088;&#51032; &#51060;&#47492;&#44284; &#54032;&#46021;';
$string['auth_emaildescription'] = '&#51060;&#47700;&#51068; &#54869;&#51064;&#51008; &#51077;&#51613;&#46108; &#48169;&#48277;&#51032; &#46356;&#54260;&#53944;&#44050; &#51077;&#45768;&#45796;. &#44032;&#51077;&#51088;&#44032; ID&#50752; &#48708;&#48128;&#48264;&#54840;&#47484; &#51077;&#47141;&#54616;&#50668; &#49324;&#51060;&#53944;&#50640; &#44032;&#51077;&#54664;&#51012; &#49884; &#44536;&#46308;&#51060; &#51077;&#47141;&#54620; &#51060;&#47700;&#51068; &#51452;&#49548;&#47196; &#44032;&#51077;&#54869;&#51064; &#47700;&#51068;&#51012; &#48372;&#45253;&#45768;&#45796;. &#44032;&#51077;&#51088;&#50640;&#44172; &#48372;&#45236;&#51652; &#51060;&#47700;&#51068;&#50644; &#44032;&#51077;&#51088;&#44032; &#44536;&#46308;&#51032; &#44228;&#51221;&#51012; &#54869;&#51064; &#54624; &#49688; &#51080;&#44172;&#45140; &#50672;&#44208;&#54644;&#51452;&#45716; &#51452;&#49548;(&#48372;&#50504;&#46104;&#50612;&#51080;&#51020;)&#44032; &#47553;&#53356;&#44032; &#54364;&#49884;&#46104;&#50612; &#51080;&#49845;&#45768;&#45796;. &#44032;&#51077;&#51088;&#45716; &#54980;&#50640; &#47553;&#53356;&#46108; &#51452;&#49548;&#47196; &#44032;&#51077;&#51088;&#45716; Moodle&#49324; &#45936;&#51060;&#53552;&#48288;&#51060;&#49828;&#50640; &#51200;&#51109;&#46108; &#44032;&#51077;&#51088;&#51032; ID&#50752; &#48708;&#48128;&#48264;&#54840;&#47484; &#54869;&#51064;&#51012; &#50948;&#54644; &#51217;&#49549;&#54644;&#50556; &#54633;&#45768;&#45796;. 

';
$string['auth_emailtitle'] = '&#51060;&#47700;&#51068;&#50640; &#44592;&#48152;&#51012;&#46164; &#51077;&#51613;';
$string['auth_imapdescription'] = '&#51060; &#48169;&#48277;&#51008; IMAP &#49436;&#48260;&#47484; &#49324;&#50857;&#54616;&#50668; &#49324;&#50857;&#51088;&#51032; &#51060;&#47492;&#44284; &#48708;&#48128;&#48264;&#54840;&#47484; &#54869;&#51064;&#54633;&#45768;&#45796;.';
$string['auth_imaphost'] = 'IMAP &#49436;&#48260;&#51032; &#51452;&#49548;. DNS &#51060;&#47492;&#51012; &#49324;&#50857;&#54616;&#51648; &#50506;&#44256; IP&#51452;&#49548;&#47484; &#49324;&#50857;&#54633;&#45768;&#45796;.
';
$string['auth_imapport'] = 'IMAP &#49436;&#48260;&#51032; &#54252;&#53944; &#49707;&#51088;&#47484; &#45208;&#53440;&#45253;&#45768;&#45796;. &#48372;&#53685; &#51060; &#49707;&#51088;&#45716; 143&#51060;&#44144;&#45208; 993&#51077;&#45768;&#45796;. 
';
$string['auth_imaptitle'] = 'IMAP &#49436;&#48260;&#47484; &#49324;&#50857;&#54633;&#45768;&#45796;. ';
$string['auth_imaptype'] = 'IMAP &#49436;&#48260;&#51032; &#50976;&#54805;. IMAP &#49436;&#48260;&#45716; &#45796;&#47480; &#50976;&#54805;&#51032; &#51064;&#51613;&#51060;&#45208; &#49888;&#50857;&#51012; &#49324;&#50857; &#54624; &#49688; &#51080;&#49845;&#45768;&#45796;.
';
$string['auth_ldap_bind_dn'] = '&#47564;&#50557; &#45817;&#49888;&#51060; &#49324;&#50857;&#51088;&#47484; &#52286;&#44592;&#50948;&#54644; bind user&#47484; &#49324;&#50857;&#54616;&#44600; &#50896;&#54620;&#45796;&#47732; &#50668;&#44592;&#50640; &#47749;&#44592; &#54616;&#49884;&#50836;. &#50696;&#47484; &#46308;&#50612; &#51060;&#47088; &#44163; &#46308;&#51060; &#51080;&#49845;&#45768;&#45796;.\'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Bind user&#47484; &#50948;&#54620; &#48708;&#48128;&#48264;&#54840;';
$string['auth_ldap_contexts'] = 'List of contexts where users are located. Separate different contexts with \';\'. For example: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'If you enable user creation with email confirmation, specify the context where users are created. This context should be different from other users to prevent security issues. You don\'t need to add this context to ldap_context-variable, Moodle will search for users from this context automatically.';
$string['auth_ldap_creators'] = 'List of groups whose members are allowed to create new courses. Separate multiple groups with \';\'. Usually something like \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Specify LDAP host in URL-form like \'ldap://ldap.myorg.com/\' or \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Specify user member attribute, when users belongs to a group. Usually \'member\'';
$string['auth_ldap_search_sub'] = 'Put value &lt;&gt; 0 if  you like to search users from subcontexts.';
$string['auth_ldap_update_userinfo'] = 'LDAP&#50640;&#49436; Mooddle&#47196; &#49324;&#50857;&#51088;&#51032; &#51221;&#48372;&#47484; &#44081;&#49888;&#54620;&#45796;.(&#49457;, &#51060;&#47492;, &#51452;&#49548; &#46321;.)  /auth/ldap/attr_mappings.php&#51012; &#51221;&#48372;&#54868; &#51648;&#46020;&#51228;&#51089;&#51012; &#50948;&#54644; &#48372;&#50500;&#50556; &#54620;&#45796;. ';
$string['auth_ldap_user_attribute'] = 'The attribute used to name/search users. Usually \'cn\'.';
$string['auth_ldapdescription'] = 'This method provides authentication against an external LDAP server.
                                  If the given username and password are valid, Moodle creates a new user 
                                  entry in its database. This module can read user attributes from LDAP and prefill 
                                  wanted fields in Moodle.  For following logins only the username and 
                                  password are checked.';
$string['auth_ldapextrafields'] = 'These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <B>LDAP fields</B> that you specify here. <P>If you leave these fields blank, then nothing will be transferred from LDAP and Moodle defaults will be used instead.<P>In either case, the user will be able to edit all of these fields after they log in.';
$string['auth_ldaptitle'] = 'LDAP &#49436;&#48260;&#47484; &#49324;&#50857;&#54620;&#45796;.';
$string['auth_manualdescription'] = '&#51060; &#48169;&#48277;&#51008; &#49324;&#50857;&#51088;&#46308;&#51032; &#44228;&#51221;&#51012; &#47564;&#46308;&#44592; &#50948;&#54644;  &#44600;&#51012; &#51228;&#44144;&#54620;&#45796;. &#47784;&#46304; &#44228;&#51221;&#51008; &#50868;&#50689;&#51088;&#50640; &#51032;&#54644; &#47564;&#46308;&#50612; &#51256;&#50556; &#54620;&#45796;. 
';
$string['auth_manualtitle'] = '&#51649;&#51217;&#47564;&#46304; &#44228;&#51221;&#47564; &#54728;&#46973;&#51060; &#46121;&#45768;&#45796;';
$string['auth_nntpdescription'] = '&#51060; &#48169;&#48277;&#51008; NNPP &#49436;&#48260;&#47484; &#51060;&#50857;&#54616;&#50668; &#49324;&#50857;&#51088;&#51032; &#51060;&#47492;&#44284; &#48708;&#48128;&#48264;&#54840;&#47484; &#54869;&#51064;&#54633;&#45768;&#45796;.';
$string['auth_nntphost'] = 'NNPP &#49436;&#48260;&#51032; &#51452;&#49548;. DNS &#51060;&#47492;&#51060; &#50500;&#45772; IP &#49707;&#51088;&#47484; &#49324;&#50857;&#54620;&#45796;. 
';
$string['auth_nntpport'] = 'Server port (119 is the most common)';
$string['auth_nntptitle'] = 'NNPP &#49436;&#48260;&#47484; &#49324;&#50857;&#54620;&#45796;. ';
$string['auth_nonedescription'] = 'Users can sign in and create valid accounts immediately, with no authentication against an external server and no confirmation via email.  Be careful using this option - think of the security and administration problems this could cause.';
$string['auth_nonetitle'] = '&#48520; &#51064;&#51613;';
$string['auth_pop3description'] = 'This method uses a POP3 server to check whether a given username and password is valid.';
$string['auth_pop3host'] = 'The POP3 server address. Use the IP number, not DNS name.';
$string['auth_pop3port'] = 'Server port (110 is the most common)';
$string['auth_pop3title'] = 'POP3 &#49436;&#48260;&#47484; &#49324;&#50857;&#54620;&#45796;. ';
$string['auth_pop3type'] = '&#49436;&#48260;&#51032; &#54805;&#49885;. &#47564;&#50557; &#45817;&#49888;&#51032; &#49436;&#48260;&#44032; &#51613;&#47732;&#46108; &#48372;&#50504;&#51012; &#49324;&#50857;&#54620;&#45796;&#47732; POP3&#47196; &#51613;&#47749;&#46108; &#44163;&#51012; &#49440;&#53469;&#54616;&#49884;&#50724;. 
';
$string['auth_user_create'] = '&#47564;&#46308; &#49688; &#51080;&#45716; &#49324;&#50857;&#51088; ';
$string['auth_user_creation'] = 'New (anonymous) users can create user accounts on the external authentication source and confirmed via email. If you enable this , remember to also configure module-specific options for user creation.';
$string['auth_usernameexists'] = 'Selected username already exists. Please choose a new one.';
$string['authenticationoptions'] = '&#51064;&#51613; &#50741;&#49496;&#46308;';
$string['authinstructions'] = 'Here you can provide instructions for your users, so they know which username and password they should be using.  The text you enter here will appear on the login page.  If you leave this blank then no instructions will be printed.';
$string['changepassword'] = '&#48708;&#48128;&#48264;&#54840;&#47484; &#48148;&#45001;&#45768;&#45796;.';
$string['changepasswordhelp'] = 'Here you can specify a location at which your users can recover or change their username/password if they\'ve forgotten it.  This will be provided to users as a button on the login page and their user page.  if you leave this blank the button will not be printed.';
$string['chooseauthmethod'] = '&#51077;&#51613;&#46108; &#48169;&#48277;&#51012; &#49440;&#53469;&#54620;&#45796;. ';
$string['guestloginbutton'] = '&#48169;&#47928;&#44061;&#51032; &#47196;&#44536;&#51064; &#48260;&#53948;';
$string['instructions'] = '&#49444;&#47749;';
$string['md5'] = 'MD5 &#54805;&#49885;&#51032; &#50516;&#54840;&#54868;';
$string['plaintext'] = 'Plain text';
$string['showguestlogin'] = '&#45817;&#49888;&#51008; &#47196;&#44536;&#51064; &#54168;&#51060;&#51648;&#50640; &#48169;&#47928;&#51088; &#47196;&#44536;&#51064; &#48260;&#53948;&#51012; &#49704;&#44592;&#44144;&#45208; &#48372;&#51060;&#44172; &#54624; &#49688; &#51080;&#49845;&#45768;&#45796;. 
';

?>
