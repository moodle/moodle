<?PHP // $Id$ 
      // install.php - created with Moodle 1.4 alpha (2004081500)


$string['admindirerror'] = 'El directorio especificado para admin es incorrecto';
$string['admindirname'] = 'Directorio Admin';
$string['admindirsetting'] = '
<p>Muy pocos servidores web usan /admin como URL especial para permitirle acceder a un panel de control o similar. Desgraciadamente, esto entra en conflicto con la ubicación estándar de las páginas de administración de Moodle Usted puede corregir esto renombrando el directorio admin en su instalación, y poniendo aquí ese nuevo nombre. Por ejemplo: <blockquote> moodleadmin</blockquote>.
Así se corregirán los enlaces admin en Moodle.</p>';
$string['chooselanguage'] = 'Seleccionar idioma';
$string['configfilenotwritten'] = 'El script instalador no ha podido crear automáticamente un archivo config.php con las especificaciones elegidas. Por favor, copie el siguiente código en un archivo llamado config.php y coloque ese archivo en el directorio raíz de Moodle.';
$string['configfilewritten'] = 'config.php se ha creado con éxito';
$string['configurationcomplete'] = 'Configuración completa';
$string['database'] = 'Base de datos';
$string['databasesettings'] = ' <p>Ahora necesita configurar la base de datos en la que se almacenará la mayor parte de datos de Moodle. Esta base de datos debe haber sido ya creada, y disponer de un nombre de usuario y de una contraseña de acceso.</p>
<p>Tipo: mysql o postgres7<br />
Servidor: e.g., localhost or db.isp.com<br />
Nombre: Nombre de la base de datos, e.g., moodle<br />
Usuario: nombre de usuario de la base de datos<br />
Contraseña: contraseña de la base de datos<br />
Prefijo de tablas: prefijo a utilizar en todos los nombres de tabla</p>';
$string['dataroot'] = 'Datos';
$string['datarooterror'] = 'El ajuste \'Data\' es incorrecto';
$string['dbconnectionerror'] = 'Error de conexión con la base de datos. Por favor, compruebe los ajustes de la base de datos';
$string['dbcreationerror'] = 'Error al crear la base de datos. No se ha podido crear la base de datos con el nombre y ajustes suministrados';
$string['dbhost'] = 'Servidor';
$string['dbpass'] = 'Contraseña';
$string['dbprefix'] = 'Prefijo de tablas';
$string['dbtype'] = 'Tipo';
$string['directorysettings'] = ' <p><b>WWW:</b>
Necesita decir a Moodle dónde está localizado. Especifique la dirección web completa en la que se ha instalado Moodle. Si su sitio web es accesible a través de varias URLs, seleccione la que resulte de acceso más natural a sus estudiantes. No incluya la última barra</p>
<p><b>Directorio:</b>
Especifique la ruta OS completa a esta misma ubicación
Asegúrese de que escribe correctamente mayúsculas y minúsculas</p>
<p><b>Datos:</b>
Usted necesita un lugar en el que Moodle pueda guardar los archivos subidos. Este directorio debe ser legible Y ESCRIBIBLE por el usuario del servidor web (normalmente \'nobody\' o \'apache\'), pero no debería ser directamente accesible desde la web.</p>';
$string['dirroot'] = 'Directorio';
$string['dirrooterror'] = 'El ajuste de \'Directorio\' es incorrecto. Inténtelo con el siguiente';
$string['wwwroot'] = 'WWW';
$string['wwwrooterror'] = 'El ajuste \'WWW\' es incorrecto';

?>
