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
 * @package     auth_ticket
 * @category    auth
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright   (C) 2010 ValEISTI (http://www.valeisti.fr)
 * @copyright   (C) 2012 onwards Valery Fremaux (http://www.mylearningfactory.com)
 *
 * Implements an external access with encrypted access ticket for notification returns.
 */
defined('MOODLE_INTERNAL') || die();

$string['privacy:metadata'] = "Le plugin d\'authentification par ticket ne stocek aucune donnée liée à des utilisateurs.";

$string['auth_ticket'] = 'Accès direct par ticket';
$string['auth_tickettitle'] = 'Accès direct par ticket';
$string['configtesturl'] = 'Copiez cette url dans un navigateur non connecté.';
$string['configshortvaliditydelay'] = 'Temps de validité des tickets courts';
$string['configlongvaliditydelay'] = 'Temps de validité des tickets longs';
$string['configpersistantvaliditydelay'] = 'Temps de validité des tickets persistants';
$string['configticketusessl'] = 'Si oui, le ticket est crypté/décrypté en utilisant les librairies openssl du système. Si non, c\'est la fonction d\'encryption interne de la base de données qui sera utilisée.';
$string['decodeerror'] = 'Erreur de lecture du ticket';
$string['encodeerror'] = 'Erreur d\'encodage du ticket';
$string['no'] = 'Non (Mysql et MariaDB uniquement)';
$string['pluginname'] = 'Accès direct par ticket';
$string['testurl'] = 'Url de test';
$string['internal'] = 'Interne';
$string['ticketerror'] = 'Erreur de désérialisaton du ticket (méthode {$a})';
$string['tickettimeguard'] = 'Temps de validité du ticket court (en heures)&nbsp;';
$string['usessl'] = 'Utiliser SSL pour crypter le ticket&nbsp;';
$string['yes'] = 'Oui (plus compatible)';
$string['configencryption'] = 'Méthode de cryptage';
$string['configinternalseed'] = 'Clef interne';
$string['configinternalseed_desc'] = 'Clef de cryptage interne';

$string['configencryption_desc'] = 'DES est une méthode plus simple et plus rapide, mais repose sur une fonction interne de Mysql.
Elle ne fonctionnera pas sur d\'autres moteurs de base de données. AES n\'est pas lié aux bases de donnéesn, mais nécessite
d\'avoir openssl installé sur le serveur.';

$string['auth_ticketdescription'] = 'Ce mode d\'authentification permet à des utilisteurs ayant reçu une notification par courriel de se
connecter directement sur leur compte sans passer par la page de login. Le ticket crypté leur ayant été transmis contient toutes les
informations suffisantes de login pendant une certaine durée de temps de validité. Au delà de cette durée le ticket est perdu.';

$string['configshortvaliditydelay_desc'] = 'Durée de validité du ticket court (en secondes). Les tickets courts sont utilisés lorsque le délai de retour
à Moodle à compter de la génération du ticket est connu comme étant court (retour immédiat ou synchrone).';

$string['configlongvaliditydelay_desc'] = 'Durée de validité du ticket long (en secondes). Les tickets longs sont utilisés lorsque le délai de retour
à Moodle à compter de la génération du ticket est connu comme étant long (retour asynchrone), en général quelques jours.';

$string['configpersistantvaliditydelay_desc'] = 'La durée de persistance peut être illimitée (valeur 0), ou être réglée sur un nombre très grand de secondes.
Notez des tickets persistants peuvent perdre leur validité si la méthode DSA (openssl) est utilisée en relation avec la valeur courante de la clef MNET.';
