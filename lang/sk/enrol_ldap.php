<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5 ALPHA (2005051500)


$string['description'] = '<p>Na kontrolu Va¹ich zápisov, mô¾ete pou¾i» LDAP server. Predpokladom je, ¾e Vá¹ LDAP strom obsahuje skupiny, ktoré mapujú kurzy a ka¾dá z týchto skupín/kurzov obsahuje záznamy o u¾ívateµoch, ktoré mapujú ¹tudentov.</p>
<p>Predpokladá sa, ¾e kurzy sú definované ako skupiny v LDAP a ka¾dá skupina má viacero u¾ívateµských polo¾iek  (<em>member</em> alebo <em>memberUid</em>), ktoré zabezpeèujú jednoznaènú definíciu u¾ívateµa.</p>
<p>Aby ste mohli pou¾i» LDAP zapisovanie, Va¹i u¾ívatelia  <strong>musia</strong> ma» aktívnu polo¾ku idnumber. LDAP skupiny musia ma» idnumber v polo¾kách pre u¾ívateµa, aby sa mohli zapisova» do kurzov. Toto bude pravdepodobne fungova» bez problémov, ak u¾ pou¾ívate LDAP Autentifikáciu.</p>
<p>Zápisy sa budú aktualizova», ak sa u¾ívateµ prihlási. Na synchronizáciu uchovávania zápisov, mô¾ete pou¾i» aj skript. Pozrite sa do  <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>Táto voµba mô¾e by» nastavená na automatické vytváranie nových kurzov, ak sa objavia nové skupiny v LDAP.</p>';
$string['enrol_ldap_autocreate'] = 'Kurzy mô¾u by» vytvárané automaticky, ak existujú zápisy do kurzov, ktoré e¹te neexistujú v Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Nastavenia automatického vytvárania kurzov';
$string['enrol_ldap_bind_dn'] = 'Ak chcete pou¾i» spoluu¾ívateµa na vyhµadávanie u¾ívateµov, tu to definujte. Nieèo ako: \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Heslo pre spoluu¾ívateµa';
$string['enrol_ldap_category'] = 'Kategória pre automaticky vytvorené kurzy';
$string['enrol_ldap_course_fullname'] = 'Nepovinné: LDAP polo¾ky, z ktorých sa má vybra» celé meno';
$string['enrol_ldap_course_idnumber'] = 'Plán jednoznaèného identifikátora v LDAP, obyèajne  <em>cn</em> alebo <em>uid</em>. Odporúèa sa \"uzamknú»\" túto hodnotu, ak pou¾ívate automatické vytváranie kurzov.';
$string['enrol_ldap_course_settings'] = 'Nastavenia zápisov do kurzov';
$string['enrol_ldap_course_shortname'] = 'Nepovinné: LDAP polo¾ky, z ktorých sa má vybra» skrátené meno';
$string['enrol_ldap_course_summary'] = 'Nepovinné: LDAP polo¾ky, z ktorých sa má vybra» sumár';
$string['enrol_ldap_editlock'] = 'Uzamknú» hodnotu';
$string['enrol_ldap_host_url'] = '©pecifikujte hos»ovský LDAP v URL forme, napr:  \'ldap://ldap.myorg.com/\'
alebo \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'Na vyhµadávanie kurzov sa pou¾íva objectClass. Obyèajne  \'posixGroup\'.';
$string['enrol_ldap_server_settings'] = 'Nastavenia LDAP servera';
$string['enrol_ldap_student_contexts'] = 'Zoznam kontextov, kde sú umiestnené skupiny so zápismi ¹tudentov. Rozdielne kontexty oddeµte  \';\', napr:  \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Atribút u¾ívateµa, keï u¾ívatelia patria (sú zapísaní) do skupiny. Obyèajne \'member\'
alebo \'memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Nastavenia zápisov ¹tudentov';
$string['enrol_ldap_teacher_contexts'] = 'Zoznam kontextov, kde sú umiestnené skupiny so zápismi uèiteµov. Rozdielne kontexty oddeµte  \';\', napr:  \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Atribút u¾ívateµa, keï u¾ívatelia patria (sú zapísaní) do skupiny. Obyèajne \'member\'
alebo \'memberUid\'.';
$string['enrol_ldap_teacher_settings'] = 'Nastavenia zápisov uèiteµov';
$string['enrol_ldap_template'] = 'Nepovinné: Pri automaticky vytváraných kurzoch sa mô¾u ich nastavenia kopírova» zo ¹ablóny kurzu.';
$string['enrol_ldap_updatelocal'] = 'Aktualizova» miestne údaje';
$string['enrol_ldap_version'] = 'Verzia LDAP protokolu, ktorú pou¾íva Vá¹ server.';
$string['enrolname'] = 'LDAP';

?>
