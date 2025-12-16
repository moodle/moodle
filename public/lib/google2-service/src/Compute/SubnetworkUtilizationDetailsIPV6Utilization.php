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

class SubnetworkUtilizationDetailsIPV6Utilization extends \Google\Model
{
  protected $totalAllocatedIpType = Uint128::class;
  protected $totalAllocatedIpDataType = '';
  protected $totalFreeIpType = Uint128::class;
  protected $totalFreeIpDataType = '';

  /**
   * @param Uint128 $totalAllocatedIp
   */
  public function setTotalAllocatedIp(Uint128 $totalAllocatedIp)
  {
    $this->totalAllocatedIp = $totalAllocatedIp;
  }
  /**
   * @return Uint128
   */
  public function getTotalAllocatedIp()
  {
    return $this->totalAllocatedIp;
  }
  /**
   * @param Uint128 $totalFreeIp
   */
  public function setTotalFreeIp(Uint128 $totalFreeIp)
  {
    $this->totalFreeIp = $totalFreeIp;
  }
  /**
   * @return Uint128
   */
  public function getTotalFreeIp()
  {
    return $this->totalFreeIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubnetworkUtilizationDetailsIPV6Utilization::class, 'Google_Service_Compute_SubnetworkUtilizationDetailsIPV6Utilization');
