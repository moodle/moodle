<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.4 (2004083100)


$string['description'] = 'Denne metode vil kontinuerligt kontrollere og behandle en specielt formateret tekstfil som du specificere hvor ligger. Denne fil kan ligne noget i stil med dette:<pre>
add, student, 5, CF101
add, teacher, 6, CF101
add, teacheredit, 7, CF101
del, student, 8, CF101
del, student, 17, CF101
add, student, 21, CF101, 1091115000, 1091215000
</pre>';
$string['enrolname'] = 'Flad fil';
$string['filelockedmail'] = 'Tekstfilen der bruges til filbaseret tilmelding ($a) kan ikke slettes af cron processen. Dette betyder som oftest at rettighederne på den er forkert. Ret venlist rettighederne så moodle kan slette den, i modsat fald kan den tilmeldningerne blive behandlet flere gange. ';
$string['filelockedmailsubject'] = 'Alvorlig fejl: Tilmeldingsfil.';
$string['location'] = 'Fil lokation';
$string['mailadmin'] = 'Orienter admin via email';
$string['mailusers'] = 'Orienter brugere via email';

?>
