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

class SubnetworkUtilizationDetailsIPV4Utilization extends \Google\Model
{
  /**
   * Will be set for secondary range. Empty for primary IPv4 range.
   *
   * @var string
   */
  public $rangeName;
  /**
   * @var string
   */
  public $totalAllocatedIp;
  /**
   * @var string
   */
  public $totalFreeIp;

  /**
   * Will be set for secondary range. Empty for primary IPv4 range.
   *
   * @param string $rangeName
   */
  public function setRangeName($rangeName)
  {
    $this->rangeName = $rangeName;
  }
  /**
   * @return string
   */
  public function getRangeName()
  {
    return $this->rangeName;
  }
  /**
   * @param string $totalAllocatedIp
   */
  public function setTotalAllocatedIp($totalAllocatedIp)
  {
    $this->totalAllocatedIp = $totalAllocatedIp;
  }
  /**
   * @return string
   */
  public function getTotalAllocatedIp()
  {
    return $this->totalAllocatedIp;
  }
  /**
   * @param string $totalFreeIp
   */
  public function setTotalFreeIp($totalFreeIp)
  {
    $this->totalFreeIp = $totalFreeIp;
  }
  /**
   * @return string
   */
  public function getTotalFreeIp()
  {
    return $this->totalFreeIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubnetworkUtilizationDetailsIPV4Utilization::class, 'Google_Service_Compute_SubnetworkUtilizationDetailsIPV4Utilization');
