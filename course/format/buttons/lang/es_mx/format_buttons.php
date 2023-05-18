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
 * format_buttons_renderer
 *
 * @package    format_buttons
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['above'] = 'Arriba de los botones de lista';
$string['below'] = 'Debajo de lasección visible';
$string['colorcurrent'] = 'Color del botón de la sección actual';
$string['colorcurrent_help'] = 'La sección actual es la sección marcada con resaltado.<br>Defina un color en hexadecimal.
<i>Ejemplo: #fab747</i><br>Si Usted desea usar el color por defecto, déjelo vacío.';
$string['colorvisible'] = 'Color del botón de la sección visible';
$string['colorvisible_help'] = 'LA sección visible es la sección sleccionada.<br>Defina un color en hexadecimal.
<i>Ejemplo: #747fab</i><br>Si Usted desea usar el color por defecto, déjelo vacío.';
$string['currentsection'] = 'Este tópico';
$string['deletesection'] = 'Eliminar tópico';
$string['divisor'] = 'Número de secciones a agrupar - {$a}';
$string['divisortext'] = 'Título del agrupamiento - {$a}';
$string['divisortext_help'] = 'El agrupamiento de secciones se usa para separar secciones por tipo o por módulos.
<i>Ejemplo: El curso tiene 10 secciones, divididas en dos módulos: Teórico (con 5 secciones) y Práctico (con 5 secciones).<br>
Defina el título con "Teórico" y configure el número de secciones a 5.</i><br><br>
Sugerencia: Si lo desea, use la marca (tag)  <strong>&lt;br&gt;</strong> type <strong>[br]</strong>.';
$string['editing'] = 'Los botones están deshabilitados mientras esté activo el modo de edición.';
$string['editsection'] = 'Editar tópico';
$string['hidefromothers'] = 'Ocultar tópico';
$string['no'] = 'No';
$string['pluginname'] = 'Formato de botones';
$string['section0name'] = 'General';
$string['sectionname'] = 'Tópico';
$string['sectionposition'] = 'Posición de la sección cero';
$string['sectionposition_help'] = 'La sección cero aparecerá junto a la sección visible.<br><br>
<strong>Arriba de la lista de botone</strong><br>Use esta opción si Usted desea añadir algun texto o recurso antes de la lista de botones.
<i>Ejemplo: Defina una imagen para ilustrar el curso.</i><br><br><strong>Debajo de la sección visible</strong><br>
Use esta opción si quiere añadir un texto o recurso después de la sección visible.
<i>Ejemplo: Recursos o enlaces a mostrarse sin importar la región visible.</i><br><br>';
$string['showdefaultsectionname'] = 'Mostrar por defecto el nombre de las secciones';
$string['showdefaultsectionname_help'] = 'Si no se configura nombre para la sección, no se mostrará nada.<br>
Por definición, un tópico sin nombre se muestra como <strong>Tópico N</strong>.';
$string['showfromothers'] = 'Mostrar tópico';
$string['yes'] = 'Si';
$string['sequential'] = 'Secuencial';
$string['notsequentialdesc'] = 'Cada nuevo grupo empieza a contar secciones de uno.';
$string['sequentialdesc'] = 'Cuente los números de sección ignorando el agrupamiento.';
$string['sectiontype'] = 'Estilo de lista';
$string['numeric'] = 'Numérico';
$string['roman'] = 'Números romanos';
$string['alphabet'] = 'Alfabeto';
$string['buttonstyle'] = 'Estilo del botón';
$string['buttonstyle_help'] = 'Define la forma geométrica de los botones.';
$string['circle'] = 'Circulo';
$string['square'] = 'Plaza';
$string['inlinesections'] = 'Secciones separadas en líneas';
$string['inlinesections_help'] = 'Muestra cada sección en una línea.';
