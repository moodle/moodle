<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.3.2 (2004052502)


$string['description'] = 'Den här metoden kommer att återkommande leta efter och bearbeta en specialformatterad textfil som Du anger. Filen kan se ut ungefär så här:
<pre>
add, student, 5, CF101
add, teacher, 6, CF101
add, teacheredit, 7, CF101
del, student, 8, CF101
del, student, 17, CF101
add, student, 21, CF101, 1091115000, 1091215000
</pre>
';
$string['enrolname'] = 'platt fil';
$string['filelockedmail'] = 'Den textfil som Du använder för filbaserade registreringar ($a) kan inte tas bort av cronprocessen. Detta innebär vanligtvis att det är något fel med rättigheterna på den. Var snäll och modifiera rättigheterna så att Moodle kan ta bort filen annars kan den komma att bli återkommande bearbetad.';
$string['filelockedmailsubject'] = 'Viktigt fel: registreringsfilen';
$string['location'] = 'Placering av fil';
$string['mailadmin'] = 'Meddela admin via e-post';
$string['mailusers'] = 'Meddela användare via e-post';

?>
