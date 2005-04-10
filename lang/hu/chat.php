<?PHP // $Id$ 
      // chat.php - created with Moodle 1.4 (2004083100)


$string['beep'] = 'csöngetés';
$string['chatintro'] = 'Bevezetõ szöveg';
$string['chatname'] = 'A csevegõszoba neve';
$string['chatreport'] = 'Csevegések';
$string['chattime'] = 'A következõ csevegési idõ';
$string['configmethod'] = 'Szokásos csevegési módszernél a csevegõk rendszeresen rákapcsolódnak a szerverre frissítésekért. Beállításra nincs szükség és mindenhol mûködik, de sok csevegõ esetén megterhelheti a szervert. Szerverdémon használata esetén megköveteli a Unix-héjhoz való hozzáférést, de gyors és méretezhetõ csevegési környezetet ad.';
$string['configoldping'] = 'Mennyi ideig tartó hallgatás után kell egy felhasználót kilépettnek tekinteni (másodpercben)?
Ez csak egy felsõ határ, mert a lekapcsolódás gyorsan érzékelhetõ. Alacsonyabb értékek jobban megterhelik a szervert. Ha a szokásos módszert használja, <strong>soha</strong> ne állítsa ezt az értéket alacsonyabbra, mint 2 * chat_refresh_room.';
$string['configrefreshroom'] = 'Milyen gyakran legyen frissítve a csevegõszoba (másodpercben)? Alacsony értékre állítva a csevegõszoba gyorsabbnak látszik, azonban nagyobb terhelést jelenthet a szervernek, ha egyszerre sokan csevegnek';
$string['configrefreshuserlist'] = 'Milyen gyakran legyen frissítve a felhasználók listája? (mp-ben)';
$string['configserverhost'] = 'A szerverdémont tartalmazó számítógép gazdaneve';
$string['configserverip'] = 'A fenti gazdanévnek megfelelõ IP-cím';
$string['configservermax'] = 'Csevegõk megengedett max. száma';
$string['configserverport'] = 'A szerveren a démonnal használandó port';
$string['currentchats'] = 'Zajló csevegések';
$string['currentusers'] = 'Aktuális felhasználók';
$string['deletesession'] = 'Csevegés törlése';
$string['deletesessionsure'] = 'Biztosan törölni akarja ezt a csevegést?';
$string['donotusechattime'] = 'Ne jelenjen meg a csevegések ideje';
$string['enterchat'] = 'Kattintson ide a csevegésbe való bekapcsolódáshoz';
$string['errornousers'] = 'Nem található felhasználó!';
$string['explaingeneralconfig'] = 'Ezek a beállítások <strong>mindig</strong> érvényesek';
$string['explainmethoddaemon'] = 'Ezek a beállítások <strong>csak</strong> akkor számítanak, ha a chat_method számára \"Csevegõ szerverdémonnal\"-t választott';
$string['explainmethodnormal'] = 'Ezek a beállítások <strong>csak</strong> akkor számítanak, ha a chat_method számára \"Szokásos módszer\"-t választott';
$string['generalconfig'] = 'Általános beállítás';
$string['helpchatting'] = 'Csevegés súgója';
$string['idle'] = 'Nem zajlik csevegés';
$string['messagebeepseveryone'] = '$a mindenkit csönget!';
$string['messagebeepsyou'] = '$a most csöngetett Önnek!';
$string['messageenter'] = '$a most lépett be';
$string['messageexit'] = '$a most távozott';
$string['messages'] = 'Üzenetek';
$string['methoddaemon'] = 'Csevegõ szerverdémon';
$string['methodnormal'] = 'Szokásos módszer';
$string['modulename'] = 'Csevegés';
$string['modulenameplural'] = 'Csevegések';
$string['neverdeletemessages'] = 'Az üzenetek soha nem törlõdjenek';
$string['nextsession'] = 'A következõ csevegés';
$string['noguests'] = 'A csevegésbe vendégek nem kapcsolódhatnak be';
$string['nomessages'] = 'Még nincs üzenet';
$string['repeatdaily'] = 'Minden nap ugyanakkor';
$string['repeatnone'] = 'Nincs ismétlés - csak a megadott idõpont megjelenítése';
$string['repeattimes'] = 'Csevegések ismétlése';
$string['repeatweekly'] = 'Minden héten ugyanakkor';
$string['savemessages'] = 'Korábbi csevegések mentése';
$string['seesession'] = 'A csevegés megtekintése';
$string['sessions'] = 'Csevegések';
$string['strftimemessage'] = '%%H:%%M';
$string['studentseereports'] = 'Korábbi csevegések megtekintése mindenkinek';
$string['viewreport'] = 'Korábbi csevegések megtekintése';

?>
