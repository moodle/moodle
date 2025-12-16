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

namespace Google\Service\Logging\Resource;

use Google\Service\Logging\ListLogScopesResponse;
use Google\Service\Logging\LogScope;
use Google\Service\Logging\LoggingEmpty;

/**
 * The "logScopes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $loggingService = new Google\Service\Logging(...);
 *   $logScopes = $loggingService->folders_locations_logScopes;
 *  </code>
 */
class FoldersLocationsLogScopes extends \Google\Service\Resource
{
  /**
   * Creates a log scope. (logScopes.create)
   *
   * @param string $parent Required. The parent resource in which to create the
   * log scope: "projects/[PROJECT_ID]/locations/[LOCATION_ID]"
   * "organizations/[ORGANIZATION_ID]/locations/[LOCATION_ID]"
   * "folders/[FOLDER_ID]/locations/[LOCATION_ID]" For example:"projects/my-
   * project/locations/global"
   * @param LogScope $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string logScopeId Required. A client-assigned identifier such as
   * "log-scope". Identifiers are limited to 100 characters and can include only
   * letters, digits, underscores, hyphens, and periods. First character has to be
   * alphanumeric.
   * @return LogScope
   * @throws \Google\Service\Exception
   */
  public function create($parent, LogScope $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], LogScope::class);
  }
  /**
   * Deletes a log scope. (logScopes.delete)
   *
   * @param string $name Required. The resource name of the log scope to delete:
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]"
   * "organizations/[ORGANIZATION_ID]/locations/[LOCATION_ID]"
   * "folders/[FOLDER_ID]/locations/[LOCATION_ID]" For example:"projects/my-
   * project/locations/global/logScopes/my-log-scope"
   * @param array $optParams Optional parameters.
   * @return LoggingEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], LoggingEmpty::class);
  }
  /**
   * Gets a log scope. (logScopes.get)
   *
   * @param string $name Required. The resource name of the log scope:
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]"
   * "organizations/[ORGANIZATION_ID]/locations/[LOCATION_ID]"
   * "folders/[FOLDER_ID]/locations/[LOCATION_ID]" For example:"projects/my-
   * project/locations/global/logScopes/my-log-scope"
   * @param array $optParams Optional parameters.
   * @return LogScope
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], LogScope::class);
  }
  /**
   * Lists log scopes. (logScopes.listFoldersLocationsLogScopes)
   *
   * @param string $parent Required. The parent resource whose log scopes are to
   * be listed: "projects/[PROJECT_ID]/locations/[LOCATION_ID]"
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of results to return
   * from this request.Non-positive values are ignored. The presence of
   * nextPageToken in the response indicates that more results might be available.
   * @opt_param string pageToken Optional. If present, then retrieve the next
   * batch of results from the preceding call to this method. pageToken must be
   * the value of nextPageToken from the previous response. The values of other
   * method parameters should be identical to those in the previous call.
   * @return ListLogScopesResponse
   * @throws \Google\Service\Exception
   */
  public function listFoldersLocationsLogScopes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListLogScopesResponse::class);
  }
  /**
   * Updates a log scope. (logScopes.patch)
   *
   * @param string $name Output only. The resource name of the log scope.Log
   * scopes are only available in the global location. For example:projects/my-
   * project/locations/global/logScopes/my-log-scope
   * @param LogScope $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask that specifies the fields
   * in log_scope that need an update. A field will be overwritten if, and only
   * if, it is in the update mask. name and output only fields cannot be
   * updated.For a detailed FieldMask definition, see
   * https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#google.protobuf.FieldMaskFor example:
   * updateMask=description
   * @return LogScope
   * @throws \Google\Service\Exception
   */
  public function patch($name, LogScope $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], LogScope::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FoldersLocationsLogScopes::class, 'Google_Service_Logging_Resource_FoldersLocationsLogScopes');
