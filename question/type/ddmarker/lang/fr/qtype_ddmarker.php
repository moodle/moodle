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
 *
 * @package    qtype
 * @subpackage ddmarker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addmoreitems'] = 'Vides pour {no} marqueurs supplémentaires';
$string['alttext'] = 'Etiquette';
$string['answer'] = 'Réponse';
$string['bgimage'] = 'Image de fond';
$string['coords'] = 'Coordonnées';
$string['correctansweris'] = 'La réponse correcte est : {$a}';
$string['draggableimage'] = 'Image déplaçable';
$string['draggableitem'] = 'Elément déplaçable';
$string['draggableitemheader'] = 'Elément déplaçable {$a}';
$string['draggableitemtype'] = 'Type';
$string['draggableword'] = 'Texte déplaçable';
$string['dropbackground'] = 'Image de fond pouvant être recouverte de marqueurs déplaçables';
$string['dropzone'] = 'Zone de dépôt {$a}';
$string['dropzoneheader'] = 'Zone de dépôt';
$string['followingarewrong'] = 'Les marqueurs suivants ont été placés dans la mauvaise zone : {$a}.';
$string['followingarewrongandhighlighted'] = 'Les marqueurs suivants étaient placés de manière incorrecte : {$a}. Les marqueurs en surbrillance sont à présent à leur place.<br /> Cliquez sur le marqueur pour mettre la zone autorisée en surbrillance.';
$string['formerror_nobgimage'] = 'Vous devez sélectionner une image à utiliser comme fond pour cette aire de glisser-déposer.';
$string['formerror_noitemselected'] = 'Vous avez spécifié une zone de dépôt mais n\'avez pas choisi de marqueur qui doive être déplacé sur cette zone';
$string['formerror_nosemicolons'] = 'Il n\'y a pas de point-virgule dans votre chaîne de caractère définissant les coordonnées. Vos coordonnées pour un(e) {$a->shape} doivent être exprimées de la manière suivante : {$a->coordsstring}.';
$string['formerror_onlysometagsallowed'] = 'Seules les balises "{$a}" sont autorisées dans le label d\'un marqueur';
$string['formerror_onlyusewholepositivenumbers'] = 'Veuillez n\'utiliser que des nombres entiers positifs pour spécifier les coordonnées x,y et/ou la largeur et la hauteur des formes. Vos coordonnées pour un(e) {$a->shape} doivent être exprimées de la manière suivante : {$a->coordsstring}.';
$string['formerror_polygonmusthaveatleastthreepoints'] = 'Vous devez spécifier au moins 3 points pour définir un polygone. Vos coordonnées pour un(e) {$a->shape} doivent être exprimées de la manière suivante : {$a->coordsstring}.';
$string['formerror_shapeoutsideboundsofbgimage'] = 'La forme que vous avez définie sort des limites de l\'image de fond';
$string['formerror_toomanysemicolons'] = 'Il y a trop d\'élément séparés par des points-virgules dans les coordonnées que vous avez spécifiées. Vos coordonnées pour un(e) {$a->shape} doivent être exprimées de la manière suivante : {$a->coordsstring}.';
$string['formerror_unrecognisedwidthheightpart'] = 'Impossible d\'évaluer la hauteur et la largeur spécifiées. Vos coordonnées pour un(e) {$a->shape} doivent être exprimées de la manière suivante : {$a->coordsstring}.';
$string['formerror_unrecognisedxypart'] = 'Impossible d\'évaluer les coordonnées x,y spécifiées. Vos coordonnées pour un(e) {$a->shape} doivent être exprimées de la manière suivante : {$a->coordsstring}.';
$string['infinite'] = 'Infini';
$string['marker'] = 'Marqueur';
$string['marker_n'] = 'Marqueur {no}';
$string['markers'] = 'Marqueurs';
$string['nolabel'] = 'Pas de label';
$string['pleasedragatleastonemarker'] = 'Votre réponse est incomplète, vous devez placer au moins un marqueur sur l\'image.';
$string['pluginname'] = 'Marqueurs à glisser-déposer';
$string['pluginname_help'] = 'Sélectionner un fichier pour l\'image de fond, entrez des labels pour les marqueurs et définissez des zones de dépôt pour ces marqueurs sur l\'image de fond.';
$string['pluginname_link'] = 'question/type/ddmarker';
$string['pluginnameadding'] = 'Ajouter des marqueurs à glisser-déposer';
$string['pluginnameediting'] = 'Editer des marqueurs à glisser-déposer';
$string['pluginnamesummary'] = 'Des marqueurs sont glissés et déposés sur une image de fond.';
$string['previewarea'] = 'Zone de prévisualisation -';
$string['previewareaheader'] = 'Aperçu';
$string['previewareamessage'] = 'Sélectionner un fichier pour l\'image de fond, entrez des labels pour les marqueurs et définissez des zones de dépôt pour ces marqueurs sur l\'image de fond.';
$string['refresh'] = 'Rafraîchir l\'aperçu';
$string['clearwrongparts'] = 'Déplacer les marqueurs qui ne sont pas à la bonne place à leur position de départ, en-dessous de l\'image';
$string['shape'] = 'Forme';
$string['shape_circle'] = 'Cercle';
$string['shape_circle_lowercase'] = 'cercle';
$string['shape_circle_coords'] = 'x,y;r (ou x,y sont les coordonnées xy du centre du cercle et r est son rayon)';
$string['shape_rectangle'] = 'Rectangle';
$string['shape_rectangle_lowercase'] = 'rectangle';
$string['shape_rectangle_coords'] = 'x,y;l,h (ou x,y sont les coordonnées xy du coin supérieur gauche du rectangle et l et h sont sa largeur et sa hauteur)';
$string['shape_polygon'] = 'Polygone';
$string['shape_polygon_lowercase'] = 'polygone';
$string['shape_polygon_coords'] = 'x1,y1;x2,y2;x3,y3;x4,y4....(ou x1, y1 sont les coordonnées x,y du premier sommet, x2, y2 sont celles du second, etc. Vous n\'avez pas besoin de répéter les coordonnées du premier sommet pour fermer le polygone)';
$string['showmisplaced'] = 'Mettre en surbrillance les zones de dépôt sur lesquelles le marqueur associé n\'a pas été déposé';
$string['shuffleimages'] = 'Mélanger les élements déplaçables chaque fois qu\'une question est tentée';
$string['stateincorrectlyplaced'] = 'Spécifier quels marqueurs sont incorrectement placés';
$string['summariseplace'] = '{$a->no}. {$a->text}';
$string['summariseplaceno'] = 'Zone de dépôt {$a}';
$string['ytop'] = 'Haut';