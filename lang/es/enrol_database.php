<?PHP // $Id$ 
      // enrol_database.php - created with Moodle 1.5 unstable development (2004083000)


$string['dbhost'] = 'Nombre del servidor de la base de datos';
$string['dbname'] = 'Base de datos a utilizar';
$string['dbpass'] = 'Contraseña de acceso al servidor';
$string['dbtable'] = 'Tabla en la base de datos';
$string['dbtype'] = 'Tipo de servidor de base de datos';
$string['dbuser'] = 'Nombre de usuario para acceder al servidor';
$string['description'] = 'Puede usar una base de datos externa (prácticamente de cualquier tipo) para controlar sus matriculaciones. Se asume que la base de datos externa dispone de un campo que contiene un identificador del curso, y otro que contiene un identificador de usuario. Estos valores son contrastados con los campos que usted elige en el curso local y en las tablas de usuario.';
$string['enrolname'] = 'Base de datos externa';
$string['localcoursefield'] = 'Nombre del campo en la tabla de cursos que estamos usando para comparar las entradas en la base de datos remota (e.g., número de identificación)';
$string['localuserfield'] = 'Nombre del campo en la tabla del usuario local que usamos para comparar al usuario con un registro remoto (e.g., número de identificación)';
$string['remotecoursefield'] = 'Campo de la base de datos remota en el que esperamos encontrar el ID del curso';
$string['remoteuserfield'] = 'Campo de la base de datos remota en el que esperamos encontrar el ID del usuario';

?>
