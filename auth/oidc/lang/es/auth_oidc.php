<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Spanish language strings.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'OpenID Connect';
$string['auth_oidcdescription'] = 'El complemento OpenID Connect ofrece funcionalidad de inicio de sesión único a través de proveedores de identidad configurables.';
$string['cfg_authendpoint_key'] = 'Extremo de autorización';
$string['cfg_authendpoint_desc'] = 'La URI del extremo de autorización del proveedor de identidad que va a utilizar.';
$string['cfg_autoappend_key'] = 'Anexo automático';
$string['cfg_autoappend_desc'] = 'Anexe automáticamente esta cadena cuando los usuarios inicien sesión mediante el flujo de inicio de sesión de nombre de usuario/contraseña. Esto es útil cuando el proveedor de identidad requiere un dominio común, pero no desea solicitar a los usuarios que lo escriban cuando inician sesión. Por ejemplo, si el usuario completo de OpenID Connect es "james@example.com" y usted escribe "@example.com" aquí, el usuario solo deberá escribir "james" como nombre de usuario. <br /><b>Nota:</b> En el caso que existan conflictos con los nombres de usuarios (es decir, un usuario de Moodle ya existe con el mismo nombre), se utiliza la prioridad del complemento de autenticación para determinar cuál de los usuarios gana.';
$string['cfg_clientid_key'] = 'ID de cliente';
$string['cfg_clientid_desc'] = 'Su ID de cliente registrado en el proveedor de identidad';
$string['cfg_clientsecret_key'] = 'Secreto de cliente';
$string['cfg_clientsecret_desc'] = 'Su secreto de cliente registrado en el proveedor de identidad. En algunos proveedores, también se conoce como clave.';
$string['cfg_err_invalidauthendpoint'] = 'Extremo de autorización no válido';
$string['cfg_err_invalidtokenendpoint'] = 'Extremo de ficha no válido';
$string['cfg_err_invalidclientid'] = 'ID de cliente no válido';
$string['cfg_err_invalidclientsecret'] = 'Secreto de cliente no válido';
$string['cfg_icon_key'] = 'Icono';
$string['cfg_icon_desc'] = 'Un icono para mostrar junto al nombre del proveedor en la página de inicio de sesión.';
$string['cfg_iconalt_o365'] = 'Icono de Microsoft 365';
$string['cfg_iconalt_locked'] = 'Icono de bloqueado';
$string['cfg_iconalt_lock'] = 'Icono de bloqueo';
$string['cfg_iconalt_go'] = 'Círculo verde';
$string['cfg_iconalt_stop'] = 'Círculo rojo';
$string['cfg_iconalt_user'] = 'Icono de usuario';
$string['cfg_iconalt_user2'] = 'Icono de usuario alternativo';
$string['cfg_iconalt_key'] = 'Icono de clave';
$string['cfg_iconalt_group'] = 'Icono de grupo';
$string['cfg_iconalt_group2'] = 'Icono de grupo alternativo';
$string['cfg_iconalt_mnet'] = 'Icono de MNET';
$string['cfg_iconalt_userlock'] = 'Icono de usuario con bloqueo';
$string['cfg_iconalt_plus'] = 'Icono de Plus';
$string['cfg_iconalt_check'] = 'Icono de marca de verificación';
$string['cfg_iconalt_rightarrow'] = 'Icono de flecha a la derecha';
$string['cfg_customicon_key'] = 'Icono personalizado';
$string['cfg_customicon_desc'] = 'Si desea utilizar su propio icono, cárguelo aquí. Esto anula cualquier icono que haya elegido arriba. <br /><br /><b>Notas sobre el uso de iconos personalizados:</b><ul><li>Esta imagen <b>no</b> cambiará de tamaño en la página de inicio de sesión, de manera que le recomendamos que cargue una imagen no mayor a 35x35 píxeles.</li><li>Si cargó un icono personalizado y desea volver a algunos de los iconos preestablecidos, haga clic en el icono personalizado en el cuadro de arriba, luego haga clic en "Eliminar", luego en "Aceptar", y luego en "Guardar cambios" en la parte inferior de este formulario. El icono preestablecido seleccionado aparecerá ahora en la página de inicio de sesión de Moodle.</li></ul>';
$string['cfg_debugmode_key'] = 'Registrar mensajes de depuración';
$string['cfg_debugmode_desc'] = 'Si está habilitado, se registrará información en el registro de Moodle que puede ayudarlo a identificar problemas.';
$string['cfg_loginflow_key'] = 'Flujo de inicio de sesión';
$string['cfg_loginflow_authcode'] = 'Solicitud de autorización';
$string['cfg_loginflow_authcode_desc'] = 'Al utiliza este flujo, el usuario hace clic en el nombre del proveedor de identidad (consulte "Nombre del proveedor" más arriba) en la página de inicio de sesión de Moodle y es redireccionado al proveedor para iniciar sesión. Una vez que haya iniciado sesión correctamente, es redireccionado de vuelta a Moodle, donde se realiza el inicio de sesión de manera transparente. Esta es la forma más segura y estandarizada de inicio de sesión del usuario.';
$string['cfg_loginflow_rocreds'] = 'Autenticación de nombre de usuario/contraseña';
$string['cfg_loginflow_rocreds_desc'] = 'Al usar este flujo, el usuario ingresa el nombre de usuario y la contraseña al formulario de inicio de sesión de Moodle como lo haría de manera manual. Luego, las credenciales se pasan al proveedor de identidad en el segundo plano para obtener la autenticación. Este flujo es la forma más transparente para el usuario ya que no posee interacción directa con el proveedor de identidad. Tenga en cuenta que no todos los proveedores de identidad admiten este flujo.';
$string['cfg_oidcresource_key'] = 'Recurso';
$string['cfg_oidcresource_desc'] = 'El recurso de OpenID Connect para el cual enviar la solicitud.';
$string['cfg_oidcscope_key'] = 'Scope';
$string['cfg_oidcscope_desc'] = 'El alcance de OIDC a utilizar.';
$string['cfg_opname_key'] = 'Nombre del proveedor';
$string['cfg_opname_desc'] = 'Esta es una etiqueta que apunta al usuario final e identifica el tipo de credenciales que el usuario debe utilizar para iniciar sesión. Esta etiqueta se utiliza durante todas las partes que apuntan al usuario de este complemento para identificar al proveedor.';
$string['cfg_redirecturi_key'] = 'URI de redireccionamiento';
$string['cfg_redirecturi_desc'] = 'Est es la URI para registrar como "URI de redireccionamiento". Su proveedor de identidad de OpenID Connect debe solicitarla cuando registra Moodle como cliente. <br /><b>NOTA:</b> Debe ingresarla en su proveedor de OpenID Connect *exactamente* como aparece aquí. Cualquier diferencia evitará el inicio de sesión usando OpenID Connect.';
$string['cfg_tokenendpoint_key'] = 'Extremo de ficha';
$string['cfg_tokenendpoint_desc'] = 'La URI del extremo de ficha del proveedor de identidad que debe utilizar.';
$string['event_debug'] = 'Mensaje de depuración';
$string['errorauthdisconnectemptypassword'] = 'La contraseña no puede estar vacía';
$string['errorauthdisconnectemptyusername'] = 'El nombre de usuario no puede estar vacío';
$string['errorauthdisconnectusernameexists'] = 'Ese nombre de usuario ya está en uso. Elija uno distinto.';
$string['errorauthdisconnectnewmethod'] = 'Usar método de inicio de sesión';
$string['errorauthdisconnectinvalidmethod'] = 'Se recibió un método de inicio de sesión no válido.';
$string['errorauthdisconnectifmanual'] = 'Si utiliza un método de inicio de sesión manual, ingrese las credenciales a continuación.';
$string['errorauthinvalididtoken'] = 'Se recibió un id_token no válido.';
$string['errorauthloginfailednouser'] = 'Inicio de sesión no válido: no se encontró el usuario en Moodle.';
$string['errorauthnoauthcode'] = 'No se recibió el código de autenticación.';
$string['errorauthnocreds'] = 'Configure las credenciales del cliente de OpenID Connect.';
$string['errorauthnoendpoints'] = 'Configure los extremos del servidor de OpenID Connect.';
$string['errorauthnohttpclient'] = 'Establezca un cliente de HTTP.';
$string['errorauthnoidtoken'] = 'No se recibió el id_token de OpenID Connect.';
$string['errorauthunknownstate'] = 'Estado desconocido.';
$string['errorauthuseralreadyconnected'] = 'Ya está conectado a un usuario distinto de OpenID Connect.';
$string['errorauthuserconnectedtodifferent'] = 'El usuario de OpenID Connect que autenticó ya está conectado al usuario de Moodle.';
$string['errorbadloginflow'] = 'Se especificó un flujo de inicio de sesión no válido. Nota: si recibió esto después de una instalación o actualización reciente, borre el caché de Moodle.';
$string['errorjwtbadpayload'] = 'No se pudo leer la carga de pago de JWT.';
$string['errorjwtcouldnotreadheader'] = 'No se pudo leer el encabezado de JWT';
$string['errorjwtempty'] = 'Se recibió un JWT vacío o que no es cadena.';
$string['errorjwtinvalidheader'] = 'Encabezado de JWT no válido';
$string['errorjwtmalformed'] = 'Se recibió un JWT incorrecto.';
$string['errorjwtunsupportedalg'] = 'JWS Alg o JWE no compatible';
$string['erroroidcnotenabled'] = 'El complemento de autenticación de OpenID Connect no está habilitado.';
$string['errornodisconnectionauthmethod'] = 'No se puede desconectar porque no hay un complemento de autenticación habilitado para volver (el método de inicio de sesión anterior del usuario o el método de inicio de sesión manual).';
$string['erroroidcclientinvalidendpoint'] = 'Se recibió una URI de extremo no válida.';
$string['erroroidcclientnocreds'] = 'Establezca las credenciales del cliente con secretos';
$string['erroroidcclientnoauthendpoint'] = 'No se configuró el extremo de autorización. Configúrelo con $this->setendpoints';
$string['erroroidcclientnotokenendpoint'] = 'No se configuró el extremo de ficha. Configúrelo con $this->setendpoints';
$string['erroroidcclientinsecuretokenendpoint'] = 'El extremo de ficha debe utilizar SSL/TLS para esto.';
$string['errorucpinvalidaction'] = 'Se recibió una acción no válida.';
$string['erroroidccall'] = 'Error en OpenID Connect. Revise los registros para obtener más información.';
$string['erroroidccall_message'] = 'Error en OpenID Connect: {$a}';
$string['eventuserauthed'] = 'Usuario autorizado con OpenID Connect';
$string['eventusercreated'] = 'Usuario creado con OpenID Connect';
$string['eventuserconnected'] = 'Usuario conectado a OpenID Connect';
$string['eventuserloggedin'] = 'Usuario inició sesión con OpenID Connect';
$string['eventuserdisconnected'] = 'Usuario desconectado de OpenID Connect';
$string['oidc:manageconnection'] = 'Administrar conexión de OpenID Connect';
$string['ucp_general_intro'] = 'Aquí puede administrar su conexión a {$a}. Si está habilitado, podrá utilizar su cuenta de {$a} para iniciar sesión en Moodle en lugar de un nombre de usuario y contraseña separados. Una vez conectado, ya no tendrá que recordar el nombre de usuario y la contraseña para Moodle, todos los inicios de sesión serán gestionados por {$a}.';
$string['ucp_login_start'] = 'Comenzar a usar {$a} para iniciar sesión en Moodle';
$string['ucp_login_start_desc'] = 'Esto cambiará su cuenta para usar {$a} para iniciar sesión en Moodle. Una vez habilitado, deberá iniciar sesión con sus credenciales de {$a} - su nombre de usuario y contraseña actuales de Moodle no funcionarán. Puede desconectar su cuenta en cualquier momento y volver a iniciar sesión normalmente.';
$string['ucp_login_stop'] = 'Dejar de usar {$a} para iniciar sesión en Moodle';
$string['ucp_login_stop_desc'] = 'Actualmente, está utilizando {$a} para iniciar sesión en Moodle. Si hace clic en "Dejar de usar el inicio de sesión de {$a}" su cuenta de Moodle se desconectará de {$a}. Ya no podrá volver a iniciar sesión en Moodle con su cuenta de {$a}. Se le solicitará que cree un nombre de usuario y una contraseña, y a partir de allí podrá volver a iniciar sesión en Moodle directamente.';
$string['ucp_login_status'] = 'El inicio de sesión de {$a} es:';
$string['ucp_status_enabled'] = 'Habilitado';
$string['ucp_status_disabled'] = 'Desactivado';
$string['ucp_disconnect_title'] = 'Desconexión de {$a}';
$string['ucp_disconnect_details'] = 'Esto desconectará su cuenta de Moodle de {$a}. Deberá crear un nombre de usuario y una contraseña para iniciar sesión en Moodle.';
$string['ucp_title'] = 'Administración de {$a}';
