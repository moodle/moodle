<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3.1 (2004052501)


$string['auth_dbdescription'] = 'Este método emprega unha táboa dunha base de datos externa para comprobar se un determinado usuario/a e contrasinal son válidos. Se a conta é nova, a información doutros campos pode tamén ser copiada en Moodle.';
$string['auth_dbextrafields'] = 'Estes campos son opcionais. Vostede pode elixir pre-cubrir algúns campos do usuario/a de Moodle con información desde os <strong>campos da base de datos externa</strong> que especifique aquí. <p>Se deixa isto en branco, tomaranse os valores por defecto</p>.<p>En ambos os dous casos, o usuario/a poderá editar todos estes campos despois de entrar</p>.';
$string['auth_dbfieldpass'] = 'Nome do campo que contén os contrasinais';
$string['auth_dbfielduser'] = 'Nome do campo que contén os nomes de usuario/a';
$string['auth_dbhost'] = 'O ordenador que hospeda o servidor da base de datos.';
$string['auth_dbname'] = 'Nome da base de datos';
$string['auth_dbpass'] = 'O contrasinal correspondente ao nome de usuario/a anterior';
$string['auth_dbpasstype'] = 'Especifique o formato que emprega o campo de contrasinal. A criptografía MD5 é útil para conectar con outras aplicacións web como PostNuke.';
$string['auth_dbtable'] = 'Nome da táboa na base de datos';
$string['auth_dbtitle'] = 'Usar unha base de datos externa';
$string['auth_dbtype'] = 'O tipo de base de datos (Vexa a <a href=../lib/adodb/readme.htm#drivers>documentación de ADOdb</a> para obter máis detalles)';
$string['auth_dbuser'] = 'Usuario/a con acceso de lectura á base de datos';
$string['auth_emaildescription'] = 'A confirmación por correo electrónico é o método de autenticación predeterminado. Cando o usuario/a se inscribe, escollendo o seu propio nome de usuario e contrasinal, envíaselle unha mensaxe electrónica de confirmación ao seu enderezo de correo electrónico. Esta mensaxe contén unha ligazón segura a unha páxina onde o usuario/a pode confirmar a súa conta. As futuras entradas comproban o nome de usuario e contrasinal contra os valores gardados na base de datos de Moodle.';
$string['auth_emailtitle'] = 'Autenticación baseada en correo electrónico';
$string['auth_imapdescription'] = 'Este método emprega un servidor IMAP para comprobar se o nome de usuario e contrasinal son válidos.';
$string['auth_imaphost'] = 'O enderezo do servidor IMAP. Empregue o número IP, non o nome DNS.';
$string['auth_imapport'] = 'Número do porto do servidor IMAP. Normalmente é o 143 ou 993.';
$string['auth_imaptitle'] = 'Usar un servidor IMAP';
$string['auth_imaptype'] = 'O tipo de servidor IMAP. Os servidores IMAP poden ter diferentes tipos de autenticación e negociación.';
$string['auth_ldap_bind_dn'] = 'Se quere empregar \'bind-user\' para buscar usuarios/as, especifíqueo aquí. Algo como \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Contrasinal para bind-user.';
$string['auth_ldap_contexts'] = 'Listaxe de contextos onde están localizados os usuarios/as. Separar contextos diferentes con \';\'. Por exemplo: \'ou=usuarios,o=org; ou=outros,o=org\'';
$string['auth_ldap_create_context'] = 'Habilítase a creación de usuario/a con confirmación por medio de correo electrónico, especifique o contexto no que se crean os usuarios/as. Este contexto debe ser diferente doutros usuarios/as para previr problemas de seguridade. Non é necesario engadir este contexto a ldap_context-variable, Moodle buscará automaticamente os usuarios/as deste contexto.';
$string['auth_ldap_creators'] = 'Lista de grupos en que os membros están autorizados para crear novos cursos. Poden separarse varios grupos con \';\'. Normalmente así: \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Especificar o host LDAP en forma de URL como \'ldap://ldap.myorg.com/\' ou \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Especificar o atributo para nome de usuario, cando os usuarios/as se integran nun grupo. Normalmente \'membro\'';
$string['auth_ldap_search_sub'] = 'Poña o valor <> 0u se quere buscar usuarios/as dende subcontextos.';
$string['auth_ldap_update_userinfo'] = 'Actualizar información do usuario/a (nome, apelido, enderezo...) dende LDAP a Moodle. Mire en /auth/ldap/attr_mappings.php para a información do mapa';
$string['auth_ldap_user_attribute'] = 'O atributo usado para nomear/buscar usuarios/as. Normalmente \'cn\'.';
$string['auth_ldap_version'] = 'A versión do protocolo LDAP que o seu servidor está empregando.';
$string['auth_ldapdescription'] = 'Este método proporciona autenticación contra un servidor LDAP externo.
Se o nome de usuario e contrasinal facilitados son válidos, Moodle crea unha nova entrada para o usuario/a na súa base de datos. Este módulo pode ler atributos de usuario dende LDAP e precubrir os campos requiridos en Moodle. Para as entradas sucesivas só se comproba o usuario/a e a contrasinal.';
$string['auth_ldapextrafields'] = 'Estes campos son opcionais. Vostede pode elixir pre-cubrir algúns campos de usuario en Moodle con información dos <strong>campos LDAP</strong> que especifique aquí. <p>Se deixa estes campos en branco, entón non se transferirá nada dende LDAP e usarase o sistema predeterminado en Moodle.</p><p>En ambos os dous casos, os usuarios/as poderán editar todos estos campos despois de entrar.</p>';
$string['auth_ldaptitle'] = 'Usar un servidor LDAP';
$string['auth_manualdescription'] = 'Este método elimina calquera forma de que os usuarios/as orixinen as súas propias contas. Todas as contas deben ser creadas manualmente polo administrador.';
$string['auth_manualtitle'] = 'Crear contas só de forma manual';
$string['auth_multiplehosts'] = 'É posible especificar múltiples servidores (por ex. servidor1.com;servidor2.com;servidor3.com';
$string['auth_nntpdescription'] = 'Este método emprega un servidor NNTP para comprobar se o nome de usuario e contrasinal facilitados son válidos.';
$string['auth_nntphost'] = 'O enderezo do servidor NNTP. Usar o número IP, non o nome DNS.';
$string['auth_nntpport'] = 'Porto do servidor (119 é o máis habitual)';
$string['auth_nntptitle'] = 'Usar un servidor NNTP';
$string['auth_nonedescription'] = 'Os usuarios/as poden rexistrarse e crear contas válidas inmediatamente, sen autenticación contra un servidor externo e sen confirmación vía correo electrónico. Teña coidado ao empregar esta opción -pense nos problemas de seguridade e de administración que pode ocasionar.';
$string['auth_nonetitle'] = 'Sen autenticación';
$string['auth_pop3description'] = 'Este método emprega un servidor POP3 para comprobar se o nome de usuario e contrasinal facilitados son válidos.';
$string['auth_pop3host'] = 'O enderezo do servidor POP3. Use o número IP, non o nome DNS.';
$string['auth_pop3port'] = 'Porto do servidor (110 é o máis habitual)';
$string['auth_pop3title'] = 'Usar un servidor POP3';
$string['auth_pop3type'] = 'Tipo de servidor. Se o seu servidor utiliza certificado de seguridade, escolla pop3cert.';
$string['auth_user_create'] = 'Habilitar creación por parte do usuario/a';
$string['auth_user_creation'] = 'Os novos usuarios/as (anónimos/as) poden crear contas de usuario sobre o código externo de autenticación e confirmar vía correo electrónico. Se vostede habilita isto, recorde tamén configurar as opcións do módulo específico para a creación de usuario.';
$string['auth_usernameexists'] = 'O nome de usuario seleccionado xa existe. Por favor, elixa outro.';
$string['authenticationoptions'] = 'Opcións de autenticación';
$string['authinstructions'] = 'Aquí pode proporcionar instrucións aos seus usuarios/as, de forma que saiban qué usuario e contrasinal deben empregar. O texto que inclúa aquí aparecerá na páxina de acceso. Se deixa isto en branco non aparecerá ningunha instrución.';
$string['changepassword'] = 'Cambiar contrasinal URL';
$string['changepasswordhelp'] = 'Aquí pode especificar onde poden os seus usuarios/as recuperar ou cambiar o seu nome de usuario/contrasinal se o esqueceron. Para iso, aparecerá un botón na páxina de entrada. Se deixa isto en branco, este botón non se amosará.';
$string['chooseauthmethod'] = 'Escoller un método de autenticación: ';
$string['guestloginbutton'] = 'Botón de entrada para convidados/as';
$string['instructions'] = 'Instrucións';
$string['md5'] = 'Criptografía M5';
$string['plaintext'] = 'Texto plano';
$string['showguestlogin'] = 'Pode ocultar ou amosar o botón de entrada para convidados na páxina de acceso.';

?>
