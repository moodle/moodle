<?php

/**
 * This is a data layer which uses the file system so it isn't specific to any framework.
 */
class H5PDevelopment {

  const MODE_NONE = 0;
  const MODE_CONTENT = 1;
  const MODE_LIBRARY = 2;

  private $h5pF, $libraries, $language, $filesPath;

  /**
   * Constructor.
   *
   * @param H5PFrameworkInterface|object $H5PFramework
   *  The frameworks implementation of the H5PFrameworkInterface
   * @param string $filesPath
   *  Path to where H5P should store its files
   * @param $language
   * @param array $libraries Optional cache input.
   */
  public function __construct(H5PFrameworkInterface $H5PFramework, $filesPath, $language, $libraries = NULL) {
    $this->h5pF = $H5PFramework;
    $this->language = $language;
    $this->filesPath = $filesPath;
    if ($libraries !== NULL) {
      $this->libraries = $libraries;
    }
    else {
      $this->findLibraries($filesPath . '/development');
    }
  }

  /**
   * Get contents of file.
   *
   * @param string $file File path.
   * @return mixed String on success or NULL on failure.
   */
  private function getFileContents($file) {
    if (file_exists($file) === FALSE) {
      return NULL;
    }

    $contents = file_get_contents($file);
    if ($contents === FALSE) {
      return NULL;
    }

    return $contents;
  }

  /**
   * Scans development directory and find all libraries.
   *
   * @param string $path Libraries development folder
   */
  private function findLibraries($path) {
    $this->libraries = array();

    if (is_dir($path) === FALSE) {
      return;
    }

    $contents = scandir($path);

    for ($i = 0, $s = count($contents); $i < $s; $i++) {
      if ($contents[$i][0] === '.') {
        continue; // Skip hidden stuff.
      }

      $libraryPath = $path . '/' . $contents[$i];
      $libraryJSON = $this->getFileContents($libraryPath . '/library.json');
      if ($libraryJSON === NULL) {
        continue; // No JSON file, skip.
      }

      $library = json_decode($libraryJSON, TRUE);
      if ($library === NULL) {
        continue; // Invalid JSON.
      }

      // TODO: Validate props? Not really needed, is it? this is a dev site.

      $library['libraryId'] = $this->h5pF->getLibraryId($library['machineName'], $library['majorVersion'], $library['minorVersion']);

      // Convert metadataSettings values to boolean & json_encode it before saving
      $library['metadataSettings'] = isset($library['metadataSettings']) ?
        H5PMetadata::boolifyAndEncodeSettings($library['metadataSettings']) :
        NULL;

      // Save/update library.
      $this->h5pF->saveLibraryData($library, $library['libraryId'] === FALSE);

      // Need to decode it again, since it is served from here.
      $library['metadataSettings'] = json_decode($library['metadataSettings']);

      $library['path'] = 'development/' . $contents[$i];
      $this->libraries[H5PDevelopment::libraryToString($library['machineName'], $library['majorVersion'], $library['minorVersion'])] = $library;
    }

    // TODO: Should we remove libraries without files? Not really needed, but must be cleaned up some time, right?

    // Go trough libraries and insert dependencies. Missing deps. will just be ignored and not available. (I guess?!)
    $this->h5pF->lockDependencyStorage();
    foreach ($this->libraries as $library) {
      $this->h5pF->deleteLibraryDependencies($library['libraryId']);
      // This isn't optimal, but without it we would get duplicate warnings.
      // TODO: You might get PDOExceptions if two or more requests does this at the same time!!
      $types = array('preloaded', 'dynamic', 'editor');
      foreach ($types as $type) {
        if (isset($library[$type . 'Dependencies'])) {
          $this->h5pF->saveLibraryDependencies($library['libraryId'], $library[$type . 'Dependencies'], $type);
        }
      }
    }
    $this->h5pF->unlockDependencyStorage();
    // TODO: Deps must be inserted into h5p_nodes_libraries as well... ? But only if they are used?!
  }

  /**
   * @return array Libraries in development folder.
   */
  public function getLibraries() {
    return $this->libraries;
  }

  /**
   * Get library
   *
   * @param string $name of the library.
   * @param int $majorVersion of the library.
   * @param int $minorVersion of the library.
   * @return array library.
   */
  public function getLibrary($name, $majorVersion, $minorVersion) {
    $library = H5PDevelopment::libraryToString($name, $majorVersion, $minorVersion);
    return isset($this->libraries[$library]) === TRUE ? $this->libraries[$library] : NULL;
  }

  /**
   * Get semantics for the given library.
   *
   * @param string $name of the library.
   * @param int $majorVersion of the library.
   * @param int $minorVersion of the library.
   * @return string Semantics
   */
  public function getSemantics($name, $majorVersion, $minorVersion) {
    $library = H5PDevelopment::libraryToString($name, $majorVersion, $minorVersion);
    if (isset($this->libraries[$library]) === FALSE) {
      return NULL;
    }
    return $this->getFileContents($this->filesPath . $this->libraries[$library]['path'] . '/semantics.json');
  }

  /**
   * Get translations for the given library.
   *
   * @param string $name of the library.
   * @param int $majorVersion of the library.
   * @param int $minorVersion of the library.
   * @param $language
   * @return string Translation
   */
  public function getLanguage($name, $majorVersion, $minorVersion, $language) {
    $library = H5PDevelopment::libraryToString($name, $majorVersion, $minorVersion);

    if (isset($this->libraries[$library]) === FALSE) {
      return NULL;
    }

    return $this->getFileContents($this->filesPath . $this->libraries[$library]['path'] . '/language/' . $language . '.json');
  }

  /**
   * Writes library as string on the form "name majorVersion.minorVersion"
   *
   * @param string $name Machine readable library name
   * @param integer $majorVersion
   * @param $minorVersion
   * @return string Library identifier.
   */
  public static function libraryToString($name, $majorVersion, $minorVersion) {
    return $name . ' ' . $majorVersion . '.' . $minorVersion;
  }
}
