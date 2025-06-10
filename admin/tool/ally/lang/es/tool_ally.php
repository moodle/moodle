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
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminurl'] = 'URL de lanzamiento';
$string['adminurldesc'] = 'URL de lanzamiento de LTI que se utiliza para acceder al Informe de accesibilidad.';
$string['allyclientconfig'] = 'Configuración de Ally';
$string['ally:clientconfig'] = 'Acceder y actualizar la configuración del cliente';
$string['ally:viewlogs'] = 'Visor de registros de Ally';
$string['clientid'] = 'ID del cliente';
$string['clientiddesc'] = 'ID del cliente de Ally';
$string['code'] = 'Código';
$string['contentauthors'] = 'Autores del contenido';
$string['contentauthorsdesc'] = 'Se evaluará la accesibilidad de los archivos del curso cargados pertenecientes a los administradores y a los usuarios asignados a estos roles seleccionados. Los archivos reciben una calificación de accesibilidad. Las calificaciones bajas implican que es necesario realizar cambios en los archivos para que sean más accesibles.';
$string['contentupdatestask'] = 'Tarea de actualizaciones del contenido';
$string['curlerror'] = 'Error de cURL: {$a}';
$string['curlinvalidhttpcode'] = 'Código de estado HTTP no válido: {$a}';
$string['curlnohttpcode'] = 'No se puede verificar el código de estado HTTP';
$string['error:invalidcomponentident'] = 'ID del componente no válido {$a}';
$string['error:pluginfilequestiononly'] = 'Solo se admiten los componentes de las preguntas para esta URL';
$string['error:componentcontentnotfound'] = 'No se encontró el contenido para {$a}';
$string['error:wstokenmissing'] = 'Falta el token del servicio web. ¿Es posible que un usuario administrador necesite ejecutar la configuración automática?';
$string['excludeunused'] = 'Excluir archivos sin usar';
$string['excludeunuseddesc'] = 'Omitir archivos adjuntos al contenido HTML, pero permitir archivos vinculados/referencias en el HTML.';
$string['filecoursenotfound'] = 'Los archivos aprobados no pertenecen a ningún curso';
$string['fileupdatestask'] = 'Insertar actualizaciones de archivos en Ally';
$string['id'] = 'ID';
$string['key'] = 'Clave';
$string['keydesc'] = 'Clave del consumidor de LTI.';
$string['level'] = 'Nivel';
$string['message'] = 'Mensajes';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'URL para actualizaciones de archivos';
$string['pushurldesc'] = 'Inserte notificaciones sobre actualizaciones de archivos en esta URL.';
$string['queuesendmessagesfailure'] = 'Se produjo un error mientras se enviaban mensajes a AWS SQS. Datos incorrectos: $a';
$string['secret'] = 'Secreto';
$string['secretdesc'] = 'Secreto de LTI.';
$string['showdata'] = 'Mostrar datos';
$string['hidedata'] = 'Ocultar datos';
$string['showexplanation'] = 'Mostar explicación';
$string['hideexplanation'] = 'Ocultar explicación';
$string['showexception'] = 'Mostrar excepción';
$string['hideexception'] = 'Ocultar excepción';
$string['usercapabilitymissing'] = 'El usuario suministrado no cuenta con el permiso para eliminar este archivo.';
$string['autoconfigure'] = 'Configurar automáticamente el servicio web de Ally';
$string['autoconfiguredesc'] = 'Cree un usuario y un rol de servicio web para Ally de forma automática.';
$string['autoconfigureconfirmation'] = 'Cree un usuario y un rol de servicio web para Ally de forma automática y habilite el servicio web. Se deben llevar a cabo las siguientes acciones:<ul><li>crear un rol denominado "ally_webservice" y un usuario con el nombre de usuario "ally_webuser"</li><li>agregar el usuario "ally_webuser" al rol "ally_webservice"</li><li>habilitar servicios web</li><li>habilitar el protocolo de servicio web rest</li><li>habilitar el servicio web de Ally</li><li>crear un token para la cuenta "ally_webuser"</li></ul>';
$string['autoconfigsuccess'] = 'Listo: el servicio web de Ally se configuró automáticamente.';
$string['autoconfigtoken'] = 'El token del servicio web es el siguiente:';
$string['autoconfigapicall'] = 'Puede probar el funcionamiento del servicio web a través de la siguiente URL:';
$string['privacy:metadata:files:action'] = 'Acción realizada en el archivo. Por ejemplo: creado, actualizado o eliminado.';
$string['privacy:metadata:files:contenthash'] = 'Función hash para el contenido del archivo a fin de determinar su originalidad.';
$string['privacy:metadata:files:courseid'] = 'ID del curso al que pertenece el archivo';
$string['privacy:metadata:files:externalpurpose'] = 'Los archivos deben intercambiarse con Ally para que sea posible integrarlos con este producto.';
$string['privacy:metadata:files:filecontents'] = 'El contenido del archivo real se envía a Ally para evaluar su accesibilidad.';
$string['privacy:metadata:files:mimetype'] = 'Tipo de archivo MIME. Por ejemplo: text/plain, image/jpeg, etc.';
$string['privacy:metadata:files:pathnamehash'] = 'Función hash para el nombre de la ruta del archivo a fin de identificarlo de forma única.';
$string['privacy:metadata:files:timemodified'] = 'Hora de la última modificación del campo.';
$string['cachedef_annotationmaps'] = 'Almacenar datos de anotaciones para cursos';
$string['cachedef_fileinusecache'] = 'Archivos de Ally en caché de uso';
$string['cachedef_pluginfilesinhtml'] = 'Archivos de Ally en caché HTML';
$string['cachedef_request'] = 'Caché de solicitud de filtros de Ally';
$string['pushfilessummary'] = 'Resumen de actualizaciones de archivos de Ally.';
$string['pushfilessummary:explanation'] = 'Resumen de las actualizaciones de archivos que se envían a Ally.';
$string['section'] = 'Sección {$a}';
$string['lessonanswertitle'] = 'Respuesta para la lección "{$a}"';
$string['lessonresponsetitle'] = 'Respuesta para la lección "{$a}"';
$string['logs'] = 'Registros de Ally';
$string['logrange'] = 'Rango de registro';
$string['loglevel:none'] = 'Ninguno';
$string['loglevel:light'] = 'Algo bajo';
$string['loglevel:medium'] = 'Mediano';
$string['loglevel:all'] = 'Todas';
$string['logcleanuptask'] = 'Tarea de limpieza del registro de Ally';
$string['loglifetimedays'] = 'Mantener los registros durante tantos días';
$string['loglifetimedaysdesc'] = 'Conserve los registros de Ally durante este número de días. Establezca 0 para no borrar nunca los registros. Una tarea programada está (por defecto) configurada para ejecutarse diariamente, y eliminará las entradas de registro que tengan un número superior a este número de días.';
$string['logger:filtersetupdebugger'] = 'Registro de configuración de filtro de Ally';
$string['logger:pushtoallysuccess'] = 'Inserción exitosa en el punto de enlace de Ally';
$string['logger:pushtoallyfail'] = 'Inserción fallida en el punto de enlace de Ally';
$string['logger:pushfilesuccess'] = 'Inserción exitosa del/de los archivo(s) en el punto de enlace de Ally';
$string['logger:pushfileliveskip'] = 'Error de inserción de archivos en vivo';
$string['logger:pushfileliveskip_exp'] = 'Omisión de la inserción de archivo(s) en vivo debido a problemas de comunicación. Esta operación se restablecerá una vez que la tarea de actualizaciones de archivos se complete exitosamente. Verifique su configuración.';
$string['logger:pushfileserror'] = 'Inserción fallida en el punto de enlace de Ally';
$string['logger:pushfileserror_exp'] = 'Errores relacionados con la inserción de actualizaciones de contenido en los servicios de Ally.';
$string['logger:pushcontentsuccess'] = 'Inserción exitosa del contenido en el punto de enlace de Ally';
$string['logger:pushcontentliveskip'] = 'Error de inserción de contenido en vivo';
$string['logger:pushcontentliveskip_exp'] = 'Omisión de la inserción de contenido en vivo debido a problemas de comunicación. Esta operación se restablecerá una vez que la tarea de actualizaciones de contenido se complete exitosamente. Verifique su configuración.';
$string['logger:pushcontentserror'] = 'Inserción fallida en el punto de enlace de Ally';
$string['logger:pushcontentserror_exp'] = 'Errores relacionados con la inserción de actualizaciones de contenido en los servicios de Ally.';
$string['logger:addingconenttoqueue'] = 'Adición de contenido a la cola de elementos que se deben insertar';
$string['logger:annotationmoderror'] = 'Hubo un error en la anotación de contenido del módulo de Ally.';
$string['logger:annotationmoderror_exp'] = 'El módulo no se identificó correctamente.';
$string['logger:failedtogetcoursesectionname'] = 'No se pudo obtener el nombre de la sección del curso';
$string['logger:moduleidresolutionfailure'] = 'No se pudo averiguar el ID del módulo';
$string['logger:cmidresolutionfailure'] = 'No se pudo averiguar la ID del módulo del curso';
$string['logger:cmvisibilityresolutionfailure'] = 'No pudo solucionar la visibilidad del módulo del curso';
$string['courseupdatestask'] = 'Insertar eventos del curso en Ally';
$string['logger:pushcoursesuccess'] = 'Inserción exitosa del/de los evento(s) en el punto de enlace de Ally';
$string['logger:pushcourseliveskip'] = 'Error de inserción del evento del curso en vivo';
$string['logger:pushcourseerror'] = 'Error de inserción del evento del curso en vivo';
$string['logger:pushcourseliveskip_exp'] = 'Omisión de la inserción del/de los evento(s) del curso en vivo debido a problemas de comunicación. Esta operación se restablecerá una vez que la tarea de actualizaciones de eventos del curso se complete exitosamente. Verifique su configuración.';
$string['logger:pushcourseserror'] = 'Inserción fallida en el punto de enlace de Ally';
$string['logger:pushcourseserror_exp'] = 'Errores relacionados con la inserción de actualizaciones del curso en los servicios de Ally.';
$string['logger:addingcourseevttoqueue'] = 'Adición de un evento del curso a la cola de elementos que se deben insertar';
$string['logger:cmiderraticpremoddelete'] = 'El ID del módulo del curso no puede eliminarlo.';
$string['logger:cmiderraticpremoddelete_exp'] = 'No se identificó el módulo correctamente. Es posible que no exista debido a la eliminación de la sección o que otro factor haya activado el enlace de eliminación y por eso no se encuentra.';
$string['logger:servicefailure'] = 'Se produjo un error en el uso del servicio.';
$string['logger:servicefailure_exp'] = '<br>Clase: {$a->class}<br>Parámetros: {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'Se produjo un error al asignar un permiso de arquetipo de profesor al rol ally_webservice.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Capacidad: {$a->cap}<br>Permiso: {$a->permission}';
$string['deferredcourseevents'] = 'Enviar eventos de cursos aplazados';
$string['deferredcourseeventsdesc'] = 'Permitir el envío de eventos de cursos almacenados que se acumularon durante el fallo de comunicación con Ally';
