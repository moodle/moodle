<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.6 development (2005060201)


$string['description'] = '<p>Si può usare un server LDAP per controllare le vostre registrazioni. Viene presupposto che l\'albero LDAP contenga gruppi che corrispondano ai corsi, e che ognuno di questi gruppi/corsi avrà la sua istanza di appartenenza per corrispondere agli studenti.</p>
<p>Viene presupposto che i corsi siano definiti come gruppi in LDAP, con ogni gruppo che possiede campi di appartenenza multipli(<em>membro</em> o <em>Uid membro</em>) che contiene una identificazione unica dell\'utente.</p>
<p>Per utilizzare la registrazione LDAP, i vostri utenti<strong>devono</strong> 
avere un campo idnumber valido. I gruppi LDAP devono avere questo idnumber nei campi membro per utenti che si iscrivono a quel corso.</p>
<p>Questo metodo funzionerà bene se state comunque utilizzando l\'autenticazione LDAP.</p/>
<p>Le iscrizioni  verranno agiornate quando l\'utente effettua il login. Potete comunque lanciare uno script per tenere le iscrizioni in sincrono. Cercate in 
<em>enrol/ldap/enrol_ldap_sync.php</em>.</p>

<p>Questo plugin può anche venire settato in modo che crei automaticamente nuovi corsi quando appaiono nuovi gruppi in LDAP.</p>';
$string['enrol_ldap_autocreate'] = 'I corsi possono essere creati automaticamente se ci sono iscrizioni in un corso che non esistono ancora in Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Impostazioni creazione automatica corsi';
$string['enrol_ldap_bind_dn'] = 'Se desiderate utilizzare bind-user per la ricerca di utenti, specificatelo qui. Qualcosa come \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Password per utenti bind';
$string['enrol_ldap_category'] = 'La categoria per corsi auto-creati';
$string['enrol_ldap_course_fullname'] = 'Opzionale: campo LDAP per ottenere il nome completo da.';
$string['enrol_ldap_course_idnumber'] = 'Mappa verso l\'identificatore unico in LDAP, di solito <em>cn</em> oppure <em>uid</em>.';
$string['enrol_ldap_course_settings'] = 'Impostazioni per l\'iscrizione al corso';
$string['enrol_ldap_course_shortname'] = 'Opzionale: campo LDAP per ottenere nomi corti da.';
$string['enrol_ldap_course_summary'] = 'Opzionale: campo LDAP per ottenere sommario da.';
$string['enrol_ldap_editlock'] = 'Blocca valore';
$string['enrol_ldap_general_options'] = 'Opzioni Generali';
$string['enrol_ldap_host_url'] = 'Specifica host LDAP in formato-URL come \'ldap://ldap.miosito.com/\' o \'ldaps://ldap.miosito.com/\'';
$string['enrol_ldap_objectclass'] = 'Classe oggetto utilizzata per ricerca corsi. solitamente, \'posixGroup\'.';
$string['enrol_ldap_search_sub'] = 'Cerca l\'appartenenza al gruppo a partire dal contesto.';
$string['enrol_ldap_server_settings'] = 'Impostazioni del Server LDAP';
$string['enrol_ldap_student_contexts'] = 'Lista dei contesti dove vengono collocati i gruppi con registrazioni studente. Separare contesti differenti con \'.\'. Per esempio: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'attributo Membro, quando gli utenti appartengono (sono iscritti) ad un gruppo. Di solito, \'membro\' o \'uid membro\'.';
$string['enrol_ldap_student_settings'] = 'Impostazioni registrazione studente';
$string['enrol_ldap_teacher_contexts'] = 'Lista dei contesti dove vengono collocati i gruppi con registrazioni docente. Separare contesti differenti con \'.\'. Per esempio: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'attributo Membro, quando gli utenti appartengono (sono iscritti) ad un gruppo. Di solito, \'membro\' o \'uid membro\'.';
$string['enrol_ldap_teacher_settings'] = 'Impostazioni registrazione docente';
$string['enrol_ldap_template'] = 'Opzionale: corsi auto-creati possono copiare le loro impostazioni da un modello (template) corso.';
$string['enrol_ldap_updatelocal'] = 'Aggiorna dati locali';
$string['enrol_ldap_version'] = 'Versione del protocollo LDAP utilizzata dal server';
$string['enrolname'] = 'LDAP';

?>
