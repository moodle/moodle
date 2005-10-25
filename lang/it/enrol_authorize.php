<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005101200)


$string['adminreview'] = 'Controllare l\'ordine prima di accettare la carta di credito';
$string['anlogin'] = 'Authorize.net: Nome Login';
$string['anpassword'] = 'Authorize.net: Password';
$string['anreferer'] = 'Definite l\'indirizzo del chiamante se lo avete impostato nell\'account di Authorize.net. Questo invierà una linea \"Referer: URL\" inglobato nella richiesta web.';
$string['antestmode'] = 'Eseguire le transazioni in modalità di test (non verrà trasferito denaro)';
$string['antrankey'] = 'Authorize.net: Chiave di transizione';
$string['ccexpire'] = 'Scadenza';
$string['ccexpired'] = 'La carta di credito è scaduta';
$string['ccinvalid'] = 'Numero di carta non valido';
$string['ccno'] = 'Numero carta di credito';
$string['cctype'] = 'Tipo di carta di credito';
$string['ccvv'] = 'Verifica carta';
$string['ccvvhelp'] = 'Guardare sul retro della carta (le ultime 3 cifre)';
$string['choosemethod'] = 'Se conoscete la chiave d\'accesso del corso, potete accedere; altrimenti dovete pagare per questo corso.';
$string['chooseone'] = 'Compilare uno o entrambi i campi seguenti';
$string['description'] = 'Il modulo Authorize.net permette di impostare un costo per i corso. Se il costo del corso è zero, non verrà chiesto agli studenti di pagare per accedervi. C\'è un costo per tutto il sito che può essere impostato per tutto il sito e poi un impostazione del corso che può essere scelta per ogni singolo corso. Il costo del corso sovrascrive quello del sito. ';
$string['enrolname'] = 'Portale per la carta di credito Authorize.net';
$string['httpsrequired'] = 'Siamo spiacenti di informarvi che la vostra richiesta non può essere attualmente evasa. Le impostazioni di questo sito potrebbero non essere corrette.<br /><br /> Siete pregati di non inserire il vostro numero di carta di credito se non vedete un lucchetto (giallo) in basso nella finestra del browser. Questo significa, che i tutti dati inviati tra client e server sono criptati. In questo modo le informazioni durante la transazione tra 2 computer sono protette e il vostro numero di carta di credito non può essere catturato attraverso internet.';
$string['logindesc'] = 'Questa opzione deve essere ON.<br /><br /> È possibile impostare l\'opzione <a href=\"$a->url\">loginhttps</a> nella sezione Variabili.<br /><br /> Mettedola a on si farà utilizzare a Moodle una connessione sicura (https) per l\'accesso e per le pagine di pagamento.';
$string['nameoncard'] = 'Nome sulla Carta';
$string['reviewday'] = 'Accettare la carta di credito automaticamente senza il controllo dell\'ordine da parte di un docente o dell\'amministratore in <b>$a</b> giorni. Il CRON deve essere ABILITATO.<br />(0 giorni = disabilita l\'autoaccettazione = docente o amministratore la controllano manualmente. La transazione viene annullata se l\'autoaccettazione è disabilitata e non viene controllata entro 30 giorni.)';
$string['reviewnotify'] = 'Il pagamento verrà controllato. Aspettatevi una mail in pochi giorni dal vostro docente.';
$string['sendpaymentbutton'] = 'Invia Pagamento';
$string['zipcode'] = 'Codice postale';

?>
