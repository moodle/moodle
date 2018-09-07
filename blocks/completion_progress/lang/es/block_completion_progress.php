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
 * Strings for component 'block_completion_progress', language 'es', branch 'MOODLE_35_STABLE'
 *
 * @package   block_completion_progress
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['completed_colour'] = '#73A839';
$string['completed_colour_descr'] = 'Código de Color HTML para los elementos que se han completado';
$string['completed_colour_title'] = 'Color completado';
$string['completion_not_enabled'] = 'El rastreo de finalización no está habilitado en este sitio.';
$string['completion_not_enabled_course'] = 'El rastreo de finalización no está habilitado en este curso.';
$string['completion_progress:addinstance'] = 'Añadir un nuevo bloque de Estado de Finalización';
$string['completion_progress:myaddinstance'] = 'Añadir un nuevo bloque de Estado de Finalización a la página de inicio';
$string['completion_progress:overview'] = 'Ver el resumen del curso del Estado de Finalización para todos los estudiantes';
$string['completion_progress:showbar'] = 'Mostrar la barra en el bloque de Estado de Finalización';
$string['config_activitiesincluded'] = 'Actividades incluidas';
$string['config_activitycompletion'] = 'Todas las actividades con Estado de Finalización';
$string['config_default_title'] = 'Barra de Progreso';
$string['config_group'] = 'Visible solo para el grupo';
$string['config_header_monitored'] = 'Seguimiento';
$string['config_icons'] = 'Usar iconos en la barra';
$string['config_longbars'] = 'Cómo presentar barras largas';
$string['config_orderby'] = 'Ordenar barra por';
$string['config_orderby_course_order'] = 'Ordenación en curso';
$string['config_orderby_due_time'] = 'Tiempo usando fecha "{$a}"';
$string['config_percentage'] = 'Mostrar porcentaje a estudiantes';
$string['config_scroll'] = 'Deslizar';
$string['config_selectactivities'] = 'Seleccionar actividades';
$string['config_selectedactivities'] = 'Actividades seleccionadas';
$string['config_squeeze'] = 'Apretujar';
$string['config_title'] = 'Alternar título';
$string['config_wrap'] = 'Envolver';
$string['coursenametoshow'] = 'Nombre del curso a mostrar en el Panel';
$string['defaultlongbars'] = 'Presentación predeterminada para las barras largas';
$string['fullname'] = 'Nombre completo del curso';
$string['futureNotCompleted_colour'] = '#025187';
$string['futureNotCompleted_colour_descr'] = 'Código de color HTML para los elementos futuros que no se han completado';
$string['futureNotCompleted_colour_title'] = 'Color futuro no-completado';
$string['how_activitiesincluded_works'] = 'Como funciona incluyendo actividades';
$string['how_activitiesincluded_works_help'] = '<p>De manera predeterminada, todas las actividades con el conjunto de ajustes de finalización de la actividad se incluyen en la barra. </p> <p> También puede seleccionar manualmente las actividades a ser incluidas.</p>';
$string['how_group_works'] = 'Como funciona grupo visible';
$string['how_group_works_help'] = '<p>La selección de un grupo limitará la visualización de este bloque a ese grupo solamente.</p>';
$string['how_longbars_works'] = 'Como son presentadas las barras largas';
$string['how_longbars_works_help'] = '<p>Cuando las barras excedan una longitud establecida, la forma en que se pueden presentar es una de las siguientes: </ p> <ul> <li>Exprimida en una barra horizontal</li><li>Desplazándola hacia los lados para mostrar segmentos de la barra desbordantes</ li> <li>Envuelta para mostrar todos los segmentos de la barra en varias líneas</ li> </ ul><p> Tenga en cuenta que cuando se envuelve la barra, no se mostrará el indicador AHORA. </ p>';
$string['how_ordering_works'] = 'Como funciona ordenamiento';
$string['how_ordering_works_help'] = '<p>Hay dos formas de ordenar las actividades en el bloque de Estado de finalización.</p> <ul> <li> <em>El tiempo usando "Esperando la finalización en" fecha </em> (predeterminado) <br />Las fechas de finalización previstas de actividades/recursos se utilizan para ordenar la barra. En el caso de que las actividades/recursos no tengan una fecha de finalización prevista, en su lugar se utiliza el orden en el curso. Cuando se utiliza esta opción, se muestra el indicador AHORA. </li> <li> <em> Ordenamiento en curso </em> <br /> Las actividades/recursos se presentan en el mismo orden en que están en la página del curso. Cuando se utiliza esta opción, las fechas de finalización prevista se ignoran. Cuando se utiliza esta opción, no se muestra el indicador AHORA. </li> </ul>';
$string['how_selectactivities_works'] = 'Como funciona incluyendo actividades';
$string['how_selectactivities_works_help'] = '<p> Para seleccionar manualmente las actividades a incluir en la barra, asegúrese que "Actividades incluidas" esté marcado como "actividades seleccionadas". </p> <p> Sólo las actividades con ajustes de finalización pueden ser incluidas. </p> <p> Mantenga pulsada la tecla CTRL para seleccionar múltiples actividades. </p>';
$string['lastonline'] = 'Último en el curso';
$string['mouse_over_prompt'] = 'Coloque el puntero del ratón encima o toque la barra para más información.';
$string['no_activities_config_message'] = 'No hay actividades o recursos con la finalización de actividad establecida o no se han seleccionado actividades o recursos. Configure los requisitos de finalización de las actividades en las actividades y los recursos, y luego configure este bloque.';
$string['no_activities_message'] = 'No hay actividades o recursos en seguimiento. Utilice la configuración para establecer el seguimiento.';
$string['no_blocks'] = 'No hay bloques de Estado de Finalización establecidos para sus cursos.';
$string['no_courses'] = 'No está inscrito en ningún curso. Sólo se mostrarán las barras de cursos matriculados.';
$string['not_all_expected_set'] = 'No todas las actividades con finalización tienen una fecha establecida "{$a}".';
$string['notCompleted_colour'] = '#C71C22';
$string['notCompleted_colour_descr'] = 'El código de color HTML para los elementos actuales que aún no se han completado';
$string['notCompleted_colour_title'] = 'Color no-completado';
$string['no_visible_activities_message'] = 'Ninguna de las actividades en seguimiento es visible actualmente.';
$string['now_indicator'] = 'AHORA';
$string['overview'] = 'Vista general de estudiantes';
$string['pluginname'] = 'Estado de Finalización';
$string['progress'] = 'Progreso';
$string['progressbar'] = 'Estado de Finalización';
$string['shortname'] = 'Nombre corto de curso';
$string['showallinfo'] = 'Mostrar toda la información';
$string['showinactive'] = 'Mostrar estudaintes inactivos en Vista general';
$string['submitted'] = 'Enviado';
$string['submittednotcomplete_colour'] = '#FFCC00';
$string['submittednotcomplete_colour_descr'] = 'El código de color HTML para los elementos enviados, pero no completados aún.';
$string['submittednotcomplete_colour_title'] = 'Color de enviado pero sin completar';
$string['time_expected'] = 'Esperado';
$string['why_set_the_title'] = '¿Porqué querría configurar el título de instancia de bloque?';
$string['why_set_the_title_help'] = '<p>Pueden existir múltiples instancias del bloque de estado de finalización. Es posible utilizar diferentes bloques de estado de finalización para seguir diferentes tipos de actividades o recursos. Por ejemplo, podría hacer seguimiento del progreso de las tareas en un bloque y los cuestionarios en otro. Por esta razón puede anular el título predeterminado y establecer un título más adecuado para cada caso. </p>';
$string['why_show_precentage'] = '¿Porqué mostrar un porcentaje de progreso a estudiantes?';
$string['why_show_precentage_help'] = '<p> Es posible mostrar un porcentaje global del progreso a los estudiantes. </ p> <p>Se calcula como el número de actividades realizadas dividido por el número total de actividades en la barra. </ p> <p>El porcentaje de progreso aparece hasta que el estudiante pasa el ratón sobre un elemento de la barra. </ p>';
$string['why_use_icons'] = '¿Porqué querría Usted usar íconos?';
$string['why_use_icons_help'] = '<p>Es posible que desee añadir iconos de marca y cruz en el Estado de Finalización para hacer este bloque visualmente más accesible para los estudiantes con ceguera al color. </p> <p>Se puede también hacer que el significado del bloque sea más claro si cree que los colores no son intuitivos, ya sea por razones culturales o personales. </p>';
$string['wrapafter'] = 'Al dar-la-vuelta, limitar filas a';
