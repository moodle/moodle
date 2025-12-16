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

class PerformanceLimits extends \Google\Model
{
  /**
   * Output only. The maximum IOPS.
   *
   * @var string
   */
  public $maxIops;
  /**
   * Output only. The maximum read IOPS.
   *
   * @var string
   */
  public $maxReadIops;
  /**
   * Output only. The maximum read throughput in bytes per second.
   *
   * @var string
   */
  public $maxReadThroughputBps;
  /**
   * Output only. The maximum write IOPS.
   *
   * @var string
   */
  public $maxWriteIops;
  /**
   * Output only. The maximum write throughput in bytes per second.
   *
   * @var string
   */
  public $maxWriteThroughputBps;

  /**
   * Output only. The maximum IOPS.
   *
   * @param string $maxIops
   */
  public function setMaxIops($maxIops)
  {
    $this->maxIops = $maxIops;
  }
  /**
   * @return string
   */
  public function getMaxIops()
  {
    return $this->maxIops;
  }
  /**
   * Output only. The maximum read IOPS.
   *
   * @param string $maxReadIops
   */
  public function setMaxReadIops($maxReadIops)
  {
    $this->maxReadIops = $maxReadIops;
  }
  /**
   * @return string
   */
  public function getMaxReadIops()
  {
    return $this->maxReadIops;
  }
  /**
   * Output only. The maximum read throughput in bytes per second.
   *
   * @param string $maxReadThroughputBps
   */
  public function setMaxReadThroughputBps($maxReadThroughputBps)
  {
    $this->maxReadThroughputBps = $maxReadThroughputBps;
  }
  /**
   * @return string
   */
  public function getMaxReadThroughputBps()
  {
    return $this->maxReadThroughputBps;
  }
  /**
   * Output only. The maximum write IOPS.
   *
   * @param string $maxWriteIops
   */
  public function setMaxWriteIops($maxWriteIops)
  {
    $this->maxWriteIops = $maxWriteIops;
  }
  /**
   * @return string
   */
  public function getMaxWriteIops()
  {
    return $this->maxWriteIops;
  }
  /**
   * Output only. The maximum write throughput in bytes per second.
   *
   * @param string $maxWriteThroughputBps
   */
  public function setMaxWriteThroughputBps($maxWriteThroughputBps)
  {
    $this->maxWriteThroughputBps = $maxWriteThroughputBps;
  }
  /**
   * @return string
   */
  public function getMaxWriteThroughputBps()
  {
    return $this->maxWriteThroughputBps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerformanceLimits::class, 'Google_Service_CloudFilestore_PerformanceLimits');
