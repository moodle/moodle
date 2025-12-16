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

namespace Google\Service\Datapipelines\Resource;

use Google\Service\Datapipelines\GoogleCloudDatapipelinesV1BatchGetTransformDescriptionsResponse;
use Google\Service\Datapipelines\GoogleCloudDatapipelinesV1TransformDescription;

/**
 * The "transformDescriptions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datapipelinesService = new Google\Service\Datapipelines(...);
 *   $transformDescriptions = $datapipelinesService->transformDescriptions;
 *  </code>
 */
class ProjectsLocationsTransformDescriptions extends \Google\Service\Resource
{
  /**
   * Gets transform descriptions in a batch, associated with a list of provided
   * uniform resource names. (transformDescriptions.batchGet)
   *
   * @param string $parent Required. The project and location shared by all
   * transform descriptions being retrieved, formatted as
   * "projects/{project}/locations/{location}".
   * @param array $optParams Optional parameters.
   *
   * @opt_param string names Optional. The names of the transform descriptions
   * being retrieved, formatted as "projects/{project}/locations/{location}/transf
   * ormdescriptions/{transform_description}". If no name is provided, all of the
   * transform descriptions will be returned.
   * @return GoogleCloudDatapipelinesV1BatchGetTransformDescriptionsResponse
   */
  public function batchGet($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('batchGet', [$params], GoogleCloudDatapipelinesV1BatchGetTransformDescriptionsResponse::class);
  }
  /**
   * Gets the transform description associated with the provided uniform resource
   * name. (transformDescriptions.get)
   *
   * @param string $name Required. The full name formatted as "projects/{your-
   * project}/locations/{google-cloud-region}/transformdescriptions/{uniform-
   * resource-name}".
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDatapipelinesV1TransformDescription
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDatapipelinesV1TransformDescription::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTransformDescriptions::class, 'Google_Service_Datapipelines_Resource_ProjectsLocationsTransformDescriptions');
