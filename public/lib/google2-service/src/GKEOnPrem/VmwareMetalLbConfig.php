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

namespace Google\Service\GKEOnPrem;

class VmwareMetalLbConfig extends \Google\Collection
{
  protected $collection_key = 'addressPools';
  protected $addressPoolsType = VmwareAddressPool::class;
  protected $addressPoolsDataType = 'array';

  /**
   * Required. AddressPools is a list of non-overlapping IP pools used by load
   * balancer typed services. All addresses must be routable to load balancer
   * nodes. IngressVIP must be included in the pools.
   *
   * @param VmwareAddressPool[] $addressPools
   */
  public function setAddressPools($addressPools)
  {
    $this->addressPools = $addressPools;
  }
  /**
   * @return VmwareAddressPool[]
   */
  public function getAddressPools()
  {
    return $this->addressPools;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareMetalLbConfig::class, 'Google_Service_GKEOnPrem_VmwareMetalLbConfig');
