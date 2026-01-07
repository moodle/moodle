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

class RddOperationGraph extends \Google\Collection
{
  protected $collection_key = 'outgoingEdges';
  protected $edgesType = RddOperationEdge::class;
  protected $edgesDataType = 'array';
  protected $incomingEdgesType = RddOperationEdge::class;
  protected $incomingEdgesDataType = 'array';
  protected $outgoingEdgesType = RddOperationEdge::class;
  protected $outgoingEdgesDataType = 'array';
  protected $rootClusterType = RddOperationCluster::class;
  protected $rootClusterDataType = '';
  /**
   * @var string
   */
  public $stageId;

  /**
   * @param RddOperationEdge[] $edges
   */
  public function setEdges($edges)
  {
    $this->edges = $edges;
  }
  /**
   * @return RddOperationEdge[]
   */
  public function getEdges()
  {
    return $this->edges;
  }
  /**
   * @param RddOperationEdge[] $incomingEdges
   */
  public function setIncomingEdges($incomingEdges)
  {
    $this->incomingEdges = $incomingEdges;
  }
  /**
   * @return RddOperationEdge[]
   */
  public function getIncomingEdges()
  {
    return $this->incomingEdges;
  }
  /**
   * @param RddOperationEdge[] $outgoingEdges
   */
  public function setOutgoingEdges($outgoingEdges)
  {
    $this->outgoingEdges = $outgoingEdges;
  }
  /**
   * @return RddOperationEdge[]
   */
  public function getOutgoingEdges()
  {
    return $this->outgoingEdges;
  }
  /**
   * @param RddOperationCluster $rootCluster
   */
  public function setRootCluster(RddOperationCluster $rootCluster)
  {
    $this->rootCluster = $rootCluster;
  }
  /**
   * @return RddOperationCluster
   */
  public function getRootCluster()
  {
    return $this->rootCluster;
  }
  /**
   * @param string $stageId
   */
  public function setStageId($stageId)
  {
    $this->stageId = $stageId;
  }
  /**
   * @return string
   */
  public function getStageId()
  {
    return $this->stageId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RddOperationGraph::class, 'Google_Service_Dataproc_RddOperationGraph');
