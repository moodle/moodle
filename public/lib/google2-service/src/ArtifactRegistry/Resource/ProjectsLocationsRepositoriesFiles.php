<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\ArtifactRegistry\Resource;

use Google\Service\ArtifactRegistry\DownloadFileResponse;
use Google\Service\ArtifactRegistry\GoogleDevtoolsArtifactregistryV1File;
use Google\Service\ArtifactRegistry\ListFilesResponse;
use Google\Service\ArtifactRegistry\Operation;
use Google\Service\ArtifactRegistry\UploadFileMediaResponse;
use Google\Service\ArtifactRegistry\UploadFileRequest;

/**
 * The "files" collection of methods.
 * Typical usage is:
 *  <code>
 *   $artifactregistryService = new Google\Service\ArtifactRegistry(...);
 *   $files = $artifactregistryService->projects_locations_repositories_files;
 *  </code>
 */
class ProjectsLocationsRepositoriesFiles extends \Google\Service\Resource
{
  /**
   * Deletes a file and all of its content. It is only allowed on generic
   * repositories. The returned operation will complete once the file has been
   * deleted. (files.delete)
   *
   * @param string $name Required. The name of the file to delete.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Download a file. (files.download)
   *
   * @param string $name Required. The name of the file to download.
   * @param array $optParams Optional parameters.
   * @return DownloadFileResponse
   * @throws \Google\Service\Exception
   */
  public function download($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('download', [$params], DownloadFileResponse::class);
  }
  /**
   * Gets a file. (files.get)
   *
   * @param string $name Required. The name of the file to retrieve.
   * @param array $optParams Optional parameters.
   * @return GoogleDevtoolsArtifactregistryV1File
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleDevtoolsArtifactregistryV1File::class);
  }
  /**
   * Lists files. (files.listProjectsLocationsRepositoriesFiles)
   *
   * @param string $parent Required. The name of the repository whose files will
   * be listed. For example: "projects/p1/locations/us-central1/repositories/repo1
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter An expression for filtering the results of the
   * request. Filter rules are case insensitive. The fields eligible for filtering
   * are: * `name` * `owner` * `annotations` Examples of using a filter: To filter
   * the results of your request to files with the name `my_file.txt` in project
   * `my-project` in the `us-central` region, in repository `my-repo`, append the
   * following filter expression to your request: * `name="projects/my-
   * project/locations/us-central1/repositories/my-repo/files/my-file.txt"` You
   * can also use wildcards to match any number of characters before or after the
   * value: * `name="projects/my-project/locations/us-central1/repositories/my-
   * repo/files/my-*"` * `name="projects/my-project/locations/us-
   * central1/repositories/my-repo/filesfile.txt"` * `name="projects/my-
   * project/locations/us-central1/repositories/my-repo/filesfile*"` To filter the
   * results of your request to files owned by the version `1.0` in package
   * `pkg1`, append the following filter expression to your request: *
   * `owner="projects/my-project/locations/us-central1/repositories/my-
   * repo/packages/my-package/versions/1.0"` To filter the results of your request
   * to files with the annotation key-value pair [`external_link`:
   * `external_link_value`], append the following filter expression to your
   * request: * `"annotations.external_link:external_link_value"` To filter just
   * for a specific annotation key `external_link`, append the following filter
   * expression to your request: * `"annotations.external_link"` If the annotation
   * key or value contains special characters, you can escape them by surrounding
   * the value with backticks. For example, to filter the results of your request
   * to files with the annotation key-value pair
   * [`external.link`:`https://example.com/my-file`], append the following filter
   * expression to your request: * ``
   * "annotations.`external.link`:`https://example.com/my-file`" `` You can also
   * filter with annotations with a wildcard to match any number of characters
   * before or after the value: * `` "annotations.*_link:`*example.com*`" ``
   * @opt_param string orderBy The field to order the results by.
   * @opt_param int pageSize The maximum number of files to return. Maximum page
   * size is 1,000.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous list request, if any.
   * @return ListFilesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesFiles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListFilesResponse::class);
  }
  /**
   * Updates a file. (files.patch)
   *
   * @param string $name The name of the file, for example:
   * `projects/p1/locations/us-central1/repositories/repo1/files/a%2Fb%2Fc.txt`.
   * If the file ID part contains slashes, they are escaped.
   * @param GoogleDevtoolsArtifactregistryV1File $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The update mask applies to the
   * resource. For the `FieldMask` definition, see
   * https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask
   * @return GoogleDevtoolsArtifactregistryV1File
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleDevtoolsArtifactregistryV1File $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleDevtoolsArtifactregistryV1File::class);
  }
  /**
   * Directly uploads a file to a repository. The returned Operation will complete
   * once the resources are uploaded. (files.upload)
   *
   * @param string $parent Required. The resource name of the repository where the
   * file will be uploaded.
   * @param UploadFileRequest $postBody
   * @param array $optParams Optional parameters.
   * @return UploadFileMediaResponse
   * @throws \Google\Service\Exception
   */
  public function upload($parent, UploadFileRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], UploadFileMediaResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesFiles::class, 'Google_Service_ArtifactRegistry_Resource_ProjectsLocationsRepositoriesFiles');
