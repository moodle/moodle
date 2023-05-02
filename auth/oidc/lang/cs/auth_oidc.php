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
 * Czech language strings.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'OpenID Connect';
$string['auth_oidcdescription'] = 'Plugin OpenID Connect poskytuje funkci jednotného přihlašování pomocí konfigurovatelných poskytovatelů identity.';
$string['cfg_authendpoint_key'] = 'Koncový bod autorizace';
$string['cfg_authendpoint_desc'] = 'Identifikátor URI koncového bodu autorizace od vašeho poskytovatele identity, který se má použít.';
$string['cfg_autoappend_key'] = 'Automaticky připojit';
$string['cfg_autoappend_desc'] = 'Automaticky připojit tento řetězec při přihlašování uživatelů pomocí postupu přihlášení uživatelské_jméno/heslo. To je užitečné, když váš poskytovatel identity požaduje společnou doménu, ale po uživatelích nechcete požadovat její psaní při přihlašování. Pokud například uživatel OpenID Connect má celé uživatelské jméno „jankovar@example.com“ a zde zadáte „@example.com“, bude uživatel jako své uživatelské jméno zadávat jen „jankovar“. <br /><b>Poznámka:</b> V případě, že existují konfliktní uživatelská jména – například že existuje uživatel v Moodlu se stejným uživatelským jménem, k určení „vítězného“ uživatele se použije priorita pluginu ověření.';
$string['cfg_clientid_key'] = 'ID klienta';
$string['cfg_clientid_desc'] = 'Vaše registrované ID klienta u poskytovatele identity.';
$string['cfg_clientsecret_key'] = 'Tajný klíč klienta';
$string['cfg_clientsecret_desc'] = 'Váš registrovaný tajný klíč klienta u poskytovatele identity. U některých poskytovatelů označovaný jen jako klíč.';
$string['cfg_err_invalidauthendpoint'] = 'Neplatný koncový bod autorizace';
$string['cfg_err_invalidtokenendpoint'] = 'Neplatný koncový bod tokenu';
$string['cfg_err_invalidclientid'] = 'Neplatné ID klienta';
$string['cfg_err_invalidclientsecret'] = 'Neplatný tajný klíč klienta';
$string['cfg_icon_key'] = 'Ikona';
$string['cfg_icon_desc'] = 'Ikona, která se bude zobrazovat na přihlašovací stránce vedle názvu poskytovatele.';
$string['cfg_iconalt_o365'] = 'Ikona Microsoft 365';
$string['cfg_iconalt_locked'] = 'Ikona uzamčení';
$string['cfg_iconalt_lock'] = 'Ikona zámku';
$string['cfg_iconalt_go'] = 'Zelené kolečko';
$string['cfg_iconalt_stop'] = 'Červené kolečko';
$string['cfg_iconalt_user'] = 'Ikona uživatele';
$string['cfg_iconalt_user2'] = 'Alternativa ikony uživatele';
$string['cfg_iconalt_key'] = 'Ikona klíče';
$string['cfg_iconalt_group'] = 'Ikona skupiny';
$string['cfg_iconalt_group2'] = 'Alternativa ikony skupiny';
$string['cfg_iconalt_mnet'] = 'Ikona MNET';
$string['cfg_iconalt_userlock'] = 'Ikona uživatele se zámkem';
$string['cfg_iconalt_plus'] = 'Ikona plus';
$string['cfg_iconalt_check'] = 'Ikona zaškrtnutí';
$string['cfg_iconalt_rightarrow'] = 'Ikona šipky doprava';
$string['cfg_customicon_key'] = 'Vlastní ikona';
$string['cfg_customicon_desc'] = 'Pokud chcete použít vlastní ikonu, nahrajte ji zde. To přepíše jakékoli ikony vybrané výše. <br /><br /><b>Poznámky k používání vlastních ikon:</b><ul><li>Velikost tohoto obrázku se na přihlašovací stránce <b>nepřizpůsobí</b>, proto doporučujeme nenahrávat obrázky větší než 35x35 pixelů.</li><li>Pokud jste nahráli vlastní ikonu a chtěli byste se vrátit k některé ze základních dodaných ikon, klikněte nahoře na vlastní ikonu, pak klikněte na tlačítko Odstranit, potvrďte kliknutím na OK a potom klikněte dole ve formuláři na tlačítko Uložit změny. Na přihlašovací stránce Moodlu se objeví vybraná základní ikona.</li></ul>';
$string['cfg_debugmode_key'] = 'Zaznamenávat zprávy ladění';
$string['cfg_debugmode_desc'] = 'Pokud je toto nastavení povoleno, do protokolu Moodlu jsou zaznamenávány informace, které vám mohou pomoci identifikovat problémy.';
$string['cfg_loginflow_key'] = 'Postup přihlášení';
$string['cfg_loginflow_authcode'] = 'Požadavek na autorizaci';
$string['cfg_loginflow_authcode_desc'] = 'Při použití tohoto postupu uživatel na přihlašovací stránce Moodlu klikne na ikonu poskytovatele identity (viz výše „Název poskytovatele“) a je následně přesměrován na poskytovatele, aby se přihlásil. Po úspěšném přihlášení je uživatel přesměrován zpět do Moodlu, kde proběhne transparentní přihlášení do Moodlu. Toto je nejstandardizovanější bezpečný způsob přihlašování uživatelů.';
$string['cfg_loginflow_rocreds'] = 'Ověřování uživatelské_jméno/heslo';
$string['cfg_loginflow_rocreds_desc'] = 'Při použití tohoto postupu uživatel zadá své uživatelské jméno a heslo do přihlašovacího formuláře Moodlu, obdobně jako při ručním přihlášení. Jeho přihlašovací údaje  jsou pak na pozadí předány poskytovateli identity, aby bylo získáno ověření. Tento postup je pro uživatele nejtransparentnější, protože nemá žádný přímý kontakt s poskytovatelem identity. Ne všichni poskytovatelé identity ale tento postup podporují.';
$string['cfg_oidcresource_key'] = 'Zdroj';
$string['cfg_oidcresource_desc'] = 'Zdroj OpenID Connect, pro který se odesílá požadavek.';
$string['cfg_oidcscope_key'] = 'Scope';
$string['cfg_oidcscope_desc'] = 'Rozsah OIDC, který se má použít.';
$string['cfg_opname_key'] = 'Název poskytovatele';
$string['cfg_opname_desc'] = 'Toto je údaj zobrazovaný koncovému uživateli, který identifikuje, jaký typ přihlašovacích údajů potřebuje uživatel použít k přihlášení. Tento údaj se používá na více místech rozhraní pro koncového uživatele v tomto pluginu k identifikaci vašeho poskytovatele.';
$string['cfg_redirecturi_key'] = 'URI pro přesměrování';
$string['cfg_redirecturi_desc'] = 'Toto je identifikátor URI, který se registruje jako „URI pro přesměrování“. Váš poskytovatel identity OpenID Connect by vás měl o tento identifikátor požádat při registraci Moodlu jako klienta.<br /><b>Poznámka:</b> Tento identifikátor musíte ve svém poskytovateli identity OpenID Connect zadat *přesně* tak, jak je zde uveden. Jakákoli odchylka znemožní přihlášení s použitím OpenID Connect.';
$string['cfg_tokenendpoint_key'] = 'Koncový bod tokenu';
$string['cfg_tokenendpoint_desc'] = 'URI koncového bodu tokenu od vašeho poskytovatele identity, který se má použít.';
$string['event_debug'] = 'Zpráva ladění';
$string['errorauthdisconnectemptypassword'] = 'Heslo nemůže být prázdné.';
$string['errorauthdisconnectemptyusername'] = 'Uživatelské jméno nemůže být prázdné.';
$string['errorauthdisconnectusernameexists'] = 'Toto uživatelské jméno se již používá. Zvolte jiné.';
$string['errorauthdisconnectnewmethod'] = 'Použít metodu přihlášení';
$string['errorauthdisconnectinvalidmethod'] = 'Přijata neplatná metoda přihlášení.';
$string['errorauthdisconnectifmanual'] = 'Pokud používáte metodu ručního přihlášení, zadejte níže přihlašovací údaje.';
$string['errorauthinvalididtoken'] = 'Přijato neplatné id_token.';
$string['errorauthloginfailednouser'] = 'Neplatné přihlášení: Uživatel nebyl v Moodlu nalezen.';
$string['errorauthnoauthcode'] = 'Nebyl přijat kód ověření.';
$string['errorauthnocreds'] = 'Nakonfigurujte přihlašovací údaje klienta OpenID Connect.';
$string['errorauthnoendpoints'] = 'Nakonfigurujte koncové body serveru OpenID Connect.';
$string['errorauthnohttpclient'] = 'Nastavte klienta HTTP.';
$string['errorauthnoidtoken'] = 'Nebylo přijato id_token OpenID Connect.';
$string['errorauthunknownstate'] = 'Neznámý stav.';
$string['errorauthuseralreadyconnected'] = 'Jste již připojeni k jinému uživateli OpenID Connect.';
$string['errorauthuserconnectedtodifferent'] = 'Uživatel OpenID Connect, který byl ověřen, je již připojen k uživateli Moodle.';
$string['errorbadloginflow'] = 'Byl zadán neplatný postup přihlášení. Poznámka: Pokud se vám tato zpráva zobrazuje poté, co jste provedli instalaci nebo upgrade, vymažte mezipaměť Moodlu.';
$string['errorjwtbadpayload'] = 'Nejde přečíst datovou část JWT.';
$string['errorjwtcouldnotreadheader'] = 'Nelze číst hlavičku JWT.';
$string['errorjwtempty'] = 'Přijato prázdné nebo ne-řetězec JWT.';
$string['errorjwtinvalidheader'] = 'Neplatná hlavička JWT';
$string['errorjwtmalformed'] = 'Přijato poškozené JWT.';
$string['errorjwtunsupportedalg'] = 'JWS Alg nebo JWE není podporováno';
$string['erroroidcnotenabled'] = 'Plugin ověření OpenID Connect není povolen.';
$string['errornodisconnectionauthmethod'] = 'Nelze se odpojit, protože není povolen žádný plugin ověření, který by mohl převzít funkci. (buď uživatelova předchozí metoda přihlášení, nebo metoda ručního přihlášení).';
$string['erroroidcclientinvalidendpoint'] = 'Přijato neplatné URI koncového bodu.';
$string['erroroidcclientnocreds'] = 'Nastavte přihlašovací údaje klienta s tajnými klíči.';
$string['erroroidcclientnoauthendpoint'] = 'Není nastaven žádný koncový bod autorizace. Nastavte pomocí $this->setendpoints';
$string['erroroidcclientnotokenendpoint'] = 'Není nastaven žádný koncový bod tokenu. Nastavte pomocí $this->setendpoints';
$string['erroroidcclientinsecuretokenendpoint'] = 'Koncový bod tokenu musí pro toto používat SSL/TLS.';
$string['errorucpinvalidaction'] = 'Přijata neplatná akce.';
$string['erroroidccall'] = 'Chyba v OpenID Connect. Další informace naleznete v protokolech.';
$string['erroroidccall_message'] = 'Chyba v OpenID Connect: {$a}';
$string['eventuserauthed'] = 'Uživatel autorizován s OpenID Connect';
$string['eventusercreated'] = 'Uživatel vytvořen s OpenID Connect';
$string['eventuserconnected'] = 'Uživatel připojen k OpenID Connect';
$string['eventuserloggedin'] = 'Uživatel přihlášen s OpenID Connect';
$string['eventuserdisconnected'] = 'Uživatel odpojen od OpenID Connect';
$string['oidc:manageconnection'] = 'Spravovat připojení OpenID Connect';
$string['ucp_general_intro'] = 'Zde můžete spravovat své připojení k {$a}. Pokud je nastavení povoleno, budete moci použít svůj účet {$a} k přihlášení do Moodlu namísto používání samostatného uživatelského jména a hesla. Jakmile se připojíte, nebudete si muset nadále pamatovat svoje uživatelské jméno a heslo pro Moodle, veškerá přihlášení budou probíhat přes {$a}.';
$string['ucp_login_start'] = 'Začít používat {$a} k přihlašování do Moodlu';
$string['ucp_login_start_desc'] = 'Toto přepne váš účet, aby k přihlašování do Moodlu používal {$a}. Jakmile nastavení povolíte, budete se přihlašovat se svými přihlašovacími údaji {$a} – vaše aktuální uživatelské jméno a heslo pro Moodle nebudou fungovat. Kdykoli můžete svůj účet odpojit a vrátit se k normálnímu přihlašování.';
$string['ucp_login_stop'] = 'Přestat používat {$a} k přihlašování do Moodlu';
$string['ucp_login_stop_desc'] = 'Aktuálně k přihlašování do Moodlu používáte {$a}. Když kliknete na „Přestat používat {$a} k přihlašování do Moodlu“, váš účet Moodle se odpojí od {$a}. Nebudete se nadále moci přihlašovat do Moodlu pomocí účtu {$a}. Bude požádáni, abyste si vytvořili uživatelské jméno a heslo, a od té chvíle se pak budete moci do Moodlu přihlašovat přímo.';
$string['ucp_login_status'] = 'Přihlašování {$a} je:';
$string['ucp_status_enabled'] = 'Povoleno';
$string['ucp_status_disabled'] = 'Zakázáno';
$string['ucp_disconnect_title'] = 'Odpojení {$a}';
$string['ucp_disconnect_details'] = 'Váš účet Moodle bude odpojen od {$a}. Budete si muset vytvořit uživatelské jméno a heslo pro přihlašování do Moodlu.';
$string['ucp_title'] = 'Správa {$a}';
