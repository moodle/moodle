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

$string['modulename'] = 'Contenu interactif';
$string['modulename_help'] = 'L\'activité H5P vous permet de créer des contenus interactifs tels que des vidéos interactives, des jeux de questions, des questions en glisser/déposer, des QCM, des présentations et bien plus encore.

En plus d\'être un outil auteur pour générer des contenus riches, H5P vous permet d\'importer ou d\'exporter vos ressources pour les réutiliser et les partager.

Les interactions utilisateur et les scores sont gérés par xAPI et disponibles dans le carnet de notes de moodle.

Vous pouvez ajouter des contenus interactifs H5P en uploadant des fichiers avec l\'extension .h5p. Vous pouvez créer et télécharger de tels fichiers sur le site h5p.org';
$string['modulename_link'] = 'https://h5p.org/moodle-more-help';
$string['modulenameplural'] = 'Contenus interactifs';
$string['pluginadministration'] = 'H5P';
$string['pluginname'] = 'H5P';
$string['intro'] = 'Introduction';
$string['h5pfile'] = 'Fichier H5P';
$string['fullscreen'] = 'Plein écran';
$string['disablefullscreen'] = 'Désactiver le plein écran';
$string['download'] = 'Télécharger';
$string['copyright'] = 'Droits d\'utilisation';
$string['embed'] = 'Embed';
$string['showadvanced'] = 'Afficher les options avancées';
$string['hideadvanced'] = 'Masquer les options avancées';
$string['resizescript'] = 'Incluez ce script dans votre site web si vous voulez bénéficier du redimensionnement dynamique de votre contenu embarqué:';
$string['size'] = 'Taille';
$string['close'] = 'Fermer';
$string['title'] = 'Titre';
$string['author'] = 'Auteur';
$string['year'] = 'Année';
$string['source'] = 'Source';
$string['license'] = 'Licence';
$string['thumbnail'] = 'Miniatures';
$string['nocopyright'] = 'Aucune information de copyright disponible pour ce contenu.';
$string['downloadtitle'] = 'Télécharger ce contenu au format H5P.';
$string['copyrighttitle'] = 'Voir les informations de droit d\'auteur pour ce contenu.';
$string['embedtitle'] = 'Voir le code embarqué pour ce contenu.';
$string['h5ptitle'] = 'Visitez H5P.org pour accéder à d\'autres ressources aussi cools.';
$string['contentchanged'] = 'Ce contenu a changé depuis votre dernière utilisation.';
$string['startingover'] = "Vous allez recommencer.";
$string['confirmdialogheader'] = 'Confirmez l\'action';
$string['confirmdialogbody'] = 'Merci de confirmer votre action. Cette opération est irréversible.';
$string['cancellabel'] = 'Annuler';
$string['confirmlabel'] = 'Confirmer';
$string['noh5ps'] = 'Il n\'y a aucune ressource interactive disponible pour ce cours.';

$string['lookforupdates'] = 'Rechercher des mises à jour H5P';
$string['removetmpfiles'] = 'Supprimer les anciens fichiers temporaires H5P';
$string['removeoldlogentries'] = 'Supprimer les anciennes entrées de logs H5P';

// Admin settings.
$string['displayoptionnevershow'] = 'Never show';
$string['displayoptionalwaysshow'] = 'Always show';
$string['displayoptionpermissions'] = 'Show only if user has permissions to export H5P';
$string['displayoptionpermissionsembed'] = 'Show only if user has permissions to embed H5P';
$string['displayoptionauthoron'] = 'Controlled by author, default is on';
$string['displayoptionauthoroff'] = 'Controlled by author, default is off';
$string['displayoptions'] = 'Afficher les options';
$string['enableframe'] = 'Afficher la barre de menu des actions';
$string['enabledownload'] = 'Bouton de téléchargement';
$string['enableembed'] = 'Bouton d\'intégration';
$string['enablecopyright'] = 'Bouton de copyright';
$string['enableabout'] = 'Bouton à propos de H5P';

$string['sendusagestatistics'] = 'Contribute usage statistics';
$string['sendusagestatistics_help'] = 'Usage statistics numbers will automatically be reported to help the developers better understand how H5P is used and to determine potential areas of improvement. Read more about which <a {$a}>data is collected on h5p.org</a>.';
$string['enablesavecontentstate'] = 'Sauvegarder l\'état du contenu actuel';
$string['enablesavecontentstate_help'] = 'Sauvegarder automatiquement l\'état actuel du contenu interactif pour chaque utilisateur. Ceci signifie que l\'utilisateur pourra reprendre là où il en est resté la fois précédente.';
$string['contentstatefrequency'] = 'Fréquence des sauvegardes d\'état de vos contenus';
$string['contentstatefrequency_help'] = 'Fréquence des sauvegardes automatiques de la progression des utilisateurs en secondes. Augmentez ce nombre si vous rencontrez des problèmes avec des requêtes ajax';

// Admin menu.
$string['settings'] = 'Paramètres H5P';
$string['libraries'] = 'Bibliothèques H5P';

// Upload libraries section.
$string['uploadlibraries'] = 'Uploader les bibliothèques';
$string['options'] = 'Options';
$string['onlyupdate'] = 'Ne mettre à jour que les bibliothèques existantes';
$string['disablefileextensioncheck'] = 'Désactiver la vérification des extensions de fichiers';
$string['disablefileextensioncheckwarning'] = "Attention! Désactiver la vérification des extensions de fichiers peut entrainer des failles de sécurité puisqu\'il est alors possible de télécharger des fichiers php. Ceci permettrait à un attaquant d\'executer du code malicieux sur vos site. Vérifiez bien que vous savez ce que vous uploadez.";
$string['upload'] = 'Uploader';

// Installed libraries section.
$string['installedlibraries'] = 'Bibliothèques installées';
$string['invalidtoken'] = 'Token de sécurité invalide.';
$string['missingparameters'] = 'Paramètres manquants';
$string['nocontenttype'] = 'No content type was specified.';
$string['invalidcontenttype'] = 'The chosen content type is invalid.';
$string['installdenied'] = 'You do not have permission to install content types. Contact the administrator of your site.';
$string['downloadfailed'] = 'Downloading the requested library failed.';
$string['validationfailed'] = 'The requested H5P was not valid';
$string['validatingh5pfailed'] = 'Validating h5p package failed.';

// H5P library list headers on admin page.
$string['librarylisttitle'] = 'Titre';
$string['librarylistrestricted'] = 'Restreint';
$string['librarylistinstances'] = 'Instances';
$string['librarylistinstancedependencies'] = 'Dépendances d\'instance';
$string['librarylistlibrarydependencies'] = 'Dépendances des bibliothèques';
$string['librarylistactions'] = 'Actions';

// H5P library page labels.
$string['addlibraries'] = 'Ajouter des bibliothèques';
$string['installedlibraries'] = 'Bibliothèques installées';
$string['notapplicable'] = 'N/A';
$string['upgradelibrarycontent'] = 'Mettre à jour les contenus des bibliothèques';

// Upgrade H5P content page.
$string['upgrade'] = 'Mettre à jour H5P';
$string['upgradeheading'] = 'Mettre à jour le contenu {$a}';
$string['upgradenoavailableupgrades'] = 'Il n\'y a aucune mise à jour disponible pour cette bibliothèque.';
$string['enablejavascript'] = 'Merci d\'activer JavaScript.';
$string['upgrademessage'] = 'Vous êtes sur le point de mettre à jour {$a} instance(s). Sélectionnez la version.';
$string['upgradeinprogress'] = 'Mise à jour vers la version %ver en cours...';
$string['upgradeerror'] = 'Une erreur est survenue durant l\'analyse des paramètres:';
$string['upgradeerrordata'] = 'Impossible de charger les données pour la bibliothèque %lib.';
$string['upgradeerrorscript'] = 'Impossible de charger les scripts de mise à jour pour %lib.';
$string['upgradeerrorcontent'] = 'Impossible de mettre à jour le contenu %id:';
$string['upgradeerrorparamsbroken'] = 'Les paramètres sont invalides.';
$string['upgradedone'] = 'Vous avez mis à jour {$a} instance(s) avec succès.';
$string['upgradereturn'] = 'Retour';
$string['upgradenothingtodo'] = "Il n\'y a aucune instance à mettre à jour.";
$string['upgradebuttonlabel'] = 'Mettre à jour';
$string['upgradeinvalidtoken'] = 'Erreur : Token de sécruité invalide!';
$string['upgradelibrarymissing'] = 'Erreur : Votre bibliothèque est manquante!';

// Results / report page.
$string['user'] = 'Utilisateur';
$string['score'] = 'Score';
$string['maxscore'] = 'Score Maximum';
$string['finished'] = 'Terminé';
$string['loadingdata'] = 'Chargement des données.';
$string['ajaxfailed'] = 'Le chargement des données a échoué.';
$string['nodata'] = "Il n\'y a aucune donnée correspondant à vos critères.";
$string['currentpage'] = 'Page $current sur $total';
$string['nextpage'] = 'Page suivante';
$string['previouspage'] = 'Page précédente';
$string['search'] = 'Rechercher';
$string['empty'] = 'Aucun résultat disponible';
$string['viewreportlabel'] = 'Report';
$string['dataviewreportlabel'] = 'View Answers';
$string['invalidxapiresult'] = 'No xAPI results were found for the given content and user id combination';
$string['reportnotsupported'] = 'Not supported';
$string['reportingscorelabel'] = 'Score:';
$string['reportingscaledscorelabel'] = 'Gradebook score:';
$string['reportingscoredelimiter'] = 'out of';
$string['reportingscaledscoredelimiter'] = ',';
$string['reportingquestionsremaininglabel'] = 'questions remaining to grade';
$string['reportsubmitgradelabel'] = 'Submit grade';
$string['noanswersubmitted'] = 'This user hasn\'t submitted an answer to the H5P yet';


// Editor.
$string['javascriptloading'] = 'Chargement de JavaScript...';
$string['action'] = 'Action';
$string['upload'] = 'Uploader';
$string['create'] = 'Créer';
$string['editor'] = 'Editeur';

$string['invalidlibrary'] = 'Bibliothèque invalide';
$string['nosuchlibrary'] = 'Aucune bibliothèque de ce type';
$string['noparameters'] = 'Pas de paramètres';
$string['invalidparameters'] = 'Paramètres invalides';
$string['missingcontentuserdata'] = 'Erreur : impossible de trouver les données utilisateur';

$string['maximumgrade'] = 'Maximum grade';
$string['maximumgradeerror'] = 'Please enter a valid positive integer as the max points available for this activity';

// Capabilities.
$string['hvp:view'] = 'See and interact with H5P activities';
$string['hvp:addinstance'] = 'Ajouter une nouvelle activité H5P';
$string['hvp:manage'] = 'Edit existing H5P activites';
$string['hvp:getexport'] = 'Récupérer un fichier H5P dans un cours';
$string['hvp:getembedcode'] = 'View H5P embed code when \'controlled by permission\' option is set';
$string['hvp:saveresults'] = 'Sauvegarder les résultats';
$string['hvp:savecontentuserdata'] = 'Sauvegarder les données utilisateur H5P';
$string['hvp:viewresults'] = 'Visualiser les résultats';
$string['hvp:viewallresults'] = 'View result for all users in course';
$string['hvp:restrictlibraries'] = 'Restreindre une bibliothèque H5P';
$string['hvp:userestrictedlibraries'] = 'Utiliser des bibliothèques H5P restreintes';
$string['hvp:updatelibraries'] = 'Mettre à jour la version d\'une bibliothèque H5P';
$string['hvp:getcachedassets'] = 'Récupérer les assets mis en cache';
$string['hvp:installrecommendedh5plibraries'] = 'Install new safe H5P content types recommended by H5P.org';

// Capabilities error messages.
$string['nopermissiontoupgrade'] = 'Vous n\'avez pas les droits pour mettre à jour les bibliothèques.';
$string['nopermissiontorestrict'] = 'Vous n\'avez pas les droits pour restreindre les biliothèques.';
$string['nopermissiontosavecontentuserdata'] = 'Vous n\'avez pas les droits pour sauvegarder les données utilisateur.';
$string['nopermissiontosaveresult'] = 'Vous n\'avez pas les droits pour sauvegarder les résultats pour ce contenu.';
$string['nopermissiontoviewresult'] = 'Vous n\'avez pas les droits pour visualiser les résultats de ce contenu.';

// Editor translations.
$string['noziparchive'] = 'Votre version de PHP ne supporte pas ZipArchive.';
$string['noextension'] = 'Le fichier que vous avez uploadé n\'est pas un package HTML5 valide (il n\'a pas l\'extension .h5p)';
$string['nounzip'] = 'Le fichier que vous avez uploadé n\'est pas un package HTML5 valide (impossible de le décompresser)';
$string['noparse'] = 'Impossible de parser le fichier h5p.json';
$string['nojson'] = 'Le fichier h5p.json n\'est pas valide';
$string['invalidcontentfolder'] = 'Répertoire de contenu invalide';
$string['nocontent'] = 'Impossible de trouver ou de parser le fichier content.json';
$string['librarydirectoryerror'] = 'Le nom du répertoire des bibliothèques doit être de la forme machineName ou machineName-majorVersion.minorVersion (issue de library.json). (Répertoire: {$a->%directoryName} , machineName: {$a->%machineName}, majorVersion: {$a->%majorVersion}, minorVersion: {$a->%minorVersion})';
$string['missingcontentfolder'] = 'Un répertoire valide de contenu est manquant';
$string['invalidmainjson'] = 'Un fichier h5p.json valide est manquant';
$string['missinglibrary'] = 'La bibliothèque requise {$a->@library} est manquante';
$string['missinguploadpermissions'] = "Notez que les bibliothèques doivent exister dans le fichier uploadé, mais vous n\'avez pas autorisé l\'upload de nouvelles bibliothèques. Contactez votre administrateur.";
$string['invalidlibraryname'] = 'Nom de bibliothèque non valide: {$a->%name}';
$string['missinglibraryjson'] = 'Impossible de trouver le fichier library.json file avec un format json valide pour la bibliothèque {$a->%name}';
$string['invalidsemanticsjson'] = 'Le fichier semantics.json inclu dans la bibliothèque {$a->%name} n\'est pas valide';
$string['invalidlanguagefile'] = 'Le fichier de langue {$a->%file} de la bibliothèque {$a->%library} n\'est pas valide';
$string['invalidlanguagefile2'] = 'Le fichier de langue {$a->%languageFile} inclu dans la bibliothèque {$a->%name} n\'est pas valide';
$string['missinglibraryfile'] = 'Le fichier "{$a->%file}" de la bibliothèque "{$a->%name}" est manquant.';
$string['missingcoreversion'] = 'Le système n\'est pas en mesure d\'installer le composant <em>{$a->%component}</em> depuis le package, ceci nécessite une version plus récente du plugin H5P. Ce site utilise actuellement la version {$a->%current}, alors que la version requise est {$a->%required} ou supérieur. Vous devriez faire une mise à jour et essayer à nouveau.';
$string['invalidlibrarydataboolean'] = 'La donnée fournie pour la proriété {$a->%property} de la bibliothèque {$a->%library} n\'est pas valide. Booléen attendu.';
$string['invalidlibrarydata'] = 'La donnée fournie pour la proriété {$a->%property} de la bibliothèque {$a->%library} n\'est pas valide';
$string['invalidlibraryproperty'] = 'Impossible de lire la propriété {$a->%property} de la bibliothèque {$a->%library}';
$string['missinglibraryproperty'] = 'La propriété requise {$a->%property} de la bibliothèque {$a->%library} est manquante';
$string['invalidlibraryoption'] = 'L\'option {$a->%option} de la bibliothèque {$a->%library} n\'est pas autorisée';
$string['addedandupdatedss'] = 'Added {$a->%new} new H5P library and updated {$a->%old} old one.';
$string['addedandupdatedsp'] = 'Added {$a->%new} new H5P library and updated {$a->%old} old ones.';
$string['addedandupdatedps'] = 'Added {$a->%new} new H5P libraries and updated {$a->%old} old one.';
$string['addedandupdatedpp'] = 'Added {$a->%new} new H5P libraries and updated {$a->%old} old ones.';
$string['addednewlibrary'] = 'Added {$a->%new} new H5P library.';
$string['addednewlibraries'] = 'Added {$a->%new} new H5P libraries.';
$string['updatedlibrary'] = 'Updated {$a->%old} H5P library.';
$string['updatedlibraries'] = 'Updated {$a->%old} H5P libraries.';
$string['missingdependency'] = 'la dépendance {$a->@dep} requise par {$a->@lib} est manquante.';
$string['invalidstring'] = 'La chaine fournie n\'est pas valide selon l\'expression régulière suivante : (value: \"{$a->%value}\", regexp: \"{$a->%regexp}\")';
$string['invalidfile'] = 'Le fichier "{$a->%filename}" n\est pas autorisé. Seuls les fichiers avec les extensions suivantes sont autorisés : {$a->%files-allowed}.';
$string['invalidmultiselectoption'] = 'éléments selectionnés dans un multi-select non valides.';
$string['invalidselectoption'] = 'élément sélectionné dans un select non valide.';
$string['invalidsemanticstype'] = 'Erreur interne H5P: Type de contenu "{$a->@type}" non valide. Supprimez ce contenu!';
$string['invalidsemantics'] = 'La bibliothèque utilisée dans cette ressource n\'est pas valide';
$string['unabletocreatedir'] = 'Impossible de créer le répertoire.';
$string['unabletogetfieldtype'] = 'Impossible de récupérer le type de champ.';
$string['filetypenotallowed'] = 'Type de fichier non autorisé.';
$string['invalidfieldtype'] = 'Type de champ invalide.';
$string['invalidimageformat'] = 'Format de fichier image non valide. Utilisez jpg, png ou gif.';
$string['filenotimage'] = 'Ce fichier n\'est pas une image.';
$string['invalidaudioformat'] = 'Format de fichier audio non valide. Utilisez mp3 ou wav.';
$string['invalidvideoformat'] = 'Format de fichier vidéo non valide. Utilisez mp4 ou webm.';
$string['couldnotsave'] = 'Impossible de sauvegarder le fichier.';
$string['couldnotcopy'] = 'Impossible de copier le fichier.';
$string['librarynotselected'] = 'You must select a content type.';

// Welcome messages.
$string['welcomeheader'] = 'Bienvenue dans le monde H5P!';
$string['welcomegettingstarted'] = 'Pour démarrer avec H5P et Moodle, consultez nos tutoriels <a {$a->moodle_tutorial}>tutorial</a> et testez <a {$a->example_content}>nos exemples</a> sur le site H5P.org pour vous en inspirer.';
$string['welcomecommunity'] = 'Nous espérons que vous allez apprécier H5P et rejoindre notre communauté en constante augmentation au travers de nos <a {$a->forums}>forums</a>.';
$string['welcomecontactus'] = 'Si vous avez des suggestions, n\'hésitez pas à <a {$a}>nous contacter</a>. Nous prenons toutes les suggestions très sérieuse en considération pour rendre H5P meilleur chaque jour !';
$string['invalidlibrarynamed'] = 'The H5P library {$a->%library} used in the content is not valid';

// Licensing.
$string['copyrightinfo'] = 'Information copyright';
$string['years'] = 'Année(s)';
$string['undisclosed'] = 'Masqué';
$string['attribution'] = 'Attribution 4.0';
$string['attributionsa'] = 'Attribution - Partage dans les Mêmes Conditions 4.0';
$string['attributionnd'] = 'Attribution - Pas de Modification 4.0';
$string['attributionnc'] = 'Attribution - Non Commercial 4.0';
$string['attributionncsa'] = 'Attribution - Non Commercial- Partage dans les Mêmes Conditions 4.0';
$string['attributionncnd'] = 'Attribution - Non Commercial- Non Commercial 4.0';
$string['gpl'] = 'Licence GPL v3';
$string['pd'] = 'Domaine Public';
$string['pddl'] = 'Transfert dans le Domaine Public et Licence';
$string['pdm'] = 'Marque du domaine public';
$string['copyrightstring'] = 'Copyright';
$string['by'] = 'by';
$string['showmore'] = 'Show more';
$string['showless'] = 'Show less';
$string['sublevel'] = 'Sublevel';
$string['noversionattribution'] = 'Attribution';
$string['noversionattributionsa'] = 'Attribution-ShareAlike';
$string['noversionattributionnd'] = 'Attribution-NoDerivs';
$string['noversionattributionnc'] = 'Attribution-NonCommercial';
$string['noversionattributionncsa'] = 'Attribution-NonCommercial-ShareAlike';
$string['noversionattributionncnd'] = 'Attribution-NonCommercial-NoDerivs';
$string['licenseCC40'] = '4.0 International';
$string['licenseCC30'] = '3.0 Unported';
$string['licenseCC25'] = '2.5 Generic';
$string['licenseCC20'] = '2.0 Generic';
$string['licenseCC10'] = '1.0 Generic';
$string['licenseGPL'] = 'General Public License';
$string['licenseV3'] = 'Version 3';
$string['licenseV2'] = 'Version 2';
$string['licenseV1'] = 'Version 1';
$string['licenseCC010'] = 'CC0 1.0 Universal (CC0 1.0) Public Domain Dedication';
$string['licenseCC010U'] = 'CC0 1.0 Universal';
$string['licenseversion'] = 'License Version';

// Embed.
$string['embedloginfailed'] = 'You do not have access to this content. Try logging in.';
