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

namespace Google\Service\Compute;

class InstanceGroupManagerVersion extends \Google\Model
{
  /**
   * The URL of the instance template that is specified for this managed
   * instance group. The group uses this template to create new instances in the
   * managed instance group until the `targetSize` for this version is reached.
   * The templates for existing instances in the group do not change unless you
   * run recreateInstances, runapplyUpdatesToInstances, or set the
   * group'supdatePolicy.type to PROACTIVE; in those cases, existing instances
   * are updated until the `targetSize` for this version is reached.
   *
   * @var string
   */
  public $instanceTemplate;
  /**
   * Name of the version. Unique among all versions in the scope of this managed
   * instance group.
   *
   * @var string
   */
  public $name;
  protected $targetSizeType = FixedOrPercent::class;
  protected $targetSizeDataType = '';

  /**
   * The URL of the instance template that is specified for this managed
   * instance group. The group uses this template to create new instances in the
   * managed instance group until the `targetSize` for this version is reached.
   * The templates for existing instances in the group do not change unless you
   * run recreateInstances, runapplyUpdatesToInstances, or set the
   * group'supdatePolicy.type to PROACTIVE; in those cases, existing instances
   * are updated until the `targetSize` for this version is reached.
   *
   * @param string $instanceTemplate
   */
  public function setInstanceTemplate($instanceTemplate)
  {
    $this->instanceTemplate = $instanceTemplate;
  }
  /**
   * @return string
   */
  public function getInstanceTemplate()
  {
    return $this->instanceTemplate;
  }
  /**
   * Name of the version. Unique among all versions in the scope of this managed
   * instance group.
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
   * Specifies the intended number of instances to be created from
   * theinstanceTemplate. The final number of instances created from the
   * template will be equal to:              - If expressed as a fixed number,
   * the minimum of either       targetSize.fixed or
   * instanceGroupManager.targetSize is used.      - if expressed as a percent,
   * the targetSize      would be (targetSize.percent/100 *
   * InstanceGroupManager.targetSize) If there is a remainder, the      number
   * is rounded.       If unset, this version will update any remaining
   * instances not updated by another version. ReadStarting a canary update for
   * more information.
   *
   * @param FixedOrPercent $targetSize
   */
  public function setTargetSize(FixedOrPercent $targetSize)
  {
    $this->targetSize = $targetSize;
  }
  /**
   * @return FixedOrPercent
   */
  public function getTargetSize()
  {
    return $this->targetSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerVersion::class, 'Google_Service_Compute_InstanceGroupManagerVersion');
