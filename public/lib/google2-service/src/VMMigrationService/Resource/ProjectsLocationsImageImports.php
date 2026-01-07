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

namespace Google\Service\VMMigrationService\Resource;

use Google\Service\VMMigrationService\ImageImport;
use Google\Service\VMMigrationService\ListImageImportsResponse;
use Google\Service\VMMigrationService\Operation;

/**
 * The "imageImports" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmmigrationService = new Google\Service\VMMigrationService(...);
 *   $imageImports = $vmmigrationService->projects_locations_imageImports;
 *  </code>
 */
class ProjectsLocationsImageImports extends \Google\Service\Resource
{
  /**
   * Creates a new ImageImport in a given project. (imageImports.create)
   *
   * @param string $parent Required. The ImageImport's parent.
   * @param ImageImport $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string imageImportId Required. The image import identifier. This
   * value maximum length is 63 characters, and valid characters are /a-z-/. It
   * must start with an english letter and must not end with a hyphen.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server will guarantee that for at least 60 minutes since the first request.
   * For example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, ImageImport $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single ImageImport. (imageImports.delete)
   *
   * @param string $name Required. The ImageImport name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server will guarantee that for at least 60 minutes after the first request.
   * For example, consider a situation where you make an initial request and t he
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Gets details of a single ImageImport. (imageImports.get)
   *
   * @param string $name Required. The ImageImport name.
   * @param array $optParams Optional parameters.
   * @return ImageImport
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ImageImport::class);
  }
  /**
   * Lists ImageImports in a given project.
   * (imageImports.listProjectsLocationsImageImports)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * targets.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The filter request (according to AIP-160).
   * @opt_param string orderBy Optional. The order by fields for the result
   * (according to AIP-132). Currently ordering is only possible by "name" field.
   * @opt_param int pageSize Optional. The maximum number of targets to return.
   * The service may return fewer than this value. If unspecified, at most 500
   * targets will be returned. The maximum value is 1000; values above 1000 will
   * be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListImageImports` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListImageImports` must match
   * the call that provided the page token.
   * @return ListImageImportsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsImageImports($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListImageImportsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsImageImports::class, 'Google_Service_VMMigrationService_Resource_ProjectsLocationsImageImports');
