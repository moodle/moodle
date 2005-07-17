<?PHP // $Id$ 
      // chat.php - created with Moodle 1.5 ALPHA (2005043000)


$string['beep'] = 'beep';
$string['chatintro'] = 'Panimulang teksto';
$string['chatname'] = 'Pangalan ng chat room na ito';
$string['chatreport'] = 'Sesyon ng Chat';
$string['chattime'] = 'Susunod na oras ng chat';
$string['configmethod'] = 'Ang normal na paraan ng chat ay regular na kinkontak ng mga kliyente ang server para sa mga pagbabago.  Hindi nito kailangang isaayos at gumagana sa lahat ng lugar, nguni\'t makapagpapabigat ito sa load ng server kung maraming nagchachat.  Ang paggamit ng server daemon ay nangangailangan ng shell access sa Unix, subali\'t nagreresulta ito sa isang mabilis at nai-scale na kapaligirang pangchat.';
$string['configoldping'] = 'Ano ang maksimum na oras na kailangang lumipas bago namin maramdaman na ang isang user ay dina konektado (sa segundo)?  Pantaas na limitasyon lamang ito, dahil kadalasan ay nararamdaman kaagad ang pagdisconnect nang madali.  Ang mas mababang halaga ay mas matrabaho para sa iyong server.  Kung ginagamit mo ang normal na paraan, <strong>huwag kailanman</strong> itatakda ito nang mas mababa sa 2 * chat_refresh_room.';
$string['configrefreshroom'] = 'Gaano kalimit sasariwain ang mismong chat room? (sa segundo).  Mukhang mabilis ang chat room kapag itinakda ito na mababa, nguni\'t maaaring magpabigat ito sa trabaho ng web server mo kung marami ang nagchachat';
$string['configrefreshuserlist'] = 'Gaano kalimit dapat sariwain ang listahan ng mga user? (sa segundo)';
$string['configserverhost'] = 'Ang hostname ng kompyuter kung saan naroon ang server daemon';
$string['configserverip'] = 'Ang numerong IP address na katugma ng hostname sa itaas';
$string['configservermax'] = 'Maks na bilang ng pinahihintulutang kliyente';
$string['configserverport'] = 'Port na gagamitin sa server para sa daemon';
$string['currentchats'] = 'Mga aktibong chat sesyon';
$string['currentusers'] = 'Mga kasalukuyang user';
$string['deletesession'] = 'Burahin ang sesyon na ito';
$string['deletesessionsure'] = 'Talaga bang nais mong burahin ang sesyon na ito?';
$string['donotusechattime'] = 'Huwag maglathala ng anumang oras ng chat';
$string['enterchat'] = 'Iklik ito para makapasok sa chat ngayon';
$string['errornousers'] = 'Walang matagpuang user!';
$string['explaingeneralconfig'] = '<strong>Palaging</strong> pinagagana ang mga kaayusang ito';
$string['explainmethoddaemon'] = 'Mahalaga ang mga kaayusang ito <strong>tangi kung</strong> pinilì mo ang \"Chat server daemon\" para sa chat_method';
$string['explainmethodnormal'] = 'Mahalaga ang mga kaayusang ito <strong>tangi kung</strong> pinilì mo ang \"Normal na paraan\" para sa chat_method';
$string['generalconfig'] = 'Pangkalahatang pagsasaayos';
$string['helpchatting'] = 'Tulong sa pagchachat';
$string['idle'] = 'Diginagamit';
$string['messagebeepseveryone'] = 'Bineep ang lahat ni $a!';
$string['messagebeepsyou'] = 'Bineep ka ni $a !';
$string['messageenter'] = 'Pumasok si $a sa chat na ito';
$string['messageexit'] = 'Umalis na si $a sa chat na ito';
$string['messages'] = 'Mga Mensahe';
$string['methoddaemon'] = 'Chat server daemon';
$string['methodnormal'] = 'Normal na paraan';
$string['modulename'] = 'Chat';
$string['modulenameplural'] = 'Mga Chat';
$string['neverdeletemessages'] = 'Huwag kailanmang buburahin ang mga mensahe';
$string['nextsession'] = 'Susunod na nakaiskedyul na sesyon';
$string['noguests'] = 'Hindi bukas ang chat sa mga bisita';
$string['nomessages'] = 'Wala pang mensahe';
$string['repeatdaily'] = 'Sa parehong oras araw-araw';
$string['repeatnone'] = 'Walang paguulit - tanging ang  partikular na oras ang ilathala';
$string['repeattimes'] = 'Ulitin ang mga sesyon';
$string['repeatweekly'] = 'Sa parehong oras linggo-linggo';
$string['savemessages'] = 'Isave ang mga nalipas na sesyon';
$string['seesession'] = 'Tingnan ang sesyon na ito';
$string['sessions'] = 'Mga sesyon ng Chat';
$string['strftimemessage'] = '%%H:%%M';
$string['studentseereports'] = 'Makikita ng lahat ang mga nakaraang sesyon';
$string['viewreport'] = 'Tingnan ang mga nakalipas na sesyon ng chat';

?>
