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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1ResourceContext extends \Google\Collection
{
  protected $collection_key = 'ancestors';
  /**
   * The ancestry path of the resource in Google Cloud [resource
   * hierarchy](https://cloud.google.com/resource-manager/docs/cloud-platform-
   * resource-hierarchy), represented as a list of relative resource names. An
   * ancestry path starts with the closest ancestor in the hierarchy and ends at
   * root. If the resource is a project, folder, or organization, the ancestry
   * path starts from the resource itself. Example: `["projects/123456789",
   * "folders/5432", "organizations/1234"]`
   *
   * @var string[]
   */
  public $ancestors;
  /**
   * The asset type of the resource as defined by CAIS. Example:
   * `compute.googleapis.com/Firewall` See [Supported asset
   * types](https://cloud.google.com/asset-inventory/docs/supported-asset-types)
   * for more information.
   *
   * @var string
   */
  public $assetType;
  /**
   * The full name of the resource. Example: `//compute.googleapis.com/projects/
   * my_project_123/zones/zone1/instances/instance1` See [Resource names](https:
   * //cloud.google.com/apis/design/resource_names#full_resource_name) for more
   * information.
   *
   * @var string
   */
  public $resource;

  /**
   * The ancestry path of the resource in Google Cloud [resource
   * hierarchy](https://cloud.google.com/resource-manager/docs/cloud-platform-
   * resource-hierarchy), represented as a list of relative resource names. An
   * ancestry path starts with the closest ancestor in the hierarchy and ends at
   * root. If the resource is a project, folder, or organization, the ancestry
   * path starts from the resource itself. Example: `["projects/123456789",
   * "folders/5432", "organizations/1234"]`
   *
   * @param string[] $ancestors
   */
  public function setAncestors($ancestors)
  {
    $this->ancestors = $ancestors;
  }
  /**
   * @return string[]
   */
  public function getAncestors()
  {
    return $this->ancestors;
  }
  /**
   * The asset type of the resource as defined by CAIS. Example:
   * `compute.googleapis.com/Firewall` See [Supported asset
   * types](https://cloud.google.com/asset-inventory/docs/supported-asset-types)
   * for more information.
   *
   * @param string $assetType
   */
  public function setAssetType($assetType)
  {
    $this->assetType = $assetType;
  }
  /**
   * @return string
   */
  public function getAssetType()
  {
    return $this->assetType;
  }
  /**
   * The full name of the resource. Example: `//compute.googleapis.com/projects/
   * my_project_123/zones/zone1/instances/instance1` See [Resource names](https:
   * //cloud.google.com/apis/design/resource_names#full_resource_name) for more
   * information.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1ResourceContext::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1ResourceContext');
