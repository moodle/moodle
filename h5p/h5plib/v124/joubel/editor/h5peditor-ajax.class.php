<?php

abstract class H5PEditorEndpoints {

  /**
   * Endpoint for retrieving library data necessary for displaying
   * content types in the editor.
   */
  const LIBRARIES = 'libraries';

  /**
   * Endpoint for retrieving a singe library's data necessary for displaying
   * main libraries
   */
  const SINGLE_LIBRARY = 'single-library';

  /**
   * Endpoint for retrieving the currently stored content type cache
   */
  const CONTENT_TYPE_CACHE = 'content-type-cache';

  /**
   * Endpoint for installing libraries from the Content Type Hub
   */
  const LIBRARY_INSTALL = 'library-install';

  /**
   * Endpoint for uploading libraries used by the editor through the Content
   * Type Hub.
   */
  const LIBRARY_UPLOAD = 'library-upload';

  /**
   * Endpoint for uploading files used by the editor.
   */
  const FILES = 'files';

  /**
   * Endpoint for retrieveing translation files
   */
  const TRANSLATIONS = 'translations';

  /**
   * Endpoint for filtering parameters.
   */
  const FILTER = 'filter';
}


  /**
 * Class H5PEditorAjax
 * @package modules\h5peditor\h5peditor
 */
class H5PEditorAjax {

  /**
   * @var \H5PCore
   */
  public $core;

  /**
   * @var \H5peditor
   */
  public $editor;

  /**
   * @var \H5peditorStorage
   */
  public $storage;

  /**
   * H5PEditorAjax constructor requires core, editor and storage as building
   * blocks.
   *
   * @param H5PCore $H5PCore
   * @param H5peditor $H5PEditor
   * @param H5peditorStorage $H5PEditorStorage
   */
  public function __construct(H5PCore $H5PCore, H5peditor $H5PEditor, H5peditorStorage $H5PEditorStorage) {
    $this->core = $H5PCore;
    $this->editor = $H5PEditor;
    $this->storage = $H5PEditorStorage;
  }

  /**
   * @param $endpoint
   */
  public function action($endpoint) {
    switch ($endpoint) {
      case H5PEditorEndpoints::LIBRARIES:
        H5PCore::ajaxSuccess($this->editor->getLibraries(), TRUE);
        break;

      case H5PEditorEndpoints::SINGLE_LIBRARY:
        // pass on arguments
        $args = func_get_args();
        array_shift($args);
        $library = call_user_func_array(
          array($this->editor, 'getLibraryData'), $args
        );
        H5PCore::ajaxSuccess($library, TRUE);
        break;

      case H5PEditorEndpoints::CONTENT_TYPE_CACHE:
        if (!$this->isHubOn()) return;
        H5PCore::ajaxSuccess($this->getContentTypeCache(!$this->isContentTypeCacheUpdated()), TRUE);
        break;

      case H5PEditorEndpoints::LIBRARY_INSTALL:
        if (!$this->isPostRequest()) return;

        $token = func_get_arg(1);
        if (!$this->isValidEditorToken($token)) return;

        $machineName = func_get_arg(2);
        $this->libraryInstall($machineName);
        break;

      case H5PEditorEndpoints::LIBRARY_UPLOAD:
        if (!$this->isPostRequest()) return;

        $token = func_get_arg(1);
        if (!$this->isValidEditorToken($token)) return;

        $uploadPath = func_get_arg(2);
        $contentId = func_get_arg(3);
        $this->libraryUpload($uploadPath, $contentId);
        break;

      case H5PEditorEndpoints::FILES:
        $token = func_get_arg(1);
        $contentId = func_get_arg(2);
        if (!$this->isValidEditorToken($token)) return;
        $this->fileUpload($contentId);
        break;

      case H5PEditorEndpoints::TRANSLATIONS:
        $language = func_get_arg(1);
        H5PCore::ajaxSuccess($this->editor->getTranslations($_POST['libraries'], $language));
        break;

      case H5PEditorEndpoints::FILTER:
        $token = func_get_arg(1);
        if (!$this->isValidEditorToken($token)) return;
        $this->filter(func_get_arg(2));
        break;
    }
  }

  /**
   * Handles uploaded files from the editor, making sure they are validated
   * and ready to be permanently stored if saved.
   *
   * Marks all uploaded files as
   * temporary so they can be cleaned up when we have finished using them.
   *
   * @param int $contentId Id of content if already existing content
   */
  private function fileUpload($contentId = NULL) {
    $file = new H5peditorFile($this->core->h5pF);
    if (!$file->isLoaded()) {
      H5PCore::ajaxError($this->core->h5pF->t('File not found on server. Check file upload settings.'));
      return;
    }

    // Make sure file is valid and mark it for cleanup at a later time
    if ($file->validate()) {
      $file_id = $this->core->fs->saveFile($file, 0);
      $this->storage->markFileForCleanup($file_id, 0);
    }
    $file->printResult();
  }

  /**
   * Handles uploading libraries so they are ready to be modified or directly saved.
   *
   * Validates and saves any dependencies, then exposes content to the editor.
   *
   * @param {string} $uploadFilePath Path to the file that should be uploaded
   * @param {int} $contentId Content id of library
   */
  private function libraryUpload($uploadFilePath, $contentId) {
    // Verify h5p upload
    if (!$uploadFilePath) {
      H5PCore::ajaxError($this->core->h5pF->t('Could not get posted H5P.'), 'NO_CONTENT_TYPE');
      exit;
    }

    $file = $this->saveFileTemporarily($uploadFilePath, TRUE);
    if (!$file) return;

    // These has to be set instead of sending parameteres to the validation function.
    if (!$this->isValidPackage()) return;

    // Install any required dependencies
    $storage = new H5PStorage($this->core->h5pF, $this->core);
    $storage->savePackage(NULL, NULL, TRUE);

    // Make content available to editor
    $files = $this->core->fs->moveContentDirectory($this->core->h5pF->getUploadedH5pFolderPath(), $contentId);

    // Clean up
    $this->storage->removeTemporarilySavedFiles($this->core->h5pF->getUploadedH5pFolderPath());

    // Mark all files as temporary
    // TODO: Uncomment once moveContentDirectory() is fixed. JI-366
    /*foreach ($files as $file) {
      $this->storage->markFileForCleanup($file, 0);
    }*/

    H5PCore::ajaxSuccess(array(
      'h5p' => $this->core->mainJsonData,
      'content' => $this->core->contentJsonData,
      'contentTypes' => $this->getContentTypeCache()
    ));
  }

  /**
   * Validates security tokens used for the editor
   *
   * @param string $token
   *
   * @return bool
   */
  private function isValidEditorToken($token) {
    $isValidToken = $this->editor->ajaxInterface->validateEditorToken($token);
    if (!$isValidToken) {
      \H5PCore::ajaxError(
        $this->core->h5pF->t('Invalid security token.'),
        'INVALID_TOKEN'
      );
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Handles installation of libraries from the Content Type Hub.
   *
   * Accepts a machine name and attempts to fetch and install it from the Hub if
   * it is valid. Will also install any dependencies to the requested library.
   *
   * @param string $machineName Name of library that should be installed
   */
  private function libraryInstall($machineName) {

    // Determine which content type to install from post data
    if (!$machineName) {
      H5PCore::ajaxError($this->core->h5pF->t('No content type was specified.'), 'NO_CONTENT_TYPE');
      return;
    }

    // Look up content type to ensure it's valid(and to check permissions)
    $contentType = $this->editor->ajaxInterface->getContentTypeCache($machineName);
    if (!$contentType) {
      H5PCore::ajaxError($this->core->h5pF->t('The chosen content type is invalid.'), 'INVALID_CONTENT_TYPE');
      return;
    }

    // Check install permissions
    if (!$this->editor->canInstallContentType($contentType)) {
      H5PCore::ajaxError($this->core->h5pF->t('You do not have permission to install content types. Contact the administrator of your site.'), 'INSTALL_DENIED');
      return;
    }
    else {
      // Override core permission check
      $this->core->mayUpdateLibraries(TRUE);
    }

    // Retrieve content type from hub endpoint
    $response = $this->callHubEndpoint(H5PHubEndpoints::CONTENT_TYPES . $machineName);
    if (!$response) return;

    // Session parameters has to be set for validation and saving of packages
    if (!$this->isValidPackage(TRUE)) return;

    // Save H5P
    $storage = new H5PStorage($this->core->h5pF, $this->core);
    $storage->savePackage(NULL, NULL, TRUE);

    // Clean up
    $this->storage->removeTemporarilySavedFiles($this->core->h5pF->getUploadedH5pFolderPath());

    // Successfully installed. Refresh content types
    H5PCore::ajaxSuccess($this->getContentTypeCache());
  }

  /**
   * End-point for filter parameter values according to semantics.
   *
   * @param {string} $libraryParameters
   */
  private function filter($libraryParameters) {
    $libraryParameters = json_decode($libraryParameters);
    if (!$libraryParameters) {
      H5PCore::ajaxError($this->core->h5pF->t('Could not parse post data.'), 'NO_LIBRARY_PARAMETERS');
      exit;
    }

    // Filter parameters and send back to client
    $validator = new H5PContentValidator($this->core->h5pF, $this->core);
    $validator->validateLibrary($libraryParameters, (object) array('options' => array($libraryParameters->library)));
    H5PCore::ajaxSuccess($libraryParameters);
  }

  /**
   * Validates the package. Sets error messages if validation fails.
   *
   * @param bool $skipContent Will not validate cotent if set to TRUE
   *
   * @return bool
   */
  private function isValidPackage($skipContent = FALSE) {
    $validator = new H5PValidator($this->core->h5pF, $this->core);
    if (!$validator->isValidPackage($skipContent, FALSE)) {
      $this->storage->removeTemporarilySavedFiles($this->core->h5pF->getUploadedH5pPath());

      H5PCore::ajaxError(
        $this->core->h5pF->t('Validating h5p package failed.'),
        'VALIDATION_FAILED',
        NULL,
        $this->core->h5pF->getMessages('error')
      );
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Saves a file or moves it temporarily. This is often necessary in order to
   * validate and store uploaded or fetched H5Ps.
   *
   * Sets error messages if saving fails.
   *
   * @param string $data Uri of data that should be saved as a temporary file
   * @param boolean $move_file Can be set to TRUE to move the data instead of saving it
   *
   * @return bool|object Returns false if saving failed or the path to the file
   *  if saving succeeded
   */
  private function saveFileTemporarily($data, $move_file = FALSE) {
    $file = $this->storage->saveFileTemporarily($data, $move_file);
    if (!$file) {
      H5PCore::ajaxError(
        $this->core->h5pF->t('Failed to download the requested H5P.'),
        'DOWNLOAD_FAILED'
      );
      return FALSE;
    }

    return $file;
  }

  /**
   * Calls provided hub endpoint and downloads the response to a .h5p file.
   *
   * @param string $endpoint Endpoint without protocol
   *
   * @return bool
   */
  private function callHubEndpoint($endpoint) {
    $path = $this->core->h5pF->getUploadedH5pPath();
    $response = $this->core->h5pF->fetchExternalData(H5PHubEndpoints::createURL($endpoint), NULL, TRUE, empty($path) ? TRUE : $path);
    if (!$response) {
      H5PCore::ajaxError(
        $this->core->h5pF->t('Failed to download the requested H5P.'),
        'DOWNLOAD_FAILED',
        NULL,
        $this->core->h5pF->getMessages('error')
      );
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Checks if request is a POST. Sets error message on fail.
   *
   * @return bool
   */
  private function isPostRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      H5PCore::ajaxError(
        $this->core->h5pF->t('A post message is required to access the given endpoint'),
        'REQUIRES_POST',
        405
      );
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Checks if H5P Hub is enabled. Sets error message on fail.
   *
   * @return bool
   */
  private function isHubOn() {
    if (!$this->core->h5pF->getOption('hub_is_enabled', TRUE)) {
      H5PCore::ajaxError(
        $this->core->h5pF->t('The hub is disabled. You can enable it in the H5P settings.'),
        'HUB_DISABLED',
        403
      );
      return false;
    }
    return true;
  }

  /**
   * Checks if Content Type Cache is up to date. Immediately tries to fetch
   * a new Content Type Cache if it is outdated.
   * Sets error message if fetching new Content Type Cache fails.
   *
   * @return bool
   */
  private function isContentTypeCacheUpdated() {

    // Update content type cache if enabled and too old
    $ct_cache_last_update = $this->core->h5pF->getOption('content_type_cache_updated_at', 0);
    $outdated_cache       = $ct_cache_last_update + (60 * 60 * 24 * 7); // 1 week
    if (time() > $outdated_cache) {
      $success = $this->core->updateContentTypeCache();
      if (!$success) {
        return false;
      }
    }
    return true;
  }

  /**
   * Gets content type cache for globally available libraries and the order
   * in which they have been used by the author
   *
   * @param bool $cacheOutdated The cache is outdated and not able to update
   */
  private function getContentTypeCache($cacheOutdated = FALSE) {
    $canUpdateOrInstall = ($this->core->h5pF->hasPermission(H5PPermission::INSTALL_RECOMMENDED) ||
                           $this->core->h5pF->hasPermission(H5PPermission::UPDATE_LIBRARIES));
    return array(
      'outdated' => $cacheOutdated && $canUpdateOrInstall,
      'libraries' => $this->editor->getLatestGlobalLibrariesData(),
      'recentlyUsed' => $this->editor->ajaxInterface->getAuthorsRecentlyUsedLibraries(),
      'apiVersion' => array(
        'major' => H5PCore::$coreApi['majorVersion'],
        'minor' => H5PCore::$coreApi['minorVersion']
      ),
      'details' => $this->core->h5pF->getMessages('info')
    );
  }
}
