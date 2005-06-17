<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.6 development (2005060201)


$string['description'] = '<p>Na kontrolu Va¹ich zápisov, mô¾ete pou¾i» LDAP server. Predpokladom je, ¾e Vá¹ LDAP strom obsahuje skupiny, ktoré mapujú kurzy a ka¾dá z týchto skupín/kurzov obsahuje záznamy o pou¾ívateµoch, ktoré mapujú ¹tudentov.</p>
<p>Predpokladá sa, ¾e kurzy sú definované ako skupiny v LDAP a ka¾dá skupina má viacero pou¾ívateµských polí (<em>èlen</em> alebo <em>Uidèlena</em>), ktoré zabezpeèujú jednoznaènú identifikáciu pou¾ívateµa.</p>
<p>Aby ste mohli pou¾i» LDAP zapisovanie, Va¹i pou¾ívatelia  <strong>musia</strong> ma» platné pole idnumber. LDAP skupiny musia ma» idnumber v poliach pre pou¾ívateµa, aby sa mohli zapisova» do kurzov. Toto bude pravdepodobne fungova» bez problémov, ak u¾ pou¾ívate LDAP Autentifikáciu.</p>
<p>Zápisy sa budú aktualizova», keï sa pou¾ívateµ prihlási. Na synchronizáciu uchovávania zápisov, mô¾ete pou¾i» aj skript. Pozrite sa do  <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>Tento plugin mô¾e by» nastavený na automatické vytváranie nových kurzov, ak sa objavia nové skupiny v LDAP.</p>';
$string['enrol_ldap_autocreate'] = 'Kurzy mô¾u by» vytvárané automaticky, ak existujú zápisy do kurzov, ktoré e¹te neexistujú v Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Nastavenia automatického vytvárania kurzov';
$string['enrol_ldap_bind_dn'] = 'Ak chcete pou¾i» spoluu¾ívateµa na vyhµadávanie pou¾ívateµov, definujte to tu. Nieèo ako: \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Heslo pre spoluu¾ívateµa.';
$string['enrol_ldap_category'] = 'Kategória pre automaticky vytvorené kurzy.';
$string['enrol_ldap_course_fullname'] = 'Nepovinné: LDAP pole, z ktorého sa má vybra» celé meno pou¾ívateµa.';
$string['enrol_ldap_course_idnumber'] = 'Plán jednoznaèného identifikátora v LDAP, obyèajne  <em>cn</em> alebo <em>uid</em>. Odporúèa sa \"uzamknú»\" túto hodnotu, ak pou¾ívate automatické vytváranie kurzov.';
$string['enrol_ldap_course_settings'] = 'Nastavenia zápisov do kurzov';
$string['enrol_ldap_course_shortname'] = 'Nepovinné: LDAP pole, z ktorého sa má vybra» skrátené menou pou¾ívateµa.';
$string['enrol_ldap_course_summary'] = 'Nepovinné: LDAP pole, z ktorého sa má vybra» súhrn.';
$string['enrol_ldap_editlock'] = 'Uzamknú» hodnotu';
$string['enrol_ldap_general_options'] = 'V¹eobecné nastavenia';
$string['enrol_ldap_host_url'] = '©pecifikujte hos»ovský LDAP v URL forme, napr:  \'ldap://ldap.myorg.com/\'
alebo \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'Na vyhµadávanie kurzov sa pou¾íva objectClass. Obyèajne \'posixGroup\'.';
$string['enrol_ldap_search_sub'] = 'Hµada» úèastníkov skupiny  v subkontextoch.';
$string['enrol_ldap_server_settings'] = 'Nastavenia LDAP servera';
$string['enrol_ldap_student_contexts'] = 'Zoznam kontextov, kde sú umiestnené skupiny so zápismi ¹tudentov. Rozdielne kontexty oddeµte bodkoèiarkou, napr: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Atribút pou¾ívateµa, keï pou¾ívatelia patria (sú zapísaní) do skupiny. Obyèajne \'member\'
alebo \'memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Nastavenia zápisov ¹tudentov';
$string['enrol_ldap_teacher_contexts'] = 'Zoznam kontextov, kde sú umiestnené skupiny so zápismi uèiteµov. Rozdielne kontexty oddeµte bodkoèiarkou, napr: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Atribút pou¾ívateµa, keï pou¾ívatelia patria (sú zapísaní) do skupiny. Obyèajne \'member\'
alebo \'memberUid\'.';
$string['enrol_ldap_teacher_settings'] = 'Nastavenia zápisov uèiteµov';
$string['enrol_ldap_template'] = 'Nepovinné: Pri automaticky vytváraných kurzoch sa mô¾u ich nastavenia kopírova» zo ¹ablóny kurzu.';
$string['enrol_ldap_updatelocal'] = 'Aktualizova» miestne údaje';
$string['enrol_ldap_version'] = 'Verzia LDAP protokolu, ktorú pou¾íva Vá¹ server.';
$string['enrolname'] = 'LDAP (Lightweight Directory Access Protocol)';

?>
