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
 * Strings for plugin 'reminders', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package   local_reminders
 * @copyright 2012 Isuru Madushanka Weerarathna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 $string['activityconfduein'] = 'Venciment en';
 $string['activityconfexplicitenable'] = 'Activació de recordatori explícit';
 $string['activityconfexplicitenabledesc'] = 'Si està marcat, els professors o autoritats rellevants han d\'habilitar <strong>explícitament</strong> els recordatoris per a cada activitat a la pàgina de configuració de recordatoris del curs. A causa d\'això, tots els recordatoris d\'activitats estaran desactivats per defecte, independentment del calendari definit a continuació. Aquesta configuració no afectarà els recordatoris de retard de totes maneres.';
 $string['activityconfexplicitenablehint'] = 'Els administradors del lloc han desactivat l\'enviament de recordatoris d\'activitats per defecte. Això vol dir que els professors han de <em>habilitar explícitament</em> els recordatoris per a les activitats d\'aquest curs que vulguin enviar.';
 $string['activityconfupcomingactivities'] = 'Activitats properes';
 $string['activityconfupcomingactivitiesdesc'] = 'Els recordatoris no s\'enviaran per a activitats desmarcades.';
 $string['activityconfnoupcomingactivities'] = 'No hi ha activitats properes per aquest curs.';
 $string['activitydueopenahead'] = 'Obrir activitat abans de l\'hora:';
 $string['activitydueopenaheaddesc'] = 'Dies abans de l\'obertura de l\'activitat per enviar els recordatoris. Aquesta configuració només és vàlida si s\'han activat els recordatoris d\'obertura d\'activitat a la configuració superior.';
 $string['activityopeningseparation'] = 'Separar les obertures d\'activitats:';
 $string['activityopeningseparationdesc'] = 'Mostra les obertures d\'activitats com una entrada separada a la pàgina de configuració de recordatoris del curs.';
 $string['activityremindersboth'] = 'Per a obertures i tancaments';
 $string['activityremindersonlyopenings'] = 'Només per a obertures d\'activitats';
 $string['activityremindersonlyclosings'] = 'Només per a tancaments d\'activitats';
 $string['activityignoreincompletes'] = 'No enviar recordatoris quan es completi:';
 $string['activityignoreincompletesdetails'] = 'Si està marcat, no s\'enviaran recordatoris si l\'activitat ja està completada pel usuari, <strong>abans</strong> que acabi l\'activitat.';
 $string['admintreelabel'] = 'Recordatoris';
 $string['calendareventupdatedprefix'] = 'ACTUALITZAT';
 $string['calendareventremovedprefix'] = 'ELIMINAT';
 $string['calendareventcreatedprefix'] = 'AFEGIT';
 $string['calendareventoverdueprefix'] = 'ENDARRERIT';
 $string['caleventchangedheading'] = 'Canvis als esdeveniments del calendari';
 $string['caleventchangedheadingdetails'] = 'Aquestes configuracions es verificaran <strong>abans</strong> de considerar el tipus d\'esdeveniment individual.';
 $string['categoryheading'] = 'Recordatoris per a esdeveniments de categoria de curs';
 $string['categorynosendforended'] = 'Sense recordatoris per a cursos completats:';
 $string['categorynosendforendeddescription'] = 'Si està marcat, no s\'enviaran recordatoris per a cursos completats.';
 $string['contentdescription'] = 'Descripció';
 $string['contenttypecategory'] = 'Categoria';
 $string['contenttypecourse'] = 'Curs';
 $string['contenttypeactivity'] = 'Activitat';
 $string['contenttypegroup'] = 'Grup';
 $string['contenttypeuser'] = 'Usuari';
 $string['contenttypelocation'] = 'On';
 $string['contentwhen'] = 'Quan';
 $string['courseheading'] = 'Recordatoris per a esdeveniments del curs';
 $string['custom'] = 'Personalitzat';
 $string['customschedulefallback'] = 'Pla personalitzat de reserva';
 $string['customschedulefallbackdesc'] = 'Si està marcat, els plans personalitzats tornaran al valor especificat per a <strong>tipus d\'esdeveniments desconeguts</strong>.';
 $string['days7'] = '7 Dies';
 $string['days3'] = '3 Dies';
 $string['days1'] = '1 Dia';
 $string['dueheading'] = 'Recordatoris per a esdeveniments d\'activitat';
 $string['emailconfigsheading'] = 'Personalització d\'emails de recordatori';
 $string['emailfootercustomname'] = 'Peu de pàgina personalitzat';
 $string['emailfootercustomnamedesc'] = 'Especifica el contingut del peu de pàgina que s\'inserirà en cada missatge de recordatori per correu electrònic. Si aquest contingut està buit i el peu de pàgina per defecte està desactivat, el peu de pàgina es suprimirà completament dels recordatoris.';
 $string['emailfooterdefaultname'] = 'Utilitzar peu de pàgina per defecte';
 $string['emailfooterdefaultnamedesc'] = 'Si està marcat, el peu de pàgina per defecte del correu de recordatori contenirà un enllaç al calendari de Moodle. Si no, es farà servir el contingut proporcionat al peu de pàgina personalitzat.';
 $string['emailheadercustomname'] = 'Capçalera personalitzada per a l\'email';
 $string['emailheadercustomnamedesc'] = 'Especifica el contingut de la capçalera que s\'inserirà en cada missatge de recordatori per correu electrònic. Això es pot utilitzar per afegir la marca al correu electrònic.';
 $string['enabled'] = 'Habilitat';
 $string['enabledoverdue'] = 'Activar endarreriment';
 $string['enableddescription'] = 'Habilitar/deshabilitar el complement de recordatoris';
 $string['enabledchangedevents'] = 'Enviar quan es canviï un esdeveniment:';
 $string['enabledremovedevents'] = 'Enviar quan es tregui un esdeveniment:';
 $string['enabledaddedevents'] = 'Enviar quan es creï un esdeveniment:';
 $string['enabledchangedeventsdescription'] = 'Indica si s\'han d\'enviar recordatoris quan s\'actualitza un esdeveniment del calendari.';
 $string['enabledremovedeventsdescription'] = 'Indica si s\'han d\'enviar recordatoris quan s\'elimina un esdeveniment del calendari.';
 $string['enabledaddedeventsdescription'] = 'Indica si s\'han d\'enviar recordatoris quan es crea un esdeveniment del calendari.';
 $string['enabledforcalevents'] = 'Habilitar per als canvis d\'esdeveniments del calendari:';
 $string['enabledforcaleventsdescription'] = 'Habilitar l\'enviament de recordatoris per a aquest tipus quan es crea, elimina o actualitza un esdeveniment al calendari.';
 $string['eventtypegradingdue'] = 'Venciment de qualificació';
 $string['eventtypeexpectcompletionon'] = 'Espera de finalització';
 $string['eventtypeopen'] = 'L\'activitat s\'obre';
 $string['eventtypeclose'] = 'L\'activitat es tanca';
 $string['explaincategoryheading'] = 'Configuració de recordatoris per a esdeveniments de categoria de curs.';
 $string['explaincourseheading'] = 'Configuració de recordatoris per a esdeveniments de curs. Aquests esdeveniments provenen d\'un curs.';
 $string['explaindueheading'] = 'Configuració de recordatoris per a esdeveniments d\'activitat. Aquests esdeveniments provenen d\'activitats/mòduls dins d\'un curs.';
 $string['explaingroupheading'] = 'Configuració de recordatoris per a esdeveniments de grup. Aquests esdeveniments són només per a un grup específic.';
 $string['explaingroupshowname'] = 'Indica si s\'ha d\'incluir el nom del grup al missatge enviat.';
 $string['explainrolesallowedfor'] = 'Esculli quins usuaris amb els rols especificats poden rebre els recordatoris.';
 $string['explainsendactivityreminders'] = 'Indica en quin estat de l\'activitat s\'han de enviar els recordatoris.';
 $string['explainsiteheading'] = 'Configuració de recordatoris per a esdeveniments de lloc. Aquests esdeveniments són rellevants per a tots els usuaris del lloc.';
 $string['explainuserheading'] = 'Configuració de recordatoris per a esdeveniments d\'usuari. Aquests esdeveniments són individuals per a cada usuari.';
 $string['excludedmodules'] = 'Mòduls exclosos:';
 $string['excludedmodulesdesc'] = 'Els recordatoris no s\'enviaran si un esdeveniment es genera a partir dels mòduls seleccionats més amunt. Aquesta configuració és global i s\'aplica per a qualsevol tipus d\'esdeveniment.';
 $string['filterevents'] = 'Filtrar esdeveniments del calendari:';
 $string['filtereventsdescription'] = 'Quins esdeveniments del calendari s\'han de filtrar i enviar recordatoris per ells.';
 $string['filtereventsonlyhidden'] = 'Només esdeveniments ocults al calendari';
 $string['filtereventsonlyvisible'] = 'Només esdeveniments visibles al calendari';
 $string['filtereventssendall'] = 'Tots els esdeveniments';
 $string['groupheading'] = 'Recordatoris per a esdeveniments de grup';
 $string['groupshowname'] = 'Mostrar nom del grup al missatge:';
 $string['messageprovider:reminders_course'] = 'Notificacions de recordatoris per a esdeveniments de curs';
 $string['messageprovider:reminders_coursecategory'] = 'Notificacions de recordatoris per a esdeveniments de categoria de curs';
 $string['messageprovider:reminders_due'] = 'Notificacions de recordatoris per a esdeveniments d\'activitat';
 $string['messageprovider:reminders_group'] = 'Notificacions de recordatoris per a esdeveniments de grup';
 $string['messageprovider:reminders_site'] = 'Notificacions de recordatoris per a esdeveniments de lloc';
 $string['messageprovider:reminders_user'] = 'Notificacions de recordatoris per a esdeveniments d\'usuari';
 $string['messagetitleprefix'] = 'Prefix del títol del missatge:';
 $string['messagetitleprefixdescription'] = 'Aquest text s\'inserirà com a prefix (dins de corxets) al títol de cada missatge de recordatori enviat.';
 $string['moodlecalendarname'] = 'Calendari Moodle';
 $string['overduemessage'] = 'Aquesta activitat és endarrerida!';
 $string['plugindisabled'] = 'El complement està desactivat per l\'administrador.';
 $string['pluginname'] = 'Recordatoris d\'esdeveniments';
 $string['privacy:metadata'] = 'El complement de recordatoris d\'esdeveniments no emmagatzema cap dada personal.';
 $string['overdueactivityreminders'] = 'Recordatoris d\'activitats endarrerides:';
 $string['overdueactivityremindersdescription'] = 'Si està marcat, s\'enviaran recordatoris als usuaris que tenen l\'activitat endarrerida.';
 $string['overduewarnmessage'] = 'Missatge d\'advertència d\'endarreriment:';
 $string['overduewarnmessagedescription'] = 'Escriviu un <strong>text senzill</strong> que s\'inserirà dins del correu d\'endarreriment en color vermell. Si això està buit, no es mostrarà cap missatge. Això només s\'activarà si els correus d\'endarreriment estan habilitats.';
 $string['overduewarnprefix'] = 'Prefix del títol d\'endarreriment:';
 $string['overduewarnprefixdescription'] = 'Escriviu un <strong>prefix senzill</strong> que s\'inserirà al títol dels correus d\'endarreriment. Si això està buit, no es posarà cap prefix. Això només s\'activarà si els correus d\'endarreriment estan habilitats.';
 $string['reminderdaysahead'] = 'Enviar abans de:';
 $string['reminderdaysaheadcustom'] = 'Horari personalitzat:';
 $string['reminderdaysaheadschedule'] = 'Horari';
 $string['reminderdaysaheadcustomdetails'] = 'A més, especifiqui l\'horari desitjat per enviar els recordatoris amb antelació per un esdeveniment.';
 $string['reminderfrom'] = 'Recordatori de';
 $string['reminderstask'] = 'Tasques de recordatori locals';
 $string['reminderstaskclean'] = 'Netejar els registres de tasques de recordatoris locals';
 $string['rolesallowedfor'] = 'Rols permesos per a';
 $string['sendactivityreminders'] = 'Recordatoris d\'activitat:';
 $string['sendas'] = 'Enviar com a:';
 $string['sendasadmin'] = 'Com a Administrador del lloc';
 $string['sendasdescription'] = 'Especifiqueu com qui s\'han d\'enviar aquests correus de recordatori.';
 $string['sendasnametitle'] = 'Nom sense resposta:';
 $string['sendasnamedescription'] = 'Especifiqueu el nom d\'usuari per a la visualització dels correus de recordatori quan s\'enviïn com a usuari sense resposta.';
 $string['sendasnoreply'] = 'Adreça de resposta sense resposta';
 $string['showmodnameintitle'] = 'Mostrar nom del mòdul a l\'assumpte del correu';
 $string['showmodnameintitledesc'] = 'Si està marcat, es posarà el nom del mòdul corresponent a l\'assumpte del correu de recordatori.';
 $string['siteheading'] = 'Recordatoris per a esdeveniments de lloc';
 $string['taskreminder'] = 'Tasques de recordatori';
 $string['titlesubjectprefix'] = 'Recordatori';
 $string['userheading'] = 'Recordatoris per a esdeveniments d\'usuari';
 $string['useservertimezone'] = "Utilitzar la zona horària del servidor";
