<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5.3+ (2005060230)


$string['description'] = '<p>Podeu utilitzar un servidor LDAP per controlar les inscripcions. S\'assumeix que el vostre arbre LDAP conté grups que es corresponen als cursos i que cada grup/curs tindrà entrades membres corresponents als estudiants.</p>
<p>S\'assumeix que els cursos estan definits com a grups en el LDAP i que cada grup té múltiples camps de membre (<em>member</em> o <em>memberUid</em>) que contenen una identificació única de l\'usuari.</p>
<p>Per a utilitzar inscripció per LDAP, <strong>cal</strong> que els vostres usuaris tinguin un camp idnumber vàlid. Els grups LDAP han de tenir aquest idnumber en els camps de membre perquè un usuari sigui inscrit en el curs. Si ja esteu utilitzant autenticació per LDAP, generalment això us funcionarà.</p>
<p>Les inscripcions s\'actualitzen quan entra l\'usuari. També podeu executar una seqüència per mantenir sincronitzades les inscripcions. Doneu una ullada a <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>Aquest connector també es pot configurar per crear automàticament nous cursos quan apareixen nous grups en el LDAP.</p>';
$string['enrol_ldap_autocreate'] = 'Els cursos poden ser creats automàticament si hi ha inscripcions a un curs que enncara no existeix en Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Paràmetres de creació automàtica de cursos';
$string['enrol_ldap_bind_dn'] = 'Si voleu utilitzar el bind-user per cercar usuaris, especifiqueu-ho aquí. P. ex. \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Contrasenya del bind-user.';
$string['enrol_ldap_category'] = 'Categoria per als cursos creats automàticament.';
$string['enrol_ldap_course_fullname'] = 'Opcional: camp del LDAP d\'on es pot treure el nom complet.';
$string['enrol_ldap_course_idnumber'] = 'Identificador únic en el LDAP, generalment <em>cn</em> o <em>uid</em>. Es recomana blocar aquest valor si utilitzeu la creació automàtica de cursos.';
$string['enrol_ldap_course_settings'] = 'Paràmetres d\'inscripció de cursos';
$string['enrol_ldap_course_shortname'] = 'Opcional: camp del LDAP d\'on es pot treure el nom curt.';
$string['enrol_ldap_course_summary'] = 'Opcional: camp del LDAP d\'on es pot treure el resum.';
$string['enrol_ldap_editlock'] = 'Bloca valor';
$string['enrol_ldap_general_options'] = 'Opcions generals';
$string['enrol_ldap_host_url'] = 'Especifiqueu el servidor LDAP en forma d\'URL, p. ex. \'ldap://ldap.myorg.com/\' o \'ldaps://ldap.myorg.com/\'.';
$string['enrol_ldap_objectclass'] = 'objectClass utilitzada per cercar cursos. Generalment \'posixGroup\'.';
$string['enrol_ldap_search_sub'] = 'Cerca la pertinença a grups en subcontextos.';
$string['enrol_ldap_server_settings'] = 'Paràmetres del servidor LDAP';
$string['enrol_ldap_student_contexts'] = 'Llista de contextos en els quals es troben els grups amb inscripcions d\'estudiants. Separeu els diferents contextos amb punt i coma, p. ex. \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Atribut de membre, quan l\'usuari pertany a un grup (hi està inscrit). Generalment el \'member\' o \'memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Paràmetres d\'inscripció de l\'estudiantat';
$string['enrol_ldap_teacher_contexts'] = 'Llista de contextos en els quals es troben els grups amb inscripcions de professorat. Separeu els diferents contextos amb punt i coma, p. ex. \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Atribut de membre, quan l\'usuari pertany a un grup (hi està inscrit). Generalment el \'member\' o \'memberUid\'.';
$string['enrol_ldap_teacher_settings'] = 'Paràmetres d\'inscripció del professorat';
$string['enrol_ldap_template'] = 'Opcional: els cursos creats automàticament poden copiar els seus paràmetres d\'un curs plantilla.';
$string['enrol_ldap_updatelocal'] = 'Actualitza dades locals';
$string['enrol_ldap_version'] = 'La versió del protocol LDAP que utilitza el vostre servidor.';
$string['enrolname'] = 'LDAP';

?>
