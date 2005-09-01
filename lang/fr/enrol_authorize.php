<?php // $Id$ 

$string['adminreview'] = 'Contrôle de la commande avant de collecter les données de la carte de crédit.';
$string['anlogin'] = 'Authorize.net&nbsp;: nom d\'utilisateur';
$string['anpassword'] = 'Authorize.net&nbsp;: mot de passe';
$string['anreferer'] = 'Taper ici une URL (référenceur) si vous avez mis en place cette fonctionnalité dans votre compte authorize.net. Ceci enverra une entête «&nbsp;Referer: URL&nbsp;» incluse dans la requête web';
$string['antestmode'] = 'Traiter les transactions en mode test (aucun montant ne sera prélevé)';
$string['antrankey'] = 'Authorize.net&nbsp;: clef de transaction';
$string['ccexpire'] = 'Date d\'échéance';
$string['ccexpired'] = 'La carte de crédit est échue';
$string['ccinvalid'] = 'Numéro de carte non valable';
$string['ccno'] = 'Numéro de carte de crédit';
$string['cctype'] = 'Type de carte de crédit';
$string['ccvv'] = 'Code vérification';
$string['ccvvhelp'] = 'Au verso de votre carte (les 3 derniers chiffres)';
$string['choosemethod'] = 'Tapez la clef d\'inscription à ce cours&nbsp;; si vous n\'avez pas cette clef, ce cours vous sera accessible contre paiement.';
$string['chooseone'] = 'Veuillez remplir l\'un des deux champs ci-dessous ou tous les deux';
$string['description'] = 'Le module Authorize.net permet de mettre en place des cours payant. Si le coût d\'un cours est nul, les étudiants peuvent s\'y inscrire sans payer. Un coût défini globalement, que vous fixez ici, est le coût par défaut pour tous les cours du site. Chaque cours peut ensuite avoir un coût spécifique fixé individuellement. S\'il est défini, le coût spécifique d\'un cours remplace le coût par défaut.';
$string['enrolname'] = 'Paiement par carte de crédit Authorize.net';
$string['httpsrequired'] = 'Votre requête ne peut pas être traitée pour l\'instant. Les réglages du site n\'ont pas pu être configurés correctement.<br /><br />Veuillez NE PAS taper votre numéro de carte de  crédit, à moins que vous ne voyez un cadenas jaune au bas ou dans la barre d\'adresse de votre navigateur. Ce cadenas indique que toutes les données transmises entre votre ordinateur et le serveur sont chiffrées, et que les informations échangées entre ces deux ordinateurs sont protégées et ne peuvent pas être interceptées sur Internet.';
$string['logindesc'] = 'Cette option doit impérativement être activée&nbsp;!<br /><br />Vous pouvez effectuer le réglage de l\'option <a href=\"$a->url\">loginhttps</a>&nbsp;» dans les paramètres de l\'administration, section Sécurité.<br /><br />L\'activation de cette optio permettra à Moodle d\'utiliser une connexion sécurisée pour l\'affichage et le traitement des pages de connexion et de paiement.';
$string['nameoncard'] = 'Nom sur la carte';
$string['reviewday'] = 'Collecter les données de la carte de crédit automatiquement, à moins qu\'un enseignant ou un administrateur ne contrôle la commande dans les <b>$a</b> jours. LE CRON DOIT ÊTRE ACTIF.<br />(0 = désactiver, 1 = contrôle par un enseignant ou administrateur. La transaction sera annulée si elle n\'est pas contrôlée dans les 30 jours)';
$string['reviewnotify'] = 'Votre paiement va être contrôlé. Votre enseignant vous contactera par courriel dans quelques jours.';
$string['sendpaymentbutton'] = 'Envoyer paiement';
$string['zipcode'] = 'Code postal';

?>
