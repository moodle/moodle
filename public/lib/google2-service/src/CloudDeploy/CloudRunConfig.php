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

namespace Google\Service\CloudDeploy;

class CloudRunConfig extends \Google\Collection
{
  protected $collection_key = 'stableRevisionTags';
  /**
   * Optional. Whether Cloud Deploy should update the traffic stanza in a Cloud
   * Run Service on the user's behalf to facilitate traffic splitting. This is
   * required to be true for CanaryDeployments, but optional for
   * CustomCanaryDeployments.
   *
   * @var bool
   */
  public $automaticTrafficControl;
  /**
   * Optional. A list of tags that are added to the canary revision while the
   * canary phase is in progress.
   *
   * @var string[]
   */
  public $canaryRevisionTags;
  /**
   * Optional. A list of tags that are added to the prior revision while the
   * canary phase is in progress.
   *
   * @var string[]
   */
  public $priorRevisionTags;
  /**
   * Optional. A list of tags that are added to the final stable revision when
   * the stable phase is applied.
   *
   * @var string[]
   */
  public $stableRevisionTags;

  /**
   * Optional. Whether Cloud Deploy should update the traffic stanza in a Cloud
   * Run Service on the user's behalf to facilitate traffic splitting. This is
   * required to be true for CanaryDeployments, but optional for
   * CustomCanaryDeployments.
   *
   * @param bool $automaticTrafficControl
   */
  public function setAutomaticTrafficControl($automaticTrafficControl)
  {
    $this->automaticTrafficControl = $automaticTrafficControl;
  }
  /**
   * @return bool
   */
  public function getAutomaticTrafficControl()
  {
    return $this->automaticTrafficControl;
  }
  /**
   * Optional. A list of tags that are added to the canary revision while the
   * canary phase is in progress.
   *
   * @param string[] $canaryRevisionTags
   */
  public function setCanaryRevisionTags($canaryRevisionTags)
  {
    $this->canaryRevisionTags = $canaryRevisionTags;
  }
  /**
   * @return string[]
   */
  public function getCanaryRevisionTags()
  {
    return $this->canaryRevisionTags;
  }
  /**
   * Optional. A list of tags that are added to the prior revision while the
   * canary phase is in progress.
   *
   * @param string[] $priorRevisionTags
   */
  public function setPriorRevisionTags($priorRevisionTags)
  {
    $this->priorRevisionTags = $priorRevisionTags;
  }
  /**
   * @return string[]
   */
  public function getPriorRevisionTags()
  {
    return $this->priorRevisionTags;
  }
  /**
   * Optional. A list of tags that are added to the final stable revision when
   * the stable phase is applied.
   *
   * @param string[] $stableRevisionTags
   */
  public function setStableRevisionTags($stableRevisionTags)
  {
    $this->stableRevisionTags = $stableRevisionTags;
  }
  /**
   * @return string[]
   */
  public function getStableRevisionTags()
  {
    return $this->stableRevisionTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudRunConfig::class, 'Google_Service_CloudDeploy_CloudRunConfig');
