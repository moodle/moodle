<?PHP // $Id$ 
      // chat.php - created with Moodle 1.6 development (2005060201)


$string['beep'] = 'pípnu»';
$string['chatintro'] = 'Úvodný text';
$string['chatname'] = 'Názov tejto miestnosti ';
$string['chatreport'] = 'Chatovanie';
$string['chattime'] = 'Najbli¾¹ie chatovanie';
$string['configmethod'] = 'Pri normálnom chate sa aktualizujú klienti pri spojení so serverom. Táto metóda si nevy¾aduje ¾iadnu konfiguráciu a funguje v¹ade, ale mô¾e spôsobi» dlhé èakanie na serveri. Pou¾ívanie démona na serveri si vy¾aduje prístup do Unixu shellu, èo v¹ak zrýchli prostredie pre chatovanie.';
$string['configoldping'] = 'Po akom èase mô¾eme pova¾ova», ¾e sa pou¾ívateµ odhlásil z chatu (v sekundách)? Toto je len horný limit, preto¾e odhlásenia z chatu sú detekované veµmi rýchlo. Ni¾¹ie hodnoty budú viac za»a¾ova» Vá¹ server. Ak pou¾ívate normálnu metódu, <strong>nikdy</strong> nenastavujte túto hodnotu na menej ako 2* chat_refresh_room.';
$string['configrefreshroom'] = 'Po akom èase sa má chatovacia miestnos» obnovova»? (v sekundách). Nastavenie príli¹ krátkeho èasu sposobí èasté obnovovanie a vy¹¹ie nároky na server, ale chat sa javí ako rýchly.';
$string['configrefreshuserlist'] = 'Ako èasto sa má obnovova» zoznam pou¾ívateµov? (v sekundách)';
$string['configserverhost'] = 'Hos»ovské meno poèítaèa, kde je umiestnený serverový démon';
$string['configserverip'] = 'Èíselná IP adresa, ktorá platí pre vy¹¹ie uvedené hos»ovské meno';
$string['configservermax'] = 'Maximálny poèet povolených klientov';
$string['configserverport'] = 'Port, ktorý sa na serveri pou¾íva pre démona';
$string['currentchats'] = 'Prebieha chatovanie';
$string['currentusers'] = 'Prihlásení pou¾ívatelia do chatu ';
$string['deletesession'] = 'Odstráni» toto chatovanie';
$string['deletesessionsure'] = 'Ste si istý, ¾e chcete odstráni» toto chatovanie?';
$string['donotusechattime'] = 'Nezverejòova» èas chatovania';
$string['enterchat'] = 'Kliknite sem, ak sa chcete zapoji» do chatovania';
$string['errornousers'] = 'Nemô¾em nájs» ¾iadnych pou¾ívateµov!';
$string['explaingeneralconfig'] = 'Tieto nastavenia pôsobia <strong>v¾dy</strong> ';
$string['explainmethoddaemon'] = 'Tieto nastavenia pôsobia <strong>iba</strong> vtedy, keï ste si vybrali chat_method \"Chat démon na serveri\"  ';
$string['explainmethodnormal'] = 'Tieto nastavenia pôsobia <strong>iba</strong> vtedy, keï ste si vybrali chat_method \"Normálna metóda\" ';
$string['generalconfig'] = 'V¹eobecná konfigurácia';
$string['helpchatting'] = 'Nápoveda k chatovaniu';
$string['idle'] = 'Neèinný/á';
$string['messagebeepseveryone'] = '$a ohlasuje v¹etkých!';
$string['messagebeepsyou'] = '$a Vás práve ohlásil!';
$string['messageenter'] = '$a práve vstúpil do tohto chatu';
$string['messageexit'] = '$a sa práve odhlásil z chatu';
$string['messages'] = 'Správy';
$string['methoddaemon'] = 'Chat démon ne serveri';
$string['methodnormal'] = 'Normálna metóda';
$string['modulename'] = 'Chat';
$string['modulenameplural'] = 'Chatovanie';
$string['neverdeletemessages'] = 'Nikdy neodstraòova» správy';
$string['nextsession'] = 'Najbli¾¹ie plánované chatovanie';
$string['noguests'] = 'Hostia nemô¾u vstúpi» do tohto chatu';
$string['nomessages'] = 'Zatiaµ ¾iadne správy';
$string['repeatdaily'] = 'V rovnaký èas ka¾dý deò';
$string['repeatnone'] = 'Bez opakovania - zverejni» len stanovený èas';
$string['repeattimes'] = 'Opakova» chatovanie';
$string['repeatweekly'] = 'V rovnaký èas ka¾dý tý¾deò';
$string['savemessages'] = 'Ulo¾i» prebehnuté chatovanie';
$string['seesession'] = 'Ukáza» toto chatovanie ';
$string['sessions'] = 'Chatovanie';
$string['strftimemessage'] = '%%H:%%M';
$string['studentseereports'] = 'Ka¾dý si mô¾e prezrie» prebehnuté chatovanie';
$string['viewreport'] = 'Ukáza» prebehnuté chatovanie';

?>
