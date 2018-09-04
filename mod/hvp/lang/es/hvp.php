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
$string['modulename'] = 'Contenido Interactivo';
$string['modulename_help'] = 'El módulo de actividad H5P le permite crear contenido interactivo como videos interactivos, conjuntos de preguntas, preguntas de arrastrar y soltar, preguntas de opción múltiple, presentaciones y mucho más.
Además de ser una herramienta de autoría para contenido enriquecido, H5P le permite importar y exportar archivos H5P para la reutilización efectiva y el intercambio de contenido.
Las interacciones del usuario y los puntajes se rastrean utilizando xAPI y están disponibles a través del Moodle Gradebook.
Agrega contenido H5P interactivo creando contenido utilizando la herramienta de creación incorporada o cargando archivos H5P encontrados en otros sitios habilitados para H5P.';
$string['modulename_link'] = 'https://h5p.org/moodle-more-help';
$string['modulenameplural'] = 'Contenido Interactivo';
$string['pluginadministration'] = 'H5P';
$string['pluginname'] = 'H5P';
$string['intro'] = 'Introducción';
$string['h5pfile'] = 'Archivo H5P';
$string['fullscreen'] = 'Pantalla completa';
$string['disablefullscreen'] = 'Inhabilitar pantalla completa';
$string['download'] = 'Descargar';
$string['copyright'] = 'Derechos de uso';
$string['embed'] = 'Embed';
$string['showadvanced'] = 'Mostrar modo avanzado';
$string['hideadvanced'] = 'Ocultar modo avanzado';
$string['resizescript'] = 'Incluya esta secuencia de comandos en su sitio web si desea un tamaño dinámico del contenido incrustado:';
$string['size'] = 'Tamaño';
$string['close'] = 'Cerrar';
$string['title'] = 'Título';
$string['author'] = 'Autor';
$string['year'] = 'Año';
$string['source'] = 'Fuente';
$string['license'] = 'Licencia';
$string['thumbnail'] = 'Thumbnail';
$string['nocopyright'] = 'No hay información de copyright disponible para este contenido.';
$string['downloadtitle'] = 'Descarga este archivo en formato H5P.';
$string['copyrighttitle'] = 'Ver información de copyright para este contenido.';
$string['embedtitle'] = 'Ver el código embed para este contenido.';
$string['h5ptitle'] = 'Visite H5P.org para ver más contenido interesante.';
$string['contentchanged'] = 'Este contenido ha cambiado desde la última vez que lo usaste.';
$string['startingover'] = "Estarás empezando de nuevo.";
$string['confirmdialogheader'] = 'Confirmar acción';
$string['confirmdialogbody'] = 'Por favor confirme que desea continuar. Esta acción no es reversible.';
$string['cancellabel'] = 'Cancelar';
$string['confirmlabel'] = 'Confirmar';
$string['noh5ps'] = 'No hay contenido interactivo disponible para este curso.';
$string['lookforupdates'] = 'Busque las actualizaciones H5P';
$string['updatelibraries'] = 'Actualizar todas las bibliotecas';
$string['removetmpfiles'] = 'Eliminar archivos temporales H5P antiguos';
$string['removeoldlogentries'] = 'Eliminar entradas antiguas de registro H5P';
// Admin settings.
$string['displayoptionnevershow'] = 'Nunca mostrar';
$string['displayoptionalwaysshow'] = 'Siempre muestra';
$string['displayoptionpermissions'] = 'Mostrar solo si el usuario tiene permisos para exportar H5P';
$string['displayoptionpermissionsembed'] = 'Mostrar solo si el usuario tiene permisos para incrustar H5P';
$string['displayoptionauthoron'] = 'Controlado por el autor, el valor predeterminado esta activado';
$string['displayoptionauthoroff'] = 'Controlado por el autor, el valor predeterminado está desactivado';
$string['displayoptions'] = 'Mostrar Opciones';
$string['enableframe'] = 'Mostrar barra de acción y marco';
$string['enabledownload'] = 'Botón de Descarga';
$string['enableembed'] = 'Embed button';
$string['enablecopyright'] = 'Bóton de copyright';
$string['enableabout'] = 'About H5P button';
$string['hubsettingsheader'] = 'Tipos de contenido';
$string['enablehublabel'] = 'Use H5P Hub';
$string['disablehubdescription'] = "Se recomienda encarecidamente mantener esta opción habilitada. H5P Hub proporciona una interfaz sencilla para obtener nuevos tipos de contenido y mantener actualizados los tipos de contenido existentes. En el futuro, también será más fácil compartir y reutilizar el contenido. Si esta opción está desactivada, deberá instalar y actualizar los tipos de contenido a través de los formularios de carga de archivos.";
$string['empty'] = 'Vacío';
$string['reveal'] = 'Revelar';
$string['hide'] = 'Ocultar';
$string['sitekey'] = 'Clave del sitio';
$string['sitekeydescription'] = 'La clave del sitio es un secreto que identifica de manera única este sitio con el Hub.';
$string['sendusagestatistics'] = 'Contribuir estadísticas de uso';
$string['sendusagestatistics_help'] = 'Los números de estadísticas de uso se informarán automáticamente para ayudar a los desarrolladores a comprender mejor cómo se usa H5P y para determinar las posibles áreas de mejora. Obtenga más información sobre qué datos de <a {$a}> se recopilan en h5p.org </a>.';
$string['enablesavecontentstate'] = 'Guardar estado de contenido';
$string['enablesavecontentstate_help'] = 'Guarde automáticamente el estado actual del contenido interactivo para cada usuario. Esto significa que el usuario puede continuar donde lo dejó.';
$string['contentstatefrequency'] = 'Guardar frecuencia de estado de contenido';
$string['contentstatefrequency_help'] = 'En segundos, ¿con qué frecuencia desea que el usuario guarde automáticamente su progreso? Aumenta este número si tienes problemas con muchas solicitudes ajax';
$string['enabledlrscontenttypes'] = 'Habilitar tipos de contenido dependientes de LRS';
$string['enabledlrscontenttypes_help'] = 'Permite utilizar tipos de contenido que dependen de un Almacén de Aprendizaje para funcionar correctamente, como el tipo de contenido del Cuestionario.';
// Admin menu.
$string['contenttypecacheheader'] = 'Tipo de contenido Caché';
$string['settings'] = 'Configuración H5P';
$string['libraries'] = 'Bibliotecas H5P';
// Content type cache section.
$string['ctcacheconnectionfailed'] = "No se pudo comunicar con el H5P Hub. Por favor, inténtelo de nuevo más tarde.";
$string['ctcachenolibraries'] = 'No se recibieron tipos de contenido del H5P Hub. Por favor, inténtelo de nuevo más tarde.';
$string['ctcachesuccess'] = '¡El caché de la biblioteca se actualizó con éxito!';
$string['ctcachelastupdatelabel'] = 'Última actualización';
$string['ctcachebuttonlabel'] = 'Actualizar el tipo de contenido de caché';
$string['ctcacheneverupdated'] = 'Nunca';
$string['ctcachetaskname'] = 'Actualizar el tipo de contenido de caché';
$string['ctcachedescription'] = 'Asegurarse de que la caché del tipo de contenido esté actualizada le garantizará que pueda ver, descargar y usar las últimas bibliotecas. Esto es diferente de actualizar las bibliotecas.';
// Upload libraries section.
$string['uploadlibraries'] = 'Upload Libraries';
$string['options'] = 'Options';
$string['onlyupdate'] = 'Only update existing libraries';
$string['disablefileextensioncheck'] = 'Disable file extension check';
$string['disablefileextensioncheckwarning'] = "Warning! Disabling the file extension check may have security implications as it allows for uploading of php files. That in turn could make it possible for attackers to execute malicious code on your site. Please make sure you know exactly what you're uploading.";
$string['upload'] = 'Upload';
// Installed libraries section.
$string['installedlibraries'] = 'Installed Libraries';
$string['invalidtoken'] = 'Invalid security token.';
$string['missingparameters'] = 'Missing parameters';
$string['nocontenttype'] = 'No content type was specified.';
$string['invalidcontenttype'] = 'The chosen content type is invalid.';
$string['installdenied'] = 'You do not have permission to install content types. Contact the administrator of your site.';
$string['downloadfailed'] = 'Downloading the requested library failed.';
$string['validationfailed'] = 'The requested H5P was not valid';
$string['validatingh5pfailed'] = 'Validating h5p package failed.';
// H5P library list headers on admin page.
$string['librarylisttitle'] = 'Title';
$string['librarylistrestricted'] = 'Restricted';
$string['librarylistinstances'] = 'Instances';
$string['librarylistinstancedependencies'] = 'Instance dependencies';
$string['librarylistlibrarydependencies'] = 'Library dependencies';
$string['librarylistactions'] = 'Actions';
// H5P library page labels.
$string['addlibraries'] = 'Add libraries';
$string['installedlibraries'] = 'Installed libraries';
$string['notapplicable'] = 'N/A';
$string['upgradelibrarycontent'] = 'Upgrade library content';
// Upgrade H5P content page.
$string['upgrade'] = 'Upgrade H5P';
$string['upgradeheading'] = 'Upgrade {$a} content';
$string['upgradenoavailableupgrades'] = 'There are no available upgrades for this library.';
$string['enablejavascript'] = 'Please enable JavaScript.';
$string['upgrademessage'] = 'You are about to upgrade {$a} content instance(s). Please select upgrade version.';
$string['upgradeinprogress'] = 'Upgrading to %ver...';
$string['upgradeerror'] = 'An error occurred while processing parameters:';
$string['upgradeerrordata'] = 'Could not load data for library %lib.';
$string['upgradeerrorscript'] = 'Could not load upgrades script for %lib.';
$string['upgradeerrorcontent'] = 'Could not upgrade content %id:';
$string['upgradeerrorparamsbroken'] = 'Parameters are broken.';
$string['upgradedone'] = 'You have successfully upgraded {$a} content instance(s).';
$string['upgradereturn'] = 'Return';
$string['upgradenothingtodo'] = "There's no content instances to upgrade.";
$string['upgradebuttonlabel'] = 'Upgrade';
$string['upgradeinvalidtoken'] = 'Error: Invalid security token!';
$string['upgradelibrarymissing'] = 'Error: Your library is missing!';
// Results / report page.
$string['user'] = 'User';
$string['score'] = 'Score';
$string['maxscore'] = 'Maximum Score';
$string['finished'] = 'Finished';
$string['loadingdata'] = 'Loading data.';
$string['ajaxfailed'] = 'Failed to load data.';
$string['nodata'] = "There's no data available that matches your criteria.";
$string['currentpage'] = 'Page $current of $total';
$string['nextpage'] = 'Next page';
$string['previouspage'] = 'Previous page';
$string['search'] = 'Search';
$string['empty'] = 'No results available';
$string['viewreportlabel'] = 'Report';
$string['dataviewreportlabel'] = 'View Answers';
$string['invalidxapiresult'] = 'No xAPI results were found for the given content and user id combination';
$string['reportnotsupported'] = 'Not supported';
$string['reportingscorelabel'] = 'Score:';
$string['reportingscaledscorelabel'] = 'Gradebook score:';
$string['reportingscoredelimiter'] = 'out of';
$string['reportingscaledscoredelimiter'] = ',';
// Editor.
$string['javascriptloading'] = 'Waiting for JavaScript...';
$string['action'] = 'Action';
$string['upload'] = 'Upload';
$string['create'] = 'Create';
$string['editor'] = 'Editor';
$string['invalidlibrary'] = 'Invalid library';
$string['nosuchlibrary'] = 'No such library';
$string['noparameters'] = 'No parameters';
$string['invalidparameters'] = 'Invalid Parameters';
$string['missingcontentuserdata'] = 'Error: Could not find content user data';
$string['maximumgrade'] = 'Maximum grade';
$string['maximumgradeerror'] = 'Please enter a valid positive integer as the max points available for this activity';
// Capabilities.
$string['hvp:view'] = 'See and interact with H5P activities';
$string['hvp:addinstance'] = 'Create new H5P activites';
$string['hvp:manage'] = 'Edit existing H5P activites';
$string['hvp:getexport'] = 'Download .h5p file when \'controlled by permission\' option is set';
$string['hvp:getembedcode'] = 'View H5P embed code when \'controlled by permission\' option is set';
$string['hvp:saveresults'] = 'Save the results from completed H5P activities';
$string['hvp:savecontentuserdata'] = 'Save the users\'s progress for H5P activities';
$string['hvp:viewresults'] = 'View own results for completed H5P activities';
$string['hvp:viewallresults'] = 'View all results for completed H5P activites';
$string['hvp:restrictlibraries'] = 'Restrict access to certain H5P content types';
$string['hvp:userestrictedlibraries'] = 'Use restricted H5P content types';
$string['hvp:updatelibraries'] = 'Install new H5P content types or update existing ones';
$string['hvp:getcachedassets'] = 'Required for viewing H5P activities';
$string['hvp:installrecommendedh5plibraries'] = 'Install new safe H5P content types recommended by H5P.org';
// Capabilities error messages.
$string['nopermissiontoupgrade'] = 'You do not have permission to upgrade libraries.';
$string['nopermissiontorestrict'] = 'You do not have permission to restrict libraries.';
$string['nopermissiontosavecontentuserdata'] = 'You do not have permission to save content user data.';
$string['nopermissiontosaveresult'] = 'You do not have permission to save result for this content.';
$string['nopermissiontoviewresult'] = 'You do not have permission to view results for this content.';
$string['nopermissiontouploadfiles'] = 'You do not have permission to upload files here.';
$string['nopermissiontouploadcontent'] = 'You do not have permission to upload content here.';
$string['nopermissiontoviewcontenttypes'] = 'You do not have permission to view the content types.';
// Editor translations.
$string['noziparchive'] = 'Your PHP version does not support ZipArchive.';
$string['noextension'] = 'The file you uploaded is not a valid HTML5 Package (It does not have the .h5p file extension)';
$string['nounzip'] = 'The file you uploaded is not a valid HTML5 Package (We are unable to unzip it)';
$string['noparse'] = 'Could not parse the main h5p.json file';
$string['nojson'] = 'The main h5p.json file is not valid';
$string['invalidcontentfolder'] = 'Invalid content folder';
$string['nocontent'] = 'Could not find or parse the content.json file';
$string['librarydirectoryerror'] = 'Library directory name must match machineName or machineName-majorVersion.minorVersion (from library.json). (Directory: {$a->%directoryName} , machineName: {$a->%machineName}, majorVersion: {$a->%majorVersion}, minorVersion: {$a->%minorVersion})';
$string['missingcontentfolder'] = 'A valid content folder is missing';
$string['invalidmainjson'] = 'A valid main h5p.json file is missing';
$string['missinglibrary'] = 'Missing required library {$a->@library}';
$string['missinguploadpermissions'] = "Note that the libraries may exist in the file you uploaded, but you're not allowed to upload new libraries. Contact the site administrator about this.";
$string['invalidlibraryname'] = 'Invalid library name: {$a->%name}';
$string['missinglibraryjson'] = 'Could not find library.json file with valid json format for library {$a->%name}';
$string['invalidsemanticsjson'] = 'Invalid semantics.json file has been included in the library {$a->%name}';
$string['invalidlanguagefile'] = 'Invalid language file {$a->%file} in library {$a->%library}';
$string['invalidlanguagefile2'] = 'Invalid language file {$a->%languageFile} has been included in the library {$a->%name}';
$string['missinglibraryfile'] = 'The file "{$a->%file}" is missing from library: "{$a->%name}"';
$string['missingcoreversion'] = 'The system was unable to install the <em>{$a->%component}</em> component from the package, it requires a newer version of the H5P plugin. This site is currently running version {$a->%current}, whereas the required version is {$a->%required} or higher. You should consider upgrading and then try again.';
$string['invalidlibrarydataboolean'] = 'Invalid data provided for {$a->%property} in {$a->%library}. Boolean expected.';
$string['invalidlibrarydata'] = 'Invalid data provided for {$a->%property} in {$a->%library}';
$string['invalidlibraryproperty'] = 'Can\'t read the property {$a->%property} in {$a->%library}';
$string['missinglibraryproperty'] = 'The required property {$a->%property} is missing from {$a->%library}';
$string['invalidlibraryoption'] = 'Illegal option {$a->%option} in {$a->%library}';
$string['addedandupdatedss'] = 'Added {$a->%new} new H5P library and updated {$a->%old} old one.';
$string['addedandupdatedsp'] = 'Added {$a->%new} new H5P library and updated {$a->%old} old ones.';
$string['addedandupdatedps'] = 'Added {$a->%new} new H5P libraries and updated {$a->%old} old one.';
$string['addedandupdatedpp'] = 'Added {$a->%new} new H5P libraries and updated {$a->%old} old ones.';
$string['addednewlibrary'] = 'Added {$a->%new} new H5P library.';
$string['addednewlibraries'] = 'Added {$a->%new} new H5P libraries.';
$string['updatedlibrary'] = 'Updated {$a->%old} H5P library.';
$string['updatedlibraries'] = 'Updated {$a->%old} H5P libraries.';
$string['missingdependency'] = 'Missing dependency {$a->@dep} required by {$a->@lib}.';
$string['invalidstring'] = 'Provided string is not valid according to regexp in semantics. (value: \"{$a->%value}\", regexp: \"{$a->%regexp}\")';
$string['invalidfile'] = 'File "{$a->%filename}" not allowed. Only files with the following extensions are allowed: {$a->%files-allowed}.';
$string['invalidmultiselectoption'] = 'Invalid selected option in multi-select.';
$string['invalidselectoption'] = 'Invalid selected option in select.';
$string['invalidsemanticstype'] = 'H5P internal error: unknown content type "{$a->@type}" in semantics. Removing content!';
$string['unabletocreatedir'] = 'Unable to create directory.';
$string['unabletogetfieldtype'] = 'Unable to get field type.';
$string['filetypenotallowed'] = 'File type isn\'t allowed.';
$string['invalidfieldtype'] = 'Invalid field type.';
$string['invalidimageformat'] = 'Invalid image file format. Use jpg, png or gif.';
$string['filenotimage'] = 'File is not an image.';
$string['invalidaudioformat'] = 'Invalid audio file format. Use mp3 or wav.';
$string['invalidvideoformat'] = 'Invalid video file format. Use mp4 or webm.';
$string['couldnotsave'] = 'Could not save file.';
$string['couldnotcopy'] = 'Could not copy file.';
$string['librarynotselected'] = 'You must select a content type.';
// Welcome messages.
$string['welcomeheader'] = 'Welcome to the world of H5P!';
$string['welcomegettingstarted'] = 'To get started with H5P and Moodle take a look at our <a {$a->moodle_tutorial}>tutorial</a> and check out the <a {$a->example_content}>example content</a> at H5P.org for inspiration.';
$string['welcomecommunity'] = 'We hope you will enjoy H5P and get engaged in our growing community through our <a {$a->forums}>forums</a>.';
$string['welcomecontactus'] = 'If you have any feedback, don\'t hesitate to <a {$a}>contact us</a>. We take feedback very seriously and are dedicated to making H5P better every day!';
$string['missingmbstring'] = 'The mbstring PHP extension is not loaded. H5P need this to function properly';
$string['wrongversion'] = 'The version of the H5P library {$a->%machineName} used in this content is not valid. Content contains {$a->%contentLibrary}, but it should be {$a->%semanticsLibrary}.';
$string['invalidlibrarynamed'] = 'The H5P library {$a->%library} used in the content is not valid';
// Setup errors.
$string['oldphpversion'] = 'Your PHP version is outdated. H5P requires version 5.2 to function properly. Version 5.6 or later is recommended.';
$string['maxuploadsizetoosmall'] = 'Your PHP max upload size is quite small. With your current setup, you may not upload files larger than {$a->%number} MB. This might be a problem when trying to upload H5Ps, images and videos. Please consider to increase it to more than 5MB.';
$string['maxpostsizetoosmall'] = 'Your PHP max post size is quite small. With your current setup, you may not upload files larger than {$a->%number} MB. This might be a problem when trying to upload H5Ps, images and videos. Please consider to increase it to more than 5MB';
$string['sslnotenabled'] = 'Your server does not have SSL enabled. SSL should be enabled to ensure a secure connection with the H5P hub.';
$string['hubcommunicationdisabled'] = 'H5P hub communication has been disabled because one or more H5P requirements failed.';
$string['reviseserversetupandretry'] = 'When you have revised your server setup you may re-enable H5P hub communication in H5P Settings.';
$string['disablehubconfirmationmsg'] = 'Do you still want to enable the hub ?';
$string['nowriteaccess'] = 'A problem with the server write access was detected. Please make sure that your server can write to your data folder.';
$string['uploadsizelargerthanpostsize'] = 'Your PHP max upload size is bigger than your max post size. This is known to cause issues in some installations.';
$string['sitecouldnotberegistered'] = 'Site could not be registered with the hub. Please contact your site administrator.';
$string['hubisdisableduploadlibraries'] = 'The H5P Hub has been disabled until this problem can be resolved. You may still upload libraries through the "H5P Libraries" page.';
$string['successfullyregisteredwithhub'] = 'Your site was successfully registered with the H5P Hub.';
$string['sitekeyregistered'] = 'You have been provided a unique key that identifies you with the Hub when receiving new updates. The key is available for viewing in the "H5P Settings" page.';
// Ajax messages.
$string['hubisdisabled'] = 'The hub is disabled. You can re-enable it in the H5P settings.';
$string['invalidh5ppost'] = 'Could not get posted H5P.';
$string['filenotfoundonserver'] = 'File not found on server. Check file upload settings.';
$string['failedtodownloadh5p'] = 'Failed to download the requested H5P.';
$string['postmessagerequired'] = 'A post message is required to access the given endpoint';
// Licensing.
$string['copyrightinfo'] = 'Copyright information';
$string['years'] = 'Year(s)';
$string['undisclosed'] = 'Undisclosed';
$string['attribution'] = 'Attribution 4.0';
$string['attributionsa'] = 'Attribution-ShareAlike 4.0';
$string['attributionnd'] = 'Attribution-NoDerivs 4.0';
$string['attributionnc'] = 'Attribution-NonCommercial 4.0';
$string['attributionncsa'] = 'Attribution-NonCommercial-ShareAlike 4.0';
$string['attributionncnd'] = 'Attribution-NonCommercial-NoDerivs 4.0';
$string['gpl'] = 'General Public License v3';
$string['pd'] = 'Public Domain';
$string['pddl'] = 'Public Domain Dedication and Licence';
$string['pdm'] = 'Public Domain Mark';
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
