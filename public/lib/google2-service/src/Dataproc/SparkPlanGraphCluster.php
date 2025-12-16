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

class SparkPlanGraphCluster extends \Google\Collection
{
  protected $collection_key = 'nodes';
  /**
   * @var string
   */
  public $desc;
  protected $metricsType = SqlPlanMetric::class;
  protected $metricsDataType = 'array';
  /**
   * @var string
   */
  public $name;
  protected $nodesType = SparkPlanGraphNodeWrapper::class;
  protected $nodesDataType = 'array';
  /**
   * @var string
   */
  public $sparkPlanGraphClusterId;

  /**
   * @param string $desc
   */
  public function setDesc($desc)
  {
    $this->desc = $desc;
  }
  /**
   * @return string
   */
  public function getDesc()
  {
    return $this->desc;
  }
  /**
   * @param SqlPlanMetric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return SqlPlanMetric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
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
  /**
   * @param string $sparkPlanGraphClusterId
   */
  public function setSparkPlanGraphClusterId($sparkPlanGraphClusterId)
  {
    $this->sparkPlanGraphClusterId = $sparkPlanGraphClusterId;
  }
  /**
   * @return string
   */
  public function getSparkPlanGraphClusterId()
  {
    return $this->sparkPlanGraphClusterId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkPlanGraphCluster::class, 'Google_Service_Dataproc_SparkPlanGraphCluster');
