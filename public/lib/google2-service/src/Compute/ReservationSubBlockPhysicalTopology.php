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

class ReservationSubBlockPhysicalTopology extends \Google\Model
{
  /**
   * The hash of the capacity block within the cluster.
   *
   * @var string
   */
  public $block;
  /**
   * The cluster name of the reservation subBlock.
   *
   * @var string
   */
  public $cluster;
  /**
   * The hash of the capacity sub-block within the capacity block.
   *
   * @var string
   */
  public $subBlock;

  /**
   * The hash of the capacity block within the cluster.
   *
   * @param string $block
   */
  public function setBlock($block)
  {
    $this->block = $block;
  }
  /**
   * @return string
   */
  public function getBlock()
  {
    return $this->block;
  }
  /**
   * The cluster name of the reservation subBlock.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * The hash of the capacity sub-block within the capacity block.
   *
   * @param string $subBlock
   */
  public function setSubBlock($subBlock)
  {
    $this->subBlock = $subBlock;
  }
  /**
   * @return string
   */
  public function getSubBlock()
  {
    return $this->subBlock;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationSubBlockPhysicalTopology::class, 'Google_Service_Compute_ReservationSubBlockPhysicalTopology');
