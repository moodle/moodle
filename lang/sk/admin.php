<?PHP // $Id$ 
      // admin.php - created with Moodle 1.6 development (2005060201)


$string['adminseesallevents'] = 'Administrátori mô¾u prezera» v¹etky udalosti';
$string['adminseesownevents'] = 'Administrátori majú rovnaké práva ako ostatní pou¾ívatelia';
$string['blockinstances'] = 'Výskyty';
$string['blockmultiple'] = 'Viacnásobný';
$string['cachetext'] = 'Doba existencie textovej vyrovnávacej pamäte';
$string['calendarsettings'] = 'Kalendár';
$string['change'] = 'zmeni»';
$string['configallowcoursethemes'] = 'Keï zapnete túto voµbu, bude mo¾né nastavi» pre kurz vlastnú tému. Téma kurzu má najvy¹¹iu prioritu, zobrazí sa aj v prípade, keï bude nastavenie témy hlavnej stránky, pou¾ívateµa èi aktuálneho sedenia odli¹né.';
$string['configallowemailaddresses'] = 'Ak chcete obmedzi» v¹etky nové emailové adresy na urèité domény, uveïte ich tu, oddelené medzerami. V¹etky ostatné domény budú odmietnuté. Napr. <strong>vasaskola.sk inaskola.sk</strong>';
$string['configallowobjectembed'] = 'Ako ¹tandardne nastavené bezpeènostné opatrenie, normálni pou¾ívatelia nemô¾u do textov vklada» multimediálne prvky (napr. Flash) prostredníctvom EMBED a OBJECT tagov v ich HTML (hoci sa to dá bezpeène urobi» pou¾itím filtera multimediálnych pluginov). Ak si ¾eláte, aby boli tieto tagy povolené pre pou¾ívateµov, potom zapnite túto voµbu.  ';
$string['configallowunenroll'] = 'Ak je toto nastavené na \'Áno\', potom sa mô¾u ¹tudenti sami kedykoµvek odhlási» z kurzov. V opaènom prípade im to nie je dovolené a celý proces prihlasovania sa do kurzov bude kontrolovaný iba uèiteµmi a administrátormi.';
$string['configallowuserblockhiding'] = 'Chcete povoli» pou¾ívateµom skrytie/zobrazenie postranných blokov na v¹etkých týchto stránkach? Táto vlastnos» pou¾íva Javasript a Cookies pre ulo¾enie aktuálneho stavu pre ka¾dý blok a ovplyvní iba pou¾ívateµov pohµad.';
$string['configallowuserthemes'] = 'Keï zapnete túto voµbu, pou¾ívateµ si bude môc» nastavi» vlastné témy. Témy pou¾ívateµa majú vy¹¹iu prioritu, zobrazia sa aj v prípade, keï bude nastavenie témy hlavnej stránky iné (toto neplatí pre témy kurzu).';
$string['configallusersaresitestudents'] = 'Mali by by» v¹etci pou¾ívatelia pova¾ovaní za ¹tudentov vzhµadom k aktivitám, ktoré sú im dostupné  na hlavnej stránke systému? Ak je Va¹a odpoveï \'Áno\', potom ka¾dý autorizovaný pou¾ívateµ sa mô¾e týchto aktivít zúèastni» ako ¹tudent. Ak je Va¹a odpoveï \'Nie\', potom len tí pou¾ívatelia, ktorí sú u¾ úèastníkmi aspoò jedného kurzu, sa mô¾u zúèastni» týchto aktivít. Ako uèitelia týchto aktivít mô¾u vystupova» len administrátori a ¹peciálne na to vymenovaní uèitelia. ';
$string['configautologinguests'] = 'Mali by by» náv¹tevníci automaticky prihlasovaní ako hostia, ak vstúpia do kurzov s hos»ovským prístupom?';
$string['configcachetext'] = 'Toto nastavenie mô¾e zrýchli» prevádzku systému predov¹etkým pre tie stránky, ktoré sú obsahovo rozsiahlej¹ie alebo pou¾ívajú textové filtre. Kópie textov sa tu budú uchováva» v pôvodnej forme poèas vopred stanoveného èasového rozpätia. Ak sa tu nastavia nízke hodnoty parametrov, mô¾e to spomali» v¹etky èinnosti, ale ak sa tu nastavia pomerne vysoké hodnoty parametrov, bude obnova textov (napr. pri pridaní nových linkov) trva» veµmi dlho.';
$string['configclamactlikevirus'] = 'Pova¾ova» súbory za napadnuté vírusom';
$string['configclamdonothing'] = 'Pova¾ova» súbory za normálne';
$string['configclamfailureonupload'] = 'Ak ste nastavili clam, aby skenoval prená¹ané súbory, ale nie je správne nastavený alebo z neznámych dôvodov nereaguje tak, ako má, ako sa má systém správa»? Ak si vyberiete \'Pova¾ova» súbory za napadnuté vírusom\', budú súbory presunuté do karanténnej oblasti alebo vymazané. Ak si vyberiete \'Pova¾ova» súbory za normálne\', budú súbory presunuté na to miesto, ktoré si Vy urèíte bez problémov. Samozrejme, administrátori budú upozornení, ak clam nebude fungova». Ak si vyberiete \'Pova¾ova» súbory za napadnuté vírusom\' a z neznámych dôvodov clam nebude reagova» správne (väè¹inou je nesprávne nastavená cesta ku clam), v¹etky prená¹ané súbory budú presunuté do zadanej karanténnej oblasti alebo vymazané. Buïte opatrný pri tomto natavení.';
$string['configcountry'] = 'Ak si tu vyberiete krajinu, tak bude táto krajina nastavá aj pre nové pou¾ívateµské kontá. Ak chcete, aby si pou¾ívatelia sami vybrali krajinu, nenastavujte ju tu.';
$string['configdbsessions'] = 'Ak povolíte túto voµbu, toto nastavenie bude pou¾íva» databázu na uchovanie informácií o aktuálnych sedeniach (lekciách). Toto je výhodné pou¾i» najmä pri obsahovo rozsiahlej¹ích stránkach zalo¾ených na zoskupení serverov. Pre väè¹inu stránok by táto voµba mala zosta» neaktívna, aby sa namiesto databázy pou¾íval disk servera. Prosím berte na vedomie, ¾e ak teraz zmeníte toto nastavenie, v¹etci pou¾ívatelia (vrátane Vás) budú zo systému odhlásení.';
$string['configdebug'] = 'Ak zapnete túto voµbu, PHP zvý¹i oznamovanie chýb tak, ¾e bude uvádzaných viac varovaní. Toto je u¾itoèné len pre vývojových pracovníkov.';
$string['configdeleteunconfirmed'] = 'Ak pou¾ívate emailovú autorizáciu, toto je èasové rozpätie, poèas ktorého bude odpoveï akceptovaná pou¾ívateµmi. Po tomto období budú staré nepou¾ívané kontá vymazané.    ';
$string['configdenyemailaddresses'] = 'Ak chcete zakáza» emailové adresy z urèitých domén, uveïte ich tu, oddelené medzerami. V¹etky ostatné domény budú akceptované. Napr. <strong>atlas.sk szm.sk hotmail.com</strong>';
$string['configdigestmailtime'] = '¥uïom, ktorí sa rozhodnú pre zasielanie emailov v ¹truktúrovanej forme, bude ka¾dý deò prichádza» email struène informujúci o najnov¹ích udalostiach v kurze. Toto nastavenie urèuje tú èas» dòa, kedy bude tento email zasielaný pou¾ívateµom (odo¹le ho nasledujúci cron po ukonèení tejto hodiny).  ';
$string['configdisplayloginfailures'] = 'Toto zobrazí vybraným pou¾ívateµom informácie o predchádzajúcich neúspe¹ných pokusoch o prihlásenie.';
$string['configenablerssfeeds'] = 'Tento prepínaè umo¾ní RSS kanály z iných stránok. Aby ste videli v¹etky aktuálne zmeny, musíte aktivova» RSS kanály aj v jednotlivých moduloch - choïte do Nastavení Moodle v Konfigurácii administrátora.';
$string['configenablerssfeedsdisabled'] = 'Voµba nie je dostupná, preto¾e RSS kanály sú deaktivované na celej Stránke. Ak ich chcete aktivova», choïte do Nastavení premenných v Konfigurácii administrátora.';
$string['configerrorlevel'] = 'Vyberte si mno¾stvo PHP varovaní, ktoré chcete ma» zobrazované. Normal je zvyèajne najlep¹ia mo¾nos».';
$string['configextendedusernamechars'] = 'Ak povolíte toto nastavenie, ¹tudenti mô¾u vo svojich pou¾ívateµských menách pou¾íva» akékoµvek znaky (to v¹ak neovplyvní ich skutoèné mená). ©tandardné nastavenie je \'Nesprávne\', ktoré obmedzuje pou¾ívané znaky v menách len na alfanumerické znaky.';
$string['configfilterall'] = 'Filtrova» v¹etky re»azce, vrátane hlavièiek, titulov, navigaènej li¹ty a podobne. Toto je najviac u¾itoèné pri pou¾ívaní viacjazyèného filtera, inak spôsobuje iba mierne zvý¹enú zá»a¾ pri generovaní stránok.';
$string['configfilteruploadedfiles'] = 'Aktivovaním tejto voµby bude Moodle spracováva» v¹etky naèítané HTML a textové súbory s filtrami predtým, ako sa zobrazia.';
$string['configforcelogin'] = 'Normálne mô¾e by» hlavná stránka s uvedeným zoznamom kurzov (nie s konkrétnymi kurzami) prezeraná pou¾ívateµmi bez toho, aby sa predtým prihlásili. Ak chcete, aby sa pou¾ívatelia prihlásili predtým, ako èokoµvek urobia na stránke, potom by ste mali aktivova» toto nastavenie. ';
$string['configforceloginforprofiles'] = 'Aktivovaním tohto nastavenia sa ka¾dý reálny pou¾ívateµ (nie hos») bude musie» najskôr prihlási», ak si chce prezera» profily pou¾ívateµov. Táto voµba je ¹tandardne deaktivovaná (\'Nesprávne\'), aby si perspektívni ¹tudenti mohli preèíta» informácie o uèiteµoch jednotlivých kurzov. Znamená to v¹ak tie¾, ¾e webové vyhµadávaèe ich doká¾u vyhµada» a prezera».';
$string['configframename'] = 'Ak pou¾ívate Moodle vo web-frame (rámci), potom tu uveïte názov tohto rámca. Inak nechajte tento názov ako \'_top\'.';
$string['configfullnamedisplay'] = 'Toto definuje, ako sa zobrazujú mená, ak sú uvedené v plnej rozsahu. Pre väè¹inu jednojazyèných stránok je najlep¹ie nastavenie toto: \'Meno a Priezvisko\', ale mô¾ete napríklad aj skry» v¹etky priezviská alebo to necha» na samotný jazykový balík, nech sa rozhodne (niektoré jazyky majú ¹pecifické zvyklosti). ';
$string['configgdversion'] = 'Oznaète verziu GD, ktorá je nain¹talovaná. Nastavená verzia je tá, ktorá bola automaticky zistená. Nemeòte to, iba ak skutoène viete èo robíte!';
$string['confightmleditor'] = 'Vyberte si, èi chcete povoli» pou¾ívanie zakomponovaného HTML textového editora alebo nie. Aj keï si vyberiete povoli», tento editor sa objaví iba keï pou¾ívateµ pou¾íva kompatibilný prehliadaè (IE 5.5 alebo nov¹ie verzie). Pou¾ívatelia sa ale mô¾u tie¾ rozhodnú» nepou¾íva» tento prehliadaè.';
$string['configidnumber'] = '©pecifické nastavenia èi (a)Pou¾ívatelia nebudú po¾iadaní o zadanie ID èísla vôbec, (b)Pou¾ívatelia budú po¾iadaní o zadanie ID èísla, ale nemusia ho vyplni», (c)Pou¾ívatelia budú po¾iadaní o zadanie ID èísla a musia ho vyplni». Ak bude zadané ID èíslo pou¾ívateµa, bude zobrazované v profile.';
$string['configintro'] = 'Na tejto stránke mô¾ete ¹pecifikova» rôzne konfiguraèné premenné, ktoré pomô¾u Moodle správne spolupracova» s Va¹im serverom. Veµmi sa tým neza»a¾ujte - východiskové nastavenia zvyèajne pracujú správne a v¾dy sa mô¾ete k tejto stránke vráti» a tieto nastavenia zmeni».';
$string['configintroadmin'] = 'Na tejto stránke by ste mali konfigurova» Va¹e hlavné administrátorské konto. Administrátor má plnú kontrolu nad celou stránkou. Dbajte na to, aby mal bezpeèné pou¾ívateµské meno, heslo a tie¾ platnú emailovú adresu. Neskôr mô¾ete vytvori» viac administrátorských úètov.';
$string['configintrosite'] = 'Táto stránka Vám umo¾òuje konfigurova» hlavnú stránku a meno tejto stránky. Neskôr to mô¾ete kedykoµvek zmeni» cez link \'Nastavenia stránky\' na domovskej stránke. ';
$string['configintrotimezones'] = 'Táto stránka umo¾ní vyhµada» nové informácie o svetových èasových pásmach (spolu s informáciami o pravidlách zmeny letného a zimného èasu). Tieto miesta budú skontrolované v poradí: $a. Táto procedúra je bezpeèná a nepo¹kodí ¹tandardnú in¹taláciu. 
Chcete aktualizova» Va¹e èasové pásma teraz? ';
$string['configlang'] = 'Vyberte si východzí jazyk pre celú stránku. Pou¾ívatelia mô¾u neskôr toto nastavenie zmeni».';
$string['configlangcache'] = 'Ulo¾te výber jazykov do vyrovnávajúcej pamäte. U¹etríte tak veµa pamäte a výkonu pri spracovávaní stránok. Pokiaµ zapnete túto voµbu, mô¾e sa menu chvíµu (niekoµko minút) aktualizova», pri pridaní èi odstránení jazyka.';
$string['configlangdir'] = 'Pri väè¹ine jazykov sa pí¹e zµava doprava, ale pri niektorých, napr.arabèina a hebrejèina, sa pí¹e sprava doµava.';
$string['configlanglist'] = 'Nechajte túto voµbu prázdnu, ak chcete, aby si pou¾ívatelia mohli vybra» µubovoµný jazyk z tejto verzie Moodle. Tento zoznam mô¾ete skráti», ak uvediete zoznam jazykov, oddelených èiarkou, napr.

sk,cz,en,es_es,fr,it.';
$string['configlangmenu'] = 'Vyberte si, èi chcete zobrazi» menu pre voµbu jazyka na www stránkach Moodle (domovská stránka, autorizaèná stránka a podobne). To neovplyvní pou¾ívateµove mo¾nosti nastavenia preferovaného jazyka vo svojom vlastnom profile.';
$string['configlocale'] = 'Vyberte si miestne jazykové nastavenie - toto ovplyvní formát a jazyk údajov. Tieto miestne údaje musíte ma» nain¹talované vo Va¹om operaènom systéme (napríklad en_US alebo es_ES). Ak neviete, èo si vybra», nechajte toto prázdne.';
$string['configloginhttps'] = 'Aktivovanie tejto voµby bude znamena», ¾e Moodle bude pou¾íva» bezpeèné https spojenie len pri autorizaènej stránke (pri prihlasovaní do systému)uvedením bezpeèného prihlasovacieho mena. Následne sa vráti k normálnemu http protokolu URL pre v¹eobecnú rýchlos». UPOZORNENIE: Toto nastavenie VY®ADUJE, aby https protokol bol aktivovaný na web serveri - ak nie je, MALI BY STE ZAMKNÚ« VA©U STRÁNKU.';
$string['configloglifetime'] = 'Táto voµba ¹pecifikuje då¾ku èasového intervalu, poèas ktorého si chcete uchova» záznamy o pou¾ívateµských aktivitách. Záznamy, ktoré sú star¹ie, sa automaticky vyma¾ú. Je dobré uchováva» si záznamy tak dlho, ako je to mo¾né, ale ak máte veµmi zaneprázdnený server a máte problémy s jeho rýchlos»ou, potom si vyberte krat¹í èas pre uchovávanie záznamov.';
$string['configlongtimenosee'] = 'Ak sa ¹tudenti dlhý èas neprihlásia, sú automaticky vyradení z kurzov. Tento parameter stanovuje tento èasový limit.';
$string['configmaxbytes'] = 'Táto voµba ¹pecifikuje maximálnu veµkos» prená¹aných súborov na celej stránke. Toto nastavenie je limitované PHP nastavením upload_max_filesize a nastavením Apache LimitRequestBody. Nastavenie maxbytes ohranièuje rozsah veµkostí, z ktorých si mô¾ete vybra» v ka¾dej úrovni kurzu alebo modulu.';
$string['configmaxeditingtime'] = 'Toto urèuje èas, ktorý majú µudia na upravovanie príspevkov do fóra, spätnej väzby pre písomné práce , atï. Zvyèajne je to 30 minút.';
$string['configmessaging'] = 'Má by» aktivovaný systém posielania správ medzi pou¾ívateµmi stránky?';
$string['confignoreplyaddress'] = 'Emaily sa niekedy posielajú v mene pou¾ívateµa (napr. prispievanie do fór). Emailová adresa, ktorú tu ¹pecifikujete, bude pou¾ívaná ako adresa \'Od koho\' v prípade, ak prijímatelia nie sú schopní priamo odpoveda» pou¾ívateµovi (napr. keï si pou¾ívateµ vyberie zachovanie súkromnej adresy).';
$string['confignotifyloginfailures'] = 'Ak sa uchovávajú záznamy o neúspe¹ných pokusoch o prihlásenie do systému, mô¾u by» tieto odoslané emailom. Kto by si mal prezera» tieto oznámenia?';
$string['confignotifyloginthreshold'] = 'Ak sú aktívne oznámenia o neúspe¹ných pokusoch o prihlásenie do systému, koµko takýchto prihlásení od jedného pou¾ívateµa alebo jednej IP adresy sa má zobrazova» v oznámeniach?';
$string['configopentogoogle'] = 'Ak aktivujete toto nastavenie, potom Google bude ma» oprávnenie vstupu do Va¹ej stránky ako Hos». Naviac, µudia prichádzajúci na Va¹u stránku z prostredia vyhµadávaèa Google, budú automaticky prihlasovaní ako Hostia. Betrte prosím na vedomie, ¾e takýto prístup mô¾e by» realizovaný len u tých kurzov, ktoré povoµujú vstup hos»ov. ';
$string['configpathtoclam'] = 'Cesta do Clam AV. Pravdepodobne nieèo ako usr/bin/clamscan alebo /usr/bin/clamdscan. Túto cestu potrebujete, aby Clam AV fungoval správne.';
$string['configproxyhost'] = 'Ak tento <b>server</b> potrebuje pou¾íva» server proxy (napríklad bránu firewall) pri prístupe na Internet, tak tu uveïte hostiteµské meno a port. V opaènom prípade to nechajte prázdne.';
$string['configquarantinedir'] = 'Ak chcete, aby Clam AV presunul napadnuté súbory do karanténneho adresára, napí¹te ho sem. Musí by» ale zobraziteµný web serverom. Ak túto voµbu nevyplníte alebo ak zadáte adresár, ktorý neexistuje alebo sa nedá zobrazi», budú napadnuté súbory vymazané. Nepridávajte koncové lomítko.';
$string['configrunclamonupload'] = 'Spusti» Clam AV pri prená¹aní súboru? K tomu budete potrebova» nastavi» správnu cestu v pathtoclam. (Clam AV je voµne ¹íriteµný vírusový skener, ktorý si mô¾ete stiahnu» na http://www.clamav.net/)';
$string['configsectioninterface'] = 'Rozhranie';
$string['configsectionmail'] = 'Po¹ta';
$string['configsectionmaintenance'] = 'Údr¾ba';
$string['configsectionmisc'] = 'Rôzne';
$string['configsectionoperatingsystem'] = 'Operaèný systém';
$string['configsectionpermissions'] = 'Práva';
$string['configsectionsecurity'] = 'Bezpeènos»';
$string['configsectionuser'] = 'Pou¾ívateµ';
$string['configsecureforms'] = 'Moodle mô¾e pou¾i» dodatoèné bezpeènostné opatrenia pri akceptovaní vstupov z web formulárov. Ak to umo¾níte, potom sa bude overova» premenná HTTP_REFERER, ktorú po¹le browser a porovná sa s aktuálnou adresou formulára. Toto mô¾e spôsobi» (vo veµmi zriedkavých prípadoch) problémy, napr. ak je pou¾ívateµ pou¾íva firewall, ktorý je konfigurovaný tak, ¾e odstráni premennú HTTP_REFERER. Vtedy sa mô¾e sta», ¾e formulár Vám \'zmrzne\'. Ak sa na to pou¾ívatelia s»a¾ujú, mô¾ete deaktivova» toto nastavenie. V tomto prípade sa v¹ak vystavujete väè¹ím útokom zvonku (brute force password attacks).
Ak si nie ste istý, nechajte túto voµbu nastavenú na \'Yes\'. ';
$string['configsessioncookie'] = 'Toto nastavenie upravuje meno cookie pou¾ívaného v Moodle sedeniach (lekciách). Táto mo¾nos» je voliteµná a u¾itoèná v tom prípade, ak je spustená viac ako jedna kópia Moodle v rámci tej istej www stránky (aby ste sa vyhli popleteniu cookies).';
$string['configsessiontimeout'] = 'Ak sú µudia pripojení na túto stránku dlho neèinní (bez \"prechádzania\" stránok), sú automaticky odpojení (ich sedenie je ukonèené). Táto premenná urèuje, aký dlhý by mal by» ten èasový interval neèinnosti.';
$string['configshowblocksonmodpages'] = 'Niektoré moduly aktivít podporujú mo¾nos» zobrazovania bloku na Va¹ich stránkach. Pokiaµ túto voµbu zapnete, mô¾u uèitelia pridáva» nové bloky na okraj svojich stránok, inak rozhranie nebude zobrazova» túto mo¾nos».';
$string['configshowsiteparticipantslist'] = 'V¹etci títo pou¾ívatelia stránky a uèitelia budú v zozname úèastníkov stránky. Komu by malo by» povolené prezeranie tohto zoznamu?';
$string['configsitepolicy'] = 'Ak máte definovanú podmienku, ktorú v¹etci pou¾ívatelia musia vidie» a s òou súhlasi» pred pou¾ívaním tejto stránky, potom sem vlo¾te danú URL adresu; v opaènom prípade túto voµbu nevypåòajte. URL adresa mô¾e by» nastavená v ktoromkoµvek mieste - jedno z najvhodnej¹ích miest by bol súbor v súboroch stránky, napr. http://yoursite/file.php/1/policy.html ';
$string['configslasharguments'] = 'Súbory (obrázky atï.) sú prená¹ané prostredníctvom skriptu pou¾ívajúceho znaèku \'lomítko\' (druhá mo¾nos» vo výbere). Táto metóda umo¾òuje, aby boli súbory µah¹ie zachytené na webových prehliadaèoch, proxy serveroch atï. Bohu¾iaµ, niektoré PHP servery  túto metódu nepodporujú. Ak máte problémy pri zobrazovaní stiahnutých súborov alebo obrázkov (napr. obrázky pou¾ívateµa), nastavte tu prvú mo¾nos» vo výbere.';
$string['configsmtphosts'] = 'Zadajte plný názov jedného alebo viacerých SMTP serverov, ktoré má Moodle pou¾íva» pri posielaní po¹ty (napríklad \'mail.a.com\' alebo \'mail.a.com;mail.b.com\'). Ak to neuvediete, Moodle pou¾ije postup posielania po¹ty podµa východzích nastavení.';
$string['configsmtpuser'] = 'Ak ste hore uviedli SMTP server a ten vy¾aduje overovanie, uveïte tu pou¾ívateµské meno a heslo.';
$string['configteacherassignteachers'] = 'Mô¾u obyèajní uèitelia prideli» iných uèiteµov do toho kurzu, v ktorom vyuèujú? Ak \'Nie\', potom iba tvorcovia kurzov a administrátori mô¾u prideµova» uèiteµov do kurzov.';
$string['configthemelist'] = 'Pokiaµ ponecháte toto pole prázdne, povolíte pou¾itie ktorejkoµvek platnej témy. Pokiaµ chcete skráti» výber tém, mô¾ete tu urèi» zoznam mien tém oddelených èiarkou. Napríklad: standard,orangewhite';
$string['configtimezone'] = 'Tu mô¾ete nastavi» východzie èasové pásmo. Toto je len VÝCHODZIE èasové pásmo pre zobrazovanie dátumov - ka¾dý pou¾ívateµ toto mô¾e zmeni» nastavením svojho preferovaného zobrazovania dátumu v profile pou¾ívateµa. \'Èas servera\' na Moodle bude implicitne nastavený podµa operaèného systému servera ale \'Èas servera\' v profile pou¾ívateµa bude nastavený podµa nastavenia èasového pásma.';
$string['configunzip'] = 'Uveïte umiestnenie Vá¹ho unzip programu (iba Unix, nepovinné). Je to potrebné pre rozbalenie zozipovaných archívov na serveri. Ak túto voµbu nevyplníte, Moodle bude pou¾íva» vlastný interný postup.';
$string['configvariables'] = 'Nastavi» premenné';
$string['configwarning'] = 'Postupujte veµmi opatrne pri zmenách týchto nastavení - nesprávne hodnoty mô¾u spôsobi» problémy.';
$string['configzip'] = 'Uveïte cestu k Vá¹mu zip programu (len pre UNIX, nepovinné). Pokiaµ je cesta ¹pecifikovaná, bude pou¾itá pre tvorbu zip archívov na serveri. Pokiaµ ju ponecháte prázdnu, Moodle bude pou¾íva» vlastný interný postup.';
$string['confirmation'] = 'Potvrdenie';
$string['cronwarning'] = '<a href=\"cron.php\" title=\"cron.php\">Skript pre údr¾bu cron.php?</a> nebol spustený najmenej 24 hodín.<br /><a href=\"../doc/?frame=install.html&#8834;=cron\">Táto dokumentácia o in¹talácii</a> vysvetµuje, ako tento proces mô¾ete zautomatizova».';
$string['edithelpdocs'] = 'Upravi» dokumentáciu nápovede';
$string['editstrings'] = 'Upravi» textové re»azce';
$string['filterall'] = 'Filtrova» v¹etky re»azce';
$string['filteruploadedfiles'] = 'Filtrova» prená¹ané súbory';
$string['helpadminseesall'] = 'Mô¾u si administrátori prezera» v¹etky udalosti kalendára, alebo len tie, ktoré sa ich týkajú? ';
$string['helpcalendarsettings'] = 'Konfigurova» viaceré aspekty kalendára týkajúce sa dátumu/èasu v prostredí Moodle';
$string['helpforcetimezone'] = 'Mô¾ete povoli» pou¾ívateµom, aby si individuálne zvolili ich vlastné èasové pásmo, alebo nastavi» jedno èasové pásmo pre ka¾dého.';
$string['helpsitemaintenance'] = 'Pre aktualizácie a ïal¹iu údr¾bu';
$string['helpstartofweek'] = 'Ktorým dòom v tý¾dni by sa mal zaèína» tý¾deò v kalendári?';
$string['helpupcominglookahead'] = 'Koµko dní dopredu sa má ¹tandardne zobrazova» v kalendári pri prezeraní nadchádzajúcich udalostí? ';
$string['helpupcomingmaxevents'] = 'Koµko nadchádzajúcich udalostí (maximum) sa má ¹tandardne zobrazova» pou¾ívateµom?';
$string['helpweekenddays'] = 'Ktoré dni v tý¾dni sú pova¾ované za \"víkend\", t.j. sú oznaèené inou farbou?';
$string['importtimezones'] = 'Aktualizova» kompletný zoznam èasových pásiem';
$string['importtimezonescount'] = '$a->count polo¾iek importovaných z $a->source';
$string['importtimezonesfailed'] = 'Nebol nájdený zdroj! (Zlá správa)';
$string['incompatibleblocks'] = 'Nekompatibilné bloky';
$string['optionalmaintenancemessage'] = 'Správa o údr¾be stránky (nepovinné)';
$string['pleaseregister'] = 'Prosím, zaregistrujte si Va¹u stránku, aby ste odstránili toto tlaèidlo';
$string['sitemaintenance'] = 'Táto stránka sa nachádza v re¾ime údr¾by a momentálne nie je prístupná';
$string['sitemaintenancemode'] = 'Re¾im údr¾by';
$string['sitemaintenanceoff'] = 'Re¾im údr¾by bol deaktivovaný a táto stránka bude ïalej be¾a» normálne';
$string['sitemaintenanceon'] = 'Va¹a stránka je momentálne v re¾ime údr¾by (prihlási» sa a pou¾íva» stránku mô¾u iba administrátori).';
$string['sitemaintenancewarning'] = 'Va¹a stránka je momentálne v re¾ime údr¾by (prihlási» sa a pou¾íva» stránku mô¾u iba administrátori). Pre návrat k normálnej prevádzke stránky <a href=\"maintenance.php\">deaktivujte re¾im údr¾by</a>.';
$string['tabselectedtofront'] = 'Mal by by» oznaèený tag v aktuálnom riadku v tabuµkách s tabulátorom umiestnený vpredu?';
$string['therewereerrors'] = 'Vo Va¹ich údajoch boli nájdené chyby';
$string['timezoneforced'] = 'Toto je nastavené administrátorom stránky';
$string['timezoneisforcedto'] = 'Nastavi» rovnaké pou¾itie pre v¹etkých pou¾ívateµov';
$string['timezonenotforced'] = 'Pou¾ívateµ si mô¾e vybra» vlastnú èasovú zónu';
$string['upgradeforumread'] = 'Novou vlastnos»ou pridanou do Moodle 1.5 je sledovanie èítaných/neèítaných príspevkov do fór.<br />
Pre pou¾itie tejto funkcie je potrebné <a href=\"$a\">aktualizova» tabuµky</a>.';
$string['upgradeforumreadinfo'] = 'Novou vlastnos»ou pridanou do Moodle 1.5 je sledovanie èítaných/neèítaných príspevkov do fór. Pre pou¾itie tejto funkcie je potrebné aktualizova» tabuµky v¹etkými informáciami pre sledovanie existujúcich príspevkov. Toto mô¾e trva» podµa veµkosti Va¹ej stránky a¾ niekoµko hodín a mô¾e to veµmi za»a¾i» Vá¹ databázový server. Napriek tomu bude Va¹a stránka stále funkèná a pou¾ívatelia nebudú zasiahnutí. Ak tento proces raz zaènete, musíte ho aj dokonèi»  (nechajte otvorené okno v prehliadaèi). Napriek tomu, pokiaµ tento proces zastavíte zatvorením okna, nemusíte sa bá», mô¾ete ho na¹tartova» znovu.<br /><br />
Chcete na¹tartova» aktualizáciu teraz?';
$string['upgradelogs'] = 'Va¹e staré záznamy musia by» aktualizované, aby bol systém plne funkèný.<a href=\"$a\">Viac informácií</a>';
$string['upgradelogsinfo'] = 'Nedávno boli prevedené nejaké zmeny týkajúce sa spôsobu, akým sú záznamy uchovávané. Aby ste si mohli prezera» v¹etky Va¹e staré záznamy, musíte ich aktualizova». Toto mô¾e trva» dos» dlho (napr. niekoµko hodín - to zále¾í od Va¹ej stránky) a mô¾e to dos» za»a¾i» samotný databázový server u obsiahlej¹ích stránok. Ak tento proces raz zaènete, musíte ho aj dokonèi» (nechajte otvorené okno v prehliadaèi). Neobávajte sa, Va¹a stránka bude pre ostatných pou¾ívateµov fungova» bez problémov, pokým Vy budete aktualizova» záznamy. <br /><br />Chcete aktualizova» Va¹e záznamy teraz?';
$string['upgradesure'] = '<p>Va¹e súbory v Moodle boli zmenené a Vy sa práve chystáte pový¹i» Vá¹ server na túto verziu:</p>
<p><b>$a</b></p>
<p>Ak to teraz zaènete, u¾ sa nemô¾ete vráti» spä».</p>
<p>Ste si istý, ¾e chcete pový¹i» tento server na túto verziu?</p>';
$string['upgradingdata'] = 'Údaje sa aktualizujú';
$string['upgradinglogs'] = 'Záznamy sa aktualizujú';

?>
