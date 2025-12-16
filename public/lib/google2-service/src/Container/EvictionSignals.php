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

class EvictionSignals extends \Google\Model
{
  /**
   * Optional. Amount of storage available on filesystem that container runtime
   * uses for storing images layers. If the container filesystem and image
   * filesystem are not separate, then imagefs can store both image layers and
   * writeable layers. Defines the amount of "imagefs.available" signal in
   * kubelet. Default is unset, if not specified in the kubelet config. It
   * takses percentage value for now. Sample format: "30%". Must be >= 15% and
   * <= 50%. See https://kubernetes.io/docs/concepts/scheduling-eviction/node-
   * pressure-eviction/#eviction-signals
   *
   * @var string
   */
  public $imagefsAvailable;
  /**
   * Optional. Amount of inodes available on filesystem that container runtime
   * uses for storing images layers. Defines the amount of "imagefs.inodesFree"
   * signal in kubelet. Default is unset, if not specified in the kubelet
   * config. Linux only. It takses percentage value for now. Sample format:
   * "30%". Must be >= 5% and <= 50%. See
   * https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @var string
   */
  public $imagefsInodesFree;
  /**
   * Optional. Memory available (i.e. capacity - workingSet), in bytes. Defines
   * the amount of "memory.available" signal in kubelet. Default is unset, if
   * not specified in the kubelet config. Format: positive number + unit, e.g.
   * 100Ki, 10Mi, 5Gi. Valid units are Ki, Mi, Gi. Must be >= 100Mi and <= 50%
   * of the node's memory. See https://kubernetes.io/docs/concepts/scheduling-
   * eviction/node-pressure-eviction/#eviction-signals
   *
   * @var string
   */
  public $memoryAvailable;
  /**
   * Optional. Amount of storage available on filesystem that kubelet uses for
   * volumes, daemon logs, etc. Defines the amount of "nodefs.available" signal
   * in kubelet. Default is unset, if not specified in the kubelet config. It
   * takses percentage value for now. Sample format: "30%". Must be >= 10% and
   * <= 50%. See https://kubernetes.io/docs/concepts/scheduling-eviction/node-
   * pressure-eviction/#eviction-signals
   *
   * @var string
   */
  public $nodefsAvailable;
  /**
   * Optional. Amount of inodes available on filesystem that kubelet uses for
   * volumes, daemon logs, etc. Defines the amount of "nodefs.inodesFree" signal
   * in kubelet. Default is unset, if not specified in the kubelet config. Linux
   * only. It takses percentage value for now. Sample format: "30%". Must be >=
   * 5% and <= 50%. See https://kubernetes.io/docs/concepts/scheduling-
   * eviction/node-pressure-eviction/#eviction-signals
   *
   * @var string
   */
  public $nodefsInodesFree;
  /**
   * Optional. Amount of PID available for pod allocation. Defines the amount of
   * "pid.available" signal in kubelet. Default is unset, if not specified in
   * the kubelet config. It takses percentage value for now. Sample format:
   * "30%". Must be >= 10% and <= 50%. See
   * https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
   * eviction/#eviction-signals
   *
   * @var string
   */
  public $pidAvailable;

  /**
   * Optional. Amount of storage available on filesystem that container runtime
   * uses for storing images layers. If the container filesystem and image
   * filesystem are not separate, then imagefs can store both image layers and
   * writeable layers. Defines the amount of "imagefs.available" signal in
   * kubelet. Default is unset, if not specified in the kubelet config. It
   * takses percentage value for now. Sample format: "30%". Must be >= 15% and
   * <= 50%. See https://kubernetes.io/docs/concepts/scheduling-eviction/node-
   * pressure-eviction/#eviction-signals
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
   * Optional. Amount of inodes available on filesystem that container runtime
   * uses for storing images layers. Defines the amount of "imagefs.inodesFree"
   * signal in kubelet. Default is unset, if not specified in the kubelet
   * config. Linux only. It takses percentage value for now. Sample format:
   * "30%". Must be >= 5% and <= 50%. See
   * https://kubernetes.io/docs/concepts/scheduling-eviction/node-pressure-
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
   * Optional. Memory available (i.e. capacity - workingSet), in bytes. Defines
   * the amount of "memory.available" signal in kubelet. Default is unset, if
   * not specified in the kubelet config. Format: positive number + unit, e.g.
   * 100Ki, 10Mi, 5Gi. Valid units are Ki, Mi, Gi. Must be >= 100Mi and <= 50%
   * of the node's memory. See https://kubernetes.io/docs/concepts/scheduling-
   * eviction/node-pressure-eviction/#eviction-signals
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
   * Optional. Amount of storage available on filesystem that kubelet uses for
   * volumes, daemon logs, etc. Defines the amount of "nodefs.available" signal
   * in kubelet. Default is unset, if not specified in the kubelet config. It
   * takses percentage value for now. Sample format: "30%". Must be >= 10% and
   * <= 50%. See https://kubernetes.io/docs/concepts/scheduling-eviction/node-
   * pressure-eviction/#eviction-signals
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
   * Optional. Amount of inodes available on filesystem that kubelet uses for
   * volumes, daemon logs, etc. Defines the amount of "nodefs.inodesFree" signal
   * in kubelet. Default is unset, if not specified in the kubelet config. Linux
   * only. It takses percentage value for now. Sample format: "30%". Must be >=
   * 5% and <= 50%. See https://kubernetes.io/docs/concepts/scheduling-
   * eviction/node-pressure-eviction/#eviction-signals
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
   * Optional. Amount of PID available for pod allocation. Defines the amount of
   * "pid.available" signal in kubelet. Default is unset, if not specified in
   * the kubelet config. It takses percentage value for now. Sample format:
   * "30%". Must be >= 10% and <= 50%. See
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
class_alias(EvictionSignals::class, 'Google_Service_Container_EvictionSignals');
