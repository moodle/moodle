<?PHP // $Id$ 
      // auth.php - created with Moodle 1.5 unstable development (2004092000)


$string['auth_common_settings'] = 'Yleiset asetukset';
$string['auth_data_mapping'] = 'Tietojen yhdist‰minen';
$string['auth_dbdescription'] = 'T‰m‰ moduli tarkistaa ulkoisen tietokannan taulusta k‰ytt‰j‰tunnuksen ja salasanan. Jos k‰ytt‰j‰tunnus on uusi, myˆs muita tietoja voidaan kopioda Moodleen.';
$string['auth_dbextrafields'] = 'N‰m‰ kent‰t ovat valinnaisia. Voit asettaa Moodlen hakemaan valmiiksi joitakin k‰ytt‰j‰tietoja <b>ulkoisesta tietokannasta</b>.<p>Jos j‰t‰t n‰m‰ kent‰t tyhjiksi, k‰ytet‰‰n oletusasetusarvoja.</p> K‰ytt‰j‰ voi joka tapauksessa muuttaa omia henkilˆtietojaan myˆhemmin.';
$string['auth_dbfieldpass'] = 'Salasanakent‰nnimi';
$string['auth_dbfielduser'] = 'K‰ytt‰j‰tunnuskent‰n nimi';
$string['auth_dbhost'] = 'Tietokantapalvelin';
$string['auth_dbname'] = 'Tietokannan nimi';
$string['auth_dbpass'] = 'Salasana k‰ytt‰j‰tunnukselle';
$string['auth_dbpasstype'] = 'M‰‰rit‰ salasanakent‰n k‰ytt‰m‰ muoto. MD5-salaus on hyˆdyllinen, jos haluat k‰ytt‰‰ muita web-sovelluksia kuten PostNukea.';
$string['auth_dbtable'] = 'Tietokannan taulun nimi';
$string['auth_dbtitle'] = 'K‰yt‰ ulkoista tietokantaa';
$string['auth_dbtype'] = 'Tietokannan tyyppi (Katso <A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentoinnista</A> yksityiskohdat)';
$string['auth_dbuser'] = 'K‰ytt‰j‰tunnus tietokantaan lukuoikeuksin';
$string['auth_editlock'] = 'Lukitse arvo';
$string['auth_editlock_expl'] = '<p><b>Lukitse arvo:</b> Est‰‰ tiedon muokaamisen Moodlen sis‰lt‰. </p>';
$string['auth_emaildescription'] = 'S‰hkˆpostivarmistus on oletusarvoinen tunnistusmetodi k‰ytt‰j‰lle.
Kun k‰ytt‰j‰ luo itselleen tunnuksen, l‰hetet‰‰n varmistusviesti
k‰ytt‰j‰lle. Viesti sis‰lt‰‰ linkin, mink‰ avulla k‰ytt‰j‰ voi aktivoida tunnuksensa.';
$string['auth_emailtitle'] = 'K‰yt‰ s‰hkˆpostivarmistusta';
$string['auth_fccreators'] = 'T‰m‰n ryhm‰n(ryhmien) j‰senet saavat luoda uusia kursseja. Eroittelu useat ryhm‰nimet \';\'-merkill‰. Nimet on oltava t‰ysin samoin kuin FirstClass palvelimella.';
$string['auth_imapdescription'] = 'T‰m‰ tapa k‰ytt‰‰ IMAP-palvelinta k‰ytt‰j‰tunnuksen ja salasanan tarkistamiseen.';
$string['auth_imaphost'] = 'IMAP-palvelimen osoite. K‰yt‰ IP-numeroa, ‰l‰ domainnime‰.';
$string['auth_imapport'] = 'IMAP-palvelimen portti, yleens‰ 143 tai 993.';
$string['auth_imaptitle'] = 'K‰yt‰ IMAP-palvelinta';
$string['auth_imaptype'] = 'IMAP-palvelimen tyyppi. Katso ohjeesta (yll‰) lis‰tietoja.';
$string['auth_ldap_bind_dn'] = 'Jos haluat k‰ytt‰‰ v‰litysk‰ytt‰j‰‰ yhteyden muodostamiseen, m‰‰rit‰ se t‰h‰n. Esim. \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Salasana v‰litysk‰ytt‰j‰lle.';
$string['auth_ldap_bind_settings'] = 'Sidosasetukset';
$string['auth_ldap_contexts'] = 'Lista konteksteista, miss‰ k‰ytt‰j‰t sijaitsevat. Erota kontekstit toisistaan \';\'-merkill‰. Esim: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Jos luodaan s‰hkˆpostiviestill‰ tunnuksensa varmentaneet k‰ytt‰j‰t automaattisesti ldap-hakemistoon, m‰‰rit‰ t‰ss‰ konteksti, minne k‰ytt‰j‰t luodaan. On hyv‰ k‰ytt‰‰ jotain erityst‰ kontekstia, jotta v‰ltyt tietoturvariskeilt‰. T‰t‰ kontekstia ei tarvitse erikseen lis‰t‰ yll‰ olevaan muuttujaan.';
$string['auth_ldap_creators'] = 'Lista ryhmist‰, mink‰ j‰senet voivat luoda uusia kursseja Moodleen. Erota useat ryhm‰t toisistaan \';\'-merkill‰. Esimerkiksi \'cn=teachers,ou=staff,o=myorg;\'';
$string['auth_ldap_host_url'] = 'M‰‰rit‰ LDAP-palvelin URL-muodossa. Esim. \'ldap://ldap.myorg.com/\' tai \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_login_settings'] = 'Kirjaantumisasetukset';
$string['auth_ldap_memberattribute'] = 'M‰‰rit‰ k‰ytt‰j‰n ryhm‰j‰senyysattribuutti. Yleens‰ \'member\' tai \'groupMembership\' ';
$string['auth_ldap_search_sub'] = 'Aseta arvo <> 0, jos haluat hakea k‰ytt‰ji‰ myˆs alikonteksteista.';
$string['auth_ldap_server_settings'] = 'LDAP palvelimen asetukset';
$string['auth_ldap_update_userinfo'] = 'P‰ivit‰ k‰ytt‰j‰tiedot LDAP:ista Moodleen (etunimi, sukunimi, osoite..). Katso <a href=\"/auth/ldap/attr_mappings.php\">/auth/ldap/attr_mappings.php</a> tarkempia m‰‰rittelytietoja.';
$string['auth_ldap_user_attribute'] = 'Attribuutti k‰ytt‰j‰nimille. Yleens‰ \'cn\'.';
$string['auth_ldap_user_settings'] = 'K‰ytt‰jien etsint‰';
$string['auth_ldap_version'] = 'Palvelimella k‰ytett‰v‰ LDAP protokolla versio';
$string['auth_ldapdescription'] = 'T‰m‰ tapa tarjoaa k‰ytt‰j‰tunnistuksen LDAP-palvelimelta. Jos salasana ja tunnus t‰sm‰‰v‰t, Moodle luo uuden k‰ytt‰j‰n  tietokantaansa. Jos olet valinnut \'auth_ldap_update_userinfo\'-option, myˆs k‰ytt‰j‰tiedot kopioidaan LDAP:sta Moodleen.

Seuraavilla kerroilla ainostaan tunnus ja salasana tarkistetaan.';
$string['auth_ldapextrafields'] = 'N‰m‰ kent‰t ovat valinnaisia. Voit asettaa Moodlen hakemaan k‰ytt‰j‰tietoja t‰ss‰ m‰‰ritellyist‰ <b>LDAP-kentist‰</b>. Mik‰li j‰t‰t n‰m‰ tyhjiksi, mit‰‰n tietoja ei haeta LDAP-palvelimelta ja k‰ytet‰‰n Moodlen oletusarvoja.
<p>K‰ytt‰j‰ voi joka tapauksessa muuttaa omia henkilˆtietojaan j‰lkeenp‰in.</p>';
$string['auth_ldaptitle'] = 'K‰yt‰ LDAP-palvelinta';
$string['auth_manualdescription'] = 'K‰ytt‰j‰t eiv‰t voi itse luoda omia tunnuksiaan. P‰‰k‰ytt‰jien pit‰‰ luoda kaikki k‰ytt‰j‰t k‰sin.';
$string['auth_manualtitle'] = 'K‰sinluonti';
$string['auth_multiplehosts'] = 'Voit m‰‰ritell‰ useita osoitteita ( joku.jossain.com;joku.toinen.com;... )';
$string['auth_nntpdescription'] = 'T‰m‰ tapa k‰ytt‰‰ NNTP-palvelinta k‰ytt‰j‰n tunnistukseen.';
$string['auth_nntphost'] = 'NNTP-palvelimen osoite. K‰yt‰ IP-numeroa, ‰l‰ domainnime‰.';
$string['auth_nntpport'] = 'NNTP-palvelimen portti (yleens‰ 119)';
$string['auth_nntptitle'] = 'K‰yt‰ NNTP-palvelinta';
$string['auth_nonedescription'] = 'K‰ytt‰j‰t voivat luoda vapaasti uuden tunnuksen ilman s‰hkˆpostivarmistusta. 
Jos k‰yt‰t t‰t‰ tapaa, mieti, mit‰ tietoturva- tai yll‰pito-ongelmia t‰m‰ voi aiheuttaa.';
$string['auth_nonetitle'] = 'Ei tunnistusta';
$string['auth_pop3description'] = 'T‰m‰ tapa k‰ytt‰‰ POP3-palvelinta k‰ytt‰j‰n tunnistukseen.';
$string['auth_pop3host'] = 'POP3-palvelimen osoite. K‰yt‰ IP-numeroa, ‰l‰ domainnime‰.';
$string['auth_pop3port'] = 'POP3-palvelimen portti (yleens‰ 110 )';
$string['auth_pop3title'] = 'K‰yt‰ POP3-palvelinta';
$string['auth_pop3type'] = 'Palvelimen tyyppi. Jos k‰yt‰tte salattua yhteytt‰, valitse pop3cert.';
$string['auth_updatelocal'] = 'P‰ivit‰ sis‰inen arvo';
$string['auth_updateremote'] = 'P‰ivit‰ ulkoinen arvo';
$string['auth_user_create'] = 'K‰ytt‰j‰n luonti';
$string['auth_user_creation'] = 'K‰ytt‰j‰t voivat itse luoda tunnuksensa. K‰ytt‰j‰tiedot tarkistetaan s‰hkˆpostin avulla. Jos aktivoit t‰m‰n vaihtoehdon, muista myˆs m‰‰ritell‰ autentikointi-modulin muut t‰h‰n liittyv‰t asetukset.';
$string['auth_usernameexists'] = 'K‰ytt‰j‰tunnus on jo k‰ytˆss‰. Valitse joku toinen.';
$string['authenticationoptions'] = 'K‰ytt‰j‰tunnistuksen asetukset';
$string['authinstructions'] = 'T‰h‰n voi kirjoittaa ohjeet opiskelijoille, mit‰ tunnusta ja salasanaa heid‰n tulisi k‰ytt‰‰. T‰m‰ teksti n‰kyy kirjautumissivulla.';
$string['changepassword'] = 'Salasananvaihto-URL';
$string['changepasswordhelp'] = 'T‰ss‰ osoitteessa k‰ytt‰j‰t voivat vaihtaa unohtamansa salasanan. K‰ytt‰jille t‰m‰ n‰kyy painikkeena kirjautumissivulla ja heid‰n k‰ytt‰j‰tietosivullaan.';
$string['chooseauthmethod'] = 'Valitse k‰ytt‰j‰ntunnistusmetodi: ';
$string['forcechangepassword'] = 'Pakoita salasanan vaihto';
$string['guestloginbutton'] = 'Kirjaudu vieraana-painike';
$string['instructions'] = 'Ohjeet';
$string['md5'] = 'MD5-salaus';
$string['plaintext'] = 'Selv‰kielinen teksti';
$string['showguestlogin'] = 'Voit n‰ytt‰‰ tai piilottaa Kirjaudu vieraana-painikkeen kirjautumissivulla.';

?>
