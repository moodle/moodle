<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2004112200)


$string['description'] = '<p>Je kunt een LDAP-server gebruiken om je vakaanmeldingen te controleren. Er wordt vanuit gegaan dat je LDAP-structuur groepen bevat die verwijzen naar de vakken en dat elk van die groepen/vakken naar lidmaatschap van leerlingen verwijzen.</p>
<p>Er wordt vanuit gegaan dat vakken als groepen gedefinieerd zijn in LDAP waarbij elke groep meerdere lidmaatschapsvelden heeft (<em>member</em> of <em>memberUid</em> die een unieke identificatie van de gebruiker bevat.</p>
<p>Om aanmeldingen met LDAP te kunnen gebruiken <strong>moeten</strong> je gebruikers een geldig idnumber-veld hebben. De LDAP-groepen moeten dat idnummer in het member-veld hebben om een gebruiker in een vak te kunnen aanmelden. Dit zal gewoonlijk goed werken als je al LDAP=authenticatie gebruikt.</p>
<p>Aanmeldingen worden geüpdatet wanneer de gebruiker inlogd. Je kunt ook een script laten lopen om de aanmeldingen te synchroniseren. Kijk daarvoor in <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>Deze plugin kan zo ingesteld worden dat nieuwe vakken aangemaakt worden als nieuwe groepen in LDAP verschijnen.</p>';
$string['enrol_ldap_autocreate'] = 'Vakken kunnen automatisch aangemaakt worden als er aanmeldingen zijn bij een vak dat in Moodle nog niet bestaat.';
$string['enrol_ldap_autocreation_settings'] = 'Instellingen voor het automatisch aanmaken van vakken.';
$string['enrol_ldap_bind_dn'] = 'Als je bind-user wil gebruikern om gebruikers te zoeken, dan moet je dat hier specifiëren. Bijvoorbeeld  \'cn=ldapuser,ou=public,o=org\' ';
$string['enrol_ldap_bind_pw'] = 'Wachtwoord voor bind-user';
$string['enrol_ldap_category'] = 'De categorie voor automatisch gemaakte vakken';
$string['enrol_ldap_course_fullname'] = 'Optioneel: LDAP-veld waaruit de volledige naam gehaald moet worden.';
$string['enrol_ldap_course_idnumber'] = 'Pad naar de unique identifier in LDAP, gewoonlijk  <em>cn</em> of <em>uid</em>. Het is aangewezen de waarde vast te zetten als je automatisch aanmaken van vakken gebruikt.';
$string['enrol_ldap_course_settings'] = 'Instellingen voor het aanmelden bij vakken';
$string['enrol_ldap_course_shortname'] = 'Optioneel: LDAP-veld om de korte vaknaam uit te halen';
$string['enrol_ldap_course_summary'] = 'Optioneel: LDAP-veld om de samenvatting uit te halen';
$string['enrol_ldap_host_url'] = 'Specifier de LDAP-host als een URL, bijvoorbeeld
\'ldap://ldap.myorg.com/\'
of \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'objectClass gebruikt om vakken te zoeken. Gewoonlijk  \'posixGroup\'. ';
$string['enrol_ldap_server_settings'] = 'LDAP-serverinstellingen';

?>
