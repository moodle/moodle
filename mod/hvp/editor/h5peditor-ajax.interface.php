<?php


/**
 * Handles Ajax functionality that must be implemented separately for each of the
 * H5P plugins
 */
interface H5PEditorAjaxInterface {

  /**
   * Gets latest library versions that exists locally
   *
   * @return array Latest version of all local libraries
   */
  public function getLatestLibraryVersions();

  /**
   * Get locally stored Content Type Cache. If machine name is provided
   * it will only get the given content type from the cache
   *
   * @param $machineName
   *
   * @return array|object|null Returns results from querying the database
   */
  public function getContentTypeCache($machineName = NULL);

  /**
   * Gets recently used libraries for the current author
   *
   * @return array machine names. The first element in the array is the
   * most recently used.
   */
  public function getAuthorsRecentlyUsedLibraries();

  /**
   * Checks if the provided token is valid for this endpoint
   *
   * @param string $token The token that will be validated for.
   *
   * @return bool True if successful validation
   */
  public function validateEditorToken($token);

}
