<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2004101900)


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
$string['parentlanguage'] = 'KÄÄNTÄJÄT: Jos kielelläsi on kantakieli jota Moodlen pitäisi käyttää merkkijonon ollessa kateissa, täsmennä sitä varten koodi tähän. Jos jätät tämän alueen tyhjäksi, käytetään englantia. Esimerkki: nl';
$string['thischarset'] = 'KÄÄNTÄJÄT: Täsmennä kielen merkistö tähän. Huomaa, että kaikki teksti joka luodaan tämän kielen ollessa aktiivinen taltioidaan tätä merkistöä käyttäen, joten älä muuta sitä, kun olet tehnyt asetukset. Esimerkki: iso-8859-1';
$string['thisdirection'] = 'KÄÄNTÄJÄT: Tämä merkkijono täsmentää tekstisi suunnan, joko vasemmalta oikealle tai oikealta vasemmalle. Syötä joko ”ltr” tai ”rtl”.';
$string['thislanguage'] = 'KÄÄNTÄJÄT: Määrittele kielesi nimi tähän. Jos mahdollista, käytä yksikoodista numeerista viittausta.';

?>
