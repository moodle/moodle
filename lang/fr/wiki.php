<?php // $Id$

$string['modulename'] = 'Wiki';
$string['modulenameplural'] = 'Wikis';

$string['wikiname'] = 'Nom de la page';
$string['wikitype'] = 'Type';
$string['ewikiprinttitle'] = 'Afficher sur chaque page le nom du wiki.';
$string['htmlmode'] = 'Mode HTML';
$string['ewikiacceptbinary'] = 'Permettre les fichiers binaires';
$string['initialcontent'] = 'Choisir une page initiale';
$string['chooseafile'] = 'Sélectionner/déposer une page initiale';
$string['pagenamechoice'] = '- ou -';

$string['wikidefaultpagename'] = 'IndexWiki';
$string['wikistartederror'] = 'Le wiki a déjà des entrées - impossible de modifier.';
$string['nowikicreated'] = 'Aucune entrée n\'a été créée dans ce wiki.';
$string['wikiusage'] = 'Utilisation de ce wiki';

$string['nohtml'] = 'Pas de code HTML';
$string['safehtml'] = 'HTML sûr';
$string['htmlonly'] = 'HTML uniquement';

$string['searchwiki'] = 'Recherche dans le wiki';
$string['wikilinks'] = 'Liens wiki';
$string['choosewikilinks'] = '-- Sélectionner les liens wiki --';
$string['viewpage'] = 'Afficher la page';
$string['sitemap'] = 'Carte du site';
$string['pageindex'] = 'Index des pages';
$string['newestpages'] = 'Pages récentes';
$string['mostvisitedpages'] = 'Pages les plus visitées';
$string['mostoftenchangedpages'] = 'Pages les plus souvent modifiées';
$string['updatedpages'] = 'Pages modifiées';
$string['orphanedpages'] = 'Pages orphelines';
$string['orphanedpage'] = 'Page orpheline';
$string['wantedpages'] = 'Pages désirées';
$string['filedownload'] = 'Dépôt de fichier';
$string['for'] = 'pour';
$string['groups'] = 'Groupes';

$string['action'] = '-- Action --';
$string['otherwikis'] = 'Autres wikis';
$string['pageactions'] = 'Actions de page';
$string['editthispage'] = 'Modifier cette page';
$string['backlinks'] = 'Références';
$string['pageinfo'] = 'Information de la page';
$string['attachments'] = 'Annexe de la page';
$string['howtowiki'] = 'Qu\'est-ce qu\'un wiki';

$string['chooseadministration'] = '-- Administration --';
$string['administration'] = 'Administration';
$string['notadministratewiki'] = 'Vous n\'êtes pas autorisé à administrer ce wiki !';
$string['noadministrationaction'] = 'Aucune action d\'administration donnée.';
$string['setpageflags'] = 'Fixer les attributs de la page';
$string['nocandidatestoremove'] = 'No candidate pages to remove, choose \'$a\' to show all pages.';
$string['removepages'] = 'Supprimer des pages';
$string['pagesremoved'] = 'Pages supprimées.';
$string['checklinkscheck'] = 'Voulez-vous vraiment vérifier les liens de cette page :';
$string['checklinks'] = 'Vérifiez les liens';
$string['linkschecked'] = 'Liens vérifiés';
$string['checklinksnotice'] = 'Merci de patienter durant cette opération.';
$string['removenotice'] = 'Seules les pages non référencées seront listées ici. Comme le moteur ewiki ne fait que des tests limités sur les références, il est possible que certaines d\'entre elles manquent ici.<br />Cependant si vous videz une page d\'abord, elle sera aussi affichée ici. D\'autres diagnostics sont également effectués.';
$string['removepagecheck'] = 'Voulez-vous vraiment supprimer ces pages ?';
$string['pagename'] = 'Nom de page';
$string['errororreason'] = 'Erreur ou cause';
$string['listall'] = 'Tout afficher';
$string['listcandidates'] = 'Afficher les candidats';
$string['removeselectedpages'] = 'Supprimer les pages sélectionnées';
$string['disabledpage'] = 'Page désactivées';
$string['errorbinandtxt'] = 'Erreur d\'attribut : page de type BIN et TXT';
$string['errornotype'] = 'Erreur d\'attribut : page ni BIN ni TXT';
$string['errorhtml'] = 'Page de type HTML';
$string['readonly'] = 'Page en lecture seule';
$string['ownerunknown'] = 'inconnu';
$string['errorroandwr'] = 'Erreur d\'attribut : la page est autorisée en écriture et en lecture seule';
$string['errorsize'] = 'Taille de la page supérieure à 64KiB';
$string['emptypage'] = 'Page vide';
$string['flags'] = 'Attributs';
$string['status'] = 'Statut';
$string['flagsset'] = 'Attributs modifiés';
$string['strippages'] = 'Purger les pages';
$string['strippagecheck'] = 'Voulez-vous vraiment purger les anciennes versions de ces pages :';
$string['nothingtostrip'] = 'Il n\'y a pas de pages avec plus d\'une version.';
$string['version'] = 'Version';
$string['versions'] = 'Versions';
$string['pagesstripped'] = 'Pages purgées.';
$string['wrongversionrange'] = '$a n\'est pas un intervalle correct !';
$string['versionrangetoobig'] = 'Il n\'est pas permis de supprimer toutesles versions d\'une page ! La dernière version doit persister.';
$string['linkok'] = 'OK';
$string['linkdead'] = 'MORT';
$string['offline'] = 'HORS-LIGNE';
$string['nolinksfound'] = 'Aucun lien trouvé sur la page.';
$string['revertpages'] = 'Rétablir les modifications effectuées';
$string['pagesreverted'] = 'Modifications rétablies';
$string['revertpagescheck'] = 'Voulez-vous vraiment rétablir les modifications suivantes :';
$string['revertchanges'] = 'Rétablir les modifications';
$string['versionstodelete'] = 'Version(s) à supprimer';
$string['nochangestorevert'] = 'Aucune modification à rétablir.';
$string['authorfieldpattern'] = 'Author field pattern';
$string['noregexp'] = 'Ceci doit être une chaîne de caractères fixe (Pas de * ni d\'expression régulière), de préférence l\'adresse IP du saboteur ou son nom d\'hôte ; ne pas inclure le numéro de port (qui augmente à chaque requête http).';
$string['changesfield'] = 'Combien d\'heures depuis le dernier changement';
$string['howtooperate'] = 'Comment collaborer';
$string['authorfieldpatternerror'] = 'Veuillez saisir un auteur.';
$string['deleteversionserror'] = 'Veuillez saisir un numéro de version correct.';
$string['changesfielderror'] = 'Veuillez saisir une heure correcte.';
$string['revertlastonly'] = 'Seulement s\'il s\'agit du dernier changement';
$string['revertallsince'] = 'Rétablir également les modifications effectuées après';
$string['revertthe'] = 'Rétablir les modifications';
$string['deleteversions'] = 'Combien de dernières versions à détruire';
$string['deletepage'] = 'Supprimer la page';

# Filter Name
$string['filtername'] = 'Liens automatiques des pages wiki';


# Flags, please be careful when translating
$string['flagtxt'] = 'TXT';
$string['flagbin'] = 'BIN';
$string['flagoff'] = 'OFF';
$string['flaghtm'] = 'HTM';
$string['flagro'] = 'RO';
$string['flagwr'] = 'WR';

# This one has to be a WikiWord !!!
$string['deletemewikiword'] = 'SupprimezMoi';
$string['deletemewikiwordfound'] = '$a trouvé sur la page';

$string['submit'] = 'Envoyer';

# Ewiki
$string['editform1'] = 'Ne vous focalisez pas trop sur le formatage, il peut toujours être amélioré ultérieurement.';
$string['editform2'] = 'Veuillez écrire intelligemment, et souvenez-vous que toutes les modifications sont enregistrées.';
$string['save'] = 'Enregistrer';
$string['preview'] = 'Prévisualiser';
$string['canceledit'] = 'Annuler';
$string['uploadpicturebutton'] = 'Déposer';
$string['lastchanged'] = 'Dernière modification le $a';
$string['hits'] = '$a requêtes';
$string['changes'] = '$a modifications';
$string['upload0'] = 'Utilisez ce formulaire pour déposer un fichier binaire dans ce wiki :';
$string['uplnewnam'] = 'Enregistrer sous un nom différent';
$string['uplok'] = 'Votre fichier a été déposé correctement.';
$string['uplerror'] = 'Désolé, votre fichier n\'a pas pu être déposé.';
$string['dwnlnofiles'] = 'Aucun fichier déposé jusqu\'ici.';
$string['file'] = 'Fichier';
$string['uploadedon'] = 'Déposé le';
$string['fileisoftype'] = 'Le fichier est de type';
$string['downloadtimes'] = 'Téléchargé $a fois';
$string['of'] = 'sur';
$string['comment'] = 'Commentaire';
$string['dwnlsection'] = 'Télécharger la section';
$string['infoaboutpage'] = 'Information sur la page';
$string['thanksforcontribution'] = 'Merci pour votre contribution.';
$string['disabledpage'] = 'Cette page n\'est actuellement pas disponible.';
$string['doesnotexist'] = 'Cette page n\'existe pas encore. si vous voulez la créer, veuillez cliquer sur le bouton modifier.';
$string['errversionsave'] = 'Désolé, pendant que vous modifiiez cette page, quelqu\'un d\'autre a enregistré une version modifiée. Veuillez retourner à l\'écran précédent et coper vos modifications dans le presse-papiers de votre ordinateur afin de les insérer de nouveau dans l\'écran d\'édition.';
$string['forbidden'] = 'Vous n\'êtes pas autorisé à accéder à cette page.';
$string['binimgtoolarge'] = 'Le fichier image est trop gros !';
$string['binnoimg'] = 'Ce format de fichier ne peut pas être accepté !';
$string['browse'] = 'Consulter';
$string['fetchback'] = 'Fetch-back';
$string['differences'] = 'Différences entre les versions $a->new_ver et $a->old_ver de $a->pagename.';
$string['diff'] = 'Diff';
$string['author'] = 'Auteur';
$string['created'] = 'Créé';
$string['lastmodified'] = 'Dernière modification';
$string['meta'] = 'Méta-data';
$string['refs'] = 'Réferences';
$string['contentsize'] = 'Taille du contenu';
$string['pageslinkingto'] = 'Pages avec un lien vers cette page';
$string['viewsmfor'] = 'Afficher la carte du site pour';
$string['smfor'] = 'Carte du site pour';
$string['cannotchangepage'] = 'Cette page ne peux pas être modifiée.';
$string['uplinsect'] = 'Déposer dans';
$string['invalidroot'] = 'Vous n\'êtes pas autorisé à accéder à la page racine actuelle, et donc aucune carte du site ne peut être créée.';
$string['thispageisntlinkedfromanywhereelse'] = 'Aucun lien n\'existe vers cette page.';

$string['wikiexportcomment'] = 'Vous pouvez ici configurer l\'exportation selon vos besoins.';
$string['wikiexport'] = 'Exporter des pages';
$string['exportformats'] = 'Exporter des formats';
$string['export'] = 'Exporter';
$string['withbinaries'] = 'Inclure les contenus binaires';
$string['withvirtualpages'] = 'Inclure les liens wiki';
$string['plaintext'] = 'Texte pur';
$string['html'] = 'Format HTML';
$string['downloadaszip'] = 'Archive zip téléchargeable';
$string['moduledirectory'] = 'Module dossier';
$string['exportto'] = 'Exporter vers';
$string['exportsuccessful'] = 'Exportation réussie.';
$string['index'] = 'Index';

?>