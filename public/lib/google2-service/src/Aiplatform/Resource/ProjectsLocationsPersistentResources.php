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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListPersistentResourcesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1PersistentResource;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1RebootPersistentResourceRequest;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "persistentResources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $persistentResources = $aiplatformService->projects_locations_persistentResources;
 *  </code>
 */
class ProjectsLocationsPersistentResources extends \Google\Service\Resource
{
  /**
   * Creates a PersistentResource. (persistentResources.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the PersistentResource in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1PersistentResource $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string persistentResourceId Required. The ID to use for the
   * PersistentResource, which become the final component of the
   * PersistentResource's resource name. The maximum length is 63 characters, and
   * valid characters are `/^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$/`.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1PersistentResource $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a PersistentResource. (persistentResources.delete)
   *
   * @param string $name Required. The name of the PersistentResource to be
   * deleted. Format: `projects/{project}/locations/{location}/persistentResources
   * /{persistent_resource}`
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a PersistentResource. (persistentResources.get)
   *
   * @param string $name Required. The name of the PersistentResource resource.
   * Format: `projects/{project_id_or_number}/locations/{location_id}/persistentRe
   * sources/{persistent_resource_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1PersistentResource
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1PersistentResource::class);
  }
  /**
   * Lists PersistentResources in a Location.
   * (persistentResources.listProjectsLocationsPersistentResources)
   *
   * @param string $parent Required. The resource name of the Location to list the
   * PersistentResources from. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The standard list page size.
   * @opt_param string pageToken Optional. The standard list page token. Typically
   * obtained via ListPersistentResourcesResponse.next_page_token of the previous
   * PersistentResourceService.ListPersistentResource call.
   * @return GoogleCloudAiplatformV1ListPersistentResourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPersistentResources($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListPersistentResourcesResponse::class);
  }
  /**
   * Updates a PersistentResource. (persistentResources.patch)
   *
   * @param string $name Immutable. Resource name of a PersistentResource.
   * @param GoogleCloudAiplatformV1PersistentResource $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Specify the fields to be overwritten
   * in the PersistentResource by the update method.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1PersistentResource $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Reboots a PersistentResource. (persistentResources.reboot)
   *
   * @param string $name Required. The name of the PersistentResource resource.
   * Format: `projects/{project_id_or_number}/locations/{location_id}/persistentRe
   * sources/{persistent_resource_id}`
   * @param GoogleCloudAiplatformV1RebootPersistentResourceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function reboot($name, GoogleCloudAiplatformV1RebootPersistentResourceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reboot', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPersistentResources::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsPersistentResources');
