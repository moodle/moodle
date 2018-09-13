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
 * English strings for the lightboxgallery module
 *
 * @package   mod_lightboxgallery
 * @copyright 2011 John Kelsh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['acceptablefiletypebriefing'] = 'Si vous désirez charger plusieurs fichiers en une seule fois, vous pouvez utiliser un fichier zip contenant des images. Celles-ci seront automatiquement ajoutées à la gallerie.';
$string['addcomment'] = 'Ajouter un commentaire';
$string['addimage'] = 'Ajouter des images';
$string['addimage_help'] = 'Naviguez vers une image sur votre machine locale pour l\'ajouter à la gallerie en cours.

Vous pouvez également sélectionner une archive zip contenant de multiples images. Celles-ci seront extraites dans le dossier image une fois le chargement terminé.';
$string['autoresize'] = 'Redimensionnement automatique';
$string['autoresize_help'] = 'Vous pouvez contrôler si et quand les galleries d\'images sont redimensionnées. Les méthodes suivantes sont disponibles lors de la configuration d\'une gallerie.

* Ecran: les images qui sont plus grandes que l\'écran des utilisateurs seront réduites pour correspondre à la taille de l\'écran.
* Upload: les images seront redimensionnées aux dimensions spécifiées lors de leur chargement à travers l\'option \'Ajouter des images\'.

Il existe également un plugin de redimensionnement inclus dans l\'éditeur d\'images que vous pouvez utiliser pour redimensionner les images manuellement.';
$string['allowcomments'] = 'Autoriser les commentaires';
$string['allowrss'] = 'Autoriser les flux RSS';
$string['allpluginsdisabled'] = 'Désolé, tous les plugins d\'édition sont actuellement désactivés.';
$string['backtogallery'] = 'Retour à la gallerie';
$string['captionfull'] = 'Afficher l\'intégralité du sous-titre?';
$string['captionpos'] = 'Position du sous-titre';
$string['commentadded'] = 'Votre commentaire a été posté dans la gallerie';
$string['commentcount'] = '{$a} commentaires';
$string['commentdelete'] = 'Confirmer la suppression du commentaire?';
$string['configdisabledplugins'] = 'Plugins désactivés';
$string['configdisabledpluginsdesc'] = 'Sélectionnez les plugins d\'édition d\'image que vous souhaitez désactiver.';
$string['configenablerssfeeds'] = 'Activer les flux RSS';
$string['configenablerssfeedsdesc'] = 'Autoriser les galleries à générer des flux RSS.';
$string['configimagelifetime'] = 'Durée de vie de l\'image';
$string['configimagelifetimedesc'] = 'Durée (en secondes) durant laquelle l\'image restera stockée dans le cache du navigateur.';
$string['configoverwritefiles'] = 'Remplacer les fichiers';
$string['configoverwritefilesdesc'] = 'Remplacer les images quand de nouvelles images sont uploadées avec le même nom';
$string['configstrictfilenames'] = 'Utiliser des noms de fichier stricts';
$string['configstrictfilenamesdesc'] = 'Forcer l\'éditeur d\'images à nettoyer les noms de fichiers suivant les règles de nomenclature Moodle.';
$string['currentsize'] = 'Taille actuelle';
$string['dimensions'] = 'Dimensions';
$string['dirup'] = 'Haut';
$string['dirdown'] = 'Bas';
$string['dirleft'] = 'Gauche';
$string['dirright'] = 'Droite';
$string['displayinggallery'] = 'Gallerie d\'affichage: {$a}';
$string['editimage'] = 'Editer l\'image';
$string['edit_choose'] = 'Choix...';
$string['edit_caption'] = 'Sous-titre';
$string['edit_crop'] = 'Tronquer';
$string['edit_delete'] = 'Effacer';
$string['edit_flip'] = 'Mirroir';
$string['edit_resize'] = 'Redimensionner';
$string['edit_resizescale'] = 'Taille';
$string['edit_rotate'] = 'Rotation';
$string['edit_tag'] = 'Etiquette';
$string['edit_thumbnail'] = 'Miniature';
$string['errornofile'] = 'Le fichier requis n\'a pu être trouvé: {$a}';
$string['errornoimages'] = 'Aucune image n\'a été trouvée dans cette gallerie';
$string['errornosearchresults'] = 'Votre requête n\'a retourné aucune image';
$string['erroruploadimage'] = 'Le fichier que vous uploadez doit être un fichier image';
$string['extendedinfo'] = 'Afficher toutes les informations sur l\'image';
$string['imageadd'] = 'Ajouter des images';
$string['imagecount'] = 'Compter les images';
$string['imagecounta'] = '{$a} images';
$string['imagedirectory'] = 'Dossier images';
$string['imagedirectory_help'] = 'Sélectionner le répertoire qui contient les images que vous voulez afficher dans la gallerie. Lorsque vous utilisez l\'option \'Ajouter des images\', les images chargées seront placées dans ce répértoire.';
$string['imagedownload'] = 'Télécharger l\'image';
$string['imageresized'] = 'Image redimensionnée: {$a}';
$string['images'] = 'Images';
$string['imagesperpage'] = 'Images par page';
$string['imagesperrow'] = 'Images par ligne';
$string['imageuploaded'] = 'Image uploadée: {$a}';
$string['invalidquizid'] = 'Lightboxgallery ID invalide';
$string['lightboxgallery'] = 'Gallerie Lightbox';
$string['lightboxgallery:addcomment'] = 'Ajouter un commentaire à la gallerie lightbox';
$string['lightboxgallery:addinstance'] = 'Ajouter une nouvelle gallerie lightbox';
$string['lightboxgallery:addimage'] = 'Ajouter une image à la gallerie lightbox';
$string['lightboxgallery:edit'] = 'Editer une gallerie lightbox';
$string['lightboxgallery:submit'] = 'Publier une gallerie lightbox';
$string['lightboxgallery:viewcomments'] = 'Voir les commentaires de la gallerie lightbox';
$string['makepublic'] = 'Rendre publique';
$string['metadata'] = 'Données meta';
$string['modulename'] = 'Gallerie Lightbox';
$string['modulename_help'] = 'Le module lightboxgallery permet aux partipants de voir une gallerie d\'images.

Cette ressource vous permet de créer des galleries d\'images \'Lightbox\' dans votre cours Moodle.

En tant qu\'enseignant, vous avez la possibilité de créer, éditer et effacer des galleries. Des miniatures seront générées et seront utilisées pour l\'affichage de la gallerie.
Un clic sur l\'une de ces miniatures donnera le focus à l\'image sélectionnée et permettra de faire défiler la gallerie à votre guise. Utiliser les scripts Lightbox permet d\'obtenir de jolis effets de transition au chargement et au défilement des images.

Si activé, les utilisateurs seront capable de laisser des commentaires sur votre gallerie.';
$string['modulenameplural'] = 'Galleries Lightbox';
$string['modulenameshort'] = 'Gallerie';
$string['modulenameadd'] = 'Gallerie Lightbox';
$string['newgallerycomments'] = 'Nouveau commentaire de gallerie';
$string['norssfeedavailable'] = 'Flux inaccessible';
$string['position_bottom'] = 'Bas';
$string['position_top'] = 'Haut';
$string['pluginadministration'] = 'Administration de la gallerie Lightbox';
$string['pluginname'] = 'Lightbox Gallery';
$string['resizeto'] = 'Redimmensionner à';
$string['rsssubscribe'] = 'Flux RSS de la gallerie';
$string['saveimage'] = 'Enregistrer {$a}';
$string['screen'] = 'Ecran';
$string['selectflipmode'] = 'Sélectionner un mode mirroir';
$string['selectrotation'] = 'Sélectionner un angle de rotation';
$string['selectthumbpos'] = 'Décalage des miniatures (depuis le centre)';
$string['setasindex'] = 'Définir comme image d\'index';
$string['showall'] = 'Tout montrer';
$string['tagscurrent'] = 'Etiquettes actuelles';
$string['tagsdisabled'] = 'L\'éditeur d\'étiquettes est désactivé';
$string['tagsimport'] = 'Importer des étiquettes';
$string['tagsimportconfirm'] = 'Etes vous certain de vouloir importer les étiquettes de toutes les images de cette gallerie?';
$string['tagsimportfinish'] = 'Importation de {$a->tags} étiquettes provenant de {$a->images} terminées';
$string['tagsiptc'] = 'Etiquettes IPTC';
$string['tagspopular'] = 'Etiquettes populaires';
$string['tagsrelated'] = 'Etiquettes liées';
$string['thumbnailoffset'] = 'Décalage';
$string['zipextracted'] = 'Fichier zip extrait: {$a}';
$string['zipnonewfiles'] = 'Aucune nouvelle image trouvée - vérifiez que les images sont bien dans le dossier racine ou dans l\'archive';
