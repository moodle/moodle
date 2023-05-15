<?php

class H5peditor {

  private static $hasWYSIWYGEditor = array(
    'H5P.CoursePresentation',
    'H5P.InteractiveVideo',
    'H5P.DragQuestion'
  );

  public static $styles = array(
    'libs/darkroom.css',
    'styles/css/h5p-hub-client.css',
    'styles/css/fonts.css',
    'styles/css/application.css',
    'styles/css/libs/zebra_datepicker.min.css'
  );
  public static $scripts = array(
    'scripts/h5p-hub-client.js',
    'scripts/h5peditor.js',
    'scripts/h5peditor-semantic-structure.js',
    'scripts/h5peditor-editor.js',
    'scripts/h5peditor-library-selector.js',
    'scripts/h5peditor-fullscreen-bar.js',
    'scripts/h5peditor-form.js',
    'scripts/h5peditor-text.js',
    'scripts/h5peditor-html.js',
    'scripts/h5peditor-number.js',
    'scripts/h5peditor-textarea.js',
    'scripts/h5peditor-file-uploader.js',
    'scripts/h5peditor-file.js',
    'scripts/h5peditor-image.js',
    'scripts/h5peditor-image-popup.js',
    'scripts/h5peditor-av.js',
    'scripts/h5peditor-group.js',
    'scripts/h5peditor-boolean.js',
    'scripts/h5peditor-list.js',
    'scripts/h5peditor-list-editor.js',
    'scripts/h5peditor-library.js',
    'scripts/h5peditor-library-list-cache.js',
    'scripts/h5peditor-select.js',
    'scripts/h5peditor-selector-hub.js',
    'scripts/h5peditor-selector-legacy.js',
    'scripts/h5peditor-dimensions.js',
    'scripts/h5peditor-coordinates.js',
    'scripts/h5peditor-none.js',
    'scripts/h5peditor-metadata.js',
    'scripts/h5peditor-metadata-author-widget.js',
    'scripts/h5peditor-metadata-changelog-widget.js',
    'scripts/h5peditor-pre-save.js',
    'ckeditor/ckeditor.js',
  );
  private $h5p, $storage;
  public $ajax, $ajaxInterface;

  /**
   * Constructor for the core editor library.
   *
   * @param \H5PCore $h5p Instance of core
   * @param \H5peditorStorage $storage Instance of h5peditor storage interface
   * @param \H5PEditorAjaxInterface $ajaxInterface Instance of h5peditor ajax
   * interface
   */
  function __construct($h5p, $storage, $ajaxInterface) {
    $this->h5p = $h5p;
    $this->storage = $storage;
    $this->ajaxInterface = $ajaxInterface;
    $this->ajax = new H5PEditorAjax($h5p, $this, $storage);
  }

  /**
   * Get list of libraries.
   *
   * @return array
   */
  public function getLibraries() {
    if (isset($_POST['libraries'])) {
      // Get details for the specified libraries.
      $libraries = array();
      foreach ($_POST['libraries'] as $libraryName) {
        $matches = array();
        preg_match_all('/(.+)\s(\d+)\.(\d+)$/', $libraryName, $matches);
        if ($matches && $matches[1] && $matches[2] && $matches[3]) {
          $libraries[] = (object) array(
            'uberName' => $libraryName,
            'name' => $matches[1][0],
            'majorVersion' => $matches[2][0],
            'minorVersion' => $matches[3][0]
          );
        }
      }
    }

    $libraries = $this->storage->getLibraries(!isset($libraries) ? NULL : $libraries);

    if ($this->h5p->development_mode & H5PDevelopment::MODE_LIBRARY) {
      $devLibs = $this->h5p->h5pD->getLibraries();
    }

    for ($i = 0, $s = count($libraries); $i < $s; $i++) {
      if (!empty($devLibs)) {
        $lid = $libraries[$i]->name . ' ' . $libraries[$i]->majorVersion . '.' . $libraries[$i]->minorVersion;
        if (isset($devLibs[$lid])) {
          // Replace library with devlib
          $isOld = !empty($libraries[$i]->isOld) && $libraries[$i]->isOld === TRUE;
          $libraries[$i] = (object) array(
            'uberName' => $lid,
            'name' => $devLibs[$lid]['machineName'],
            'title' => $devLibs[$lid]['title'],
            'majorVersion' => $devLibs[$lid]['majorVersion'],
            'minorVersion' => $devLibs[$lid]['minorVersion'],
            'runnable' => $devLibs[$lid]['runnable'],
            'restricted' => $libraries[$i]->restricted,
            'tutorialUrl' => $libraries[$i]->tutorialUrl,
            'metadataSettings' => $devLibs[$lid]['metadataSettings'],
          );
          if ($isOld) {
            $libraries[$i]->isOld = TRUE;
          }
        }
      }

      // Some libraries rely on an LRS to work and must be enabled manually
      if (in_array($libraries[$i]->name, array('H5P.Questionnaire', 'H5P.FreeTextQuestion')) &&
          !$this->h5p->h5pF->getOption('enable_lrs_content_types')) {
        $libraries[$i]->restricted = TRUE;
      }
    }

    return $libraries;
  }

  /**
   * Get translations for a language for a list of libraries
   *
   * @param array $libraries An array of libraries, in the form "<machineName> <majorVersion>.<minorVersion>
   * @param string $language_code
   * @return array
   */
  public function getTranslations($libraries, $language_code) {
    return $this->ajaxInterface->getTranslations($libraries, $language_code);
  }

  /**
   * Move uploaded files, remove old files and update library usage.
   *
   * @param stdClass $content
   * @param array $newLibrary
   * @param array $newParameters
   * @param array $oldLibrary
   * @param array $oldParameters
   */
  public function processParameters($content, $newLibrary, $newParameters, $oldLibrary = NULL, $oldParameters = NULL) {
    $newFiles = array();
    $oldFiles = array();

    // Keep track of current content ID (used when processing files)
    $this->content = $content;

    // Find new libraries/content dependencies and files.
    // Start by creating a fake library field to process. This way we get all the dependencies of the main library as well.
    $field = (object) array(
      'type' => 'library'
    );
    $libraryParams = (object) array(
      'library' => H5PCore::libraryToString($newLibrary),
      'params' => $newParameters
    );
    $this->processField($field, $libraryParams, $newFiles);

    if ($oldLibrary !== NULL) {
      // Find old files and libraries.
      $this->processSemantics($oldFiles, $this->h5p->loadLibrarySemantics($oldLibrary['name'], $oldLibrary['majorVersion'], $oldLibrary['minorVersion']), $oldParameters);

      // Remove old files.
      for ($i = 0, $s = count($oldFiles); $i < $s; $i++) {
        if (!in_array($oldFiles[$i], $newFiles) &&
            preg_match('/^(\w+:\/\/|\.\.\/)/i', $oldFiles[$i]) === 0) {
          $this->h5p->fs->removeContentFile($oldFiles[$i], $content);
          // (optionally we could just have marked them as tmp files)
        }
      }
    }
  }

  /**
   * Recursive function that moves the new files in to the h5p content folder and generates a list over the old files.
   * Also locates all the librares.
   *
   * @param array $files
   * @param array $libraries
   * @param array $semantics
   * @param array $params
   */
  private function processSemantics(&$files, $semantics, &$params) {
    for ($i = 0, $s = count($semantics); $i < $s; $i++) {
      $field = $semantics[$i];
      if (!isset($params->{$field->name})) {
        continue;
      }
      $this->processField($field, $params->{$field->name}, $files);
    }
  }

  /**
   * Process a single field.
   *
   * @staticvar string $h5peditor_path
   * @param object $field
   * @param mixed $params
   * @param array $files
   */
  private function processField(&$field, &$params, &$files) {
    switch ($field->type) {
      case 'file':
      case 'image':
        if (isset($params->path)) {
          $this->processFile($params, $files);

          // Process original image
          if (isset($params->originalImage) && isset($params->originalImage->path)) {
            $this->processFile($params->originalImage, $files);
          }
        }
        break;

      case 'video':
      case 'audio':
        if (is_array($params)) {
          for ($i = 0, $s = count($params); $i < $s; $i++) {
            $this->processFile($params[$i], $files);
          }
        }
        break;

      case 'library':
        if (isset($params->library) && isset($params->params)) {
          $library = H5PCore::libraryFromString($params->library);
          $semantics = $this->h5p->loadLibrarySemantics($library['machineName'], $library['majorVersion'], $library['minorVersion']);

          // Process parameters for the library.
          $this->processSemantics($files, $semantics, $params->params);
        }
        break;

      case 'group':
        if (isset($params)) {
          $isSubContent = isset($field->isSubContent) && $field->isSubContent == TRUE;

          if (count($field->fields) == 1 && !$isSubContent) {
            $params = (object) array($field->fields[0]->name => $params);
          }
          $this->processSemantics($files, $field->fields, $params);
        }
        break;

      case 'list':
        if (is_array($params)) {
          for ($j = 0, $t = count($params); $j < $t; $j++) {
            $this->processField($field->field, $params[$j], $files);
          }
        }
        break;
    }
  }

  /**
   * @param mixed $params
   * @param array $files
   */
  private function processFile(&$params, &$files) {
    if (preg_match('/^https?:\/\//', $params->path)) {
      return; // Skip external files
    }

    // Remove temporary files suffix
    if (substr($params->path, -4, 4) === '#tmp') {
      $params->path = substr($params->path, 0, strlen($params->path) - 4);
    }

    // File could be copied from another content folder.
    $matches = array();
    if (preg_match($this->h5p->relativePathRegExp, $params->path, $matches)) {

      // Create a copy of the file
      $this->h5p->fs->cloneContentFile($matches[5], $matches[4], $this->content);

      // Update Params with correct filename
      $params->path = $matches[5];
    }
    else {
      // Check if file exists in content folder
      $fileId = $this->h5p->fs->getContentFile($params->path, $this->content);
      if ($fileId) {
        // Mark the file as a keeper
        $this->storage->keepFile($fileId);
      }
      else {
        // File is not in content folder, try to copy it from the editor tmp dir
        // to content folder.
        $this->h5p->fs->cloneContentFile($params->path, 'editor', $this->content);
        // (not removed in case someone has copied it)
        // (will automatically be removed after 24 hours)
      }
    }

    $files[] = $params->path;
  }

  /**
   * TODO: Consider moving to core.
   */
  public function getLibraryLanguage($machineName, $majorVersion, $minorVersion, $languageCode) {
    if ($this->h5p->development_mode & H5PDevelopment::MODE_LIBRARY) {
      // Try to get language development library first.
      $language = $this->h5p->h5pD->getLanguage($machineName, $majorVersion, $minorVersion, $languageCode);
    }

    if (isset($language) === FALSE) {
      $language = $this->storage->getLanguage($machineName, $majorVersion, $minorVersion, $languageCode);
    }

    return ($language === FALSE ? NULL : $language);
  }

  /**
   * Return all libraries used by the given editor library.
   *
   * @param string $machineName Library identfier part 1
   * @param int $majorVersion Library identfier part 2
   * @param int $minorVersion Library identfier part 3
   */
  public function findEditorLibraries($machineName, $majorVersion, $minorVersion) {
    $library = $this->h5p->loadLibrary($machineName, $majorVersion, $minorVersion);
    $dependencies = array();
    $this->h5p->findLibraryDependencies($dependencies, $library);

    // Load addons for wysiwyg editors
    if (in_array($machineName, self::$hasWYSIWYGEditor)) {
      $addons = $this->h5p->h5pF->loadAddons();
      foreach ($addons as $addon) {
        $key = 'editor-' . $addon['machineName'];
        $dependencies[$key]['weight'] = sizeof($dependencies)+1;
        $dependencies[$key]['type'] = 'editor';
        $dependencies[$key]['library'] = $addon;
      }
    }

    // Order dependencies by weight
    $orderedDependencies = array();
    for ($i = 1, $s = count($dependencies); $i <= $s; $i++) {
      foreach ($dependencies as $dependency) {
        if ($dependency['weight'] === $i && $dependency['type'] === 'editor') {
          // Only load editor libraries.
          $dependency['library']['id'] = $dependency['library']['libraryId'];
          $orderedDependencies[$dependency['library']['libraryId']] = $dependency['library'];
          break;
        }
      }
    }

    return $orderedDependencies;
  }

  /**
   * Get all scripts, css and semantics data for a library
   *
   * @param string $machineName Library name
   * @param int $majorVersion
   * @param int $minorVersion
   * @param string $prefix Optional part to add between URL and asset path
   * @param string $fileDir Optional file dir to read files from
   *
   * @return array Libraries that was requested
   */
  public function getLibraryData($machineName, $majorVersion, $minorVersion, $languageCode, $prefix = '', $fileDir = '', $defaultLanguage = '') {
    $libraryData = new stdClass();

    $library = $this->h5p->loadLibrary($machineName, $majorVersion, $minorVersion);

    // Include name and version in data object for convenience
    $libraryData->name = $library['machineName'];
    $libraryData->version = (object) array('major' => $library['majorVersion'], 'minor' => $library['minorVersion']);
    $libraryData->title = $library['title'];

    $libraryData->upgradesScript = $this->h5p->fs->getUpgradeScript($library['machineName'], $library['majorVersion'], $library['minorVersion']);
    if ($libraryData->upgradesScript !== NULL) {
      // If valid add URL prefix
      $libraryData->upgradesScript = $this->h5p->url . $prefix . $libraryData->upgradesScript;
    }

    $libraries              = $this->findEditorLibraries($library['machineName'], $library['majorVersion'], $library['minorVersion']);
    $libraryData->semantics = $this->h5p->loadLibrarySemantics($library['machineName'], $library['majorVersion'], $library['minorVersion']);
    $libraryData->language  = $this->getLibraryLanguage($library['machineName'], $library['majorVersion'], $library['minorVersion'], $languageCode);
    $libraryData->defaultLanguage = empty($defaultLanguage) ? NULL : $this->getLibraryLanguage($library['machineName'], $library['majorVersion'], $library['minorVersion'], $defaultLanguage);
    $libraryData->languages = $this->storage->getAvailableLanguages($library['machineName'], $library['majorVersion'], $library['minorVersion']);

    // Temporarily disable asset aggregation
    $aggregateAssets            = $this->h5p->aggregateAssets;
    $this->h5p->aggregateAssets = FALSE;
    // This is done to prevent files being loaded multiple times due to how
    // the editor works.

    // Get list of JS and CSS files that belongs to the dependencies
    $files = $this->h5p->getDependenciesFiles($libraries, $prefix);
    $libraryName = H5PCore::libraryToFolderName($library);
    if ($this->hasPresave($libraryName) === true) {
      $this->addPresaveFile($files, $library, $prefix);
    }
    $this->storage->alterLibraryFiles($files, $libraries);

    // Restore asset aggregation setting
    $this->h5p->aggregateAssets = $aggregateAssets;

    // Create base URL
    $url = $this->h5p->url;

    // Javascripts
    if (!empty($files['scripts'])) {
      foreach ($files['scripts'] as $script) {
        if (preg_match('/:\/\//', $script->path) === 1) {
          // External file
          $libraryData->javascript[] = $script->path . $script->version;
        }
        else {
          // Local file
          $path = $url . $script->path;
          if (!isset($this->h5p->h5pD)) {
            $path .= $script->version;
          }
          $libraryData->javascript[] = $path;
        }
      }
    }

    // Stylesheets
    if (!empty($files['styles'])) {
      foreach ($files['styles'] as $css) {
        if (preg_match('/:\/\//', $css->path) === 1) {
          // External file
          $libraryData->css[] = $css->path . $css->version;
        }
        else {
          // Local file
          $path = $url . $css->path;
          if (!isset($this->h5p->h5pD)) {
            $path .= $css->version;
          }
          $libraryData->css[] = $path;
        }
      }
    }

    $translations = array();
    // Add translations for libraries.
    foreach ($libraries as $library) {
      if (empty($library['semantics'])) {
        $translation = $this->getLibraryLanguage($library['machineName'], $library['majorVersion'], $library['minorVersion'], $languageCode);

        // If translation was not found, and this is not the English one, try to load
        // the English translation
        if ($translation === NULL && $languageCode !== 'en') {
          $translation = $this->getLibraryLanguage($library['machineName'], $library['majorVersion'], $library['minorVersion'], 'en');
        }

        if ($translation !== NULL) {
          $translations[$library['machineName']] = json_decode($translation);
        }
      }
    }

    $libraryData->translations = $translations;

    return $libraryData;
  }

  /**
   * This function will prefix all paths within a CSS file.
   * Copied from Drupal 6.
   *
   * @staticvar type $_base
   * @param type $matches
   * @param type $base
   * @return type
   */
  public static function buildCssPath($matches, $base = NULL) {
    static $_base;
    // Store base path for preg_replace_callback.
    if (isset($base)) {
      $_base = $base;
    }

    // Prefix with base and remove '../' segments where possible.
    $path = $_base . $matches[1];
    $last = '';
    while ($path != $last) {
      $last = $path;
      $path = preg_replace('`(^|/)(?!\.\./)([^/]+)/\.\./`', '$1', $path);
    }
    return 'url('. $path .')';
  }

  /**
   * Gets content type cache, applies user specific properties and formats
   * as camelCase.
   *
   * @return array $libraries Cached libraries from the H5P Hub with user specific
   * permission properties
   */
  public function getUserSpecificContentTypeCache() {
    $cached_libraries = $this->ajaxInterface->getContentTypeCache();

    // Check if user has access to install libraries
    $libraries = array();
    foreach ($cached_libraries as &$result) {
      // Check if user can install content type
      $result->restricted = !$this->canInstallContentType($result);

      // Formats json
      $libraries[] = $this->getCachedLibsMap($result);
    }

    return $libraries;
  }

  public function canInstallContentType($contentType) {
    $canInstallAll         = $this->h5p->h5pF->hasPermission(H5PPermission::UPDATE_LIBRARIES);
    $canInstallRecommended = $this->h5p->h5pF->hasPermission(H5PPermission::INSTALL_RECOMMENDED);

    return $canInstallAll || $contentType->is_recommended && $canInstallRecommended;
  }

  /**
   * Gets local and external libraries data with metadata to display
   * all libraries that are currently available for the user.
   *
   * @return array $libraries Latest local and external libraries data with
   * user specific permissions
   */
  public function getLatestGlobalLibrariesData() {
    $latest_local_libraries = $this->ajaxInterface->getLatestLibraryVersions();
    $cached_libraries       = $this->getUserSpecificContentTypeCache();
    $this->mergeLocalLibsIntoCachedLibs($latest_local_libraries, $cached_libraries);
    return $cached_libraries;
  }


  /**
   * Extract library properties from cached library so they are ready to be
   * returned as JSON
   *
   * @param object $cached_library A single library from the content type cache
   *
   * @return array A map containing the necessary properties for a cached
   * library to send to the front-end
   */
  public function getCachedLibsMap($cached_library) {
    $restricted = isset($cached_library->restricted) ? $cached_library->restricted : FALSE;

    // Add mandatory fields
    $lib = array(
      'id'              => intval($cached_library->id),
      'machineName'     => $cached_library->machine_name,
      'majorVersion'    => intval( $cached_library->major_version),
      'minorVersion'    => intval($cached_library->minor_version),
      'patchVersion'    => intval($cached_library->patch_version),
      'h5pMajorVersion' => intval($cached_library->h5p_major_version),
      'h5pMinorVersion' => intval($cached_library->h5p_minor_version),
      'title'           => $cached_library->title,
      'summary'         => $cached_library->summary,
      'description'     => $cached_library->description,
      'icon'            => $cached_library->icon,
      'createdAt'       => intval($cached_library->created_at),
      'updatedAt'       => intval($cached_library->updated_at),
      'isRecommended'   => $cached_library->is_recommended != 0,
      'popularity'      => intval($cached_library->popularity),
      'screenshots'     => json_decode($cached_library->screenshots),
      'license'         => json_decode($cached_library->license),
      'owner'           => $cached_library->owner,
      'installed'       => FALSE,
      'isUpToDate'      => FALSE,
      'restricted'      => $restricted,
      'canInstall'      => !$restricted
    );

    // Add optional fields
    if (!empty($cached_library->categories)) {
      $lib['categories'] = json_decode($cached_library->categories);
    }
    if (!empty($cached_library->keywords)) {
      $lib['keywords'] = json_decode($cached_library->keywords);
    }
    if (!empty($cached_library->tutorial)) {
      $lib['tutorial'] = $cached_library->tutorial;
    }
    if (!empty($cached_library->example)) {
      $lib['example'] = $cached_library->example;
    }
    if (!empty($cached_library->icons)) {
      $lib['icons'] = json_decode($cached_library->icons);
    }

    return $lib;
  }


  /**
   * Merge local libraries into cached libraries so that local libraries will
   * get supplemented with the additional info from externally cached libraries.
   *
   * Also sets whether a given cached library is installed and up to date with
   * the locally installed libraries
   *
   * @param array $local_libraries Locally installed libraries
   * @param array $cached_libraries Cached libraries from the H5P hub
   */
  public function mergeLocalLibsIntoCachedLibs($local_libraries, &$cached_libraries) {
    $can_create_restricted = $this->h5p->h5pF->hasPermission(H5PPermission::CREATE_RESTRICTED);

    // Add local libraries to supplement content type cache
    foreach ($local_libraries as $local_lib) {
      $is_local_only = TRUE;
      $icon_path = NULL;

      // Check if icon is available locally:
      if ($local_lib->has_icon) {
        // Create path to icon:
        $library_folder = H5PCore::libraryToFolderName([
          'machineName' => $local_lib->machine_name,
          'majorVersion' => $local_lib->major_version,
          'minorVersion' => $local_lib->minor_version,
          'patchVersion' => $local_lib->patch_version,
          'patchVersionInFolderName' => $local_lib->patch_version_in_folder_name
        ]);
        $icon_path = $this->h5p->h5pF->getLibraryFileUrl($library_folder, 'icon.svg');
      }

      foreach ($cached_libraries as &$cached_lib) {
        // Determine if library is local
        $is_matching_library = $cached_lib['machineName'] === $local_lib->machine_name;
        if ($is_matching_library) {
          $is_local_only = FALSE;

          // Set icon if it exists locally
          if (isset($icon_path)) {
            $cached_lib['icon'] = $icon_path;
          }

          // Set local properties
          $cached_lib['installed']  = TRUE;
          $cached_lib['restricted'] = $can_create_restricted ? FALSE
            : ($local_lib->restricted ? TRUE : FALSE);

          // Set local version
          $cached_lib['localMajorVersion'] = (int) $local_lib->major_version;
          $cached_lib['localMinorVersion'] = (int) $local_lib->minor_version;
          $cached_lib['localPatchVersion'] = (int) $local_lib->patch_version;

          // Determine if library is newer or same as cache
          $major_is_updated =
            $cached_lib['majorVersion'] < $cached_lib['localMajorVersion'];

          $minor_is_updated =
            $cached_lib['majorVersion'] === $cached_lib['localMajorVersion'] &&
            $cached_lib['minorVersion'] < $cached_lib['localMinorVersion'];

          $patch_is_updated =
            $cached_lib['majorVersion'] === $cached_lib['localMajorVersion'] &&
            $cached_lib['minorVersion'] === $cached_lib['localMinorVersion'] &&
            $cached_lib['patchVersion'] <= $cached_lib['localPatchVersion'];

          $is_updated_library =
            $major_is_updated ||
            $minor_is_updated ||
            $patch_is_updated;

          if ($is_updated_library) {
            $cached_lib['isUpToDate'] = TRUE;
          }
        }
      }

      // Add minimal data to display local only libraries
      if ($is_local_only) {
        $local_only_lib = array(
          'id'                => (int) $local_lib->id,
          'machineName'       => $local_lib->machine_name,
          'title'             => $local_lib->title,
          'description'       => '',
          'majorVersion'      => (int) $local_lib->major_version,
          'minorVersion'      => (int) $local_lib->minor_version,
          'patchVersion'      => (int) $local_lib->patch_version,
          'localMajorVersion' => (int) $local_lib->major_version,
          'localMinorVersion' => (int) $local_lib->minor_version,
          'localPatchVersion' => (int) $local_lib->patch_version,
          'canInstall'        => FALSE,
          'installed'         => TRUE,
          'isUpToDate'        => TRUE,
          'owner'             => '',
          'restricted'        => $can_create_restricted ? FALSE :
            ($local_lib->restricted ? TRUE : FALSE)
        );

        if (isset($icon_path)) {
          $local_only_lib['icon'] = $icon_path;
        }

        $cached_libraries[] = $local_only_lib;
      }
    }

    // Restrict LRS dependent content
    if (!$this->h5p->h5pF->getOption('enable_lrs_content_types')) {
      foreach ($cached_libraries as &$lib) {
        if (in_array($lib['machineName'], array('H5P.Questionnaire', 'H5P.FreeTextQuestion'))) {
          $lib['restricted'] = TRUE;
        }
      }
    }
  }

  /**
   * Determine if a library has a presave.js file in the root folder
   *
   * @param string $libraryName
   * @return bool
   */
  public function hasPresave($libraryName){
    if( isset($this->h5p->h5pD) ){
      $parsedLibrary = H5PCore::libraryFromString($libraryName);
      if($parsedLibrary !== false){
        $machineName = $parsedLibrary['machineName'];
        $majorVersion = $parsedLibrary['majorVersion'];
        $minorVersion = $parsedLibrary['minorVersion'];
        $library = $this->h5p->h5pD->getLibrary($machineName, $majorVersion, $minorVersion);
        if( !is_null($library)){
          return $this->h5p->fs->hasPresave($libraryName, $library['path']);
        }
      }
    }
    return $this->h5p->fs->hasPresave($libraryName);
  }

  /**
   * Adds the path to the presave.js file to the list of dependency assets for the library
   *
   * @param array $assets
   * @param array $library
   * @param string $prefix
   */
  public function addPresaveFile(&$assets, $library, $prefix = ''){
    $path = 'libraries' . '/' . H5PCore::libraryToFolderName($library);
    if( array_key_exists('path', $library)){
      $path = $library['path'];
    }
    $version = "?ver={$library['majorVersion']}.{$library['minorVersion']}.{$library['patchVersion']}";
    if( array_key_exists('version', $library) ){
      $version = $library['version'];
    }

    $assets['scripts'][] = (object) array(
      'path' => $prefix . '/' . $path . '/' . 'presave.js',
      'version' => $version,
    );
  }
}
