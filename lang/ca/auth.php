<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 (2004052500)


$string['auth_dbdescription'] = 'Aquest mètode utilitza una taula d\'una base de dades externa per comprovar si un nom d\'usuari i una contrasenya són vàlids. Si el compte és nou, aleshores també es pot copiar en Moodle informació d\'altres camps.';
$string['auth_dbextrafields'] = 'Aquests camps són opcionals. Podeu triar d\'omplir alguns camps d\'usuari del Moodle amb informació dels <B>camps de la base de dades externa</B> especificats aquí. <P>Si els deixeu en blanc s\'utilitzaran valors per defecte.<P>En tot cas, l\'usuari podrà editar tots aquests camps quan es connecti.';
$string['auth_dbfieldpass'] = 'Nom del camp que conté la contrasenya';
$string['auth_dbfielduser'] = 'Nom del camp que conté el nom d\'usuari';
$string['auth_dbhost'] = 'L\'ordinador que allotja el servidor de la base de dades.';
$string['auth_dbname'] = 'El nom de la base de dades';
$string['auth_dbpass'] = 'Contrasenya corresponent al nom d\'usuari anterior';
$string['auth_dbpasstype'] = 'Especifiqueu el format que utilitza el camp de la contrasenya. El xifratge MD5 és útil per connectar-se a altres aplicacions web comunes com ara PostNuke';
$string['auth_dbtable'] = 'Nom de la taula';
$string['auth_dbtitle'] = 'Utilitza una base de dades externa';
$string['auth_dbtype'] = 'Tipus de base de dades (vg. la <A HREF=../lib/adodb/readme.htm#drivers>documentació sobre ADOdb</A>)';
$string['auth_dbuser'] = 'Nom d\'usuari amb accés de lectura a la base de dades';
$string['auth_emaildescription'] = 'La confirmació per correu electrònic és el mètode d\'autenticació per defecte. Quan l\'usuari es registra i tria el seu nom d\'usuari i contrasenya, se li envia un missatge per confirmar les dades. Aquest missatge conté un enllaç segur a una pàgina en la qual l\'usuari pot confirmar el seu compte. En les connexions següents simplement es compara el nom d\'usuari i la contrasenya amb els valors guardats a la base de dades de Moodle.';
$string['auth_emailtitle'] = 'Autenticació basada en el correu electrònic';
$string['auth_imapdescription'] = 'Aquest mètode utilitza un servidor IMAP per comprovar si un nom d\'usuari i una contrasenya són vàlids.';
$string['auth_imaphost'] = 'L\'adreça del servidor IMAP. Ha de ser el número IP, no el nom del DNS.';
$string['auth_imapport'] = 'El número de port del servidor IMAP. Generalment és el 143 o el 993.';
$string['auth_imaptitle'] = 'Utilitza un servidor IMAP';
$string['auth_imaptype'] = 'Tipus de servidor IMAP. Els servidors IMAP poden tenir diferents tipus d\'autenticació i negociació.';
$string['auth_ldap_bind_dn'] = 'Si voleu utilitzar el bind-user per cercar usuaris, especifiqueu-ho aquí. Una cosa semblant a \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Contrasenya del bind-user.';
$string['auth_ldap_contexts'] = 'Llista de contextos en què estan ubicats els usuaris. Separeu els contextos amb \';\'. Per exemple: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Si activeu la creació d\'usuaris mitjançant confirmació per correu electrònic, especifiqueu en quin context s\'han de crear els usuaris. Aquest context ha de ser diferent del d\'altres usuaris per tal de prevenir problemes de seguretat. No cal afegir aquest context a ldap_context-variable. Moodle cercarà els usuaris en aquest context automàticament.';
$string['auth_ldap_creators'] = 'Llista de grups als membres dels quals els és permès  crear nous cursos. Separeu els grups amb \';\'. Generalment una cosa semblant a \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Especifiqueu l\'hoste LDAP en format URL, per exemple \'ldap://ldap.myorg.com/\' o \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Especifiqueu l\'atribut de membre de l\'usuari, quan els usuaris pertanyen a un grup. Generalment \'member\'';
$string['auth_ldap_search_sub'] = 'Poseu el valor <> 0 si voleu cercar els usuaris en subcontextos.';
$string['auth_ldap_update_userinfo'] = 'Passar les dades de l\'usuari (nom, cognoms, adreça...) de LDAP a Moodle. Informació sobre mapatge en /auth/ldap/attr_mappings.php';
$string['auth_ldap_user_attribute'] = 'L\'atribut utilitzat per anomenar/cercar usuaris. Generalment \'cn\'.';
$string['auth_ldap_version'] = 'La versió del protocol LDAP que està utilitzant el servidor.';
$string['auth_ldapdescription'] = 'Aquest mètode proporciona autenticació contra un servidor LDAP extern.

                                  Si un nom d\'usuari i una contrasenya són vàlids, Moodle crea una entrada per a un nou usuari 

                                  a la seva base de dades. Aquest mòdul pot llegir atributs de l\'usuari del LDAP i omplir 

                                  els camps corresponents de Moodle. En connexions successives només es comproven  

                                  el nom d\'usuari i la contrasenya.';
$string['auth_ldapextrafields'] = 'Aquests camps són opcionals. Podeu triar d\'omplir alguns camps d\'usuari de Moodle amb informació dels <B>camps LDAP</B> especificats aquí. <P>Si els deixeu en blanc, aleshores s\'utilitzaran valors per defecte.<P>En tot cas, l\'usuari podrà editar tots aquests camps quan es connecti.';
$string['auth_ldaptitle'] = 'Utilitza un servidor LDAP';
$string['auth_manualdescription'] = 'Aquest mètode impedeix que els usuaris puguin crear-se comptes. Tots els comptes han de ser creats manualment per l\'usuari administrador.';
$string['auth_manualtitle'] = 'Només comptes manuals';
$string['auth_multiplehosts'] = 'Podeu especificar diversos ordinadors (p. e. host1.com; host2.com; host3.com)';
$string['auth_nntpdescription'] = 'Aquest mètode utilitza un servidor NNTP per comprovar si un nom d\'usuari i una contrasenya són vàlids.';
$string['auth_nntphost'] = 'L\'adreça del servidor NNTP. Ha de ser el número IP, no el nom del DNS.';
$string['auth_nntpport'] = 'Número de port del servidor (el 119 és el més habitual)';
$string['auth_nntptitle'] = 'Utilitza un servidor NNTP';
$string['auth_nonedescription'] = 'Els usuaris es poden registrar i crear comptes immediatament vàlids, sense cap mena d\'autenticació contra un servidor extern ni confirmar la identitat per correu electrònic. Teniu compte amb aquesta  opció - penseu en els problemes de seguretat i d\'administració que pot causar.';
$string['auth_nonetitle'] = 'Sense autenticació';
$string['auth_pop3description'] = 'Aquest mètode utilitza un servidor POP3 per comprovar si un nom d\'usuari i una contrasenya són vàlids.';
$string['auth_pop3host'] = 'L\'adreça del servidor POP3. Ha de ser el número IP, no el nom del DNS.';
$string['auth_pop3port'] = 'Número de port del servidor (el 110 és el més habitual)';
$string['auth_pop3title'] = 'Utilitza un servidor POP3';
$string['auth_pop3type'] = 'Tipus de servidor. Si el vostre servidor utilitza seguretat per certificat, trieu pop3cert.';
$string['auth_user_create'] = 'Activa la creació d\'usuaris';
$string['auth_user_creation'] = 'Els nous usuaris (anònims) poden crear comptes d\'usuari en la font d\'autenticació externa i confirmar-los via correu electrònic. Si activeu aquesta opció, recordeu de configurar també opcions específiques del mòdul per a la creació d\'usuaris.';
$string['auth_usernameexists'] = 'El nom d\'usuari elegit ja existeix. Sisplau trieu-ne un altre.';
$string['authenticationoptions'] = 'Opcions d\'autenticació';
$string['authinstructions'] = 'Aquí podeu posar instruccions per als vostres usuaris, per tal que sàpiguen quin nom d\'usuari i quina contrasenya han d\'utilitzar. El text apareixerà a la pàgina d\'entrada. Si el deixeu en blanc no hi haurà instruccions.';
$string['changepassword'] = 'URL de canvi de contrasenya';
$string['changepasswordhelp'] = 'Aquí podeu especificar una adreça en la qual els usuaris puguin recuperar o canviar la seua contrasenya si se n\'han oblidat. Aquesta opció apareixerà en forma de botó a la pàgina d\'entrada. Si la deixeu en blanc no apareixerà el botó.';
$string['chooseauthmethod'] = 'Trieu un mètode d\'autenticació: ';
$string['guestloginbutton'] = 'Botó d\'entrada d\'invitats';
$string['instructions'] = 'Instruccions';
$string['md5'] = 'Xifratge MD5';
$string['plaintext'] = 'Text net';
$string['showguestlogin'] = 'Podeu ocultar o mostrar el botó d\'entrada com a invitat a la pàgina d\'entrada.';

?>
