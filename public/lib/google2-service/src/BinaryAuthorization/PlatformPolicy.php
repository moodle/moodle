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

namespace Google\Service\BinaryAuthorization;

class PlatformPolicy extends \Google\Model
{
  /**
   * Optional. A description comment about the policy.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Used to prevent updating the policy when another request has
   * updated it since it was retrieved.
   *
   * @var string
   */
  public $etag;
  protected $gkePolicyType = GkePolicy::class;
  protected $gkePolicyDataType = '';
  /**
   * Output only. The relative resource name of the Binary Authorization
   * platform policy, in the form of `projects/platforms/policies`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Time when the policy was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. A description comment about the policy.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Used to prevent updating the policy when another request has
   * updated it since it was retrieved.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. GKE platform-specific policy.
   *
   * @param GkePolicy $gkePolicy
   */
  public function setGkePolicy(GkePolicy $gkePolicy)
  {
    $this->gkePolicy = $gkePolicy;
  }
  /**
   * @return GkePolicy
   */
  public function getGkePolicy()
  {
    return $this->gkePolicy;
  }
  /**
   * Output only. The relative resource name of the Binary Authorization
   * platform policy, in the form of `projects/platforms/policies`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Time when the policy was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlatformPolicy::class, 'Google_Service_BinaryAuthorization_PlatformPolicy');
