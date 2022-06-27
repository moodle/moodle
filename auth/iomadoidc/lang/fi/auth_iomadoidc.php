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
 * @package auth_iomadoidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'OpenID Connect';
$string['auth_iomadoidcdescription'] = 'OpenID Connect -lisäosa mahdollistaa kertakirjautumisen käyttämällä määritettävissä olevaa identiteetintarjoajaa.';
$string['cfg_authendpoint_key'] = 'Todennuksen päätepiste';
$string['cfg_authendpoint_desc'] = 'Käytettävän identiteetintarjoajan todennuksen päätepisteen URI.';
$string['cfg_autoappend_key'] = 'Lisää automaattisesti';
$string['cfg_autoappend_desc'] = 'Lisää tämän merkkijonon automaattisesti, kun käyttäjät käyttävät kirjautumiseen käyttäjänimi/salasana-kulkua. Tästä on hyötyä, kun identiteetintarjoaja edellyttää yhtenäistä toimialuetta, mutta et halua, että käyttäjien on kirjoitettava toimialue jokaisen kirjautumisen yhteydessä. Jos käyttäjän täydellinen OpenID Connect -käyttäjätunnus on esimerkiksi james@example.com ja kirjoitat tähän kenttään @example.com, käyttäjän tarvitsee antaa käyttäjänimeksi vain james. <br /><b>Huomautus:</b> Jos käyttäjänimissä on ristiriitoja, esimerkiksi järjestelmässä on samanniminen Moodle-käyttäjä, prioriteettijärjestys määräytyy todennuslisäosan mukaan.';
$string['cfg_clientid_key'] = 'Asiakastunnus';
$string['cfg_clientid_desc'] = 'Identiteetintarjoajan palveluun rekisteröity asiakastunnus.';
$string['cfg_clientsecret_key'] = 'Asiakassalaisuus';
$string['cfg_clientsecret_desc'] = 'Identiteetintarjoajan palveluun rekisteröity asiakassalaisuus. Joidenkin palveluntarjoajien palveluissa tätä kutsutaan avaimeksi.';
$string['cfg_err_invalidauthendpoint'] = 'Virheellinen todennuksen päätepiste';
$string['cfg_err_invalidtokenendpoint'] = 'Virheellinen avaimen päätepiste';
$string['cfg_err_invalidclientid'] = 'Virheellinen asiakastunnus';
$string['cfg_err_invalidclientsecret'] = 'Virheellinen asiakassalaisuus';
$string['cfg_icon_key'] = 'Kuvake';
$string['cfg_icon_desc'] = 'Kirjautumissivulla palveluntarjoajan nimen vieressä näkyvä kuvake.';
$string['cfg_iconalt_o365'] = 'Microsoft 365 -kuvake';
$string['cfg_iconalt_locked'] = 'Lukittu-kuvake';
$string['cfg_iconalt_lock'] = 'Lukkokuvake';
$string['cfg_iconalt_go'] = 'Vihreä ympyrä';
$string['cfg_iconalt_stop'] = 'Punainen ympyrä';
$string['cfg_iconalt_user'] = 'Käyttäjäkuvake';
$string['cfg_iconalt_user2'] = 'Vaihtoehtoinen käyttäjäkuvake';
$string['cfg_iconalt_key'] = 'Avainkuvake';
$string['cfg_iconalt_group'] = 'Ryhmäkuvake';
$string['cfg_iconalt_group2'] = 'Vaihtoehtoinen ryhmäkuvake';
$string['cfg_iconalt_mnet'] = 'MNET-kuvake';
$string['cfg_iconalt_userlock'] = 'Käyttäjä ja lukko -kuvake';
$string['cfg_iconalt_plus'] = 'Pluskuvake';
$string['cfg_iconalt_check'] = 'Valintamerkki-kuvake';
$string['cfg_iconalt_rightarrow'] = 'Oikealle osoittava nuolikuvake';
$string['cfg_customicon_key'] = 'Mukautettu kuvake';
$string['cfg_customicon_desc'] = 'Jos haluat käyttää mukautettua kuvaketta, lataa se tähän. Ladattu kuvake korvaa valittuna olevan kuvakkeen. <br /><br /><b>Mukautettujen kuvakkeiden käytössä huomioitavaa:</b><ul><li>Kuvan kokoa <b>ei</b> muuteta kirjautumissivulla, joten suosittelemme lataamaan kuvan, jonka koko on enintään 35 x 35 pikseliä.</li><li>Jos olet ladannut mukautetun kuvan, mutta haluat palata käyttämään vakiokuvaketta, napsauta mukautetun kuvakkeen ruutua yllä ja valitse Poista ja sitten OK. Valitse lopuksi Tallenna muutokset tämän lomakkeen alaosassa. Valittu vakiokuvake näytetään tämän jälkeen Moodlen kirjautumissivulla.</li></ul>';
$string['cfg_debugmode_key'] = 'Kirjaa virheenkorjausviestit';
$string['cfg_debugmode_desc'] = 'Jos asetus on käytössä, tiedot kirjataan Moodlen lokiin ongelmien tunnistamista varten.';
$string['cfg_loginflow_key'] = 'Kirjautumiskulku';
$string['cfg_loginflow_authcode'] = 'Valtuutuspyyntö';
$string['cfg_loginflow_authcode_desc'] = 'Jos tämä kirjautumiskulku on käytössä, käyttäjä napsauttaa identiteetintarjoajan nimeä (ks. Palveluntarjoajan nimi) Moodlen kirjautumissivulla, jonka jälkeen käyttäjä ohjataan palveluntarjoajan sivulle kirjautumista varten. Jos kirjautuminen onnistuu, käyttäjä ohjataan takaisin Moodleen, jossa Moodle-kirjautuminen tapahtuu läpinäkyvästi. Tämä on standardisoitu ja turvallisin käyttäjien kirjautumismenetelmä.';
$string['cfg_loginflow_rocreds'] = 'Käyttäjänimen/salasanan todennus';
$string['cfg_loginflow_rocreds_desc'] = 'Jos tämä kirjautumiskulku on käytössä, käyttäjä kirjautuu Moodleen antamalla käyttäjänimen ja salasanan Moodlen kirjautumislomakkeeseen. Tunnistetiedot välitetään taustalla identiteetintarjoajalle todennusta varten. Tämä kulku on läpinäkyvin käyttäjän kannalta, koska käyttäjä ei ole suoraan tekemisissä identiteetintarjoajan kanssa. Huomaa, että kaikki identiteetintarjoajat eivät tue tätä kulkua.';
$string['cfg_iomadoidcresource_key'] = 'Resurssi';
$string['cfg_iomadoidcresource_desc'] = 'OpenID Connect -resurssi, jota lähetettävä pyyntö koskee.';
$string['cfg_iomadoidcscope_key'] = 'laajuus';
$string['cfg_iomadoidcscope_desc'] = 'Käytettävä IOMADoIDC-soveltamisala.';
$string['cfg_opname_key'] = 'Palveluntarjoajan nimi';
$string['cfg_opname_desc'] = 'Tämä on loppukäyttäjälle näkyvä selite, joka ilmoittaa kirjautumiseen käytettävien tunnistetietojen tyypin. Tätä palveluntarjoajan selitettä käytetään tämän lisäosan kaikissa käyttäjälle näkyvissä osioissa.';
$string['cfg_redirecturi_key'] = 'Uudelleenohjauksen URI';
$string['cfg_redirecturi_desc'] = 'Tämä on rekisteröitävä uudelleenohjauksen URI. OpenID Connect -identiteetintarjoaja pyytää tätä tietoa, kun rekisteröit Moodlen asiakkaaksi. <br /><b>HUOMAUTUS:</b> Anna URI OpenID Connect -palveluntarjoajan palveluun *täsmälleen* tässä näkyvässä muodossa. Muussa tapauksessa kirjautuminen OpenID Connect -palvelun avulla ei onnistu.';
$string['cfg_tokenendpoint_key'] = 'Avaimen päätepiste';
$string['cfg_tokenendpoint_desc'] = 'Käytettävän identiteetintarjoajan avaimen päätepiste.';
$string['event_debug'] = 'Virheenkorjausviesti';
$string['errorauthdisconnectemptypassword'] = 'Salasana ei voi olla tyhjä';
$string['errorauthdisconnectemptyusername'] = 'Käyttäjänimi ei voi olla tyhjä';
$string['errorauthdisconnectusernameexists'] = 'Annettu käyttäjänimi on jo käytössä. Valitse toinen nimi.';
$string['errorauthdisconnectnewmethod'] = 'Käyttäjän kirjautumismenetelmä';
$string['errorauthdisconnectinvalidmethod'] = 'Virheellinen kirjautumismenetelmä vastaanotettiin.';
$string['errorauthdisconnectifmanual'] = 'Jos käytät manuaalista kirjautumismenetelmää, anna tunnistetiedot alla.';
$string['errorauthinvalididtoken'] = 'Virheellinen id_token vastaanotettiin.';
$string['errorauthloginfailednouser'] = 'Kirjautumisvirhe: käyttäjää ei löydy Moodlesta.';
$string['errorauthnoauthcode'] = 'Todennuskoodia ei vastaanotettu.';
$string['errorauthnocreds'] = 'Määritä OpenID Connect -asiakkaan tunnistetiedot.';
$string['errorauthnoendpoints'] = 'Määritä OpenID Connect -palvelimen päätepisteet.';
$string['errorauthnohttpclient'] = 'Määritä HTTP-asiakas.';
$string['errorauthnoidtoken'] = 'OpenID Connectin id_token-avainta ei vastaanotettu.';
$string['errorauthunknownstate'] = 'Tuntematon tila.';
$string['errorauthuseralreadyconnected'] = 'Yhteys on jo muodostettu toiseen OpenID Connect -käyttäjään.';
$string['errorauthuserconnectedtodifferent'] = 'Todennettu OpenID Connect -käyttäjä on jo yhdistetty Moodle-käyttäjään.';
$string['errorbadloginflow'] = 'Määritetty kirjautumiskulku on virheellinen. Huomautus: Jos saat tämän viestin asennuksen tai päivityksen jälkeen, tyhjennä Moodlen välimuisti.';
$string['errorjwtbadpayload'] = 'JWT-tietoja ei voitu lukea.';
$string['errorjwtcouldnotreadheader'] = 'JWT-otsikkoa ei voitu lukea';
$string['errorjwtempty'] = 'Vastaanotettu JWT on tyhjä, tai se ei ole kelvollinen merkkijono.';
$string['errorjwtinvalidheader'] = 'Virheellinen JWT-otsikko';
$string['errorjwtmalformed'] = 'Vastaanotettu JWT on virheellinen.';
$string['errorjwtunsupportedalg'] = 'JWS Alg tai JWE ei ole tuettu';
$string['erroriomadoidcnotenabled'] = 'OpenID Connect -todennuslisäosa ei ole käytössä.';
$string['errornodisconnectionauthmethod'] = 'Yhteyttä ei voi katkaista, koska vaihtoehtoista todennuslisäosaa ei ole määritetty (se voi olla käyttäjän edellinen kirjautumismenetelmä tai manuaalinen kirjautumismenetelmä).';
$string['erroriomadoidcclientinvalidendpoint'] = 'Virheellinen pääpisteen URI vastaanotettiin.';
$string['erroriomadoidcclientnocreds'] = 'Määritä asiakkaan tunnistetiedot setcreds-komennolla';
$string['erroriomadoidcclientnoauthendpoint'] = 'Todennuksen päätepistettä ei ole määritetty. Määritä komennolla $this->setendpoints';
$string['erroriomadoidcclientnotokenendpoint'] = 'Avaimen päätepistettä ei ole määritetty. Määritä komennolla $this->setendpoints';
$string['erroriomadoidcclientinsecuretokenendpoint'] = 'Avaimen päätepisteen on käytettävä tähän SSL/TLS-yhteyttä.';
$string['errorucpinvalidaction'] = 'Virheellinen toiminto vastaanotettiin.';
$string['erroriomadoidccall'] = 'OpenID Connect -palvelussa tapahtui virhe. Lisätietoja on lokeissa.';
$string['erroriomadoidccall_message'] = 'Virhe OpenID Connect -palvelussa: {$a}';
$string['eventuserauthed'] = 'Käyttäjä valtuutettiin OpenID Connectin avulla';
$string['eventusercreated'] = 'Käyttäjä luotiin OpenID Connectin avulla';
$string['eventuserconnected'] = 'Käyttäjä yhdistettiin OpenID Connectin avulla';
$string['eventuserloggedin'] = 'Käyttäjä kirjautui OpenID Connectin avulla';
$string['eventuserdisconnected'] = 'Käyttäjän OpenID Connect -yhteys katkaistiin';
$string['iomadoidc:manageconnection'] = 'OpenID Connect -yhteyden hallinta';
$string['ucp_general_intro'] = 'Tässä kohdassa voit hallita {$a} -yhteyttä. Jos asetus on käytössä, voit kirjautua Moodleen käyttämällä {$a} -tiliäsi erillisen käyttäjänimen ja salasanan sijaan. Kun yhteys on luotu, sinun ei tarvitse muistaa Moodle-käyttäjänimeä ja -salasanaa, koska {$a} huolehtii kirjautumisesta.';
$string['ucp_login_start'] = 'Aloita palvelun {$a} käyttö Moodle-kirjautumiseen';
$string['ucp_login_start_desc'] = 'Voit käyttää {$a} -tiliäsi Moodle-kirjautumiseen. Kun asetus on käytössä, kirjaudut Moodleen käyttämällä {$a} -tunnistetietojasi. Nykyinen Moodle-käyttäjänimi ja -salasana eivät enää toimi. Voit katkaista tilisi yhteyden milloin tahansa ja palata käyttämään normaalia kirjautumista.';
$string['ucp_login_stop'] = 'Lopeta palvelun {$a} käyttö Moodle-kirjautumiseen';
$string['ucp_login_stop_desc'] = '{$a} on tällä hetkellä käytössä Moodle-kirjautumiseen. Jos valitset Lopeta palvelun {$a} käyttö -asetuksen, Moodle-tilin yhteys palveluun {$a} katkaistaan. Tämän jälkeen et voi kirjautua Moodleen käyttämällä {$a} -tiliäsi. Sinua pyydetään luomaan käyttäjänimi ja salasana, joiden avulla voit kirjautua suoraan Moodleen.';
$string['ucp_login_status'] = '{$a} -kirjautuminen:';
$string['ucp_status_enabled'] = 'Käytössä';
$string['ucp_status_disabled'] = 'Ei käytössä';
$string['ucp_disconnect_title'] = '{$a} -yhteyden katkaisu';
$string['ucp_disconnect_details'] = 'Tämä katkaisee Moodle-tilin yhteyden kohteesta {$a}. Tarvitset käyttäjänimen ja salasanan, jotta voit kirjautua Moodleen.';
$string['ucp_title'] = '{$a} -hallinta';
