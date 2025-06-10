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

$string['adminurl'] = 'URL de llançament';
$string['adminurldesc'] = 'L\'URL de llançament de LTI utilitzada per accedir a l\'informe d\'accessibilitat.';
$string['allyclientconfig'] = 'Configuració d\'Ally';
$string['ally:clientconfig'] = 'Accedeix i actualitza la configuració del client';
$string['ally:viewlogs'] = 'Visualitzador de registres d\'Ally';
$string['clientid'] = 'ID del client';
$string['clientiddesc'] = 'L\'ID de client d\'Ally';
$string['code'] = 'Codi';
$string['contentauthors'] = 'Autors del contingut';
$string['contentauthorsdesc'] = 'S\'avaluarà l\'accessibilitat dels fitxers del curs carregats pels administradors i els usuaris assignats a aquests rols. Els fitxers rebran una qualificació d\'accessibilitat. Si aquesta qualificació és baixa, s\'haurà de fer canvis en els fitxers per fer-los més accessibles.';
$string['contentupdatestask'] = 'Tasca d\'actualització del contingut';
$string['curlerror'] = 'Error de cURL: {$a}';
$string['curlinvalidhttpcode'] = 'Codi d\'estat HTTP no vàlid: {$a}';
$string['curlnohttpcode'] = 'No s\'ha pogut verificar el codi d\'estat HTTP';
$string['error:invalidcomponentident'] = 'Identificador de component no vàlid {$a}';
$string['error:pluginfilequestiononly'] = 'Aquest URL només és compatible amb els components de qüestions';
$string['error:componentcontentnotfound'] = 'No s\'ha trobat contingut per a {$a}';
$string['error:wstokenmissing'] = 'Falta el testimoni de servei web. Potser cal que un usuari administrador executi una configuració automàtica?';
$string['excludeunused'] = 'Excloure els fitxers no utilitzats';
$string['excludeunuseddesc'] = 'Ometeu els fitxers que s\'adjunten al contingut HTML, però tenen enllaços o referències a l\'HTML.';
$string['filecoursenotfound'] = 'El fitxer que s\'ha passat no pertany a cap curs';
$string['fileupdatestask'] = 'Passa les actualitzacions de fitxers a Ally';
$string['id'] = 'Identificador';
$string['key'] = 'Clau';
$string['keydesc'] = 'La clau de consumidor de LTI.';
$string['level'] = 'Nivell';
$string['message'] = 'Missatge';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'URL d\'actualitzacions dels fitxers';
$string['pushurldesc'] = 'Envia notificacions sobre actualitzacions de fitxers a aquest URL.';
$string['queuesendmessagesfailure'] = 'S\'ha produït un error mentre s\'enviaven els missatges a l\'AWS SQS. Dades de l\'error: $a';
$string['secret'] = 'Contrasenya';
$string['secretdesc'] = 'El secret LTI.';
$string['showdata'] = 'Mostra les dades';
$string['hidedata'] = 'Oculta les dades';
$string['showexplanation'] = 'Mostra l\'explicació';
$string['hideexplanation'] = 'Oculta l\'explicació';
$string['showexception'] = 'Mostra l\'excepció';
$string['hideexception'] = 'Oculta l\'excepció';
$string['usercapabilitymissing'] = 'L\'usuari que s\'ha proporcionat no té la capacitat necessària per suprimir aquest fitxer.';
$string['autoconfigure'] = 'Configura automàticament el servei web d\'Ally';
$string['autoconfiguredesc'] = 'Creació automàtica de l\'usuari i el servei web d\'Ally.';
$string['autoconfigureconfirmation'] = 'Crea automàticament un rol de servei web i un usuari per Ally i activa el servei web. S\'executaran aquestes accions:<ul><li>crear un rol amb el títol &quot;ally_webservice&quot; i un usuari amb el nom d\'usuari &quot;ally_webuser&quot;</li><li>afegir l\'usuari &quot;ally_webuser&quot; al rol &quot;ally_webservice&quot;</li><li>activar els serveis web</li><li>activar el protocol REST web service</li><li>activar el servei web d\'Ally</li><li>crear un testimoni per al compte &quot;ally_webuser&quot;</li></ul>';
$string['autoconfigsuccess'] = 'Èxit - el servei web d\'Ally s\'ha configurat automàticament.';
$string['autoconfigtoken'] = 'El testimoni de servei de web és com s\'indica:';
$string['autoconfigapicall'] = 'Podeu comprovar que el servei web funciona utilitzant aquest URL:';
$string['privacy:metadata:files:action'] = 'L\'acció que s\'ha pres en el fitxer, per exemple: creat, actualitzat o suprimit.';
$string['privacy:metadata:files:contenthash'] = 'El hash del contingut del fitxer per determinar que és únic.';
$string['privacy:metadata:files:courseid'] = 'L\'ID del curs al qual pertany el fitxer.';
$string['privacy:metadata:files:externalpurpose'] = 'Per integrar-se amb Ally, els fitxers s\'han d\'intercanviar amb Ally.';
$string['privacy:metadata:files:filecontents'] = 'El contingut del fitxer actual s\'envia a Ally perquè s\'avaluï la seva accessibilitat.';
$string['privacy:metadata:files:mimetype'] = 'El tipus de MIME del fitxer, per exemple: text/plain, image/jpeg, etc.';
$string['privacy:metadata:files:pathnamehash'] = 'El camí al hash del nom del fitxer per identificar-lo de manera única.';
$string['privacy:metadata:files:timemodified'] = 'La data de l\'última modificació del camp';
$string['cachedef_annotationmaps'] = 'Emmagatzemar les dades d\'anotacions dels cursos';
$string['cachedef_fileinusecache'] = 'Fitxers Ally a la memòria cau en ús';
$string['cachedef_pluginfilesinhtml'] = 'Fitxers Ally a la memòria cau HTML';
$string['cachedef_request'] = 'Memòria cau de la sol·licitud de filtre d\'Ally';
$string['pushfilessummary'] = 'Resum de les actualitzacions de fitxers d\'Ally.';
$string['pushfilessummary:explanation'] = 'Resum de les actualitzacions de fitxers enviades a Ally.';
$string['section'] = 'Secció {$a}';
$string['lessonanswertitle'] = 'Resposta per la lliçó &quot;{$a}&quot;';
$string['lessonresponsetitle'] = 'Resposta per a la lliçó &quot;{$a}&quot;';
$string['logs'] = 'Registres d\'Ally';
$string['logrange'] = 'Interval del registre';
$string['loglevel:none'] = 'Ningun';
$string['loglevel:light'] = 'Lleuger';
$string['loglevel:medium'] = 'Mitjana';
$string['loglevel:all'] = 'Totes';
$string['logcleanuptask'] = 'Tasca de neteja de registres d\'Ally';
$string['loglifetimedays'] = 'Conserva els registres durant tots aquests dies';
$string['loglifetimedaysdesc'] = 'Conserva els registres d\'Ally durant tots aquests dies. Si es defineix a 0 no s\'esborren mai els registres. Una tasca programada (per defecte) s\'executa diàriament i esborrarà les entrades del registre que tinguin més d\'aquests dies.';
$string['logger:filtersetupdebugger'] = 'Registre de configuració del filtre d’Ally';
$string['logger:pushtoallysuccess'] = 'S\'ha passat correctament al punt final d\'Ally';
$string['logger:pushtoallyfail'] = 'No s\'ha passat al punt final d\'Ally correctament';
$string['logger:pushfilesuccess'] = 'S\'han passat correctament els fitxers al punt final d\'Ally';
$string['logger:pushfileliveskip'] = 'Ha fallat el pas de fitxers en temps real';
$string['logger:pushfileliveskip_exp'] = 'S\'estan ometent fitxers que s\'haurien de passar en temps real per problemes de comunicació. Els fitxers que s\'haurien de passar en temps real es restauraran quan la tasca d\'actualització dels fitxers s\'executi correctament. Reviseu la configuració.';
$string['logger:pushfileserror'] = 'No s\'ha passat al punt final d\'Ally correctament';
$string['logger:pushfileserror_exp'] = 'Els errors associats amb les actualitzacions de contingut passen als serveis d\'Ally.';
$string['logger:pushcontentsuccess'] = 'S\'ha passat correctament el contingut al punt final d\'Ally';
$string['logger:pushcontentliveskip'] = 'Error en passar el contingut en temps real';
$string['logger:pushcontentliveskip_exp'] = 'Ometent contingut que s\'hauria de passar en temps real per problemes de comunicació. El contingut que s\'hauria de passar en temps real es restaurarà quan la tasca d\'actualització de contingut s\'executi correctament. Reviseu la vostra configuració.';
$string['logger:pushcontentserror'] = 'No s\'ha passat al punt final d\'Ally correctament';
$string['logger:pushcontentserror_exp'] = 'Els errors associats amb les actualitzacions de contingut passen als serveis d\'Ally.';
$string['logger:addingconenttoqueue'] = 'S\'està afegint contingut a la cua per passar';
$string['logger:annotationmoderror'] = 'El mòdul d\'anotació del contingut d\'Ally ha fallat.';
$string['logger:annotationmoderror_exp'] = 'El mòdul no s\'ha identificat correctament.';
$string['logger:failedtogetcoursesectionname'] = 'No s\'ha pogut aconseguir el nom de la secció del curs';
$string['logger:moduleidresolutionfailure'] = 'No s\'ha pogut resoldre l\'ID del mòdul';
$string['logger:cmidresolutionfailure'] = 'No s\'ha pogut resoldre l\'ID del mòdul del curs';
$string['logger:cmvisibilityresolutionfailure'] = 'No s\'ha pogut resoldre la visibilitat del mòdul del curs';
$string['courseupdatestask'] = 'Passa els esdeveniments del curs a Ally';
$string['logger:pushcoursesuccess'] = 'S\'han passat correctament els esdeveniments del curs al punt final d\'Ally';
$string['logger:pushcourseliveskip'] = 'Error en passar l\'esdeveniment del curs en temps real';
$string['logger:pushcourseerror'] = 'Error en passar l\'esdeveniment del curs en temps real';
$string['logger:pushcourseliveskip_exp'] = 'Ometent esdeveniments del curs que s\'haurien de passar en temps real per problemes de comunicació. Els esdeveniments del curs que s\'haurien de passar en temps real es restauraran quan la tasca d\'actualització d\'esdeveniments del curs s\'executi correctament. Reviseu la vostra configuració.';
$string['logger:pushcourseserror'] = 'No s\'ha passat al punt final d\'Ally correctament';
$string['logger:pushcourseserror_exp'] = 'Els errors associats amb les actualitzacions del curs passen als serveis d\'Ally.';
$string['logger:addingcourseevttoqueue'] = 'S\'estan afegint esdeveniments de curs a la cua per passar';
$string['logger:cmiderraticpremoddelete'] = 'Problemes d\'eliminació prèvia de l\'ID de mòdul del curs.';
$string['logger:cmiderraticpremoddelete_exp'] = 'El mòdul no s\'ha identificat correctament, o bé no existeix perquè s\'ha eliminat la secció, o bé hi ha algun altre factor que ha activat l\'eliminació i no es pot trobar.';
$string['logger:servicefailure'] = 'S\'ha produït un error en consumir el servei.';
$string['logger:servicefailure_exp'] = '<br>Classe: {$a->class}<br>Paràmetres: {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'S\'ha produït un error en assignar la capacitat d\'arquetipus de professor al rol ally_webservice.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Capacitat: {$a->cap}<br>Permís: {$a->permission}';
$string['deferredcourseevents'] = 'Envia els esdeveniments del curs diferits';
$string['deferredcourseeventsdesc'] = 'Permet l\'enviament d\'esdeveniments del curs emmagatzemats, que s\'han acumulat durant l\'error de comunicació amb Ally.';
