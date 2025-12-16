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

class SparkStandaloneAutoscalingConfig extends \Google\Model
{
  /**
   * Required. Timeout for Spark graceful decommissioning of spark workers.
   * Specifies the duration to wait for spark worker to complete spark
   * decommissioning tasks before forcefully removing workers. Only applicable
   * to downscaling operations.Bounds: 0s, 1d.
   *
   * @var string
   */
  public $gracefulDecommissionTimeout;
  /**
   * Optional. Remove only idle workers when scaling down cluster
   *
   * @var bool
   */
  public $removeOnlyIdleWorkers;
  /**
   * Required. Fraction of required executors to remove from Spark Serverless
   * clusters. A scale-down factor of 1.0 will result in scaling down so that
   * there are no more executors for the Spark Job.(more aggressive scaling). A
   * scale-down factor closer to 0 will result in a smaller magnitude of scaling
   * donw (less aggressive scaling).Bounds: 0.0, 1.0.
   *
   * @var 
   */
  public $scaleDownFactor;
  /**
   * Optional. Minimum scale-down threshold as a fraction of total cluster size
   * before scaling occurs. For example, in a 20-worker cluster, a threshold of
   * 0.1 means the autoscaler must recommend at least a 2 worker scale-down for
   * the cluster to scale. A threshold of 0 means the autoscaler will scale down
   * on any recommended change.Bounds: 0.0, 1.0. Default: 0.0.
   *
   * @var 
   */
  public $scaleDownMinWorkerFraction;
  /**
   * Required. Fraction of required workers to add to Spark Standalone clusters.
   * A scale-up factor of 1.0 will result in scaling up so that there are no
   * more required workers for the Spark Job (more aggressive scaling). A scale-
   * up factor closer to 0 will result in a smaller magnitude of scaling up
   * (less aggressive scaling).Bounds: 0.0, 1.0.
   *
   * @var 
   */
  public $scaleUpFactor;
  /**
   * Optional. Minimum scale-up threshold as a fraction of total cluster size
   * before scaling occurs. For example, in a 20-worker cluster, a threshold of
   * 0.1 means the autoscaler must recommend at least a 2-worker scale-up for
   * the cluster to scale. A threshold of 0 means the autoscaler will scale up
   * on any recommended change.Bounds: 0.0, 1.0. Default: 0.0.
   *
   * @var 
   */
  public $scaleUpMinWorkerFraction;

  /**
   * Required. Timeout for Spark graceful decommissioning of spark workers.
   * Specifies the duration to wait for spark worker to complete spark
   * decommissioning tasks before forcefully removing workers. Only applicable
   * to downscaling operations.Bounds: 0s, 1d.
   *
   * @param string $gracefulDecommissionTimeout
   */
  public function setGracefulDecommissionTimeout($gracefulDecommissionTimeout)
  {
    $this->gracefulDecommissionTimeout = $gracefulDecommissionTimeout;
  }
  /**
   * @return string
   */
  public function getGracefulDecommissionTimeout()
  {
    return $this->gracefulDecommissionTimeout;
  }
  /**
   * Optional. Remove only idle workers when scaling down cluster
   *
   * @param bool $removeOnlyIdleWorkers
   */
  public function setRemoveOnlyIdleWorkers($removeOnlyIdleWorkers)
  {
    $this->removeOnlyIdleWorkers = $removeOnlyIdleWorkers;
  }
  /**
   * @return bool
   */
  public function getRemoveOnlyIdleWorkers()
  {
    return $this->removeOnlyIdleWorkers;
  }
  public function setScaleDownFactor($scaleDownFactor)
  {
    $this->scaleDownFactor = $scaleDownFactor;
  }
  public function getScaleDownFactor()
  {
    return $this->scaleDownFactor;
  }
  public function setScaleDownMinWorkerFraction($scaleDownMinWorkerFraction)
  {
    $this->scaleDownMinWorkerFraction = $scaleDownMinWorkerFraction;
  }
  public function getScaleDownMinWorkerFraction()
  {
    return $this->scaleDownMinWorkerFraction;
  }
  public function setScaleUpFactor($scaleUpFactor)
  {
    $this->scaleUpFactor = $scaleUpFactor;
  }
  public function getScaleUpFactor()
  {
    return $this->scaleUpFactor;
  }
  public function setScaleUpMinWorkerFraction($scaleUpMinWorkerFraction)
  {
    $this->scaleUpMinWorkerFraction = $scaleUpMinWorkerFraction;
  }
  public function getScaleUpMinWorkerFraction()
  {
    return $this->scaleUpMinWorkerFraction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkStandaloneAutoscalingConfig::class, 'Google_Service_Dataproc_SparkStandaloneAutoscalingConfig');
