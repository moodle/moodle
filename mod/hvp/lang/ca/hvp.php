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

$string['modulename'] = 'Contingut interactiu';
$string['modulename_help'] = 'El mòdul d\'activitat H5P us permet crear continguts com ara vídeos interactius, qüestionaris, activitats d\'arrossegar i deixar anar, preguntes de resposta múltiple, presentacions i molt més.

A més de tractar-se d\'una eina d\'autor per a contingut enriquit, l\'H5P us permet importar i exportar les activitats, facilitant així la possibilitat de reutilitzar-les i compartir-les.

El mòdul fa un seguiment de la interacció amb els usuaris mitjançant xAPI. Les puntuacions obtingudes queden enregistrades al mòdul de qualificacions de Moodle.

Podeu afegir continguts interactius H5P creant-los amb la seva eina d\'autor, o bé pujant al Moodle fitxers H5P generats en altres plataformes compatibles.';
$string['modulename_link'] = 'https://h5p.org/moodle-more-help';
$string['modulenameplural'] = 'Contingut interactiu';
$string['pluginadministration'] = 'H5P';
$string['pluginname'] = 'H5P';
$string['intro'] = 'Introducció';
$string['h5pfile'] = 'Fitxer H5P';
$string['fullscreen'] = 'Pantalla completa';
$string['disablefullscreen'] = 'Desactiva la pantalla completa';
$string['download'] = 'Baixa-ho';
$string['copyright'] = 'Drets d\'ús';
$string['embed'] = 'Incrusta';
$string['showadvanced'] = 'Mostra les opcions avançades';
$string['hideadvanced'] = 'Amaga les opcions avançades';
$string['resizescript'] = 'Inseriu aquest script al vostre lloc web si voleu que el contingut incrustat s\'ajusti dinàmicament a la mida disponible:';
$string['size'] = 'Mida';
$string['close'] = 'Tanca';
$string['title'] = 'Títol';
$string['author'] = 'Autor';
$string['year'] = 'Any';
$string['source'] = 'Font';
$string['license'] = 'Llicència';
$string['thumbnail'] = 'Miniatura';
$string['nocopyright'] = 'No hi ha informació de copyright disponible per aquest contingut.';
$string['downloadtitle'] = 'Baixa aquest contingut en un fitxer H5P.';
$string['copyrighttitle'] = 'Mostra la informació de copyright per aquest contingut.';
$string['embedtitle'] = 'Mostra el codi d\'incrustació per aquest contingut.';
$string['h5ptitle'] = 'Visiteu H5P.org per trobar més continguts interessants.';
$string['contentchanged'] = 'Aquest contingut ha canviat des de la darrera vegada que el vau utilitzar.';
$string['startingover'] = "Haureu de tornar a començar.";
$string['confirmdialogheader'] = 'Confirmeu l\'acció';
$string['confirmdialogbody'] = 'Si us plau, confirmeu que realment voleu fer-ho. Aquesta acció no és reversible.';
$string['cancellabel'] = 'Cancel·la';
$string['confirmlabel'] = 'Confirma';
$string['noh5ps'] = 'No hi ha cap contingut interactiu en aquest curs.';

$string['lookforupdates'] = 'Cerca actualitzacions de l\'H5P';
$string['updatelibraries'] = 'Actualitza totes les biblioteques';
$string['removetmpfiles'] = 'Elimina els fitxers temporals antics de l\'H5P';
$string['removeoldlogentries'] = 'Elimina les entrades de registre antigues de l\'H5P';

// Admin settings.
$string['displayoptionnevershow'] = 'No ho mostris mai';
$string['displayoptionalwaysshow'] = 'Mostra-ho sempre';
$string['displayoptionpermissions'] = 'Mostra-ho només si l\'usuari té permís per exportar continguts H5P';
$string['displayoptionauthoron'] = 'Controlat per l\'autor, activat per defecte';
$string['displayoptionauthoroff'] = 'Controlat per l\'autor, desactivat per defecte';
$string['displayoptions'] = 'Opcions de visualització';
$string['enableframe'] = 'Mostra el marc i la barra d\'accions';
$string['enabledownload'] = 'Botó de baixades';
$string['enableembed'] = 'Botó d\'incrustació';
$string['enablecopyright'] = 'Botó de Copyright';
$string['enableabout'] = 'Botó "Quant a l\'H5P"';
$string['hubsettingsheader'] = 'Tipus de contingut';
$string['enablehublabel'] = 'Ús de l\'"H5P Hub"';
$string['disablehubdescription'] = "Es recomana vivament deixar aquesta opció habilitada. El servei \"H5P Hub\" us proporciona una interfície simple per aconseguir nous tipus de contingut i mantenir actualitzats els que ja teniu instal·lats. En el futur també farà més senzill compartir i reutilitzar continguts creats pels usuaris. Si desactiveu aquesta opció haureu d'instal·lar i actualitzar manualment els tipus de contingut mitjançant formularis de pujada de fitxers.";
$string['empty'] = 'Buit';
$string['reveal'] = 'Revela';
$string['hide'] = 'Amaga';
$string['sitekey'] = 'Clau de lloc';
$string['sitekeydescription'] = 'La clau de lloc és un secret que identifica de manera única aquest lloc web a l\'"H5P Hub".';

$string['sendusagestatistics'] = 'Envia les estadístiques d\'ús';
$string['sendusagestatistics_help'] = 'Les estadístiques d\'ús s\'enviaran de manera automàtica per ajudar els desenvolupadors a entendre millor com s\'està utilitzant l\'H5P i determinar les possibles àrees de millora.';
$string['enablesavecontentstate'] = 'Desa l\'estat del contingut interactiu';
$string['enablesavecontentstate_help'] = 'Desa de manera automàtica l\'estat actual del contingut interactiu per a cada usuari. Això significa que els usuaris podran reprendre les activitats allí on les hagin deixat.';
$string['contentstatefrequency'] = 'Freqüència amb què es desarà l\'estat del contingut';
$string['contentstatefrequency_help'] = 'Indiqueu amb quina freqüència voleu que el sistema desi el progrés de cada usuari, indicada en segons. Incrementeu el temps si us trobeu amb problemes de rendiment provocats per les transaccions Ajax.';
$string['enabledlrscontenttypes'] = 'Activa els tipus de contingut dependents d\'un repositori d\'objectes d\'aprenentatge (LRS)';
$string['enabledlrscontenttypes_help'] = 'Fa possible l\'ús de continguts que requereixen d\'un repositori d\'objectes d\'aprenentatge (LRS) per funcionar correctament, com ara els del tipus "Questionnaire".';

// Admin menu.
$string['contenttypecacheheader'] = 'Memòria cau dels tipus de contingut';
$string['settings'] = 'Configuració H5P';
$string['libraries'] = 'Biblioteques H5P';

// Content type cache section.
$string['ctcacheconnectionfailed'] = "No s'ha pogut contactar amb el servei \"H5P Hub\". Proveu-ho més tard.";
$string['ctcachenolibraries'] = 'No s\'ha rebut cap tipus de contingut des del servei "H5P Hub". Proveu-ho més tard.';
$string['ctcachesuccess'] = 'S\'ha actualitzat correctament la memòria cau de les biblioteques!';
$string['ctcachelastupdatelabel'] = 'Darrera actualització';
$string['ctcachebuttonlabel'] = 'Actualitza la memòria cau dels tipus de contingut';
$string['ctcacheneverupdated'] = 'Mai';
$string['ctcachetaskname'] = 'Actualitza la memòria cau dels tipus de contingut';
$string['ctcachedescription'] = 'Si manteniu actualitzada la memòria cau dels tipus de contingut podreu veure, baixar i utilitzar les darreres versions de les biblioteques. Aquesta acció és diferent de la d\'actualitzar cada una de les biblioteques.';

// Upload libraries section.
$string['uploadlibraries'] = 'Penja biblioteques';
$string['options'] = 'Opcions';
$string['onlyupdate'] = 'Només actualitza les biblioteques existents';
$string['disablefileextensioncheck'] = 'Desactiva la comprovació de l\'extensió dels noms de fitxers';
$string['disablefileextensioncheckwarning'] = "Atenció! El fet de desactivar la comprovació de l'extensió dels noms de fitxers pot tenir implicacions de seguretat, ja que permetria penjar fitxers php al servidor. Això podria facilitar a atacants potencials l'execució de codi maliciós. Assegureu-vos de conèixer exactament què esteu penjant!";
$string['upload'] = 'Penja';

// Installed libraries section.
$string['installedlibraries'] = 'Biblioteques instal·lades';
$string['invalidtoken'] = 'El testimoni de seguretat no és vàlid.';
$string['missingparameters'] = 'Falten paràmetres';
$string['nocontenttype'] = 'No heu especificat cap tipus de contingut.';
$string['invalidcontenttype'] = 'El tipus de contingut escollit no és vàlid.';
$string['installdenied'] = 'No teniu permis per instal·lar tipus de contingut. Contacteu amb l\'administrador del lloc.';
$string['downloadfailed'] = 'Ha fallat la baixada de la biblioteca sol·licitada.';
$string['validationfailed'] = 'El paquet H5P sol·licitat no és vàlid';
$string['validatingh5pfailed'] = 'Ha fallat la validació del paquet H5P.';

// H5P library list headers on admin page.
$string['librarylisttitle'] = 'Títol';
$string['librarylistrestricted'] = 'Restringit';
$string['librarylistinstances'] = 'Instàncies';
$string['librarylistinstancedependencies'] = 'Dependències de la instància';
$string['librarylistlibrarydependencies'] = 'Dependències de la biblioteca';
$string['librarylistactions'] = 'Accions';

// H5P library page labels.
$string['addlibraries'] = 'Afegeix biblioteques';
$string['installedlibraries'] = 'Biblioteques instal·lades';
$string['notapplicable'] = 'N/A';
$string['upgradelibrarycontent'] = 'Actualitza el contingut de la biblioteca';

// Upgrade H5P content page.
$string['upgrade'] = 'Actualitza l\'H5P';
$string['upgradeheading'] = 'Actualitza el contingut {$a}';
$string['upgradenoavailableupgrades'] = 'No hi ha cap actualització disponible per aquesta biblioteca.';
$string['enablejavascript'] = 'Cal que activeu el JavaScript.';
$string['upgrademessage'] = 'Sou a punt d\'actualitzar {$a}. Escolliu la versió a la qual voleu actualitzar.';
$string['upgradeinprogress'] = 'Actualitzant a %ver...';
$string['upgradeerror'] = 'S\'ha produït un error en processar els paràmetres:';
$string['upgradeerrordata'] = 'No s\'han pogut llegir les dades de la biblioteca %lib.';
$string['upgradeerrorscript'] = 'No s\'ha pogut llegir l\'script d\'actualització de %lib.';
$string['upgradeerrorcontent'] = 'No s\'ha pogut actualitzar el contingut interactiu %id:';
$string['upgradeerrorparamsbroken'] = 'Els paràmetres estan trencats.';
$string['upgradedone'] = 'Heu actualitzat correctament {$a} instància/es de contingut.';
$string['upgradereturn'] = 'Torna';
$string['upgradenothingtodo'] = "No hi ha cap instància de contingut interactiu per actualitzar.";
$string['upgradebuttonlabel'] = 'Actualitza';
$string['upgradeinvalidtoken'] = 'Error: El testimoni de seguretat no és vàlid!';
$string['upgradelibrarymissing'] = 'Error: No es pot trobar la biblioteca!';

// Results / report page.
$string['user'] = 'Usuari';
$string['score'] = 'Puntuació';
$string['maxscore'] = 'Puntuació màxima';
$string['finished'] = 'Acabat';
$string['loadingdata'] = 'S\'estan llegint les dades.';
$string['ajaxfailed'] = 'Ha fallat la lectura de dades.';
$string['nodata'] = "No hi ha cap dada que coincideixi amb el criteri especificat.";
$string['currentpage'] = 'Pàgina $current de $total';
$string['nextpage'] = 'Pàgina següent';
$string['previouspage'] = 'Pàgina anterior';
$string['search'] = 'Cerca';
$string['empty'] = 'No hi ha cap resultat disponible';
$string['viewreportlabel'] = 'Informe';
$string['dataviewreportlabel'] = 'Mostra les respostes';
$string['invalidxapiresult'] = 'No s\'han trobat resultats xAPI per a la combinació de contingut i identificador d\'usuari especificada.';
$string['reportnotsupported'] = 'No està suportat';
$string['reportingscorelabel'] = 'Puntuació:';
$string['reportingscaledscorelabel'] = 'Puntuació al llibre de qualificacions:';
$string['reportingscoredelimiter'] = 'sobre';
$string['reportingscaledscoredelimiter'] = ',';
$string['reportingquestionsremaininglabel'] = 'questions remaining to grade';
$string['reportsubmitgradelabel'] = 'Submit grade';
$string['noanswersubmitted'] = 'This user hasn\'t submitted an answer to the H5P yet';

// Editor.
$string['javascriptloading'] = 'S\'està esperant el JavaScript...';
$string['action'] = 'Acció';
$string['upload'] = 'Puja';
$string['create'] = 'Crea';
$string['editor'] = 'Editor';

$string['invalidlibrary'] = 'La biblioteca no eś vàlida';
$string['nosuchlibrary'] = 'No existeix aquesta biblioteca';
$string['noparameters'] = 'No s\'han passat paràmetres';
$string['invalidparameters'] = 'Els paràmetres no són vàlids';
$string['missingcontentuserdata'] = 'Error: No s\'han trobat dades d\'usuari per aquest contingut';

$string['maximumgrade'] = 'Puntuació màxima';
$string['maximumgradeerror'] = 'Indiqueu en un nombre positiu enter el nombre màxim de punts disponibles en aquesta activitat';

// Capabilities.
$string['hvp:view'] = 'See and interact with H5P activities';
$string['hvp:addinstance'] = 'Afegir activitats H5P noves';
$string['hvp:manage'] = 'Edit existing H5P activites';
$string['hvp:getexport'] = 'Aconseguir el fitxer d\'exportació dels H5P del curs';
$string['hvp:getembedcode'] = 'View H5P embed code when \'controlled by permission\' option is set';
$string['hvp:saveresults'] = 'Desar resultats de continguts H5P';
$string['hvp:savecontentuserdata'] = 'Desar continguts o dades d\'usuari H5P';
$string['hvp:viewresults'] = 'Veure els resultats de les meves preguntes';
$string['hvp:viewallresults'] = 'Veure els resultats d\'altres usuaris del curs';
$string['hvp:restrictlibraries'] = 'Restringir una biblioteca H5P';
$string['hvp:userestrictedlibraries'] = 'Fer ús de biblioteques H5P restringides';
$string['hvp:updatelibraries'] = 'Actualitzar la versió d\'una biblioteca H5P';
$string['hvp:getcachedassets'] = 'Aconseguir actius H5P desats a la memòria cau';
$string['hvp:installrecommendedh5plibraries'] = 'Instal·lar les biblioteques H5P recomanades';

// Capabilities error messages.
$string['nopermissiontoupgrade'] = 'Mo teniu permís per actualitzar biblioteques.';
$string['nopermissiontorestrict'] = 'No teniu permís per restringir biblioteques.';
$string['nopermissiontosavecontentuserdata'] = 'No teniu permís per desar les dades d\'usuari dels continguts interactius.';
$string['nopermissiontosaveresult'] = 'No teniu permís per desar el resultat d\'aquest contingut interactiu.';
$string['nopermissiontoviewresult'] = 'No teniu permís per veure els resultats d\'aquest contingut interactiu.';

// Editor translations.
$string['noziparchive'] = 'La vostra versió de PHP no suporta ZipArchive.';
$string['noextension'] = 'El fitxer que heu pujat no és un paquet HTML5 vàlid (El nom del fitxer no porta l\'extensió .h5p)';
$string['nounzip'] = 'El fitxer que heu pujat no és un paquet HTML5 vàlid (No s\'ha pogut descomprimir)';
$string['noparse'] = 'No s\'ha pogut processar el fitxer principal "h5p.json"';
$string['nojson'] = 'El fitxer principal h5p.json no és vàlid';
$string['invalidcontentfolder'] = 'El contingut té una carpeta que no és vàlida';
$string['nocontent'] = 'No s\'ha pogut trobar o processar el fitxer "content.json"';
$string['librarydirectoryerror'] = 'El nom de directori de la biblioteca ha de coincidir amb l\'esquema "nomDeMàquina" o "nomDeMàquina-versióMajor.versióMenor" (a "library.json"). (Directori: {$a->%directoryName} , nomDeMàquina: {$a->%machineName}, versióMajor: {$a->%majorVersion}, versióMenor: {$a->%minorVersion})';
$string['missingcontentfolder'] = 'No s\'ha trobat una carpeta vàlida per al contingut';
$string['invalidmainjson'] = 'No s\'ha trobat un fitxer "h5p.json" vàlid';
$string['missinglibrary'] = 'No s\'ha trobat la biblioteca requerida {$a->@library}';
$string['missinguploadpermissions'] = "El fitxer que esteu pujant pot contenir biblioteques noves, però no teniu permís per instal·lar-les. Contacteu amb l\'administrador del lloc per resoldre aquest problema.";
$string['invalidlibraryname'] = 'El nom de la biblioteca no és vàlid: {$a->%name}';
$string['missinglibraryjson'] = 'No s\'ha trobat cap fitxer "library.json" amb un format vàlid per a la biblioteca {$a->%name}';
$string['invalidsemanticsjson'] = 'S\'ha trobat un fitxer "semantics.json" amb un format que no és vàlid a la biblioteca {$a->%name}';
$string['invalidlanguagefile'] = 'S\'ha trobat un fitxer d\'idioma {$a->%file} amb un format que no és no vàlid a la biblioteca {$a->%library}';
$string['invalidlanguagefile2'] = 'S\'ha trobat un fitxer d\'idioma {$a->%languageFile} amb un format que no és no vàlid a la biblioteca {$a->%name}';
$string['missinglibraryfile'] = 'No es troba el fitxer "{$a->%file}" a la biblioteca: "{$a->%name}"';
$string['missingcoreversion'] = 'El sistema no ha estat capaç d\'instal·lar el component <em>{$a->%component}</em> del paquet, donat que requereix d\'una versió del connector H5P més actual que la que hi ha ara instal·lada. En aquest lloc s\'està utilitzant la versió {$a->%current}, i la versió requerida és la {$a->%required} o superior. Hauríeu d\'actualitzar el connector i tornar a intentar-ho.';
$string['invalidlibrarydataboolean'] = 'S\'han indicat dades no vàlides per a {$a->%property} a {$a->%library}. S\'esperava un booleà.';
$string['invalidlibrarydata'] = 'S\'han indicat dades no vàlides per a {$a->%property} a {$a->%library}';
$string['invalidlibraryproperty'] = 'No es pot llegir la propietat {$a->%property} de {$a->%library}';
$string['missinglibraryproperty'] = 'La propietat requerida {$a->%property} no es troba present a {$a->%library}';
$string['invalidlibraryoption'] = 'Opció il·legal per a {$a->%option} a {$a->%library}';
$string['addedandupdatelibraries'] = 'S\'han afegit {$a->%new} biblioteques H5P noves i se n\'han actualitzat {$a->%old} d\'antigues.';
$string['addednewlibraries'] = 'S\'han afegit {$a->%new} biblioteques H5P noves.';
$string['updatedlibraries'] = 'S\'han actualitzat {$a->%old} biblioteques H5P.';
$string['missingdependency'] = 'No s\'ha trobat la dependència {$a->@dep} requerida per {$a->@lib}.';
$string['invalidstring'] = 'L\'expressió proporcionada no és vàlida d\'acord amb el sistema d\'expressions regulars utilitzat a "semantics.json". (valor: \"{$a->%value}\", regexp: \"{$a->%regexp}\")';
$string['invalidfile'] = 'El fitxer "{$a->%filename}" no és permès. Només es permeten fitxers amb les extensions següents: {$a->%files-allowed}.';
$string['invalidmultiselectoption'] = 'S\'ha seleccionat una opció que no és vàlida en una activitat de selecció múltiple.';
$string['invalidselectoption'] = 'S\'ha seleccionat una opció que no és vàlida en  una activitat de selecció.';
$string['invalidsemanticstype'] = 'Error intern de l\'H5P: tipus de contingut desconegut "{$a->@type}" a "semantics.json". El contingut s\'eliminarà.';
$string['unabletocreatedir'] = 'No s\'ha pogut crear el directori.';
$string['unabletogetfieldtype'] = 'No s\'ha obtingut el tipus de camp.';
$string['filetypenotallowed'] = 'Els fitxers d\'aquest tipus no estan permesos.';
$string['invalidfieldtype'] = 'El tipus de camp no és vàlid.';
$string['invalidimageformat'] = 'El format de la imatge no és vàlid. Feu servir jpg, png o gif.';
$string['filenotimage'] = 'Aquest fitxer no és una imatge.';
$string['invalidaudioformat'] = 'El format del fitxer d\'àudio no és vàlid. Feu servir mp3 o wav.';
$string['invalidvideoformat'] = 'El format del fitxer de vídeo no és vàlid. Feu servir mp4 o webm.';
$string['couldnotsave'] = 'El fitxer no s\'ha pogut desar.';
$string['couldnotcopy'] = 'El fitxer no s\'ha pogut copiar.';
$string['librarynotselected'] = 'You must select a content type.';

// Welcome messages.
$string['welcomeheader'] = 'Benvingut al món de l\'H5P!';
$string['welcomegettingstarted'] = 'Per iniciar-vos en l\'ús de l\'H5P al Moodle feu un cop d\'ull al nostre <a {$a->moodle_tutorial}>tutorial</a> i proveu els <a {$a->example_content}>exemples de continguts interactius</a> a H5P.org, que us serviran per inspirar-vos.';
$string['welcomecommunity'] = 'Esperem que gaudiu amb l\'H5P i us convidem a participar activament en la nostra comunitat mitjançant els <a {$a->forums}>fòrums</a>.';
$string['welcomecontactus'] = 'Si ens voleu fer algun comentari no dubteu a <a {$a}>contactar amb nosaltres</a>. Ens prenem molt seriosament la retroacció amb els usuaris, que ens ajuda a fer que l\'H5P sigui cada dia millor!';
$string['missingmbstring'] = 'No s\'ha carregat l\'extensió "mbstring" del PHP. L\'H5P necessita aquesta extensió per poder funcionar correctament.';
$string['wrongversion'] = 'La versió de la biblioteca H5P {$a->%machineName} utilitzada en aquest contingut interactiu no és vàlida. L\'objecte conté {$a->%contentLibrary}, i hauria de ser {$a->%semanticsLibrary}.';
$string['invalidlibrarynamed'] = 'La biblioteca H5P {$a->%library} utilitzada en aquest contingut interactiu no és vàlida.';

// Setup errors.
$string['oldphpversion'] = 'La vostra versió de PHP és antiga. L\'H5P necessita com a mínim la versió 5.2 per funcionar correctament. S\'aconsella una versió 5.6 or superior.';
$string['maxuploadsizetoosmall'] = 'El valor actual del paràmetre PHP "upload_max_filesize" és massa petit. Amb la configuració actual és probable que no es puguin pujar fitxers més grans de {$a->%number} MB. Això pot ser problemàtic quan intenteu pujar imatges i vídeos a les activitats H5P. És aconsellable augmentar aquest valor a més de 5 MB.';
$string['maxpostsizetoosmall'] = 'El valor actual del paràmetre PHP "post_max_size" és massa petit. Amb la configuració actual és probable que no es puguin pujar fitxers més grans de {$a->%number} MB. Això pot ser problemàtic quan intenteu pujar imatges i vídeos a les activitats H5P. És aconsellable augmentar aquest valor a més de 5 MB.';
$string['sslnotenabled'] = 'El vostre servidor no té activat el servei SSL. Heu d\'activar l\'SSL per tal que les connexions amb l\'"H5P hub" siguin segures.';
$string['hubcommunicationdisabled'] = 'La comunicació amb l\'"H5P hub" s\'ha desactivat degut a que un o més requeriments de l\'H5P no s\'han pogut satisfer.';
$string['reviseserversetupandretry'] = 'Quan hàgiu revisat la configuració del servidor, proveu a a reactivar la comunicació amb l\'"H5P Hub" a les opcions del mòdul H5P.';
$string['disablehubconfirmationmsg'] = 'Voleu activar l\'"H5P Hub" de totes maneres?';
$string['nowriteaccess'] = 'S\'ha detectat un problema d\'escriptura al servidor. Assegureu-vos que el servidor pugui escriure a la vostra carpeta de dades.';
$string['uploadsizelargerthanpostsize'] = 'El valor actual del paràmetre PHP "upload_max_filesize" és més gran que el de "post_max_size". Això acostuma a provocar problemes en algunes instal·lacions.';
$string['sitecouldnotberegistered'] = 'El lloc no s\'ha pogut registrar a l\'"H5P Hub". Contacteu amb l\'administrador.';
$string['hubisdisableduploadlibraries'] = 'El servei "H5P Hub" s\'ha desactivat fins que aquest problema no es pugui resoldre. Amb tot, podeu pujar manualment fitxers de biblioteca des de la pàgina "Biblioteques H5P".';
$string['successfullyregisteredwithhub'] = 'El vostre lloc s\'ha registrat correctament al servei "H5P Hub".';
$string['sitekeyregistered'] = 'Heu proveït una clau única que us identifica davant el servei "H5P Hub" per rebre actualitzacions. Podeu consultar el valor d\'aquesta clau a la pàgina "Configuració de l\'H5P".';

// Ajax messages.
$string['hubisdisabled'] = 'El servei "H5P Hub" està desactivat. Podeu reactivar-lo a la pàgina de configuració de l\'H5P.';
$string['invalidh5ppost'] = 'No s\'ha pogut enviar la informació a l\'H5P.';
$string['filenotfoundonserver'] = 'El fitxer no es troba al servidor. Comproveu les opcions de pujada de fitxers.';
$string['failedtodownloadh5p'] = 'No s\'ha pogut baixar l\'H5P sol·licitat.';
$string['postmessagerequired'] = 'Per accedir al servei indicat cal enviar un missatge de tipus "post"';

// Licensing.
$string['copyrightinfo'] = 'Informació de Copyright';
$string['years'] = 'Any(s)';
$string['undisclosed'] = 'No revelat';
$string['attribution'] = 'Atribució 4.0';
$string['attributionsa'] = 'Atribució-CompartirIgual 4.0';
$string['attributionnd'] = 'Atribució-NoDerivats 4.0';
$string['attributionnc'] = 'Atribució-NoComercial 4.0';
$string['attributionncsa'] = 'Atribució-NoComercial-CompartirIgual 4.0';
$string['attributionncnd'] = 'Atribució-NoComercial-NoDerivats 4.0';
$string['gpl'] = 'Llicència Pública General (GPL) v3';
$string['pd'] = 'Domini Públic';
$string['pddl'] = 'Dedicació i Llicència de Domini Públic';
$string['pdm'] = 'Marca de Domini Públic';
$string['copyrightstring'] = 'Copyright';
$string['by'] = 'per';
$string['showmore'] = 'Mostra\'n més';
$string['showless'] = 'Mostra\'n menys';
$string['sublevel'] = 'Subnivell';
$string['noversionattribution'] = 'Atribució';
$string['noversionattributionsa'] = 'Atribució-CompartirIgual';
$string['noversionattributionnd'] = 'Atribució-NoDerivats';
$string['noversionattributionnc'] = 'Atribució-NoComercial';
$string['noversionattributionncsa'] = 'Atribució-NoComercial-CompartirIgual';
$string['noversionattributionncnd'] = 'Atribució-NoComercial-NoDerivats';
$string['licenseCC40'] = '4.0 Internacional';
$string['licenseCC30'] = '3.0 No portat';
$string['licenseCC25'] = '2.5 Genèrica';
$string['licenseCC20'] = '2.0 Genèrica';
$string['licenseCC10'] = '1.0 Genèrica';
$string['licenseGPL'] = 'Llicència Pública General (GPL)';
$string['licenseV3'] = 'Versió 3';
$string['licenseV2'] = 'Versió 2';
$string['licenseV1'] = 'Versió 1';
$string['licenseCC010'] = 'CC0 1.0 Universal (CC0 1.0) Dedicació de Domini Públic';
$string['licenseCC010U'] = 'CC0 1.0 Universal';
$string['licenseversion'] = 'Versió de la llicència';

// Embed.
$string['embedloginfailed'] = 'You do not have access to this content. Try logging in.';
