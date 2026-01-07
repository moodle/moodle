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

namespace Google\Service\CloudAsset;

class RelatedAsset extends \Google\Collection
{
  protected $collection_key = 'ancestors';
  /**
   * The ancestors of an asset in Google Cloud [resource
   * hierarchy](https://cloud.google.com/resource-manager/docs/cloud-platform-
   * resource-hierarchy), represented as a list of relative resource names. An
   * ancestry path starts with the closest ancestor in the hierarchy and ends at
   * root. Example: `["projects/123456789", "folders/5432",
   * "organizations/1234"]`
   *
   * @var string[]
   */
  public $ancestors;
  /**
   * The full name of the asset. Example: `//compute.googleapis.com/projects/my_
   * project_123/zones/zone1/instances/instance1` See [Resource names](https://c
   * loud.google.com/apis/design/resource_names#full_resource_name) for more
   * information.
   *
   * @var string
   */
  public $asset;
  /**
   * The type of the asset. Example: `compute.googleapis.com/Disk` See
   * [Supported asset types](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types) for more information.
   *
   * @var string
   */
  public $assetType;
  /**
   * The unique identifier of the relationship type. Example:
   * `INSTANCE_TO_INSTANCEGROUP`
   *
   * @var string
   */
  public $relationshipType;

  /**
   * The ancestors of an asset in Google Cloud [resource
   * hierarchy](https://cloud.google.com/resource-manager/docs/cloud-platform-
   * resource-hierarchy), represented as a list of relative resource names. An
   * ancestry path starts with the closest ancestor in the hierarchy and ends at
   * root. Example: `["projects/123456789", "folders/5432",
   * "organizations/1234"]`
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
   * The full name of the asset. Example: `//compute.googleapis.com/projects/my_
   * project_123/zones/zone1/instances/instance1` See [Resource names](https://c
   * loud.google.com/apis/design/resource_names#full_resource_name) for more
   * information.
   *
   * @param string $asset
   */
  public function setAsset($asset)
  {
    $this->asset = $asset;
  }
  /**
   * @return string
   */
  public function getAsset()
  {
    return $this->asset;
  }
  /**
   * The type of the asset. Example: `compute.googleapis.com/Disk` See
   * [Supported asset types](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types) for more information.
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
   * The unique identifier of the relationship type. Example:
   * `INSTANCE_TO_INSTANCEGROUP`
   *
   * @param string $relationshipType
   */
  public function setRelationshipType($relationshipType)
  {
    $this->relationshipType = $relationshipType;
  }
  /**
   * @return string
   */
  public function getRelationshipType()
  {
    return $this->relationshipType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RelatedAsset::class, 'Google_Service_CloudAsset_RelatedAsset');
