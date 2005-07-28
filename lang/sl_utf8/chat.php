<?PHP // $Id:chat.php from chat.xml
      // Comments: tomaz at zid dot si

$string['beep'] = 'zvočni signal';
$string['chatintro'] = 'Uvodno besedilo';
$string['chatname'] = 'Ime te klepetalnice';
$string['chatreport'] = 'Seje klepeta';
$string['chattime'] = 'Naslednji čas klepeta';
$string['configmethod'] = 'Navaden način klepeta vključuje redno komunikacijo odjemalca s strežnikom za posodobitve. To ne zahteva nobene konfiguracije in deluje povsod, a lahko ustvari veliko obremenjenost strežnika z mnogimi klepetalci.  Uporaba strežniškega programa zahteva dostop do Unix lupine, a ima za posledico prilagodljivo okolje klepeta.';
$string['configoldping'] = 'Kaj je največji čas, ki sme preteči preden zaznamo, da je uporabnik prekinil zvezo (v sekundah)? To je zgolj zgornja meja, saj so ponavadi prekinitve zvez zaznane zelo hitro. Nižje vrednosti bodo bolj zahtevne za vaš strežnik. Če uporabljate navaden način, <strong>nikoli</stop> ne nastavite tega nižje od 2 * chat_refresh_room.';
$string['configrefreshroom'] = 'Kako pogosto naj se osveži sama klepetalnica? (v sekundah).  Z nizko nastavitvijo te vrednosti bo videti klepetalnica hitrejša, a lahko povzroči višjo obremenitev vašega spletnega strežnika, ko bo več oseb klepetalo';
$string['configrefreshuserlist'] = 'Kako pogosto naj se osveži seznam uporabnikov? (v sekundah)';
$string['configserverhost'] = 'Ime gostiteljskega strežnika na katerem je strežniški demon';
$string['configserverip'] = 'Številčni IP naslov, ki se ujema z gornjim imenom gostitelja';
$string['configservermax'] = 'Največje dovoljeno število odjemalcev';
$string['configserverport'] = 'Vrata strežnika za demona';
$string['currentchats'] = 'Aktivne seje klepeta';
$string['currentusers'] = 'Trenutni uporabniki';
$string['deletesession'] = 'Izbriši to sejo';
$string['deletesessionsure'] = 'Ste prepričani, da želite izbrisati to sejo?';
$string['donotusechattime'] = 'Ne objavi časov klepeta';
$string['enterchat'] = 'Kliknite tu za vstop v klepet';
$string['errornousers'] = 'Ni možno najti uporabnikov!';
$string['explaingeneralconfig'] = 'Te nastavitve so <strong>vedno</strong>  veljavne';
$string['explainmethoddaemon'] = 'Te nastavitve štejejo <strong>samo</strong>, če ste izbrali \"Strežniški demon klepeta\" za chat_method';
$string['explainmethodnormal'] = 'Te nastavitve štejejo <strong>samo</strong>, če ste izbrali \"Navaden način\" za chat_method';
$string['generalconfig'] = 'Splošna konfiguracija';
$string['helpchatting'] = 'Pomoč pri klepetu';
$string['idle'] = 'Nedejaven';
$string['messagebeepseveryone'] = '$a pozvoni vsem!';
$string['messagebeepsyou'] = '$a vam je pravkar pozvonil!';
$string['messageenter'] = '$a se je pravkar pridružil temu klepetu';
$string['messageexit'] = '$a je zapustil ta klepet';
$string['messages'] = 'Sporočila';
$string['methodnormal'] = 'Navaden način';
$string['methoddaemon'] = 'Strežniški demon klepeta';
$string['modulename'] = 'Klepet';
$string['modulenameplural'] = 'Klepeti';
$string['neverdeletemessages'] = 'Nikoli ne briši sporočil';
$string['nextsession'] = 'Naslednja seja po urniku';
$string['noguests'] = 'Klepet ni odprt za goste';
$string['nomessages'] = 'Ni še sporočil';
$string['repeatdaily'] = 'Vsak dan ob istem času';
$string['repeatnone'] = 'Brez ponovitev - objavi samo določen čas';
$string['repeattimes'] = 'Ponovi seje';
$string['repeatweekly'] = 'Vsak teden ob istem času';
$string['savemessages'] = 'Shrani minule seje';
$string['seesession'] = 'Poglej to sejo';
$string['sessions'] = 'Seje klepeta';
$string['strftimemessage'] = '%%H:%%M';
$string['studentseereports'] = 'Vsi lahko vidijo minule seje';
$string['viewreport'] = 'Ogled minulih sej klepeta';


?>