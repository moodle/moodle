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
 * Strings for component 'format_remuiformat'
 *
 * @package    format_remuiformat
 * @copyright  2019 Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Nom du plugin.
$string['pluginname'] = 'Formats de cours Edwiser';
$string['plugin_description'] = 'Les cours sont présentés sous forme de listes pliables ou sous forme de cartes de sections avec un design responsive pour une meilleure navigation.';
// Réglages.
$string['defaultcoursedisplay'] = 'Affichage par défaut du cours';
$string['defaultcoursedisplay_desc'] = "Afficher toutes les sections sur une seule page ou la section zéro et la section choisie sur la page.";

$string['defaultbuttoncolour'] = 'Couleur par défaut du bouton Afficher le sujet';
$string['defaultbuttoncolour_desc'] = 'La couleur du bouton Afficher le sujet.';

$string['defaultoverlaycolour'] = 'Couleur de superposition par défaut lorsque l\'utilisateur survole les activités';
$string['defaultoverlaycolour_desc'] = 'La couleur de superposition lorsque l\'utilisateur survole les activités.';

$string['enablepagination'] = 'Activer la pagination';
$string['enablepagination_desc'] = 'Cela permet d\'afficher plusieurs pages lorsque le nombre de sections/activités est très élevé.';

$string['defaultnumberoftopics'] = 'Nombre par défaut de sujets par page';
$string['defaultnumberoftopics_desc'] = 'Le nombre de sujets à afficher sur une page';

$string['defaultnumberofactivities'] = 'Nombre par défaut d\'activités par page';
$string['defaultnumberofactivities_desc'] = 'Le nombre d\'activités à afficher sur une page';

$string['off'] = 'Désactivé';
$string['on'] = 'Activé';

$string['defaultshowsectiontitlesummary'] = 'Afficher le résumé du titre de la section lors du survol';
$string['defaultshowsectiontitlesummary_desc'] = 'Afficher le résumé du titre de la section lors du survol sur la boîte de la grille.';
$string['sectiontitlesummarymaxlength'] = 'Définir la longueur maximale du résumé de la section/activités.';
$string['sectiontitlesummarymaxlength_help'] = 'Définir la longueur maximale du résumé du titre de la section/activités affiché sur la carte.';
$string['defaultsectionsummarymaxlength'] = 'Définir la longueur maximale du résumé de la section/activités.';
$string['defaultsectionsummarymaxlength_desc'] = 'Définir la longueur maximale du résumé de la section/activités affiché sur la carte.';
$string['hidegeneralsectionwhenempty'] = 'Masquer la section générale lorsqu\'elle est vide';
$string['hidegeneralsectionwhenempty_help'] = 'Lorsque la section générale ne contient aucune activité ni aucun résumé, vous pouvez la masquer.';

// Section.
$string['sectionname'] = 'Section';
$string['sectionnamecaps'] = 'SECTION';
$string['section0name'] = 'Introduction';
$string['hidefromothers'] = 'Masquer la section';
$string['showfromothers'] = 'Afficher la section';
$string['viewtopic'] = 'Voir';
$string['editsection'] = 'Modifier la section';
$string['editsectionname'] = 'Modifier le nom de la section';
$string['newsectionname'] = 'Nouveau nom pour la section {$a}';
$string['currentsection'] = 'Cette section';
$string['addnewsection'] = 'Ajouter une section';
$string['moveresource'] = 'Déplacer la ressource';

// Activité.
$string['viewactivity'] = 'Voir l\'activité';
$string['markcomplete'] = 'Marquer comme complété';
$string['grade'] = 'Note';
$string['notattempted'] = 'Non tenté';
$string['subscribed'] = "Abonné";
$string['notsubscribed'] = "Non abonné";
$string['completed'] = "Complété";
$string['notcompleted'] = 'Non complété';
$string['progress'] = 'Progression';
$string['showinrow'] = 'Afficher en ligne';
$string['showincard'] = 'Afficher en carte';
$string['moveto'] = 'Déplacer vers';
$string['changelayoutnotify'] = 'Actualisez la page pour voir les changements.';
$string['generalactivities'] = 'Activités';
$string['coursecompletionprogress'] = 'Progression du cours';
$string['resumetoactivity'] = 'Reprendre';

// Pour le format liste.
$string['remuicourseformat'] = 'Choisir la mise en page';
$string['remuicourseformat_card'] = 'Mise en carte';
$string['remuicourseformat_list'] = 'Mise en liste';
$string['remuicourseformat_help'] = 'Choisir une mise en page de cours';
$string['remuicourseimage_filemanager'] = 'Image d\'en-tête du cours';
$string['remuicourseimage_filemanager_help'] = 'Cette image sera affichée dans la carte de la section générale en format carte et comme arrière-plan de la section générale en format liste. <strong> Taille d\'image recommandée 1272x288. <strong>';
$string['addsections'] = 'Ajouter des sections';
$string['teacher'] = 'Enseignant';
$string['teachers'] = 'Enseignants';
$string['remuiteacherdisplay'] = 'Afficher l\'image de l\'enseignant';
$string['remuiteacherdisplay_help'] = 'Afficher l\'image de l\'enseignant dans l\'en-tête du cours.';
$string['defaultremuiteacherdisplay'] = 'Afficher l\'image de l\'enseignant';
$string['defaultremuiteacherdisplay_desc'] = 'Afficher l\'image de l\'enseignant dans l\'en-tête du cours.';

$string['remuidefaultsectionview'] = 'Choisir l\'affichage par défaut des sections';
$string['remuidefaultsectionview_help'] = 'Choisir un affichage par défaut pour les sections du cours.';
$string['expanded'] = 'Développer tout';
$string['collapsed'] = 'Réduire tout';

$string['remuienablecardbackgroundimg'] = 'Image de fond de section';
$string['remuienablecardbackgroundimg_help'] = 'Activer l\'image de fond de section. Par défaut, elle est désactivée. Elle récupère l\'image du résumé de la section.';
$string['enablecardbackgroundimg'] = 'Afficher l\'image de fond de section en carte.';
$string['disablecardbackgroundimg'] = 'Masquer l\'image de fond de section en carte.';
$string['next'] = 'Suivant';
$string['previous'] = 'Précédent';

$string['remuidefaultsectiontheme'] = 'Choisir le thème par défaut des sections';
$string['remuidefaultsectiontheme_help'] = 'Choisir un thème par défaut pour les sections du cours.';

$string['dark'] = 'Sombre';
$string['light'] = 'Clair';

$string['defaultcardbackgroundcolor'] = 'Définir la couleur de fond de la section en format carte.';
$string['cardbackgroundcolor_help'] = 'Aide sur la couleur de fond de la carte.';
$string['cardbackgroundcolor'] = 'Définir la couleur de fond de la section en format carte.';
$string['defaultcardbackgroundcolordesc'] = 'Description de la couleur de fond de la carte';

// GDPR.
$string['privacy:metadata'] = 'Le plugin Formats de cours Edwiser ne stocke aucune donnée personnelle.';

// Validation.
$string['coursedisplay_error'] = 'Veuillez choisir la combinaison correcte de mise en page.';

// Textes d'activités complétées.
$string['activitystart'] = "Commencer";
$string['outof'] = 'sur';
$string['activitiescompleted'] = 'activités complétées';
$string['activitycompleted'] = 'activité complétée';
$string['activitiesremaining'] = 'activités restantes';
$string['activityremaining'] = 'activité restante';
$string['allactivitiescompleted'] = "Toutes les activités sont complétées";

// Utilisé dans format.js pour changer la mise en page du cours.
$string['showallsectionperpage'] = 'Afficher toutes les sections par page';

// Section générale en format carte.
$string['showfullsummary'] = '+ Afficher le résumé complet';
$string['showless'] = 'Afficher moins';
$string['showmore'] = 'Afficher plus';
$string['Complete'] = 'complet';

// Suivi d'utilisation.
$string['enableusagetracking'] = "Activer le suivi d'utilisation";
$string['enableusagetrackingdesc'] = "<strong>AVIS DE SUIVI D'UTILISATION</strong>

<hr class='text-muted' />

<p>Edwiser collectera désormais des données anonymes pour générer des statistiques d'utilisation du produit.</p>

<p>Ces informations nous aideront à orienter le développement dans la bonne direction et à faire prospérer la communauté Edwiser.</p>

<p>Cela dit, nous ne collectons pas vos données personnelles ni celles de vos étudiants dans ce processus. Vous pouvez désactiver cette fonctionnalité depuis le plugin à tout moment si vous souhaitez ne plus participer à ce service.</p>

<p>Un aperçu des données collectées est disponible <strong><a href='https://forums.edwiser.org/topic/67/anonymously-tracking-the-usage-of-edwiser-products' target='_blank'>ici</a></strong>.</p>";
$string['edw_format_hd_bgpos'] = "Position de l'image de fond de l'en-tête du cours";
$string['bottom'] = "bas";
$string['center'] = "centre";
$string['top'] = "haut";
$string['left'] = "gauche";
$string['right'] = "droite";
$string["edw_format_hd_bgpos_help"] = "Choisir la position de l'image de fond";

$string['edw_format_hd_bgsize'] = "Taille de l'image de fond de l'en-tête du cours";
$string['cover'] = "couvrir";
$string['contain'] = "contenir";
$string['auto'] = "auto";
$string['edw_format_hd_bgsize_help'] = "Sélectionner la taille de l'image de fond de l'en-tête du cours";
$string['courseinformation'] = "Informations sur le cours";
$string["defaultheader"] = 'Défaut ';
$string["remuiheader"] = 'En-tête';
$string["headereditingbutton"] = "Sélectionner la position du bouton d'édition";
$string['headereditingbutton_help'] = "Sélectionner la position du bouton d'édition. Ce paramètre ne fonctionnera pas dans remui, vérifiez les paramètres du cours";

$string['headeroverlayopacity'] = "Changer l'opacité de l'overlay de l'en-tête";
$string['headeroverlayopacity_help'] = "La valeur par défaut est déjà définie sur '100'. Pour ajuster l'opacité, veuillez entrer une valeur entre 0 et 100";
$string['viewalltext'] = 'Voir tout';
