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
 * Strings for component 'certificate', language 'es', branch 'MOODLE_32_STABLE'
 *
 * @package   certificate
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addlinklabel'] = 'Añadir otra opción de actividad enlazada';
$string['addlinktitle'] = 'Seleccione para añadir otra opción de actividad enlazada';
$string['areaintro'] = 'Introducción al certificado';
$string['awarded'] = 'Otorgado';
$string['awardedto'] = 'Otorgado a';
$string['back'] = 'Reverso';
$string['border'] = 'Borde';
$string['borderblack'] = 'Negro';
$string['borderblue'] = 'Azul';
$string['borderbrown'] = 'Marrón';
$string['bordercolor'] = 'Líneas del borde';
$string['bordercolor_help'] = 'Dado que las imágenes pueden aumentar sustancialmente el tamaño del archivo PDF, usted puede elegir imprimir un borde de líneas en lugar de emplear una imagen para el borde (asegúrese de que la opción de imagen del Borde sea No). La opción de líneas del borde imprimirá un bonito borde de tres líneas de anchos variables del color elegido.';
$string['bordergreen'] = 'Verde';
$string['borderlines'] = 'Líneas';
$string['borderstyle'] = 'Imagen del borde';
$string['borderstyle_help'] = 'La opción de imagen del borde le permite elegir una imagen para el borde entre las que están en la carpeta certificateuv/pix/borders. Elija la imagen del borde que desee que aparezca alrededor de las orillas del certificado o seleccione sin borde.';
$string['certificate'] = 'Verificación del código de certificado::';
$string['certificate:addinstance'] = 'Añada una instancia de certificado';
$string['certificate:manage'] = 'Administrar una instancia de certificado';
$string['certificatename'] = 'Nombre de certificado';
$string['certificate:printteacher'] = 'Figurarás como profesor en el certificado si el ajuste de impresión profesor está habilitado';
$string['certificatereport'] = 'Informe de certificados';
$string['certificatesfor'] = 'Certificados para';
$string['certificate:student'] = 'Recuperar un certificado';
$string['certificatetype'] = 'Tipo de Certificado';
$string['certificatetype_help'] = 'Aquí es donde usted determina el diseño del certificado. La carpeta del tipo de certificado incluye cuatro certificados por defecto:
A4 Incrustado imprime en papel tamaño A4 con tipos de letra incrustados.
A4 No-Incrustado imprime en papel tamaño A4 sin tipos de letra incrustados.
Carta incrustado imprime en papel tamaño carta con tipos de letra incrustados.
Carta no-incrustado imprime en papel tamaño carta sin tipos de letra incrustados

Los tipos de diseño no-incrustados emplean los tipos de letra Helvetica y Times. Si usted cree que sus usuarios no tiene instalados estos tipos de letra en sus ordenadores, o si su idioma emplea caracteres y símbolos que no están incluidos en los tipos de letra Helvetica y Times, entonces debe elegir tipos incrustados. Los tipos de diseños  incrustados usan los tipos de letra Dejavusans y Dejavuserif. Esto hará que los archivos PDF sean bastante grandes; por eso no se recomienda emplear una variedad incrustada a menos que realmente la necesite.

Se pueden añadir carpetas con nuevos tipos de letra en la carpeta de certificateuv/type. El nombre de la carpeta y cualquier cadena de idioma nueva deberán añadirse al archivo de idioma del certificado.';
$string['certificate:view'] = 'Ver un certificado';
$string['certify'] = 'Hace constar que';
$string['code'] = 'Código';
$string['completiondate'] = 'Finalización del curso';
$string['course'] = 'Para';
$string['coursegrade'] = 'Calificación del Curso';
$string['coursename'] = 'Curso';
$string['coursetimereq'] = 'Minutos requeridos en el curso';
$string['coursetimereq_help'] = 'Escriba aquí la cantidad mínima de tiempo, en minutos, que el estudiante debe mantener la sesión en el curso antes de que pueda recibir el certificado.';
$string['credithours'] = 'Horas de crédito';
$string['customtext'] = 'Texto personalizado';
$string['customtext_help'] = 'Si quiere que el certificado imprima nombres diferentes para el profesor en lugar de aquellos que tienen asignado el rol de profesor, no elija \'Imprimir Profesor\' ni seleccione imagen de firma, exceptuando la imagen de línea. Introduzca los nombres de los profesores en este cuadro de texto tal como usted quiera que aparezcan. Por defecto, este texto se coloca en la parte inferior izquierda del certificado. Las siguientes marcas (tags) HTML están disponibles:<br>, <p>, <b>, <i>, <u>, <img> (src y width/height son obligatorias), <a> (href es obligatoria), <font> (los atributos posibles son: color, (hex color code), face, (arial, times, courier, helvetica, symbol)).';
$string['date'] = 'En';
$string['datefmt'] = 'Formato de fecha';
$string['datefmt_help'] = 'Elija un formato de fecha para imprimir la fecha en el certificado, o bien elija la última opción para que se imprima la fecha en el formato de fecha del idioma elegido por el usuario.';
$string['datehelp'] = 'Fecha';
$string['deletissuedcertificates'] = 'Borrar certificados emitidos';
$string['delivery'] = 'Entregar';
$string['delivery_help'] = 'Elija aquí cómo desea que sus estudiantes obtengan su certificado.
Abrir en navegador: Abre el certificado en una nueva ventana del navegador.
Forzar descarga: Abre la ventana para descargar archivo del navegador
Certificado por email: Eligiendo esta opción manda el certificado al estudiante como un anexo de email.
Después de que un usuario reciba su certificado, si seleccionan en enlace del certificado en la página inicial del curso, verán la fecha en que recibieron el certificado y podrán visualizar el certificado recibido.';
$string['designoptions'] = 'Opciones de diseño';
$string['download'] = 'Forzar descarga';
$string['emailcertificate'] = 'Email (¡También debe elegir guardar!)';
$string['emailothers'] = 'Email otros';
$string['emailothers_help'] = 'Introduzca las direcciones de correo electrónico aquí, separadas por coma, de quienes deberán ser avisados por correo cuando los estudiantes reciban un certificado.';
$string['emailstudenttext'] = 'Se adjunta su certificado de {$a->course}.';
$string['emailteachermail'] = '{$a->student} ha recibido su certificado: \'{$a->certificate}\' de {$a->course}.

Usted puede verlo aquí:

 {$a->url}';
$string['emailteachermailhtml'] = '{$a->student} ha recibido su certificado: \'<i>{$a->certificate}</i>\' de {$a->course}. Usted puede verlo aquí <a href="{$a->url}">Informe de Certificado</a>.';
$string['emailteachers'] = 'Email a Profesores';
$string['emailteachers_help'] = 'Si se habilita, entonces los profesores serán alertados por correo electrónico cuando los estudiantes reciban un certificad';
$string['entercode'] = 'Introduzca el código del certificado a verificar:';
$string['fontsans'] = 'Fuente Sans-serif';
$string['fontsans_desc'] = 'Fuente Sans-serif para los certificados con fuentes incrustadas';
$string['fontserif'] = 'Fuente Serif';
$string['fontserif_desc'] = 'Fuente Serif para los certificados con fuentes incrustadas';
$string['getcertificate'] = 'Obtener su certificado';
$string['grade'] = 'Calificación';
$string['gradedate'] = 'Fecha de calificación';
$string['gradefmt'] = 'Formato de calificación';
$string['gradefmt_help'] = 'Hay tres opciones de formatos disponibles si usted elige imprimir una calificación en el certificado:

Calificación en porcentaje: Imprime la calificación como porcentaje.
Calificación en puntuación: Imprime el valor en puntos de la calificación.
Calificación en letra: Imprime el porcentaje de calificación con una letra.';
$string['gradeletter'] = 'Calificación en letra';
$string['gradepercent'] = 'Calificación en porcentaje';
$string['gradepoints'] = 'Calificación en puntuación';
$string['imagetype'] = 'Tipo de imagen';
$string['incompletemessage'] = 'Para descargar su certificado, usted debe primero completar todas las actividades requeridas. Por favor, vuelva al curso para completar su trabajo.';
$string['intro'] = 'Introducción';
$string['issued'] = 'Emitido';
$string['issueddate'] = 'Fecha de emisión';
$string['issueoptions'] = 'Opciones de emisión';
$string['landscape'] = 'Apaisado';
$string['lastviewed'] = 'Usted recibió este certificado por última vez en:';
$string['letter'] = 'Carta';
$string['lockingoptions'] = 'Opciones de bloqueo';
$string['modulename'] = 'CertificadoUV';
$string['modulename_help'] = 'Este módulo permite generar certificados dinámicamente, recuerde que esta función debe ser autorizada por la Dirección De Nuevas Tecnologías (DINTEV)';
$string['mycertificates'] = 'Mis certificados';
$string['nocertificates'] = 'No hay certificados';
$string['nocertificatesissued'] = 'No hay certificados emitidos';
$string['nocertificatesreceived'] = 'no ha recibido ningún certificado de curso.';
$string['nofileselected'] = 'Debe seleccionar un archivo a subir!';
$string['nogrades'] = 'Sin calificaciones disponibles';
$string['notapplicable'] = 'N/A';
$string['notfound'] = 'El número de certificado no pudo ser validado.';
$string['notissued'] = 'No emitido';
$string['notissuedyet'] = 'No emitido aún';
$string['notreceived'] = 'Usted no ha recibido este certificado';
$string['openbrowser'] = 'Abrir en ventana nueva';
$string['opendownload'] = 'Pulse en el botón inferior para guardar su certificado en su ordenador..';
$string['openemail'] = 'Pulse en el botón inferior y su certificado se le enviará como anexo en un correo electrónico.';
$string['openwindow'] = 'Pulse en el botón inferior para abrir su certificado en una nueva ventana del navegador.';
$string['or'] = 'O';
$string['orientation'] = 'Orientación';
$string['orientation_help'] = 'Elija si quiere que la orientación de su certificado sea vertical o apaisada.';
$string['pluginadministration'] = 'Administración del certificado';
$string['pluginname'] = 'CertificadoUV';
$string['portrait'] = 'Vertical';
$string['printdate'] = 'Fecha de impresión';
$string['printdate_help'] = 'Esta es la fecha que se imprimirá, si se selecciona que se imprima la fecha.
Si se selecciona la fecha de finalización del curso, pero el estudiante no lo hubiese finalizado aún, se imprimirá la fecha recibida.
También puede usted seleccionar imprimir la fecha en función de cuándo fue calificada una actividad. Si se emite un certificado antes de que se califique esa actividad, se imprimirá la fecha recibida.';
$string['printerfriendly'] = 'Página para imprimir';
$string['printgrade'] = 'Imprimir calificación';
$string['printgrade_help'] = 'Puede elegir cualquier ítem de calificación del curso, disponible en
del libro de calificaciones, para imprimir la calificación obtenida por el usuario en el certificado. Los items de calificación se listan en el orden en que aparecen en el libro de calificaciones. Seleccione debajo el formato de la calificación.';
$string['printhours'] = 'Imprimir crédito de horas';
$string['printhours_help'] = 'Introduzca aquí el número de horas de crédito que se imprimirán en el certificado.';
$string['printnumber'] = 'Imprimir código';
$string['printnumber_help'] = 'Un código individual de 10 dígitos de letras y números aleatorios puede imprimirse en el certificado. Este número podrá después ser verificado al compararlo con el número de código mostrado en el informe de certificados.';
$string['printoutcome'] = 'Imprimir competencia (outcome)';
$string['printoutcome_help'] = 'Usted puede elegir cualquier competencia del curso para imprimir el nombre de la competencia y el resultado obtenido por el usuario en el certificado. Un ejemplo sería:

Competencia en la tarea: Eficiente.';
$string['printseal'] = 'Imagen del sello o del logo';
$string['printseal_help'] = 'Esta opción le permite elegir un sello o un logo a imprimir en el certificado, entre los que están en la carpeta certificate/pix/seals. Por defecto, esta imágen se pondrá en la esquina inferior derecha del certificado.';
$string['printsignature'] = 'Imagen de firma';
$string['printsignature_help'] = 'Esta opción le permite elegir una imagen de firma digitalizada para imprimir en el certificado, entre las que están en la carpeta certificate/pix/signatures. Por defecto, esta imagen se pondrá en la esquina inferior izquierda del certificado.';
$string['printteacher'] = 'Imprimir nombre(s) de profesor(es)';
$string['printteacher_help'] = 'Para imprimir el nombre del profesor en el certificado, asigne el rol de profesor a nivel del módulo. Para hacer esto, por ejemplo, usted tiene más de un profesor para el curso o tiene más de un certificado en el curso y desea imprimir diferentes nombres en cada certificado.

Elija \'editar el certificado\', después elija la pestaña para roles asignados localmente. Después asigne el rol de profesor-editor al certificado (no necesitan SER profesores en el curso --  puede asignar este rol a quien desee). Estos nombres serán impresos en los certificados como profesor.';
$string['printwmark'] = 'Marca de agua';
$string['printwmark_help'] = 'Puede ponerse una marca de agua en el fondo del certificado. Una marca de agua es un gráfico desvanecido. Una marca de agua podría ser un logo, un escudo, un sello, una frase, o cualquier otro elemento que usted quiera emplear como fondo gráfico.';
$string['receivedcerts'] = 'Certificados recibidos';
$string['receiveddate'] = 'Fecha de recepción';
$string['removecert'] = 'Certificados emitidos eliminados';
$string['report'] = 'Informe';
$string['reportcert'] = 'Informe de Certificados';
$string['reportcert_help'] = 'Si elige sí aquí, entonces la fecha de recepción, número de código y el nombre del curso para este certificado se mostrarán en los informes de certificado de usuario. Si selecciona imprimir una calificación en este certificado, entonces dicha calificación también aparecerá en el informe del certificado.';
$string['requiredtimenotmet'] = 'Usted debe pasar al menos un mínimo de {$a->requiredtime} minutos en el curso antes de poder acceder a este certificado';
$string['requiredtimenotvalid'] = 'El tiempo requerido debe ser un número válido mayor que 0';
$string['reviewcertificate'] = 'Revisar su certificado';
$string['savecert'] = 'Guardar certificados';
$string['savecert_help'] = 'Si elige esta opción, entonces una copia de cada archivo PDF de certificado de usuario se guardará en la carpeta moddata . Se mostrará un enlace para cada certificado de usuario guardado en el informe de certificados.';
$string['seal'] = 'Sello';
$string['sigline'] = 'línea';
$string['signature'] = 'Firma';
$string['statement'] = 'Ha completado el curso';
$string['summaryofattempts'] = 'Resumen de certificados recibidos anteriormente';
$string['teachertosign'] = 'Profesor a firmar';
$string['teachertosign_help'] = 'Seleccione el profesor que aparecerá como firmante en el certificado, recuerde que la firma de este docente debe estar digitalizada, si no lo ha hecho por favor dirijase a la DINTEV';
$string['textoptions'] = 'Opciones de texto';
$string['timestartcourse'] = 'Fecha Inicial';
$string['timefinalcourse'] = 'Fecha Final';
$string['title'] = 'CERTIFICADO de APROVECHAMIENTO';
$string['to'] = 'Otorgado a';
$string['typeA4_embedded'] = 'A4 Incrustado';
$string['typeA4_non_embedded'] = 'A4 No Incrustado';
$string['typeletter_embedded'] = 'Carta incrustado';
$string['typeletter_non_embedded'] = 'Carta no incrustado';
$string['unsupportedfiletype'] = 'El archivo debe ser un archivo JPEG o PNG';
$string['uploadimage'] = 'Subir imagen';
$string['uploadimagedesc'] = 'Este botón le llevará a una nueva pantalla en la que podrá subir imágenes.';
$string['userdateformat'] = 'Formato de fecha del usuario';
$string['validate'] = 'Verificar';
$string['verifycertificate'] = 'Verificar certificado';
$string['viewcertificateviews'] = 'Ver {$a} certificados emitidos';
$string['viewed'] = 'Usted recibió este certificado en:';
$string['viewtranscript'] = 'Ver certificados';
$string['watermark'] = 'Marca de agua';
$string['datestartcourse'] = 'Fecha Inicial';
$string['dateendcourse'] = 'Fecha Final';
$string['teachertosign'] = 'Profesor a firmar';
$string['teachertosign_help'] = 'Seleccione el profesor que aparecerá como firmante en el certificado, recuerde que la firma de este docente debe estar digitalizada, si no lo ha hecho por favor dirijase a la DINTEV';