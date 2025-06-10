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
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC
 */

/*
 * To change this template, choose Tools | Templates.
 * and open the template in the editor.
 */

// General.
$string['pluginname'] = 'Plugin de plagio de Turnitin';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Tarea del plugin de plagio de Turnitin';
$string['connecttesterror'] = 'Ha ocurrido un error al conectarse a Turnitin, el mensaje del error se muestra abajo:<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'Habilitar Turnitin';
$string['excludebiblio'] = 'Excluir bibliografía';
$string['excludequoted'] = 'Excluir citas bibliográficas';
$string['excludevalue'] = 'Excluir coincidencias pequeñas';
$string['excludewords'] = 'Palabras';
$string['excludepercent'] = 'Porcentaje';
$string['norubric'] = 'Sin matriz de evaluación';
$string['otherrubric'] = 'Utilizar la matriz de evaluación de otro profesor';
$string['attachrubric'] = 'Adjuntar una matriz de evaluación a este ejercicio';
$string['launchrubricmanager'] = 'Iniciar el administrador de matrices de evaluación';
$string['attachrubricnote'] = 'Nota: Los estudiantes podrán ver las matrices de evaluación adjuntas y su contenido, antes de realizar la entrega.';
$string['anonblindmarkingnote'] = 'Nota: Se ha eliminado la configuración de comentarios anónimos de Turnitin. Turnitin utilizará la configuración de comentarios anónimos de Moodle para establecer la configuración de comentarios anónimos.';
$string['transmatch'] = 'Coincidencias traducidas';
$string["reportgen_immediate_add_immediate"] = "Genere informes inmediatamente. Los envíos se agregarán al repositorio de inmediato (si se configura un repositorio).";
$string["reportgen_immediate_add_duedate"] = "Genere informes inmediatamente. Los envíos se agregarán al repositorio en la fecha límite de entrega (si se configura un repositorio).";
$string["reportgen_duedate_add_duedate"] = "Genere informes en la fecha límite de entrega. Los envíos se agregarán al repositorio en la fecha límite de entrega (si se configura un repositorio).";
$string['launchquickmarkmanager'] = 'Activar el administrador de Quickmark';
$string['launchpeermarkmanager'] = 'Activar el administrador de Peermark';
$string['studentreports'] = 'Mostrar los reportes de originalidad a los estudiantes';
$string['studentreports_help'] = 'Permite mostrar los Informes de Originalidad Turnitin a los estudiantes usuarios. Si está configurado a Sí, el Informe de Originalidad generado por Turnitin estará disponible para ser visto por el estudiante.';
$string['submitondraft'] = 'Entregar el archivo en cuanto esté cargado';
$string['submitonfinal'] = 'Entregar el archivo cuando el estudiantes lo envía para su corrección';
$string['draftsubmit'] = '¿Cuándo debe entregarse el archivo a Turnitin?';
$string['allownonor'] = '¿Permitir la entrega de cualquier tipo de archivo?';
$string['allownonor_help'] = 'Esta configuración permitirá que se entregue cualquier tipo de archivo. Con esta opción seleccionada en &#34;Sí&#34;, se comprobará la originalidad de las entregas siempre que sea posible, estas estarán disponibles para descargar y las herramientas de comentarios de GradeMark estarán disponibles siempre que sea posible.';
$string['norepository'] = 'No hay depósitos';
$string['standardrepository'] = 'Depósito estándar';
$string['submitpapersto'] = 'Almacenar trabajos del estudiante';
$string['institutionalrepository'] = 'Depósito de los trabajos de la institución (cuando existe)';
$string['checkagainstnote'] = 'Nota: Si no seleccionas “Sí” en al menos una de las opciones de “Comparar con...” que se encuentran más abajo, NO se generará el reporte de originalidad.';
$string['spapercheck'] = 'Comparar con trabajos almacenados de estudiantes';
$string['internetcheck'] = 'Comparar con Internet';
$string['journalcheck'] = 'Comparar con diarios,<br /> revistas y otras publicaciones';
$string['compareinstitution'] = 'Comparar los archivos entregados con los trabajos entregados dentro de esta institución';
$string['reportgenspeed'] = 'Rapidez de generación del reporte';
$string['locked_message'] = 'Mensaje bloqueado';
$string['locked_message_help'] = 'Si cualquier configuración está bloqueada, se muestra este mensaje para indicar el motivo.';
$string['locked_message_default'] = 'Esta configuración está bloqueada en el nivel del sitio.';
$string['sharedrubric'] = 'Matriz de evaluación compartida';
$string['turnitinrefreshsubmissions'] = 'Actualizar entregas';
$string['turnitinrefreshingsubmissions'] = 'Actualizando entregas';
$string['turnitinppulapre'] = 'Para enviar un archivo a Turnitin, debe aceptar primero nuestro EULA. No aceptar el EULA enviará su archivo a Moodle únicamente. Haga clic aquí para leer y aceptar el acuerdo.';
$string['noscriptula'] = '(Como no tienes javascript habilitado, tendrás que actualizar manualmente esta página antes de poder realizar una entrega después de aceptar el acuerdo de usuario de Turnitin)';
$string['filedoesnotexist'] = 'El archivo ha sido eliminado';
$string['reportgenspeed_resubmission'] = 'Ya entregó un trabajo para este ejercicio y se generó el reporte de similitud correspondiente. Si decide volver a entregar el trabajo, la entrega anterior se reemplazará y se generará un reporte nuevo. Después de {$a->num_resubmissions} segundas entregas, deberá esperar {$a->num_hours} para ver un reporte de similitud nuevo.';

// Plugin settings.
$string['config'] = 'Configuración';
$string['defaults'] = 'Configuración predeterminada';
$string['showusage'] = 'Mostrar volcado de datos';
$string['saveusage'] = 'Guardar volcado de datos';
$string['errors'] = 'Errores';
$string['turnitinconfig'] = 'Configuración del plugin de plagio de Turnitin';
$string['tiiexplain'] = 'Turnitin es un producto comercial y debes estar suscrito para utilizar el servicio. Para obtener más información visita <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>';
$string['useturnitin'] = 'Habilitar Turnitin';
$string['useturnitin_mod'] = 'Habilitar Turnitin para  {$a}';
$string['turnitindefaults'] = 'Configuración predeterminada del plugin de plagio de Turnitin';
$string['defaultsdesc'] = 'Los siguientes son los ajustes predeterminados cuando se habilita Turnitin dentro de un módulo de actividad';
$string['turnitinpluginsettings'] = 'Configuración del plugin de plagio de Turnitin';
$string['pperrorsdesc'] = 'Ha ocurrido un problema al intentar cargar en Turnitin los archivos que se detallan a continuación. Para volver a entregar, seleccione los archivos que deseas cargar y presiona el botón para volver a entregar. Así, los archivos podrán ser procesados la próxima vez que se ejecute cron.';
$string['pperrorssuccess'] = 'Los archivos que seleccionaste han sido entregados de nuevo y serán procesador por cron.';
$string['pperrorsfail'] = 'Hubo un problema con algunos de los archivos seleccionados. No se pudo crear un nuevo evento de cron para ellos.';
$string['resubmitselected'] = 'Volver a entregar archivos seleccionados';
$string['deleteconfirm'] = '¿Estás seguro de que quieres eliminar esta entrega?\n\nEsta acción no puede deshacerse.';
$string['deletesubmission'] = 'Eliminar entrega';
$string['semptytable'] = 'No se han encontrado resultados.';
$string['configupdated'] = 'Configuración actualizada';
$string['defaultupdated'] = 'Las opciones predeterminadas de Turnitin se han actualizado';
$string['notavailableyet'] = 'No está disponible';
$string['resubmittoturnitin'] = 'Volver a entregar a Turnitin';
$string['resubmitting'] = 'Volver a entregar';
$string['id'] = 'Identificador';
$string['student'] = 'Estudiante';
$string['course'] = 'Curso';
$string['module'] = 'Módulo';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = 'Ver el reporte de originalidad';
$string['launchrubricview'] = 'Ver la matriz de evaluación que se ha usado para corregir';
$string['turnitinppulapost'] = 'El archivo no ha sido entregado a Turnitin. Haz clic aquí para aceptar el acuerdo de licencia de software.';
$string['ppsubmissionerrorseelogs'] = 'Este archivo no se ha entregado a Turnitin. Consulta con el administrador del sistema';
$string['ppsubmissionerrorstudent'] = 'Este archivo no se ha entregado a Turnitin. Consulta con tu tutor para obtener más detalles';

// Receipts.
$string['messageprovider:submission'] = 'Notificaciones con recibo digital del plugin de plagio de Turnitin';
$string['digitalreceipt'] = 'Recibo digital';
$string['digital_receipt_subject'] = 'Este es su recibo digital de Turnitin';
$string['pp_digital_receipt_message'] = 'Estimado {$a->firstname} {$a->lastname}:<br /><br />Ha enviado correctamente el archivo <strong>{$a->submission_title}</strong> para el trabajo <strong>{$a->assignment_name}{$a->assignment_part}</strong> de la clase <strong>{$a->course_fullname}</strong> en <strong>{$a->submission_date}</strong>. El código de identificación de la entrega es <strong>{$a->submission_id}</strong>. El recibo digital completo está disponible para ser visto e impreso mediante el botón imprimir/descargar que se encuentra en el visor de documentos.<br /><br />Gracias por utilizar Turnitin,<br /><br />El equipo de Turnitin';

// Paper statuses.
$string['turnitinid'] = 'Identificador de Turnitin';
$string['turnitinstatus'] = 'Estado de Turnitin';
$string['pending'] = 'Pendiente';
$string['similarity'] = 'Similitud';
$string['notorcapable'] = 'No es posible producir un reporte de originalidad para este archivo.';
$string['grademark'] = 'GradeMark';
$string['student_read'] = 'El estudiante ha visto este trabajo el:';
$string['student_notread'] = 'El estudiante no ha visto este trabajo.';
$string['launchpeermarkreviews'] = 'Activar las revisiones de Peermark';

// Cron.
$string['ppqueuesize'] = 'Cantidad de eventos en la lista de eventos del plugin de plagio';
$string['ppcronsubmissionlimitreached'] = 'No se enviarán otras entregas a Turnitin mediante esta ejecución de cron, ya que solamente se procesan {$a} por cada ejecución';
$string['cronsubmittedsuccessfully'] = 'Entrega: {$a->title} (ID de TII: {$a->submissionid}) para el trabajo {$a->assignmentname} del curso {$a->coursename} se entregó correctamente a Turnitin.';
$string['pp_submission_error'] = 'Turnitin muestra un error con su entrega:';
$string['turnitindeletionerror'] = 'La eliminación de la entrega a Turnitin ha fallado. La copia local de Moodle ha sido eliminada, pero la entrega a Turnitin no.';
$string['ppeventsfailedconnection'] = 'No se procesarán eventos mediante el plugin de plagio de Turnitin que se lleva a cabo mediante esta ejecución de cron, debido a que no puede establecerse una conexión con Turnitin.';

// Error codes.
$string['tii_submission_failure'] = 'Consulta con tu tutor o administrador para obtener más detalles';
$string['faultcode'] = 'Código de errores';
$string['line'] = 'Línea';
$string['message'] = 'Mensaje';
$string['code'] = 'Código';
$string['tiisubmissionsgeterror'] = 'Ha ocurrido un error al intentar obtener entregas de este ejercicio de Turnitin';
$string['errorcode0'] = 'Este archivo no se ha entregado a Turnitin. Consulta con el administrador del sistema';
$string['errorcode1'] = 'Este archivo no ha sido enviado a Turnitin dado que no tiene suficiente contenido como para generar un reporte de originalidad.';
$string['errorcode2'] = 'Este archivo no se entregará a Turnitin, ya que excede el tamaño máximo de {$a->maxfilesize} permitido';
$string['errorcode3'] = 'Este archivo no se ha entregado a Turnitin porque el usuario no ha aceptado el acuerdo de licencia del usuario final de Turnitin';
$string['errorcode4'] = 'Debe cargar un tipo de archivo compatible para este ejercicio. Los tipos de archivo admitidos son: .doc, .docx, .ppt, .pptx, .pps, .ppsx, .pdf, .txt, .htm, .html, .hwp, .odt, .wpd, .ps y .rtf';
$string['errorcode5'] = 'Este archivo no se ha entregado a Turnitin porque hay un problema al crear el módulo en Turnitin que impide las entregas, consulta los registros de tu API para obtener más información';
$string['errorcode6'] = 'Este archivo no se ha entregado a Turnitin porque hay un problema al editar la configuración del módulo en Turnitin que impide las entregas, consulta los registros de tu API para obtener más información';
$string['errorcode7'] = 'Este archivo no se ha entregado a Turnitin porque hay un problema al crear el usuario en Turnitin que impide las entregas, consulta los registros de tu API para obtener más información';
$string['errorcode8'] = 'Este archivo no se ha entregado a Turnitin porque existe un problema al crear el archivo temp. La causa más probable es que el nombre del archivo no sea válido. Cambia el nombre del archivo y repite la carga con Editar entrega.';
$string['errorcode9'] = 'El archivo no puede entregarse, ya que no existe contenido accesible en el grupo de archivos por entregar.';
$string['coursegeterror'] = 'No se pudieron obtener datos del curso';
$string['configureerror'] = 'Debes configurar este módulo completamente como administrador antes de usarlo dentro de un curso. Ponte en contacto con tu administrador de Moodle.';
$string['turnitintoolofflineerror'] = 'Estamos experimentando un problema temporal. Vuelve a intentarlo más tarde.';
$string['defaultinserterror'] = 'Ha ocurrido un error al intentar insertar un ajuste predeterminado en la base de datos';
$string['defaultupdateerror'] = 'Ha ocurrido un error al intentar actualizar un ajuste predeterminado en la base de datos';
$string['tiiassignmentgeterror'] = 'Ha ocurrido un error al intentar obtener un ejercicio de Turnitin';
$string['assigngeterror'] = 'No se han podido obtener los datos de Turnitin';
$string['classupdateerror'] = 'No se pudieron actualizar los datos de la clase de Turnitin';
$string['pp_createsubmissionerror'] = 'Ha ocurrido un error al intentar crear la entrega en Turnitin';
$string['pp_updatesubmissionerror'] = 'Ha ocurrido un error al intentar entregar de nuevo tu entrega en Turnitin';
$string['tiisubmissiongeterror'] = 'Ha ocurrido un error al intentar obtener una entrega de Turnitin';

// Javascript.
$string['closebutton'] = 'Cerrar';
$string['loadingdv'] = 'Cargando el visor de documentos de Turnitin...';
$string['changerubricwarning'] = 'Cambiar o desvincular una matriz de evaluación eliminará todas las correcciones existentes de los trabajos de este ejercicio, incluidas las que ya se han utilizado. Las notas generales de otros trabajos corregidos anteriormente se mantendrán.';
$string['messageprovider:submission'] = 'Notificaciones con recibo digital del plugin de plagio de Turnitin';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Estado de Turnitin';
$string['deleted'] = 'Eliminado';
$string['pending'] = 'Pendiente';
$string['because'] = 'Se debe a que un administrador eliminó el ejercicio pendiente de la lista de procesamiento y abortó la entrega a Turnitin.<br /><strong>El archivo aún existe en Moodle, póngase en contacto con su instructor.</strong><br />Vea los detalles a continuación para identificar los códigos de errores:';
$string['submitpapersto_help'] = '<strong>No hay depósitos: </strong><br />Se indica a Turnitin que no almacene los documentos enviados a un depósito. El trabajo solo se procesará para realizar la comprobación de similitud inicial.<br /><br /><strong>Depósito estándar: </strong><br />Turnitin almacena una copia del documento enviado solamente en el depósito estándar. Al elegir esta opción, se indica a Turnitin que solo use los documentos almacenados para hacer comprobaciones de similitud de los documentos que se envíen a futuro.<br /><br /><strong>Depósito de los trabajos de la institución (cuando existe): </strong><br />Al elegir esta opción, se indica a Turnitin que solo agregue los documentos enviados a un depósito privado de la institución. Las comprobaciones de similitud de los documentos entregados solamente estarán a cargo de otros instructores de la institución.';
$string['errorcode12'] = 'Este archivo no se entregó a Turnitin porque pertenece a un ejercicio en el que se eliminó el curso. Id. de fila: ({$a->id}) | Id. de módulo de curso: ({$a->cm}) | Id. de usuario: ({$a->userid})';
$string['errorcode15'] = "Este archivo no se ha enviado a Turnitin, porque no se encontró el módulo de actividad al que pertenece.";
$string['tiiaccountconfig'] = 'Configuración de la cuenta Turnitin';
$string['turnitinaccountid'] = 'Identificador de la cuenta Turnitin';
$string['turnitinsecretkey'] = 'Clave compartida de Turnitin';
$string['turnitinapiurl'] = 'URL del API de Turnitin';
$string['tiidebugginglogs'] = 'Depuración y registro';
$string['turnitindiagnostic'] = 'Habilitar Modo de Diagnóstico';
$string['turnitindiagnostic_desc'] = '<b>[Precaución]</b><br />Habilitar Modo de diagnóstico solo para detectar problemas con el API de Turnitin';
$string['tiiaccountsettings_desc'] = 'Asegúrate de que estas configuraciones coincidan con las de tu cuenta de Turnitin, de no hacerlo podrías tener problemas con la creación de ejercicios o con las entregas de estudiantes.';
$string['tiiaccountsettings'] = 'Configuración de la cuenta Turnitin';
$string['turnitinusegrademark'] = 'Usar GradeMark';
$string['turnitinusegrademark_desc'] = 'Seleccionar si desea usar GradeMark para calificar entregas.<br /><i>(Esta opción solo está disponible para aquellos que tienen GradeMark configurado en su cuenta)</i>';
$string['turnitinenablepeermark'] = 'Habilitar los ejercicios de Peermark';
$string['turnitinenablepeermark_desc'] = 'Elige si quieres permitir la creación de ejercicios de Peermark <br/><i>(Esta función solo está disponible para aquellos que tengan Peermark configurado para su cuenta)</i>';
$string['transmatch_desc'] = 'Determina si las coincidencias traducidas estarán disponibles como un ajuste en la pantalla donde aparece el ejercicio. <br /><i>(Habilita esta opción solo si la función Coincidencias traducidas está habilitada en tu cuenta de Turnitin)</i>';
$string['repositoryoptions_0'] = 'Habilitar las opciones de depósito estándar del instructor';
$string['repositoryoptions_1'] = 'Habilitar las opciones ampliadas del depósito del instructor';
$string['repositoryoptions_2'] = 'Entregar todos los trabajos al depósito estándar';
$string['repositoryoptions_3'] = 'No entregar ningún trabajo al depósito';
$string['turnitinrepositoryoptions'] = 'Ejercicios del depósito de trabajos';
$string['turnitinrepositoryoptions_desc'] = 'Elija las opciones de depósitos para los ejercicios de Turnitin.<br /><i>(El depósito de los trabajos de la institución solo está disponible para quienes tengan la opción habilitada en su cuenta)</i>';
$string['tiimiscsettings'] = 'Configuraciones misceláneas del plugin';
$string['pp_agreement_default'] = 'Al marcar esta casilla, confirmo que este trabajo es de mi autoría y asumo toda responsabilidad relacionada con cualquier violación de derechos de autor que pueda ocurrir como resultado de la entrega.';
$string['pp_agreement_desc'] = '<b>[Opcional]</b><br />Introduce una declaración de confirmación de acuerdo para las entregas. <br />(<b>Nota:</b> Si el acuerdo se deja completamente en blanco, entonces no se requerirá confirmación por parte de los estudiantes durante la entrega)';
$string['pp_agreement'] = 'Aviso legal / Acuerdo';
$string['studentdataprivacy'] = 'Configuración de privacidad de los datos del estudiante';
$string['studentdataprivacy_desc'] = 'Las siguientes opciones pueden configurarse para asegurarse de que la información personal de los estudiantes&#39; no se transmita a Turnitin a través del API.';
$string['enablepseudo'] = 'Habilitar privacidad del estudiante';
$string['enablepseudo_desc'] = 'Si se selecciona esta opción, las direcciones de correo electrónico de los estudiantes se transformarán en un pseudo equivalente para las llamadas API de Turnitin.<br /><i>(<b>Nota:</b> Esta opción no puede cambiarse si los datos de algún estudiante de Moodle ya se han sincronizado con Turnitin)</i>';
$string['pseudofirstname'] = 'Pseudo nombre del estudiante';
$string['pseudofirstname_desc'] = '<b>[Opcional]</b><br />Nombre del estudiante que se muestra en el visor de documentos de Turnitin';
$string['pseudolastname'] = 'Pseudo apellido del estudiante';
$string['pseudolastname_desc'] = 'Apellido del estudiante para mostrarlo en el visor de documentos de Turnitin';
$string['pseudolastnamegen'] = 'Autogenerar apellido';
$string['pseudolastnamegen_desc'] = 'Si está establecido en sí y el pseudo apellido está asignado a un campo de perfil de usuario, el campo se completará automáticamente con un identificador único.';
$string['pseudoemailsalt'] = 'Pseudo elemento de criptografía';
$string['pseudoemailsalt_desc'] = '<b>[Opcional]</b><br />Un elemento opcional para incrementar la complejidad de la pseudo dirección de correo electrónico del estudiante generada.<br />(<b>Nota:</b> El elemento debe permanecer igual para poder mantener las pseudo direcciones de correo electrónico consistentes)';
$string['pseudoemaildomain'] = 'Pseudo dominio de correo electrónico';
$string['pseudoemaildomain_desc'] = '<b>[Opcional]</b><br />Un dominio opcional para pseudo direcciones de correo electrónico. (@tiimoodle.com si se deja vacío)';
$string['pseudoemailaddress'] = 'Pseudo dirección de correo electrónico';
$string['connecttest'] = 'Probar la conexión de Turnitin';
$string['connecttestsuccess'] = 'Moodle ha conectado correctamente con Turnitin.';
$string['diagnosticoptions_0'] = 'Desactivado';
$string['diagnosticoptions_1'] = 'Estándar';
$string['diagnosticoptions_2'] = 'Depuración';
$string['repositoryoptions_4'] = 'Enviar todos los trabajos al depósito de la institución';
$string['turnitinrepositoryoptions_help'] = '<strong>Habilitar las opciones de depósito estándar del instructor: </strong><br />Los instructores pueden indicar a Turnitin que agregue documentos al depósito estándar, al depósito privado de la institución o a ningún depósito. <br /><br /><strong>Habilitar las opciones ampliadas del depósito del instructor: </strong><br />Esta opción permite que los instructores vean la configuración de un ejercicio para que los estudiantes puedan indicar a Turnitin dónde se almacenarán sus documentos. Los estudiantes pueden elegir agregar sus documentos en un depósito estándar de estudiantes o en el depósito privado de la institución.<br /><br /><strong>Entregar todos los trabajos al depósito estándar: </strong><br />Todos los documentos se agregarán al depósito estándar de estudiantes de forma predeterminada.<br /><br /><strong>No entregar ningún trabajo al depósito: </strong><br />Los documentos solo se usarán para realizar la revisión original en Turnitin y para que el instructor pueda verlos y calificarlos.<br /><br /><strong>Enviar todos los trabajos al depósito de la institución: </strong><br />Se indica a Turnitin que almacene todos los trabajos dentro del depósito de trabajos de la institución. Las comprobaciones de similitud de los documentos entregados solo estarán a cargo de otros instructores de la institución.';
$string['turnitinuseanon'] = 'Utilizar Anotaciones Anónimas';
$string['createassignmenterror'] = 'Ha ocurrido un error al intentar crear un ejercicio en Turnitin';
$string['editassignmenterror'] = 'Ha ocurrido un error al intentar editar el ejercicio en Turnitin';
$string['ppassignmentediterror'] = 'El módulo {$a->title} (ID de TII: {$a->assignmentid}) no ha podido editarse en Turnitin. Consulta tu registro API para obtener más información';
$string['pp_classcreationerror'] = 'Esta clase no ha podido crearse en Turnitin. Consulta tu registro API para obtener más información';
$string['unlinkusers'] = 'Desvincular los usuarios';
$string['relinkusers'] = 'Volver a vincular usuarios';
$string['unlinkrelinkusers'] = 'Desvincular/ volver a vincular usuarios';
$string['nointegration'] = 'Sin integración';
$string['sprevious'] = 'Previo';
$string['snext'] = 'Siguiente';
$string['slengthmenu'] = 'Mostrar entradas_MENU_';
$string['ssearch'] = 'Buscar:';
$string['sprocessing'] = 'Procesando datos de Turnitin...';
$string['szerorecords'] = 'No hay registros que mostrar.';
$string['sinfo'] = 'Mostrando _TOTAL_ de entradas de _START_ a _END_';
$string['userupdateerror'] = 'No se han podido actualizar los datos del usuario';
$string['connecttestcommerror'] = 'No se pudo conectar a Turnitin. Comprueba nuevamente tu configuración de URL de API.';
$string['userfinderror'] = 'Ha ocurrido un error al intentar encontrar al usuario en Turnitin';
$string['tiiusergeterror'] = 'Ha ocurrido un error al intentar obtener los detalles del usuario de Turnitin';
$string['usercreationerror'] = 'Creación de usuario Turnitin fallida';
$string['ppassignmentcreateerror'] = 'Este módulo no ha podido crearse en Turnitin. Consulta tu registro API para obtener más información';
$string['excludebiblio_help'] = 'Esta opción permite que el profesor evite que se busquen coincidencias al generar Reportes de originalidad en ciertas partes del trabajo, como texto que aparezca en la bibliografía, obras citadas o secciones de referencia de los trabajos de los estudiantes. Esta opción puede ignorarse en Reportes de originalidad individuales.';
$string['excludequoted_help'] = 'Esta opción permite que el profesor evite que se busquen coincidencias al generar Reportes de originalidad en texto que pertenezca a citas. Esta opción puede ignorarse en reportes de originalidad individuales.';
$string['excludevalue_help'] = 'Esta opción permite que el profesor evite que las coincidencias que no tengan la longitud suficiente (determinada por el profesor) sean consideradas al generar reportes de originalidad. Esta opción puede ignorarse en reportes de originalidad individuales.';
$string['spapercheck_help'] = 'Compara con el depósito de trabajos de estudiantes de Turnitin cuando estés procesando Informes de originalidad para los trabajos. El porcentaje del índice de similitud puede disminuir si esta opción no está seleccionada.';
$string['internetcheck_help'] = 'Compara con el depósito de Internet de Turnitin cuando estés procesando reportes de originalidad para los trabajos. El porcentaje del índice de similitud puede disminuir si esta opción no está seleccionada.';
$string['journalcheck_help'] = 'Compara con el depósito de publicaciones de Turnitin cuando estés procesando reportes de originalidad para los trabajos. El porcentaje del índice de similitud puede disminuir si esta opción no está seleccionada.';
$string['reportgenspeed_help'] = 'Existen tres opciones para la configuración de este ejercicio: &#39;Genere informes inmediatamente. Los envíos se agregarán al repositorio en la fecha límite de entrega (si se configura un repositorio).&#39;, &#39;Genere informes inmediatamente. Los envíos se agregarán al repositorio de inmediato (si se configura un repositorio).&#39; y &#39;Genere informes en la fecha límite de entrega. Los envíos se agregarán al repositorio en la fecha límite de entrega (si se configura un repositorio).&#39;<br /><br />La opción &#39;Genere informes inmediatamente. Los envíos se agregarán al repositorio en la fecha límite de entrega (si se configura un repositorio).&#39; genera el reporte de originalidad inmediatamente cuando un estudiante realiza una entrega. Con esta opción seleccionada, tus estudiantes no podrán volver a realizar entregas al ejercicio.<br /><br />Para permitir segundas entregas, selecciona la opción &#39;Genere informes inmediatamente. Los envíos se agregarán al repositorio de inmediato (si se configura un repositorio).&#39;. Esta opción permite a los estudiantes volver a realizar entregas al ejercicio hasta la fecha límite de entrega. El proceso de los reportes de originalidad en ese caso puede demorar hasta 24 horas.<br /><br />La opción &#39;Genere informes en la fecha límite de entrega. Los envíos se agregarán al repositorio en la fecha límite de entrega (si se configura un repositorio).&#39; solo generará un reporte de originalidad en la fecha límite de entrega del ejercicio&#39;. Esta configuración hará que todos los trabajos entregados al ejercicio se comparen los unos a los otros cuando se creen los reportes de originalidad.';
$string['turnitinuseanon_desc'] = 'Selecciona si deseas permitir Correcciones anónimas para evaluar entregas.<br /><i>(Esta opción solo está disponible para aquellos que han configurado las correcciones anónimas en sus cuentas)</i>';
