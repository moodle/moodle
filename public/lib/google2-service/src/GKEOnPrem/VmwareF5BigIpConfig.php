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

class VmwareF5BigIpConfig extends \Google\Model
{
  /**
   * The load balancer's IP address.
   *
   * @var string
   */
  public $address;
  /**
   * The preexisting partition to be used by the load balancer. This partition
   * is usually created for the admin cluster for example: 'my-f5-admin-
   * partition'.
   *
   * @var string
   */
  public $partition;
  /**
   * The pool name. Only necessary, if using SNAT.
   *
   * @var string
   */
  public $snatPool;

  /**
   * The load balancer's IP address.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * The preexisting partition to be used by the load balancer. This partition
   * is usually created for the admin cluster for example: 'my-f5-admin-
   * partition'.
   *
   * @param string $partition
   */
  public function setPartition($partition)
  {
    $this->partition = $partition;
  }
  /**
   * @return string
   */
  public function getPartition()
  {
    return $this->partition;
  }
  /**
   * The pool name. Only necessary, if using SNAT.
   *
   * @param string $snatPool
   */
  public function setSnatPool($snatPool)
  {
    $this->snatPool = $snatPool;
  }
  /**
   * @return string
   */
  public function getSnatPool()
  {
    return $this->snatPool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareF5BigIpConfig::class, 'Google_Service_GKEOnPrem_VmwareF5BigIpConfig');
