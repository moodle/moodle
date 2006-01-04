<?php // $Id$ 

$string['adminauthorizeccapture'] = 'Contrôle des commandes & réglages de saisie automatique';
$string['adminauthorizeemail'] = 'Réglages d\'envoi de courriel';
$string['adminauthorizesettings'] = 'Réglages Authorize.net';
$string['adminauthorizewide'] = 'Réglages globaux';
$string['adminneworder'] = 'Cher administrateur,
  	 
Vous avez reçu un nouvel ordre en attente :

    No d\'ordere : $a->orderid
    No de transaction : $a->transid
    Utilisateur : $a->user
    Cours : $a->course
    Montant : $a->amount

    SAISE AUTOMATIQUE ACTIVE ? $a->acstatus

Si la saisie automatique est actovie, les infos de carte de crédit seront
saisies le $a->captureon et l\'étudiant sera inscrit au cours. Dans le cas
contraire, ces données arriveront à échéance le $a->expireon et ne pourront
plus être saisies après cette date.

Vous pouvez immédiatement accepter ou refuser le paiement pour l\'inscription
de l\'étudiant en cliquant sur le lien ci-dessous.

$a->url';
$string['adminnewordersubject'] = '$a->course : nouvel ordre en attente de traintement ($a->orderid)';
$string['adminreview'] = 'Contrôle de la commande avant envoi des données de la carte de crédit.';
$string['amount'] = 'Montant';
$string['anlogin'] = 'Authorize.net&nbsp;: nom d\'utilisateur';
$string['anpassword'] = 'Authorize.net&nbsp;: mot de passe';
$string['anreferer'] = 'Taper ici une URL (référenceur) si vous avez mis en place cette fonctionnalité dans votre compte authorize.net. Ceci enverra une entête «&nbsp;Referer: URL&nbsp;» incluse dans la requête web';
$string['antestmode'] = 'Traiter les transactions en mode test (aucun montant ne sera prélevé)';
$string['antrankey'] = 'Authorize.net&nbsp;: clef de transaction';
$string['authorizedpendingcapture'] = 'Autorisé / En attente de saisie';
$string['canbecredit'] = 'Rembousable à concurrence de $a->upto';
$string['cancelled'] = 'Annulé';
$string['capture'] = 'Saisie';
$string['capturedpendingsettle'] = 'Saisi / En attente de règlement';
$string['capturedsettled'] = 'Saisi / Réglé';
$string['capturetestwarn'] = 'La saisie semble fonctionner, mais aucun enregistrement n\'a été mis à jour en mode test.';
$string['captureyes'] = 'Les données de la carte de crédit vont être saisies et l\'étudiant sera inscrit au cours. Voulez-vous continuer&nbsp;?';
$string['ccexpire'] = 'Date d\'échéance';
$string['ccexpired'] = 'La carte de crédit est échue';
$string['ccinvalid'] = 'Numéro de carte non valable';
$string['ccno'] = 'Numéro de carte de crédit';
$string['cctype'] = 'Type de carte de crédit';
$string['ccvv'] = 'Code vérification';
$string['ccvvhelp'] = 'Au verso de votre carte (les 3 derniers chiffres)';
$string['choosemethod'] = 'Tapez la clef d\'inscription à ce cours&nbsp;; si vous n\'avez pas cette clef, ce cours vous sera accessible contre paiement.';
$string['chooseone'] = 'Veuillez remplir l\'un des deux champs ci-dessous ou tous les deux';
$string['credittestwarn'] = 'Le crédit semble fonctionner, mais aucun enregistrement n\'a été inséré dans la base de données en mode test.';
$string['cutofftime'] = 'Date butoir de transaction. Quand la dernière transaction doit-elle être traitée pour règlement&nbsp;?';
$string['description'] = 'Le module Authorize.net permet de mettre en place des cours payant. Si le coût d\'un cours est nul, les étudiants peuvent s\'y inscrire sans payer. Un coût défini globalement, que vous fixez ici, est le coût par défaut pour tous les cours du site. Chaque cours peut ensuite avoir un coût spécifique fixé individuellement. S\'il est défini, le coût spécifique d\'un cours remplace le coût par défaut.<br /><br /><b>Remarque&nbsp;:</b> si vous indiquez une clef d\'inscription dans les réglages du cours, les étudiants auront également la possibilité de s\'y inscrire avec cette clef. Ceci est utile si vous avez un mélange d\'étudiants payant et non payant.';
$string['enrolname'] = 'Paiement par carte de crédit Authorize.net';
$string['expired'] = 'Échu';
$string['howmuch'] = 'Combien&nbsp;?';
$string['httpsrequired'] = 'Votre requête ne peut pas être traitée pour l\'instant. Les réglages du site n\'ont pas pu être configurés correctement.<br /><br />Veuillez NE PAS taper votre numéro de carte de  crédit, à moins que vous ne voyez un cadenas jaune au bas ou dans la barre d\'adresse de votre navigateur. Ce cadenas indique que toutes les données transmises entre votre ordinateur et le serveur sont chiffrées, et que les informations échangées entre ces deux ordinateurs sont protégées et ne peuvent pas être interceptées sur Internet.';
$string['logindesc'] = 'Cette option doit impérativement être activée&nbsp;!<br /><br />Veuillez vous assurer que l\'option «&nbsp;<a href=\"$a->url\">loginhttps</a>&nbsp;» soit activée dans les paramètres de l\'administration, section Sécurité.<br /><br />L\'activation de cette optio permettra à Moodle d\'utiliser une connexion sécurisée pour l\'affichage et le traitement des pages de connexion et de paiement.';
$string['nameoncard'] = 'Nom sur la carte';
$string['noreturns'] = 'Pas de retour&nbsp;!';
$string['notsettled'] = 'Non réglé';
$string['orderid'] = 'No d\'ordre';
$string['paymentmanagement'] = 'Gestion des paiements';
$string['paymentpending'] = 'Votre paiement pour ce cours est en attente de traitement. Son numéro d\'ordre est $a->orderid.';
$string['refund'] = 'Remboursement';
$string['refunded'] = 'Remboursé';
$string['returns'] = 'Retour';
$string['reviewday'] = 'Saisir les données de la carte de crédit automatiquement, à moins qu\'un enseignant ou un administrateur ne contrôle la commande dans les <b>$a</b> jours. LE CRON DOIT ÊTRE ACTIF.<br />(0 jour signifie que la saisie automatique sera désactivée. Un contrôle par un enseignant ou administrateur est alors nécessaire. Dans ce cas, la transaction sera annulée si elle n\'est pas contrôlée dans les 30 jours)';
$string['reviewnotify'] = 'Votre paiement va être contrôlé. Votre enseignant vous contactera par courriel dans quelques jours.';
$string['sendpaymentbutton'] = 'Envoyer paiement';
$string['settled'] = 'Réglé';
$string['settlementdate'] = 'Date de réglement';
$string['subvoidyes'] = 'La transaction remboursée $a->transid sera annulée et votre compte sera crédité de $a->amount. Voulez-vous continuer&nbsp;?';
$string['tested'] = 'Testé';
$string['testmode'] = '[MODE TEST]';
$string['transid'] = 'No de transaction';
$string['unenrolstudent'] = 'Désinscrire l\'étudiant&nbsp;?';
$string['void'] = 'Nul';
$string['voidtestwarn'] = 'L\'annulation semble fonctionner, mais aucun enregistrement n\'a été mis à jour en mode test.';
$string['voidyes'] = 'La transaction sera annulée. Voulez-vous continuer&nbsp;?';
$string['zipcode'] = 'Code postal';

?>
