<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5.2 + (2005060222)


$string['description'] = '<p>Voit käyttää LDAP-palvelinta hallinnoidaksesi ilmoittautumisia. Tällöin oletetaan että LDAP-puusi sisältää ryhmät, jotka liittyvät kursseihin ja että jokainen näistä ryhmistä/kursseista tulee sisältämään jäsenyysrekisterin, johon oppilaat liitetään.</p>

<p>Oletetaan myös, että kurssit määritellään ryhminä LDAPssä ja että jokaisessa ryhmässä on useita jäsenyyskenttiä (<em>member</em> or <em>memberUid</em>), jotka sisältävät yksilöllisen identiteetin käyttäjillä.</p>

<p>Käyttääksesi LDAP-ilmoittautumisia käyttäjilläsi <strong>täytyy</strong> olla kelvollinen id-numero kentässään. LDAP-ryhmillä täytyy olla tämä id-numero jäsenyyskentässään, jotta käyttäjä voi liittyä kurssille. Yleensä tämä toimii hyvin, jos käytät jo LDAP-varmennusta.</p>

<p>Ilmoittautumiset päivitetään kun käyttäjät kirjautuvat sisään. Voit myös ajaa scriptin, joka pitää ilmoittautumiset synkronoituina. Se löytyy  <em>enrol/ldap/enrol_ldap_sync.php</em> polulta.</p>

<p>Tämä laajennus voidaan myös asettaa luomaan uusia kursseja kun LDAPhen luodaan uusia ryhmiä.</p>';
$string['enrol_ldap_autocreate'] = 'Kurssit voidaan luoda automaattisesti, jos sellaisella kurssille on ilmoittautumisia, jota ei vielä ole Moodlessa.';
$string['enrol_ldap_autocreation_settings'] = 'Automaattisen kurssin luonnin asetukset';
$string['enrol_ldap_bind_dn'] = 'Jos haluat käyttää bind-useria etsiäksesi käyttäjiä, merkitse se tähän. Esimerkki:  \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Salasana ´bind-user´ille.';
$string['enrol_ldap_category'] = 'Kategoria automaattisesti luoduille kursseille.';
$string['enrol_ldap_course_fullname'] = 'Valinnainen: LDAP-kenttä jolta haetaan koko nimi.';
$string['enrol_ldap_course_idnumber'] = 'Linkitä yksilölliseen tunnukseen LDAPssa, yleensä  <em>cn</em> tai <em>uid</em>. On suositeltavaa lukita arvo, jos käytät automaattista kurssin luontia.';
$string['enrol_ldap_course_settings'] = 'Kurssin ilmoittautumisen asetukset';
$string['enrol_ldap_course_shortname'] = 'Valinnainen: LDAP-kenttä jolta haetaan lyhyt nimi.';
$string['enrol_ldap_course_summary'] = 'Valinnainen: LDAP-kenttä jolta haetaan yhteenveto.';
$string['enrol_ldap_editlock'] = 'Lukitse arvo';
$string['enrol_ldap_general_options'] = 'Yleiset asetukset';
$string['enrol_ldap_host_url'] = 'Määritä LDAP-palvelin URL-muodossa. Malli:  \'ldap://ldap.myorg.com/\'tai \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'objektiLuokka jolla etsittän kursseilta. Yleensä \'posixGroup\'.';
$string['enrol_ldap_search_sub'] = 'Etsi ryhmien jäsenyyksiä alakonteksteista';
$string['enrol_ldap_server_settings'] = 'LDAP-palvelimen asetukset';
$string['enrol_ldap_student_contexts'] = 'Lista konteksteista, joissa ryhmät joille on ilmoittautunut oppilaita sijaitsevat. Eri kontekstit erotetaan puolipisteellä ´;´. Esimerkki: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Jäsenominaisuus, kun käyttäjä kuuluu (on ilmoittautunut) ryhmään. Yleensä ´member´ tai ´memberUid´.';
$string['enrol_ldap_student_settings'] = 'Oppilaan ilmoittautumisen asetukset';
$string['enrol_ldap_teacher_contexts'] = 'Lista konteksteista, joissa ryhmät joille on ilmoittautunut opettaja, sijaitsee. Eri kontekstit erotetaan puolipisteellä ´;´. Esimerkki: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Jäsenominaisuus, kun käyttäjä kuuluu (on ilmoittautunut) kurssille. Yleensä ´member´ tai ´memberUid´.';
$string['enrol_ldap_teacher_settings'] = 'Opettajan ilmoittautumisen asetukset';
$string['enrol_ldap_template'] = 'Valinnainen: automaattisesti luodut kurssit voivat kopioida asetuksensa käyttäen pohjana mallikurssin asetuksia.';
$string['enrol_ldap_updatelocal'] = 'Päivitä paikalliset tiedot';
$string['enrol_ldap_version'] = 'LDAP-protokollan versio, jota palvelimesi käyttää.';
$string['enrolname'] = 'LDAP';

?>
