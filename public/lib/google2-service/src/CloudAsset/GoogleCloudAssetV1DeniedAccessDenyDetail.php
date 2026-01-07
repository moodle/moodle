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

class GoogleCloudAssetV1DeniedAccessDenyDetail extends \Google\Collection
{
  protected $collection_key = 'resources';
  protected $accessesType = GoogleCloudAssetV1DeniedAccessAccess::class;
  protected $accessesDataType = 'array';
  protected $denyRuleType = GoogleIamV2DenyRule::class;
  protected $denyRuleDataType = '';
  /**
   * @var bool
   */
  public $fullyDenied;
  protected $identitiesType = GoogleCloudAssetV1DeniedAccessIdentity::class;
  protected $identitiesDataType = 'array';
  protected $resourcesType = GoogleCloudAssetV1DeniedAccessResource::class;
  protected $resourcesDataType = 'array';

  /**
   * @param GoogleCloudAssetV1DeniedAccessAccess[]
   */
  public function setAccesses($accesses)
  {
    $this->accesses = $accesses;
  }
  /**
   * @return GoogleCloudAssetV1DeniedAccessAccess[]
   */
  public function getAccesses()
  {
    return $this->accesses;
  }
  /**
   * @param GoogleIamV2DenyRule
   */
  public function setDenyRule(GoogleIamV2DenyRule $denyRule)
  {
    $this->denyRule = $denyRule;
  }
  /**
   * @return GoogleIamV2DenyRule
   */
  public function getDenyRule()
  {
    return $this->denyRule;
  }
  /**
   * @param bool
   */
  public function setFullyDenied($fullyDenied)
  {
    $this->fullyDenied = $fullyDenied;
  }
  /**
   * @return bool
   */
  public function getFullyDenied()
  {
    return $this->fullyDenied;
  }
  /**
   * @param GoogleCloudAssetV1DeniedAccessIdentity[]
   */
  public function setIdentities($identities)
  {
    $this->identities = $identities;
  }
  /**
   * @return GoogleCloudAssetV1DeniedAccessIdentity[]
   */
  public function getIdentities()
  {
    return $this->identities;
  }
  /**
   * @param GoogleCloudAssetV1DeniedAccessResource[]
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return GoogleCloudAssetV1DeniedAccessResource[]
   */
  public function getResources()
  {
    return $this->resources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssetV1DeniedAccessDenyDetail::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1DeniedAccessDenyDetail');
