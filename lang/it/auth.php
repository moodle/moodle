<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 development (2004041800)


$string['auth_dbdescription'] = 'Questo metodo usa una tabella di una base di dati esterna per controllare se un dato username e password siano validi.  Se l\'utente è nuovo, allora le informazioni degli altri campi possono essere copiate in Moodle.';
$string['auth_dbextrafields'] = 'Questi campi sono facoltativi. Potete scegliere pre compilare alcuni campi dell\'utente di Moodle con le informazioni dei <B>campi della base di dati esterna</B> che voi specificate qui.  < P>Se lasciate questi vuoti, saranno usati quelli di default.<P>In entrambi i casi, l\'utente potrà modificare tutti questi campi dopo la registrazione.';
$string['auth_dbfieldpass'] = 'Nome del campo che contiene le password';
$string['auth_dbfielduser'] = 'Nome del campo che contiene gli username';
$string['auth_dbhost'] = 'Il computer su cui si trova la base dati';
$string['auth_dbname'] = 'Nome della base dati';
$string['auth_dbpass'] = 'Password corrisponde al suddetto username';
$string['auth_dbpasstype'] = 'Specifica il formato usato per il campo password. La criptatura MD5 é utile per connettersi con altre applicazioni web come PostNuke';
$string['auth_dbtable'] = 'Nome della tabella della base dati';
$string['auth_dbtitle'] = 'Usa un database esterno';
$string['auth_dbtype'] = 'Il tipo di base di dati (guarda la <A HREF=../lib/adodb/readme.htm#drivers>documentazione ADOdb</A> per i dettagli)';
$string['auth_dbuser'] = 'Nome utente con diritti di lettura nella base dati';
$string['auth_emaildescription'] = 'La conferma tramite email é il metodo di autenticazione di default. Quando l\'utente si iscrive, scegliendo il suo nome utente e la password, un email di conferma viene spedita all\'indirizzo di posta elettronica dell\'utente. Questa email contiene un link sicuro a una pagina dove l\'utente può confermare la sua iscrizione. Ai successivi login verranno controllati il nome utente e la password con i valori salvati nella base di dati di Moodle.';
$string['auth_emailtitle'] = 'Autenticazione via email';
$string['auth_imapdescription'] = 'Questo metodo usa un server IMAP per controllare se il nome utente e la password dati sono validi. ';
$string['auth_imaphost'] = 'Indirizzo server IMAP. Usa il numero IP, non il nome DNS.';
$string['auth_imapport'] = 'Porta server IMAP. Normalmente é 143o 993.';
$string['auth_imaptitle'] = 'Usa un server IMAP';
$string['auth_imaptype'] = 'Tipo di server IMAP. I server IMAP possono avere modi  differenti di autenticazione e negoziazione.';
$string['auth_ldap_bind_dn'] = 'Se vuoi usare bind utente per cercare gli utenti, specificalo qui. Qualcosa come \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Password per bind utente.';
$string['auth_ldap_contexts'] = 'Lista dei contesti dove sono messi gli utenti. Separa contesti diversi con il \';\'. Per esempio: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Se abiliti la creazione degli utenti con conferma tramite email, specifica il contestodove gli utenti vengono creati. Questo contesto deve essere diverso dagli altri utenti per prevenire problemi di sicurezza. Non é necessario aggiungere questo contesto alla variabile ldap_context, Moodle cercherà gli utenti in questo contesto automaticamente. ';
$string['auth_ldap_creators'] = 'Lista dei gruppi nei quali i membri possono creare nuovi corsi. Separa i gruppi multipli con \';\'. Normalmente qualcosa come \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Specifica il server LDAP con un URL tipo \'ldap://ldap.myorg.com/\' o \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Specifica l\'attributo del utente membro, quando gli utenti appartengono a un gruppo. Normalmente \'menber\'';
$string['auth_ldap_search_sub'] = 'Metti un valore &lt;&gt; 0 se preferisci cercare gli utenti da sottocontesti.';
$string['auth_ldap_update_userinfo'] = 'Aggiorna le informazioni utente (nome, cognome, indirizzo...) da LDAP a Moodle. Guarda /auth/ldap/attr_mappings.php per le informazioni su mapping';
$string['auth_ldap_user_attribute'] = 'L\'attributo usato per cercare gli utenti. Normalmente \'cn\'.';
$string['auth_ldap_version'] = 'La versione del protocollo LDAP che il tuo server utilizza.';
$string['auth_ldapdescription'] = 'Questo metodo fornisce l\'autenticazione tramite un server LDAP esterno.
Se il nome utente e la password dati sono validi, Moodle crea un nuovo utente nella sua base dati. Questo modulo può leggere gli attributi da LDAP e precompilare i campi richiesti in Moodle. I successivi login solo il nome utente e la password vengono controllati.';
$string['auth_ldapextrafields'] = 'Questi campi sono opzionali. Puoi scegliere di precompilare alcuni campi dell\'utente in Moodle con le informazioni dai <b>campi LDAP</b> che tu specifichi qui. <p>Se lasci questi campi vuoti, non verrà trasferito niente dal LDAP e verranno usati i dati default di Moodle.</p><p>In entrambi i casi, gli utenti possono modificare tutti questi campi dopo essersi logati.';
$string['auth_ldaptitle'] = 'Usa un server LDAP';
$string['auth_manualdescription'] = 'Questo metodo rimuove ogni possibilità agli utenti di iscriversi. Tutte le iscrizioni devono essere create a mano da un amministratore.';
$string['auth_manualtitle'] = 'Solo iscrizione manuale';
$string['auth_multiplehosts'] = 'Possono essere elencati più macchine remote (es. host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = 'Questo metodo utilizza un server NNTP per controllare se il nome utente e la password dati sono validi.';
$string['auth_nntphost'] = 'Indirizzo del server NNTP. Usa il numero IP, non il nome DNS.';
$string['auth_nntpport'] = 'Porta server (119 tipicamente)';
$string['auth_nntptitle'] = 'Usa un server NNTP';
$string['auth_nonedescription'] = 'Gli utenti possono registrarsi e creare iscrizioni valide immediatamente, senza autenticazione di un server esterno e senza conferma tramite email. Stai attento ad usare questa opzione - pensa alla sicurezza e ai problemi di amministrazione chequesto può causare. ';
$string['auth_nonetitle'] = 'Senza autenticazione';
$string['auth_pop3description'] = 'Questo metodo utilizza un server POP3 per controllare se il nome utente e la password dati sono validi.';
$string['auth_pop3host'] = 'L\'indirizzo del server POP3. Usa il numero IP, non il nome DNS.';
$string['auth_pop3port'] = 'Porta del server (110 é la tipica)';
$string['auth_pop3title'] = 'Usa server POP3';
$string['auth_pop3type'] = 'Tipo di server. Se il vostro server usa i certificati per sicurezza, scegli pop3cert.';
$string['auth_user_create'] = 'Abilita creazione utente';
$string['auth_user_creation'] = 'I nuovi utenti (anonimi) possono iscriversi alla sorgente di autenticazione esterna e confermare tramite email. Se abiliti questo, ricorda anche di configurare le opzioni specifiche del modulo per la creazione degli utenti';
$string['auth_usernameexists'] = 'Il nome utente scelto é già utilizzato. Sceglierne uno nuovo. ';
$string['authenticationoptions'] = 'Opzioni di autenticazione';
$string['authinstructions'] = 'Qui puoi fornire le istruzioni per i tuoi utenti, così potranno sapere quale nome utente e password dovranno usare. Il testo che inserisci qui apparira nella pagina di login. Se lo lasci vuoto non saranno fornite istruzioni.';
$string['changepassword'] = 'Cambia URL delle password';
$string['changepasswordhelp'] = 'Qui puoi specificare dove i tuoi utenti possono recuperare o cambiare i loro nome utente/password se li hanno dimenticati. Questo verrà fornito agli utenti comee un pulsante nella pagina di login e nella loro pagina utente. Se lo lasci vuoto il bottone non verrà visualizzato.';
$string['chooseauthmethod'] = 'Scegli un metodo di autenticazione:';
$string['guestloginbutton'] = 'Pulsante login ospite';
$string['instructions'] = 'Istruzioni';
$string['md5'] = 'Criptatura MD5';
$string['plaintext'] = 'Testo semplice';
$string['showguestlogin'] = 'Puoi nascondere o mostrare il pulsante del login come ospite nella pagina di login.';

?>
