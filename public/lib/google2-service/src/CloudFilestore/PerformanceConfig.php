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

namespace Google\Service\CloudFilestore;

class PerformanceConfig extends \Google\Model
{
  protected $fixedIopsType = FixedIOPS::class;
  protected $fixedIopsDataType = '';
  protected $iopsPerTbType = IOPSPerTB::class;
  protected $iopsPerTbDataType = '';

  /**
   * Choose a fixed provisioned IOPS value for the instance, which will remain
   * constant regardless of instance capacity. Value must be a multiple of 1000.
   * If the chosen value is outside the supported range for the instance's
   * capacity during instance creation, instance creation will fail with an
   * `InvalidArgument` error. Similarly, if an instance capacity update would
   * result in a value outside the supported range, the update will fail with an
   * `InvalidArgument` error.
   *
   * @param FixedIOPS $fixedIops
   */
  public function setFixedIops(FixedIOPS $fixedIops)
  {
    $this->fixedIops = $fixedIops;
  }
  /**
   * @return FixedIOPS
   */
  public function getFixedIops()
  {
    return $this->fixedIops;
  }
  /**
   * Provision IOPS dynamically based on the capacity of the instance.
   * Provisioned IOPS will be calculated by multiplying the capacity of the
   * instance in TiB by the `iops_per_tb` value. For example, for a 2 TiB
   * instance with an `iops_per_tb` value of 17000 the provisioned IOPS will be
   * 34000. If the calculated value is outside the supported range for the
   * instance's capacity during instance creation, instance creation will fail
   * with an `InvalidArgument` error. Similarly, if an instance capacity update
   * would result in a value outside the supported range, the update will fail
   * with an `InvalidArgument` error.
   *
   * @param IOPSPerTB $iopsPerTb
   */
  public function setIopsPerTb(IOPSPerTB $iopsPerTb)
  {
    $this->iopsPerTb = $iopsPerTb;
  }
  /**
   * @return IOPSPerTB
   */
  public function getIopsPerTb()
  {
    return $this->iopsPerTb;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerformanceConfig::class, 'Google_Service_CloudFilestore_PerformanceConfig');
