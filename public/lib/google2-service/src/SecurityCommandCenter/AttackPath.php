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

namespace Google\Service\SecurityCommandCenter;

class AttackPath extends \Google\Collection
{
  protected $collection_key = 'pathNodes';
  protected $edgesType = AttackPathEdge::class;
  protected $edgesDataType = 'array';
  /**
   * The attack path name, for example,
   * `organizations/12/simulation/34/valuedResources/56/attackPaths/78`
   *
   * @var string
   */
  public $name;
  protected $pathNodesType = AttackPathNode::class;
  protected $pathNodesDataType = 'array';

  /**
   * A list of the edges between nodes in this attack path.
   *
   * @param AttackPathEdge[] $edges
   */
  public function setEdges($edges)
  {
    $this->edges = $edges;
  }
  /**
   * @return AttackPathEdge[]
   */
  public function getEdges()
  {
    return $this->edges;
  }
  /**
   * The attack path name, for example,
   * `organizations/12/simulation/34/valuedResources/56/attackPaths/78`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * A list of nodes that exist in this attack path.
   *
   * @param AttackPathNode[] $pathNodes
   */
  public function setPathNodes($pathNodes)
  {
    $this->pathNodes = $pathNodes;
  }
  /**
   * @return AttackPathNode[]
   */
  public function getPathNodes()
  {
    return $this->pathNodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttackPath::class, 'Google_Service_SecurityCommandCenter_AttackPath');
