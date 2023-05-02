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
 * Finnish language strings.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'Microsoft 365 -integrointi';
$string['acp_title'] = 'Microsoft 365 -järjestelmänvalvojan ohjauspaneeli';
$string['acp_healthcheck'] = 'Kuntotarkistus';
$string['acp_parentsite_name'] = 'Moodle';
$string['acp_parentsite_desc'] = 'Sivusto jaettuja Moodle-kurssitietoja varten.';
$string['calendar_user'] = 'Oma (käyttäjän) kalenteri';
$string['calendar_site'] = 'Sivuston kalenteri';
$string['erroracpauthoidcnotconfig'] = 'Määritä sovelluksen tunnistetiedot ensin auth_oidc-lisäosaan.';
$string['erroracplocalo365notconfig'] = 'Määritä ensin local_o365.';
$string['errorhttpclientbadtempfileloc'] = 'Väliaikaista sijaintia ei voitu avata tiedoston tallentamista varten.';
$string['errorhttpclientnofileinput'] = 'Tiedostoparametri puuttuu pyynnöstä httpclient::put';
$string['errorcouldnotrefreshtoken'] = 'Avainta ei voitu päivittää';
$string['errorchecksystemapiuser'] = 'Järjestelmän ohjelmointirajapinnan käyttäjäavainta ei voitu hakea. Suorita kuntotarkistus ja varmista, että Moodlen cron on käynnissä. Päivitä järjestelmän ohjelmointirajapinnan käyttäjä tarvittaessa.';
$string['erroro365apibadcall'] = 'Virhe ohjelmointirajapinnan kutsussa.';
$string['erroro365apibadcall_message'] = 'Virhe ohjelmointirajapinnan kutsussa: {$a}';
$string['erroro365apibadpermission'] = 'Käyttöoikeutta ei löydy';
$string['erroro365apicouldnotcreatesite'] = 'Sivuston luonnissa oli ongelma.';
$string['erroro365apicoursenotfound'] = 'Kurssia ei löydy.';
$string['erroro365apiinvalidtoken'] = 'Avain on virheellinen tai vanhentunut.';
$string['erroro365apiinvalidmethod'] = 'API-kutsuun välitettiin virheellinen HttpMethod-ominaisuus';
$string['erroro365apinoparentinfo'] = 'Pääkansion tietoja ei löydetty';
$string['erroro365apinotimplemented'] = 'Tämä pitäisi korvata.';
$string['erroro365apinotoken'] = 'Määritetyn resurssin ja käyttäjän avain puuttuu, eikä sitä voitu hakea. Onko käyttäjän päivitysavain vanhentunut?';
$string['erroro365apisiteexistsnolocal'] = 'Sivusto on jo luotu, mutta paikallista tietuetta ei löydy.';
$string['eventapifail'] = 'Ohjelmointirajapinnan virhe';
$string['eventcalendarsubscribed'] = 'Käyttäjä tilasi kalenterin';
$string['eventcalendarunsubscribed'] = 'Käyttäjä lopetti kalenterin tilauksen';
$string['healthcheck_fixlink'] = 'Korjaa ongelma napsauttamalla tätä.';
$string['healthcheck_systemapiuser_title'] = 'Järjestelmän ohjelmointirajapinnan käyttäjä';
$string['healthcheck_systemtoken_result_notoken'] = 'Moodlella ei ole tarvittavaa avainta kommunikointiin Microsoft 365:n kanssa järjestelmän ohjelmointirajapinnan käyttäjänä. Tämän ongelman voi ratkaista tavallisesti päivittämällä järjestelmän ohjelmointirajapinnan käyttäjän.';
$string['healthcheck_systemtoken_result_noclientcreds'] = 'OpenID Connect -lisäosassa ei ole sovelluksen tunnistetietoja. Ilman näitä tunnistetietoja Moodle ei voi kommunikoida Microsoft 365:n kanssa. Siirry asetussivulle ja lisää tunnistetiedot napsauttamalla tätä.';
$string['healthcheck_systemtoken_result_badtoken'] = 'Microsoft 365 -yhteydessä oli ongelma järjestelmän ohjelmointirajapinnan käyttäjänä. Järjestelmän ohjelmointirajapinnan käyttäjän päivittäminen ratkaisee tavallisesti tämän ongelman.';
$string['healthcheck_systemtoken_result_passed'] = 'Moodle voi kommunikoida Microsoft 365:n kanssa järjestelmän ohjelmointirajapinnan käyttäjänä.';
$string['settings_aadsync'] = 'Synkronoi käyttäjät Azure AD:n kanssa';
$string['settings_aadsync_details'] = 'Kun asetus on käytössä, Moodle- ja Azure AD -käyttäjät synkronoidaan edellä olevien asetusten mukaisesti.<br /><br /><b>Huomautus: </b>Synkronointityö suoritetaan Moodlen cron-prosessin aikana, ja enintään 1 000 käyttäjää voidaan synkronoida kerralla. Työ suoritetaan oletusarvoisesti klo 1:00 palvelimen aikavyöhykkeen mukaisesti. Jos suuria käyttäjäjoukkoja on synkronoitava nopeasti, lyhennä <b>Synkronoi käyttäjät Azure AD:n kanssa</b> -tehtävän suoritusväliä <a href="{$a}">ajoitettujen tehtävien hallintasivulla.</a><br /><br />Lisätietoja on <a href="https://docs.moodle.org/30/en/Office365#User_sync">käyttäjien synkronointia käsittelevissä ohjeaiheissa</a><br /><br />';
$string['settings_aadsync_create'] = 'Luo Azure AD -käyttäjille tilit Moodlessa';
$string['settings_aadsync_delete'] = 'Poista aiemmin synkronoidut tilit Moodlesta, kun tilit poistetaan Azure AD:stä';
$string['settings_aadsync_match'] = 'Yhdistä nykyiset Moodle-käyttäjät samannimisiin Azure AD -tileihin<br /><small>Tämä tarkistaa Microsoft 365 -käyttäjänimet ja Moodle-käyttäjänimet ja etsii niiden vastaavauudet. Vastaavuuksien etsintä ei erota kirjainkokoa eikä Microsoft 365 -vuokraajatunnusta. Esimerkiksi Moodlen käyttäjätunnus BoB.SmiTh täsmää käyttäjätunnukseen bob.smith@example.onmicrosoft.com. Vastaavien käyttäjien Moodle- ja Microsoft 365-tilit yhdistetään ja kyseiset käyttäjät voivat käyttää Microsoft 365:n ja Moodlen integrointitoimintoja. Käyttäjän todennusmenetelmä ei muutu, ellei alla olevaa asetusta oteta käyttöön.</small>';
$string['settings_aadsync_matchswitchauth'] = 'Vaihda vastaavien käyttäjien todennustavaksi Microsoft 365 (OpenID Connect)<br /><small>Tämän asetuksen käyttäminen edellyttää, että yllä oleva yhdistämisasetus on käytössä. Tämän asetuksen ottaminen käyttöön vaihtaa yhdistettyjen käyttäjien todennustavaksi OpenID Connectin. Kun käyttäjälle löytyy vastaavauus, käyttäjän todennustavaksi muutetaan OpenID Connect. Tämän jälkeen käyttäjä kirjautuu Moodleen käyttämällä Microsoft 365 -tunnistetietoja. <b>Huomautus:</b> Varmista, että OpenID Connect -todennuslisäosa on otettu käyttöön, jos haluat käyttää tätä asetusta.</small>';
$string['settings_aadtenant'] = 'Azure AD -vuokraaja';
$string['settings_aadtenant_details'] = 'Määritä tähän organisaation Azure AD vuokraajatunnus, esimerkiksi contoso.onmicrosoft.com.';
$string['settings_azuresetup'] = 'Azure-määritys';
$string['settings_azuresetup_details'] = 'Tämä työkalu tarkistaa, että kaikki Azure-asetukset on määritetty oikein. Se voi myös auttaa korjaamaan yleisiä virheitä.';
$string['settings_azuresetup_update'] = 'Päivitä';
$string['settings_azuresetup_checking'] = 'Tarkistetaan...';
$string['settings_azuresetup_missingperms'] = 'Puuttuvat käyttöoikeudet:';
$string['settings_azuresetup_permscorrect'] = 'Käyttöoikeudet ovat riittävät.';
$string['settings_azuresetup_errorcheck'] = 'Azure-määritysten tarkistamisen aikana tapahtui virhe.';
$string['settings_azuresetup_unifiedheader'] = 'Yhdistetty ohjelmointirajapinta';
$string['settings_azuresetup_unifieddesc'] = 'Yhdistetty ohjelmointirajapinta korvaa aiemmat sovelluskohtaiset ohjelmointirajapinnat. Jos mahdollista, lisää tämä rajapinta jo nyt Azure-sovellukseen, jotta sovellus on valmis tulevia muutoksia varten. Vanha ohjelmointirajapinta poistetaan käytöstä myöhemmin.';
$string['settings_azuresetup_unifiederror'] = 'Yhdistetyn ohjelmointirajapinnan tuen tarkistuksessa tapahtui virhe.';
$string['settings_azuresetup_unifiedactive'] = 'Yhdistetty ohjelmointirajapinta on aktiivinen.';
$string['settings_azuresetup_unifiedmissing'] = 'Tästä sovelluksesta ei löytynyt yhdistettyä ohjelmointirajapintaa.';
$string['settings_creategroups'] = 'Luo käyttäjäryhmät';
$string['settings_creategroups_details'] = 'Jos asetus on käytössä, opettaja- ja opiskelijaryhmät luodaan Microsoft 365:een jokaista sivuston kurssia varten. Kaikki tarvittavat ryhmät luodaan jokaisen cron-työn aikana (myös kaikki uudet jäsenet lisätään). Tämän jälkeen ryhmien jäsenyyksiä lisätään ja poistetaan sen mukaan, miten käyttäjät rekisteröityvät Moodle-kursseille.<br /><b>Huomautus: </b>Tämä toiminto edellyttää Microsoft 365:n yhdistetyn ohjelmointirajapinnan lisäämistä sovellukseen Azuressa. <a href="https://docs.moodle.org/30/en/Office365#User_groups">Katso määritysohjeet täältä.</a>';
$string['settings_o365china'] = 'Microsoft 365 Kiinassa';
$string['settings_o365china_details'] = 'Valitse tämä, jos käytät Microsoft 365:tä Kiinassa.';
$string['settings_debugmode'] = 'Kirjaa virheenkorjausviestit';
$string['settings_debugmode_details'] = 'Jos asetus on käytössä, tiedot kirjataan Moodlen lokiin ongelmien tunnistamista varten.';
$string['settings_detectoidc'] = 'Sovelluksen tunnistetiedot';
$string['settings_detectoidc_details'] = 'Moodle tarvitsee tunnistetiedot Microsoft 365 -yhteyden muodostamista varten. Tunnistetiedot määritetään OpenID Connect -todennuslisäosassa.';
$string['settings_detectoidc_credsvalid'] = 'Tunnistetiedot on määritetty';
$string['settings_detectoidc_credsvalid_link'] = 'Muuta';
$string['settings_detectoidc_credsinvalid'] = 'Tunnistetietoja ei ole määritetty tai ne ovat puutteelliset.';
$string['settings_detectoidc_credsinvalid_link'] = 'Määritä tunnistetiedot';
$string['settings_detectperms'] = 'Sovelluksen käyttöoikeudet';
$string['settings_detectperms_details'] = 'Tämän lisäosan toimintojen käyttäminen edellyttää asianmukaisten käyttöoikeuksien määrittämistä sovellukselle Azure AD:ssä.';
$string['settings_detectperms_nocreds'] = 'Määritä ensin sovelluksen tunnistetiedot. Katso lisätiedot edellisestä asetuksesta.';
$string['settings_detectperms_missing'] = 'Puuttuu:';
$string['settings_detectperms_errorfix'] = 'Käyttöoikeuksien korjaamisen aikana tapahtui virhe. Määritä oikeudet manuaalisesti Azuressa.';
$string['settings_detectperms_fixperms'] = 'Korjaa käyttöoikeudet';
$string['settings_detectperms_fixprereq'] = 'Automaattinen korjaus edellyttää, että järjestelmän API-käyttäjä on järjestelmänvalvoja ja että Windows Azure Active Directory -sovellukselle on annettu Azuressa organisaation hakemiston käyttöoikeus.';
$string['settings_detectperms_nounified'] = 'Yhdistetty ohjelmointirajapinta ei ole käytettävissä. Jotkin uudet toiminnot eivät ehkä toimi.';
$string['settings_detectperms_unifiednomissing'] = 'Kaikki yhdistetyt käyttöoikeudet ovat käytettävissä.';
$string['settings_detectperms_update'] = 'Päivitä';
$string['settings_detectperms_valid'] = 'Käyttöoikeudet on määritetty.';
$string['settings_detectperms_invalid'] = 'Tarkasta oikeudet Azure AD:ssä';
$string['settings_header_setup'] = 'Määritys';
$string['settings_header_options'] = 'Asetukset';
$string['settings_healthcheck'] = 'Kuntotarkistus';
$string['settings_healthcheck_details'] = 'Jos jokin menee vikaan, suorita kuntotarkistus. Kuntotarkistus voi tunnistaa ongelmat ja ehdottaa ratkaisuja.';
$string['settings_healthcheck_linktext'] = 'Suorita kuntotarkistus';
$string['settings_odburl'] = 'OneDrive for Businessin URL-osoite';
$string['settings_odburl_details'] = 'URL-osoite OneDrive for Businessin käyttämistä varten. Osoite määräytyy tavallisesti Azure AD -vuokraajatunnuksen mukaan. Jos Azure AD -vuokraajatunnus on esimerkiksi contoso.onmicrosoft.com, oikea osoite on todennäköisesti contoso-my.sharepoint.com. Anna vain toimialuenimi ilman etuliitettä http:// tai https://';
$string['settings_serviceresourceabstract_valid'] = '{$a} on kelvollinen.';
$string['settings_serviceresourceabstract_invalid'] = 'Tämä arvo ei kelpaa.';
$string['settings_serviceresourceabstract_nocreds'] = 'Määritä ensin sovelluksen tunnistetiedot.';
$string['settings_serviceresourceabstract_empty'] = 'Anna arvo tai yritä löytää oikea arvo valitsemalla Tunnista.';
$string['settings_systemapiuser'] = 'Järjestelmän ohjelmointirajapinnan käyttäjä';
$string['settings_systemapiuser_details'] = 'Tämä voi olla kuka tahansa Azure AD -käyttäjä, jolla on järjestelmänvalvojan tili tai erityinen tili. Tiliä käytetään muiden kuin käyttäjäkohtaisten toimintojen tekemiseen, esimerkiksi kurssin SharePoint-sivustojen hallintaan.';
$string['settings_systemapiuser_change'] = 'Vaihda käyttäjä';
$string['settings_systemapiuser_usernotset'] = 'Käyttäjää ei ole määritetty.';
$string['settings_systemapiuser_userset'] = '{$a}';
$string['settings_systemapiuser_setuser'] = 'Määritä käyttäjä';
$string['spsite_group_contributors_name'] = 'Ryhmän {$a} osallistujat';
$string['spsite_group_contributors_desc'] = 'Kaikki käyttäjät, joilla on oikeus käyttää kurssin {$a} tiedostoja';
$string['task_calendarsyncin'] = 'Synkronoi O365-tapahtumat Moodleen';
$string['task_coursesync'] = 'Luo käyttäjäryhmät Microsoft 365:ssä';
$string['task_refreshsystemrefreshtoken'] = 'Päivitä järjestelmän API-käyttäjän päivitysavain';
$string['task_syncusers'] = 'Synkronoi käyttäjät Azure AD:n kanssa.';
$string['ucp_connectionstatus'] = 'Yhteyden tila';
$string['ucp_calsync_availcal'] = 'Käytettävissä olevat Moodle-kalenterit';
$string['ucp_calsync_title'] = 'Outlook-kalenterin synkronointi';
$string['ucp_calsync_desc'] = 'Valitut kalenterit synkronoidaan Moodlesta Outlookin kalenteriin.';
$string['ucp_connection_status'] = 'Microsoft 365 -yhteyden tila:';
$string['ucp_connection_start'] = 'Muodosta Microsoft 365 -yhteys';
$string['ucp_connection_stop'] = 'Katkaise Microsoft 365 -yhteys';
$string['ucp_features'] = 'Microsoft 365:n toiminnot';
$string['ucp_features_intro'] = 'Seuraavassa on luettelo Microsoft 365:n toiminnoista, joilla voit tehostaa Moodlen käyttöä.';
$string['ucp_features_intro_notconnected'] = 'Osa toiminnoista edellyttää Microsoft 365 -yhteyden muodostamista.';
$string['ucp_general_intro'] = 'Tässä kohdassa voit hallita Microsoft 365 -yhteyden asetuksia.';
$string['ucp_index_aadlogin_title'] = 'Microsoft 365 -kirjautuminen';
$string['ucp_index_aadlogin_desc'] = 'Voit kirjautua Moodleen käyttämällä Microsoft 365 -tunnistetietojasi. ';
$string['ucp_index_calendar_title'] = 'Outlook-kalenterin synkronointi';
$string['ucp_index_calendar_desc'] = 'Voit synkronoida Moodlen ja Outlookin kalenterit. Voit viedä Moodle-kalenterin tapahtumat Outlookiin ja tuoda Outlook-tapahtumat Moodleen.';
$string['ucp_index_connectionstatus_connected'] = 'Olet tällä hetkellä yhteydessä Microsoft 365:een';
$string['ucp_index_connectionstatus_matched'] = 'Sinut on yhdistetty Microsoft 365 -käyttäjään <small>{$a}</small>. Vahvista yhteys kirjautumalla Microsoft 365:een alla olevan linkin kautta.';
$string['ucp_index_connectionstatus_notconnected'] = 'Microsoft 365 -yhteyttä ei tällä hetkellä ole muodostettu';
$string['ucp_index_onenote_title'] = 'OneNote';
$string['ucp_index_onenote_desc'] = 'OneNote-integroinnin avulla voit käyttää Microsoft 365:n OneNote-sovellusta Moodlen kanssa. Voit tehdä tehtäviä OneNotessa ja laatia muistiinpanoja kurssien aikana.';
$string['ucp_notconnected'] = 'Muodosta Microsoft 365 -yhteys ennen tämän sivun avaamista.';
$string['settings_onenote'] = 'Poista käytöstä Microsoft 365 OneNote';
$string['ucp_status_enabled'] = 'Aktiivinen';
$string['ucp_status_disabled'] = 'Ei yhteyttä';
$string['ucp_syncwith_title'] = 'Synkronointi:';
$string['ucp_syncdir_title'] = 'Synkronointitoiminta:';
$string['ucp_syncdir_out'] = 'Moodlesta Outlookiin';
$string['ucp_syncdir_in'] = 'Outlookista Moodleen';
$string['ucp_syncdir_both'] = 'Päivitä Outlook ja Moodle';
$string['ucp_title'] = 'Microsoft 365 / Moodle -ohjauspaneeli';
$string['ucp_options'] = 'Asetukset';
