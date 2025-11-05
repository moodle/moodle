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
 * Strings for plugin 'reminders', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package   local_reminders
 * @copyright 2012 Isuru Madushanka Weerarathna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 $string['activityconfduein'] = 'Vencimiento en';
 $string['activityconfexplicitenable'] = 'Activación de recordatorio explícito';
 $string['activityconfexplicitenabledesc'] = 'Si está marcado, los profesores o las autoridades pertinentes deben <strong>habilitar explícitamente</strong> los recordatorios para cada actividad en la página de configuración de recordatorios del curso. Debido a esto, todos los recordatorios de actividades estarán deshabilitados por defecto, independientemente del horario definido más abajo. Esta configuración no afectará los recordatorios de actividades vencidas.';
 $string['activityconfexplicitenablehint'] = 'El o los administradores del sitio han deshabilitado el envío de recordatorios de actividades por defecto. Esto significa que los profesores deben <em>habilitar explícitamente</em> los recordatorios para las actividades de este curso que deseen enviar.';
 $string['activityconfupcomingactivities'] = 'Actividades próximas';
 $string['activityconfupcomingactivitiesdesc'] = 'No se enviarán recordatorios para las actividades no seleccionadas.';
 $string['activityconfnoupcomingactivities'] = 'No hay actividades próximas para este curso.';
 $string['activitydueopenahead'] = 'Envío de aperturas de actividades antes de:';
 $string['activitydueopenaheaddesc'] = 'Días antes de enviar los recordatorios para las aperturas de actividades. Esta configuración solo es válida si las aperturas de actividades están habilitadas para enviar recordatorios desde la configuración anterior.';
 $string['activityopeningseparation'] = 'Separar aperturas de actividades:';
 $string['activityopeningseparationdesc'] = 'Mostrar las aperturas de actividades como una entrada separada en la página de configuración de recordatorios del curso.';
 $string['activityremindersboth'] = 'Para aperturas y cierres';
 $string['activityremindersonlyopenings'] = 'Solo para aperturas de actividades';
 $string['activityremindersonlyclosings'] = 'Solo para cierres de actividades';
 $string['activityignoreincompletes'] = 'No enviar recordatorios una vez completada:';
 $string['activityignoreincompletesdetails'] = 'Si está marcado, no se enviarán recordatorios si la actividad ya ha sido completada por el usuario, <strong>antes</strong> de que termine la actividad.';
 $string['admintreelabel'] = 'Recordatorios';
 $string['calendareventupdatedprefix'] = 'ACTUALIZADO';
 $string['calendareventremovedprefix'] = 'ELIMINADO';
 $string['calendareventcreatedprefix'] = 'AÑADIDO';
 $string['calendareventoverdueprefix'] = 'VENCIDO';
 $string['caleventchangedheading'] = 'Recordatorios de cambios en eventos del calendario';
 $string['caleventchangedheadingdetails'] = 'Estas configuraciones se verificarán <strong>antes</strong> de considerar el tipo de evento individual.';
 $string['categoryheading'] = 'Recordatorios de eventos de categoría del curso';
 $string['categorynosendforended'] = 'No enviar recordatorios para cursos completados:';
 $string['categorynosendforendeddescription'] = 'Si está marcado, no se enviarán recordatorios para los cursos completados.';
 $string['contentdescription'] = 'Descripción';
 $string['contenttypecategory'] = 'Categoría';
 $string['contenttypecourse'] = 'Curso';
 $string['contenttypeactivity'] = 'Actividad';
 $string['contenttypegroup'] = 'Grupo';
 $string['contenttypeuser'] = 'Usuario';
 $string['contenttypelocation'] = 'Ubicación';
 $string['contentwhen'] = 'Cuándo';
 $string['courseheading'] = 'Recordatorios de eventos del curso';
 $string['custom'] = 'Personalizado';
 $string['customschedulefallback'] = 'Horario personalizado por defecto';
 $string['customschedulefallbackdesc'] = 'Si está marcado, los horarios personalizados se revertirán al valor especificado en las actividades para <strong>tipos de eventos desconocidos</strong>.';
 $string['days7'] = '7 días';
 $string['days3'] = '3 días';
 $string['days1'] = '1 día';
 $string['dueheading'] = 'Recordatorios de eventos de actividad';
 $string['emailconfigsheading'] = 'Personalización de correos electrónicos de recordatorio';
 $string['emailfootercustomname'] = 'Pie de página de correo electrónico personalizado';
 $string['emailfootercustomnamedesc'] = 'Especifica el contenido del pie de página que se insertará en cada mensaje de correo electrónico de recordatorio. Si este contenido está vacío y el pie de página predeterminado está deshabilitado, entonces el pie de página se eliminará completamente de los recordatorios.';
 $string['emailfooterdefaultname'] = 'Usar pie de página predeterminado';
 $string['emailfooterdefaultnamedesc'] = 'Si está marcado, el pie de página predeterminado del correo electrónico de recordatorio contendrá un enlace al calendario de Moodle. De lo contrario, se usará el contenido proporcionado en el pie de página personalizado.';
 $string['emailheadercustomname'] = 'Encabezado de correo electrónico personalizado';
 $string['emailheadercustomnamedesc'] = 'Especifica el contenido del encabezado que se insertará en cada mensaje de correo electrónico de recordatorio. Esto puede usarse para agregar una marca al mensaje de correo electrónico.';
 $string['enabled'] = 'Habilitado';
 $string['enabledoverdue'] = 'Habilitar vencidos';
 $string['enableddescription'] = 'Habilitar/deshabilitar el complemento de recordatorios';
 $string['enabledchangedevents'] = 'Enviar cuando el evento sea actualizado:';
 $string['enabledremovedevents'] = 'Enviar cuando el evento sea eliminado:';
 $string['enabledaddedevents'] = 'Enviar cuando el evento sea creado:';
 $string['enabledchangedeventsdescription'] = 'Indica si se deben enviar recordatorios cuando se actualice un evento del calendario.';
 $string['enabledremovedeventsdescription'] = 'Indica si se deben enviar recordatorios cuando se elimine un evento del calendario.';
 $string['enabledaddedeventsdescription'] = 'Indica si se deben enviar recordatorios cuando se cree un evento del calendario.';
 $string['enabledforcalevents'] = 'Habilitar para eventos de cambio en el calendario:';
 $string['enabledforcaleventsdescription'] = 'Habilitar el envío de recordatorios para este tipo de evento cuando se cree, elimine o actualice un evento del calendario.';
 $string['eventtypegradingdue'] = 'Calificación vencida';
 $string['eventtypeexpectcompletionon'] = 'Se espera la finalización en';
 $string['eventtypeopen'] = 'Actividad abierta';
 $string['eventtypeclose'] = 'Actividad cerrada';
 $string['explaincategoryheading'] = 'Configuración de recordatorios para eventos de categorías de curso.';
 $string['explaincourseheading'] = 'Configuración de recordatorios para eventos de curso. Estos eventos provienen de un curso.';
 $string['explaindueheading'] = 'Configuración de recordatorios para eventos de actividad. Estos eventos provienen de actividades/módulos dentro de un curso.';
 $string['explaingroupheading'] = 'Configuración de recordatorios para eventos de grupo. Estos eventos son solo para un grupo específico.';
 $string['explaingroupshowname'] = 'Indica si el nombre del grupo debe incluirse en el mensaje enviado o no.';
 $string['explainrolesallowedfor'] = 'Elija qué usuarios con los roles mencionados anteriormente pueden recibir recordatorios.';
 $string['explainsendactivityreminders'] = 'Indica en qué estado de actividad se deben enviar los recordatorios.';
 $string['explainsiteheading'] = 'Configuración de recordatorios para eventos del sitio. Estos eventos son relevantes para todos los usuarios del sitio.';
 $string['explainuserheading'] = 'Configuración de recordatorios para eventos de usuario. Estos eventos son individuales para cada usuario.';
 $string['excludedmodules'] = 'Módulos excluidos:';
 $string['excludedmodulesdesc'] = 'No se enviarán recordatorios si un evento se genera desde los módulos seleccionados anteriormente. Esta configuración es global y se aplica a cualquier tipo de evento.';
 $string['filterevents'] = 'Filtrar eventos del calendario:';
 $string['filtereventsdescription'] = 'Qué eventos del calendario deben ser filtrados y a cuáles enviar recordatorios.';
 $string['filtereventsonlyhidden'] = 'Solo eventos ocultos en el calendario';
 $string['filtereventsonlyvisible'] = 'Solo eventos visibles en el calendario';
 $string['filtereventssendall'] = 'Todos los eventos';
 $string['groupheading'] = 'Recordatorios de eventos de grupo';
 $string['groupshowname'] = 'Mostrar el nombre del grupo en el mensaje:';
 $string['messageprovider:reminders_course'] = 'Notificaciones de recordatorio para eventos de curso';
 $string['messageprovider:reminders_coursecategory'] = 'Notificaciones de recordatorio para eventos de categoría de curso';
 $string['messageprovider:reminders_due'] = 'Notificaciones de recordatorio para eventos de actividad';
 $string['messageprovider:reminders_group'] = 'Notificaciones de recordatorio para eventos de grupo';
 $string['messageprovider:reminders_site'] = 'Notificaciones de recordatorio para eventos de sitio';
 $string['messageprovider:reminders_user'] = 'Notificaciones de recordatorio para eventos de usuario';
 $string['messagetitleprefix'] = 'Prefijo del título del mensaje:';
 $string['messagetitleprefixdescription'] = 'Este texto se insertará como un prefijo (dentro de corchetes) al título de cada mensaje de recordatorio que se envíe.';
 $string['moodlecalendarname'] = 'Calendario de Moodle';
 $string['overduemessage'] = '¡Esta actividad está vencida!';
 $string['plugindisabled'] = 'El complemento está deshabilitado por el administrador.';
 $string['pluginname'] = 'Recordatorios de eventos';
 $string['privacy:metadata'] = 'El complemento Recordatorios de eventos no almacena ningún dato personal.';
 $string['overdueactivityreminders'] = 'Recordatorios de actividades vencidas:';
 $string['overdueactivityremindersdescription'] = 'Si está marcado, los recordatorios se enviarán a los usuarios que tengan la actividad vencida.';
 $string['overduewarnmessage'] = 'Mensaje de advertencia de vencimiento:';
 $string['overduewarnmessagedescription'] = 'Ingrese un <strong>texto simple</strong> que se incluirá en los correos electrónicos de vencimiento en color rojo. Si está vacío, no se mostrará ningún mensaje. Esto solo se habilitará si los correos electrónicos de vencimiento están habilitados.';
 $string['overduewarnprefix'] = 'Prefijo del título de vencimiento:';
 $string['overduewarnprefixdescription'] = 'Ingrese un <strong>prefijo simple</strong> que se incluirá en el título de los correos electrónicos de vencimiento. Si está vacío, no se añadirá ningún prefijo. Esto solo se habilitará si los correos electrónicos de vencimiento están habilitados.';
 $string['reminderdaysahead'] = 'Enviar antes de:';
 $string['reminderdaysaheadcustom'] = 'Horario personalizado:';
 $string['reminderdaysaheadschedule'] = 'Horario';
 $string['reminderdaysaheadcustomdetails'] = 'Especifique adicionalmente el horario deseado para enviar los recordatorios antes del evento.';
 $string['reminderfrom'] = 'Recordatorio de';
 $string['reminderstask'] = 'Tarea de recordatorios';
 $string['reminderstaskclean'] = 'Limpiar registros de recordatorios locales';
 $string['rolesallowedfor'] = 'Roles permitidos:';
 $string['sendactivityreminders'] = 'Recordatorios de actividades:';
 $string['sendas'] = 'Enviar como:';
 $string['sendasadmin'] = 'Como Administrador del Sitio';
 $string['sendasdescription'] = 'Especifique como quién deben enviarse estos correos electrónicos de recordatorio.';
 $string['sendasnametitle'] = 'Nombre sin respuesta:';
 $string['sendasnamedescription'] = 'Especifique el nombre de usuario para los correos electrónicos de recordatorio cuando se envíen como un usuario sin respuesta.';
 $string['sendasnoreply'] = 'Dirección sin respuesta';
 $string['showmodnameintitle'] = 'Mostrar el nombre del módulo en el asunto del correo electrónico';
 $string['showmodnameintitledesc'] = 'Si está marcado, se agregará el nombre del módulo correspondiente al asunto del correo electrónico de recordatorio.';
 $string['siteheading'] = 'Recordatorios de eventos del sitio';
 $string['taskreminder'] = 'Tarea de recordatorios';
 $string['titlesubjectprefix'] = 'Recordatorio';
 $string['userheading'] = 'Recordatorios de eventos de usuario';
 $string['useservertimezone'] = "Usar zona horaria del servidor";
