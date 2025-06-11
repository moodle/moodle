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
 * Italian language strings.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment

$string['pluginname'] = 'OpenID Connect';
$string['auth_oidcdescription'] = 'Il plugin OpenID Connect fornisce funzionalità single-sign-on utilizzando Identity Provider configurabili.';
$string['cfg_authendpoint_key'] = 'Endpoint di autorizzazione';
$string['cfg_authendpoint_desc'] = 'L\'URI dell\'endpoint di autorizzazione dall\'Identity Provider da utilizzare.';
$string['cfg_autoappend_key'] = 'Aggiungi automaticamente';
$string['cfg_autoappend_desc'] = 'Aggiunge automaticamente questa stringa durante il login di utenti utilizzando il flusso di login Nome utente/Password. È utile quando l\'Identity Provider richiede un dominio comune senza però richiederne l\'inserimento durante il login. Ad esempio, se l\'utente OpenID Connect completo è "james@example.com" e si inserisce "@example.com", l\'utente deve inserire solo "james" come nome utente. <br /><b>Nota:</b> in caso di conflitto tra nomi utente, ovvero quando esiste un utente Moodle con lo stesso nome, la priorità del plugin di autenticazione è utilizzata per determinare quale utente ha la meglio.';
$string['cfg_clientid_key'] = 'ID cliente';
$string['cfg_clientid_desc'] = 'Il tuo ID cliente registrato sull\'Identity Provider.';
$string['cfg_clientsecret_key'] = 'Segreto cliente';
$string['cfg_clientsecret_desc'] = 'Il tuo segreto cliente registrato sull\'Identity Provider. Su alcuni provider, viene anche indicato come chiave.';
$string['cfg_err_invalidauthendpoint'] = 'Endpoint di autorizzazione non valido';
$string['cfg_err_invalidtokenendpoint'] = 'Endpoint token non valido';
$string['cfg_err_invalidclientid'] = 'ID cliente non valido';
$string['cfg_err_invalidclientsecret'] = 'Segreto cliente non valido';
$string['cfg_icon_key'] = 'Icona';
$string['cfg_icon_desc'] = 'Un\'icona visualizzata accanto al nome del provider sulla pagina di login.';
$string['cfg_iconalt_o365'] = 'Icona Microsoft 365';
$string['cfg_iconalt_locked'] = 'Icona Bloccato';
$string['cfg_iconalt_lock'] = 'Icona blocca';
$string['cfg_iconalt_go'] = 'Cerchio verde';
$string['cfg_iconalt_stop'] = 'Cerchio rosso';
$string['cfg_iconalt_user'] = 'Icona utente';
$string['cfg_iconalt_user2'] = 'Icona utente alternativa';
$string['cfg_iconalt_key'] = 'Icona chiave';
$string['cfg_iconalt_group'] = 'Icona gruppo';
$string['cfg_iconalt_group2'] = 'Icona gruppo alternativa';
$string['cfg_iconalt_mnet'] = 'Icona MNET';
$string['cfg_iconalt_userlock'] = 'Utente con icona blocco';
$string['cfg_iconalt_plus'] = 'Icona più';
$string['cfg_iconalt_check'] = 'Icona segno di spunta';
$string['cfg_iconalt_rightarrow'] = 'Icona freccia verso destra';
$string['cfg_customicon_key'] = 'Icona personalizzato';
$string['cfg_customicon_desc'] = 'Se desideri utilizzare la tua icona, caricala qui. Questa ha la precedenza su qualsiasi icona scelta in precedenza. <br /><br /><b>Note sull\'utilizzo di icone personalizzate:</b><ul><li>Questa immagine <b>non</b> verrà ridimensionata sulla pagina di login, pertanto si consiglia di caricare un\'immagine di dimensioni inferiori a 35x35 pixel.</li><li>Se hai caricato un\'icona personalizzata e desideri tornare a una delle icone di origine, fai clic sull\'icona personalizzata nella casella precedente, scegli "Elimina", fai clic su "OK", quindi su "Salva modifiche" nella parte inferiore di questo modulo. L\'icona di origine selezionata verrà visualizzata nella pagina di login di Moodle.</li></ul>';
$string['cfg_debugmode_key'] = 'Registra messaggi di debug';
$string['cfg_debugmode_desc'] = 'Abilitando questa opzione, le informazioni verranno registrate nel log di Moodle per risolvere problemi di identificazione.';
$string['cfg_loginflow_key'] = 'Flusso di login';
$string['cfg_loginflow_authcode'] = 'Richiesta di autorizzazione';
$string['cfg_loginflow_authcode_desc'] = 'Utilizzando questo flusso, l\'utente fa clic sul nome dell\'Identity Provider (vedere "Nome provider" in precedenza) nella pagina login di Moodle e viene reindirizzato al provider per il login. Dopo il login, l\'utente viene nuovamente reindirizzato in Moodle dove il login a Moodle viene eseguito in maniera trasparente. Questo è il metodo di login più standardizzato e sicuro.';
$string['cfg_loginflow_rocreds'] = 'Autenticazione nome utente/password';
$string['cfg_loginflow_rocreds_desc'] = 'Utilizzando questo flusso, l\'utente inserisce il nome utente e la password nel modulo di login di Moodle seguendo la stessa procedura del login manuale. Le credenziali vengono quindi passate all\'Identity Provider in background per ottenere l\'autenticazione. Questo flusso è quello più trasparente per l\'utente in quanto non esiste interazione diretta con l\'Identity Provider. Osservare che non tutti gli Identity Provider supportano questo flusso.';
$string['cfg_oidcresource_key'] = 'Risorsa';
$string['cfg_oidcresource_desc'] = 'La risorsa OpenID Connect per la quale inviare la richiesta.';
$string['cfg_oidcscope_key'] = 'Scopo';
$string['cfg_oidcscope_desc'] = 'L\'ambito OIDC da utilizzare.';
$string['cfg_opname_key'] = 'Nome del provider';
$string['cfg_opname_desc'] = 'Si tratta di un\'etichetta presentata all\'utente finale che identifica il tipo di credenziali che l\'utente deve utilizzare per eseguire il login. Questa etichetta viene utilizzata in tutta la parte rivolta all\'utente del plugin per identificare il provider.';
$string['cfg_redirecturi_key'] = 'URI di reindirizzamento';
$string['cfg_redirecturi_desc'] = 'Si tratta dell\'URI da registrare come "URI di reindirizzamento". L\'Identity Provider di OpenID Connect deve richiedere questa informazione durante la registrazione come client. <br /><b>NOTA:</b> devi immettere questa informazione nell\'apposito campo *esattamente* come appare qui. Qualsiasi differenza non consentirà di accedere con OpenID Connect.';
$string['cfg_tokenendpoint_key'] = 'Endpoint token';
$string['cfg_tokenendpoint_desc'] = 'L\'URI dell\'endpoint token dall\'Identity Provider.';
$string['event_debug'] = 'Messaggio di debug';
$string['errorauthdisconnectemptypassword'] = 'La password non può essere vuota';
$string['errorauthdisconnectemptyusername'] = 'Il nome utente non può essere vuoto';
$string['errorauthdisconnectusernameexists'] = 'Questo nome utente è già impegnato. Sceglierne uno diverso.';
$string['errorauthdisconnectnewmethod'] = 'Utilizza metodo di login';
$string['errorauthdisconnectinvalidmethod'] = 'Ricevuto metodo di login non valido.';
$string['errorauthdisconnectifmanual'] = 'Se si utilizza il metodo di login manuale, immettere le credenziali sottostanti.';
$string['errorauthinvalididtoken'] = 'id_token non valido ricevuto.';
$string['errorauthloginfailednouser'] = 'Login non valido: utente non trovato in Moodle.';
$string['errorauthnoauthcode'] = 'Codice di autenticazione non ricevuto.';
$string['errorauthnocreds'] = 'Configurare le credenziali cliente OpenID Connect.';
$string['errorauthnoendpoints'] = 'Configurare gli endpoint server OpenID Connect.';
$string['errorauthnohttpclient'] = 'Impostare un client HTTP.';
$string['errorauthnoidtoken'] = 'OpenID Connect id_token non ricevuto.';
$string['errorauthunknownstate'] = 'Stato sconosciuto.';
$string['errorauthuseralreadyconnected'] = 'Sei già connesso a un utente OpenID Connect diverso.';
$string['errorauthuserconnectedtodifferent'] = 'L\'utente OpenID Connect autenticato è già connesso a un utente Moodle.';
$string['errorbadloginflow'] = 'Specificato flusso di login non valido. Nota: se stai ricevendo questo messaggio dopo un\'installazione o aggiornamento recente, cancella la cache Moodle.';
$string['errorjwtbadpayload'] = 'Impossibile leggere payload JWT.';
$string['errorjwtcouldnotreadheader'] = 'Impossibile leggere intestazione JWT';
$string['errorjwtempty'] = 'Ricevuto JWT vuoto o di tipo non stringa.';
$string['errorjwtinvalidheader'] = 'Intestazione JWT non valida';
$string['errorjwtmalformed'] = 'Ricevuto JWT danneggiato.';
$string['errorjwtunsupportedalg'] = 'JWS Alg o JWE non supportato';
$string['erroroidcnotenabled'] = 'Il plugin di autenticazione OpenID Connect non è abilitato.';
$string['errornodisconnectionauthmethod'] = 'Impossibile disconnettersi perché non esiste un plugin di autenticazione abilitato verso cui eseguire il fallback (metodo di accesso precedente dell\'utente o metodo di accesso manuale).';
$string['erroroidcclientinvalidendpoint'] = 'Ricevuto URI endpoint non valido.';
$string['erroroidcclientnocreds'] = 'Impostare credenziali cliente con segreti';
$string['erroroidcclientnoauthendpoint'] = 'Nessun endpoint di autorizzazione impostato. Impostarlo con $this->setendpoints';
$string['erroroidcclientnotokenendpoint'] = 'Nessun endpoint token impostato. Impostare con $this->setendpoints';
$string['erroroidcclientinsecuretokenendpoint'] = 'L\'endpoint token deve utilizzare SSL/TLS.';
$string['errorucpinvalidaction'] = 'Ricevuta azione non valida.';
$string['erroroidccall'] = 'Si è verificato un errore in OpenID Connect. Consultare i log per ulteriori informazioni.';
$string['erroroidccall_message'] = 'Si è verificato un errore in OpenID Connect: {$a}';
$string['eventuserauthed'] = 'Utente autorizzato con OpenID Connect';
$string['eventusercreated'] = 'Utente creato con OpenID Connect';
$string['eventuserconnected'] = 'Utente connesso a OpenID Connect';
$string['eventuserloggedin'] = 'Utente autenticato con OpenID Connect';
$string['eventuserdisconnected'] = 'Utente disconnesso da OpenID Connect';
$string['oidc:manageconnection'] = 'Gestisci connessione OpenID Connect';
$string['ucp_general_intro'] = 'Qui puoi gestire la connessione a {$a}. Abilitando questa opzione, sarai in grado di utilizzare il tuo account {$a} per accedere a Moodle anziché un nome utente e una password separati. Dopo la connessione, non dovrai più ricordare il nome utente e la password per Moodle, tutti i login verranno gestiti da {$a}.';
$string['ucp_login_start'] = 'Inizia a utilizzare {$a} per accedere a Moodle';
$string['ucp_login_start_desc'] = 'Il tuo account verrà cambiato per utilizzare {$a} per accedere a Moodle. Dopo che è stato abilitato, l\'accesso verrà eseguito utilizzando le tue credenziali {$a} - il nome utente e la password Moodle correnti non funzioneranno. In qualsiasi momento puoi disconnetterti dal tuo account e tornare a eseguire il login normalmente.';
$string['ucp_login_stop'] = 'Non utilizzare più {$a} per accedere a Moodle';
$string['ucp_login_stop_desc'] = 'Stai attualmente utilizzando {$a} per accedere a Moodle. Facendo clic su "Non utilizzare più il login {$a}", l\'account Moodle verrà disconnesso da {$a}. Non potrai più accedere a Moodle con il tuo account {$a}. Ti verrà chiesto di creare un nome utente e una password con cui potrai accedere a Moodle direttamente.';
$string['ucp_login_status'] = 'Login {$a} è:';
$string['ucp_status_enabled'] = 'Abilitato';
$string['ucp_status_disabled'] = 'Disabilitato';
$string['ucp_disconnect_title'] = 'Disconnessione {$a}';
$string['ucp_disconnect_details'] = 'Il tuo account Moodle verrà disconnesso da {$a}. Per accedere a Moodle dovrai creare un nome utente e una password.';
$string['ucp_title'] = 'Gestione {$a}';

// phpcs:enable moodle.Files.LangFilesOrdering.IncorrectOrder
// phpcs:enable moodle.Files.LangFilesOrdering.UnexpectedComment