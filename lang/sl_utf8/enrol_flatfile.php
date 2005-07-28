<?PHP // $Id:enrol_flatfile.php from enrol_flatfile.xml
      // Comments: tomaz at zid dot si

$string['enrolname'] = 'Navadna datoteka';
$string['description'] = 'Ta način bo redno preverjal in obdeloval posebno oblikovano besedilno datoteko na lokaciji, ki jo navedete.  Datotek lahko izgleda nekako tako: 
<pre>
   add, student, 5, CF101
   add, teacher, 6, CF101
   add, teacheredit, 7, CF101
   del, student, 8, CF101
   del, student, 17, CF101
   add, student, 21, CF101, 1091115000, 1091215000
</pre>
';
$string['filelockedmailsubject'] = 'Pomembna napaka: datoteka vpisa';
$string['filelockedmail'] = 'Besedilne datoteke, ki jo uporabljate za vpis na osnovi datoteke ($a) cron proces ne more izbrisati.  To običajno pomeni, da so njena dovoljenja napačna.  Prosimo, popravite dovoljenja tako, da Moodle lahko izbriše datoteko, sicer bo lahko obdelovana vedno znova.';
$string['location'] = 'Mesto datoteke';
$string['mailusers'] = 'Obvesti uporabnike z e-pošto';
$string['mailadmin'] = 'Obvesti skrbnika z e-pošto';


?>