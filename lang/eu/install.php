<?PHP // $Id$ 
      // install.php - created with Moodle 1.4.1 (2004083101)


$string['admindirerror'] = 'Admin-erako zehaztutako direktorioa ez da zuzena';
$string['admindirname'] = 'Admin direktorioa';
$string['admindirsetting'] = '<p>Muy pocos servidores web usan /admin como URL especial para permitirle acceder a un panel de control o similar. Desgraciadamente, esto entra en conflicto con la ubicación estándar de las páginas de administración de Moodle Usted puede corregir esto renombrando el directorio admin en su instalación, y poniendo aquí ese nuevo nombre. Por ejemplo: <blockquote> moodleadmin</blockquote>.
Así se corregirán los enlaces admin en Moodle.</p>';
$string['caution'] = 'Kontuz';
$string['chooselanguage'] = 'Hizkuntza aukeratu';
$string['compatibilitysettings'] = 'Zure PHP ezarpenak probatzen...';
$string['configfilenotwritten'] = 'El script instalador no ha podido crear automáticamente un archivo config.php con las especificaciones elegidas. Por favor, copie el siguiente código en un archivo llamado config.php y coloque ese archivo en el directorio raíz de Moodle.';
$string['configfilewritten'] = 'config.php arrakastaz sortu da';
$string['configurationcomplete'] = 'Konfigurazioa osatuta';
$string['database'] = 'Datu basea';
$string['databasesettings'] = ' <p>Ahora necesita configurar la base de datos en la que se almacenará la mayor parte de datos de Moodle. Esta base de datos debe haber sido ya creada, y disponer de un nombre de usuario y de una contraseña de acceso.</p>
<p>Tipo: mysql o postgres7<br />
Servidor: e.g., localhost or db.isp.com<br />
Nombre: Nombre de la base de datos, e.g., moodle<br />
Usuario: nombre de usuario de la base de datos<br />
Contraseña: contraseña de la base de datos<br />
Prefijo de tablas: prefijo a utilizar en todos los nombres de tabla</p>';
$string['dataroot'] = 'Datuak';
$string['datarooterror'] = '\'Data\' ezarpena ez da zuzena';
$string['dbconnectionerror'] = 'Errorea Datu basearekiko konexioan. Mesedez datu basearen ezarpenak egiaztatu.';
$string['dbcreationerror'] = 'Error al crear la base de datos. No se ha podido crear la base de datos con el nombre y ajustes suministrados';
$string['dbhost'] = 'Zerbitzaria';
$string['dbpass'] = 'Pasahitza';
$string['dbprefix'] = 'Taulen aurrizkia';
$string['dbtype'] = 'Mota';
$string['directorysettings'] = ' <p><b>WWW:</b>
Necesita decir a Moodle dónde está localizado. Especifique la dirección web completa en la que se ha instalado Moodle. Si su sitio web es accesible a través de varias URLs, seleccione la que resulte de acceso más natural a sus estudiantes. No incluya la última barra</p>
<p><b>Directorio:</b>
Especifique la ruta OS completa a esta misma ubicación
Asegúrese de que escribe correctamente mayúsculas y minúsculas</p>
<p><b>Datos:</b>
Usted necesita un lugar en el que Moodle pueda guardar los archivos subidos. Este directorio debe ser legible Y ESCRIBIBLE por el usuario del servidor web (normalmente \'nobody\' o \'apache\'), pero no debería ser directamente accesible desde la web.</p>';
$string['dirroot'] = 'Direktorioa';
$string['dirrooterror'] = 'El ajuste de \'Directorio\' es incorrecto. Inténtelo con el siguiente';
$string['download'] = 'Behera kargatu';
$string['fail'] = 'Huts egin du';
$string['fileuploads'] = 'Subidas de archivos';
$string['fileuploadserror'] = 'Debe estar activado';
$string['fileuploadshelp'] = '<p>La subida de archivos parece estar desactivada en su servidor.</p>

<p>Moodle aún puede ser instalado, pero usted no podrá subir archivos a un curso ni imágenes de los usuarios.

<p>Para posibilitar la subida de archivos, usted (o el administrador del sistema) necesita editar el archivo php.ini principal y cambiar el ajuste de <b>file_uploads</b> a \'1\'.</p>';
$string['gdversion'] = 'GD bertsioa';
$string['gdversionerror'] = 'La librería GD debería estar presente para procesar y crear imágenes';
$string['gdversionhelp'] = '<p>Su servidor parece no tener el GD instalado.</p>

<p>GD es una librería que PHP necesita para que Moodle procese imágenes (tales como los iconos de los usuarios) y para crear imágenes nuevas (e.g., logos). Moodle puede trabajar sin GD, pero usted no dispondrá de las características mencionadas.</p>

<p>Para agregar GD a PHP en entorno Unix, compile PHP usando el parámetro --with-gd.</p>

<p>En un entorno Windows, puede editar php.ini y quitar los comentarios de la línea referida a libgd.dll.</p>';
$string['installation'] = 'Instalazioa';
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

<p>Esto puede ocasionar que Moodle tenga problemas de memoria más adelante, especialmente si usted tiene activados muchos módulos y/o muchos usuarios.

<p>Recomendamos que configure PHP con el límite más alto posible, e.g. 16M.
Hay varias formas de hacer esto:
<ol>
<li>Si puede hacerlo, recompile PHP con <i>--enable-memory-limit</i>.
Esto hace que Moodle fije por sí mismo el límite de memoria.
<li>Si usted tiene acceso al archivo php.ini, puede cambiar el ajuste <b>memory_limit</b>
a, digamos, 16M. Si no lo tiene, pida a su administrador que lo haga por usted.
<li>En algunos servidores PHP usted puede crear en el directorio Moodle un archivo .htaccess que contenga esta línea:
<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Sin embargo, en algunos servidores esto hace que <b>todas</b> las páginas PHP dejen de funcionar
(podrá ver los errores cuando mire las páginas) de modo que tendrá que eliminar el archivo .htaccess.
</ol>';
$string['parentlanguage'] = 'es';
$string['pass'] = 'Pass';
$string['phpversion'] = 'PHP bertsioa';
$string['phpversionerror'] = 'PHP bertsioa 4.1.0 edo goragokoa izan behar';
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
$string['thischarset'] = 'iso-8859-1';
$string['thisdirection'] = 'ltr';
$string['thislanguage'] = 'Euskara';
$string['wwwroot'] = 'WWW';
$string['wwwrooterror'] = '\'WWW\' ezarpena ez da zuzena';

?>
