<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5 + (2005060201)


$string['description'] = '<p>Você pode usar um server LDAP para controlar as inscrições. Se presume que o ramo LDAP contenha grupos mapeados em relação aos cursos e que cada um destes grupos/cursos terá itens que identificam membros mapeados em relação aos estudantes.</p>
<p>Se presume que os cursos sejam definidos como grupos em LDAP, com cada grupo contendo campos múltiplos que identificam os membros (<em>member</em> ou <em>memberUid</em>) e que contém uma identificação unívoca do usuário </p>';
$string['enrol_ldap_autocreate'] = 'Podem ser criados cursos automaticamente quando existem inscrições em cursos ainda inexistentes.';
$string['enrol_ldap_autocreation_settings'] = 'Parâmetros de criação automática de cursos';
$string['enrol_ldap_bind_dn'] = 'Se você quiser usar o bind-user para buscar usuários, indicá-lo aqui. Algo como \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Password para o bind-user';
$string['enrol_ldap_category'] = 'Categoria para cursos criados automaticamente';
$string['enrol_ldap_course_fullname'] = 'Opcional: campo LDAP que define o nome completo';
$string['enrol_ldap_course_idnumber'] = 'Mapa ao identificador único em LDAP, normalmente <em>cn</em> ou <em>uid</em>. É recomendável o bloqueio do valor quando é ativada a criação automática de cursos.';
$string['enrol_ldap_course_settings'] = 'Configuração da Inscrição em Cursos';
$string['enrol_ldap_course_shortname'] = 'Opcional: campo LDAP que define o nome breve';
$string['enrol_ldap_course_summary'] = 'Opcional: campo LDAP que define o sumário';
$string['enrol_ldap_editlock'] = 'Bloquear valor';
$string['enrol_ldap_general_options'] = 'Opções Gerais';
$string['enrol_ldap_host_url'] = 'Definir o host LDAP em formato URL como \'ldap://ldap.myorg.com/\' 
ou \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'objectClass usado para buscar cursos. Normalmente é \'posixGroup\'.';
$string['enrol_ldap_search_sub'] = 'Buscar membros de grupos em subcontextos';
$string['enrol_ldap_server_settings'] = 'Parâmetros do Server LDAP';
$string['enrol_ldap_student_contexts'] = 'Lista de contextos onde grupos com inscrição de estudantes estão localizados. Separar contextos diferentes com \';\'. Por exemplo: 
\'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Atributo de membro quando os usuários estão inscritos em um grupo. Normalmente \'member\'
or \'memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Parâmetros de inscrição dos estudantes';
$string['enrol_ldap_teacher_contexts'] = 'Lista de contextos onde grupos com inscrição de docentes estão localizados. Separar contextos diferentes com \';\'. Por exemplo: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Atributo de um membro de um grupo. Normalmente \'member\' ou \'memberUid\'.';
$string['enrol_ldap_teacher_settings'] = 'Parâmetros de inscrição dos professores';
$string['enrol_ldap_template'] = 'Opcional: cursos criados automaticamente podem copiar as suas configurações a partir de um modelo de curso';
$string['enrol_ldap_updatelocal'] = 'Atualizar local data';
$string['enrol_ldap_version'] = 'A versão de protocolo LDAP que o seu servidor está usando';
$string['enrolname'] = 'LDAP';

?>
