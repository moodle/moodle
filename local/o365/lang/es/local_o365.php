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
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'Integración de Microsoft 365';
$string['acp_title'] = 'Panel de control de administración de Microsoft 365';
$string['acp_healthcheck'] = 'Comprobación de estado';
$string['acp_parentsite_name'] = 'Moodle';
$string['acp_parentsite_desc'] = 'Sitio para datos de cursos de Moodle.';
$string['calendar_user'] = 'Calendario personal (usuario)';
$string['calendar_site'] = 'Calendario de todo el sitio';
$string['erroracpauthoidcnotconfig'] = 'Establezca las credenciales de la aplicación en auth_oidc primero.';
$string['erroracplocalo365notconfig'] = 'Configure local_o365 primero.';
$string['errorhttpclientbadtempfileloc'] = 'No se pudo abrir la ubicación temporal para almacenar el archivo.';
$string['errorhttpclientnofileinput'] = 'No hay parámetros del archivo en httpclient::put';
$string['errorcouldnotrefreshtoken'] = 'No se pudo actualizar la ficha';
$string['errorchecksystemapiuser'] = 'No se pudo abrir una ficha de usuario de API del sistema, ejecute la comprobación de estado, asegúrese de que se esté ejecutando el cron de Moodle y actualice la API del sistema si es necesario.';
$string['erroro365apibadcall'] = 'Error en la llamada a API.';
$string['erroro365apibadcall_message'] = 'Error en la llamada a la API: {$a}';
$string['erroro365apibadpermission'] = 'No se encontró el permiso';
$string['erroro365apicouldnotcreatesite'] = 'Problema al crear el sitio.';
$string['erroro365apicoursenotfound'] = 'No se pudo encontrar el curso.';
$string['erroro365apiinvalidtoken'] = 'Ficha no válida o vencida.';
$string['erroro365apiinvalidmethod'] = 'Método http no válido pasado a llamada de api';
$string['erroro365apinoparentinfo'] = 'No se pudo encontrar la información de la carpeta principal';
$string['erroro365apinotimplemented'] = 'Este debe ser anulado.';
$string['erroro365apinotoken'] = 'No posee una ficha para el recurso y usuario brindados, y no se pudo obtener una. ¿Venció la ficha actualizada del usuario?';
$string['erroro365apisiteexistsnolocal'] = 'El sitio ya existe, pero no se pudo encontrar el registro local.';
$string['eventapifail'] = 'Falla de API';
$string['eventcalendarsubscribed'] = 'El usuario se suscribió a un calendario';
$string['eventcalendarunsubscribed'] = 'El usuario canceló la suscripción a un calendario';
$string['healthcheck_fixlink'] = 'Haga clic aquí para corregirlo.';
$string['healthcheck_systemapiuser_title'] = 'Usuario de API del sistema';
$string['healthcheck_systemtoken_result_notoken'] = 'Moodle no tiene una ficha para comunicarse con Microsoft 365 como usuario de API del sistema. Generalmente, esto puede resolverse restableciendo el usuario de API del sistema.';
$string['healthcheck_systemtoken_result_noclientcreds'] = 'No hay credenciales de aplicación presentes en el complemento OpenID Connect. Sin estas credenciales, Moodle no puede establecer ninguna comunicación con Microsoft 365. Haga clic aquí para visitar la página de configuración e ingresar sus credenciales.';
$string['healthcheck_systemtoken_result_badtoken'] = 'Ocurrió un problema al comunicarse con Microsoft 365 como usuario de API del sistema. Generalmente, esto puede resolverse restableciendo el usuario de API del sistema.';
$string['healthcheck_systemtoken_result_passed'] = 'Moodle puede comunicarse con Microsoft 365 como usuario de API del sistema.';
$string['settings_aadsync'] = 'Sincronizar usuarios con Azure AD';
$string['settings_aadsync_details'] = 'Cuando está habilitado, los usuarios de Moodle y Azure AD se sincronizan según las opciones anteriores.<br /><br /><b>Nota: </b>El trabajo de sincronización se ejecuta en el cron de Moodle y sincroniza 1000 usuarios por vez. De manera predeterminada, se ejecuta una vez por día a las 1:00 a. m. en la zona horaria local del servidor. Para sincronizar conjuntos de usuarios más grandes más rápidamente, puede aumentar la frecuencia de la tarea <b>Sincronizar usuarios con Azure AD</b> utilizando la <a href="{$a}">página Administración de tareas programadas.</a><br /><br />Para obtener instrucciones más detalladas, consulte la <a href="https://docs.moodle.org/30/en/Office365#User_sync">documentación de sincronización de usuarios</a><br /><br />';
$string['settings_aadsync_create'] = 'Crear cuentas en Moodle para usuarios en Azure AD';
$string['settings_aadsync_delete'] = 'Eliminar cuentas previamente sincronizadas en Moodle cuando se eliminen de Azure AD';
$string['settings_aadsync_match'] = 'Hacer coincidir a los usuarios preexistentes de Moodle con las cuentas del mismo nombre en Azure AD<br /><small>Esto buscará el nombre de usuario en Microsoft 365 y el nombre de usuario en Moodle e intentará encontrar coincidencias. Las coincidencias distinguen mayúsculas de minúsculas y omiten el abonado de Microsoft 365. Por ejemplo, BoB.SmiTh en Moodle coincidirá con bob.smith@example.onmicrosoft.com. Los usuarios que coincidan tendrán sus cuentas de Moodle y Microsoft 365 conectadas y podrán usar todas las características de integración de Microsoft 365/Moodle. El método de autenticación del usuario no cambiará al menos que se habilite el ajuste a continuación.</small>';
$string['settings_aadsync_matchswitchauth'] = 'Cambiar usuarios coincidentes a la autenticación de Microsoft 365 (OpenID Connect)<br /><small>Esto requiere que se habilite el ajuste "Hacer coincidir" de arriba. Cuando se hace coincidir a un usuario, al habilitar este ajuste se cambia el método de autenticación a OpenID Connect. Deberán iniciar sesión en Moodle con las credenciales de Microsoft 365. <b>Nota:</b> Asegúrese de que el complemento de autenticación de OpenID Connect esté habilitado si desea utilizar este ajuste.</small>';
$string['settings_aadtenant'] = 'Abonado de Azure AD';
$string['settings_aadtenant_details'] = 'Se utiliza para identificar a su organización dentro de Azure AD. Por ejemplo: "contoso.onmicrosoft.com"';
$string['settings_azuresetup'] = 'Configuración de Azure';
$string['settings_azuresetup_details'] = 'Esta herramienta verifica con Azure para garantizar que todo esté configurado correctamente. También puede corregir algunos errores comunes.';
$string['settings_azuresetup_update'] = 'Actualizar';
$string['settings_azuresetup_checking'] = 'Verificando...';
$string['settings_azuresetup_missingperms'] = 'Permisos faltantes:';
$string['settings_azuresetup_permscorrect'] = 'Los permisos son correctos.';
$string['settings_azuresetup_errorcheck'] = 'Ocurrió un error al intentar verificar la configuración de Azure.';
$string['settings_azuresetup_unifiedheader'] = 'API unificada';
$string['settings_azuresetup_unifieddesc'] = 'La API unificada reemplaza a las API específicas de la aplicación existentes. Si está disponible, debe agregarla a la aplicación Azure para estar preparado para el futuro. Eventualmente, reemplazará a la API de legado.';
$string['settings_azuresetup_unifiederror'] = 'Ocurrió un error al verificar el soporte de la API unificada.';
$string['settings_azuresetup_unifiedactive'] = 'API unificada activa.';
$string['settings_azuresetup_unifiedmissing'] = 'No se encontró la API unificada en esta aplicación.';
$string['settings_creategroups'] = 'Crear grupos de usuarios';
$string['settings_creategroups_details'] = 'Si está habilitado, creará y mantendrá un grupo de profesores y alumnos en Microsoft 365 para cada curso del sitio. Esto creará todas las ejecuciones de cron de cada grupo necesario (y agregará todos los miembros actuales). Después de esto, se mantendrá la membresía de grupos a medida que los usuarios se inscriban o cancelen la inscripción desde cursos de Moodle.<br /><b>Nota: </b>Esta característica requiere agregar la API unificada de Microsoft 365 a la aplicación agregada en Azure. <a href="https://docs.moodle.org/30/en/Office365#User_groups">Instrucciones y documentación de configuración.</a>';
$string['settings_o365china'] = 'Microsoft 365 para China';
$string['settings_o365china_details'] = 'Marque esta opción si está utilizando Microsoft 365 para China.';
$string['settings_debugmode'] = 'Grabar mensajes de depuración';
$string['settings_debugmode_details'] = 'Si está habilitado, se registrará la información en el registro de Moodle que puede ayudar a identificar problemas.';
$string['settings_detectoidc'] = 'Credenciales de la aplicación';
$string['settings_detectoidc_details'] = 'Para comunicarse con Microsoft 365, Moodle necesita credenciales para identificarse. Estas se establecen en el complemento de autenticación "OpenID Connect".';
$string['settings_detectoidc_credsvalid'] = 'Se establecieron las credenciales.';
$string['settings_detectoidc_credsvalid_link'] = 'Cambiar';
$string['settings_detectoidc_credsinvalid'] = 'No se establecieron las credenciales o están incompletas.';
$string['settings_detectoidc_credsinvalid_link'] = 'Establecer credenciales';
$string['settings_detectperms'] = 'Permisos de la aplicación';
$string['settings_detectperms_details'] = 'Para usar las características del complemento, deben establecerse los permisos correctos para la aplicación en Azure AD.';
$string['settings_detectperms_nocreds'] = 'Debe establecer las credenciales primero. Consulte la configuración anterior.';
$string['settings_detectperms_missing'] = 'Ausente:';
$string['settings_detectperms_errorfix'] = 'Ocurrió un error al intentar corregir los permisos. Configúrelos manualmente en Azure.';
$string['settings_detectperms_fixperms'] = 'Corregir permisos';
$string['settings_detectperms_fixprereq'] = 'Para corregir esto automáticamente, el usuario de la API del sistema debe ser un administrador, y debe habilitarse el permiso "Acceder al directorio de su organización" en Azure para la aplicación "Windows Azure Active Directory".';
$string['settings_detectperms_nounified'] = 'La API unificada no está presente, es posible que algunas características no funcionen.';
$string['settings_detectperms_unifiednomissing'] = 'Todos los permisos unificados presentes.';
$string['settings_detectperms_update'] = 'Actualizar';
$string['settings_detectperms_valid'] = 'Se configuraron los permisos.';
$string['settings_detectperms_invalid'] = 'Compruebe los permisos en Azure AD';
$string['settings_header_setup'] = 'Ajuste';
$string['settings_header_options'] = 'Opciones';
$string['settings_healthcheck'] = 'Comprobación de estado';
$string['settings_healthcheck_details'] = 'Si algo no está funcionando correctamente, puede realizar una comprobación de estado para identificar el problema y proponer soluciones.';
$string['settings_healthcheck_linktext'] = 'Realizar comprobación de estado';
$string['settings_odburl'] = 'URL de OneDrive for Business';
$string['settings_odburl_details'] = 'La URL para acceder a OneDrive for Business. Generalmente, esto puede determinarse por el abonado de Azure AD. Por ejemplo, si su abonado de Azure AD es "contoso.onmicrosoft.com", probablemente será "contoso-my.sharepoint.com". Ingrese solo el nombre de dominio, no incluya http:// o https://';
$string['settings_serviceresourceabstract_valid'] = '{$a} se puede usar.';
$string['settings_serviceresourceabstract_invalid'] = 'Al parecer, esta valor no se puede usar.';
$string['settings_serviceresourceabstract_nocreds'] = 'Configure las credenciales de la aplicación primero.';
$string['settings_serviceresourceabstract_empty'] = 'Ingrese un valor o haga clic en "Detectar" para intentar detectar el valor correcto.';
$string['settings_systemapiuser'] = 'Usuario de API del sistema';
$string['settings_systemapiuser_details'] = 'Cualquier usuario de Azure AD, pero debe ser la cuenta de un administrador o una cuenta dedicada. Esta cuenta se utiliza para realizar operaciones no específicas del usuario. Por ejemplo, administrar sitios de SharePoint de cursos.';
$string['settings_systemapiuser_change'] = 'Cambiar usuario';
$string['settings_systemapiuser_usernotset'] = 'No se estableció el usuario.';
$string['settings_systemapiuser_userset'] = '{$a}';
$string['settings_systemapiuser_setuser'] = 'Establecer usuario';
$string['spsite_group_contributors_name'] = '{$a} contribuidores';
$string['spsite_group_contributors_desc'] = 'Todos los usuarios que tienen acceso para administrar los archivos del curso {$a}';
$string['task_calendarsyncin'] = 'Sincronizar eventos de o365 en Moodle';
$string['task_coursesync'] = 'Crear grupos de usuarios en Microsoft 365';
$string['task_refreshsystemrefreshtoken'] = 'Actualizar ficha de actualización de usuario de API del sistema';
$string['task_syncusers'] = 'Sincronizar usuarios con Azure AD.';
$string['ucp_connectionstatus'] = 'Estado de conexión';
$string['ucp_calsync_availcal'] = 'Calendarios de Moodle disponibles';
$string['ucp_calsync_title'] = 'Sincronización de calendario de Outlook';
$string['ucp_calsync_desc'] = 'Los calendarios seleccionados se sincronizarán desde Moodle con el calendario de Outlook.';
$string['ucp_connection_status'] = 'La conexión de Microsoft 365 es:';
$string['ucp_connection_start'] = 'Conectar a Microsoft 365';
$string['ucp_connection_stop'] = 'Desconectar de Microsoft 365';
$string['ucp_features'] = 'Características de Microsoft 365';
$string['ucp_features_intro'] = 'A continuación encontrará una lista de características que puede utilizar para mejorar Moodle con Microsoft 365.';
$string['ucp_features_intro_notconnected'] = 'Es posible que algunas no estén disponibles hasta que se conecte a Microsoft 365.';
$string['ucp_general_intro'] = 'Aquí puede gestionar su conexión a Microsoft 365.';
$string['ucp_index_aadlogin_title'] = 'Inicio de sesión de Microsoft 365';
$string['ucp_index_aadlogin_desc'] = 'Puede usar sus credenciales de Microsoft 365 para iniciar sesión en Moodle. ';
$string['ucp_index_calendar_title'] = 'Sincronización de calendario de Outlook';
$string['ucp_index_calendar_desc'] = 'Aquí puede configurar la sincronización entre sus calendarios de Moodle y Outlook. Puede exportar eventos del calendario de Moodle a Outlook, y traer eventos de Outlook a Moodle.';
$string['ucp_index_connectionstatus_connected'] = 'Actualmente, está conectado a Microsoft 365';
$string['ucp_index_connectionstatus_matched'] = 'Ha coincidido con el usuario de Microsoft 365 <small>"{$a}"</small>. Para completar esta conexión, haga clic en el enlace a continuación e inicie sesión en Microsoft 365.';
$string['ucp_index_connectionstatus_notconnected'] = 'Actualmente, no está conectado a Microsoft 365';
$string['ucp_index_onenote_title'] = 'OneNote';
$string['ucp_index_onenote_desc'] = 'La integración de OneNote le permite utilizar Microsoft 365 OneNote con Moodle. Puede completar tareas con OneNote y tomar notas de para sus cursos fácilmente .';
$string['ucp_notconnected'] = 'Conéctese a Microsoft 365 antes de entrar aquí.';
$string['settings_onenote'] = 'Deshabilitar Microsoft 365 OneNote';
$string['ucp_status_enabled'] = 'Activa';
$string['ucp_status_disabled'] = 'No conectado';
$string['ucp_syncwith_title'] = 'Sincronizar con:';
$string['ucp_syncdir_title'] = 'Comportamiento de sincronización:';
$string['ucp_syncdir_out'] = 'De Moodle a Outlook';
$string['ucp_syncdir_in'] = 'De Outlook a Moodle';
$string['ucp_syncdir_both'] = 'Actualizar Outlook y Moodle';
$string['ucp_title'] = 'Panel de control de Microsoft 365/Moodle';
$string['ucp_options'] = 'Opciones';

$string['assignment'] = 'Tarea';
$string['course_assignment_submitted_due'] = 'Curso - {$a->course} &nbsp; |  &nbsp; Tarea -{$a->assignment} <br />
Entregada el - {$a->submittedon} &nbsp; |  &nbsp; Due date - {$a->duedate}';
$string['due_date'] = 'Fecha de vencimiento - {$a}';
$string['grade_date'] = 'Calification - {$a->grade} &nbsp; | &nbsp; Fecha - {$a->date}';
$string['help_message'] = 'Hola! Soy tu ayudante con Moodle. Me puedes hacer las siguientes preguntas:';
$string['last_login_date'] = 'Fecha del último inicio de sesión  - {$a}';
$string['list_of_absent_students'] = 'Esta es la lista de estudiantes que estuvieron ausentes este mes.:';
$string['list_of_assignments_grades_compared'] = 'Esta es la lista de tus calificaciones en tareas en comparación con el promedio de la clase:';
$string['list_of_assignments_needs_grading'] = 'Esta es la lista de las tareas que necesitan corrección:';
$string['list_of_due_assignments'] = 'Esta es la lista de tareas cuya fecha entrega ha pasado';
$string['list_of_incomplete_assignments'] = 'Esta es la lista de las tareas que están incompletas:';
$string['list_of_last_logged_students'] = 'Esta es la lista de los últimos estudiantes que han iniciado sesión:';
$string['list_of_late_submissions'] = 'Esta es la lista de estudiantes que hicieron envíos de tareas tardíos:';
$string['list_of_latest_logged_students'] = 'Esta es la lista de los últimos estudiantes que han iniciado sesión:';
$string['list_of_recent_grades'] = 'Esta es la lista de tus calificaciones recientes:';
$string['list_of_students_with_least_score'] = 'Esta es la lista de estudiantes con el menor nota en la última tarea:';
$string['list_of_students_with_name'] = 'Estos son los estudiantes con el nombre {$a}:';
$string['never'] = 'Nunca';
$string['no_absent_users_found'] = 'No se encontraron usuarios ausentes';
$string['no_assignments_for_grading_found'] = 'No se encontraron tareas para calificar';
$string['no_assignments_found'] = 'No se encontraron tareas';
$string['no_due_assignments_found'] = 'No se encontraron tareas cuya fecha entrega ha pasado';
$string['no_due_incomplete_assignments_found'] = 'No se encontraron tareas incompletas o cuya fecha entrega ha pasado';
$string['no_graded_assignments_found'] = 'No se encontraron tareas calificadas';
$string['no_grades_found'] = 'No se encontraron calificaciones';
$string['no_late_submissions_found'] = 'No se encontraron envíos tardíos';
$string['no_users_found'] = 'No se encontraron usuarios';
$string['no_user_with_name_found'] = 'No se encontró ningún usuario con ese nombre';
$string['participants_submitted_needs_grading'] = 'Usuarios - {$a->participants}  &nbsp; |  &nbsp; Entregado - {$a->submitted}  &nbsp; |  &nbsp;
                        Necesita calificación - {$a->needsgrading}';
$string['pending_submissions_due_date'] = ' Entrega Pendiente - {$a->incomplete} / {$a->total} &nbsp; |  &nbsp; Fecha entrega pasada - {$a->duedate}';
$string['sorry_do_not_understand'] = 'Lo siento, no entiendo';
$string['question_student_assignments_compared'] = "¿Cómo lo hice en mis últimas tareas en comparación con la clase?";
$string['question_student_assignments_due'] = "¿Qué tareas se deben entregar próximamente?";
$string['question_student_latest_grades'] = "¿Cuáles son las últimas calificaciones que he recibido?";
$string['question_teacher_absent_students'] = "¿Qué estudiantes han faltado este mes?";
$string['question_teacher_assignments_incomplete_submissions'] = "¿Cuántas tareas tienen entregas incompletas?";
$string['question_teacher_assignments_for_grading'] = "¿Qué tareas aún no se han calificado?";
$string['question_teacher_last_logged_students'] = "¿Qué estudiantes han iniciado sesion los últimos en moodle?";
$string['question_teacher_late_submissions'] = "¿Qué estudiantes han entregado tareas tarde?";
$string['question_teacher_latest_logged_students'] = "¿Qué estudiantes han iniciado sesión más recientemente en moodle??";
$string['question_teacher_least_scored_in_assignment'] = "¿Qué estudiantes obtuvieron la menor calificación en la última tarea?";
$string['question_teacher_student_last_logged'] = "¿Cuándo Nombre Apellido se conectó por última vez en moodle?";
$string['your_grade'] = 'Tu calificación - {$a}';
$string['your_grade_class_grade'] = 'Tu calificación - {$a->usergrade} &nbsp; |  &nbsp; Calificación media de la clase - {$a->classgrade}';
