<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2005033100)


$string['description'] = '<p>Du kan bruge en LDAP server til at kontrollere dine tilmeldinger. 
Det forudsættes at dit LDAP træ indeholder grupper der mappes til de enkelte kurser og enhver student der er er med i en gruppe vil blive tilmeldt kurset.</p>
<p> Det forudsættes at kurset er defineret som en gruppe i LDAP, som hver har flere medlemsfelter (<em>member</em> eller <em>memberUid</em>) der indeholder et unikt brugerid.</p>
<p>For at bruge LDAP tilmelding <strong>skal</strong>
alle brugere have et udfyldt IDnummer felt.
LDAP-gruppen skal så have det IDnummer som medlemsfelt for at en bruger bliver tilmeldt kurset. Det fungerer normalt udmærket hvis du allerede bruger LDAP autorisering.</p>
<p>Tilmeldingen vil blive opdateret når en bruger logger ind. Du kan også køre et script for at synkronisere tilmeldingerne. Scriptet ligger i <em>enrol/ldap/enrol_ldap_sync.php</em>.
<p>Dette plugin kan også konfigureres til at oprette nye kurser når en ny gruppe dukker op i LDAP.</p>';
$string['enrol_ldap_autocreate'] = 'Kurser kan oprettes automatisk hvis der er tilmeldinger til kurser der endnu ikke er oprettet i Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Automatisk oprettelse af kursus';
$string['enrol_ldap_bind_dn'] = 'Hvis du vil bruge bind-bruger til at søge efter brugere skal det specificeres her. Noget i stil med  \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Password for bind-bruger';
$string['enrol_ldap_category'] = 'Kategorien for autooprettede kurser';
$string['enrol_ldap_course_fullname'] = 'Valgfrit: LDAP felt indeholdende det fulde navn';
$string['enrol_ldap_course_idnumber'] = 'Map til det unikke ID i LDAP, som regel  <em>cn</em> eller <em>uid</em>. Det er tilrådeligt at låse værdierne hvis du benytter automatisk kursusoprettelse.';
$string['enrol_ldap_course_settings'] = 'Kursus tilmeldingsindstilling';
$string['enrol_ldap_course_shortname'] = 'Valgfrit: LDAP felt indeholdende kort navn.';
$string['enrol_ldap_course_summary'] = 'Valgfrit: LDAP felt indeholdende resume.';
$string['enrol_ldap_editlock'] = 'Lås værdi';
$string['enrol_ldap_host_url'] = 'Angiv adressen på LDAPserveren på URL form som f.eks. \'ldap://ldap.myorg.com/\' eller \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'objectClass brugt til at finde kurser. Som regel  \'posixGroup\'.';
$string['enrol_ldap_server_settings'] = 'LDAP Server indstilling';
$string['enrol_ldap_student_contexts'] = 'Liste af sammenhænge hvor grupperne med elevtilmeldinger ligger. Adskil forskellige sammenhænge med med \';\'. For eksempel:  \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Medlemsfelt når en bruger tilhører (er tilmeldt) til en gruppe. Som regel \'member\' eller \'memberUid\'';
$string['enrol_ldap_student_settings'] = 'Indstillinger for tilmelding af studerende';
$string['enrol_ldap_teacher_contexts'] = 'Liste af sammenhænge hvor grupper med lærertilmeldinger ligger. Adskil forskellige sammenhænge \';\'. For eksempel:  \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Medlems felt, når en bruger tilhører (bliver tilmeldt) til en gruppe. Normalt er det \'member\' eller \'memberUid\'';
$string['enrol_ldap_teacher_settings'] = 'Indstillinger for tilmelding af lærere';
$string['enrol_ldap_template'] = 'Valgfrit: Automatisk oprettede kurser kan kopiere deres indstillinger fra et \'skabelon\' kursus.';
$string['enrol_ldap_updatelocal'] = 'Opdater lokale data';
$string['enrol_ldap_version'] = 'Versionen af LDAP-protokollen som din server bruger.';
$string['enrolname'] = 'LDAP';

?>
