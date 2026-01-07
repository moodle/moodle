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

namespace Google\Service\Dataproc;

class GkeNodePoolTarget extends \Google\Collection
{
  protected $collection_key = 'roles';
  /**
   * Required. The target GKE node pool. Format: 'projects/{project}/locations/{
   * location}/clusters/{cluster}/nodePools/{node_pool}'
   *
   * @var string
   */
  public $nodePool;
  protected $nodePoolConfigType = GkeNodePoolConfig::class;
  protected $nodePoolConfigDataType = '';
  /**
   * Required. The roles associated with the GKE node pool.
   *
   * @var string[]
   */
  public $roles;

  /**
   * Required. The target GKE node pool. Format: 'projects/{project}/locations/{
   * location}/clusters/{cluster}/nodePools/{node_pool}'
   *
   * @param string $nodePool
   */
  public function setNodePool($nodePool)
  {
    $this->nodePool = $nodePool;
  }
  /**
   * @return string
   */
  public function getNodePool()
  {
    return $this->nodePool;
  }
  /**
   * Input only. The configuration for the GKE node pool.If specified, Dataproc
   * attempts to create a node pool with the specified shape. If one with the
   * same name already exists, it is verified against all specified fields. If a
   * field differs, the virtual cluster creation will fail.If omitted, any node
   * pool with the specified name is used. If a node pool with the specified
   * name does not exist, Dataproc create a node pool with default values.This
   * is an input only field. It will not be returned by the API.
   *
   * @param GkeNodePoolConfig $nodePoolConfig
   */
  public function setNodePoolConfig(GkeNodePoolConfig $nodePoolConfig)
  {
    $this->nodePoolConfig = $nodePoolConfig;
  }
  /**
   * @return GkeNodePoolConfig
   */
  public function getNodePoolConfig()
  {
    return $this->nodePoolConfig;
  }
  /**
   * Required. The roles associated with the GKE node pool.
   *
   * @param string[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return string[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GkeNodePoolTarget::class, 'Google_Service_Dataproc_GkeNodePoolTarget');
