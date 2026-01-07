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

namespace Google\Service\ManagedKafka;

class UpdateOptions extends \Google\Model
{
  /**
   * Optional. If true, allows an update operation that increases the total vCPU
   * and/or memory allocation of the cluster to significantly decrease the per-
   * broker vCPU and/or memory allocation. This can result in reduced
   * performance and availability. By default, the update operation will fail if
   * an upscale request results in a vCPU or memory allocation for the brokers
   * that is smaller than 90% of the current broker size.
   *
   * @var bool
   */
  public $allowBrokerDownscaleOnClusterUpscale;

  /**
   * Optional. If true, allows an update operation that increases the total vCPU
   * and/or memory allocation of the cluster to significantly decrease the per-
   * broker vCPU and/or memory allocation. This can result in reduced
   * performance and availability. By default, the update operation will fail if
   * an upscale request results in a vCPU or memory allocation for the brokers
   * that is smaller than 90% of the current broker size.
   *
   * @param bool $allowBrokerDownscaleOnClusterUpscale
   */
  public function setAllowBrokerDownscaleOnClusterUpscale($allowBrokerDownscaleOnClusterUpscale)
  {
    $this->allowBrokerDownscaleOnClusterUpscale = $allowBrokerDownscaleOnClusterUpscale;
  }
  /**
   * @return bool
   */
  public function getAllowBrokerDownscaleOnClusterUpscale()
  {
    return $this->allowBrokerDownscaleOnClusterUpscale;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateOptions::class, 'Google_Service_ManagedKafka_UpdateOptions');
