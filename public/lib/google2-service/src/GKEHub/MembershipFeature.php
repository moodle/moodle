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

namespace Google\Service\GKEHub;

class MembershipFeature extends \Google\Model
{
  /**
   * Output only. When the MembershipFeature resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. When the MembershipFeature resource was deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * GCP labels for this MembershipFeature.
   *
   * @var string[]
   */
  public $labels;
  protected $lifecycleStateType = LifecycleState::class;
  protected $lifecycleStateDataType = '';
  /**
   * Output only. The resource name of the membershipFeature, in the format: `pr
   * ojects/{project}/locations/{location}/memberships/{membership}/features/{fe
   * ature}`. Note that `membershipFeatures` is shortened to `features` in the
   * resource name. (see http://go/aip/122#collection-identifiers)
   *
   * @var string
   */
  public $name;
  protected $specType = FeatureSpec::class;
  protected $specDataType = '';
  protected $stateType = FeatureState::class;
  protected $stateDataType = '';
  /**
   * Output only. When the MembershipFeature resource was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. When the MembershipFeature resource was created.
   *
   * @param string $createTime
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
   * Output only. When the MembershipFeature resource was deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * GCP labels for this MembershipFeature.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Lifecycle information of the resource itself.
   *
   * @param LifecycleState $lifecycleState
   */
  public function setLifecycleState(LifecycleState $lifecycleState)
  {
    $this->lifecycleState = $lifecycleState;
  }
  /**
   * @return LifecycleState
   */
  public function getLifecycleState()
  {
    return $this->lifecycleState;
  }
  /**
   * Output only. The resource name of the membershipFeature, in the format: `pr
   * ojects/{project}/locations/{location}/memberships/{membership}/features/{fe
   * ature}`. Note that `membershipFeatures` is shortened to `features` in the
   * resource name. (see http://go/aip/122#collection-identifiers)
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
   * Optional. Spec of this membershipFeature.
   *
   * @param FeatureSpec $spec
   */
  public function setSpec(FeatureSpec $spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return FeatureSpec
   */
  public function getSpec()
  {
    return $this->spec;
  }
  /**
   * Output only. State of the this membershipFeature.
   *
   * @param FeatureState $state
   */
  public function setState(FeatureState $state)
  {
    $this->state = $state;
  }
  /**
   * @return FeatureState
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. When the MembershipFeature resource was last updated.
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
class_alias(MembershipFeature::class, 'Google_Service_GKEHub_MembershipFeature');
