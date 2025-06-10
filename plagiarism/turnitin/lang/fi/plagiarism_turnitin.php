<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC
 */

/*
 * To change this template, choose Tools | Templates.
 * and open the template in the editor.
 */

// General.
$string['pluginname'] = 'Turnitin plagiointi plugin-laajennus';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Turnitin plagiointi plugin-tehtävä';
$string['connecttesterror'] = 'Turnitiniin liittymisessä ilmeni virhe. Palautettu virheilmoitus on alla:<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'Aktivoi Turnitin';
$string['excludebiblio'] = 'Jätä kirjallisuusluettelo pois';
$string['excludequoted'] = 'Sulje Lainaukset Pois';
$string['excludevalue'] = 'Sulje pois pienet vastineet';
$string['excludewords'] = 'Sanaa';
$string['excludepercent'] = 'Prosenttia';
$string['norubric'] = 'Ei arvostelumatriisia';
$string['otherrubric'] = 'Käytä toiselle ohjaajalle kuuluvaa arvostelumatriisia';
$string['attachrubric'] = 'Liitä tähän tehtävään arvostelumatriisi';
$string['launchrubricmanager'] = 'Käynnistä Arvostelumatriisin Hallitsija';
$string['attachrubricnote'] = 'Huomaa: opiskelijat pystyvät näkemään liitetyt arvostelumatriisit ja niiden sisällön ennen palautusta.';
$string['anonblindmarkingnote'] = 'Huomautus: Erillinen Turnitinin nimetön merkintäasetus on poistettu. Turnitin käyttää Moodlen sokkomerkintäasetusta nimettömän merkintäasetuksen määrityksessä.';
$string['transmatch'] = 'Käännetty Vertailu';
$string["reportgen_immediate_add_immediate"] = "Luo raportit välittömästi. Palautukset lisätään arkistoon välittömästi (jos arkisto on määritetty).";
$string["reportgen_immediate_add_duedate"] = "Luo raportit välittömästi. Palautukset lisätään arkistoon palautuspäivänä (jos arkisto on määritetty).";
$string["reportgen_duedate_add_duedate"] = "Luo raportit palautuspäivänä. Palautukset lisätään arkistoon palautuspäivänä (jos arkisto on määritetty).";
$string['launchquickmarkmanager'] = 'Käynnistä Quickmark Hallitsija';
$string['launchpeermarkmanager'] = 'Käynnistä Peermark Hallitsija';
$string['studentreports'] = 'Näytä Alkuperäisyysraportit Opiskelijoille';
$string['studentreports_help'] = 'Sallii sinun näyttää opiskelija-käyttäjille Turnitinin Alkuperäisyysraportteja. Mikäli asetus on kyllä, tulevat
Turnitinin kehittämät alkuperäisyysraportit olemaan opiskelijan nähtävissä.';
$string['submitondraft'] = 'Palauta tiedosto ensimmäisen siirron yhteydessä';
$string['submitonfinal'] = 'Palauta tiedosto, kun opiskelija lähettää sen merkintää varten';
$string['draftsubmit'] = 'Milloin tiedosto pitää palauttaa Turnitiniin?';
$string['allownonor'] = 'Salli palautus missä tahansa tiedostoformaatissa?';
$string['allownonor_help'] = 'Tämä asetus sallii minkä tahansa tiedostosformaatin palautuksen. Kun tämä vaihtoehto on asetettu asentoon &#34;Kyllä&#34;, tullaan palautusten alkuperäisyys tarkistamaan missä se on mahdollista, palautukset voidaan ladata palvelimelta ja GradeMark-palautetyökalut ovat käytettävissä missä se on mahdollista.';
$string['norepository'] = 'Ei Arkistoa';
$string['standardrepository'] = 'Vakioarkisto';
$string['submitpapersto'] = 'Varastoi Opiskelijatyöt';
$string['institutionalrepository'] = 'Laitoskohtainen Arkisto (Mikäli Soveltuu)';
$string['checkagainstnote'] = 'Huomautus: Jos et valitse "Kyllä" vähintään yhdessä ”Tarkista... vastaan" -kohdassa alla, Alkuperäisyysraporttia EI luoda.';
$string['spapercheck'] = 'Tarkista varastoituja opiskelijatöitä vastaan';
$string['internetcheck'] = 'Tarkista Internetiä vastaan';
$string['journalcheck'] = 'Tarkista sanomalehtiä, <br />aikakauslehtiä ja julkaisuja vastaan';
$string['compareinstitution'] = 'Vertaa palautettuja tiedostoja tämän oppilaitoksen sisällä palautettuihin töihin';
$string['reportgenspeed'] = 'Raportin Kehittämisnopeus';
$string['locked_message'] = 'Lukittu viesti';
$string['locked_message_help'] = 'Jos jokin asetus on lukittu, tämä sanoma ilmaisee syyn.';
$string['locked_message_default'] = 'Tämä asetus on lukittu sivustotasolla.';
$string['sharedrubric'] = 'Jaettu Arvostelumatriisi';
$string['turnitinrefreshsubmissions'] = 'Päivitä Palautukset';
$string['turnitinrefreshingsubmissions'] = 'Palautusten Päivitys';
$string['turnitinppulapre'] = 'Jos haluat lähettää tiedoston Turnitiniin, sinun on ensin hyväksyttävä käyttöoikeussopimuksemme EULA. Jos et hyväksy käyttöoikeussopimustamme EULA, tiedostosi lähetetään vain Moodleen. Siirry lukemaan ja hyväksymään sopimus napsauttamalla tästä.';
$string['noscriptula'] = '(Joudut päivittämään tämän sivun manuaalisesti, koska sinulla ei ole JavaScriptiä käytössä, ennenkuin voit, -Turnitinin Käyttäjäsopimuksen hyväksymisen jälkeen, tehdä palautuksen)';
$string['filedoesnotexist'] = 'Tiedosto on hävitetty';
$string['reportgenspeed_resubmission'] = 'Olet jo palauttanut tätä tehtävää koskevan työn, ja palautuksellesi laadittiin alkuperäisyysraportti. Jos päätät palauttaa työsi uudelleen, se korvaa aikaisemman palautuksesi ja luodaan uusi raportti. {$a->num_resubmissions} uuden palautuksen jälkeen sinun on odotettava {$a->num_hours} tuntia uuden palautuksen jälkeen jotta saat nähdä uuden alkuperäisyysraportin.';

// Plugin settings.
$string['config'] = 'Konfiguraatio';
$string['defaults'] = 'Oletusarvoiset asetukset';
$string['showusage'] = 'Näytä Data Dump';
$string['saveusage'] = 'Tallenna Data Dump';
$string['errors'] = 'Virheitä';
$string['turnitinconfig'] = 'Turnitin Plagiointi Plugin-lisäosan konfigurointi';
$string['tiiexplain'] = 'Turnitin on kaupallinen tuote, ja sinulla pitää olla maksettu tilaus voidaksesi käyttää tätä palvelua. Lisätietoja saat täältä: <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>';
$string['useturnitin'] = 'Aktivoi Turnitin';
$string['useturnitin_mod'] = 'Aktivoi Turnitin {$a}';
$string['turnitindefaults'] = 'Turnitin plagiarismi plugin-laajennuksen oletusasetukset';
$string['defaultsdesc'] = 'Seuraavat asetukset ovat oletusarvot silloin kun Turnitin on aktivoitu Toimintamoduulin sisällä';
$string['turnitinpluginsettings'] = 'Turnitin plagiarismi plugin-laajennuksen asetukset';
$string['pperrorsdesc'] = 'Seuraavien tiedostojen siirrossa Turnitiniin ilmeni ongelma. Voit yrittää palautusta uudelleen valitsemalla haluamasi tiedostot ja painamalla Palauta uudelleen -painiketta. Tiedostot käsitellään, kun cron suoritetaan seuraavan kerran.';
$string['pperrorssuccess'] = 'Valitsemasi tiedostot on palautettu uudelleen, ja cron käsittelee ne.';
$string['pperrorsfail'] = 'Joidenkin valitsemiesi tiedostojen kohdalla on ilmennyt ongelma. Niille ei voitu luoda uutta cron-tapahtumaa.';
$string['resubmitselected'] = 'Palauta valitut tiedostot uudelleen';
$string['deleteconfirm'] = 'Oletko varma, että haluat poistaa tämän palautuksen?\n\nTätä toimintoa ei voi peruuttaa.';
$string['deletesubmission'] = 'Poista Palautus';
$string['semptytable'] = 'Tuloksia ei löytynyt.';
$string['configupdated'] = 'Konfigurointi päivitetty';
$string['defaultupdated'] = 'Turnitin oletusarvot päivitetty';
$string['notavailableyet'] = 'Ei saatavilla';
$string['resubmittoturnitin'] = 'Palauta uudelleen Turnitiniin';
$string['resubmitting'] = 'Palautetaan uudelleen';
$string['id'] = 'tunnus';
$string['student'] = 'Opiskelija';
$string['course'] = 'Kurssi';
$string['module'] = 'Moduuli';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = 'Katsele Alkuperäisyysraporttia';
$string['launchrubricview'] = 'Katsele merkintään käytettyä Arvostelumatriisia';
$string['turnitinppulapost'] = 'Tiedostoasi ei ole toimitettu Turnitiniin. Hyväksy käyttöoikeussopimuksemme napsauttamalla tätä.';
$string['ppsubmissionerrorseelogs'] = 'Tätä tiedostoa ei ole toimitettu Turnitiniin. Kysyä neuvoa järjestelmäsi ylläpitäjältä';
$string['ppsubmissionerrorstudent'] = 'Tätä tiedostoa ei ole toimitettu Turnitiniin. Pyydä lisätietoja tuutoriltasi.';

// Receipts.
$string['messageprovider:submission'] = 'Turnitin Plagiointi Plugin-lisäosa Sähköinen Kuitti ilmoitukset';
$string['digitalreceipt'] = 'Sähköinen Kuitti';
$string['digital_receipt_subject'] = 'Tämä on sähköinen Turnitin-kuittisi';
$string['pp_digital_receipt_message'] = 'Hyvä {$a->firstname} {$a->lastname}!<br /><br />Olet palauttanut tiedoston <strong>{$a->submission_title}</strong> tehtävään <strong>{$a->assignment_name}{$a->assignment_part}</strong> luokassa <strong>{$a->course_fullname}</strong> <strong>{$a->submission_date}</strong>. Palautustunnus on <strong>{$a->submission_id}</strong>. Voit tarkastella sähköistä kuittiasi tai tulostaa sen Dokumenttikatselimen tulosta/lataa-painikkeella.<br /><br />Kiitos, että käytät Turnitinia.<br /><br />Turnitin-tiimi';

// Paper statuses.
$string['turnitinid'] = 'Turnitin-tunnusluku';
$string['turnitinstatus'] = 'Turnitin-tila';
$string['pending'] = 'Avoin';
$string['similarity'] = 'Yhtäläisyys';
$string['notorcapable'] = 'Alkuperäisyysraportin kehittäminen tälle tiedostolle on mahdotonta.';
$string['grademark'] = 'GradeMark';
$string['student_read'] = 'Opiskelija katsoi työtä:';
$string['student_notread'] = 'Opiskelija ei ole katsonut tätä työtä.';
$string['launchpeermarkreviews'] = 'Käynnistä Peermark Katselmukset';

// Cron.
$string['ppqueuesize'] = 'Tapahtumien määrä Plagiointi Plugin-lisäosan tapahtumajonossa';
$string['ppcronsubmissionlimitreached'] = 'Tämä cron-suoritus ei lähetä muita palautuksia Turnitiniin, sillä kerralla käsitellään vain {$a}';
$string['cronsubmittedsuccessfully'] = 'Palautus: {$a->title} (TII-tunnus: {$a->submissionid}) tehtävään {$a->assignmentname} kurssilla {$a->coursename} palautettiin Turnitiniin.';
$string['pp_submission_error'] = 'Turnitin ilmoitti virheestä palautuksessa:';
$string['turnitindeletionerror'] = 'Turnitiniin tehdyn palautuksen poisto epäonnistui. Paikallinen Moodle-kopio poistettiin, mutta Turnitiniin tehdyn palautuksen poisto ei onnistunut.';
$string['ppeventsfailedconnection'] = 'Turnitin Plagiointi Plugin-lisäosa ei käsittele tapahtumia tässä cron-suorituksessa, sillä yhteyttä Turnitiniin ei voida muodostaa.';

// Error codes.
$string['tii_submission_failure'] = 'Kysy lisätietoja tutoriltasi tai järjestelmäsi ylläpitäjältä';
$string['faultcode'] = 'Vikakoodi';
$string['line'] = 'Rivi';
$string['message'] = 'Viesti';
$string['code'] = 'Koodi';
$string['tiisubmissionsgeterror'] = 'Kun tähän tehtävään palautettuja töitä yritettiin saadaTurnitinistä, kohdattiin virhe';
$string['errorcode0'] = 'Tätä tiedostoa ei ole toimitettu Turnitiniin. Kysyä neuvoa järjestelmäsi ylläpitäjältä';
$string['errorcode1'] = 'Tätä tiedostoa ei ole lähetetty Turnitiniin, sillä tiedostossa ei ole riittävästi sisältöä, jotta Alkuperäisyysraportti voitaisiin luoda.';
$string['errorcode2'] = 'Tätä tiedostoa ei voi toimittaa Turnitiniin, koska se ylittää sallitun enimmäiskoon {$a->maxfilesize}';
$string['errorcode3'] = 'Tätä tiedostoa ei ole toimitettu Turnitiniin, koska käyttäjä ei ole hyväksynyt Turnitinin loppukäyttäjän lisenssisopimusta.';
$string['errorcode4'] = 'Tätä tehtävää varten on palautettava tuettu tiedostotyyppi. Sallitut tiedostotyypit: .doc, .docx, .ppt, .pptx, .pps, .ppsx, .pdf, .txt, .htm, .html, .hwp, .odt, .wpd, .ps ja .rtf';
$string['errorcode5'] = 'Tätä tiedostoa ei ole toimitettu Turnitiniin, koska Turnitinin moduulin luontiongelma estää palautukset. Lisätietoja on API-lokeissa';
$string['errorcode6'] = 'Tätä tiedostoa ei ole toimitettu Turnitiniin, koska Turnitinin moduuliasetusten muokkausongelma estää palautukset. Lisätietoja on API-lokeissa';
$string['errorcode7'] = 'Tätä tiedostoa ei ole toimitettu Turnitiniin, koska Turnitinin käyttäjän luontiongelma estää palautukset. Lisätietoja on API-lokeissa';
$string['errorcode8'] = 'Tätä tiedostoa ei ole toimitettu Turnitiniin, koska tilapäistiedoston luonnissa on ongelma. Todennäköisin syy on virheellinen tiedostonimi. Vaihda tiedoston nimi ja tee siirto uudelleen Muokkaa Palautusta -toiminnolla.';
$string['errorcode9'] = 'Tiedostoa ei voi palauttaa, koska tiedostovarannossa ei ole palautettavaa sisältöä.';
$string['coursegeterror'] = 'Kurssin tietoja ei voitu saada';
$string['configureerror'] = 'Sinun täytyy konfiguroida tämä moduuli Ylläpitäjänä loppuun asti, ennen kuin käytät sitä kurssin sisällä. Ota yhteys Moodlen ylläpitäjään.';
$string['turnitintoolofflineerror'] = 'Meillä on väliaikainen pulma. Kokeile taas hetken päästä uudestaan.';
$string['defaultinserterror'] = 'Kun yritettiin lisätä oletusarvoinen asetus tietokantaan, tapahtui
virhe';
$string['defaultupdateerror'] = 'Kun yritettiin päivittää oletusarvoinen asetus tietokantaan, tapahtui virhe';
$string['tiiassignmentgeterror'] = 'Kun tehtävää yritettiin saada Turnitinistä, kohdattiin virhe';
$string['assigngeterror'] = 'Turnitintyökalukahden tietoja ei saatu';
$string['classupdateerror'] = 'Turnitin-luokan tietoja ei voitu päivittää';
$string['pp_createsubmissionerror'] = 'Kun työn palautusta yritettiin luoda Turnitinissä, kohdattiin virhe';
$string['pp_updatesubmissionerror'] = 'Kun työtäsi yritettiin palauttaa uudelleen Turnitiniin, kohdattiin virhe';
$string['tiisubmissiongeterror'] = 'Kun palautettua työtä yritettiin saada Turnitinistä, kohdattiin virhe';

// Javascript.
$string['closebutton'] = 'Sulje';
$string['loadingdv'] = 'Ladataan Turnitinin Dokumenttikatselinta...';
$string['changerubricwarning'] = 'Arvostelumatriisin muuttaminen tai irrottaminen poistaa kaiken arvostelumatriisi-arvostelun tämän tehtävän töistä, kuten pistekortit, joihin on aiemmin tehty merkintöjä. Aiemmin arvosteltujen töiden yleisarvosanat jäävät koskematta.';
$string['messageprovider:submission'] = 'Turnitin Plagiointi Plugin-lisäosa Sähköinen Kuitti ilmoitukset';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Turnitin-tila';
$string['deleted'] = 'Poistettu';
$string['pending'] = 'Avoin';
$string['because'] = 'Syynä on se, että ylläpitäjä on poistanut avoimen tehtävän käsittelyjonosta ja keskeyttänyt palautuksen Turnitiniin.<br /><strong>Tiedosto on yhä Moodlessa, joten ota yhteyttä ohjaajaasi.</strong><br />Mahdolliset virhekoodit löytyvät alta:';
$string['submitpapersto_help'] = '<strong>Ei Arkistoa: </strong><br />Turnitin ei tallenna lähetettyjä asiakirjoja mihinkään arkistoon. Järjestelmä käsittelee työn ainoastaan ensimmäistä yhtäläisyystarkistusta varten.<br /><br /><strong>Vakioarkisto: </strong><br />Turnitin tallentaa kopion lähetetystä asiakirjasta vain vakioarkistoon. Valitsemalla tämän vaihtoehdon Turnitin-järjestelmää ohjeistetaan käyttämään ainoastaan tallennettuja asiakirjoja uusien asiakirjojen yhtäläisyystarkistuksissa.<br /><br /><strong>Laitoskohtainen Arkisto (Mikäli Soveltuu): </strong><br />Vaihtoehdon valitseminen ohjeistaa Turnitin-järjestelmää lisäämään lähetetyt asiakirjat ainoastaan oppilaitoksen yksityiseen arkistoon. Vain oppilaitoksen muut ohjaajat voivat suorittaa lähetettyjen asiakirjojen yhtäläisyystarkastuksia.';
$string['errorcode12'] = 'Tätä tiedostoa ei ole palautettu Turnitiniin, koska se kuuluu tehtävään, jossa kurssi on poistettu. Rivin tunnus: ({$a->id}) | Kurssimoduulin tunnus: ({$a->cm}) | Käyttäjätunnus: ({$a->userid})';
$string['errorcode15'] = 'Tätä tiedostoa ei ole lähetetty Turnitinille, koska toimintamoduulia, johon se kuuluu, ei löydy.';
$string['tiiaccountconfig'] = 'Turnitin-tilin konfigurointi';
$string['turnitinaccountid'] = 'Turnitin Tilitunnus';
$string['turnitinsecretkey'] = 'Turnitin Yhteisöavain';
$string['turnitinapiurl'] = 'Turnitin API-URL';
$string['tiidebugginglogs'] = 'Virheiden korjaus ja lokiinkirjaus';
$string['turnitindiagnostic'] = 'Aktivoi Diagnoosi-tila';
$string['turnitindiagnostic_desc'] = '<b>[Varoitus]</b><br />Aktivoi Diagnoosi-tila ainoastaan löytääksesi ongelmia Turnitin API:ssä.';
$string['tiiaccountsettings_desc'] = 'Varmista, että nämä asetukset ovat samat kuin Turnitin-tilissäsi, sillä muuten tehtävien luonnissa ja/tai opiskelijoiden palautuksissa voi ilmetä ongelmia.';
$string['tiiaccountsettings'] = 'Turnitin-tilin asetukset';
$string['turnitinusegrademark'] = 'Käytä GradeMarkia';
$string['turnitinusegrademark_desc'] = 'Valitse, käytätkö GradeMarkia palautusten arvosteluun.<br /><i>(Tämä edellyttää, että tilille on määritetty GradeMark-asetus)</i>';
$string['turnitinenablepeermark'] = 'Aktivoi Peermark Tehtävät';
$string['turnitinenablepeermark_desc'] = 'Valitse haluatko sallia Peermark-tehtävien luomisen.<br/><i>(Tämä edellyttää, että tilille on määritetty Peermark-asetus)</i>';
$string['transmatch_desc'] = 'Määrittelee onko Käännetty Vertailu-vaihtoehto saatavilla tehtävän asetusnäytössä.<br /><i>(Aktivoi tämä vaihtoehto ainoastaan silloin, kun Käännetty Vertailu on aktivoitu Turnitin-tililläsi)</i>';
$string['repositoryoptions_0'] = 'Salli ohjaajan vakioarkistovaihtoehdot';
$string['repositoryoptions_1'] = 'Aktivoi ohjaajan laajennetut arkistovaihtoehdot';
$string['repositoryoptions_2'] = 'Toimita kaikki työt perusarkistoon';
$string['repositoryoptions_3'] = 'Älä toimita töitä mihinkään arkistoon';
$string['turnitinrepositoryoptions'] = 'Arkistoidut palautukset';
$string['turnitinrepositoryoptions_desc'] = 'Valitse arkistovaihtoehdot Turnitin-tehtäville.<br /><i>(Tämä edellyttää, että tilillä on otettu käyttöön Laitoskohtainen Arkisto -asetus)</i>';
$string['tiimiscsettings'] = 'Sekalaiset plugin-laajennusten asetukset';
$string['pp_agreement_default'] = 'Merkitsemällä tämän ruudun vahvistan että palautus on minun oma työni ja otan vastuulleni kaikki, palautukseni tuloksena mahdollisesti ilmenneet tekijänoikeuden rikkomiset.';
$string['pp_agreement_desc'] = '<b>[Valinnainen]</b><br />Syötä palautuksille sopimusvahvistuslausunto.<br />(<b>Huomaa:</b> Jos sopimus on jätetty täysin tyhjäksi, ei opiskelijoilta vaadita sopimusvahvistusta palautuksen yhteydessä)';
$string['pp_agreement'] = 'Vastuuvapauslauseke / Sopimus';
$string['studentdataprivacy'] = 'Opiskelijan Tietoturva-asetukset';
$string['studentdataprivacy_desc'] = 'Seuraavat asetukset voidaan konfiguroida, jotta varmistetaan, että opiskelijan henkilökohtaisia tietoja ei lähetetä Turnitiniin sovellusliittymän kautta.';
$string['enablepseudo'] = 'Aktivoi Opiskelijan Yksityisyys';
$string['enablepseudo_desc'] = 'Mikäli tämä vaihtoehto on valittu, tullaan opiskelijoiden sähköpostiosoitteet muuntamaan valevastaaviksi Turnitinin API-kutsuja varten.<br /><i>(<b>Huomautus:</b> Tätä vaihtoehtoa ei voi muuttaa, jos Moodlen käyttäjädata on jo synkronoitu Turnitinin kanssa)</i>';
$string['pseudofirstname'] = 'Opiskelijan Vale-etunimi';
$string['pseudofirstname_desc'] = '<b>[Valinnainen]</b><br />Turnitinin dokumenttikatselimessa näytettävä opiskelijan etunimi';
$string['pseudolastname'] = 'Opiskelijan Vale-sukunimi';
$string['pseudolastname_desc'] = '<b>[Valinnainen]</b><br />Turnitinin dokumenttikatselimessa näytettävä opiskelijan sukunimi';
$string['pseudolastnamegen'] = 'Kehitä Sukunimi Automaattisesti';
$string['pseudolastnamegen_desc'] = 'Jos asetus on otettu käyttöön ja vale-sukunimeksi on asetettu käyttäjäprofiilikenttä, kenttään tulee automaattisesti yksilöllinen tunnus.';
$string['pseudoemailsalt'] = 'Pseudo Salaus Salt';
$string['pseudoemailsalt_desc'] = '<b>[Valinnainen]</b><br />Valinnainen salt-luku jolla lisätään Opiskelijan kehitetyn Vale-sähköpostiosoitteen monimutkaisuutta.<br />(<b>Huomautus:</b> Salt-luvun tulisi olla koskematon, jotta säilytetään johdonmukaiset vale-sähköpostiosoitteet)';
$string['pseudoemaildomain'] = 'Vale-sähköpostitoimialue';
$string['pseudoemaildomain_desc'] = '<b>[Valinnainen]</b><br />Valinnainen toimialue vale-sähköpostiosoitteille. (Oletusarvoisesti @tiimoodle.com, mikäli jätetty tyhjäksi)';
$string['pseudoemailaddress'] = 'Vale-sähköpostiosoite';
$string['connecttest'] = 'Testaa Turnitin Yhteys';
$string['connecttestsuccess'] = 'Moodle on saanut onnistuneesti yhteyden Turnitiniin.';
$string['diagnosticoptions_0'] = 'Pois päältä';
$string['diagnosticoptions_1'] = 'Vakio';
$string['diagnosticoptions_2'] = 'Virheiden korjaus';
$string['repositoryoptions_4'] = 'Palauta kaikki esseet oppilaitoksen tietokantaan';
$string['turnitinrepositoryoptions_help'] = '<strong>Salli ohjaajan vakioarkistovaihtoehdot: </strong><br />Ohjaajat voivat ohjeistaa Turnitin-järjestelmää joko lisäämään asiakirjoja vakioarkistoon tai oppilaitoksen yksityiseen arkistoon tai jättää asiakirjat lisäämättä mihinkään arkistoon.<br /><br /><strong>Aktivoi ohjaajan laajennetut arkistovaihtoehdot: </strong><br />Tällä vaihtoehdolla ohjaaja voi tarkastella tehtäväasetusta, joka sallii opiskelijoiden muokata asiakirjojen tallennuspaikkaa Turnitinissa. Opiskelijat voivat lisätä asiakirjansa vakioarkistoon tai oppilaitoksen yksityiseen arkistoon.<br /><br /><strong>Toimita kaikki työt perusarkistoon: </strong><br />Kaikki asiakirjat lisätään oletusarvoisesti vakioarkistoon.<br /><br /><strong>Älä toimita töitä mihinkään arkistoon: </strong><br />Asiakirjoja käytetään ainoastaan ensimmäisessä Turnitin-tarkastuksessa ja näytettäessä ohjaajalle arvostelua varten.<br /><br /><strong>Palauta kaikki esseet oppilaitoksen tietokantaan: </strong><br />Turnitin tallentaa kaikki työt oppilaitoksen arkistoon. Vain oppilaitoksen ohjaajat voivat suorittaa lähetettyjen asiakirjojen yhtäläisyystarkastuksia.';
$string['turnitinuseanon'] = 'Käytä Nimetöntä Merkintää';
$string['createassignmenterror'] = 'Kun tehtävää yritettiin luoda Turnitinissä, kohdattiin virhe';
$string['editassignmenterror'] = 'Kun tehtävää yritettiin muokata Turnitinissä, kohdattiin virhe';
$string['ppassignmentediterror'] = 'Moduulia {$a->title} (TII-tunnus: {$a->assignmentid}) ei voitu muokata Turnitinissä; lisätietoja on API-rajapinta-lokeissa';
$string['pp_classcreationerror'] = 'Tätä luokkaa ei voitu luoda Turnitiniin, ole hyvä ja hae lisätietoja API-rajapinta-lokeista';
$string['unlinkusers'] = 'Erota Käyttäjät';
$string['relinkusers'] = 'Linkitä käyttäjät uudelleen';
$string['unlinkrelinkusers'] = 'Erota / Linkitä Uudestaan Turnitin Käyttäjät';
$string['nointegration'] = 'Ei integrointia';
$string['sprevious'] = 'Edellinen';
$string['snext'] = 'Seuraava';
$string['slengthmenu'] = 'Näytä_MENU_Lisäykset';
$string['ssearch'] = 'Hae:';
$string['sprocessing'] = 'Tietoja ladataan Turnitinistä...';
$string['szerorecords'] = 'Ei esitettäviä tiedostoja.';
$string['sinfo'] = 'Näyttää _ALUSTA _LOPPUUN _palautusten _KOKONAISLUKUMÄÄRÄSTÄ.';
$string['userupdateerror'] = 'Käyttäjätietoja ei voitu päivittää';
$string['connecttestcommerror'] = 'Turnitiniin ei saatu yhteyttä. Tarkasta API:n URL-asetus.';
$string['userfinderror'] = 'Kun käyttäjää haettiin Turnitinistä, kohdattiin virhe';
$string['tiiusergeterror'] = 'Kun käyttäjän tietoja yritettiin saada Turnitinistä, kohdattiin virhe';
$string['usercreationerror'] = 'Turnitin käyttäjän luominen epäonnistui';
$string['ppassignmentcreateerror'] = 'Tätä modulia ei voitu luoda Turnitiniin. Ole hyvä ja hae lisätietoja API-rajapinta-lokista';
$string['excludebiblio_help'] = 'Tällä asetuksella ohjaaja voi valita sulkea pois Alkuperäisyysraporttia kehitettäessä kirjallisuusluettelossa esiintyvää tekstiä, siteerattuja töitä, tai opiskelijatöiden viiteosia. Tämä asetus voidaan syrjäyttää yksittäisissä Alkuperäisyysraporteissa.';
$string['excludequoted_help'] = 'Tällä asetuksella ohjaaja voi valita sulkea pois vastinetarkastuksesta lainauksissa esiintyvää tekstiä, silloin kun Alkuperäisyysraportteja luodaan. Tämä asetus voidaan syrjäyttää yksittäisissä Alkuperäisyysraporteissa.';
$string['excludevalue_help'] = 'Tällä asetuksella ohjaaja voi valita sulkea pois vastineet, jotka eivät ole riittävän pitkiä (ohjaajan määrittämä), silloin kun Alkuperäisyysraportteja luodaan. Tämä asetus voidaan ohittaa yksittäisissä Alkuperäisyysraporteissa.';
$string['spapercheck_help'] = 'Tarkista Turnitinin opiskelijatöiden arkistoa vastaan kun töiden Alkuperäisyysraportteja käsitellään. Samankaltaisuusindeksi-prosentti saattaa laskea, jos tämä valinta poistetaan';
$string['internetcheck_help'] = 'Tarkista Turnitinin internetarkistoa vastaan, silloin kun töiden Alkuperäisyysraportteja käsitellään. Samankaltaisuusindeksin prosenttiluku saattaa laskea, jos tämä valinta poistetaan.';
$string['journalcheck_help'] = 'Tarkista Turnitinin akateemisten aikakauslehtien, aikakauslehtien ja julkisujen arkistoa vastaan, silloin kun töiden Alkuperäisyysraportteja käsitellään. Samankaltaisuusindeksin prosenttiluku saattaa laskea, jos tämä valinta poistetaan.';
$string['reportgenspeed_help'] = 'Tähän tehtäväasetukseen on kolme vaihtoehtoa: &#39;Luo raportit välittömästi. Palautukset lisätään arkistoon palautuspäivänä (jos arkisto on määritetty).&#39;, &#39;Luo raportit välittömästi. Palautukset lisätään arkistoon välittömästi (jos arkisto on määritetty).&#39; ja &#39;Luo raportit palautuspäivänä. Palautukset lisätään arkistoon palautuspäivänä (jos arkisto on määritetty).&#39;<br /><br />Vaihtoehto &#39;Luo raportit välittömästi. Palautukset lisätään arkistoon palautuspäivänä (jos arkisto on määritetty).&#39; luo Alkuperäisyysraportin saman tien kun opiskelija on tehnyt palautuksen. Kun tämä vaihtoehto on valittuna, eivät opiskelijasi pysty enää palauttamaan tehtäväänsä uudelleen.<br /><br />Kun haluat sallia uudelleenpalautukset, valitse &#39;Luo raportit välittömästi. Palautukset lisätään arkistoon välittömästi (jos arkisto on määritetty).&#39; -vaihtoehto. Tämä antaa opiskelijoille mahdollisuuden palauttaa töitään jatkuvasti eräpäivään asti. Uudelleenpalautuksia varten kehitettävien Alkuperäisyysraporttien käsittely saattaa kestää jopa 24 tuntia.<br /><br />Vaihtoehto &#39;Luo raportit palautuspäivänä. Palautukset lisätään arkistoon palautuspäivänä (jos arkisto on määritetty).&#39; kehittää Alkuperäisyysraportin ainoastaan tehtävän eräpäivänä. Tätä asetusta käytettäessä tullaan kaikkia tehtävään palautettuja töitä vertailemaan toisiinsa silloin kun Alkuperäisyysraportteja luodaan.';
$string['turnitinuseanon_desc'] = 'Valitse haluatko sallia Nimettömän Merkinnän palautuksien arvostelussa.<br /><i>(Tämä edellyttää, että tilille on määritetty Nimetön Merkintä -asetus)</i>';
