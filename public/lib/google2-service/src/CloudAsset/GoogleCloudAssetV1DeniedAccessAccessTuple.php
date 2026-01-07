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

class GoogleCloudAssetV1DeniedAccessAccessTuple extends \Google\Model
{
  protected $accessType = GoogleCloudAssetV1DeniedAccessAccess::class;
  protected $accessDataType = '';
  protected $identityType = GoogleCloudAssetV1DeniedAccessIdentity::class;
  protected $identityDataType = '';
  protected $resourceType = GoogleCloudAssetV1DeniedAccessResource::class;
  protected $resourceDataType = '';

  /**
   * @param GoogleCloudAssetV1DeniedAccessAccess
   */
  public function setAccess(GoogleCloudAssetV1DeniedAccessAccess $access)
  {
    $this->access = $access;
  }
  /**
   * @return GoogleCloudAssetV1DeniedAccessAccess
   */
  public function getAccess()
  {
    return $this->access;
  }
  /**
   * @param GoogleCloudAssetV1DeniedAccessIdentity
   */
  public function setIdentity(GoogleCloudAssetV1DeniedAccessIdentity $identity)
  {
    $this->identity = $identity;
  }
  /**
   * @return GoogleCloudAssetV1DeniedAccessIdentity
   */
  public function getIdentity()
  {
    return $this->identity;
  }
  /**
   * @param GoogleCloudAssetV1DeniedAccessResource
   */
  public function setResource(GoogleCloudAssetV1DeniedAccessResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return GoogleCloudAssetV1DeniedAccessResource
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssetV1DeniedAccessAccessTuple::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1DeniedAccessAccessTuple');
