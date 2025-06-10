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

$string['adminurl'] = 'Adresa URL ke spuštění';
$string['adminurldesc'] = 'Adresa URL ke spuštění LTI, která se používá pro přístup k sestavě přístupnosti';
$string['allyclientconfig'] = 'Konfigurace služby Ally';
$string['ally:clientconfig'] = 'Otevřít a aktualizovat konfiguraci klienta';
$string['ally:viewlogs'] = 'Prohlížeč protokolů služby Ally';
$string['clientid'] = 'ID klienta';
$string['clientiddesc'] = 'ID klienta služby Ally';
$string['code'] = 'Kód';
$string['contentauthors'] = 'Autoři obsahu';
$string['contentauthorsdesc'] = 'Správcům a uživatelům přiřazeným k těmto vybraným rolím se vyhodnotí přístupnost nahraných souborů kurzu. Souborům se udělí hodnocení přístupnosti. Nízké hodnocení znamená, že je soubor třeba změnit tak, aby byl přístupnější.';
$string['contentupdatestask'] = 'Úloha aktualizací obsahu';
$string['curlerror'] = 'Chyba cURL: {$a}';
$string['curlinvalidhttpcode'] = 'Neplatný stavový kód protokolu HTTP: {$a}';
$string['curlnohttpcode'] = 'Kód stavu HTTP nelze ověřit';
$string['error:invalidcomponentident'] = 'Neplatný identifikátor komponenty {$a}';
$string['error:pluginfilequestiononly'] = 'U této adresy URL jsou podporovány pouze komponenty otázek';
$string['error:componentcontentnotfound'] = 'Obsah pro položku {$a} nebyl nalezen';
$string['error:wstokenmissing'] = 'Chybí token webové služby. Možná by měl uživatel správce spustit automatickou konfiguraci?';
$string['excludeunused'] = 'Vyloučit nepoužívané soubory';
$string['excludeunuseddesc'] = 'Vynechejte soubory, které jsou připojeny k obsahu HTML, ale v kódu HTML jsou propojeny nebo odkazují.';
$string['filecoursenotfound'] = 'Předaný soubor nenáleží žádnému kurzu.';
$string['fileupdatestask'] = 'Posunout aktualizace souborů do služby Ally';
$string['id'] = 'ID';
$string['key'] = 'Klíč';
$string['keydesc'] = 'Klíč příjemce LTI';
$string['level'] = 'Úroveň';
$string['message'] = 'Zpráva';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'Adresa URL aktualizací souborů';
$string['pushurldesc'] = 'Posune oznámení o aktualizacích souborů na tuto adresu URL.';
$string['queuesendmessagesfailure'] = 'Při odesílání zpráv do služby AWS SQS došlo k chybě. Data chyby: $a';
$string['secret'] = 'Tajný klíč';
$string['secretdesc'] = 'Tajný klíč LTI';
$string['showdata'] = 'Zobrazit data';
$string['hidedata'] = 'Skrýt data';
$string['showexplanation'] = 'Zobrazit vysvětlení';
$string['hideexplanation'] = 'Skrýt vysvětlení';
$string['showexception'] = 'Zobrazit výjimku';
$string['hideexception'] = 'Skrýt výjimku';
$string['usercapabilitymissing'] = 'Poskytnutý uživatel není způsobilý k odstranění tohoto souboru.';
$string['autoconfigure'] = 'Automaticky konfigurovat webovou službu Ally';
$string['autoconfiguredesc'] = 'Automaticky vytvoří uživatele a roli webové služby pro službu Ally.';
$string['autoconfigureconfirmation'] = 'Automaticky vytvoří uživatele a roli webové služby pro službu Ally a povolí webovou službu. Provedou se následující akce:<ul><li>vytvořte roli s názvem ,ally_webservice\' a uživatele s uživatelským jménem ,ally_webuser‘</li><li>přidejte uživatele ,ally_webuser‘ k roli ,ally_webservice‘</li><li>povolte webové služby</li><li>povolte protokol webových služeb Rest</li><li>povolte webovou službu Ally</li><li>vytvořte token pro účet ,ally_webuser‘</li></ul>';
$string['autoconfigsuccess'] = 'Webová služba Ally byla úspěšně automaticky nakonfigurována.';
$string['autoconfigtoken'] = 'Token webové služby:';
$string['autoconfigapicall'] = 'Pomocí následující adresy URL můžete otestovat, zda webová služba funguje:';
$string['privacy:metadata:files:action'] = 'Akce provedená u souboru, například vytvoření, aktualizace nebo odstranění';
$string['privacy:metadata:files:contenthash'] = 'Hash hodnota obsahu souboru za účelem určení jedinečnosti';
$string['privacy:metadata:files:courseid'] = 'ID kurzu, ke kterému soubor patří';
$string['privacy:metadata:files:externalpurpose'] = 'Aby bylo možné provést integraci se službou Ally, je potřeba vyměnit s ní soubory.';
$string['privacy:metadata:files:filecontents'] = 'Skutečný obsah souboru byl odeslán do služby Ally za účelem vyhodnocení z hlediska přístupnosti.';
$string['privacy:metadata:files:mimetype'] = 'Typ MIME souboru, například text/plain, image/jpeg atd.';
$string['privacy:metadata:files:pathnamehash'] = 'Hash hodnota názvu cesty k souboru za účelem jedinečné identifikace';
$string['privacy:metadata:files:timemodified'] = 'Čas poslední úpravy pole';
$string['cachedef_annotationmaps'] = 'Ukládat data anotací pro kurzy';
$string['cachedef_fileinusecache'] = 'Spojit soubory v mezipaměti použití';
$string['cachedef_pluginfilesinhtml'] = 'Spojit soubory v mezipaměti HTML';
$string['cachedef_request'] = 'Vyrovnávací paměť požadavků filtru služby Ally';
$string['pushfilessummary'] = 'Souhrn aktualizací souboru služby Ally';
$string['pushfilessummary:explanation'] = 'Souhrn aktualizací souborů odeslaných do služby Ally';
$string['section'] = 'Sekce {$a}';
$string['lessonanswertitle'] = 'Odpověď pro lekci "{$a}"';
$string['lessonresponsetitle'] = 'Odpověď pro lekci {$a}';
$string['logs'] = 'Protokoly služby Ally';
$string['logrange'] = 'Rozsah protokolu';
$string['loglevel:none'] = 'Bez určení';
$string['loglevel:light'] = 'Mírný';
$string['loglevel:medium'] = 'Střední';
$string['loglevel:all'] = 'Všechny';
$string['logcleanuptask'] = 'Úloha čištění protokolu služby Ally';
$string['loglifetimedays'] = 'Uchovávat protokoly tento počet dnů';
$string['loglifetimedaysdesc'] = 'Protokoly Ally se budou uchovávat po tento počet dnů. Když nastavíte 0, protokoly nebudou odstraňovány nikdy. Naplánovaná úloha je (ve výchozím nastavení) nastavena na denní spouštění a odstraní záznamy protokolů, které jsou starší než tento počet dní.';
$string['logger:filtersetupdebugger'] = 'Protokol nastavení filtru služby Ally';
$string['logger:pushtoallysuccess'] = 'Úspěšné posunutí do koncového bodu služby Ally';
$string['logger:pushtoallyfail'] = 'Neúspěšné posunutí do koncového bodu služby Ally';
$string['logger:pushfilesuccess'] = 'Úspěšné posunutí souboru nebo souborů do koncového bodu služby Ally';
$string['logger:pushfileliveskip'] = 'Selhání posunutí živého souboru';
$string['logger:pushfileliveskip_exp'] = 'Posunutí živého souboru nebo souborů bylo přeskočeno kvůli komunikačním problémům. Posunutí živých souborů bude obnoveno, až proběhne úspěšně úloha aktualizace souborů. Zkontrolujte konfiguraci.';
$string['logger:pushfileserror'] = 'Neúspěšné posunutí do koncového bodu služby Ally';
$string['logger:pushfileserror_exp'] = 'Chyby související s posunem aktualizací obsahu do služeb Ally';
$string['logger:pushcontentsuccess'] = 'Úspěšné posunutí obsahu do koncového bodu služby Ally';
$string['logger:pushcontentliveskip'] = 'Selhání posunutí živého obsahu';
$string['logger:pushcontentliveskip_exp'] = 'Posunutí živého obsahu bylo přeskočeno kvůli komunikačním problémům. Posunutí živého obsahu bude obnoveno, až proběhne úspěšně úloha aktualizace obsahu. Zkontrolujte konfiguraci.';
$string['logger:pushcontentserror'] = 'Neúspěšné posunutí do koncového bodu služby Ally';
$string['logger:pushcontentserror_exp'] = 'Chyby související s posunem aktualizací obsahu do služeb Ally';
$string['logger:addingconenttoqueue'] = 'Přidávání obsahu do fronty pro push';
$string['logger:annotationmoderror'] = 'Vytvoření poznámky k obsahu modulu služby Ally se nezdařilo.';
$string['logger:annotationmoderror_exp'] = 'Modul nebyl správně identifikován.';
$string['logger:failedtogetcoursesectionname'] = 'Nepodařilo se získat název sekce kurzu';
$string['logger:moduleidresolutionfailure'] = 'Nepodařilo se vyřešit ID modulu';
$string['logger:cmidresolutionfailure'] = 'Nepodařilo se vyřešit ID modulu kurzu';
$string['logger:cmvisibilityresolutionfailure'] = 'Nepodařilo se vyřešit viditelnost modulu kurzu';
$string['courseupdatestask'] = 'Posunout události kurzu do služby Ally';
$string['logger:pushcoursesuccess'] = 'Úspěšné posunutí události nebo událostí kurzu do koncového bodu služby Ally';
$string['logger:pushcourseliveskip'] = 'Selhání posunutí události živého kurzu';
$string['logger:pushcourseerror'] = 'Selhání posunutí události živého kurzu';
$string['logger:pushcourseliveskip_exp'] = 'Posunutí události nebo událostí živého kurzu bylo přeskočeno kvůli komunikačním problémům. Posunutí událostí živého kurzu bude obnoveno, až proběhne úspěšně úloha aktualizace událostí živého kurzu. Zkontrolujte konfiguraci.';
$string['logger:pushcourseserror'] = 'Neúspěšné posunutí do koncového bodu služby Ally';
$string['logger:pushcourseserror_exp'] = 'Chyby související s posunem aktualizací kurzu do služeb Ally';
$string['logger:addingcourseevttoqueue'] = 'Přidávání události kurzu do fronty pro push';
$string['logger:cmiderraticpremoddelete'] = 'ID modulu kurzu má problémy s předběžným odstraněním.';
$string['logger:cmiderraticpremoddelete_exp'] = 'Modul nebyl správně identifikován. Buď neexistuje v důsledku odstranění sekce, nebo existuje jiný faktor, který aktivoval hák odstranění a nebyl nalezen.';
$string['logger:servicefailure'] = 'Používání služby se nezdařilo.';
$string['logger:servicefailure_exp'] = '<br>Třída: {$a->class}<br>Parametry: {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'Přiřazení pravomoci archetypu učitele k roli ally_webservice se nezdařilo.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Pravomoc: {$a->cap}<br>Oprávnění: {$a->permission}';
$string['deferredcourseevents'] = 'Odesílat odložené události kurzu';
$string['deferredcourseeventsdesc'] = 'Povolte odesílání uložených událostí kurzu, které se nahromadily během selhání komunikace s Ally';
