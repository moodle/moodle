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

class SparkPlanGraph extends \Google\Collection
{
  protected $collection_key = 'nodes';
  protected $edgesType = SparkPlanGraphEdge::class;
  protected $edgesDataType = 'array';
  /**
   * @var string
   */
  public $executionId;
  protected $nodesType = SparkPlanGraphNodeWrapper::class;
  protected $nodesDataType = 'array';

  /**
   * @param SparkPlanGraphEdge[] $edges
   */
  public function setEdges($edges)
  {
    $this->edges = $edges;
  }
  /**
   * @return SparkPlanGraphEdge[]
   */
  public function getEdges()
  {
    return $this->edges;
  }
  /**
   * @param string $executionId
   */
  public function setExecutionId($executionId)
  {
    $this->executionId = $executionId;
  }
  /**
   * @return string
   */
  public function getExecutionId()
  {
    return $this->executionId;
  }
  /**
   * @param SparkPlanGraphNodeWrapper[] $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return SparkPlanGraphNodeWrapper[]
   */
  public function getNodes()
  {
    return $this->nodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkPlanGraph::class, 'Google_Service_Dataproc_SparkPlanGraph');
