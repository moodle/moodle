<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.8.1 (2003011200)


$string['auth_dbdescription'] = "Este método utiliza una tabla de una base de datos externa para comprobar si un determinado usuario y contraseña son válidos. Si la cuenta es nueva, la información de otros campos puede también ser copiada en Moodle.";
$string['auth_dbextrafields'] = "Estos campos son opcionales. Usted puede elegir pre-rellenar algunos campos del usuario de Moodle con información de los <B>campos de la base de datos externa</B> que especifique aquí. <P>Si deja esto en blanco, se tomarán los valores por defecto.<P>En ambos casos, el usuario podrá editar todos estos campos después de entrar.";
$string['auth_dbfieldpass'] = "Nombre del campo que contiene las contraseñas";
$string['auth_dbfielduser'] = "Nombre del campo que contiene los nombres de usuario";
$string['auth_dbhost'] = "El ordenador que hospeda el servidor de la base de datos.";
$string['auth_dbname'] = "Nombre de la base de datos";
$string['auth_dbpass'] = "Contraseña correspondiente al usuario anterior";
$string['auth_dbpasstype'] = "Especifica el formato que usa el campo de contraseña. La encriptación MD5 es útil para conectar on otras aplicaciones web como PostNuke.";
$string['auth_dbtable'] = "Nombre de la tabla en la base de datos";
$string['auth_dbtitle'] = "Usar una base de datos externa";
$string['auth_dbtype'] = "El tipo de base de datos (Vea la <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentación</A> para más detalles)";
$string['auth_dbuser'] = "Usuario con acceso de lectura a la base de datos";
$string['auth_emaildescription'] = "La confirmación por correo alectrónico es el método de autenticación predeterminado. Cuando el usuario se inscribe, escogiendo su propio nombre de usuario y contraseña, un email de confirmación es enviado a su dirección de correo electrónico. Este email contiene un enlace seguro a una página donde el usuario puede confirmar su cuenta. Las futuras entradas comprueban el nombre de usuario y contraseña contra los valores guardados en la base de datos de Moodle.";
$string['auth_emailtitle'] = "Autenticación basada en Email";
$string['auth_imapdescription'] = "Este método usa un servidor IMAP para comprobar si el nombre de usuario y contraseña son válidos.";
$string['auth_imaphost'] = "La dirección del servidor IMAP. Use el número IP, no el nombre DNS.";
$string['auth_imapport'] = "Número del puerto del servidor IMAP. Normalmente es el 143 o 993.";
$string['auth_imaptitle'] = "Usar un servidor IMAP";
$string['auth_imaptype'] = "El tipo de servidor IMAP. Los servidores IMAP pueden tener diferentes tipos de autenticación y negociación.";
$string['auth_ldap_bind_dn'] = "Si quiere usar 'bind-user' para buscar usuarios, especifíquelo aquí. Algo como 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Contraseña para bind-user.";
$string['auth_ldap_contexts'] = "Lista de contextos donde los usuarios son ubicados. Separar contetos diferentes con ';'. Por ejemplo: 'ou=usuarios,o=org; ou=otros,o=org'";
$string['auth_ldap_host_url'] = "Especificar el host LDAP en forma de URL como 'ldap://ldap.myorg.com/' o 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_search_sub'] = "Poner el valor &lt;&gt; 0 si quiere buscar usuarios de subcontextos.";
$string['auth_ldap_update_userinfo'] = "Actualizar información del usuario (nombre, apellido, dirección..) desde LDAP a Moodle. Mire en /auth/ldap/attr_mappings.php para información de mapeado";
$string['auth_ldap_user_attribute'] = "El atributo usado para nombrar/buscar usuarios. Normalmente 'cn'.";
$string['auth_ldapdescription'] = "Este método proporciona autenticación contra un servidor LDAP externo.

Si el nombre de usuario y contraseña facilitados son válidos, Moodle crea una nueva entrada para el usuario en su base de datos. Este módulo puede leer atributos de usuario desde LDAP y prerrellenar los campos requeridos en Moodle. Para las entradas sucesivas sólo se comprueba el usuario y la contraseña.";
$string['auth_ldapextrafields'] = "Estos campos son opcionales. Usted puede elegir pre-rellenar algunos campos de usuario en Moodle con información de los <B>campos LDAP</B> que especifique aquí. <P>Si deja estos campos en blanco, entonces no se transferirá nada desde LDAP y se usará el sistema predeterminado en Moodle.<P>En ambos casos, los usuarios podrán editar todos estos campos después de entrar.";
$string['auth_ldaptitle'] = "Usar un servidor LDAP";
$string['auth_nntpdescription'] = "Este método usa un servidor NNTP para comprobar si el nombre de usuario y contraseña facilitados son válidos.";
$string['auth_nntphost'] = "La dirección del servidor NNTP. Usar el número IP, no el nombre DNS.";
$string['auth_nntpport'] = "Puerto del Servidor (119 es el más habitual)";
$string['auth_nntptitle'] = "Usar un servidor NNTP";
$string['auth_nonedescription'] = "Los usuarios pueden suscribirse y crear cuentas válidas inmediatamente, sin autenticación contra un servidor externo y sin confirmación vía email. Tenga cuidado al usar esta opción - piense en los problemas de seguridad y de administración que puede ocasionar.";
$string['auth_nonetitle'] = "Sin autenticación";
$string['auth_pop3description'] = "Este método utiliza un servidor POP3 para comprobar si el nombre de usuario y contraseña facilitados son válidos.";
$string['auth_pop3host'] = "La dirección del servidor POP3. Use el número IP, no el nombre DNS.";
$string['auth_pop3port'] = "Puerto del Servidor (110 es el más habitual)";
$string['auth_pop3title'] = "Usar un servidor POP3";
$string['auth_pop3type'] = "Tipo de Servidor. Si su servidor utiliza certificado de seguridad, escoja pop3cert.";
$string['authenticationoptions'] = "Opciones de Autenticación";
$string['authinstructions'] = "Aquí puede proporcionar instrucciones a sus usuarios, de forma que sepan que usuario y contraseña deben usar. El texto que incluya aquí aparecerá en la página de acceso. Si deja esto en blanco no aparecerán ningunas instrucciones.";
$string['changepassword'] = "Cambiar contraseña URL";
$string['changepasswordhelp'] = "Aquí puede especificar donde pueden sus usuarios recuperar o cambiar sus nombre de usuario/contraseña si los han olvidado. Para ello, aparecerá un botón en la página de entrada. Si deja esto en blanco, este botón no se mostrará.";
$string['chooseauthmethod'] = "Escoger un método de autenticación: ";
$string['guestloginbutton'] = "Botón de entrada para invitados";
$string['instructions'] = "Instrucciones";
$string['md5'] = "Encriptación M5";
$string['plaintext'] = "Texto plano";
$string['showguestlogin'] = "Puede ocultar o mostrar el botón de entrada para invitados en la página de acceso.";

?>
