<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_aiquestions
 * @category    string
 * @copyright   2023 Ruthy Salomon <ruthy.salomon@gmail.com> , Yedidia Klein <yedidia@openapp.co.il>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Générateur de questions à partir de texte en IA';
$string['pluginname_desc'] = 'Ce plugin vous permet de générer des questions à partir d\'un texte.';
$string['pluginname_help'] = 'Utilisez ce plugin depuis le menu d\'administration du cours.';
$string['privacy:metadata'] = 'Le générateur de questions à partir de texte en IA ne stocke aucune donnée personnelle.';
$string['openaikey'] = 'Clé d\'API OpenAI';
$string['openaikeydesc'] = 'Veuillez saisir ici votre clé d\'API OpenAI<br>
Vous pouvez obtenir votre clé d\'API sur <a href="https://platform.openai.com/account/api-keys">https://platform.openai.com/account/api-keys</a><br>
Sélectionnez le bouton "+ Créer une nouvelle clé secrète" et copiez la clé dans ce champ.<br>
Notez que vous devez disposer d\'un compte OpenAI avec des paramètres de facturation pour obtenir une clé d\'API.';
$string['story'] = 'Texte';
$string['storydesc'] = 'Veuillez saisir ici votre texte.';
$string['numofquestions'] = 'Nombre de questions';
$string['numofquestionsdesc'] = 'Veuillez sélectionner ici le nombre de questions que vous souhaitez générer.';
$string['generate'] = 'Générer des questions';
$string['aiquestions'] = 'Questions en IA';
$string['backtocourse'] = 'Retour au cours';
$string['gotoquestionbank'] = 'Accéder à la banque de questions';
$string['generatemore'] = 'Générer plus de questions';
$string['createdquestionwithid'] = 'Question créée avec l\'identifiant ';
$string['language'] = 'Langue';
$string['languagedesc'] = 'Veuillez sélectionner ici la langue que vous souhaitez utiliser pour la génération des questions.<br>
Notez que certaines langues sont moins bien prises en charge que d\'autres sur ChatGPT.';
$string['usepersonalprompt'] = 'Utiliser une instruction personnelle';
$string['usepersonalpromptdesc'] = 'Veuillez sélectionner ici si vous souhaitez utiliser une instruction personnelle.';
$string['personalprompt'] = 'Instruction personnelle';
$string['personalpromptdesc'] = "Veuillez saisir ici votre instruction personnelle.
L'instruction est l'explication donnée à ChatGPT sur la manière de générer les questions.
<br> Vous devez inclure ces deux paramètres : {{numofquestions}} et {{language}}.";
$string['tasksuccess'] = 'La tâche de génération des questions a été créée avec succès.';
$string['generating'] = 'Génération de vos questions en cours... (Vous pouvez quitter cette page en toute sécurité et vérifier ultérieurement dans la banque de questions)';
$string['generationfailed'] = 'La génération des questions a échoué après {$a} tentatives.';
$string['generationtries'] = 'Nombre de tentatives envoyées à OpenAI : <b>{$a}</b>.';
$string['outof'] = 'sur';
$string['numoftries'] = '<b>{$a}</b> tentatives.';
$string['numoftriesset'] = 'Nombre de tentatives';
$string['numoftriesdesc'] = 'Veuillez indiquer ici le nombre de tentatives que vous souhaitez envoyer à OpenAI.';
$string['preview'] = 'Aperçu de la question dans un nouvel onglet';
$string['cronoverdue'] = 'La tâche cron semble ne pas s\'exécuter,
la génération des questions dépend des tâches ad hoc créées par la tâche cron, veuillez vérifier vos paramètres cron.
Consultez <a href="https://docs.moodle.org/en/Cron#Setting_up_cron_on_your_system">
https://docs.moodle.org/en/Cron#Setting_up_cron_on_your_system
</a> pour plus d\'informations.';
$string['createdquestionsuccess'] = 'Question créée avec succès.';
$string['createdquestionssuccess'] = 'Questions créées avec succès.';
$string['errornotcreated'] = 'Erreur : les questions n\'ont pas été créées.';
