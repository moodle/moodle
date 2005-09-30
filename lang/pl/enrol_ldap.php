<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5.2 + (2005060223)


$string['description'] = '<p>Mo¿esz u¿yæ serwera LDAP do kontroli zapisów.
Zak³ada siê ¿e twoje drzewo LDAP zawiera grupy odwzorowuj±ce kursy ¿e ka¿da z tych grup/kursów bêdzie mia³a wpisy cz³onkowskie odwzorowuj±ce studentów. </p>
Zak³ada siê, ¿e kursy s± zdefiniowane jako grupy w LDAPie, a ka¿da z tych grup ma wiele pól czlonkowkich (<em>member</em> lub <em>memberUid</em>)  które zawieraj± unikatowy identyfikator u¿ytkownika.
Aby wykorzystywaæ zapisy przez LDAP twoi u¿ytkownicy <strong> musz± </strong> mieæ wa¿ne (aktualne, poprawne) pole idnumber. Grupy LDAP musz± mieæ ten idnumber w polach cz³onków aby u¿ytkownik zosta³ zapisany na kurs.
To bêdzie dzia³aæ poprawnie je¶li ju¿ korzystasz z autoryzacji LDAP.</p>
Zapisywanie bêdzie uaktualniane kiedy u¿ytkownik zaloguje siê. Mo¿na równie¿ uruchomiæ skrypt do synchronizacji zapisów. Zobacz w em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p> Ta wtyczka mo¿e równie¿ tworzyæ automatycznie nowe kursy, kiedy pojawiaj± siê nowe grupy w LDAP. </p>';
$string['enrol_ldap_autocreate'] = 'Kursy mog± byæ tworzone automatycznie je¿eli pojawia siê zg³oszenie na kurs, który dotychczas nie istnieje w Moodle';
$string['enrol_ldap_autocreation_settings'] = 'Ustawinia automatycznego tworzenia kursów';
$string['enrol_ldap_bind_dn'] = 'Je¿eli chcesz u¿ywaæ bind-user do poszukiwania u¿ytkowników, okre¶l ich tutaj. Podobnie do \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Has³o dla bind-user';
$string['enrol_ldap_category'] = 'Kategoria dla automatycznie tworzonych kursów';
$string['enrol_ldap_course_fullname'] = 'Opcjonalne: Pole sk±d LDAP ma pobieraæ pe³n± nazwê.';
$string['enrol_ldap_course_idnumber'] = 'Mapuj (odwzoruj) unikalny identyfikator w LDAP, przewa¿nie <em>cn</em> lub <em>uid</em>. Blokuj tê warto¶æ je¿eli u¿ywasz automatycznego tworzenia kursów.';
$string['enrol_ldap_course_settings'] = 'Ustawienie zapisywania na kurs';
$string['enrol_ldap_course_shortname'] = 'Opcjonalne:Pole sk±d LDAP ma pobieraæ nazwê skrócon±';
$string['enrol_ldap_course_summary'] = 'Opcjonalne:Pole sk±d LDAP ma pobieraæ opis';
$string['enrol_ldap_editlock'] = 'Blokuj warto¶æ';
$string['enrol_ldap_general_options'] = 'Opcje ogólne';
$string['enrol_ldap_host_url'] = 'Okre¶l URL hostu LDAP podobnie do: \'ldap://ldap.myorg.com/\' 
lub \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'objectClass u¿ywany do szukania kursów. Przewa¿nie \'posixGroup\'.';
$string['enrol_ldap_search_sub'] = 'Szukaj cz³onków grupy dla podkontekstów.';
$string['enrol_ldap_server_settings'] = 'Ustawienia sewera LDAP';
$string['enrol_ldap_student_contexts'] = 'Wymieñ kolejno listê kontekstów gdzie grupy z zapisanymi studentami s± rozmieszczane . Oddziel ró¿ne konteksty \';\'. Np: ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Cecha cz³onek grupy, okre¶la kiedy student nale¿y (jest zapisany) do grupy. Zwykle zawiera pole \'member\' albo \'memberUid\'. ';
$string['enrol_ldap_student_settings'] = 'Ustawienia zapisywania studentów';
$string['enrol_ldap_teacher_contexts'] = 'Wymieñ kolejno listê kontekstów gdzie grupy z zapisanymi prowadz±cymi s± rozmieszczane . Oddziel ró¿ne konteksty \';\'. Np: ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Cecha cz³onek grupy, okre¶la kiedy prowadz±cy nale¿y (jest zapisany) do grupy. Zwykle zawiera pole \'member\' albo \'memberUid\'. ';
$string['enrol_ldap_teacher_settings'] = 'Ustawienia zapisywania prowadz±cych';
$string['enrol_ldap_template'] = 'Opcjonalnie: Auto-tworzenie kursów mo¿e kopiowaæ ustawienia z wzorcowego kursu.';
$string['enrol_ldap_updatelocal'] = 'Uaktualnij dane lokalne';
$string['enrol_ldap_version'] = 'Wersja protoko³u LDAP zainstalowana na Twoim serwerze.';
$string['enrolname'] = 'LDAP';

?>
