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

class GoogleCloudPolicysimulatorV1alphaOrgPolicyViolationsPreview extends \Google\Collection
{
  protected $collection_key = 'customConstraints';
  /**
   * @var string
   */
  public $createTime;
  /**
   * @var string[]
   */
  public $customConstraints;
  /**
   * @var string
   */
  public $name;
  protected $overlayType = GoogleCloudPolicysimulatorV1alphaOrgPolicyOverlay::class;
  protected $overlayDataType = '';
  protected $resourceCountsType = GoogleCloudPolicysimulatorV1alphaOrgPolicyViolationsPreviewResourceCounts::class;
  protected $resourceCountsDataType = '';
  /**
   * @var string
   */
  public $state;
  /**
   * @var int
   */
  public $violationsCount;

  /**
   * @param string
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * @param string[]
   */
  public function setCustomConstraints($customConstraints)
  {
    $this->customConstraints = $customConstraints;
  }
  /**
   * @return string[]
   */
  public function getCustomConstraints()
  {
    return $this->customConstraints;
  }
  /**
   * @param string
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
   * @param GoogleCloudPolicysimulatorV1alphaOrgPolicyOverlay
   */
  public function setOverlay(GoogleCloudPolicysimulatorV1alphaOrgPolicyOverlay $overlay)
  {
    $this->overlay = $overlay;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1alphaOrgPolicyOverlay
   */
  public function getOverlay()
  {
    return $this->overlay;
  }
  /**
   * @param GoogleCloudPolicysimulatorV1alphaOrgPolicyViolationsPreviewResourceCounts
   */
  public function setResourceCounts(GoogleCloudPolicysimulatorV1alphaOrgPolicyViolationsPreviewResourceCounts $resourceCounts)
  {
    $this->resourceCounts = $resourceCounts;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1alphaOrgPolicyViolationsPreviewResourceCounts
   */
  public function getResourceCounts()
  {
    return $this->resourceCounts;
  }
  /**
   * @param string
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * @param int
   */
  public function setViolationsCount($violationsCount)
  {
    $this->violationsCount = $violationsCount;
  }
  /**
   * @return int
   */
  public function getViolationsCount()
  {
    return $this->violationsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1alphaOrgPolicyViolationsPreview::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1alphaOrgPolicyViolationsPreview');
