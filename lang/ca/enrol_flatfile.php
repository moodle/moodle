<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.5 unstable development (2004083000)


$string['description'] = 'Aquest mètode comprova repetidament si hi ha un fitxer de text en una ubicació que especifiqueu i quan hi és el processa. Aquest fitxer ha de tenir un format especial. Pot tenir un aspecte semblant a aquest:
<pre>
add, student, 5, CF101
add, teacher, 6, CF101
add, teacheredit, 7, CF101
del, student, 8, CF101
del, student, 17, CF101
add, student, 21, CF101, 1091115000, 1091215000
</pre>';
$string['enrolname'] = 'Fitxer de text';
$string['filelockedmail'] = 'El procés del cron no pot suprimir el fitxer de text que esteu utilitzant per a les inscripcions basades en un fitxer de text ($a). Generalment això és degut a un error de permisos. Corregiu els permisos de manera que Moodle pugui suprimir el fitxer, o si no es podria tornar a processar repetidament.';
$string['filelockedmailsubject'] = 'Error important: fitxer d\'inscripcions';
$string['location'] = 'Ubicació del fitxer';
$string['mailadmin'] = 'Notifica a l\'administrador per correu electrònic';
$string['mailusers'] = 'Notifica als usuaris per correu electrònic';

?>
