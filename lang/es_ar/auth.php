<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.6.4 (2002112400)

// Adaptado a es_AR por Rodrigo Vigil (rmvigil@frre.utn.edu.ar)
$string['auth_dbdescription'] = "Este m&eacute;todo utiliza una tabla de una base de datos externa para comprobar si un determinado usuario y contrase&ntilde;a son v&aacute;lidos. Si la cuenta es nueva, la informaci&oacute;n de otros campos tambi&eacute;n puede ser copiada en Moodle.";
$string['auth_dbextrafields'] = "Estos campos son opcionales. Usted puede elegir pre-completar algunos campos del usuario de Moodle con informaci&oacute;n de los <B>campos de la base de datos externa</B> que especifique aqu&iacute;. <P>Si deja en blanco, se tomar&aacute;n los valores por defecto.<P>En ambos casos, el usuario podr&aacute; editar todos estos campos despu&eacute;s de ingresar.";
$string['auth_dbfieldpass'] = "Nombre del campo que contiene las contrase&ntilde;as";
$string['auth_dbfielduser'] = "Nombre del campo que contiene los nombres de usuario";
$string['auth_dbhost'] = "La computadora que ejecuta el servidor de base de datos.";
$string['auth_dbname'] = "Nombre de la base de datos";
$string['auth_dbpass'] = "Contrase&ntilde;a correspondiente al usuario anterior";
$string['auth_dbtable'] = "Nombre de la tabla en la base de datos";
$string['auth_dbtitle'] = "Usar una base de datos externa";
$string['auth_dbtype'] = "El tipo de base de datos (Vea la <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentaci&oacute;n</A> para m&aacute;s detalles)";
$string['auth_dbuser'] = "Usuario con acceso de lectura a la base de datos";
$string['auth_emaildescription'] = "La confirmaci&oacute;n mediante correo alectr&oacute;nico es el m&eacute;todo de autenticaci&oacute;n predeterminado. Cuando el usuario se inscribe, escogiendo su propio nombre de usuario y contrase&ntilde;a, un email de confirmaci&oacute;n es enviado a su direcci&oacute;n de correo electr&oacute;nico. Este email contiene un enlace seguro a una p&aacute;gina donde el usuario puede confirmar su cuenta. Las futuras entradas al sistema comprueban el nombre de usuario y contrase&ntilde;a contra los valores almacenados en la base de datos de Moodle.";
$string['auth_emailtitle'] = "Autenticaci&oacute;n basada en Email";
$string['auth_imapdescription'] = "Este m&eacute;todo usa un servidor IMAP para comprobar si el nombre de usuario y contrase&ntilde;a son v&aacute;lidos.";
$string['auth_imaphost'] = "La direcci&oacute;n del servidor IMAP. Use el n&uacute;mero IP, no el nombre DNS.";
$string['auth_imapport'] = "N&uacute;mero del puerto del servidor IMAP. Normalmente es 143 o 993.";
$string['auth_imaptitle'] = "Usar un servidor IMAP";
$string['auth_imaptype'] = "El tipo de servidor IMAP. Los servidores IMAP pueden tener diferentes tipos de autenticaci&oacute;n y negociaci&oacute;n.";
$string['auth_ldap_bind_dn'] = "Si quiere usar 'bind-user' para buscar usuarios, especif&iacute;quelo aqu&iacute;. Algo como 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Contrase&ntilde;a para bind-user.";
$string['auth_ldap_contexts'] = "Lista de contextos donde los usuarios son ubicados. Separar contextos diferentes con ';'. Por ejemplo: 'ou=usuarios,o=org; ou=otros,o=org'";
$string['auth_ldap_host_url'] = "Especificar el host LDAP en forma de URL como 'ldap://ldap.myorg.com/' o 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_search_sub'] = "Poner el valor &lt;&gt; 0 si desea buscar usuarios de subcontextos.";
$string['auth_ldap_update_userinfo'] = "Actualizar informaci&oacute;n del usuario (nombre, apellido, direcci&oacute;n..) desde LDAP a Moodle. Vea en /auth/ldap/attr_mappings.php para informaci&oacute;n de mapeado";
$string['auth_ldap_user_attribute'] = "El atributo usado para nombrar/buscar usuarios. Normalmente es 'cn'.";
$string['auth_ldapdescription'] = "Este m&eacute;todo proporciona autenticaci&oacute;n contra un servidor LDAP externo.
Si el nombre de usuario y contrase&ntilde;a facilitados son v&aacute;lidos, Moodle crea una nueva entrada para el usuario en su base de datos. Este m&oacute;dulo puede leer atributos de usuario desde LDAP y prerrellenar los campos requeridos en Moodle. Para las entradas sucesivas s&oacute;lo se comprueba el usuario y la contrase&ntilde;a.";
$string['auth_ldapextrafields'] = "Estos campos son opcionales. Usted puede elegir pre-completar algunos campos de usuario en Moodle con informaci&oacute;n proveniente de los <B>campos LDAP</B> que especifique aqu&iacute;. <P>Si deja estos campos en blanco, entonces no se transferir&aacute; nada desde LDAP y se usar&aacute; el sistema predeterminado en Moodle.<P>En ambos casos, los usuarios podr&aacute;n editar todos estos campos despu&eacute;s de ingresar al sistema.";
$string['auth_ldaptitle'] = "Usar un servidor LDAP";
$string['auth_nntpdescription'] = "Este m&eacute;todo usa un servidor NNTP para comprobar si el nombre de usuario y contrase&ntilde;a facilitados son v&aacute;lidos.";
$string['auth_nntphost'] = "La direcci&oacute;n del servidor NNTP. Usar el n&uacute;mero IP, no el nombre DNS.";
$string['auth_nntpport'] = "Puerto del Servidor NNTP (119 es el m&aacute;s habitual)";
$string['auth_nntptitle'] = "Usar un servidor NNTP";
$string['auth_nonedescription'] = "Sin autenticaci&oacute;n contra un servidor externo y sin confirmaci&oacute;n v&iacute;a email, los usuarios pueden suscribirse y crear cuentas v&aacute;lidas inmediatamente. Tenga cuidado al usar esta opci&oacute;n - piense en los problemas de seguridad y de administraci&oacute;n que puede ocasionar.";
$string['auth_nonetitle'] = "Sin autenticaci&oacute;n";
$string['auth_pop3description'] = "Este m&eacute;todo utiliza un servidor POP3 para comprobar si el nombre de usuario y contrase&ntilde;a facilitados son v&aacute;lidos.";
$string['auth_pop3host'] = "La direcci&oacute;n del servidor POP3. Use el n&uacute;mero IP, no el nombre DNS.";
$string['auth_pop3port'] = "Puerto del Servidor (110 es el m&aacute;s habitual)";
$string['auth_pop3title'] = "Usar un servidor POP3";
$string['auth_pop3type'] = "Tipo de Servidor. Si el servidor utiliza certificado de seguridad, elija pop3cert.";
$string['authenticationoptions'] = "Opciones de Autenticaci&oacute;n";
$string['authinstructions'] = "Aqu&iacute; puede proporcionar instrucciones a sus usuarios, de forma que sepan que usuario y contrase&ntilde;a deben usar. El texto que incluya aqu&iacute; aparecer&aacute; en la p&aacute;gina de acceso. Si deja esto en blanco no aparecer&aacute;n instrucciones.";
$string['changepassword'] = "Cambiar contrase&ntilde;a URL";
$string['changepasswordhelp'] = "Aqu&iacute; puede especificar donde sus usuarios pueden recuperar o cambiar sus nombre de usuario/contrase&ntilde;a si los han olvidado. Para ello, aparecer&aacute; un bot&oacute;n en la p&aacute;gina de entrada. Si deja esto en blanco, este bot&oacute;n no se mostrar&aacute;.";
$string['chooseauthmethod'] = "Escoger un m&eacute;todo de autenticaci&oacute;n: ";
$string['guestloginbutton'] = "Bot&oacute;n de entrada para invitados";
$string['instructions'] = "Instrucciones";
$string['showguestlogin'] = "Puede ocultar o mostrar el bot&oacute;n de entrada para invitados en la p&aacute;gina de acceso.";

?>
