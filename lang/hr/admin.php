<?PHP // $Id$ 
      // admin.php - created with Moodle 1.5.2 + (2005060220)


$string['adminseesallevents'] = 'Administratori vide sve dogaðaje (event)';
$string['adminseesownevents'] = 'Administratori su poput drugih korisnika';
$string['blockinstances'] = 'Instance';
$string['blockmultiple'] = 'Višestruki';
$string['cachetext'] = 'Text cache lifetime';
$string['calendarsettings'] = 'Kalendar';
$string['change'] = 'izmijeni';
$string['configallowcoursethemes'] = 'Ako ukljuèite ovu opciju, svaki kolegij može koristiti svoju vlastitu temu. Teme kolegija imaju prednost pred svim ostalim odabirima tema (na razini sitea, korisnika ili sesije).';
$string['configallowemailaddresses'] = 'Ako želite ogranièiti unos email adresa samo sa odreðenih domena, navedite ih ovdje, odvojene razmakom. Sve ostale domene bit æe odbijene. Primjer: <strong>moj-fakultet.hr</strong> ';
$string['configallowobjectembed'] = 'U sklopu mjera sigurnosti, krajnjim korisnicima nije dozvoljeno umetanje multimedijalnih sadržaja (poput Flash sadržaja) unutar teksta korištenjem eksplicitnih tagova EMBED i OBJECT unutar HTML koda (iako je to i dalje moguæe izvesti na sigurniji naèin putem mediaplugins filtera). Želite li ipak dozvoliti uporabu ovih tagova, ukljuèite ovu opciju.';
$string['configallowunenroll'] = 'Ako je oznaèena opcija \'DA\', studenti se mogu SAMI ispisati s kolegija kad god požele. U suprotnom, to ime nije dopušteno, i procesom ispisivanja upravljaju predavaèi i administratori.';
$string['configallowuserblockhiding'] = 'Želite li dopustiti korisnicima moguænost prikazivanja/skrivanja blokova (s lijeve i desne strane suèelja) na ovom siteu? Ova opcija koristi Javascript i \"cookies\" za pamæenje stanja svakog bloka koji se može minimizirati, i utjeèe jedino na navedenog korisnika.';
$string['configallowuserthemes'] = 'Ukljuèite li ovu opciju, korisnicima æe biti dozvoljeno podešavanje vlastitih tema. Korisnièke teme imaju prioritet nad temama na razini cijelog sitea (ali ne i nad temama na razini pojedinog kolegija)';
$string['configallusersaresitestudents'] = 'Vezano uz aktivnosti na naslovnici sitea, trebaju li SVI korisnici biti tretirani kao studenti? Ako odgovorite sa \'DA\', onda æe svakom korisnièkom raèunu biti dozvoljeno sudjelovanje u navedenim aktivnostima u ulozi studenta. Ako odgovorite sa \'NE\', jedino korisnici koji su polaznici bar JEDNOG kolegija æe biti u stanju sudjelovati u aktivnostima na naslovnici sitea. Samo administratori i predavaèi s posebnim ovlastima mogu biti u ulozi predavaèa na razini naslovnice.';
$string['configautologinguests'] = 'Trebaju li anonimni korisnici biti automatski prijavljeni sustavu kao gosti prilikom pokušaja pristupa koelgijima koji dozvoljavaju pristup gostima (anonimnim korisnicima)? ';
$string['configcachetext'] = 'Za veæe siteove ili siteove koji koriste tekstualne filtere, ova postavka može znaèajno ubrzati rad. Kopije tekstova æe biti zadržane u njihovom obraðenom obliku u vremenskom periodu zadanom ovdje (odnosno, tekstovi æe biti privremeno pohranjeni sa linkovima koji su rezultat filtriranja). Zadavanje premale vrijednosti ovoj postavci bi moglo donekle usporiti rad, a zadavanje prevelike vrijednosti bi moglo rezultirati time da tekstovima treba previše vremena za osvježenje (npr. s novim linkovima).  ';
$string['configclamactlikevirus'] = 'Smatraj datoteke virusima';
$string['configclamdonothing'] = 'Smatraj datoteke èistima od virusa';
$string['configcountry'] = 'Ako ovdje zadate državu, ista æe biti standardno ponuðena za SVE nove korisnièke raèune. Kako bi prisilili korisnike na davanje informacije o državi iz koje dolaze, ostavite ovo polje praznim.';
$string['configdebug'] = 'Ako ukljuèite ovu opciju, PHP error_reporting æe prikazati više upozorenja no inaèe. Ova opcija je jedino korisna razvojnim timovima (programerima).';
$string['configdeleteunconfirmed'] = 'Ako koristite autentikaciju putem emaila, ovo je period unutar kojeg æe korisnièki odgovor biti prihvaæen. Nakon isteka ovog perioda, stari nepotvrðeni korisnièki raèuni bit æe obrisani.';
$string['configdenyemailaddresses'] = 'Kako biste zabranili registraciju email adresa sa odreðenih domena, navedite ih ovdje razdvojene razmacima. Sve ostale domene bit æe prihvaæene. Primjer: <strong>hotmail.com yahoo.com</strong>';
$string['configdigestmailtime'] = 'Korisnici koji odaberu digest oblik slanja email poruka, dobivat æe iste na dnevnoj bazi. Ova postavka zadaje vrijeme dana kada æe navedena poruka biti poslana (prvi sljedeæi cron koji se pokrene nakon zadanog vremena æe ih poslati).';
$string['configdisplayloginfailures'] = 'Ova opcija omoguæava prikaz informacije o pogreškama pri prijavljivanju sustavu korisnicima (failed logins).';
$string['configenablerssfeeds'] = 'Ukljuèuje RSS feedove na razini cijelog sitea. Kako biste mogli vidjeti RSS feedove, morate osim ove opcije ukljuèiti i RSS feedove na razini individualnih modula, i to u postavkama modula na stranici admin konfiguracije. ';
$string['configerrorlevel'] = 'Odaberite razinu PHP upozorenja koja æe vam se prikazivati na ekranu. Postavka Normal je obièno najbolji odabir.';
$string['configextendedusernamechars'] = 'Ukljuèite ovu opciju kako biste omoguæili studentima uporabu SVIH znakova u njihovim korisnièkim imenima (napomena: ovo se NE ODNOSI na njihova prava imena). Standardno je postavljena opcija \'netoèno\' koja u korisnièkim imenima iskljuèivo dozvoljava uporabu alfanumerièkih znakova ';
$string['configfilterall'] = 'Filtriranje svih nizova (strings), ukljuèujuæi naslove, nazive, navigacijske elemente i slièno. Ovo je jedino korisno kod korištenja Višejeziènog filtera, u suprotnom previše optereæuje poslužitelj uz malu ili nikakvu korist.';
$string['configfilteruploadedfiles'] = 'Ukljuèivanje ove opcije æe \"natjerati\" Moodle na filtriranje svih uploadanih HTML i tekstualnih datoteka prije samog prikaza.';
$string['configforcelogin'] = 'Uobièajeno su naslovnica i popis kolegija na njoj vidljivi anonimnim korisnicima (bez prijave sustavu). Ako želite prisiliti korisnike da se prijave sustavu prije BILO KAKVOG PRIKAZA SADRŽAJA na siteu, onda ukljuèite ovu opciju.';
$string['configforceloginforprofiles'] = 'Ukljuèite ovu postavku kako bi prisilili korisnike da se prijave kao pravi korisnici (ne gosti) prije nego što mogu vidjeti stranice sa osobnim podacima. Standardno je ova postavka iskljuèena (postavljena na \"netoèno\") kako bi potencijalni studenti mogli saznati informacije o predavaèu na pojedinom kolegiju, no ovo takoðer znaèi kako ih mogu vidjeti i pretraživaèi (search-engine).';
$string['configframename'] = 'Ako stavljate Moodle unutar web framea, zadajte ime tog framea ovdje. U suprotnom, ova vrijednost æe biti \'_top\'';
$string['configfullnamedisplay'] = 'Ova postavka definira naèin prikazivanja pravih imena korisnika. Standardna postavka \"Ime + Prezime\" je odgovarajuæa, ali postoji i moguænost skrivanja prezimena u potpunosti, kao i moguænost zadavanja ove postavke putem pojedinog jeziènog paketa (neki od jezika preferiraju konvenciju \"Prezime + Ime\").';
$string['configgdversion'] = 'Zadajte inaèicu GD koja je instalirana na poslužitelju. Inaèica koja je prikazana ovdje je ona koju je Moodle bio u stanju automatski detektirati. Nemojte mijenjati ovu opciju ako uistinu ne znate što radite.';
$string['confightmleditor'] = 'Odaberite želite li omoguæiti uporabu internog HTML text editora. Napomena: èak i kad ukljuèite ovu opciju, editor æe se pojaviti samo kod korisnika koji imaju kompatibilni internet preglednik (browser). Korisnici takoðer mogu u svojim postavkama odluèiti žele li koristiti navedeni editor ili ne.';
$string['configidnumber'] = 'Ova postavka odreðuje (a) hoæe li korisnici uopæe biti pitani za ID broj, (b) hoæe li korisnici biti pitani za ID broj, ali æe navedeno polje moæi ostaviti prazno ili (c) hoæe li korisnicima unos u polje ID broj biti obavezan. Ako je ID broj unešen, isti se prikazuje u njihovom profilu. ';
$string['configintro'] = 'Putem ove stranice moguæe je podesiti veæi broj konfiguracijskih varijabli koje bi trebale osigurati  nesmetan rad Moodle sustava na vašem poslužitelju. Nemojte se previše brinuti oko ovih postavki - standardne postavke (default) su obièno dovoljne za ugodan i nesmetan rad sustava, a i uvijek možete ponovno otvoriti ovu stranicu i promijeniti neke od varijabli po potrebi.';
$string['configintroadmin'] = 'Putem ove stranice moguæe je podesiti  administratorski korisnièki raèun koji ima potpunu kontrolu nad cijelim siteom. Pobrinite se da date SIGURNO korisnièko ime i lozinku, kao i VALJANU email adresu (prejednostavne lozinke, lozinke koje su iste kao i korisnièko ime, kao i PRAZNA lozinka su OGROMNA sigurnosna rupa, pa navedeno izbjegnite pod svaku cijenu). Naknadno možete napraviti veæi broj administratorskih korisnièkih raèuna.';
$string['configintrosite'] = 'Putem ove stranice možete podesiti izgled naslovnice i naziv sitea. Uvijek se možete naknadno vratiti na ovu stranicu i izmjeniti zadane postavke koristeæi link \"Postavke sitea\" na naslovnici (ako ste prijavljeni kao administrator ili korisnik s posebnim pravima).';
$string['configlang'] = 'Odaberite standardni jezik za cijeli site. Korisnici mogu zadati vlastite postavke za svoj korisnièki raèun naknadno.';
$string['configlangcache'] = 'Ukljuèivanje CACHE postavke za jezièni izbornik. Ova postavka štedi velike kolièine radne memorije i procesorske snage. Ako ukljuèite ovu postavku, jezièni izbornik æe registrirati i prikazati promjene (dodavanje ili brisanje odreðenih paketa na razini sustava) nakon par minuta.';
$string['configlangdir'] = 'Veæina jezika, odnosno njihovih pisama, se prikazuje s lijeva na desno, ali neki, poput Arapskog ili Hebrejskog, se prikazuju s desna na lijevo.';
$string['configlanglist'] = 'Ostavite ovu opciju praznom ako želite dati vašim korisnicima pravo na odabir BILO KOJEG jeziènog paketa kojeg imate instaliranog na razini Moodle sustava. Meðutim, možete skratiti padajuæi jezièni izbornik unošenjem željenih jeziènih kodova odvojenih zarezima. Primjer: en,hr,de,fr,it';
$string['configlangmenu'] = 'Odaberite želite li prikazati padajuæi izbornik za odabir jezika suèelja na naslovnici, stranici za prijavu sustavu, itd. Ova postavka ne onemoguæava korisnika u odabiru željenog jezika suèelja putem opcije željenog jezika u njihovom osobnom profilu.';
$string['configlocale'] = 'Odaberite lokalne postavke na razini cijelog sustava Moodle, što æe utjecati na oblik prikaza i jezik na kojem se ispisuju datumi. Navedene lokalne postavke morate prethodno imati instalirane unutar operativnog sustava poslužitelja. (primjer en_US). Ako ne znate što biste odabrali, ostavite ovo polje praznim.';
$string['configloginhttps'] = 'Ukljuèivanjem ove postavke, Moodle æe koristiti HTTPS protokol iskljuèivo za stranicu prijave sustavu (login page), a nakon toga æe se protokol prebaciti na HTTP, poveæavajuæi time brzinu rada. OPREZ: ova postavka ZAHTJEVA da HTTPS bude omoguæen i na vašem web poslužitelju - ako HTTPS nije podešen na poslužitelju MOŽETE UKLJUÈIVANJEM OVE POSTAVKE ONEMOGUÆITI PRISTUP Moodle sustavu SEBI I DRUGIMA!! ';
$string['configloglifetime'] = 'Ovom postavkom možete zadati koliko dugo želite èuvati logove o korisnièkoj aktivnosti. Logovi koji su stariji od zadanog roka æe biti automatski obrisani. Dobro je èuvati logove što je dulje moguæe, u sluèaju da vam zatrebaju, ali ako imate poslužitelj sa veæim brojem korisnika i/ili imate zbog toga probleme s performansama, možda bi bilo bolje podesiti kraæi vijek logova.';
$string['configlongtimenosee'] = 'Ako se studenti nisu prijavili sustavu tijekom razmjerno dugog perioda, onda se automatski ispisuju iz kolegija. Ova postavka zadaje navedeno vremensko ogranièenje.';
$string['configmaxbytes'] = 'Ova postavka odreðuje maksimalnu velièinu uploadanih datoteka na razini cijelog sitea. Vrijednosti ove postavke su ogranièene PHP specifiènom postavkom upload_max_filesize i Apache postavkom LimitRequestBody. ';
$string['configmaxeditingtime'] = 'Odreðuje kolièinu vremena koje korisnici imaju na raspolaganju za nakndano ureðivanje forum poruka, rjeènièkih komentara i sliènih operacija. Uobièajena vrijednost od 30 minuta je obièno zadovoljavajuæa.';
$string['configmessaging'] = 'Treba li sustav instant poruka (messaging system) za komunikaciju meðu korisnicima sustava biti ukljuèen?';
$string['confignotifyloginfailures'] = 'Ako postoji log o pogreškama pri prijavi sustavu (login failures), sustav može poslati email poruku o tome. Tko bi trebao dobiti navedenu poruku?';
$string['configpathtoclam'] = 'Putanja do Clam AV alata. Vjerojatno nešto poput /usr/bin/clamscan ili /usr/bin/clamdscan. Ovu vrijednost je potrebno unijeti ako želite koristiti Clam AV.';
$string['configproxyhost'] = 'Ako ovaj <b>poslužitelj</b> koristi proxy poslužitelj (ili vatrozid) kako bi pristupio Internetu, molimo unesite ime proxy poslužitelja u polje. U suprotnom, ostavite navedeno polje prazno.';
$string['configrunclamonupload'] = 'Koristi Clam AV pri uploadu datoteka? Da bi ova postavka uspješno radila, morate navesti toèno putanju u varijabli \'pathtoclam\'. (Clam AV je BESPLATNI antivirusni alat koji možete pronaæi na http://www.clamav.net/)';
$string['configsectioninterface'] = 'Suèelje';
$string['configsectionmail'] = 'Mail';
$string['configsectionmaintenance'] = 'Održavanje';
$string['configsectionmisc'] = 'Razno';
$string['configsectionoperatingsystem'] = 'Operativni sustav';
$string['configsectionpermissions'] = 'Dozvole';
$string['configsectionsecurity'] = 'Sigurnost';
$string['configsectionuser'] = 'Korisnik';
$string['configvariables'] = 'Varijable';
$string['confirmation'] = 'Potvrda';
$string['edithelpdocs'] = 'Uredi dokumente s pomoæi';
$string['editstrings'] = 'Uredi nizove (strings)';
$string['filterall'] = 'Filtriraj sve nizove (strings)';
$string['filteruploadedfiles'] = 'Filter uploaded files';
$string['helpcalendarsettings'] = 'Podesite razlièite Moodle postavke vezane uz kalendar i vrijeme, odnosno datum.';
$string['helpsitemaintenance'] = 'Za upgrade i ostale poslove održavanja';
$string['helpstartofweek'] = 'Poèetni dan u tjednu (kalendar)?';
$string['incompatibleblocks'] = 'Nekompatibilni blokovi';
$string['optionalmaintenancemessage'] = 'Opcionalna poruka prilikom održavanja i radova na sustavu';
$string['pleaseregister'] = 'Molimo registrirajte vaš site kako biste maknuli ovu poruku';
$string['sitemaintenance'] = 'Sustav je trenutno nedostupan zbog održavanja i radova.';
$string['sitemaintenancemode'] = 'Stanje održavanja i radova na sustavu';
$string['sitemaintenanceon'] = 'Vaš sustav je trenutno u stanju održavanja i radova na sustavu (prijaviti se mogu samo administratori)';
$string['timezoneisforcedto'] = 'Prisili sve korisnike na uporabu';
$string['timezonenotforced'] = 'Korisnici mogu odabrati vlastitu vremensku zonu';
$string['upgradelogs'] = 'For full functionality, your old logs need to be upgraded.  <a href=\"$a\">More information</a>';
$string['upgradelogsinfo'] = 'Some changes have recently been made in the way logs are stored.  To be able to view all of your old logs on a per-activity basis, your old logs need to be upgraded.  Depending on your site this can take a long time (eg several hours) and can be quite taxing on the database for large sites.  Once you start this process you should let it finish (by keeping the browser window open).  Don\'t worry - your site will work fine for other people while the logs are being upgraded.<br /><br />Do you want to upgrade your logs now?';
$string['upgradesure'] = 'Your Moodle files have been changed, and you are about to automatically upgrade your server to this version:
<p><b>$a</b></p>
<p>Once you do this you can not go back again.</p> 
<p>Are you sure you want to upgrade this server to this version?</p>';
$string['upgradingdata'] = 'Nadograðujem podatke';
$string['upgradinglogs'] = 'Nadograðujem logove';

?>
