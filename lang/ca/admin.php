<?PHP // $Id$ 
      // admin.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2005041101)


$string['adminseesallevents'] = 'Els administradors veuen tots els esdeveniments';
$string['adminseesownevents'] = 'Els administradors són com els altres usuaris';
$string['blockinstances'] = 'Instàncies';
$string['blockmultiple'] = 'Múltiple';
$string['cachetext'] = 'Durada de la memòria cau de text';
$string['calendarsettings'] = 'Calendari';
$string['change'] = 'Canvia';
$string['configallowcoursethemes'] = 'Si habiliteu aquesta opció, cada curs podrà definir el seu tema. Els temes dels cursos substitueixen qualsevol altra selecció de tema: tema del lloc, de l\'usuari o de la sessió.';
$string['configallowemailaddresses'] = 'Si voleu limitar les noves adreces de correu a certs dominis, especifiqueu-los aquí separats per espais. Tots els altres dominis seran rebutjats. P. ex. <strong>uji.es upc.es xtec.es</strong>';
$string['configallowunenroll'] = 'Si especifiqueu \'Sí\', llavors els estudiants podran cancel·lar quan vulguin la seva inscripció en un curs. Si no, només podran cancel·lar la inscripció els professors i els administradors.';
$string['configallowuserblockhiding'] = 'Voleu que els usuaris puguin ocultar/mostrar els blocs laterals arreu d\'aquest lloc? Aquesta característica fa servir Javascript i galetes per recordar l\'estat de cada bloc. Només afecta la visualització de cada usuari.';
$string['configallowuserthemes'] = 'Si habiliteu aquesta opció, els usuaris podran definir els seus temes. Els temes dels usuaris substitueixen el tema del lloc (però no substitueixen els temes dels cursos).';
$string['configallusersaresitestudents'] = 'Cal considerar com a estudiants TOTS els usuaris en les activitats de la pàgina inicial d\'aquest lloc? Si la resposta és \"Sí\", llavors qualsevol usuari amb un compte confirmat podrà participar com a estudiant en aquestes activitats. Si la resposta és \"No\", llavors només els usuaris que ja siguin membres d\'almenys un curs podran participar en aquestes activitats de la pàgina inicial. Només els administradors i els professors que hi hagin estat assignats poden actuar com a professors d\'aquestes activitats.';
$string['configautologinguests'] = 'Cal fer entrar automàticament com a visitants els usuaris externs que intenten entrar en un curs que permet l\'accés de visitants?';
$string['configcachetext'] = 'Aquest paràmetre pot agilitzar el funcionament de llocs amb molts usuaris o llocs que utilitzen filtres de text. Durant el temps que s\'especifica aquí es reté una còpia del text ja filtrat. Teniu en compte que si el temps especificat és massa breu el funcionament es podria alentir i tot, i que un temps massa prolongat podria implicar que els textos triguessin massa a actualitzar-se.';
$string['configclamactlikevirus'] = 'Tracta els fitxers com a virus';
$string['configclamdonothing'] = 'Dóna els fitxers per bons';
$string['configclamfailureonupload'] = 'Si heu configurat el clam per escanejar els fitxers que es pugin, però està configurat incorrectament o no es pot executar per alguna raó desconeguda, com s\'hauria de comportar? Si trieu \"Tracta els fitxers com a virus\", tots els fitxers es mouran a l\'àrea de quarantena, o seran suprimits. Si trieu \"Dóna els fitxers per bons\", els fitxers es mouran al seu directori de destinació com és normal.';
$string['configcountry'] = 'Si definiu un país aquí, llavors aquest país quedarà seleccionat per defecte en els nous comptes d\'usuari. Si voleu que els usuaris triïn obligatòriament un país, no n\'especifiqueu cap aquí.';
$string['configdbsessions'] = 'Si habiliteu aquest paràmetre, la base de dades emmagatzemarà la informació de les sessions dels usuaris. Això és especialment útil en llocs amb molts usuaris o en llocs que funcionen en clústers de servidors. Per a la majoria de llocs problema és millor no habilitar-lo i utilitzar el disc del servidor en lloc de la base de dades. Teniu en compte que si canvieu ara aquest paràmetre tancareu les sessions de tots els usuaris (la vostra inclosa).';
$string['configdebug'] = 'Si activeu aquest paràmetre s\'incrementarà l\'error_reporting del PHP, de manera que es visualitzaran més avisos. Útil només per a desenvolupadors.';
$string['configdeleteunconfirmed'] = 'Si esteu utilitzant l\'autenticació per correu electrònic, aquest és el període dins del qual s\'acceptarà la resposta dels usuaris. Després d\'aquest període, els comptes no confirmats se suprimeixen.';
$string['configdenyemailaddresses'] = 'Per refusar les adreces de correu de certs dominis, especifiqueu-les aquí de la mateixa manera. Tots els altres dominis seran acceptats. P. ex. <strong>hotmail.com yahoo.com</strong>';
$string['configdigestmailtime'] = 'Les persones que triïn rebre el correu electrònic en format resum, el rebran una vegada al dia. Aquest paràmetre controla a quina hora s\'envia el resum diari (el següent cron que s\'executi després d\'aquesta hora l\'enviarà).';
$string['configdisplayloginfailures'] = 'Aquest paràmetre permet que usuaris seleccionats visualitzin informació sobre intents d\'entrada erronis.';
$string['configenablerssfeeds'] = 'Aquest commutador habilita l\'RSS per a tot el lloc. Per a utilitzar realment l\'RSS, l\'haureu d\'activar també en cada mòdul. Aneu als paràmetres dels mòduls en Administració > Configuració.';
$string['configenablerssfeedsdisabled'] = 'No està disponible perquè l\'RSS està inhabilitat per a tot el lloc. Per habilitar-lo, aneu a la pantalla de variables en Administració > Configuració.';
$string['configerrorlevel'] = 'Trieu el nivell d\'avisos del PHP que voleu visualitzar. Generalment \'Normal\' és la millor opció.';
$string['configextendedusernamechars'] = 'Habiliteu aquest paràmetre perquè els estudiants puguin usar qualsevol caràcter en el seu nom d\'usuari (no afecta els noms actuals). El valor per defecte és \"fals\", la qual cosa limita els noms d\'usuari a caràcters alfanumèrics.';
$string['configfilterall'] = 'Filtra totes les cadenes, inclosos encapçalaments, títols, barres de navegació, etc. Útil sobretot amb el filtre multillenguatge. Si no, pot crear una càrrega extra al servidor sense guanyar res a canvi.';
$string['configfilteruploadedfiles'] = 'Habilitar aquest paràmetre fa que Moodle processi amb els filtres, abans de visualitzar-los, tots els fitxers de text i HTML que es pengin.';
$string['configforcelogin'] = 'Normalment la pàgina inicial del lloc i la llista de cursos es poden llegir sense entrar-hi amb nom d\'usuari i contrasenya. Si voleu imposar que els usuari entrin abans de veure o fer RES en aquest lloc, habiliteu aquest paràmetre.';
$string['configforceloginforprofiles'] = 'Habiliteu aquest paràmetre per imposar que els usuaris entrin amb un compte real (no com a visitants) abans que puguin veure les pàgines dels perfils dels usuaris. Per defecte està inhabilitat (\"fals\"), la qual cosa vol dir que els possibles estudiants poden llegir la informació dels professors de cada curs, i també que els motors de recerca web, com ara Google, poden entrar-hi.';
$string['configframename'] = 'Si teniu incrustat Moodle dins d\'un marc web, escriviu aquí el nom del marc. Si no aquest valor hauria de ser \'_top\'.';
$string['configfullnamedisplay'] = 'Aquest paràmetre defineix el format dels noms quan es visualitzen complets. En la majoria de llocs el valor per defecte és el més adequat: \"Nom + Cognoms\". Però si voleu podeu ocultar els cognoms, o deixar que el paquet d\'idioma decideixi el format (alguns idiomes tenen convencions diferents).';
$string['configgdversion'] = 'Indiqueu la versió instal·lada de GD. La versió que es mostra per defecte és la que s\'ha detectat automàticament. No la canvieu si no esteu segur del que feu.';
$string['confightmleditor'] = 'Trieu si voleu permetre l\'ús de l\'editor HTML integrat. Encara que decidiu permetre\'n l\'ús, aquest editor només apareixerà si l\'usuari fa servir un navegador web compatible. Els usuaris també poden triar no usar-lo.';
$string['configidnumber'] = 'Aquesta opció especifica si: a) no es demana cap número d\'identificació als usuaris; b) es demana un número d\'identificació als usuaris però poden deixar-lo en blanc o c) es demana un número d\'identificació als usuaris i no poden deixar-lo en blanc. Si l\'usuari ha donat un número d\'identificació, aquest número es mostra al seu perfil.';
$string['configintro'] = 'En aquesta pàgina podeu especificar un gran nombre de variables de configuració que contribueixen a fer funcionar Moodle de la manera adequada en el vostre servidor. Però no cal que us amoïneu: els valors per defecte solen anar molt bé i sempre podeu tornar-hi més tard per fer canvis en aquests paràmetres.';
$string['configintroadmin'] = 'En aquesta pàgina hauríeu de configurar el compte de l\'administrador principal que tindrà control complet sobre aquest lloc. Doneu-li un nom i una contrasenya segurs i una adreça de correu electrònic vàlida. Després podreu crear més comptes d\'administració.';
$string['configintrosite'] = 'Aquesta pàgina us permet configurar la pàgina inicial i el nom d\'aquest lloc. Podeu tornar-hi després en qualsevol moment per canviar aquests paràmetres, per mitjà de l\'enllaç \"Paràmetres del lloc\" de la pàgina inicial.';
$string['configintrotimezones'] = 'Aquesta pàgina cercarà informació nova sobre zones horàries (inclosos horaris d\'estiu) i actualitzarà la vostra base de dades. S\'inspeccionaran, per ordre, aquestes ubicacions: $a Aquest procediment generalment és molt segur i no pot perjudicar les instal·lacions normals. Desitgeu actualitzar ara les zones horàries?';
$string['configlang'] = 'Trieu un idioma per defecte per a tot el lloc. Casa usuari podrà triar després el seu idioma. ';
$string['configlangdir'] = 'La majoria d\'idiomes s\'escriuen d\'esquerra a dreta, però alguns, com l\'àrab o l\'hebreu, s\'escriuen de dreta a esquerra.';
$string['configlanglist'] = 'Deixeu en blanc aquest camp per tal que els usuaris puguin triar qualsevol idioma instal·lat. Si voleu abreujar el menú d\'idiomes, introduïu aquí una llista de codis separats per comes. Per exemple: ca,es_es,en,fr,it.';
$string['configlangmenu'] = 'Trieu si voleu visualitzar o no el menú d\'idioma a la pàgina inicial, pàgina d\'entrada, etc. No impedeix que l\'usuari pugui definir el seu idioma preferit en el seu perfil.';
$string['configlocale'] = 'Trieu un <em>locale</em> per a tot el lloc. Afecta el format i l\'idioma de les dates. Heu de tenir instal·lades les dades d\'aquest <em>locale</em> en el vostre sistema operatiu. P. ex. ca_ES, es_ES o en_US. Si no sabeu què triar deixeu-lo en blanc.';
$string['configloginhttps'] = 'Activar aquest paràmetre fa que Moodle utilitzi una connexió https segura en la pàgina d\'entrada, tot proporcionant així una entrada segura, i després torni als URL normals amb http per a mantenir la velocitat normal. ALERTA: aquest paràmetre requereix que l\'https estigui habilitat en el vostre servidor. Si no està habilitat US PODRÍEU QUEDAR FORA SENSE POSSIBILITAT D\'ENTRAR AL VOSTRE LLOC.';
$string['configsectioninterface'] = 'Interfície';
$string['configsectionmail'] = 'Correu';
$string['configsectionmaintenance'] = 'Manteniment';
$string['configsectionmisc'] = 'Miscel·lània';
$string['configsectionoperatingsystem'] = 'Sistema Operatiu';
$string['configsectionpermissions'] = 'Permisos';
$string['configsectionsecurity'] = 'Seguretat';
$string['configsectionuser'] = 'Usuari';
$string['configvariables'] = 'Variables';
$string['confirmation'] = 'Confirmació';
$string['cronwarning'] = 'La <a href=\"cron.php\">seqüència de manteniment cron.php</a> no s\'ha executat en les darreres 24 hores com a mínim.<br />La <a href=\"../doc/?frame=install.html?=cron\">documentació d\'instal·lació</a> explica com podeu automatitzar-ho.';
$string['edithelpdocs'] = 'Edita documents d\'ajuda';
$string['editstrings'] = 'Edita cadenes';
$string['filterall'] = 'Filtra totes les cadenes';
$string['filteruploadedfiles'] = 'Filtrar fitxers penjats';
$string['helpadminseesall'] = 'Veuen els administradors tots els esdeveniments o només aquells que se\'ls hi apliquin?';
$string['helpcalendarsettings'] = 'Configureu diversos aspectes de Moodle relatius al calendari i a les dates i horaris.';
$string['helpstartofweek'] = 'En quin dia comença la setmana?';
$string['helpupcominglookahead'] = 'Quants dies per endavant considera el calendari per determinar els esdeveniments pròxims?';
$string['helpupcomingmaxevents'] = 'Quin nombre màxim d\'esdeveniments pròxims es mostra per defecte als usuaris?';
$string['helpweekenddays'] = 'Quins dies de la setmana es consideren \"cap de setmana\" i es mostren amb un color diferent?';
$string['importtimezones'] = 'Actualitza la llista completa de zones horàries';
$string['sitemaintenancemode'] = 'Mode manteniment';
$string['therewereerrors'] = 'Hi ha errors en aquestes dades';
$string['upgradelogs'] = 'Per a disposar de totes les funcionalitats, els vostres registres s\'han d\'actualitzar. <a href=\"$a\">Més informació</a>';
$string['upgradelogsinfo'] = 'S\'han introduït alguns canvis en la manera d\'emmagatzemar els registres. Per tal de poder veure tots els vostres registres vells per activitat, els vostres registres vells s\'han d\'actualitzar. Depenent del vostre servidor això pot trigar una bona estona (unes quantes hores) i en una instal·lació gran pot carregar una mica la base de dades. Una vegada hàgeu engegat aquest procés haureu de deixar que acabi (mantenint la finestra del navegador oberta). No us amoïneu: el vostre lloc seguirà actiu per als usuaris mentre els registres s\'actualitzen. <br /><br />Voleu actualitzar els registres ara?';
$string['upgradesure'] = 'Els vostres fitxers de Moodle han canviat i esteu a punt d\'actualitzar automàticament el servidor a aquesta versió:
<p><b>$a</b></p>
<p>Després de fer això no podreu tornar enrere.</p> 
<p>Esteu segur que voleu actualitzar aquest servidor a aquesta versió?</p>';
$string['upgradinglogs'] = 'S\'estan actualitzant els registres';

?>
