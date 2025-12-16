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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1PolicyTargetKey extends \Google\Model
{
  /**
   * Map containing the additional target key name and value pairs used to
   * further identify the target of the policy.
   *
   * @var string[]
   */
  public $additionalTargetKeys;
  /**
   * The target resource on which this policy is applied. The following
   * resources are supported: * Organizational Unit ("orgunits/{orgunit_id}") *
   * Group ("groups/{group_id}")
   *
   * @var string
   */
  public $targetResource;

  /**
   * Map containing the additional target key name and value pairs used to
   * further identify the target of the policy.
   *
   * @param string[] $additionalTargetKeys
   */
  public function setAdditionalTargetKeys($additionalTargetKeys)
  {
    $this->additionalTargetKeys = $additionalTargetKeys;
  }
  /**
   * @return string[]
   */
  public function getAdditionalTargetKeys()
  {
    return $this->additionalTargetKeys;
  }
  /**
   * The target resource on which this policy is applied. The following
   * resources are supported: * Organizational Unit ("orgunits/{orgunit_id}") *
   * Group ("groups/{group_id}")
   *
   * @param string $targetResource
   */
  public function setTargetResource($targetResource)
  {
    $this->targetResource = $targetResource;
  }
  /**
   * @return string
   */
  public function getTargetResource()
  {
    return $this->targetResource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1PolicyTargetKey::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1PolicyTargetKey');
