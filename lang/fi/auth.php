<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.6.4 beta (2002112001)


$string['auth_dbdescription'] = "Tämä moduli tarkistaa ulkoisen tietokannan taulusta onko käyttäjätunnuksen ja salasanan.";
$string['auth_dbfieldpass'] = "Salasana sarakeen nimi";
$string['auth_dbfielduser'] = "Käyttätunnus sarakkeen nimi";
$string['auth_dbhost'] = "Tietokanta palvelin";
$string['auth_dbname'] = "Tietokannan nimi";
$string['auth_dbpass'] = "Salasana käyttäjätunnukselle";
$string['auth_dbtable'] = "Taulun nimi";
$string['auth_dbtitle'] = "Käytä ulkoista tietokantaa";
$string['auth_dbtype'] = "Tietokannan tyyppi (Katso <A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentoinnista</A> yksityiskohdat)";
$string['auth_dbuser'] = "Käyttäjätunnus lukuoikeuksin tietokantaan";
$string['auth_emaildescription'] = "Sähköpostivarmistus on oletus käyttäjäntunnistus tapa.
Kun käyttäjä luo itseleen tunnuksen lähetetään varmistus viesti käyttäjälle. Viesti sisältää linkin jonka avulla käyttäjä voi aktivoida tunnuksensa.";
$string['auth_emailtitle'] = "Käytä sähköpostivarmistusta";
$string['auth_imapdescription'] = "Tämä tapa käyttää imap-palvelinta käyttäjätunnuksen ja salasanan tarkistamiseen.";
$string['auth_imaphost'] = "IMAP palvelimen osoite. Käytä IP-numeroa, älä domainnimeä.";
$string['auth_imapport'] = "IMAP palvelimen portti. Yleensä 143 tai 993.";
$string['auth_imaptitle'] = "Käytä IMAP palvelinta";
$string['auth_imaptype'] = "IMAP palvelimen tyyppi.  katso ohjeesta (yllä) lisätietoja.";
$string['auth_ldap_bind_dn'] = "Jos haluat käyttää välitys-käyttäjää yhteyden muodostamiseen,määriritä se tähän. Esim. 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Salasana välityskäyttäjälle.";
$string['auth_ldap_contexts'] = "Lista konteksteista joisssa käyttäjät sijaitsevat. Erota kontekstit toisistaan ';'-merkillä. Esim: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_host_url'] = "Määritä LDAP-palvelin URL-muodossa. Esim. 'ldap://ldap.myorg.com/' tai 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_search_sub'] = "Aseta arvo &lt;&gt; 0 jos haluat haka käyttäjiä myös alikonteksteista.";
$string['auth_ldap_update_userinfo'] = "Päivitä käyttäjätiedot LDAP:ista moodleen (firstname, lastname, address..) . Katso lisätietoa /auth/ldap/attr_mappings.php.";
$string['auth_ldap_user_attribute'] = "Attribuutti käyttäjänimille . Yleensä 'cn'.";
$string['auth_ldapdescription'] = "Tämä tapa tarjoaa käyttäjätunnistuksen LDAP-palvelimelta.
                  Jos salasana ja tunnus täsmäävät, moodle luo uuden käyttäjän  tietokantaansa. Jos olet valinnut 'auth_ldap_update_userinfo' option niin myös käyttäjätiedot siirretään LDAP:sta moodleen.

Seuraavilla kerroilla ainostaan tunnus ja salasana tarkistetaan.";
$string['auth_ldaptitle'] = "Käytä LDAP palvelinta";
$string['auth_nntpdescription'] = "Tämä tapa käyttää NNTP palvelinta käyttäjän tunnistukseen.";
$string['auth_nntphost'] = "NNTP palvelimen osoite. Käytä IP-numeroa, älä domainnimeä.";
$string['auth_nntpport'] = "Palvelimen portti (119 , yleensä)";
$string['auth_nntptitle'] = "Käytä NNTP palvelinta";
$string['auth_nonedescription'] = "Käyttäjät voivat luoda vapaasti uuden tunnuksen ilman sähköpostivarmistusta. 
Jos käytät tätä tapaa mieti mitä tietoturva- tai ylläpito-ongelmia tämä voi aiheuttaa.";
$string['auth_nonetitle'] = "Ei tunnistusta";
$string['auth_pop3description'] = "Tämä tapa käyttää POP3 palvelinta käyttäjän tunnistukseen.";
$string['auth_pop3host'] = "POP3 palvelimen osoite. Käytä IP-numeroa, älä domainnimeä.";
$string['auth_pop3port'] = "Palvelimen portti (110 , yleensä)";
$string['auth_pop3title'] = "Käytä POP3 palvelinta";
$string['auth_pop3type'] = "Palvelimen tyyppi. Jos käytätte salattua yhteyttä valitse pop3cert.";
$string['authenticationoptions'] = "Käyttäjäntunnistus asetukset";
$string['authinstructions'] = "Tähän voi kirjoittaa ohjeet opiskelijoille mitä tunnusta ja salasanaa heidän tulisi käyttää. Tämä teksti näkyy kirjaantumissivulla.";
$string['chooseauthmethod'] = "Valitse käyttäjäntunnistus tapa: ";
$string['showguestlogin'] = "Voit näyttää tai piiloittaa vieraskäyttäjä painikkeen kirjaantumissivulla.";

?>
