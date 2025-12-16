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

namespace Google\Service\ToolResults;

class GraphicsStats extends \Google\Collection
{
  protected $collection_key = 'buckets';
  protected $bucketsType = GraphicsStatsBucket::class;
  protected $bucketsDataType = 'array';
  /**
   * Total "high input latency" events.
   *
   * @var string
   */
  public $highInputLatencyCount;
  /**
   * Total frames with slow render time. Should be <= total_frames.
   *
   * @var string
   */
  public $jankyFrames;
  /**
   * Total "missed vsync" events.
   *
   * @var string
   */
  public $missedVsyncCount;
  /**
   * 50th percentile frame render time in milliseconds.
   *
   * @var string
   */
  public $p50Millis;
  /**
   * 90th percentile frame render time in milliseconds.
   *
   * @var string
   */
  public $p90Millis;
  /**
   * 95th percentile frame render time in milliseconds.
   *
   * @var string
   */
  public $p95Millis;
  /**
   * 99th percentile frame render time in milliseconds.
   *
   * @var string
   */
  public $p99Millis;
  /**
   * Total "slow bitmap upload" events.
   *
   * @var string
   */
  public $slowBitmapUploadCount;
  /**
   * Total "slow draw" events.
   *
   * @var string
   */
  public $slowDrawCount;
  /**
   * Total "slow UI thread" events.
   *
   * @var string
   */
  public $slowUiThreadCount;
  /**
   * Total frames rendered by package.
   *
   * @var string
   */
  public $totalFrames;

  /**
   * Histogram of frame render times. There should be 154 buckets ranging from
   * [5ms, 6ms) to [4950ms, infinity)
   *
   * @param GraphicsStatsBucket[] $buckets
   */
  public function setBuckets($buckets)
  {
    $this->buckets = $buckets;
  }
  /**
   * @return GraphicsStatsBucket[]
   */
  public function getBuckets()
  {
    return $this->buckets;
  }
  /**
   * Total "high input latency" events.
   *
   * @param string $highInputLatencyCount
   */
  public function setHighInputLatencyCount($highInputLatencyCount)
  {
    $this->highInputLatencyCount = $highInputLatencyCount;
  }
  /**
   * @return string
   */
  public function getHighInputLatencyCount()
  {
    return $this->highInputLatencyCount;
  }
  /**
   * Total frames with slow render time. Should be <= total_frames.
   *
   * @param string $jankyFrames
   */
  public function setJankyFrames($jankyFrames)
  {
    $this->jankyFrames = $jankyFrames;
  }
  /**
   * @return string
   */
  public function getJankyFrames()
  {
    return $this->jankyFrames;
  }
  /**
   * Total "missed vsync" events.
   *
   * @param string $missedVsyncCount
   */
  public function setMissedVsyncCount($missedVsyncCount)
  {
    $this->missedVsyncCount = $missedVsyncCount;
  }
  /**
   * @return string
   */
  public function getMissedVsyncCount()
  {
    return $this->missedVsyncCount;
  }
  /**
   * 50th percentile frame render time in milliseconds.
   *
   * @param string $p50Millis
   */
  public function setP50Millis($p50Millis)
  {
    $this->p50Millis = $p50Millis;
  }
  /**
   * @return string
   */
  public function getP50Millis()
  {
    return $this->p50Millis;
  }
  /**
   * 90th percentile frame render time in milliseconds.
   *
   * @param string $p90Millis
   */
  public function setP90Millis($p90Millis)
  {
    $this->p90Millis = $p90Millis;
  }
  /**
   * @return string
   */
  public function getP90Millis()
  {
    return $this->p90Millis;
  }
  /**
   * 95th percentile frame render time in milliseconds.
   *
   * @param string $p95Millis
   */
  public function setP95Millis($p95Millis)
  {
    $this->p95Millis = $p95Millis;
  }
  /**
   * @return string
   */
  public function getP95Millis()
  {
    return $this->p95Millis;
  }
  /**
   * 99th percentile frame render time in milliseconds.
   *
   * @param string $p99Millis
   */
  public function setP99Millis($p99Millis)
  {
    $this->p99Millis = $p99Millis;
  }
  /**
   * @return string
   */
  public function getP99Millis()
  {
    return $this->p99Millis;
  }
  /**
   * Total "slow bitmap upload" events.
   *
   * @param string $slowBitmapUploadCount
   */
  public function setSlowBitmapUploadCount($slowBitmapUploadCount)
  {
    $this->slowBitmapUploadCount = $slowBitmapUploadCount;
  }
  /**
   * @return string
   */
  public function getSlowBitmapUploadCount()
  {
    return $this->slowBitmapUploadCount;
  }
  /**
   * Total "slow draw" events.
   *
   * @param string $slowDrawCount
   */
  public function setSlowDrawCount($slowDrawCount)
  {
    $this->slowDrawCount = $slowDrawCount;
  }
  /**
   * @return string
   */
  public function getSlowDrawCount()
  {
    return $this->slowDrawCount;
  }
  /**
   * Total "slow UI thread" events.
   *
   * @param string $slowUiThreadCount
   */
  public function setSlowUiThreadCount($slowUiThreadCount)
  {
    $this->slowUiThreadCount = $slowUiThreadCount;
  }
  /**
   * @return string
   */
  public function getSlowUiThreadCount()
  {
    return $this->slowUiThreadCount;
  }
  /**
   * Total frames rendered by package.
   *
   * @param string $totalFrames
   */
  public function setTotalFrames($totalFrames)
  {
    $this->totalFrames = $totalFrames;
  }
  /**
   * @return string
   */
  public function getTotalFrames()
  {
    return $this->totalFrames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GraphicsStats::class, 'Google_Service_ToolResults_GraphicsStats');
