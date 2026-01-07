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

class BasicYarnAutoscalingConfig extends \Google\Model
{
  /**
   * Required. Timeout for YARN graceful decommissioning of Node Managers.
   * Specifies the duration to wait for jobs to complete before forcefully
   * removing workers (and potentially interrupting jobs). Only applicable to
   * downscaling operations.Bounds: 0s, 1d.
   *
   * @var string
   */
  public $gracefulDecommissionTimeout;
  /**
   * Required. Fraction of average YARN pending memory in the last cooldown
   * period for which to remove workers. A scale-down factor of 1 will result in
   * scaling down so that there is no available memory remaining after the
   * update (more aggressive scaling). A scale-down factor of 0 disables
   * removing workers, which can be beneficial for autoscaling a single job. See
   * How autoscaling works
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/autoscaling#how_autoscaling_works) for more information.Bounds:
   * 0.0, 1.0.
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
   * Required. Fraction of average YARN pending memory in the last cooldown
   * period for which to add workers. A scale-up factor of 1.0 will result in
   * scaling up so that there is no pending memory remaining after the update
   * (more aggressive scaling). A scale-up factor closer to 0 will result in a
   * smaller magnitude of scaling up (less aggressive scaling). See How
   * autoscaling works
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/autoscaling#how_autoscaling_works) for more information.Bounds:
   * 0.0, 1.0.
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
   * Required. Timeout for YARN graceful decommissioning of Node Managers.
   * Specifies the duration to wait for jobs to complete before forcefully
   * removing workers (and potentially interrupting jobs). Only applicable to
   * downscaling operations.Bounds: 0s, 1d.
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
class_alias(BasicYarnAutoscalingConfig::class, 'Google_Service_Dataproc_BasicYarnAutoscalingConfig');
