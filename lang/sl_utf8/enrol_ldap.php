<?PHP // $Id:enrol_ldap.php from enrol_ldap.xml
      // Comments: tomaz at zid dot si

$string['enrolname'] = 'LDAP';
$string['description'] = '<p>Uporabite lahko strežnik LDAP za nadzor vaših prijav.  
                          Predvideno je, da drevo LDAP vsebuje skupine, ki se ujemajo s 
                          predmeti in vsaka izmed teh skupin oz. predmetov bo
                          imela vnose članstva, ki se bodo ujemali z udeleženci.</p>
                          <p>Predvideno je, da so predmeti določeni kot skupine v
                          LDAP-u, kjer ima vsaka skupina več polij za članstvo
                          (<em>member</em> ali <em>memberUid</em>), ki vsebujejo enolično
                          identifikacijo uporabnika.</p>
                          <p>Za uporabo LDAP vpisovanja, <stron>morajo</strong> vaši uporabniki
                          imeti veljavno polje idnumber. Skupine LDAP morajo imeti
                          isto idnumber oznako v poljih member, da je uporabnik prijavljen
                          v predmet.
                          To bo običajno delovalo dobro, če že uporabljate LDAP
                          preverjanje pristnosti.</p>
                          <p>Prijave v predmete bodo posodobljene, ko se uporabnik prijavi. Lahko
                           tudi zaženete skripto, za ohranjanje sinhroniziranih prijav. Poglejte v 
                          <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
                          <p>Ta vtičnik lahko nastavite za samodejno ustvarjanje novih
                          predmetov, ko se nova skupina pojavi v LDAP-u.</p>';
$string['enrol_ldap_server_settings'] = 'Nastavitve strežnika LDAP';
$string['enrol_ldap_host_url'] = 'Navedite ime LDAP gostitelja in obliki URL npr.:
                                  \'ldap://ldap.myorg.com/\' 
                                  ali \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_version'] = 'Različica protokola LDAP, ki ga uporablja vaš strežnik.';
$string['enrol_ldap_bind_dn'] = 'Če želite uporabiti povezovalnega uporabnika (bind-user) za iskanje uporabnikov,
                                 ga navedite tu: Nekaj podobnega
                                 \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Geslo za bind-user.';
$string['enrol_ldap_search_sub'] = 'Iskanje članstva skupine iz podkontekstov.';
$string['enrol_ldap_student_settings'] = 'Nastavitve vpisa udeleženca';
$string['enrol_ldap_teacher_settings'] = 'Nastavitve vpisa izvajalca';
$string['enrol_ldap_course_settings'] = 'Nastavitve vpisa predmeta';
$string['enrol_ldap_student_contexts'] = 'Seznam kontekstov v katerih se nahajajo
                                          skupine z udeleženci. Ločite različne 
                                          kotekste s podpičjem \';\'. Na primer: 
                                          \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Lastnost člana, ko uporabnik pripada
                                          (je vpisan) v skupino. Običajno \'member\'
                                          ali \'memberUid\'.';
$string['enrol_ldap_teacher_contexts'] = 'Seznam kontekstov v katerih se nahajajo
                                          skupine z izvajalci. Ločite različne 
                                          kotekste s podpičjem \';\'. Na primer: 
                                          \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Lastnost člana, ko uporabnik pripada
                                          (je vpisan) v skupino. Običajno \'member\'
                                          ali \'memberUid\'.';
$string['enrol_ldap_autocreation_settings'] = 'Nastavitve samodejnega ustvarjanja predmetov';
$string['enrol_ldap_autocreate'] = 'Predmeti se lahko samodejno ustvarijo, če so
                                    vpisi v predmet, ki še ne obstaja 
                                    v Moodle.';
$string['enrol_ldap_objectclass'] = 'objectClass uporabljen za iskanje predmetov. Običajno
                                     \'posixGroup\'.';
$string['enrol_ldap_category'] = 'Kategorija za samodejno ustvarjene predmete.';
$string['enrol_ldap_template'] = 'Neobvezno: samodejno ustvarjeni predmeti lahko kopirajo 
                                  svoje nastavitve iz predloge predmeta.';
$string['enrol_ldap_updatelocal'] = 'Posodobi lokalne podatke';
$string['enrol_ldap_editlock'] = 'Zakleni vrednost';
$string['enrol_ldap_course_idnumber'] = 'Povezovanje z enoličnim identifikatorjem v LDAP, običajno
                                         <em>cn</em> ali <em>uid</em>. Je 
                                         priporočeno, da zaklenete vrednost, če uporabljate 
                                         samodejno ustvarjanje predmetov.';
$string['enrol_ldap_course_shortname'] = 'Neobvezno: LDAP polje za pridobitev kratkega imena.';
$string['enrol_ldap_course_fullname'] = 'Neobvezno: LDAP polje za pridobitev polnega imena.';
$string['enrol_ldap_course_summary'] = 'Neobvezno: LDAP polje za pridobitev povzetka.';
$string['enrol_ldap_general_options'] = 'Splošne možnosti';


?>