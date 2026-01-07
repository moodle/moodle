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

namespace Google\Service\Container;

class EvictionMinimumReclaim extends \Google\Model
{
  /**
   * Optional. Minimum reclaim for eviction due to imagefs available signal.
   * Only take percentage value for now. Sample format: "10%". Must be <=10%.
   * See https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @var string
   */
  public $imagefsAvailable;
  /**
   * Optional. Minimum reclaim for eviction due to imagefs inodes free signal.
   * Only take percentage value for now. Sample format: "10%". Must be <=10%.
   * See https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @var string
   */
  public $imagefsInodesFree;
  /**
   * Optional. Minimum reclaim for eviction due to memory available signal. Only
   * take percentage value for now. Sample format: "10%". Must be <=10%. See
   * https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @var string
   */
  public $memoryAvailable;
  /**
   * Optional. Minimum reclaim for eviction due to nodefs available signal. Only
   * take percentage value for now. Sample format: "10%". Must be <=10%. See
   * https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @var string
   */
  public $nodefsAvailable;
  /**
   * Optional. Minimum reclaim for eviction due to nodefs inodes free signal.
   * Only take percentage value for now. Sample format: "10%". Must be <=10%.
   * See https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @var string
   */
  public $nodefsInodesFree;
  /**
   * Optional. Minimum reclaim for eviction due to pid available signal. Only
   * take percentage value for now. Sample format: "10%". Must be <=10%. See
   * https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @var string
   */
  public $pidAvailable;

  /**
   * Optional. Minimum reclaim for eviction due to imagefs available signal.
   * Only take percentage value for now. Sample format: "10%". Must be <=10%.
   * See https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @param string $imagefsAvailable
   */
  public function setImagefsAvailable($imagefsAvailable)
  {
    $this->imagefsAvailable = $imagefsAvailable;
  }
  /**
   * @return string
   */
  public function getImagefsAvailable()
  {
    return $this->imagefsAvailable;
  }
  /**
   * Optional. Minimum reclaim for eviction due to imagefs inodes free signal.
   * Only take percentage value for now. Sample format: "10%". Must be <=10%.
   * See https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @param string $imagefsInodesFree
   */
  public function setImagefsInodesFree($imagefsInodesFree)
  {
    $this->imagefsInodesFree = $imagefsInodesFree;
  }
  /**
   * @return string
   */
  public function getImagefsInodesFree()
  {
    return $this->imagefsInodesFree;
  }
  /**
   * Optional. Minimum reclaim for eviction due to memory available signal. Only
   * take percentage value for now. Sample format: "10%". Must be <=10%. See
   * https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @param string $memoryAvailable
   */
  public function setMemoryAvailable($memoryAvailable)
  {
    $this->memoryAvailable = $memoryAvailable;
  }
  /**
   * @return string
   */
  public function getMemoryAvailable()
  {
    return $this->memoryAvailable;
  }
  /**
   * Optional. Minimum reclaim for eviction due to nodefs available signal. Only
   * take percentage value for now. Sample format: "10%". Must be <=10%. See
   * https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @param string $nodefsAvailable
   */
  public function setNodefsAvailable($nodefsAvailable)
  {
    $this->nodefsAvailable = $nodefsAvailable;
  }
  /**
   * @return string
   */
  public function getNodefsAvailable()
  {
    return $this->nodefsAvailable;
  }
  /**
   * Optional. Minimum reclaim for eviction due to nodefs inodes free signal.
   * Only take percentage value for now. Sample format: "10%". Must be <=10%.
   * See https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @param string $nodefsInodesFree
   */
  public function setNodefsInodesFree($nodefsInodesFree)
  {
    $this->nodefsInodesFree = $nodefsInodesFree;
  }
  /**
   * @return string
   */
  public function getNodefsInodesFree()
  {
    return $this->nodefsInodesFree;
  }
  /**
   * Optional. Minimum reclaim for eviction due to pid available signal. Only
   * take percentage value for now. Sample format: "10%". Must be <=10%. See
   * https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @param string $pidAvailable
   */
  public function setPidAvailable($pidAvailable)
  {
    $this->pidAvailable = $pidAvailable;
  }
  /**
   * @return string
   */
  public function getPidAvailable()
  {
    return $this->pidAvailable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EvictionMinimumReclaim::class, 'Google_Service_Container_EvictionMinimumReclaim');
