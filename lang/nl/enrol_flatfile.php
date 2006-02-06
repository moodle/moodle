<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2004093001)


$string['description'] = 'Deze methode controleert regelmatig een speciaal opgemaakt tekstbestand op een lokatie die je zelf bepaalt. Het bestand kan er uitzien als volgt:
<pre>
add, student, 5, CF101
add, teacher, 6, CF101
add, teacheredit, 7, CF101
del, student, 8, CF101
del, student, 17, CF101
add, student, 21, CF101, 1091115000, 1091215000
</pre>';
$string['enrolname'] = 'Tekstbestand';
$string['filelockedmail'] = 'Het tekstbestand dat je gebruikt voor bestandsgebaseerde inschrijvingen ($a) kan niet verwijderd worden door het cron-proces. Dit is gewoonlijk omdat de rechten op het bestand verkeerd ingesteld zijn. Zet aub de rechten zo dat Moodle het bestand kan verwijderen, anders wordt dat herhaaldelijk verwerkt.';
$string['filelockedmailsubject'] = 'Belangrijke fout: aanmeldingsbestand';
$string['location'] = 'Bestandslokatie';
$string['mailadmin'] = 'Verwittig de beheerder per e-mail';
$string['mailusers'] = 'Verwittig de gebruikers per e-mail';

?>
