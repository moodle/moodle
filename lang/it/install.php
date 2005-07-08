<?PHP // $Id$ 
      // install.php - created with Moodle 1.6 development (2005060201)


$string['admindirerror'] = 'La directory di amministrazione specificata non è corretta';
$string['admindirname'] = 'Directory di amministrazione';
$string['admindirsetting'] = 'Pochissimi web server utilizzano /admin come un URL speciale per accedere al pannello di controllo o altro. Sfortunatamente questo collide con la posizione standard per le pagine di amministrazione di Moodle. È possibile risolvere questo problema rinominando la directory di amministrazione nella vostra installazione, e inserendo il nuovo nome qui. Per esempio:<p><b>moodleadmin</b></p>
Questo sistemerà i collegamenti di amministrazione in Moodle.';
$string['caution'] = 'Attenzione';
$string['chooselanguage'] = 'Scegli la lingua';
$string['compatibilitysettings'] = 'Controllo impostazioni PHP...';
$string['configfilenotwritten'] = 'Il sistema di installazione non è in grado di creare il file config.php contenente le vostre impostazioni, probabilmente perché la directory di Moodle non è scrivibile. È possbile copiare manualmente il codice seguente in un file chiamato config.php nella directory principale di Moodle.';
$string['configfilewritten'] = 'Il config.php è stato creato correttamente';
$string['configurationcomplete'] = 'Configurazione completata';
$string['database'] = 'Base di dati';
$string['databasecreationsettings'] = 'È necessario configurare le impostazioni della base dati dove Moodle salvac la maggior parte dei dati. Questa base dati sarà creata automaticamente dall\'installatore di Moodle4Windows con le impostazioni specificate sotto.<br /><br /><br />
<b>Tipo:</b> impostato a \"mysql\" dall\'installatore<br />
<b>Server:</b> impostato a \"localhost\" dall\'installatore<br />
<b>Nome:</b> nome della base dati, es. moodle<br />
<b>Utente:</b> impostato a \"root\" dall\'installatore<br />
<b>Password:</b> la vostra password della base dati<br />
<b>Prefisso Tabelle:</b> prefisso opzionale che viene utilizzato per tutti i nomi delle tabelle';
$string['databasesettings'] = '<p>È necessario configurare la base dati dove la maggior parte dei dati di Moodle vengono salvati. Questa base di dati deve essere già stata creata e un utente con password deve essere stato creato per accedervi.</p>
<b>Tipo:</b> es. mysql o postgres7<br/>
<b>Server della base dati:</b> es. localhost o db.isp.com<br />
<b>Base di dati:</b>il nome della base dati creata es. moodle<br/>
<b>Utente:</b>un utente accreditato sulla base dati<br/>
<b>Password:</b>la password dell\'utente accreditato<br/>
<b>Prefisso tabelle:</b>prefisso opzionale utilizza in tutti nomi delle tabelle es. mld_';
$string['dataroot'] = 'Directory dati';
$string['datarooterror'] = 'La \'Directory dati\' specificata non può essere trovata o creata. È possibile correggere il percorso o crearla manualmente.';
$string['dbconnectionerror'] = 'Non è possibile connettersi alla base dati specificata. Controllare le impostazioni della base dati.';
$string['dbcreationerror'] = 'Errore durante la creazione della base dati. Non è possibile creare una base dati con le impostazioni fornite.';
$string['dbhost'] = 'Server della base dati';
$string['dbpass'] = 'Password';
$string['dbprefix'] = 'Prefisso tabelle';
$string['dbtype'] = 'Tipo';
$string['directorysettings'] = '<p>Confermare le locazioni di questa installazione di Moodle.</p>
<p><b>Indirizzo Web:</b>
Specificare l\'indirizzo completo dove Moodle sarà locato.
Se il vostro sito web è accessibile tramite indirizzi multipli scegliere quello più naturale per gli studenti. Non aggiungere lo slash(/) finale.</p>
<p><b>Directory di Moodle:</b>
Specificare il percorso completo per questa installazione. Controllare che la capitalizzazione sia corretta.</p>
<p><b>Directory dati:</b>
È necessario un posto dove Moodle può salvare i file inviati. Questa directory deve essere leggibile e SCRIVIBILE dall\'utente del server web (normalmente \'nobody\' o \'apache\'), ma non dovrebbe essere direttamente accessibile via web.</p>';
$string['dirroot'] = 'Directory di Moodle';
$string['dirrooterror'] = 'L\'impostazione \'Directory di Moodle\' sembra essere scorretta - non è possibile trovare un\'installazione di Moodle nel percorso specificato. Il valore sotto è stato ripristinato.';
$string['download'] = 'Download';
$string['fail'] = 'Fallito';
$string['fileuploads'] = 'Invio file';
$string['fileuploadserror'] = 'Questo deve essere impostato a on';
$string['fileuploadshelp'] = '<p>L\'invio dei file sembra essere disabilitato sul vostro server.</p>
<p>Moodle può essere installato, ma senza questa caratteristica, non si potrà inviare file per i corsi o nuove immagini degli utenti.</p>
<p>Per abilitare l\'invio dei file è necessario modificare il file php.ini sul vostro sistema e cambiare l\'impostazione <b>file_uploads</b> a \'on\'.</p>';
$string['gdversion'] = 'Versione GD';
$string['gdversionerror'] = 'La libreria GD deve essere presente per elaborare e creare immagini';
$string['gdversionhelp'] = '<p>Sul vostro server sembra non essere installato il supporto per le librerie GD.</p>
<p>GD è una libreria che è richiesta dal PHP per permettere a Moodle di elaborare le immagini (come le icone dei profili utente) e creare nuove immagini (come i grafici dei log). Moodle continuerà a funzionare senza GD - queste caratteristiche non saranno disponibili sulla vostra installazione.</p>
<p>Per aggiungere GD al PHP su sistemi operativi Unix/Linux, compilare il PHP utilizzando l\'opzione --with-gd.</p>
<p>Su Windows normalmente è possibile modificare il file php.ini e togliere il commento dalla linea che contiene libgd.dll.</p>';
$string['installation'] = 'Installazione';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Questo deve essere impostato a off';
$string['magicquotesruntimehelp'] = '<p>L\'opzione Magic Quotes Run Time deve essere impostata a off per permettere a Moodle di funzionare correttamente.</p>
<p>Normalmente questa è impostata a off ... controllate l\'impostazione <b>magic_quotes_runtime</b> nel file php.ini.</p>
<p>Se non vi è possibile modificare il file php.ini, è possibile inserire la linea seguente in un file chiamato .htaccess nella Directory di Moodle: <blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>';
$string['memorylimit'] = 'Limite memoria';
$string['memorylimiterror'] = 'Il limite di memoria del PHP è impostato a un valore basso ... potrebbero verificarsi probremi in futuro.';
$string['memorylimithelp'] = '<p>Il limite della memoria assegnata a PHP attualmente è $a.</p>
<p>Questo può dare problemi a Moodle in futuro, specialmente se avete molti moduli abilitati e molti utenti.</p>
<p>Vi raccomandiamo di impostare il PHP con un limite più alto se possibile, ad esempio 16M.
Ci sono diversi modi che potete provare:
<ol>
<li>Se possibile, ricompilare il PHP con l\'opzione <i>--enable-memory-limit</i>.
Questo permetterà a Moodle di impostare il limite di memoria da solo.</li>
<li>Se avete accesso al file php.ini, è possibile modificare l\'impostazione <b>memory_limit</b> a un valore tipo 16M. Se non avete l\'accesso potete chiedere al vostro amministratore di sistema di farlo.</li>
<li>Su alcuni server PHP è possibile creare un file .htaccess nella Directory di Moodle che contenga questa linea:
<blockquote>php_value memory_limit 16M</blockquote>
<p>Tuttavia, su alcuni server questo impedirà a <b>tutte</b> le pagine PHP di funzionare (vedrete degli errori quando visualizzerete le pagine) cosi dovrete rimuovere il file .htaccess.</li></ol>';
$string['mysqlextensionisnotpresentinphp'] = 'Il PHP non è stato correttamente configurato con l\'estensione di MySQL. Controllate il vostro php.ini o ricompilate il PHP.';
$string['pass'] = 'Passato';
$string['phpversion'] = 'Versione PHP';
$string['phpversionerror'] = 'La versione del PHP deve essere come minimo la 4.1.0';
$string['phpversionhelp'] = '<p>Moodle richiede come minimo la versione 4.1.0 del PHP.</p>
<p>Attualmente state utilizzando la versione $a</p>
<p>È necessario aggiornare il PHP o spostarsi su un server con una versione di PHP più recente!</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle può avere problemi con il safemode impostato a on';
$string['safemodehelp'] = '<p>Moodle può avere diversi problemi con il paramentro safemode impostato a on, non ultima l\'impossibilità di creare nuovi file.</p>
<p>Safemode è normalmente abiltato da paranoici web server pubblici, se è cosi l\'unica soluzine è trovare un nuovo web server per il tuo sito di Moodle.</p>
<p>È possibile a continuare l\'installazione se si vuole, ma aspettatevi alcuni problemi dopo.</p>';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'Questo deve essere off';
$string['sessionautostarthelp'] = '<p>Moodle richiede il supporto delle sessioni e non funziona senza.</p>
<p>Le sessioni possono essere abilitate nel file php.ini ... cerca il parametro session.auto_start.</p>';
$string['wwwroot'] = 'Indirizzo web';
$string['wwwrooterror'] = 'L\'indirizzo web sembra non essere valido - questa installazione di Moodle non sembra esere li.';

?>
