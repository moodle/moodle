<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.5.3+ (2005060230)


$string['description'] = 'Deze methode controleert regelmatig een speciaal opgemaakt tekstbestand op een lokatie die je zelf bepaalt. Het bestand is een door komma\'s gescheiden lijst met vier of zes velden per lijn:
Actie, rol, gebruikers IDnummer, cursus IDnummer, [,starttijd, eindtijd]

* actie = add|del
* rol = student|teacher|teacheredit
* gebruikers IDnummer = IDnummer in de user tabel NB, niet id
* cursus IDnummer = IDnummer in de course tabel NB, niet id
* starttime = starttijd (in seconden sinds epoch) - optioneel
* endtime = eindtijd (in seconden sinds epoch) - optioneel
Het bestand kan er uitzien zoals dit:
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
