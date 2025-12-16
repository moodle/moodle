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

class VersionedResource extends \Google\Collection
{
  protected $collection_key = 'assetExceptions';
  protected $assetExceptionsType = AssetException::class;
  protected $assetExceptionsDataType = 'array';
  /**
   * JSON representation of the resource as defined by the corresponding service
   * providing this resource. Example: If the resource is an instance provided
   * by Compute Engine, this field will contain the JSON representation of the
   * instance as defined by Compute Engine:
   * `https://cloud.google.com/compute/docs/reference/rest/v1/instances`. You
   * can find the resource definition for each supported resource type in this
   * table: `https://cloud.google.com/asset-inventory/docs/supported-asset-
   * types`
   *
   * @var array[]
   */
  public $resource;
  /**
   * API version of the resource. Example: If the resource is an instance
   * provided by Compute Engine v1 API as defined in
   * `https://cloud.google.com/compute/docs/reference/rest/v1/instances`,
   * version will be "v1".
   *
   * @var string
   */
  public $version;

  /**
   * The exceptions of a resource.
   *
   * @param AssetException[] $assetExceptions
   */
  public function setAssetExceptions($assetExceptions)
  {
    $this->assetExceptions = $assetExceptions;
  }
  /**
   * @return AssetException[]
   */
  public function getAssetExceptions()
  {
    return $this->assetExceptions;
  }
  /**
   * JSON representation of the resource as defined by the corresponding service
   * providing this resource. Example: If the resource is an instance provided
   * by Compute Engine, this field will contain the JSON representation of the
   * instance as defined by Compute Engine:
   * `https://cloud.google.com/compute/docs/reference/rest/v1/instances`. You
   * can find the resource definition for each supported resource type in this
   * table: `https://cloud.google.com/asset-inventory/docs/supported-asset-
   * types`
   *
   * @param array[] $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return array[]
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * API version of the resource. Example: If the resource is an instance
   * provided by Compute Engine v1 API as defined in
   * `https://cloud.google.com/compute/docs/reference/rest/v1/instances`,
   * version will be "v1".
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VersionedResource::class, 'Google_Service_CloudAsset_VersionedResource');
