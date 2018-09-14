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
 * Strings for component 'format_onetopic', language 'es'
 *
 * @since 2.4
 * @package format_onetopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['currentsection'] = 'Este tema';
$string['pluginname'] = 'Temas en pestañas';
$string['sectionname'] = 'Tema';
$string['page-course-view-topics'] = 'Alguna página principal de curso en formato onetopic';
$string['page-course-view-topics-x'] = 'Alguna página de curso en formato onetopic';
$string['hidefromothers'] = 'Ocultar tema';
$string['showfromothers'] = 'Mostrar tema';
$string['hidetabsbar'] = 'Ocultar barra de pestañas';
$string['hidetabsbar_help'] = 'Oculta la barra de pestañas en la página principal del curso, la navegación se llevará a cabo con la barra de navegación entre temas.';

$string['movesectionto'] = 'Mover el tema actual';
$string['movesectionto_help'] = 'Mover el tema actual <strong>antes</strong> de (para temas a la izquierda del actual) o <strong>después</strong> de (para temas a la derecha del actual) el tema que seleccione';

$string['utilities'] = 'Utilidades de edición de pestañas';
$string['disableajax'] = 'Acciones de edición asíncronas';
$string['disable'] = 'Deshabilitar';
$string['enable'] = 'Habilitar';
$string['disableajax_help'] = 'Deshabilitarlas le permite mover recursos entre pestañas de temas. Sólo se deshabilitan las acciones asíncronas en la sesión actual, no es permanente.';

$string['subtopictoright'] = 'Mover a la derecha como pestaña hija';

$string['duplicatesection'] = 'Duplicar tema actual';
$string['duplicatesection_help'] = 'Usado para duplicar los recursos del tema actual en un nuevo tema.';
$string['duplicate'] = 'Duplicar';
$string['duplicating'] = 'Duplicando';
$string['creating_section'] = 'Creando el nuevo tema';
$string['rebuild_course_cache'] = 'Recreando el caché del curso';
$string['duplicate_confirm'] = '¿Está seguro de que desea duplicar el tema actual? La tarea puede tardar un buen rato dependiendo de la cantidad de recursos.';
$string['cantcreatesection'] = 'Error creando un nuevo tema';
$string['progress_counter'] = 'Duplicando actividades ({$a->current}/{$a->size})';
$string['progress_full'] = 'Duplicando el tema';
$string['error_nosectioninfo'] = 'El tema indicado no contiene información.';

$string['level'] = 'Nivel';
$string['index'] = 'Inicio';
$string['asprincipal'] = 'Normal, como una pestaña de primer nivel';
$string['aschild'] = 'Hijo de la pestaña anterior';
$string['level_help'] = 'Cambiar el nivel de la pestaña, para aparecer como un subnivel de pestañas.';
$string['fontcolor'] = 'Color de fuente';
$string['fontcolor_help'] = 'Utilizado para cambiar el color de la fuente en el nombre de la pestaña. El valor puede ser un color en cualquier representación válida para CSS como por ejemplo: <ul><li>Hexadecimal: #ffffff</li><li>RGB: rgb(0,255,0)</li><li>Nombre: green</li></ul>';
$string['bgcolor'] = 'Color de fondo';
$string['bgcolor_help'] = 'Utilizado para cambiar el color de fondo del texto en el nombre de la pestaña. El valor puede ser un color en cualquier representación válida para CSS como por ejemplo: <ul><li>Hexadecimal: #ffffff</li><li>RGB: rgb(0,255,0)</li><li>Nombre: green</li></ul>';
$string['cssstyles'] = 'Propiedades CSS';
$string['cssstyles_help'] = 'Sirve para cambiar las propiedades CSS de la pestaña. Utilice el formato tradicional del atributo <em>style</em> de una etiqueta html. Ejemplo: <br /><strong>font-weight: bold; font-size: 16px;</strong>';
$string['firsttabtext'] = 'Nombre de la primera pestaña (inicio) en el subnivel';
$string['firsttabtext_help'] = 'Si la pestaña tiene un subnivel de pestañas, éste será el texto para la primera pestaña del subnivel.';

$string['coursedisplay'] = 'Modo de visualización de la sección 0';
$string['coursedisplay_help'] = 'Define como se muestra la sección 0: como la primera pestaña o como una sección encima de las demás pestañas.';
$string['coursedisplay_single'] = 'Como pestaña';
$string['coursedisplay_multi'] = 'Arriba de las pestañas';

$string['templatetopic'] = 'Usar resumen de tema como plantilla';
$string['templatetopic_help'] = 'Permite usar el resumen del tema como una plantilla, de esa manera se pueden ubicar los recursos en cualquier parte del contenido, no necesariamente como listas secuenciales como se muestran tradicionalmente en Moodle. <br />Para ubicar un recurso, simplemente agrege en el resumen de la sección el nombre del recurso encerrado entre dobles corchetes, ejemplo: <strong>[[Foro de Novedades]]</strong>. Su comportamiento es similar al filtro por nombre de actividad con la diferencia de que se puede agregar el icono del recurso y además se puede elegir cuales recursos se muestran y cuales no.';
$string['templetetopic_not'] = 'No, mostrar normal';
$string['templetetopic_single'] = 'Si, usar el resumen como una plantilla';
$string['templetetopic_list'] = 'Si, usar el resumen como plantilla y listar los recursos no referenciados';
$string['templatetopic_icons'] = 'Mostrar icono en enlaces de recursos en el resumen';
$string['templatetopic_icons_help'] = 'Esta opción define si se muestran o no los iconos de los recursos como parte del nombre, cuando el resumen del tema se utiliza como plantilla.';
