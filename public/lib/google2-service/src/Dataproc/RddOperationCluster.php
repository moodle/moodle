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

class RddOperationCluster extends \Google\Collection
{
  protected $collection_key = 'childNodes';
  protected $childClustersType = RddOperationCluster::class;
  protected $childClustersDataType = 'array';
  protected $childNodesType = RddOperationNode::class;
  protected $childNodesDataType = 'array';
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $rddClusterId;

  /**
   * @param RddOperationCluster[] $childClusters
   */
  public function setChildClusters($childClusters)
  {
    $this->childClusters = $childClusters;
  }
  /**
   * @return RddOperationCluster[]
   */
  public function getChildClusters()
  {
    return $this->childClusters;
  }
  /**
   * @param RddOperationNode[] $childNodes
   */
  public function setChildNodes($childNodes)
  {
    $this->childNodes = $childNodes;
  }
  /**
   * @return RddOperationNode[]
   */
  public function getChildNodes()
  {
    return $this->childNodes;
  }
  /**
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
   * @param string $rddClusterId
   */
  public function setRddClusterId($rddClusterId)
  {
    $this->rddClusterId = $rddClusterId;
  }
  /**
   * @return string
   */
  public function getRddClusterId()
  {
    return $this->rddClusterId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RddOperationCluster::class, 'Google_Service_Dataproc_RddOperationCluster');
