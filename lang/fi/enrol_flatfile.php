<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2004093001)


$string['description'] = 'Tämä moduli tarkistaa ajoittain määrittelemäsi tekstiedoston. Tiedoston tulisi näyttää seuraavalta <pre>
add, student, 5, CF101
add, teacher, 6, CF101
add, teacheredit, 7, CF101
del, student, 8, CF101
del, student, 17, CF101
add, student, 21, CF101, 1091115000, 1091215000
</pre>';
$string['enrolname'] = 'Tekstitiedosto';
$string['filelockedmail'] = 'Tiedostoa jota käytetään kurssikirjaantumisiin ei voida poistaa. Tämä saattaa tarkoitaa sitä että tiedoston komennot suoritetaan useamman kerran. Ongelmien välttämiseksi korjaa tiedosto-oikeudet niin että cron-toimito voi poistaa tiedoston.';
$string['filelockedmailsubject'] = 'Virhe rekiteröitymis tiedostossa';
$string['location'] = 'Tiedoston sijainti';
$string['mailadmin'] = 'Ilmoita ylläpitäjälle';
$string['mailusers'] = 'Ilmoita käyttäjälle';

?>
