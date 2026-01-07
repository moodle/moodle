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

class SoleTenantConfig extends \Google\Collection
{
  protected $collection_key = 'nodeAffinities';
  /**
   * Optional. The minimum number of virtual CPUs this instance will consume
   * when running on a sole-tenant node. This field can only be set if the node
   * pool is created in a shared sole-tenant node group.
   *
   * @var int
   */
  public $minNodeCpus;
  protected $nodeAffinitiesType = NodeAffinity::class;
  protected $nodeAffinitiesDataType = 'array';

  /**
   * Optional. The minimum number of virtual CPUs this instance will consume
   * when running on a sole-tenant node. This field can only be set if the node
   * pool is created in a shared sole-tenant node group.
   *
   * @param int $minNodeCpus
   */
  public function setMinNodeCpus($minNodeCpus)
  {
    $this->minNodeCpus = $minNodeCpus;
  }
  /**
   * @return int
   */
  public function getMinNodeCpus()
  {
    return $this->minNodeCpus;
  }
  /**
   * NodeAffinities used to match to a shared sole tenant node group.
   *
   * @param NodeAffinity[] $nodeAffinities
   */
  public function setNodeAffinities($nodeAffinities)
  {
    $this->nodeAffinities = $nodeAffinities;
  }
  /**
   * @return NodeAffinity[]
   */
  public function getNodeAffinities()
  {
    return $this->nodeAffinities;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SoleTenantConfig::class, 'Google_Service_Container_SoleTenantConfig');
