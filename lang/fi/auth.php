<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 development (2004050200)


$string['auth_dbdescription'] = 'Tämä moduli tarkistaa ulkoisen tietokannan taulusta käyttäjätunnuksen ja salasanan. Jos käyttäjätunnus on uusi, myös muita tietoja voidaan kopioda Moodleen.';
$string['auth_dbextrafields'] = 'Nämä kentät ovat valinnaisia. Voit asettaa Moodlen hakemaan valmiiksi joitakin käyttäjätietoja <b>ulkoisesta tietokannasta</b>.<p>Jos jätät nämä kentät tyhjiksi, käytetään oletusasetusarvoja.</p> Käyttäjä voi joka tapauksessa muuttaa omia henkilötietojaan myöhemmin.';
$string['auth_dbfieldpass'] = 'Salasanakentännimi';
$string['auth_dbfielduser'] = 'Käyttäjätunnuskentän nimi';
$string['auth_dbhost'] = 'Tietokantapalvelin';
$string['auth_dbname'] = 'Tietokannan nimi';
$string['auth_dbpass'] = 'Salasana käyttäjätunnukselle';
$string['auth_dbpasstype'] = 'Määritä salasanakentän käyttämä muoto. MD5-salaus on hyödyllinen, jos haluat käyttää muita web-sovelluksia kuten PostNukea.';
$string['auth_dbtable'] = 'Tietokannan taulun nimi';
$string['auth_dbtitle'] = 'Käytä ulkoista tietokantaa';
$string['auth_dbtype'] = 'Tietokannan tyyppi (Katso <A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentoinnista</A> yksityiskohdat)';
$string['auth_dbuser'] = 'Käyttäjätunnus tietokantaan lukuoikeuksin';
$string['auth_emaildescription'] = 'Sähköpostivarmistus on oletusarvoinen tunnistusmetodi käyttäjälle.
Kun käyttäjä luo itselleen tunnuksen, lähetetään varmistusviesti
käyttäjälle. Viesti sisältää linkin, minkä avulla käyttäjä voi aktivoida tunnuksensa.';
$string['auth_emailtitle'] = 'Käytä sähköpostivarmistusta';
$string['auth_imapdescription'] = 'Tämä tapa käyttää IMAP-palvelinta käyttäjätunnuksen ja salasanan tarkistamiseen.';
$string['auth_imaphost'] = 'IMAP-palvelimen osoite. Käytä IP-numeroa, älä domainnimeä.';
$string['auth_imapport'] = 'IMAP-palvelimen portti, yleensä 143 tai 993.';
$string['auth_imaptitle'] = 'Käytä IMAP-palvelinta';
$string['auth_imaptype'] = 'IMAP-palvelimen tyyppi. Katso ohjeesta (yllä) lisätietoja.';
$string['auth_ldap_bind_dn'] = 'Jos haluat käyttää välityskäyttäjää yhteyden muodostamiseen, määritä se tähän. Esim. \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Salasana välityskäyttäjälle.';
$string['auth_ldap_contexts'] = 'Lista konteksteista, missä käyttäjät sijaitsevat. Erota kontekstit toisistaan \';\'-merkillä. Esim: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Jos luodaan sähköpostiviestillä tunnuksensa varmentaneet käyttäjät automaattisesti ldap-hakemistoon, määritä tässä konteksti, minne käyttäjät luodaan. On hyvä käyttää jotain eritystä kontekstia, jotta vältyt tietoturvariskeiltä. Tätä kontekstia ei tarvitse erikseen lisätä yllä olevaan muuttujaan.';
$string['auth_ldap_creators'] = 'Lista ryhmistä, minkä jäsenet voivat luoda uusia kursseja Moodleen. Erota useat ryhmät toisistaan \';\'-merkillä. Esimerkiksi \'cn=teachers,ou=staff,o=myorg;\'';
$string['auth_ldap_host_url'] = 'Määritä LDAP-palvelin URL-muodossa. Esim. \'ldap://ldap.myorg.com/\' tai \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Määritä käyttäjän ryhmäjäsenyysattribuutti. Yleensä \'member\' tai \'groupMembership\' ';
$string['auth_ldap_search_sub'] = 'Aseta arvo &lt;&gt; 0, jos haluat hakea käyttäjiä myös alikonteksteista.';
$string['auth_ldap_update_userinfo'] = 'Päivitä käyttäjätiedot LDAP:ista Moodleen (etunimi, sukunimi, osoite..). Katso <a href=\"/auth/ldap/attr_mappings.php\">/auth/ldap/attr_mappings.php</a> tarkempia määrittelytietoja.';
$string['auth_ldap_user_attribute'] = 'Attribuutti käyttäjänimille. Yleensä \'cn\'.';
$string['auth_ldap_version'] = 'Palvelimella käytettävä LDAP protokolla versio';
$string['auth_ldapdescription'] = 'Tämä tapa tarjoaa käyttäjätunnistuksen LDAP-palvelimelta. Jos salasana ja tunnus täsmäävät, Moodle luo uuden käyttäjän  tietokantaansa. Jos olet valinnut \'auth_ldap_update_userinfo\'-option, myös käyttäjätiedot kopioidaan LDAP:sta Moodleen.

Seuraavilla kerroilla ainostaan tunnus ja salasana tarkistetaan.';
$string['auth_ldapextrafields'] = 'Nämä kentät ovat valinnaisia. Voit asettaa Moodlen hakemaan käyttäjätietoja tässä määritellyistä <b>LDAP-kentistä</b>. Mikäli jätät nämä tyhjiksi, mitään tietoja ei haeta LDAP-palvelimelta ja käytetään Moodlen oletusarvoja.
<p>Käyttäjä voi joka tapauksessa muuttaa omia henkilötietojaan jälkeenpäin.</p>';
$string['auth_ldaptitle'] = 'Käytä LDAP-palvelinta';
$string['auth_manualdescription'] = 'Käyttäjät eivät voi itse luoda omia tunnuksiaan. Pääkäyttäjien pitää luoda kaikki käyttäjät käsin.';
$string['auth_manualtitle'] = 'Käsinluonti';
$string['auth_multiplehosts'] = 'Voit määritellä useita osoitteita ( joku.jossain.com;joku.toinen.com;... )';
$string['auth_nntpdescription'] = 'Tämä tapa käyttää NNTP-palvelinta käyttäjän tunnistukseen.';
$string['auth_nntphost'] = 'NNTP-palvelimen osoite. Käytä IP-numeroa, älä domainnimeä.';
$string['auth_nntpport'] = 'NNTP-palvelimen portti (yleensä 119)';
$string['auth_nntptitle'] = 'Käytä NNTP-palvelinta';
$string['auth_nonedescription'] = 'Käyttäjät voivat luoda vapaasti uuden tunnuksen ilman sähköpostivarmistusta. 
Jos käytät tätä tapaa, mieti, mitä tietoturva- tai ylläpito-ongelmia tämä voi aiheuttaa.';
$string['auth_nonetitle'] = 'Ei tunnistusta';
$string['auth_pop3description'] = 'Tämä tapa käyttää POP3-palvelinta käyttäjän tunnistukseen.';
$string['auth_pop3host'] = 'POP3-palvelimen osoite. Käytä IP-numeroa, älä domainnimeä.';
$string['auth_pop3port'] = 'POP3-palvelimen portti (yleensä 110 )';
$string['auth_pop3title'] = 'Käytä POP3-palvelinta';
$string['auth_pop3type'] = 'Palvelimen tyyppi. Jos käytätte salattua yhteyttä, valitse pop3cert.';
$string['auth_user_create'] = 'Käyttäjän luonti';
$string['auth_user_creation'] = 'Käyttäjät voivat itse luoda tunnuksensa. Käyttäjätiedot tarkistetaan sähköpostin avulla. Jos aktivoit tämän vaihtoehdon, muista myös määritellä autentikointi-modulin muut tähän liittyvät asetukset.';
$string['auth_usernameexists'] = 'Käyttäjätunnus on jo käytössä. Valitse joku toinen.';
$string['authenticationoptions'] = 'Käyttäjätunnistuksen asetukset';
$string['authinstructions'] = 'Tähän voi kirjoittaa ohjeet opiskelijoille, mitä tunnusta ja salasanaa heidän tulisi käyttää. Tämä teksti näkyy kirjautumissivulla.';
$string['changepassword'] = 'Salasananvaihto-URL';
$string['changepasswordhelp'] = 'Tässä osoitteessa käyttäjät voivat vaihtaa unohtamansa salasanan. Käyttäjille tämä näkyy painikkeena kirjautumissivulla ja heidän käyttäjätietosivullaan.';
$string['chooseauthmethod'] = 'Valitse käyttäjäntunnistusmetodi: ';
$string['guestloginbutton'] = 'Kirjaudu vieraana-painike';
$string['instructions'] = 'Ohjeet';
$string['md5'] = 'MD5-salaus';
$string['plaintext'] = 'Selväkielinen teksti';
$string['showguestlogin'] = 'Voit näyttää tai piilottaa Kirjaudu vieraana-painikkeen kirjautumissivulla.';

?>
