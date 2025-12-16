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

namespace Google\Service\Config\Resource;

use Google\Service\Config\ExportPreviewResultRequest;
use Google\Service\Config\ExportPreviewResultResponse;
use Google\Service\Config\ListPreviewsResponse;
use Google\Service\Config\Operation;
use Google\Service\Config\Preview;

/**
 * The "previews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $configService = new Google\Service\Config(...);
 *   $previews = $configService->projects_locations_previews;
 *  </code>
 */
class ProjectsLocationsPreviews extends \Google\Service\Resource
{
  /**
   * Creates a Preview. (previews.create)
   *
   * @param string $parent Required. The parent in whose context the Preview is
   * created. The parent value is in the format:
   * 'projects/{project_id}/locations/{location}'.
   * @param Preview $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string previewId Optional. The preview ID.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Preview $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a Preview. (previews.delete)
   *
   * @param string $name Required. The name of the Preview in the format:
   * 'projects/{project_id}/locations/{location}/previews/{preview}'.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
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
   * Export Preview results. (previews.export)
   *
   * @param string $parent Required. The preview whose results should be exported.
   * The preview value is in the format:
   * 'projects/{project_id}/locations/{location}/previews/{preview}'.
   * @param ExportPreviewResultRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ExportPreviewResultResponse
   * @throws \Google\Service\Exception
   */
  public function export($parent, ExportPreviewResultRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('export', [$params], ExportPreviewResultResponse::class);
  }
  /**
   * Gets details about a Preview. (previews.get)
   *
   * @param string $name Required. The name of the preview. Format:
   * 'projects/{project_id}/locations/{location}/previews/{preview}'.
   * @param array $optParams Optional parameters.
   * @return Preview
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Preview::class);
  }
  /**
   * Lists Previews in a given project and location.
   * (previews.listProjectsLocationsPreviews)
   *
   * @param string $parent Required. The parent in whose context the Previews are
   * listed. The parent value is in the format:
   * 'projects/{project_id}/locations/{location}'.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Lists the Deployments that match the
   * filter expression. A filter expression filters the resources listed in the
   * response. The expression must be of the form '{field} {operator} {value}'
   * where operators: '<', '>', '<=', '>=', '!=', '=', ':' are supported (colon
   * ':' represents a HAS operator which is roughly synonymous with equality).
   * {field} can refer to a proto or JSON field, or a synthetic field. Field names
   * can be camelCase or snake_case. Examples: - Filter by name: name =
   * "projects/foo/locations/us-central1/deployments/bar - Filter by labels: -
   * Resources that have a key called 'foo' labels.foo:* - Resources that have a
   * key called 'foo' whose value is 'bar' labels.foo = bar - Filter by state: -
   * Deployments in CREATING state. state=CREATING
   * @opt_param string orderBy Optional. Field to use to sort the list.
   * @opt_param int pageSize Optional. When requesting a page of resources,
   * 'page_size' specifies number of resources to return. If unspecified, at most
   * 500 will be returned. The maximum value is 1000.
   * @opt_param string pageToken Optional. Token returned by previous call to
   * 'ListDeployments' which specifies the position in the list from where to
   * continue listing the resources.
   * @return ListPreviewsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPreviews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPreviewsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPreviews::class, 'Google_Service_Config_Resource_ProjectsLocationsPreviews');
