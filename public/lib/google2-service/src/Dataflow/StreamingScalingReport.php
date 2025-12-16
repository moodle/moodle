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

namespace Google\Service\Dataflow;

class StreamingScalingReport extends \Google\Model
{
  /**
   * @deprecated
   * @var int
   */
  public $activeBundleCount;
  /**
   * Current acive thread count.
   *
   * @var int
   */
  public $activeThreadCount;
  /**
   * Maximum bundle count.
   *
   * @var int
   */
  public $maximumBundleCount;
  /**
   * Maximum bytes.
   *
   * @var string
   */
  public $maximumBytes;
  /**
   * @deprecated
   * @var int
   */
  public $maximumBytesCount;
  /**
   * Maximum thread count limit.
   *
   * @var int
   */
  public $maximumThreadCount;
  /**
   * Current outstanding bundle count.
   *
   * @var int
   */
  public $outstandingBundleCount;
  /**
   * Current outstanding bytes.
   *
   * @var string
   */
  public $outstandingBytes;
  /**
   * @deprecated
   * @var int
   */
  public $outstandingBytesCount;

  /**
   * @deprecated
   * @param int $activeBundleCount
   */
  public function setActiveBundleCount($activeBundleCount)
  {
    $this->activeBundleCount = $activeBundleCount;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getActiveBundleCount()
  {
    return $this->activeBundleCount;
  }
  /**
   * Current acive thread count.
   *
   * @param int $activeThreadCount
   */
  public function setActiveThreadCount($activeThreadCount)
  {
    $this->activeThreadCount = $activeThreadCount;
  }
  /**
   * @return int
   */
  public function getActiveThreadCount()
  {
    return $this->activeThreadCount;
  }
  /**
   * Maximum bundle count.
   *
   * @param int $maximumBundleCount
   */
  public function setMaximumBundleCount($maximumBundleCount)
  {
    $this->maximumBundleCount = $maximumBundleCount;
  }
  /**
   * @return int
   */
  public function getMaximumBundleCount()
  {
    return $this->maximumBundleCount;
  }
  /**
   * Maximum bytes.
   *
   * @param string $maximumBytes
   */
  public function setMaximumBytes($maximumBytes)
  {
    $this->maximumBytes = $maximumBytes;
  }
  /**
   * @return string
   */
  public function getMaximumBytes()
  {
    return $this->maximumBytes;
  }
  /**
   * @deprecated
   * @param int $maximumBytesCount
   */
  public function setMaximumBytesCount($maximumBytesCount)
  {
    $this->maximumBytesCount = $maximumBytesCount;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getMaximumBytesCount()
  {
    return $this->maximumBytesCount;
  }
  /**
   * Maximum thread count limit.
   *
   * @param int $maximumThreadCount
   */
  public function setMaximumThreadCount($maximumThreadCount)
  {
    $this->maximumThreadCount = $maximumThreadCount;
  }
  /**
   * @return int
   */
  public function getMaximumThreadCount()
  {
    return $this->maximumThreadCount;
  }
  /**
   * Current outstanding bundle count.
   *
   * @param int $outstandingBundleCount
   */
  public function setOutstandingBundleCount($outstandingBundleCount)
  {
    $this->outstandingBundleCount = $outstandingBundleCount;
  }
  /**
   * @return int
   */
  public function getOutstandingBundleCount()
  {
    return $this->outstandingBundleCount;
  }
  /**
   * Current outstanding bytes.
   *
   * @param string $outstandingBytes
   */
  public function setOutstandingBytes($outstandingBytes)
  {
    $this->outstandingBytes = $outstandingBytes;
  }
  /**
   * @return string
   */
  public function getOutstandingBytes()
  {
    return $this->outstandingBytes;
  }
  /**
   * @deprecated
   * @param int $outstandingBytesCount
   */
  public function setOutstandingBytesCount($outstandingBytesCount)
  {
    $this->outstandingBytesCount = $outstandingBytesCount;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getOutstandingBytesCount()
  {
    return $this->outstandingBytesCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamingScalingReport::class, 'Google_Service_Dataflow_StreamingScalingReport');
