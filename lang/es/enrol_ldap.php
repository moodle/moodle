<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2004093001)


$string['description'] = '<p>Usted puede utilizar un servidor LDAP para coltrolar sus matriculaciones. Se asume que su árbol LDAP contiene grupos que apuntan a los cursos, y que cada uno de esos grupos o cursos contienen entradas de matriculación que hacen referencia a los estudiantes.</p>
<p>Se asume que los cursos están definidos como grupos en LDAP, de modo que cada grupo tiene múltiples campos de matriculación  (<em>member</em> or <em>memberUid</em>) que contienen una identificación única del usuario.</p>
<p>Para usar la matriculación LDAP, los usuarios <strong>deben</strong> tener un campo \'idnumber\' válido. Los grupos LDAP deben contener ese \'idnumber\' en los campos de membresía para que un usuario pueda matricularse en un curso. Esto normalmente funcionará bien si usted ya está usando la Autenticación LDAP.</p>
<p>Las matriculaciones se actualizarán cuando el usuario se identifica. Consulte en <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>Este plugin puede también ajustarse para crear nuevos cursos de forma automática cuando aparecen nuevos grupos en LDAP.</p>';
$string['enrol_ldap_autocreate'] = 'Los cursos pueden crearse automáticamente si existen matriculaciones en un curso que aún no existe en Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Ajustes para la creación automática de cursos';
$string['enrol_ldap_bind_dn'] = 'Si desea usar \'bind-user\' para buscar usuarios, especifíquelo aquí. Algo como \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Contraseña para \'bind-user\'.';
$string['enrol_ldap_category'] = 'Categoría para cursos auto-creados.';
$string['enrol_ldap_course_fullname'] = 'Opcional: campo LDAP del que conseguir el nombre completo.';
$string['enrol_ldap_course_idnumber'] = 'Mapa del identificador único en LDAP, normalmente  <em>cn</em> or <em>uid</em>. Se recomienda bloquear el valor si se está utilizando la creación automática del curso.';
$string['enrol_ldap_course_settings'] = 'Ajustes de matriculación de Curso';
$string['enrol_ldap_course_shortname'] = 'Opcional: campo LDAP del que conseguir el nombre corto.';
$string['enrol_ldap_course_summary'] = 'Opcional: campo LDAP del que conseguir el sumario.';
$string['enrol_ldap_editlock'] = 'Bloquear valor';
$string['enrol_ldap_host_url'] = 'Especifique el host LDAP en formato URL, e.g.,  \'ldap://ldap.myorg.com/\'
or \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'objectClass usada para buscar cursos. Normalmente
\'posixGroup\'.';
$string['enrol_ldap_server_settings'] = 'Ajustes de Servidor LDAP';
$string['enrol_ldap_student_contexts'] = 'Lista de contextos en que se ubican los grupos con matriculaciones de estudiantes. Separe los distintos contextos con \';\'. Por ejemplo:  \'ou=cursos,o=org; ou=otros,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Atributo de miembro, cuando el usuario pertenece a un grupo (i.e., está matriculado). Normalmente \'miembro\' o \'memberUid\'-';
$string['enrol_ldap_student_settings'] = 'Ajustes de matriculación de estudiantes';
$string['enrol_ldap_teacher_contexts'] = 'Lista de contextos en que se ubican los grupos con matriculaciones de profesores. Separe los distintos contextos con \';\'. Por ejemplo:  \'ou=cursos,o=org; ou=otros,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Atributo de miembro, cuando el usuario pertenece a un grupo (i.e., está matriculado). Normalmente \'miembro\' o \'memberUid\'-';
$string['enrol_ldap_teacher_settings'] = 'Ajustes de matriculación de profesores';
$string['enrol_ldap_template'] = 'Opcional: los cursos auto-creados pueden copiar sus ajustes a partir de un curso-plantilla.';
$string['enrol_ldap_updatelocal'] = 'Actualizar datos locales';
$string['enrol_ldap_version'] = 'Versión del protocolo LDAP usado por el servidor.';
$string['enrolname'] = 'LDAP';
$string['parentlanguage'] = 'es';
$string['thischarset'] = 'iso-8859-1';
$string['thisdirection'] = 'ltr';
$string['thislanguage'] = 'es';

?>
