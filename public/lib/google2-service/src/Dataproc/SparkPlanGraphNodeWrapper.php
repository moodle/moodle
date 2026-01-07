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

class SparkPlanGraphNodeWrapper extends \Google\Model
{
  protected $clusterType = SparkPlanGraphCluster::class;
  protected $clusterDataType = '';
  protected $nodeType = SparkPlanGraphNode::class;
  protected $nodeDataType = '';

  /**
   * @param SparkPlanGraphCluster $cluster
   */
  public function setCluster(SparkPlanGraphCluster $cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return SparkPlanGraphCluster
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * @param SparkPlanGraphNode $node
   */
  public function setNode(SparkPlanGraphNode $node)
  {
    $this->node = $node;
  }
  /**
   * @return SparkPlanGraphNode
   */
  public function getNode()
  {
    return $this->node;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkPlanGraphNodeWrapper::class, 'Google_Service_Dataproc_SparkPlanGraphNodeWrapper');
