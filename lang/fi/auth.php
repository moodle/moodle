<?PHP // $Id$ 
      // auth.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2004101900)


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
$string['auth_fccreators'] = 'T‰m‰n ryhm‰n(ryhmien) j‰senet saavat luoda uusia kursseja. Erottele useat ryhm‰nimet \';\'-merkill‰. Nimet on oltava t‰ysin samoin kuin FirstClass palvelimella.';
$string['auth_fcdescription'] = 'T‰m‰ menetelm‰ k‰ytt‰‰ FirstClass palvelinta tarkistaakseen ovatko annetttu k‰ytt‰j‰nimi ja salasana voimassa olevia.';
$string['auth_fcfppport'] = 'Palvelin portti (3333 on yleisin)';
$string['auth_fchost'] = 'FisrtClass palvelimen osoite. K‰yt‰ IP numeroa tai DNS nime‰.';
$string['auth_fcpasswd'] = 'Salasana yll‰ olevalle tilille';
$string['auth_fctitle'] = 'K‰yt‰ FirstClass palvelinta';
$string['auth_fcuserid'] = 'K‰ytt‰j‰tunnus FirstClass tilille etuoikeutetulla \"alayll‰pit‰j‰\" asetuksella.';
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
$string['auth_ldap_expiration_desc'] = 'Valitse \"Ei\" poistaaksesi vanhentuneiden salasanojen seurannan. Tai \"LDAP\" jos haluat n‰ytt‰‰ k‰ytt‰jille viestin kun heid‰n salasanansa on vanhenemassa.';
$string['auth_ldap_expiration_warning_desc'] = 'p‰ivien m‰‰r‰ ennen salasanan voimassaolon loppumista on asetettu.';
$string['auth_ldap_expireattr_desc'] = 'Valinnainen: ylim‰‰rit‰ haluamasi ';
$string['auth_ldap_graceattr_desc'] = 'Valinnainen: ylim‰‰rit‰ graceLogin atribuutti';
$string['auth_ldap_gracelogins_desc'] = 'K‰yt‰ graceLogin ominaisuutta. Esim Edirectory voidaan konfiguroida kirjaamaan k‰ytt‰j‰sis‰‰n  muutaman viel‰ kerran salasanan vanhenemisen j‰lkeen, jotta salana voidaan vaihtaa. JOs haluat antaa ilmoituksen kun k‰ytt‰j‰ k‰ytt‰‰ grace-logineja valise \"Kyll‰\".';
$string['auth_ldap_host_url'] = 'M‰‰rit‰ LDAP-palvelin URL-muodossa. Esim. \'ldap://ldap.myorg.com/\' tai \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_login_settings'] = 'Kirjaantumisasetukset';
$string['auth_ldap_memberattribute'] = 'Valinnainen: ylim‰‰rit‰ k‰ytt‰j‰n ryhm‰j‰senyysattribuutti. Yleens‰ \'member\' tai \'groupMembership\' ';
$string['auth_ldap_objectclass'] = 'Valinnainen: ylim‰‰rit‰ objectClass jota k‰ytet‰‰ k‰ytt‰jien hakuun.';
$string['auth_ldap_passwdexpire_settings'] = 'LDAP salasanojen vanheneminen';
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
$string['forcechangepassword'] = 'Pakoita salasanan vaihto';
$string['forcechangepassword_help'] = 'Pakota k‰ytt‰j‰t vaihtamaan salasanaa heid‰n seuraavalla Moodleen kirjautumiskerrallaan.';
$string['forcechangepasswordfirst_help'] = 'Pakota k‰ytt‰j‰t vaihtamaan salasanaa heid‰n ensimm‰isell‰ Moodleen kirjautumiskerrallaan.';
$string['guestloginbutton'] = 'Kirjaudu vieraana-painike';
$string['instructions'] = 'Ohjeet';
$string['md5'] = 'MD5-salaus';
$string['parentlanguage'] = 'KƒƒNTƒJƒT: Jos kielell‰si on kantakieli jota Moodlen pit‰isi k‰ytt‰‰ merkkijonon ollessa kateissa, t‰smenn‰ sit‰ varten koodi t‰h‰n. Jos j‰t‰t t‰m‰n alueen tyhj‰ksi, k‰ytet‰‰n englantia. Esimerkki: nl';
$string['plaintext'] = 'Selv‰kielinen teksti';
$string['showguestlogin'] = 'Voit n‰ytt‰‰ tai piilottaa Kirjaudu vieraana-painikkeen kirjautumissivulla.';
$string['stdchangepassword'] = 'K‰yt‰ norminmukaista Vaihda salasana Sivua';
$string['stdchangepassword_expl'] = 'Jos ulkoinen oikeuksien tarkistaminen sallii salasanojen vaihdot Moodlen kautta, vaihda t‰m‰ muotoon kyll‰. T‰m‰ asetus syrj‰ytt‰‰ \"Vaihda salasana URL\".';
$string['stdchangepassword_explldap'] = 'HUOMAUTUS: On suositeltavaa, ett‰ k‰ytet‰‰n ennemmin LDAP kuin SSL salakirjoitettua tunnelia (ldaps://)jos LDAP palvelin on et‰k‰ytˆss‰.';
$string['thischarset'] = 'KƒƒNTƒJƒT: T‰smenn‰ kielen merkistˆ t‰h‰n. Huomaa, ett‰ kaikki teksti joka luodaan t‰m‰n kielen ollessa aktiivinen taltioidaan t‰t‰ merkistˆ‰ k‰ytt‰en, joten ‰l‰ muuta sit‰, kun olet tehnyt asetukset. Esimerkki: iso-8859-1';
$string['thisdirection'] = 'KƒƒNTƒJƒT: T‰m‰ merkkijono t‰sment‰‰ tekstisi suunnan, joko vasemmalta oikealle tai oikealta vasemmalle. Syˆt‰ joko îltrî tai îrtlî.';
$string['thislanguage'] = 'KƒƒNTƒJƒT: M‰‰rittele kielesi nimi t‰h‰n. Jos mahdollista, k‰yt‰ yksikoodista numeerista viittausta.';

?>
