<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.4.3 + (2004083131)


$string['description'] = '<p>Du kan använda en LDAP-server för att styra Dina registreringar. Utgångspunkten är att Ditt LDAP-träd innehåller grupper som visar en karta till kurserna och att var och en av dessa grupper/kurser har kartor över medlemsdata som visar vägen till studenterna/eleverna/deltagarna/de lärande</p><p>Utgångspunkten är att kurser är definierade som grupper i LDAP där varje grupp har ett flertal fält för medlemsskap (<em>member</em> eller <em>memberUid</em>) som innehåller en unik identifiering av användaren.</p><p>För att använda LDAP-registrering <strong>måste</strong> Dina användare ha giltiga fält för ID-nummer. LDAP-grupperna måste ha detta ID-nummer i fältet för medlemmar för att man ska kunna registrera en användare på en kurs. Detta kommer i normalfallet att fungera bra om Du redan använder autenticering via LDAP.</p><p>Registreringarna kommer att uppdateras när användaren loggar in. Du kan också köra ett skript för att synkronisera registreringarna. Titta i <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>Denna plugin kan också ställas in så att den automatiskt skapar nya kurser när det dyker upp nya grupper i LDAP.</p>';
$string['enrol_ldap_autocreate'] = 'Kurser kan skapas automatiskt om det finns registreringar på en kurs som ännu inte finns i Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Inställningar för att skapa kurser automatiskt.';
$string['enrol_ldap_bind_dn'] = 'Om Du vill använda \"bind\"-användare för att söka användare så ska Du ange detta här. Någonting i stil med \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Lösenord för \'bind\'-användare';
$string['enrol_ldap_category'] = 'Kategorin för automatiskt skapade kurser';
$string['enrol_ldap_course_fullname'] = 'Valfritt: LDAP-fält för att hämta det kompletta namnet från';
$string['enrol_ldap_course_idnumber'] = 'Karta som visar var den unika identifieraren i LDAP finns, vanligtvis <em>cn</em> or <em>uid</em>. Du rekommenderas att låsa detta  värde om Du använder automatiskt skapande av kurser.';
$string['enrol_ldap_course_settings'] = 'Inställningar för registrering på kurser';
$string['enrol_ldap_course_shortname'] = 'Valfritt: LDAP-fält att hämta kortnamnet från.';
$string['enrol_ldap_course_summary'] = 'Valfritt: LDAP-fält att hämta sammanfattningen från.';
$string['enrol_ldap_editlock'] = 'Låsets värde';
$string['enrol_ldap_host_url'] = 'Ange LDAP-värden i URL-form som \'ldap://ldap.myorg.com/\' 
eller \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'objectClass som används för att söka kurser. Vanligtvis \'posixGroup\'.';
$string['enrol_ldap_server_settings'] = 'Inställningar för LDAP-server';
$string['enrol_ldap_student_contexts'] = 'Lista över sammanhang där grupper med registreringar av studenter/elever/deltagare/lärande är placerade. Skilj olika sammanhang åt med \';\'. T.ex. \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Attribut till medlem, när användare tillhör (är registrerade i) en grupp. Vanligtvis \'member\'
eller \'memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Inställningar för registrering av student/elev/deltagare/lärande';
$string['enrol_ldap_teacher_contexts'] = 'Lista över sammanhang där grupper med registreringar av (distans)lärare har placerats. Skilj olika sammanhang åt med \';\'. Till exempel: 
\'ou=kurser,o=org; ou=andra,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Attribut till medlem (member), när användare tillhör (är registrerade i) en grupp. Vanligtvis \'medlem\' (member) eller \'medlemAnvid\'
(memberUid).';
$string['enrol_ldap_teacher_settings'] = 'Inställningar för registrering av (distans)lärare';
$string['enrol_ldap_template'] = 'Valfritt: automatiskt skapade kurser kan kopiera sina inställningar från en kursmall.';
$string['enrol_ldap_updatelocal'] = 'Uppdatera lokala data';
$string['enrol_ldap_version'] = 'Detta är den version av LDAP-protokollet som DIn server använder.';
$string['enrolname'] = 'LDAP';

?>
