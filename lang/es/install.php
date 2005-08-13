<?PHP // $Id$ 
      // install.php - created with Moodle 1.5.1 (2005060210)


$string['admindirerror'] = 'El directorio especificado para admin es incorrecto';
$string['admindirname'] = 'Directorio Admin';
$string['admindirsetting'] = '<p>Muy pocos servidores web usan /admin como URL especial para permitirle acceder a un panel de control o similar. Desgraciadamente, esto entra en conflicto con la ubicación estándar de las páginas de administración de Moodle Usted puede corregir esto renombrando el directorio admin en su instalación, y poniendo aquí ese nuevo nombre. Por ejemplo: <blockquote> moodleadmin</blockquote>.
Así se corregirán los enlaces admin en Moodle.</p>';
$string['caution'] = 'Precaución';
$string['chooselanguage'] = 'Seleccione un idioma';
$string['compatibilitysettings'] = 'Comprobando sus ajustes PHP...';
$string['configfilenotwritten'] = 'El script instalador no ha podido crear automáticamente un archivo config.php con las especificaciones elegidas. Por favor, copie el siguiente código en un archivo llamado config.php y coloque ese archivo en el directorio raíz de Moodle.';
$string['configfilewritten'] = 'config.php se ha creado con éxito';
$string['configurationcomplete'] = 'Configuración completa';
$string['database'] = 'Base de datos';
$string['databasecreationsettings'] = 'Ahora necesita configurar los ajustes de la base de datos donde se almacenarán la mayoría de los datos de Moodle. El instalador creará la base de datos con los ajustes especificados más abajo.<br />
<br /> <br />
<b>Tipo:</b> el valor por defecto es \"mysql\"<br />
<b>Servidor:</b> el valor por defecto es \"localhost\"<br />
<b>Nombre:</b> nombre de la base de datos, e.g., moodle<br />
<b>Usuario:</b> el valor por defecto es  \"root\"<br />
<b>Contraseña:</b> contraseña de la base de datos<br />
<b>Prefijo de tablas:</b> prefijo opcional para todas las tablas';
$string['databasesettings'] = 'Ahora necesita configurar la base de datos en la que se almacenará la mayor parte de datos de Moodle. Esta base de datos debe haber sido ya creada, y disponer de un nombre de usuario y de una contraseña de acceso.<br />
<br /> <br />
<b>Tipo:</b> mysql o postgres7<br />
<b>Servidor:</b> e.g., localhost or db.isp.com<br />
<b>Nombre:</b> Nombre de la base de datos, e.g., moodle<br />
<b>Usuario:</b> nombre de usuario de la base de datos<br />
<b>Contraseña:</b> contraseña de la base de datos<br />
<b>Prefijo de tablas:</b> prefijo a utilizar en todos los nombres de tabla';
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
$string['download'] = 'Descargar';
$string['fail'] = 'Fallo';
$string['fileuploads'] = 'Subidas de archivos';
$string['fileuploadserror'] = 'Debe estar activado';
$string['fileuploadshelp'] = '<p>La subida de archivos parece estar desactivada en su servidor.</p>

<p>Moodle aún puede ser instalado, pero usted no podrá subir archivos a un curso ni imágenes de los usuarios.</p>

<p>Para posibilitar la subida de archivos, usted (o el administrador del sistema) necesita editar el archivo php.ini principal y cambiar el ajuste de <b>file_uploads</b> a \'1\'.</p>';
$string['gdversion'] = 'Versión GD';
$string['gdversionerror'] = 'La librería GD debería estar presente para procesar y crear imágenes';
$string['gdversionhelp'] = '<p>Su servidor parece no tener el GD instalado.</p>

<p>GD es una librería que PHP necesita para que Moodle procese imágenes (tales como los iconos de los usuarios) y para crear imágenes nuevas (e.g., logos). Moodle puede trabajar sin GD, pero usted no dispondrá de las características mencionadas.</p>

<p>Para agregar GD a PHP en entorno Unix, compile PHP usando el parámetro --with-gd.</p>

<p>En un entorno Windows, puede editar php.ini y quitar los comentarios de la línea referida a libgd.dll.</p>';
$string['installation'] = 'Instalación';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Debe estar desactivado';
$string['magicquotesruntimehelp'] = '<p>Magic quotes runtime debe estar desactivado para que Moodle funcione adecuadamente.</p>

<p>Normalmente está desactivado por defecto... Vea el ajuste <b>magic_quotes_runtime</b> en su archivo php.ini.</p>

<p>Si usted no tiene acceso a php.ini, debería poder escribir la siguiente línea en un archivo denominado .htaccess dentro del directorio Moodle:
<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>';
$string['memorylimit'] = 'Límite de memoria';
$string['memorylimiterror'] = 'El límite de memoria PHP es demasiado bajo... Puede tener problemas más tarde.';
$string['memorylimithelp'] = '<p>El límite de memoria PHP en su servidor es actualmente $a.</p>

<p>Esto puede ocasionar que Moodle tenga problemas de memoria más adelante, especialmente si usted tiene activados muchos módulos y/o muchos usuarios.</p>

<p>Recomendamos que configure PHP con el límite más alto posible, e.g. 16M.
Hay varias formas de hacer esto:</p>
<ol>
<li>Si puede hacerlo, recompile PHP con <i>--enable-memory-limit</i>.
Esto hace que Moodle fije por sí mismo el límite de memoria.</li>
<li>Si usted tiene acceso al archivo php.ini, puede cambiar el ajuste <b>memory_limit</b>
a, digamos, 16M. Si no lo tiene, pida a su administrador que lo haga por usted.</li>
<li>En algunos servidores PHP usted puede crear en el directorio Moodle un archivo .htaccess que contenga esta línea:
<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Sin embargo, en algunos servidores esto hace que <b>todas</b> las páginas PHP dejen de funcionar
(podrá ver los errores cuando mire las páginas) de modo que tendrá que eliminar el archivo .htaccess.</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP no ha sido adecuadamente configurado con la extensión MySQL de modo que pueda comunicarse con MySQL. Por favor, compruebe el archivo php.ini o recompile PHP.';
$string['pass'] = 'Pass';
$string['phpversion'] = 'Versión PHP';
$string['phpversionerror'] = 'La versión PHP debe ser 4.1.0 o superior';
$string['phpversionhelp'] = '<p>Moodle requiere una versión de PHP 4.1.0 o superior.</p>
<p>Su versión es $a</p>
<p>Debe actualizar PHP o acudir a otro servidor con una versión más reciente de PHP</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle puede tener problemas con \'safe mode\' activado';
$string['safemodehelp'] = '<p>Moodle puede tener varios problemas con \'safe mode\' activado, y probablemente no pueda crear nuevos archivos.</p>

<p>Normalmente el \'safe mode\' sólo es activado por servidores web públicos paranoides, así que lo que usted debe hacer es encontrar otra compañía para su sitio Moodle.</p>

<p>Si lo desea, puede seguir con la instalación, pero experimentará problemas más adelante.</p>';
$string['sessionautostart'] = 'Autocomienzo de sesión';
$string['sessionautostarterror'] = 'Deb e estar desactivado';
$string['sessionautostarthelp'] = '<p>Moodle requiere apoyo de sesión y no funcionará sin él.</p>

<p>Las sesiones deben estar activadas en el archhivo php.ini para el parámetro session.auto_start.</p>';
$string['wwwroot'] = 'WWW';
$string['wwwrooterror'] = 'El ajuste \'WWW\' es incorrecto';

?>
