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

class ResourceStatusPhysicalHostTopology extends \Google\Model
{
  /**
   * [Output Only] The ID of the block in which the running instance is located.
   * Instances within the same block experience low network latency.
   *
   * @var string
   */
  public $block;
  /**
   * [Output Only] The global name of the Compute Engine cluster where the
   * running instance is located.
   *
   * @var string
   */
  public $cluster;
  /**
   * [Output Only] The ID of the host on which the running instance is located.
   * Instances on the same host experience the lowest possible network latency.
   *
   * @var string
   */
  public $host;
  /**
   * [Output Only] The ID of the sub-block in which the running instance is
   * located. Instances in the same sub-block experience lower network latency
   * than instances in the same block.
   *
   * @var string
   */
  public $subblock;

  /**
   * [Output Only] The ID of the block in which the running instance is located.
   * Instances within the same block experience low network latency.
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
   * [Output Only] The global name of the Compute Engine cluster where the
   * running instance is located.
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
   * [Output Only] The ID of the host on which the running instance is located.
   * Instances on the same host experience the lowest possible network latency.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * [Output Only] The ID of the sub-block in which the running instance is
   * located. Instances in the same sub-block experience lower network latency
   * than instances in the same block.
   *
   * @param string $subblock
   */
  public function setSubblock($subblock)
  {
    $this->subblock = $subblock;
  }
  /**
   * @return string
   */
  public function getSubblock()
  {
    return $this->subblock;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceStatusPhysicalHostTopology::class, 'Google_Service_Compute_ResourceStatusPhysicalHostTopology');
