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

namespace Google\Service\ServiceControl;

class OrgPolicyViolationInfo extends \Google\Collection
{
  protected $collection_key = 'violationInfo';
  /**
   * Optional. Deprecated. Resource payload that is currently in scope and is
   * subjected to orgpolicy conditions. This payload may be the subset of the
   * actual Resource that may come in the request.
   *
   * @deprecated
   * @var array[]
   */
  public $payload;
  /**
   * Optional. Deprecated. Tags referenced on the resource at the time of
   * evaluation.
   *
   * @deprecated
   * @var string[]
   */
  public $resourceTags;
  /**
   * Optional. Resource type that the orgpolicy is checked against. Example:
   * compute.googleapis.com/Instance, store.googleapis.com/bucket
   *
   * @var string
   */
  public $resourceType;
  protected $violationInfoType = ViolationInfo::class;
  protected $violationInfoDataType = 'array';

  /**
   * Optional. Deprecated. Resource payload that is currently in scope and is
   * subjected to orgpolicy conditions. This payload may be the subset of the
   * actual Resource that may come in the request.
   *
   * @deprecated
   * @param array[] $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @deprecated
   * @return array[]
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Optional. Deprecated. Tags referenced on the resource at the time of
   * evaluation.
   *
   * @deprecated
   * @param string[] $resourceTags
   */
  public function setResourceTags($resourceTags)
  {
    $this->resourceTags = $resourceTags;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getResourceTags()
  {
    return $this->resourceTags;
  }
  /**
   * Optional. Resource type that the orgpolicy is checked against. Example:
   * compute.googleapis.com/Instance, store.googleapis.com/bucket
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
  /**
   * Optional. Policy violations
   *
   * @param ViolationInfo[] $violationInfo
   */
  public function setViolationInfo($violationInfo)
  {
    $this->violationInfo = $violationInfo;
  }
  /**
   * @return ViolationInfo[]
   */
  public function getViolationInfo()
  {
    return $this->violationInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrgPolicyViolationInfo::class, 'Google_Service_ServiceControl_OrgPolicyViolationInfo');
