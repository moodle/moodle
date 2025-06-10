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
 * Strings for the French language.
 *
 * @copyright  2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulenameplural'] = 'Questionnaires adaptatifs';
$string['modulename'] = 'Questionnaire adaptatif';
$string['modulename_help'] = 'L’application Questionnaire adaptative permet à un enseignant de créer des questionnaires mesurant efficacement les capacités des candidats.Les questionnaires adaptatifs sont composés de questions selectionnées dans la banque d\'items et répertoriées selon leur niveau de difficulté.Les items sont choisis pour correspondre au niveau de capacité estimé du candidat en cours. Si le candidat répond correctement à un item, un item plus difficile lui est ensuite proposé. Si le candidat répond de manière incorrecte à un item, un item moins difficile lui est ensuite proposé. Cette technique prendra la forme d’une série d’items convergeant vers le niveau réel du candidat. Le test s’arrête quand le niveau du ou des candidats est déterminé avec la précision souhaitée. Cette application est particulièrement adaptée pour déterminer un niveau sur une échelle de mesure unidimensionnelle. Bien que l’échelle de mesure puisse être très large, les items eux, doivent tous fournir un niveau ou une indication d’aptitude étalonnés sur la même échelle. Par exemple, pour un test de positionnement, les items placés bas dans l’échelle de mesure et auxquelles les débutants sont capables de répondre correctement, devraient recevoir une réponse correcte de la part des experts, à l’inverse les questions placées plus haut dans  l’échelle de mesure ne devraient recevoir de réponse correcte que par les experts ou grâce à la chance. Les items ne discriminant pas les candidats de différents niveaux de capacité rendront le test inefficace et pourront mener à des résultats non concluants.

Les questions utilisées dans « questionnaire adaptatif » doivent :

* être automatiquement répertoriées comme étant correctes ou incorrectes.
* être répertoriées par difficulté en utilisant \'adpq_\' suivi d’un entier positif compris dans le classement prévu pour le questionnaire.

Le questionnaire adaptatif peut être configuré pour :

* définir lui-même le ratio item-difficulté / utilisateur-niveaux à mesurer. 1-10, 1-16 et 1-100 sont des exemples de classements valides.
* définir la précision requise avant que le questionnaire ne s’arrête. Pour établir un niveau, on considère souvent qu’une erreur de 5 % est une règle d’arrêt appropriée.
* définir un nombre minimum de questions nécessitant une réponse
* définir un nombre maximum de questions pouvant faire l’objet d’une réponse

La description et le processus de tests dans cette application sont basées sur <a href="http://www.rasch.org/memo69.pdf">Computer-Adaptive Testing: A Methodology Whose Time Has Come</a> by John Michael Linacre, Ph.D. MESA Psychometric Laboratory - University of Chicago. MESA Memorandum No. 69.';
$string['pluginadministration'] = 'questionnaire adaptatif';
$string['pluginname'] = 'questionnaire adaptatif';
$string['nonewmodules'] = 'Aucune occurrence de questionnaire adaptatif n’a été trouvée';
$string['adaptivequizname'] = 'Nom';
$string['adaptivequizname_help'] = 'Entrez le nom de l’occurence questionnaire adaptatif';
$string['adaptivequiz:addinstance'] = 'Ajoutez un nouveau questionnaire adaptatif';
$string['adaptivequiz:viewreport'] = 'Voir les rapports du questionnaire adaptatif';
$string['adaptivequiz:reviewattempts'] = 'Revoir les propositions du questionnaire adaptatif';
$string['adaptivequiz:attempt'] = 'Débuter un test adaptatif';
$string['attemptsallowed'] = 'Nombre de tentative autorisée';
$string['attemptsallowed_help'] = 'Nombre de tentative autorisée pour le candidat';
$string['requirepassword'] = 'Mot de passe requis';
$string['requirepassword_help'] = 'Les candidats doivent entrer un mot de passe pour ouvrir leur session';
$string['browsersecurity'] = 'Sécurité du navigateur';
$string['browsersecurity_help'] = 'Si « Full screen pop-up with some JavaScript security » est sélectionné, le questionnaire débutera seulement si le candidat dispose d’un navigateur-web autorisant Javascipt . Le questionnaire apparaît en plein écran dans une fenêtre contextuelle couvrant toutes les autres fenêtres et qui ne dispose d’aucun contrôle de navigation. De même, dans la mesure du possible, l’utilisation de certaines commandes comme « copier » et « coller » sont désactivées pour les candidats';
$string['minimumquestions'] = 'Nombre minimum d’items';
$string['minimumquestions_help'] = 'Nombre minimum d’items auxquels le candidat doit répondre';
$string['maximumquestions'] = 'Nombre maximum d’items';
$string['maximumquestions_help'] = 'Nombre maximum d’items qu’un candidat peut tenter';
$string['startinglevel'] = 'Niveau de difficulté de départ';
$string['startinglevel_help'] = 'Lorsque le candidat commence une session, l’application sélectionnera aléatoirement un item correspondant au niveau de difficulté';
$string['lowestlevel'] = 'Niveau de difficulté le plus bas';
$string['lowestlevel_help'] = 'Niveau le moins difficile duquel les items seront sélectionnés à l’occasion de ce test. Lors d’une session, l’activité n’ira pas au delà de ce niveau de difficulté';
$string['highestlevel'] = 'Niveau de difficulté le plus élevé';
$string['highestlevel_help'] = 'Niveau de difficulté le plus difficile duquel les items seront sélectionnés à l’occasion de ce test. Lors d’une session, l’activité n’ira pas au delà de ce niveau de difficulté';
$string['questionpool'] = 'Banque d’items';
$string['questionpool_help'] = 'Sélectionnez  les catégories desquelles les items pourront être tirés durant une session';
$string['formelementempty'] = 'Entrez un entier positif compris entre 1 et 999';
$string['formelementnumeric'] = 'Entrez une valeur chiffrée comprise entre 1 et 999';
$string['formelementnegative'] = 'Entrez un nombre positif compris entre 1 et 999';
$string['formminquestgreaterthan'] = 'Le nombre minimum de questions doit être inférieur au nombre maximum de question';
$string['formlowlevelgreaterthan'] = 'Le niveau le plus bas doit être inférieur au niveau le plus élevé';
$string['formstartleveloutofbounds'] = 'Le niveau de départ doit être un nombre compris entre le niveau le plus bas et le niveau le plus élevé';
$string['standarderror'] = 'Erreur standard provoquant l’arrêt';
$string['standarderror_help'] = 'Lorsque le nombre d’erreurs fait que la capacité de l’utilisateur est évaluée en dessous du niveau seuil le questionnaire s’arrêtera. Réglez cette valeur dans la limite de 5% pour  obtenir plus ou moins de précision pour mesurer sa capacité';
$string['formelementdecimal'] = 'Entrez un nombre décimal d’une longueur maximum de 10 chiffres et comportant un maximum de 5 chiffres après la virgule';
$string['attemptfeedback'] = 'Commentaire';
$string['attemptfeedback_help'] = 'Un commentaire est proposé à l’utilisateur une fois la session est terminée';
$string['formquestionpool'] = 'Sélectionnez au moins une catégorie de question';
$string['submitanswer'] = 'Soumettre la réponse';
$string['startattemptbtn'] = 'Démarrez la session';
$string['viewreportbtn'] = 'Voir le rapport';
$string['errorfetchingquest'] = 'Impossible de récupérer un item pour ce niveau {$a->level}';
$string['leveloutofbounds'] = 'Le niveau requis {$a->level} n’est pas celui prévu pour cette session';
$string['errorattemptstate'] = 'Une erreur s’est produite en déterminant l’état de la session';
$string['nopermission'] = 'Accès réservé';
$string['maxquestattempted'] = 'Nombre maximum de items tentés';
$string['notyourattempt'] = 'Cette tentative n’est pas la votre pour cette activité';
$string['noattemptsallowed'] = 'Plus aucune tentative autorisée pour cette activité';
$string['updateattempterror'] = 'Erreur lors de la mise à jour de l’enregistrement';
$string['numofattemptshdr'] = 'Nombre de tentatives';
$string['standarderrorhdr'] = 'Erreur standard';
$string['errorlastattpquest'] = 'Erreur lors de la vérification de réponse du dernier item';
$string['errornumattpzero'] = 'Le nombre de tentatives est égal à zéro, bien que l’utilisateur ait soumis une réponse à la question précédente';
$string['errorsumrightwrong'] = 'La somme des réponses correctes et incorrectes est différent du nombre total de items tentées';
$string['calcerrorwithinlimits'] = 'L’erreur standard calculée par {$a->calerror} est comprise dans les limites imposées par l’application {$a->definederror}';
$string['missingtagprefix'] = 'Tag prefix manquant';
$string['recentactquestionsattempted'] = 'Items tentés: {$a}';
$string['recentattemptstate'] = 'État de la tentative';
$string['recentinprogress'] = 'En court';
$string['notinprogress'] = 'Cette tentative n’est pas en court';
$string['recentcomplete'] = 'Terminé';
$string['functiondisabledbysecuremode'] = 'Cette fonctionnalité est actuellement désactivée';
$string['enterrequiredpassword'] = 'Entrez le mot de passe requis';
$string['requirepasswordmessage'] = 'Pour débuter ce questionnaire vous devez connaître son mot de passe';
$string['wrongpassword'] = 'Mot de passe incorrect';
$string['attemptstate'] = 'État de la tentative';
$string['attemptstopcriteria'] = 'Raison de l’abandon';
$string['questionsattempted'] = 'Total des items tentés';
$string['attemptfinishedtimestamp'] = 'Heure de fin de la tentative';
$string['backtomainreport'] = 'Retour au rapport principal';
$string['reviewattempt'] = 'Revoir sur la tentative';
$string['indvuserreport'] = 'Rapport individuel d’activité pour l’utilisateur {$a}';
$string['activityreports'] = 'Rapport d’activité';
$string['stopingconditionshdr'] = 'Conditions d’arrêt';
$string['backtoviewattemptreport'] = 'Retour vers le rapport de tentative';
$string['backtoviewreport'] = 'Retour vers le rapport principal';
$string['reviewattemptreport'] = 'Revue de la tentative par {$a->fullname} soumise à {$a->finished}';
$string['deleteattemp'] = 'Supprimez la tentative';
$string['confirmdeleteattempt'] = 'Confirmation de la suppression de la tentative à partir de  {$a->name}  soumise à {$a->timecompleted}';
$string['attemptdeleted'] = 'Tentative supprimée pour {$a->name} soumise à  {$a->timecompleted}';
$string['closeattempt'] = 'Clôturer la tentative';
$string['confirmcloseattempt'] = 'Êtes vous certain(e) de vouloir clôturer et finaliser cette tentative de {$a->name}?';
$string['confirmcloseattemptstats'] = 'Cette tentative commencée le {$a->started} a été mise à jour le {$a->modified}';
$string['confirmcloseattemptscore'] = '{$a->num_questions} items ont été complétés et le score est de {$a->measure} {$a->standarderror}.';
$string['attemptclosedstatus'] = 'Tentative clôturée manuellement par {$a->current_user_name} (user-id: {$a->current_user_id}) le {$a->now}.';
$string['attemptclosed'] = 'La tentative a été clôturée manuellement';
$string['errorclosingattempt_alreadycomplete'] = 'Cette tentative est déjà validée et ne peut être clôturée manuellement';
$string['formstderror'] = 'Un pourcentage inférieur à 50 et supérieur ou égal à 0 doit être entré';
$string['backtoviewattemptreport'] = 'Retour vers le rapport de tentative';
$string['backtoviewreport'] = 'Retour vers le rapport principal';
$string['reviewattemptreport'] = 'Revue de la tentative par {$a->fullname} soumise à {$a->finished}';
$string['score'] = 'Résultat';
$string['bestscore'] = 'Meilleur résultat';
$string['bestscorestderror'] = 'Erreur standard';
$string['attempt_summary'] = 'Résumé de la tentative';
$string['scoring_table'] = 'Table des résultats';
$string['attempt_questiondetails'] = 'Détails de l’item';
$string['attemptstarttime'] = 'Heure de début de la tentative';
$string['attempttotaltime'] = 'Temps total (hh:mm:ss)';
$string['attempt_user'] = 'utilisateur';
$string['attempt_state'] = 'État de la tentative';
$string['attemptquestion_num'] = 'Item #';
$string['attemptquestion_level'] = 'Niveau de difficulté de l’item';
$string['attemptquestion_rightwrong'] = 'Vrai/faux';
$string['attemptquestion_ability'] = 'Mesure de capacité';
$string['attemptquestion_error'] = 'Erreur standard (&plusmn;&nbsp;x%)';
$string['attemptquestion_difficulty'] = 'Difficulté de l’item (logits)';
$string['attemptquestion_diffsum'] = 'Somme des difficultés';
$string['attemptquestion_abilitylogits'] = 'Capacité mesurée (logits)';
$string['attemptquestion_stderr'] = 'Erreur standard (&plusmn;&nbsp;logits)';
$string['graphlegend_target'] = 'Niveau cible';
$string['graphlegend_error'] = 'Erreur standard';
$string['answerdistgraph_title'] = 'Communication de la réponse pour {$a->firstname} {$a->lastname}';
$string['answerdistgraph_questiondifficulty'] = 'Niveau de l\'item';
$string['answerdistgraph_numrightwrong'] = 'Nombre incorrect (-) / Nombre correct (+)';
$string['numright'] = 'Nombre correct';
$string['numwrong'] = 'Nombre incorrect';
$string['questionnumber'] = 'Item #';
$string['na'] = 'Non disponible';
$string['downloadcsv'] = 'Téléchargez le fichier CSV';

$string['grademethod'] = 'Méthode de notation';
$string['gradehighest'] = 'Note la plus élevée';
$string['attemptfirst'] = 'Première tentative';
$string['attemptlast'] = 'Dernière tentative';
$string['grademethod_help'] = 'Lorsque plusieurs tentatives sont autorisées, les méthodes suivantes sont disponibles pour calculer la note du questionnaire final

* Note la plus haute pour l’ensemble des tentatives
* Première tentative (toutes les autres tentatives sont ignorées)
* Dernière tentative (toutes les autres tentatives sont ignorées)
';

$string['resetadaptivequizsall'] = 'Effacer toutes les tentatives du questionnaire adaptatif';
$string['all_attempts_deleted'] = 'Toutes les tentatives du questionnaire adaptatif ont été effacées';
$string['all_grades_removed'] = 'Toutes les notes du questionnaire adaptatif ont été retirées';
$string['questionanalysisbtn'] = 'Analyse de la question';
$string['id'] = 'Identifiant';
$string['name'] = 'Nom';
$string['questions_report'] = 'Rapport sur les items';
$string['question_report'] = 'Analyse de l’item';
$string['times_used_display_name'] = 'Temps écoulé';
$string['percent_correct_display_name'] = '% de réponse correcte';
$string['discrimination_display_name'] = 'Discrimination';
$string['back_to_all_questions'] = '&laquo Retour aux questions';
$string['answers_display_name'] = 'Réponses';
$string['answer'] = 'Réponse';
$string['statistic'] = 'Statistique(s)';
$string['value'] = 'Valeur';
$string['highlevelusers'] = 'Utilisateurs au dessus du niveau requis';
$string['midlevelusers'] = 'Utilisateurs proche du niveau requis';
$string['lowlevelusers'] = 'Utilisateurs en dessous du niveau requis';
$string['user'] = 'Utilisateur';
$string['result'] = 'Résultat';
