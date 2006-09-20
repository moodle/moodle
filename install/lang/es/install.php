<?PHP // $Id$ 
      // install.php - created with Moodle 1.5 Beta (2005052300)


$string['admindirerror'] = 'El directorio especificado para admin es incorrecto';
$string['admindirname'] = 'Directorio Admin';
$string['admindirsetting'] = '<p>Muy pocos servidores web usan /admin como URL especial para permitirle acceder a un panel de control o similar. Desgraciadamente, esto entra en conflicto con la ubicaci�n est�ndar de las p�ginas de administraci�n de Moodle Usted puede corregir esto renombrando el directorio admin en su instalaci�n, y poniendo aqu� ese nuevo nombre. Por ejemplo: <blockquote> moodleadmin</blockquote>.
As� se corregir�n los enlaces admin en Moodle.</p>';
$string['caution'] = 'Precauci�n';
$string['chooselanguage'] = 'Seleccionar idioma';
$string['compatibilitysettings'] = 'Comprobando sus ajustes PHP...';
$string['configfilenotwritten'] = 'El script instalador no ha podido crear autom�ticamente un archivo config.php con las especificaciones elegidas. Por favor, copie el siguiente c�digo en un archivo llamado config.php y coloque ese archivo en el directorio ra�z de Moodle.';
$string['configfilewritten'] = 'config.php se ha creado con �xito';
$string['configurationcomplete'] = 'Configuraci�n completa';
$string['database'] = 'Base de datos';
$string['databasecreationsettings'] = '<p>Ahora necesita configurar los ajustes de la base de datos 
    donde se almacenar�n la mayor�a de los datos de Moodle. La base de datos ser� creada autom�ticamente
    por el instalador con los valores por defecto o los que especifique en los campos editables m�s abajo. Si
    la seguridad de su ordenador es importante deber�a definir una contrase�a en el campo \"Contrase�a\".</p>
      <p><b>Tipo:</b> el valor por defecto es \"mysql\"<br />
      <b>Servidor:</b> el valor por defecto es \"localhost\"<br />
      <b>Nombre:</b> nombre de la base de datos, e.g., moodle<br />
      <b>Usuario:</b> el valor por defecto es  \"root\"<br />
      <b>Contrase�a:</b> contrase�a de la base de datos<br />
      <b>Prefijo de tablas:</b> prefijo opcional para todas las tablas</p>';
$string['databasesettings'] = 'Ahora necesita configurar la base de datos en la que se almacenar� la mayor parte de datos de Moodle. Esta base de datos debe haber sido ya creada, y disponer de un nombre de usuario y de una contrase�a de acceso.<br />
<br /> <br />
<b>Tipo:</b> mysql o postgres7<br />
<b>Servidor:</b> e.g., localhost or db.isp.com<br />
<b>Nombre:</b> Nombre de la base de datos, e.g., moodle<br />
<b>Usuario:</b> nombre de usuario de la base de datos<br />
<b>Contrase�a:</b> contrase�a de la base de datos<br />
<b>Prefijo de tablas:</b> prefijo a utilizar en todos los nombres de tabla';
$string['dataroot'] = 'Datos';
$string['datarooterror'] = 'El ajuste \'Data\' es incorrecto';
$string['dbconnectionerror'] = 'Error de conexi�n con la base de datos. Por favor, compruebe los ajustes de la base de datos';
$string['dbcreationerror'] = 'Error al crear la base de datos. No se ha podido crear la base de datos con el nombre y ajustes suministrados';
$string['dbhost'] = 'Servidor';
$string['dbpass'] = 'Contrase�a';
$string['dbprefix'] = 'Prefijo de tablas';
$string['dbtype'] = 'Tipo';
$string['directorysettings'] = ' <p><b>WWW:</b>
Necesita decir a Moodle d�nde est� localizado. Especifique la direcci�n web completa en la que se ha instalado Moodle. Si su sitio web es accesible a trav�s de varias URLs, seleccione la que resulte de acceso m�s natural a sus estudiantes. No incluya la �ltima barra</p>
<p><b>Directorio:</b>
Especifique la ruta OS completa a esta misma ubicaci�n
Aseg�rese de que escribe correctamente may�sculas y min�sculas</p>
<p><b>Datos:</b>
Usted necesita un lugar en el que Moodle pueda guardar los archivos subidos. Este directorio debe ser legible Y ESCRIBIBLE por el usuario del servidor web (normalmente \'nobody\' o \'apache\'), pero no deber�a ser directamente accesible desde la web.</p>';
$string['dirroot'] = 'Directorio';
$string['dirrooterror'] = 'El ajuste de \'Directorio\' es incorrecto. Int�ntelo con el siguiente';
$string['download'] = 'Descargar';
$string['fail'] = 'Fallo';
$string['fileuploads'] = 'Subidas de archivos';
$string['fileuploadserror'] = 'Debe estar activado';
$string['fileuploadshelp'] = '<p>La subida de archivos parece estar desactivada en su servidor.</p>

<p>Moodle a�n puede ser instalado, pero usted no podr� subir archivos a un curso ni im�genes de los usuarios.</p>

<p>Para posibilitar la subida de archivos, usted (o el administrador del sistema) necesita editar el archivo php.ini principal y cambiar el ajuste de <b>file_uploads</b> a \'1\'.</p>';
$string['gdversion'] = 'Versi�n GD';
$string['gdversionerror'] = 'La librer�a GD deber�a estar presente para procesar y crear im�genes';
$string['gdversionhelp'] = '<p>Su servidor parece no tener el GD instalado.</p>

<p>GD es una librer�a que PHP necesita para que Moodle procese im�genes (tales como los iconos de los usuarios) y para crear im�genes nuevas (e.g., logos). Moodle puede trabajar sin GD, pero usted no dispondr� de las caracter�sticas mencionadas.</p>

<p>Para agregar GD a PHP en entorno Unix, compile PHP usando el par�metro --with-gd.</p>

<p>En un entorno Windows, puede editar php.ini y quitar los comentarios de la l�nea referida a libgd.dll.</p>';
$string['installation'] = 'Instalaci�n';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Debe estar desactivado';
$string['magicquotesruntimehelp'] = '<p>Magic quotes runtime debe estar desactivado para que Moodle funcione adecuadamente.</p>

<p>Normalmente est� desactivado por defecto... Vea el ajuste <b>magic_quotes_runtime</b> en su archivo php.ini.</p>

<p>Si usted no tiene acceso a php.ini, deber�a poder escribir la siguiente l�nea en un archivo denominado .htaccess dentro del directorio Moodle:
<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>';
$string['memorylimit'] = 'L�mite de memoria';
$string['memorylimiterror'] = 'El l�mite de memoria PHP es demasiado bajo... Puede tener problemas m�s tarde.';
$string['memorylimithelp'] = '<p>El l�mite de memoria PHP en su servidor es actualmente $a.</p>

<p>Esto puede ocasionar que Moodle tenga problemas de memoria m�s adelante, especialmente si usted tiene activados muchos m�dulos y/o muchos usuarios.</p>

<p>Recomendamos que configure PHP con el l�mite m�s alto posible, e.g. 16M.
Hay varias formas de hacer esto:</p>
<ol>
<li>Si puede hacerlo, recompile PHP con <i>--enable-memory-limit</i>.
Esto hace que Moodle fije por s� mismo el l�mite de memoria.</li>
<li>Si usted tiene acceso al archivo php.ini, puede cambiar el ajuste <b>memory_limit</b>
a, digamos, 16M. Si no lo tiene, pida a su administrador que lo haga por usted.</li>
<li>En algunos servidores PHP usted puede crear en el directorio Moodle un archivo .htaccess que contenga esta l�nea:
<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Sin embargo, en algunos servidores esto hace que <b>todas</b> las p�ginas PHP dejen de funcionar
(podr� ver los errores cuando mire las p�ginas) de modo que tendr� que eliminar el archivo .htaccess.</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP no ha sido adecuadamente configurado con la extensi�n MySQL de modo que pueda comunicarse con MySQL. Por favor, compruebe el archivo php.ini o recompile PHP.';
$string['pass'] = 'Pass';
$string['phpversion'] = 'Versi�n PHP';
$string['phpversionerror'] = 'La versi�n PHP debe ser 4.1.0 o superior';
$string['phpversionhelp'] = '<p>Moodle requiere una versi�n de PHP 4.1.0 o superior.</p>
<p>Su versi�n es $a</p>
<p>Debe actualizar PHP o acudir a otro servidor con una versi�n m�s reciente de PHP</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle puede tener problemas con \'safe mode\' activado';
$string['safemodehelp'] = '<p>Moodle puede tener varios problemas con \'safe mode\' activado, y probablemente no pueda crear nuevos archivos.</p>

<p>Normalmente el \'safe mode\' s�lo es activado por servidores web p�blicos paranoides, as� que lo que usted debe hacer es encontrar otra compa��a para su sitio Moodle.</p>

<p>Si lo desea, puede seguir con la instalaci�n, pero experimentar� problemas m�s adelante.</p>';
$string['sessionautostart'] = 'Autocomienzo de sesi�n';
$string['sessionautostarterror'] = 'Deb e estar desactivado';
$string['sessionautostarthelp'] = '<p>Moodle requiere apoyo de sesi�n y no funcionar� sin �l.</p>

<p>Las sesiones deben estar activadas en el archhivo php.ini para el par�metro session.auto_start.</p>';
$string['wwwroot'] = 'WWW';
$string['wwwrooterror'] = 'El ajuste \'WWW\' es incorrecto';
$string['welcomep10'] = '$a->installername ($a->installerversion)';
$string['welcomep20'] = 'Si est� viendo esta p�gina es porque ha podido ejecutar el paquete 
    <strong>$a->packname $a->packversion</strong> en su ordenador. !Enhorabuena!';
$string['welcomep30'] = 'Esta versi�n de <strong>$a->installername</strong> incluye las 
    aplicaciones necesarias para que <strong>Moodle</strong> funcione en su ordenador,
    principalmente:';
$string['welcomep40'] = 'El paquete tambi�n incluye <strong>Moodle $a->moodlerelease ($a->moodleversion)</strong>.';
$string['welcomep50'] = 'El uso de todas las aplicaciones del paquete est� gobernado por sus respectivas 
    licencias. El programa <strong>$a->installername</strong> es 
    <a href=\"http://www.opensource.org/docs/definition_plain.html\">c�digo abierto</a> y se distribuye 
    bajo licencia <a href=\"http://www.gnu.org/copyleft/gpl.html\">GPL</a>.';
$string['welcomep60'] = 'Las siguientes p�ginas le guiar�n a traves de algunos sencillos pasos para configurar
    y ajustar <strong>Moodle</strong> en su ordenador. Puede utilizar los valores por defecto sugeridos o,
    de forma opcional, modificarlos para que se ajusten a sus necesidades.';
$string['welcomep70'] = 'Pulse en el bot�n \"Siguiente\" para continuar con la configuraci�n de <strong>Moodle</strong>.';

?>
