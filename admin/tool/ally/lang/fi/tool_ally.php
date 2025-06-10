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
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminurl'] = 'Käynnistysosoite';
$string['adminurldesc'] = 'Tämä on LTI:n käynnistysosoite, jolla esteettömyysraporttia käytetään.';
$string['allyclientconfig'] = 'Ally-määritys';
$string['ally:clientconfig'] = 'Käytä ja päivitä asiakasmääritystä';
$string['ally:viewlogs'] = 'Ally-lokien lukutoiminto';
$string['clientid'] = 'Asiakastunnus';
$string['clientiddesc'] = 'Allyn asiakastunnus';
$string['code'] = 'Koodi';
$string['contentauthors'] = 'Sisällön tekijät';
$string['contentauthorsdesc'] = 'Ylläpitäjien ja näihin valittuihin rooleihin määritettyjen käyttäjien lähettämien kurssitiedostojen esteettömyys arvioidaan. Näille tiedostoille annetaan esteettömyysarvo. Huono arvo tarkoittaa sitä, että tiedostoon täytyy tehdä esteettömyyttä parantavia muutoksia.';
$string['contentupdatestask'] = 'Sisältöpäivitystehtävä';
$string['curlerror'] = 'cURL-virhe: {$a}';
$string['curlinvalidhttpcode'] = 'Virheellinen HTTP-tilakoodi: {$a}';
$string['curlnohttpcode'] = 'HTTP-tilakoodin vahvistaminen ei onnistu';
$string['error:invalidcomponentident'] = 'Virheellinen komponenttitunniste {$a}';
$string['error:pluginfilequestiononly'] = 'Tässä URL-osoitteessa tuetaan vain kysymyskomponentteja';
$string['error:componentcontentnotfound'] = '{$a}-sisältöä ei löydetty';
$string['error:wstokenmissing'] = 'Verkkopalveluavain puuttuu. Ylläpitäjäkäyttäjän täytyy ehkä suorittaa automaattinen määritys.';
$string['excludeunused'] = 'Jätä käyttämättömät tiedostot pois';
$string['excludeunuseddesc'] = 'Jätä pois tiedostot, jotka on liitetty HTML-sisältöön, mutta linkkeinä/viittauksina HTML:ssä.';
$string['filecoursenotfound'] = 'Välitetty tiedosto ei kuulu mihinkään kurssiin';
$string['fileupdatestask'] = 'Lähetä tiedostopäivitykset Allyyn';
$string['id'] = 'Tunnus';
$string['key'] = 'Avain';
$string['keydesc'] = 'Tämä on LTI-kuluttajan avain.';
$string['level'] = 'Taso';
$string['message'] = 'Viesti';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'Tiedostopäivitysten URL-osoite';
$string['pushurldesc'] = 'Lähetä ilmoitukset tiedostopäivityksistä tähän URL-osoitteeseen.';
$string['queuesendmessagesfailure'] = 'Viestien lähettämisessä AWS SQS:ään tapahtui virhe. Virhetiedot: $a';
$string['secret'] = 'Salaisuus';
$string['secretdesc'] = 'Tämä on LTI-salaisuus.';
$string['showdata'] = 'Näytä tiedot';
$string['hidedata'] = 'Piilota tiedot';
$string['showexplanation'] = 'Näytä selitys';
$string['hideexplanation'] = 'Piilota selitys';
$string['showexception'] = 'Näytä poikkeus';
$string['hideexception'] = 'Piilota poikkeus';
$string['usercapabilitymissing'] = 'Annetulla käyttäjällä ei ole oikeutta poistaa tätä tiedostoa.';
$string['autoconfigure'] = 'Määritä Ally-verkkopalvelu automaattisesti';
$string['autoconfiguredesc'] = 'Luo verkkopalvelurooli ja -käyttäjä Allylle automaattisesti.';
$string['autoconfigureconfirmation'] = 'Luo Allyn verkkopalvelurooli ja käyttäjä ja ota verkkopalvelu käyttöön automaattisesti. Seuraavat toimet suoritetaan:<ul><li>ally_webservice-rooli ja käyttäjä käyttäjänimellä ally_webuser luodaan</li><li>ally_webuser-käyttäjä lisätään ally_webservice-rooliin</li><li>verkkopalvelut otetaan käyttöön</li><li>REST-verkkopalveluprotokolla otetaan käyttöön</li><li>Ally-verkkopalvelu otetaan käyttöön</li><li>ally_webuser-tilille luodaan avain</li></ul>';
$string['autoconfigsuccess'] = 'Ally-verkkopalvelu on määritetty automaattisesti.';
$string['autoconfigtoken'] = 'Verkkopalvelun avain on seuraava:';
$string['autoconfigapicall'] = 'Seuraavan URL-osoitteen avulla voit testata, että verkkopalvelu toimii:';
$string['privacy:metadata:files:action'] = 'Tämä on tiedostolle suoritettu toiminto, esimerkiksi luotu, päivitetty tai poistettu.';
$string['privacy:metadata:files:contenthash'] = 'Tämä on tiedoston sisällön hajautusarvo, jolla määritetään yksilöllisyys.';
$string['privacy:metadata:files:courseid'] = 'Tämä on sen kurssin tunnus, johon tiedosto kuuluu.';
$string['privacy:metadata:files:externalpurpose'] = 'Jos haluat integroida Allyn, tiedostot täytyy jakaa Allyn kanssa.';
$string['privacy:metadata:files:filecontents'] = 'Tiedoston todellinen sisältö lähetetään Allylle esteettömyysarvioitavaksi.';
$string['privacy:metadata:files:mimetype'] = 'Tämä on tiedoston MIME-tyyppi, esimerkiksi pelkkä teksti, jpeg-kuva jne.';
$string['privacy:metadata:files:pathnamehash'] = 'Tämä on tiedostopolun nimen hajautusarvo, jolla tiedosto yksilöidään.';
$string['privacy:metadata:files:timemodified'] = 'Tämä on kentän edellinen muokkausaika.';
$string['cachedef_annotationmaps'] = 'Tallenna kurssien huomautustiedot';
$string['cachedef_fileinusecache'] = 'Ally-tiedostot käytön välimuistissa';
$string['cachedef_pluginfilesinhtml'] = 'Ally-tiedostot HTML-välimuistissa';
$string['cachedef_request'] = 'Ally-suodatinpyyntövälimuisti';
$string['pushfilessummary'] = 'Tämä on Ally-tiedostopäivitysten yhteenveto.';
$string['pushfilessummary:explanation'] = 'Tämä on yhteenveto Allyyn lähetetyistä tiedostopäivityksistä.';
$string['section'] = 'Osio {$a}';
$string['lessonanswertitle'] = 'Vastaus oppitunnille {$a}';
$string['lessonresponsetitle'] = 'Vastaus oppitunnille {$a}';
$string['logs'] = 'Ally-lokit';
$string['logrange'] = 'Lokitaso';
$string['loglevel:none'] = 'ei mitään';
$string['loglevel:light'] = 'Kevyt';
$string['loglevel:medium'] = 'Keskisuuri';
$string['loglevel:all'] = 'Kaikkien';
$string['logcleanuptask'] = 'Ally-lokin puhdistustehtävä';
$string['loglifetimedays'] = 'Säilytä lokit näin monen päivän ajan';
$string['loglifetimedaysdesc'] = 'Säilytä Ally-lokit näin monen päivän ajan. Kun arvoksi on määritetty 0, lokeja ei poisteta koskaan. Ajastettu tehtävä on (oletusarvoisesti) määritetty siten, että se suoritetaan päivittäin ja että se poistaa lokitietueet, joita on säilytetty kauemmin kuin näin monen päivän ajan.';
$string['logger:filtersetupdebugger'] = 'Ally-suodatinmäärityksen loki';
$string['logger:pushtoallysuccess'] = 'Lähetettiin Ally-päätepisteeseen';
$string['logger:pushtoallyfail'] = 'Lähetys Ally-päätepisteeseen epäonnistui';
$string['logger:pushfilesuccess'] = 'Tiedostot lähetettiin Ally-päätepisteeseen';
$string['logger:pushfileliveskip'] = 'Tiedostojen live-lähetysvirhe';
$string['logger:pushfileliveskip_exp'] = 'Tiedostojen live-lähetys ohitetaan yhteysongelmien vuoksi. Tiedostojen live-lähetys palautetaan, kun tiedostojen päivitystehtävä onnistuu. Tarkista määritykset.';
$string['logger:pushfileserror'] = 'Lähetys Ally-päätepisteeseen epäonnistui';
$string['logger:pushfileserror_exp'] = 'Nämä ovat Ally-palveluihin lähetettyjen sisältöpäivitysten virheet.';
$string['logger:pushcontentsuccess'] = 'Sisältö lähetettiin Ally-päätepisteeseen';
$string['logger:pushcontentliveskip'] = 'Live-sisältölähetyksen virhe';
$string['logger:pushcontentliveskip_exp'] = 'Sisällön live-lähetys ohitetaan yhteysongelmien vuoksi. Sisällön live-lähetys palautetaan, kun tiedostojen päivitystehtävä onnistuu. Tarkista määritykset.';
$string['logger:pushcontentserror'] = 'Lähetys Ally-päätepisteeseen epäonnistui';
$string['logger:pushcontentserror_exp'] = 'Nämä ovat Ally-palveluihin lähetettyjen sisältöpäivitysten virheet.';
$string['logger:addingconenttoqueue'] = 'Lisätään sisältöä lähetysjonoon';
$string['logger:annotationmoderror'] = 'Ally-moduulin sisällön merkitseminen epäonnistui.';
$string['logger:annotationmoderror_exp'] = 'Moduulia ei tunnistettu oikein';
$string['logger:failedtogetcoursesectionname'] = 'Kurssiosion nimen hakeminen epäonnistui';
$string['logger:moduleidresolutionfailure'] = 'Kurssimoduulitunnuksen tarkistaminen epäonnistui';
$string['logger:cmidresolutionfailure'] = 'Kurssimoduulitunnuksen tarkistaminen epäonnistui';
$string['logger:cmvisibilityresolutionfailure'] = 'Kurssimoduulin näkyvyyden tarkistaminen epäonnistui';
$string['courseupdatestask'] = 'Lähetä kurssitapahtumat Allyyn';
$string['logger:pushcoursesuccess'] = 'Kurssitapahtumat lähetettiin Ally-päätepisteeseen';
$string['logger:pushcourseliveskip'] = 'Kurssitapahtumien live-lähetysvirhe';
$string['logger:pushcourseerror'] = 'Kurssitapahtumien live-lähetysvirhe';
$string['logger:pushcourseliveskip_exp'] = 'Kurssitapahtumien live-lähetys ohitetaan yhteysongelmien vuoksi. Kurssitapahtumien live-lähetys palautetaan, kun tiedostojen päivitystehtävä onnistuu. Tarkista määritykset.';
$string['logger:pushcourseserror'] = 'Lähetys Ally-päätepisteeseen epäonnistui';
$string['logger:pushcourseserror_exp'] = 'Nämä ovat Ally-palveluihin lähetettyjen kurssipäivitysten virheet.';
$string['logger:addingcourseevttoqueue'] = 'Lisätään kurssitapahtuma lähetysjonoon';
$string['logger:cmiderraticpremoddelete'] = 'Kurssimoduulin tunnuksella on ongelmia esipoistamisen kanssa.';
$string['logger:cmiderraticpremoddelete_exp'] = 'Moduulia ei ole tunnistettu oikein. Joko se ei ole olemassa osion poistamisen takia tai on toinen tekijä, mikä laukaisi poistokoukun ja sitä ei löydetä.';
$string['logger:servicefailure'] = 'Palvelun käyttäminen epäonnistui.';
$string['logger:servicefailure_exp'] = '<br>Luokka: {$a->class}<br>Parametrit: {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'Roolin ally_webservice perustoimintojen määrittäminen opettajalle epäonnistui.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Oikeus: {$a->cap}<br>Käyttöoikeus: {$a->permission}';
$string['deferredcourseevents'] = 'Lähetä lykkäytyneet kurssin tapahtumat';
$string['deferredcourseeventsdesc'] = 'Salli tallennettujen kurssin tapahtumien lähetys, joita on kertynyt Ally-tietoyhteysongelman aikana';
