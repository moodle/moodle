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

class StoragePoolExapoolProvisionedCapacityGb extends \Google\Model
{
  /**
   * Output only. Size, in GiB, of provisioned capacity-optimized capacity for
   * this Exapool
   *
   * @var string
   */
  public $capacityOptimized;
  /**
   * Output only. Size, in GiB, of provisioned read-optimized capacity for this
   * Exapool
   *
   * @var string
   */
  public $readOptimized;
  /**
   * Output only. Size, in GiB, of provisioned write-optimized capacity for this
   * Exapool
   *
   * @var string
   */
  public $writeOptimized;

  /**
   * Output only. Size, in GiB, of provisioned capacity-optimized capacity for
   * this Exapool
   *
   * @param string $capacityOptimized
   */
  public function setCapacityOptimized($capacityOptimized)
  {
    $this->capacityOptimized = $capacityOptimized;
  }
  /**
   * @return string
   */
  public function getCapacityOptimized()
  {
    return $this->capacityOptimized;
  }
  /**
   * Output only. Size, in GiB, of provisioned read-optimized capacity for this
   * Exapool
   *
   * @param string $readOptimized
   */
  public function setReadOptimized($readOptimized)
  {
    $this->readOptimized = $readOptimized;
  }
  /**
   * @return string
   */
  public function getReadOptimized()
  {
    return $this->readOptimized;
  }
  /**
   * Output only. Size, in GiB, of provisioned write-optimized capacity for this
   * Exapool
   *
   * @param string $writeOptimized
   */
  public function setWriteOptimized($writeOptimized)
  {
    $this->writeOptimized = $writeOptimized;
  }
  /**
   * @return string
   */
  public function getWriteOptimized()
  {
    return $this->writeOptimized;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StoragePoolExapoolProvisionedCapacityGb::class, 'Google_Service_Compute_StoragePoolExapoolProvisionedCapacityGb');
