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

namespace Google\Service\SecurityCommandCenter;

class SimulatedResource extends \Google\Model
{
  protected $iamPolicyDataType = Policy::class;
  protected $iamPolicyDataDataType = '';
  /**
   * Optional. A representation of the Google Cloud resource. Should match the
   * Google Cloud resource JSON format.
   *
   * @var array[]
   */
  public $resourceData;
  /**
   * Required. The type of the resource, for example,
   * `compute.googleapis.com/Disk`.
   *
   * @var string
   */
  public $resourceType;

  /**
   * Optional. A representation of the IAM policy.
   *
   * @param Policy $iamPolicyData
   */
  public function setIamPolicyData(Policy $iamPolicyData)
  {
    $this->iamPolicyData = $iamPolicyData;
  }
  /**
   * @return Policy
   */
  public function getIamPolicyData()
  {
    return $this->iamPolicyData;
  }
  /**
   * Optional. A representation of the Google Cloud resource. Should match the
   * Google Cloud resource JSON format.
   *
   * @param array[] $resourceData
   */
  public function setResourceData($resourceData)
  {
    $this->resourceData = $resourceData;
  }
  /**
   * @return array[]
   */
  public function getResourceData()
  {
    return $this->resourceData;
  }
  /**
   * Required. The type of the resource, for example,
   * `compute.googleapis.com/Disk`.
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SimulatedResource::class, 'Google_Service_SecurityCommandCenter_SimulatedResource');
