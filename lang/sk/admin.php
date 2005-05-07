<?PHP // $Id$ 
      // admin.php - created with Moodle 1.5 ALPHA (2005043000)


$string['adminseesallevents'] = 'Administrátori mô¾u prezera» v¹etky udalosti';
$string['adminseesownevents'] = 'Administrátori majú rovnaké práva ako ostatní u¾ívatelia';
$string['blockinstances'] = 'Uká¾ky';
$string['blockmultiple'] = 'Viacnásobný';
$string['cachetext'] = 'Doba existencie textovej vyrovnávacej pamäte';
$string['calendarsettings'] = 'Kalendár';
$string['change'] = 'zmeni»';
$string['configallowunenroll'] = 'Ak je toto nastavené na \'Áno\', potom sa mô¾u ¹tudenti sami kedykoµvek odhlási» z kurzov. V opaènom prípade im to nie je dovolené a celý proces prihlasovania sa do kurzov bude kontrolovaný len uèiteµmi a administrátormi.';
$string['configallusersaresitestudents'] = 'Mali by by» v¹etci u¾ívatelia pova¾ovaní za ¹tudentov vzhµadom k aktivitám, ktoré sú im dostupné  na hlavnej stránke systému? Ak je Va¹a odpoveï \'Áno\', potom ka¾dý autorizovaný u¾ívateµ sa mô¾e týchto aktivít zúèastni» ako ¹tudent. Ak je Va¹a odpoveï \'Nie\', potom len tí u¾ívatelia, ktorí sú u¾ úèastníkmi aspoò jedného kurzu, sa mô¾u zúèastni» týchto aktivít. Ako uèitelia týchto aktivít mô¾u vystupova» len administrátori a ¹peciálne na to vymenovaní uèitelia. ';
$string['configautologinguests'] = 'Mali by by» náv¹tevníci automaticky prihlasovaní ako hostia, ak vstúpia do kurzov s hos»ovským prístupom?';
$string['configcachetext'] = 'Toto nastavenie mô¾e zrýchli» prevádzku systému predov¹etkým pre tie stránky, ktoré sú obsahovo rozsiahlej¹ie alebo pou¾ívajú textové filtre. Kópie textov sa tu budú uchováva» v pôvodnej forme poèas vopred stanoveného èasového rozpätia. Ak sa tu nastavia nízke hodnoty parametrov, mô¾e to spomali» v¹etky èinnosti, ale ak sa tu nastavia pomerne vysoké hodnoty parametrov, bude obnova textov (napr. pri pridaní nových linkov) trva» veµmi dlho.';
$string['configclamactlikevirus'] = 'Pova¾ova» súbory za napadnuté vírusom';
$string['configclamdonothing'] = 'Pova¾ova» súbory za normálne';
$string['configclamfailureonupload'] = 'Ak ste nastavili clam, aby skenoval naèítavané súbory, ale nie je správne nastavené alebo z neznámych dôvodov nereaguje tak, ako má, ako sa má systém správa»? Ak si vyberiete \'Pova¾ova» súbory za napadnuté vírusom\', budú súbory presunuté do karanténej oblasti alebo vymazané. Ak si vyberiete \'Pova¾ova» súbory za normálne\', budú súbory presunuté na to miesto, ktoré si Vy urèíte bez problémov. Samozrejme, administrátori budú upozornení, ak clam nebude fungova». Ak si vyberiete \'Pova¾ova» súbory za napadnuté vírusom\' a z neznámych dôvodov clam nebude reagova» správne (väè¹inou je nesprávne nastavená cesta ku clam), v¹etky naèítané súbory budú presunuté do zadanej karanténej oblasti alebo vymazané. Buïte opatrný pri tomto natavení.';
$string['configcountry'] = 'Ak si tu vyberiete krajinu, tak bude táto krajina nastavá aj pre nové u¾ívateµské kontá. Ak chcete, aby si u¾ívatelia sami vybrali krajinu, nechajte ju tu nenastavenú.';
$string['configdbsessions'] = 'Ak povolené, toto nastavenie bude pou¾íva» databázu na uchovanie informácií o aktuálnych sedeniach (lekciách). Toto je výhodné pou¾i» najmä pri obsahovo rozsiahlej¹ích stránkach zalo¾ených na zoskupení serverov. Pre väè¹inu stránok by táto voµba mala zosta» neaktívna, aby sa namiesto databázy pou¾íval disk servera. Prosím berte na vedomie, ¾e ak teraz zmeníte toto nastavenie, v¹etci u¾ívatelia (vrátane vás) budú zo systému odhlásení.';
$string['configdebug'] = 'Ak toto zapnete, PHP zvý¹i oznamovanie chýb tak, ¾e bude uvádzaných viac varovaní. Toto je u¾itoèné len pre tvorcov-programátorov.';
$string['configdeleteunconfirmed'] = 'Ak pou¾ívate emailovú autorizáciu, toto je èasové rozpätie, poèas ktorého bude odpoveï akceptovaná u¾ívateµmi. Po tomto období budú staré nepou¾ívané kontá vymazané.    ';
$string['configdigestmailtime'] = '¥uïom, ktorí sa rozhodnú pre zasielanie emailov v ¹truktúrovanej forme, bude ka¾dý deò prichádza» email struène informujúci o najnov¹ích udalostiach v kurze. Toto nastavenie urèuje tú èas» dòa, kedy bude tento email zasielaný u¾ívateµom.  ';
$string['configdisplayloginfailures'] = 'Toto zobrazí vybraným u¾ívateµom informácie o predchádzajúcich neúspe¹ných pokusoch o prihlásenie do kurzov. ';
$string['configenablerssfeeds'] = 'Tento prepínaè umo¾ní RSS väzbu z iných stránok. Aby ste videli v¹etky aktuálne zmeny, musíte aktivova» RSS väzbu aj v jednotlivých moduloch - choïte do Nastavení Moodle na Konfiguráciu administrátora.';
$string['configenablerssfeedsdisabled'] = 'Voµba nie je dostupná, preto¾e RSS väzba je deaktivovaná na celej stránke. Ak ju chcete aktivova», choïte do Nastavení premenných na Konfiguráciu administrátora.';
$string['configerrorlevel'] = 'Vyberte si mno¾stvo PHP varovaní, ktoré chcete ma» znázoròované. Normal je zvyèajne najlep¹ia mo¾nos».';
$string['configextendedusernamechars'] = 'Ak povolíte toto nastavenie, ¹tudenti mô¾u vo svojich u¾ívateµských menách pou¾íva» akékoµvek znaky (to v¹ak neovplyvní ich súèasné mená). ©tandardné nastavenie je \'Nesprávne\', ktoré obmedzuje pou¾ívané znaky v menách len na alfanumerické znaky.';
$string['configfilteruploadedfiles'] = 'Aktivovaním tejto voµby bude Moodle spracováva» v¹etky naèítané HTML a textové súbory s filtrami predtým ako sa zobrazia.';
$string['configforcelogin'] = 'Normálne mô¾e by» hlavná stránka s uvedeným zoznamom kurzov (nie konkrétnymi kurzami) prezeraná u¾ívateµmi bez toho, aby sa predtým prihlásili. Ak chcete, aby sa u¾ívatelia prihlásili predtým, ako èokoµvek urobia na stránke, potom by ste mali aktivova» toto nastavenie. ';
$string['configforceloginforprofiles'] = 'Aktivovaním tohto nastavenia sa ka¾dý reálny u¾ívateµ (nie hos») bude musie» najskôr prihlási», ak si chce prezera» profily u¾ívateµov. Táto voµba je implicitne deaktivovaná (\'Nesprávne\'), aby si perspektívni ¹tudenti mohli preèíta» informácie o uèiteµoch jednotlivých kurzov. Znamená to v¹ak tie¾, ¾e webové vyhµadávaèe ich doká¾u vyhµada».';
$string['configframename'] = 'Ak pou¾ívate Moodle vo web-frame (rámci), potom tu uveïte názov tohto rámca. Inak nechajte tento názov ako (\'_top\').';
$string['configfullnamedisplay'] = 'Toto definuje, ako sa ukazujú mená, ak sú zobrazované v plnej forme. Pre väè¹inu jednojazyèných stránok je najlep¹ie nastavenie toto: \'Meno a Priezvisko\', ale mô¾ete napríklad aj skry» v¹etky priezviská alebo to necha» na samotný jazykový balík, nech sa rozhodne (niektoré jazyky majú rozdielne zvyklosti). ';
$string['configgdversion'] = 'Oznaète verziu GD, ktorá je nain¹talovaná. Nastavená verzia je tá, ktorá bola automaticky detekovaná. Nemeòte to, iba ak skutoène viete èo robíte!';
$string['confightmleditor'] = 'Vyberte si, èi chcete povoli» pou¾ívanie zakomponovaného HTML textového editora alebo nie. Aj keï si vyberiete povoli», tento editor sa objaví iba keï u¾ívateµ pou¾íva kompatibilný prehliadaè (IE 5.5 alebo nov¹ie verzie). U¾ívatelia sa ale mô¾u rozhodnú» nepou¾íva» tento prehliadaè.';
$string['configidnumber'] = '©pecifické nastavenia èi (a)U¾ívatelia nebudú po¾iadaní o zadanie ID èísla vôbec, (b)U¾ívatelia budú po¾iadaní o zadanie ID èísla, ale nemusia ho vyplni», (c)U¾ívatelia budú po¾iadaní o zadanie ID èísla a musia ho vyplni». Ak bude zadané ID èíslo uèívateµa, bude zobrazované v profile.';
$string['configintro'] = 'Na tejto strane mô¾ete ¹pecifikova» rôzne konfiguraèné premenné, ktoré pomô¾u Moodle správne spolupracova»  s Va¹im serverom. Veµmi sa s tým netrápte - východiskové nastavenia zvyèajne pracujú správne a v¾dy sa mô¾ete k tejto stránke vráti» a tieto nastavenia zmeni».';
$string['configintroadmin'] = 'Na tejto stránke by ste mali nakonfigurova» Va¹e hlavné konto pre administrátora, ktorý má plnú kontrolu nad celou stránkou. Dbajte na to, aby mal bezpeèné u¾ívateµské meno, heslo a tie¾ platnú e-mailovú adresu. Neskôr mô¾ete vytvori» viac administrátorských úètov.';
$string['configintrosite'] = 'Táto stránka vám umo¾òuje konfigurova» hlavnú stránku a meno tejto stránky. Neskôr to mô¾ete kedykoµvek zmeni» cez link \'Site Settings\' na domovskej stránke. ';
$string['configlang'] = 'Vyberte si východzí jazyk pre celú stránku. U¾ívatelia mô¾u neskôr toto nastavenie prepísa».';
$string['configlangdir'] = 'Pri väè¹ine jazykov sa pí¹e zµava doprava, ale pri niektorých, napr.arabèina a hebrejèina, sa pí¹e sprava doµava.';
$string['configlanglist'] = 'Nechajte túto voµbu prázdnu, ak chcete, aby si u¾ívatelia mohli vybra» µubovoµný jazyk z Va¹ej verzie Moodle. Tento zoznam mô¾ete skráti», ak uvediete zoznam jazykov, oddelených èiarkou, napr.

sk,cz,en,es_es,fr,it.';
$string['configlangmenu'] = 'Vyberte si, èi chcete zobrazi» menu pre voµbu jazyka na www stránkach Moodle (domovská stránka, autorizaèná stránka a pod.). To neovplyvní u¾ívateµove mo¾nosti nastavenia preferovaného jazyka v svojom vlastnom profile.';
$string['configlocale'] = 'Vyberte si miestne jazykové nastavenie - toto ovplyvní formát a jazyk údajov. Tieto miestne údaje musíte ma» nain¹talované vo Va¹om operaènom systéme (napríklad en_US alebo es_ES). Ak neviete, èo si vybra», nechajte to prázdne.';
$string['configloginhttps'] = 'Aktivovanie tejto voµby bude znamena», ¾e Moodle bude pou¾íva» bezpeèné https spojenie len pri autorizaènej stránke (pri prihlasovaní so systému)uvedením bezpeèného prihlasovacieho mena. Následne sa vráti k normálnemu http protokolu URL pre v¹eobecnú rýchlos». UPOZORNENIE: toto nastavenie vy¾aduje, aby https protokol bol aktivovaný na web serveri - ak nie je, MALI BY STE ZAMKNÚ« VA©U STRÁNKU.';
$string['configloglifetime'] = 'Tu ¹pecifikujte då¾ku èasového intervalu, poèas ktorého chcete zachova» záznamy o u¾ívateµských aktivitách. Záznamy, ktoré sú star¹ie, sa automaticky vyma¾ú. Je dobré uchováva» si záznamy tak dlho, ako je to mo¾né, ale ak máte veµmi zaneprázdnený server a máte problémy s jeho rýchlos»ou, potom si vyberte krat¹í èas pre uchovávanie záznamov.';
$string['configlongtimenosee'] = 'Ak sa ¹tudenti dlhý èas neprihlásia, sú automaticky vyradení z kurzov. Tento parameter stanovuje tento èasový limit.';
$string['configmaxbytes'] = 'Táto voµba ¹pecifikuje maximálnu veµkos» naèítavaných súborov na celej stránke. Toto nastavenie je limitované PHP nastavením upload_max_filesize a nastavením Apache LimitRequestBody. Nastavenie maxbytes ohranièuje rozsah veµkostí, z ktorých si mô¾ete vybra» v ka¾dej úrovni kurzu alebo modulu.';
$string['configmaxeditingtime'] = 'Toto urèuje èas, ktorý majú µudia na upravovanie príspevkov do fóra, spätnej väzby do spisu atï. Zvyèajne je to 30 minút.';
$string['configmessaging'] = 'Má by» aktivovaný systém posielania správ medzi u¾ívateµmi stránky?';
$string['confignoreplyaddress'] = 'Emaily sa niekedy posielajú v mene pou¾ívateµa (napr. prispievanie do fór). Emailová adresa, ktorú tu ¹pecifikujete, bude pou¾ívaná ako adresa \'Od koho\' v prípade, ak prijímatelia nie sú schopní priamo odpoveda» pou¾ívateµovi (napr. keï si u¾ívateµ vyberie zachovanie súkromnej adresy).';
$string['confignotifyloginfailures'] = 'Ak sa uchovávajú záznamy o neúspe¹ných pokusoch o prihlásenie do systému, mô¾u by» tieto odoslané emailom. Kto by si mal prezera» tieto oznámenia?';
$string['confignotifyloginthreshold'] = 'Ak sú aktíne oznámenia o neúspe¹ných pokusoch o prihlásenie do systému, koµko takýchto prihlásení od jedného u¾ívateµa alebo jednej IP adresy sa má zobrazova» v oznámeniach?';
$string['configopentogoogle'] = 'Ak aktivujete toto nastavenie, potom Google bude ma» oprávnenie vstupu do Va¹ej stránky ako Hos». Naviac, µudia prichádzajúci na Va¹u stránku z prostredia vyhµadávaèa Google, budú automaticky prihlasovaní ako Hostia. Majte prosím na vedomí, ¾e takýto prístup mô¾e by» realizovaný len do tých kurzov, ktoré povoµujú vstup hos»ov. ';
$string['configpathtoclam'] = 'Cesta do Clam AV. Pravdepodobne nieèo ako usr/bin/clamscan alebo /usr/bin/clamdscan. Túto cestu potrebujete, aby Clam AV fungoval správne.';
$string['configproxyhost'] = 'Ak tento <B>server</B> potrebuje pou¾íva» server proxy (napríklad bránu firewall) pri prístupe na internet, tak tu uveïte hostiteµské meno a port. Ak nie, nechajte to prázdne.';
$string['configquarantinedir'] = 'Ak chcete, aby Clam AV presunul napadnuté súbory do karanténeho adresára, napí¹te ho sem. Musí by» ale zobraziteµný web serverom. Ak túto voµbu nevyplníte alebo ak zadáte neexistuje alebo sa nedá zobrazi», budú napadnuté súbory vymazané. Nepridávajte koncové lomítko.';
$string['configrunclamonupload'] = 'Spusti» Clam AV pri naèítavaní súboru? K tomu budete potrebova» nastavi» správnu cestu v pathtoclam. (Clam AV je voµne ¹íriteµný vírusový skener, ktorý si mô¾ete stiahnu» na http://www.clamav.net/)';
$string['configsecureforms'] = 'Moodle mô¾e pou¾i» dodatoèné bezpeènostné opatrenia pri akceptovaní vstupov z web formulárov. Ak to umo¾níte, potom sa bude overova» premenná HTTP_REFERER, ktorú po¹le browser a porovná sa s aktuálnou adresou formulára. Toto mô¾e spôsobi» (vo veµmi zriedkavých prípadoch) problémy, napr. ak je u¾ívateµ za firewallom, ktorý je nakonfigurovaný tak, ¾e odstráni premennú HTTP_REFERER. Vtedy sa mô¾e sta», ¾e formulár vám \'stvrdne\'. Ak sa na to u¾ívatelia s»a¾ujú, mô¾ete deaktivova» toto nastavenie. V tomto prípade sa v¹ak vystavujete väè¹ím útokom zvonku (brute force password attacks).
Ak si nie ste istý, ponechajte túto voµbu nastavenú na \'Yes\'. ';
$string['configsessioncookie'] = 'Toto nastavenie upravuje meno cookie pou¾ívaného v Moodle sedeniach(lekciách). Táto mo¾nos» je voliteµná a u¾itoèná v tom prípade, ak je spustená viac ako jedna kópia Moodle v rámci tej istej www stránky (aby ste sa vyhli popleteniu cookies).';
$string['configsessiontimeout'] = 'Ak sú µudia pripojení na túto stránku dlho neèinní (bez s»ahovania stránok), sú automaticky odpojení. Táto premenná urèuje, aký dlhý by mal by» ten èasový interval neèinnosti.';
$string['configshowsiteparticipantslist'] = 'V¹etci títo u¾ívatelia stránky a uèitelia budú v zozname úèastníkov stránky. Komu by malo by» povolené prezeranie tohto zoznamu?';
$string['configsitepolicy'] = 'Ak máte definovanú podmienku, ¾e v¹etci u¾ívatelia musia vidie» a súhlasi» pred pou¾ívaním tejto stránky, potom sem vlo¾te danú URL adresu; v opaènom prípade túto voµbu nevypåòajte. URL adresa mô¾e by» nastavená v ktoromkoµvek mieste - jedno z najvhodnej¹ích miest by bol súbor v súboroch stránky, napr.http://yoursite/file.php/1/policy.html ';
$string['configslasharguments'] = 'Súbory (obrázky atï.) sú prená¹ané prostredníctvom skriptu pou¾ívajúceho znaèku \'lomítko\' (druhá mo¾nos» vo výbere). Táto metóda umo¾òuje, aby boli súbory µah¹ie zachytené na webových prehliadaèoch, proxy serveroch atï. Nane¹»astie, niektoré PHP servery  túto metódu nepodporujú. Ak máte problémy pri zobrazovaní stiahnutých súborov alebo obrázkov (napr. obrázky u¾ívateµa), nastavte tu prvú mo¾nos» vo výbere.';
$string['configsmtphosts'] = 'Udajte plný názov jedného alebo viacerých SMTP serverov, ktoré má Moodle pou¾íva» pri posielaní po¹ty (napríklad \'mail.a.com\' alebo \'mail.a.com;mail.b.com\'). Ak to neuvediete, Moodle pou¾ije postup posielania po¹ty podµa východzích nastavení.';
$string['configsmtpuser'] = 'Ak ste hore uviedli SMTP server a ten vy¾aduje overovanie, uveïte tu u¾ívateµské meno a heslo.';
$string['configteacherassignteachers'] = 'Mô¾u obyèajní uèitelia prideli» iných uèiteµov do toho kurzu, v ktorom vyuèujú? Ak \'Nie\', potom iba tvorcovia kurzov a administrátori mô¾u prideµova» uèiteµov ku kurzom.';
$string['configtimezone'] = 'Tu mô¾ete nastavi» východzie èasové pásmo. Toto je len východzie èasové pásmo pre zobrazovanie dátumov - ka¾dý u¾ívateµ toto mô¾e prepísa» nastavením svojho preferovaného zobrazovania dátumu v profile u¾ívateµa. \'Èas servera\' na Moodle bude implicitne nastavený podµa operaèného systému servera ale \'Èas servera\' v profile u¾ívateµa bude nastavený podµa nastavenia èasového pásma.';
$string['configunzip'] = 'Uveïte umiestnenie vá¹ho unzip programu (iba Unix). Je to potrebné pre rozbalenie zozipovaných archívov na serveri. Ak túto voµbu nevyplníte, Moodle bude pou¾íva» vlastný interný postup.';
$string['configuration'] = 'Konfigurácia';
$string['configvariables'] = 'Nastavi» premenné';
$string['configwarning'] = 'Postupujte veµmi opatrne pri zmenách týchto nastavení - nesprávne hodnoty mô¾u spôsobova» problémy.';
$string['confirmation'] = 'Potvrdenie';
$string['cronwarning'] = '<a href=\"cron.php\">Údr¾ba cron.php skriptu nebola prevedená najmenej 24 hodín.<br /><a href=\"../doc/?frame=install.html&#8834;=cron\">Táto dokumentácia o in¹talácii</a> vysvetµuje, ako tento proces mô¾ete zautomatizova».';
$string['filteruploadedfiles'] = 'Filtrova» prená¹ané súbory';
$string['helpadminseesall'] = 'Mô¾u si administrátori prezera» v¹etky udalosti kalendára, alebo len tie, ktoré sa ich týkajú? ';
$string['helpcalendarsettings'] = 'Konfigurova» viaceré apekty kalendára týkajúce sa dátumu/èasu v prostredí Moodle';
$string['helpstartofweek'] = 'Ktorým dòom v tý¾dni by sa mal zaèína» tý¾deò v kalendári?';
$string['helpupcominglookahead'] = 'Koµko dní dopredu sa má implicitne zobrazova» v kalendári pri prezeraní nadchádzajúcich udalostí? ';
$string['helpupcomingmaxevents'] = 'Koµko nadchádzajúcich udalostí (maximum) sa má implicitne zobrazova» u¾ívateµom?';
$string['helpweekenddays'] = 'Ktoré dni v tý¾dni sú pova¾ované za \"víkend\", t.j. sú oznaèené inou farbou?';
$string['nodstpresetsexist'] = 'Podpora DST je deaktivovaná pre v¹etkých u¾ívateµov, preto¾e nie sú definované ¾iadne DST prednastavenia. Ak chcete nejaké definova», pou¾ite prosím tlaèidlo dolu.';
$string['therewereerrors'] = 'Vo Va¹ich údajoch boli nájdené nejaké chyby';
$string['upgradelogs'] = 'Va¹e staré záznamy musia by» aktualizované, aby bol systém plne funkèný.<a href=\"$a\">Viac informácií na</a>';
$string['upgradelogsinfo'] = 'Nedávno boli prevedené nejaké zmeny týkajúce sa spôsobu, ako sú záznamy uchovávané. Aby ste si mohli prezera» v¹etky Va¹e staré záznamy, musíte ich aktualizova». Toto mô¾e trva» dos» dlho (napr. niekoµko hodín - to zále¾í od Va¹ej stránky) a mô¾e to dos» za»a¾i» samotnú databázu u obsiahlej¹ích stránok. Ak tento proces raz zaènete, musíte ho aj dokonèi» (nechajte otvorené okno v prehliadaèi). Neobávjte sa, Va¹a stránka bude pre ostatných u¾ívateµov fungova» bez problémov, pokým Vy budete aktualizova» záznamy. <br /><br />Chcete aktualizova» va¹e záznamy teraz?';
$string['upgradesure'] = '<p>Va¹e súbory v Moodle boli zmenené a Vy sa práve chystáte upgradova» Vá¹ server na túto verziu:</p><p><b>$a</b></p>
<p>Ak to teraz zaènete, u¾ sa nemô¾ete vráti» spä».</p>
<p>Ste si istý, ¾e chcete upgradova» tento server na túto verziu?</p>';
$string['upgradinglogs'] = 'Záznamy sa aktualizujú';

?>
