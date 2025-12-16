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

namespace Google\Service\Container;

class PlacementPolicy extends \Google\Model
{
  /**
   * TYPE_UNSPECIFIED specifies no requirements on nodes placement.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * COMPACT specifies node placement in the same availability domain to ensure
   * low communication latency.
   */
  public const TYPE_COMPACT = 'COMPACT';
  /**
   * If set, refers to the name of a custom resource policy supplied by the
   * user. The resource policy must be in the same project and region as the
   * node pool. If not found, InvalidArgument error is returned.
   *
   * @var string
   */
  public $policyName;
  /**
   * Optional. TPU placement topology for pod slice node pool.
   * https://cloud.google.com/tpu/docs/types-topologies#tpu_topologies
   *
   * @var string
   */
  public $tpuTopology;
  /**
   * The type of placement.
   *
   * @var string
   */
  public $type;

  /**
   * If set, refers to the name of a custom resource policy supplied by the
   * user. The resource policy must be in the same project and region as the
   * node pool. If not found, InvalidArgument error is returned.
   *
   * @param string $policyName
   */
  public function setPolicyName($policyName)
  {
    $this->policyName = $policyName;
  }
  /**
   * @return string
   */
  public function getPolicyName()
  {
    return $this->policyName;
  }
  /**
   * Optional. TPU placement topology for pod slice node pool.
   * https://cloud.google.com/tpu/docs/types-topologies#tpu_topologies
   *
   * @param string $tpuTopology
   */
  public function setTpuTopology($tpuTopology)
  {
    $this->tpuTopology = $tpuTopology;
  }
  /**
   * @return string
   */
  public function getTpuTopology()
  {
    return $this->tpuTopology;
  }
  /**
   * The type of placement.
   *
   * Accepted values: TYPE_UNSPECIFIED, COMPACT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlacementPolicy::class, 'Google_Service_Container_PlacementPolicy');
