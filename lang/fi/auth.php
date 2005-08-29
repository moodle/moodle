<?PHP // $Id$ 
      // auth.php - created with Moodle 1.5.2 + (2005060222)


$string['alternatelogin'] = 'Jos kirjoitat t‰h‰n URL:n, sit‰ k‰ytet‰‰n kirjautumissivuna t‰lle sivustolle. Sivun pit‰isi sis‰lt‰‰ lomake, jonak ominaisuudet on asetettu <strong>\'$a\'</strong> ja joko antaa paluukent‰t <strong>k‰ytt‰j‰nimi</strong> and <strong>salasana</strong>.<br />

Ole varovainen, ettet syˆt‰ virheellist‰ URL:‰‰, koska siten voit lukita itsesi ulos sivustoltasi.<br />

J‰t‰ t‰m‰ kohta tyhj‰ksi k‰ytt‰‰ksesi oletuskirjautumissivua.';
$string['alternateloginurl'] = 'Vaihtoehtoinen kirjautumis-URL';
$string['auth_cas_baseuri'] = 'Palvelimen URI (tyhj‰, jos ei baseURIa)<br /> Esimerkiksi, jos CAS-palvelin on ¥host.domaine.fr/CAS/¥, niin t‰llˆin<br />
cas_baseuri = CAS/';
$string['auth_cas_create_user'] = 'Laita t‰m‰ asetus p‰‰lle, jos haluat lis‰t‰ CAsvarmistetut k‰ytt‰j‰t Moodlen tietokantaan. Jos n‰in ei tehd‰, vain jo ennest‰‰n Moodlen tietokannassa olevat k‰ytt‰j‰t voivat kirjautua sis‰‰n.';
$string['auth_cas_enabled'] = 'Laita t‰m‰ asetus p‰‰lle, jos haluat k‰ytt‰‰ CAS-varmennusta';
$string['auth_cas_hostname'] = 'CAS-palvelimen palvelinnimi 
<br />Esim. host.domain.fr';
$string['auth_cas_invalidcaslogin'] = 'Kirjautumisesi ei onnistunut - sinua ei voitu varmentaa';
$string['auth_cas_language'] = 'Valitse kieli';
$string['auth_cas_logincas'] = 'Suojatun yhteyden muodostus';
$string['auth_cas_port'] = 'CAS-palvelimen k‰ytt‰m‰ portti';
$string['auth_cas_server_settings'] = 'CAS-palvelimen asetukset';
$string['auth_cas_text'] = 'Suojattu yhteys';
$string['auth_cas_version'] = 'CAS:in versio';
$string['auth_casdescription'] = 'T‰ss‰ menetelm‰ss‰ k‰ytet‰‰n CAS-palvelinta (Central Authentication Service) k‰ytt‰jien varmennukseen k‰ytt‰m‰ll‰ yhden kirjautumisen ymp‰ristˆ‰, Single Sign On environment (SSO). Voit myˆs k‰ytt‰‰ yksinkertaista LDAP-varmistusta. Jos annettu k‰ytt‰j‰nimi ja salasana ovat kelvollisia CAS:n mukaan Moodle luo uuden k‰ytt‰j‰tiedon tietokantaan ottaen k‰ytt‰j‰tiedot LDAP:st‰, jos se  on tarpeen. Seuraavilla kirjautumiskerroilla vain k‰ytt‰j‰nimi ja salasana tarkistetaan.';
$string['auth_castitle'] = 'K‰yt‰ CAS-palvelinta (SSO)';
$string['auth_common_settings'] = 'Yleiset asetukset';
$string['auth_data_mapping'] = 'Tietojen yhdist‰minen';
$string['auth_dbdescription'] = 'T‰m‰ moduli tarkistaa ulkoisen tietokannan taulusta k‰ytt‰j‰tunnuksen ja salasanan. Jos k‰ytt‰j‰tunnus on uusi, myˆs muita tietoja voidaan kopioda Moodleen.';
$string['auth_dbextrafields'] = 'N‰m‰ kent‰t ovat valinnaisia. Voit asettaa Moodlen hakemaan valmiiksi joitakin k‰ytt‰j‰tietoja <b>ulkoisesta tietokannasta</b>.<p>Jos j‰t‰t n‰m‰ kent‰t tyhjiksi, k‰ytet‰‰n oletusasetusarvoja.</p> <p>K‰ytt‰j‰ voi joka tapauksessa muuttaa omia henkilˆtietojaan myˆhemmin.</p>';
$string['auth_dbfieldpass'] = 'Salasanakent‰n nimi';
$string['auth_dbfielduser'] = 'K‰ytt‰j‰tunnuskent‰n nimi';
$string['auth_dbhost'] = 'Tietokantapalvelin';
$string['auth_dbname'] = 'Tietokannan nimi';
$string['auth_dbpass'] = 'Salasana k‰ytt‰j‰tunnukselle';
$string['auth_dbpasstype'] = 'M‰‰rit‰ salasanakent‰n k‰ytt‰m‰ muoto. MD5-salaus on hyˆdyllinen, jos haluat k‰ytt‰‰ muita web-sovelluksia kuten PostNukea.';
$string['auth_dbtable'] = 'Tietokannan taulun nimi';
$string['auth_dbtitle'] = 'K‰yt‰ ulkoista tietokantaa';
$string['auth_dbtype'] = 'Tietokannan tyyppi (Katso <a href=\"../lib/adodb/readme.htm#drivers\">ADOdb dokumentoinnista</a> yksityiskohdat)';
$string['auth_dbuser'] = 'K‰ytt‰j‰tunnus tietokantaan lukuoikeuksin';
$string['auth_emaildescription'] = 'S‰hkˆpostivarmistus on oletusarvoinen tunnistusmetodi k‰ytt‰j‰lle.
Kun k‰ytt‰j‰ luo itselleen tunnuksen, l‰hetet‰‰n varmistusviesti
k‰ytt‰j‰lle. Viesti sis‰lt‰‰ linkin, mink‰ avulla k‰ytt‰j‰ voi aktivoida tunnuksensa.';
$string['auth_emailtitle'] = 'K‰yt‰ s‰hkˆpostivarmistusta';
$string['auth_fccreators'] = 'T‰m‰n ryhm‰n(ryhmien) j‰senet saavat luoda uusia kursseja. Erottele useat ryhm‰nimet \';\'-merkill‰. Nimet on oltava t‰ysin samoin kuin FirstClass palvelimella.';
$string['auth_fcdescription'] = 'T‰m‰ menetelm‰ k‰ytt‰‰ FirstClass palvelinta tarkistaakseen ovatko annetttu k‰ytt‰j‰nimi ja salasana voimassa olevia.';
$string['auth_fcfppport'] = 'Palvelin portti (3333 on yleisin)';
$string['auth_fchost'] = 'FisrtClass palvelimen osoite. K‰yt‰ IP numeroa tai DNS nime‰.';
$string['auth_fcpasswd'] = 'Salasana yll‰ olevalle tilille';
$string['auth_fctitle'] = 'K‰yt‰ FirstClass palvelinta';
$string['auth_fcuserid'] = 'K‰ytt‰j‰tunnus FirstClass tilille etuoikeutetulla \"alayll‰pit‰j‰\" asetuksella.';
$string['auth_fieldlock'] = 'Lukitse arvo';
$string['auth_fieldlock_expl'] = '<p><b>Lukitse arvo:</b>P‰‰ll‰ ollessaan t‰m‰ asetus est‰‰ Moodlen k‰ytt‰ji‰ ja yll‰pit‰ji‰ muokkaamasta kentt‰‰ suoraan. K‰yt‰ t‰ta asetusta, jos hallinnoit t‰t‰ tietoa ulkoisesta j‰rjestelm‰st‰.</p>';
$string['auth_fieldlocks'] = 'Lukitse k‰ytt‰jien kent‰t';
$string['auth_fieldlocks_help'] = '<p>Voit lukita k‰ytt‰jien tietokent‰t. T‰m‰ on hyˆdyllist‰ sivustoilla, joilla yll‰pit‰j‰t hallinnoivat k‰ytt‰j‰tietoja k‰sin muokkaamalla k‰ytt‰j‰rekistereit‰ tai kopioimalla palvelimelle k‰ytt‰en ¥Upload Users¥-toimintoa. Jos lukitset kentti‰, joita Moodle tarvitsee, varmista ett‰ annat kenttien tiedot luodessasi k‰ytt‰ji‰ tai muuten k‰ytt‰j‰tilit ovat k‰yttˆkelvottomia.</p>
<p>Harkitse ¥Lukitsematon, jos tyhj‰¥-asetuksen k‰yttˆ‰ v‰ltt‰‰ksesi t‰m‰n ongelman.</p>';
$string['auth_imapdescription'] = 'T‰m‰ tapa k‰ytt‰‰ IMAP-palvelinta k‰ytt‰j‰tunnuksen ja salasanan tarkistamiseen.';
$string['auth_imaphost'] = 'IMAP-palvelimen osoite. K‰yt‰ IP-numeroa, ‰l‰ domainnime‰.';
$string['auth_imapport'] = 'IMAP-palvelimen portti, yleens‰ 143 tai 993.';
$string['auth_imaptitle'] = 'K‰yt‰ IMAP-palvelinta';
$string['auth_imaptype'] = 'IMAP-palvelimen tyyppi. Katso ohjeesta (yll‰) lis‰tietoja.';
$string['auth_ldap_bind_dn'] = 'Jos haluat k‰ytt‰‰ v‰litysk‰ytt‰j‰‰ yhteyden muodostamiseen, m‰‰rit‰ se t‰h‰n. Esim. \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Salasana v‰litysk‰ytt‰j‰lle.';
$string['auth_ldap_bind_settings'] = 'Sidosasetukset';
$string['auth_ldap_contexts'] = 'Lista konteksteista, miss‰ k‰ytt‰j‰t sijaitsevat. Erota kontekstit toisistaan \';\'-merkill‰. Esim: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Jos s‰hkˆpostiviestill‰ tunnuksensa varmentaneet k‰ytt‰j‰t luodaan automaattisesti ldap-hakemistoon, m‰‰rit‰ t‰ss‰ konteksti, minne k‰ytt‰j‰t luodaan. On hyv‰ k‰ytt‰‰ jotain erityst‰ kontekstia, jotta v‰ltyt tietoturvariskeilt‰. T‰t‰ kontekstia ei tarvitse erikseen lis‰t‰ yll‰ olevaan muuttujaan.';
$string['auth_ldap_creators'] = 'Lista ryhmist‰, mink‰ j‰senet voivat luoda uusia kursseja Moodleen. Erota useat ryhm‰t toisistaan \';\'-merkill‰. Esimerkiksi \'cn=teachers,ou=staff,o=myorg;\'';
$string['auth_ldap_expiration_desc'] = 'Valitse \"Ei\" poistaaksesi vanhentuneiden salasanojen seurannan. Tai \"LDAP\" jos haluat n‰ytt‰‰ k‰ytt‰jille viestin kun heid‰n salasanansa on vanhenemassa.';
$string['auth_ldap_expiration_warning_desc'] = 'p‰ivien m‰‰r‰ ennen salasanan voimassaolon loppumista on asetettu.';
$string['auth_ldap_expireattr_desc'] = 'Valinnainen: ylim‰‰rit‰ haluamasi ';
$string['auth_ldap_graceattr_desc'] = 'Valinnainen: ohita graceLogin atribuutti';
$string['auth_ldap_gracelogins_desc'] = 'K‰yt‰ LDAP graceLogin ominaisuutta. Esim. Edirectory voidaan konfiguroida kirjaamaan k‰ytt‰j‰ sis‰‰n  viel‰ muutaman kerran salasanan vanhenemisen j‰lkeen, jotta salana voidaan vaihtaa. Jos haluat antaa ilmoituksen kun k‰ytt‰j‰ k‰ytt‰‰ grace-logineja valise \"Kyll‰\".';
$string['auth_ldap_host_url'] = 'M‰‰rit‰ LDAP-palvelin URL-muodossa. Esim. \'ldap://ldap.myorg.com/\' tai \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_login_settings'] = 'Kirjaantumisasetukset';
$string['auth_ldap_memberattribute'] = 'Valinnainen: ylim‰‰rit‰ k‰ytt‰j‰n ryhm‰j‰senyysattribuutti. Yleens‰ \'member\' tai \'groupMembership\' ';
$string['auth_ldap_objectclass'] = 'Valinnainen: ylim‰‰rit‰ objectClass jota k‰ytet‰‰ k‰ytt‰jien hakuun.';
$string['auth_ldap_opt_deref'] = 'm‰‰ritt‰ kuinka aliakset k‰sitell‰‰n haun aikana. Valitse yksi seuraavista vaihtoehdoista: \"Ei\" (LDAP_DEREF_NEVER) tai \"Kyll‰\" (LDAP_DEREF_ALWAYS)';
$string['auth_ldap_passwdexpire_settings'] = 'LDAP salasanojen vanheneminen';
$string['auth_ldap_preventpassindb'] = 'Valitse kyll‰, jos haluat est‰‰ salasanojen tallentamisen Moodlen tietokantaa.';
$string['auth_ldap_search_sub'] = 'Aseta arvo <> 0, jos haluat hakea k‰ytt‰ji‰ myˆs alikonteksteista.';
$string['auth_ldap_server_settings'] = 'LDAP palvelimen asetukset';
$string['auth_ldap_update_userinfo'] = 'P‰ivit‰ k‰ytt‰j‰tiedot LDAP:ista Moodleen (etunimi, sukunimi, osoite..). Katso <a href=\"/auth/ldap/attr_mappings.php\">/auth/ldap/attr_mappings.php</a> tarkempia m‰‰rittelytietoja.';
$string['auth_ldap_user_attribute'] = 'Valinnainen: ylim‰‰rit‰ attribuutti k‰ytt‰j‰nimille. Yleens‰ \'cn\'.';
$string['auth_ldap_user_settings'] = 'K‰ytt‰jien etsint‰';
$string['auth_ldap_user_type'] = 'Valitse kuinka k‰ytt‰j‰t tallennetaan LDAP:iin. T‰m‰ asetus myˆs m‰‰ritt‰‰ kuinka sis‰‰nkirjautumisen voimassaolo, vapaat sis‰‰nkirjautumiset ja k‰ytt‰jien luominen toimii';
$string['auth_ldap_version'] = 'Palvelimella k‰ytett‰v‰ LDAP protokolla versio';
$string['auth_ldapdescription'] = 'T‰m‰ tapa tarjoaa k‰ytt‰j‰tunnistuksen LDAP-palvelimelta. Jos salasana ja tunnus t‰sm‰‰v‰t, Moodle luo uuden k‰ytt‰j‰n  tietokantaansa. 

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
$string['auth_pamdescription'] = 'T‰m‰ menetelm‰ k‰ytt‰‰ PAM:ia p‰‰st‰kseen k‰siksi t‰m‰n palvelimen alkuper‰isiin k‰ytt‰j‰nimiin. Sinun t‰ytyy asentaa <a href=\"http://www.math.ohio-state.edu/~ccunning/pam_auth/\" target=\"_blank\">PHP4 PAM Authentication</a> p‰‰st‰ksesi k‰ytt‰m‰‰n t‰t‰ moduulia.';
$string['auth_pamtitle'] = 'PAM (  kytkett‰v‰t oikeuksien tarkistamis moduulit)';
$string['auth_passwordisexpired'] = 'Salasanasi on vanhentunut. Haluatko vaihtaa salasanasi nyt?';
$string['auth_passwordwillexpire'] = 'Salasanasi vanhentuu $a p‰iv‰ss‰. Haluatko vaihtaa salasanasi nyt?';
$string['auth_pop3description'] = 'T‰m‰ tapa k‰ytt‰‰ POP3-palvelinta k‰ytt‰j‰n tunnistukseen.';
$string['auth_pop3host'] = 'POP3-palvelimen osoite. K‰yt‰ IP-numeroa, ‰l‰ domainnime‰.';
$string['auth_pop3mailbox'] = 'Postilaatikon nimi jonka kanssa yritet‰‰n yhteytt‰. (yleens‰ INBOX)';
$string['auth_pop3port'] = 'POP3-palvelimen portti (yleens‰ 110 )';
$string['auth_pop3title'] = 'K‰yt‰ POP3-palvelinta';
$string['auth_pop3type'] = 'Palvelimen tyyppi. Jos k‰yt‰tte salattua yhteytt‰, valitse pop3cert.';
$string['auth_shib_convert_data'] = 'Tiedon muokaamisen API';
$string['auth_shib_convert_data_description'] = 'Voit k‰ytt‰‰ t‰t‰ APIa muokataksesi edelleen tietoja, joita Shibboleth tarjoaa. Lue  <a href=\"../auth/shibboleth/README.txt\" target=\"_blank\">README (englanniksi)</a> saadakseis lis‰‰ tietoa.';
$string['auth_shib_convert_data_warning'] = 'Tiedosto ei ole olemassa tai se ei ole verkkopalvelinprosessin luettavissa!';
$string['auth_shib_instructions'] = 'K‰yt‰ <a href=\"$a\">Shibboleth-kirjautumista</a> k‰ytt‰‰ksesi yhteyden muodostamiseen Shibbolethia, jos se on tarjolla. <br />
Muuten voit k‰ytt‰‰ t‰t‰ tavallista kirjautumislomaketta.';
$string['auth_shib_instructions_help'] = 'T‰h‰n voit kirjoittaa lis‰ohjeita k‰ytt‰jillesi selitt‰‰ksesi Shibboleth-varmennusta. N‰m‰ ohjeet n‰ytet‰‰n kirjautumissivun ohjeosiossa. Siin‰ pit‰isi olla linkki, joka ohjaa k‰ytt‰j‰t \"<b>$a</b>\", niin ett‰ Shibbolethin k‰ytt‰j‰t voivat kirjautua sis‰‰n Moodleen. Jos j‰t‰t t‰m‰n tyhj‰ksi, n‰ytet‰‰ k‰ytt‰jille tavallset ohjeet (eiv‰t k‰sittele erityisesti Shibbolethia)';
$string['auth_shib_only'] = 'Vain Shibboleth';
$string['auth_shib_only_description'] = 'K‰yt‰ t‰t‰ valintaa, jos haluat pakottaa Shibboleth-varmennuksen';
$string['auth_shib_username_description'] = 'Sen verkkopalvelimen Shibboleth-ymp‰ristˆn muuttujan nimi, jota k‰ytet‰‰n Moodlen k‰ytt‰j‰nimen‰.';
$string['auth_shibboleth_login'] = 'Shibboleth-kirjautuminen';
$string['auth_shibboleth_manual_login'] = 'Sis‰‰nkirjaantuminen k‰sin';
$string['auth_shibbolethdescription'] = 'T‰t‰ menetelm‰‰ k‰ytt‰ess‰ k‰ytt‰j‰t luodaan ja varmennetaan k‰ytt‰en href=\"http://shibboleth.internet2.edu/\" target=\"_blank\">Shibboleth-k‰ytt‰j‰nvarmennusta</a>. Lue <a href=\"../auth/shibboleth/README.txt\" target=\"_blank\">README (englanniksi)</a>, jossa kerrotaan kuinka Moodle asetaan k‰ytt‰m‰‰n Shibbolethin-varmennusta.';
$string['auth_shibbolethtitle'] = 'Shibboleth';
$string['auth_updatelocal'] = 'P‰ivit‰ sis‰inen arvo';
$string['auth_updatelocal_expl'] = '<p><b>P‰ivit‰ sis‰inen arvo:</b> Jos ei onnistu, kentt‰ p‰ivittyy joka kerta k‰ytt‰j‰n kirjautuessa tai k‰ytt‰j‰synkronoinnin yhteydess‰. Kent‰t jotka on asetettu p‰ivittym‰‰n paikallisesti tulisi lukita.</p> ';
$string['auth_updateremote'] = 'P‰ivit‰ ulkoinen arvo';
$string['auth_updateremote_expl'] = '<p><b>P‰ivit‰ ulkoinen tieto:</b> Jos t‰m‰ ei onnistu, ulkoinen tieto p‰ivitet‰‰n samalla kun k‰ytt‰j‰ rekisteri p‰ivitet‰‰n. Kenttien pit‰isi olla lukitsemattomia, jotta editointi sallitaan.</p>';
$string['auth_updateremote_ldap'] = '<p><b>Huomautus:</b> Ulkoisen LDAP tiedon p‰ivitys vaatii, ett‰ asetetaan binddn ja bindpw
kaikilla sidosk‰ytt‰jille muotoiluoikeus kaikkiin k‰ytt‰j‰rekistereihin. Se ei t‰ll‰ hetkell‰ s‰ilyt‰ moniarvoisia m‰‰reit‰, eik‰ poista ylim‰‰r‰isi‰ arvoja p‰ivityksess‰. </p>';
$string['auth_user_create'] = 'K‰ytt‰j‰n luonti';
$string['auth_user_creation'] = 'K‰ytt‰j‰t voivat itse luoda tunnuksensa. K‰ytt‰j‰tiedot tarkistetaan s‰hkˆpostin avulla. Jos aktivoit t‰m‰n vaihtoehdon, muista myˆs m‰‰ritell‰ autentikointi-modulin muut t‰h‰n liittyv‰t asetukset.';
$string['auth_usernameexists'] = 'K‰ytt‰j‰tunnus on jo k‰ytˆss‰. Valitse joku toinen.';
$string['authenticationoptions'] = 'K‰ytt‰j‰tunnistuksen asetukset';
$string['authinstructions'] = 'T‰h‰n voi kirjoittaa ohjeet opiskelijoille, mit‰ tunnusta ja salasanaa heid‰n tulisi k‰ytt‰‰. T‰m‰ teksti n‰kyy kirjautumissivulla.';
$string['changepassword'] = 'Salasananvaihto-URL';
$string['changepasswordhelp'] = 'T‰ss‰ osoitteessa k‰ytt‰j‰t voivat vaihtaa unohtamansa salasanan. K‰ytt‰jille t‰m‰ n‰kyy painikkeena kirjautumissivulla ja heid‰n k‰ytt‰j‰tietosivullaan.';
$string['chooseauthmethod'] = 'Valitse k‰ytt‰j‰ntunnistusmetodi: ';
$string['createchangepassword'] = 'Luo, jos ei olemassa - pakota muutos';
$string['createpassword'] = 'Luo, jos ei olemassa';
$string['forcechangepassword'] = 'Pakoita salasanan vaihto';
$string['forcechangepassword_help'] = 'Pakota k‰ytt‰j‰t vaihtamaan salasanaa heid‰n seuraavalla Moodleen kirjautumiskerrallaan.';
$string['forcechangepasswordfirst_help'] = 'Pakota k‰ytt‰j‰t vaihtamaan salasanaa heid‰n ensimm‰isell‰ Moodleen kirjautumiskerrallaan.';
$string['guestloginbutton'] = 'Kirjaudu vieraana-painike';
$string['infilefield'] = 'Kentt‰‰n tarvitaan tiedostossa';
$string['instructions'] = 'Ohjeet';
$string['locked'] = 'Lukittu';
$string['md5'] = 'MD5-salaus';
$string['passwordhandling'] = 'Salasanakent‰n k‰sittely';
$string['plaintext'] = 'Selv‰kielinen teksti';
$string['showguestlogin'] = 'Voit n‰ytt‰‰ tai piilottaa Kirjaudu vieraana-painikkeen kirjautumissivulla.';
$string['stdchangepassword'] = 'K‰yt‰ norminmukaista Vaihda salasana Sivua';
$string['stdchangepassword_expl'] = 'Jos ulkoinen oikeuksien tarkistaminen sallii salasanojen vaihdot Moodlen kautta, vaihda t‰m‰ muotoon kyll‰. T‰m‰ asetus syrj‰ytt‰‰ \"Vaihda salasana URL\".';
$string['stdchangepassword_explldap'] = 'HUOMAUTUS: On suositeltavaa, ett‰ k‰ytet‰‰n ennemmin LDAP kuin SSL salakirjoitettua tunnelia (ldaps://)jos LDAP palvelin on et‰k‰ytˆss‰.';
$string['unlocked'] = 'Lukitsematon';
$string['unlockedifempty'] = 'Lukitsematon, jos tyhj‰';
$string['update_never'] = 'Ei koskaan';
$string['update_oncreate'] = 'Luotaessa';
$string['update_onlogin'] = 'Jokaisella kirjautumisella';
$string['update_onupdate'] = 'P‰ivitett‰ess‰';

?>
