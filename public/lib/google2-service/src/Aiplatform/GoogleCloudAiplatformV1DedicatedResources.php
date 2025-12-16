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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1DedicatedResources extends \Google\Collection
{
  protected $collection_key = 'autoscalingMetricSpecs';
  protected $autoscalingMetricSpecsType = GoogleCloudAiplatformV1AutoscalingMetricSpec::class;
  protected $autoscalingMetricSpecsDataType = 'array';
  protected $machineSpecType = GoogleCloudAiplatformV1MachineSpec::class;
  protected $machineSpecDataType = '';
  /**
   * Immutable. The maximum number of replicas that may be deployed on when the
   * traffic against it increases. If the requested value is too large, the
   * deployment will error, but if deployment succeeds then the ability to scale
   * to that many replicas is guaranteed (barring service outages). If traffic
   * increases beyond what its replicas at maximum may handle, a portion of the
   * traffic will be dropped. If this value is not provided, will use
   * min_replica_count as the default value. The value of this field impacts the
   * charge against Vertex CPU and GPU quotas. Specifically, you will be charged
   * for (max_replica_count * number of cores in the selected machine type) and
   * (max_replica_count * number of GPUs per replica in the selected machine
   * type).
   *
   * @var int
   */
  public $maxReplicaCount;
  /**
   * Required. Immutable. The minimum number of machine replicas that will be
   * always deployed on. This value must be greater than or equal to 1. If
   * traffic increases, it may dynamically be deployed onto more replicas, and
   * as traffic decreases, some of these extra replicas may be freed.
   *
   * @var int
   */
  public $minReplicaCount;
  /**
   * Optional. Number of required available replicas for the deployment to
   * succeed. This field is only needed when partial deployment/mutation is
   * desired. If set, the deploy/mutate operation will succeed once
   * available_replica_count reaches required_replica_count, and the rest of the
   * replicas will be retried. If not set, the default required_replica_count
   * will be min_replica_count.
   *
   * @var int
   */
  public $requiredReplicaCount;
  /**
   * Optional. If true, schedule the deployment workload on [spot
   * VMs](https://cloud.google.com/kubernetes-engine/docs/concepts/spot-vms).
   *
   * @var bool
   */
  public $spot;

  /**
   * Immutable. The metric specifications that overrides a resource utilization
   * metric (CPU utilization, accelerator's duty cycle, and so on) target value
   * (default to 60 if not set). At most one entry is allowed per metric. If
   * machine_spec.accelerator_count is above 0, the autoscaling will be based on
   * both CPU utilization and accelerator's duty cycle metrics and scale up when
   * either metrics exceeds its target value while scale down if both metrics
   * are under their target value. The default target value is 60 for both
   * metrics. If machine_spec.accelerator_count is 0, the autoscaling will be
   * based on CPU utilization metric only with default target value 60 if not
   * explicitly set. For example, in the case of Online Prediction, if you want
   * to override target CPU utilization to 80, you should set
   * autoscaling_metric_specs.metric_name to
   * `aiplatform.googleapis.com/prediction/online/cpu/utilization` and
   * autoscaling_metric_specs.target to `80`.
   *
   * @param GoogleCloudAiplatformV1AutoscalingMetricSpec[] $autoscalingMetricSpecs
   */
  public function setAutoscalingMetricSpecs($autoscalingMetricSpecs)
  {
    $this->autoscalingMetricSpecs = $autoscalingMetricSpecs;
  }
  /**
   * @return GoogleCloudAiplatformV1AutoscalingMetricSpec[]
   */
  public function getAutoscalingMetricSpecs()
  {
    return $this->autoscalingMetricSpecs;
  }
  /**
   * Required. Immutable. The specification of a single machine being used.
   *
   * @param GoogleCloudAiplatformV1MachineSpec $machineSpec
   */
  public function setMachineSpec(GoogleCloudAiplatformV1MachineSpec $machineSpec)
  {
    $this->machineSpec = $machineSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1MachineSpec
   */
  public function getMachineSpec()
  {
    return $this->machineSpec;
  }
  /**
   * Immutable. The maximum number of replicas that may be deployed on when the
   * traffic against it increases. If the requested value is too large, the
   * deployment will error, but if deployment succeeds then the ability to scale
   * to that many replicas is guaranteed (barring service outages). If traffic
   * increases beyond what its replicas at maximum may handle, a portion of the
   * traffic will be dropped. If this value is not provided, will use
   * min_replica_count as the default value. The value of this field impacts the
   * charge against Vertex CPU and GPU quotas. Specifically, you will be charged
   * for (max_replica_count * number of cores in the selected machine type) and
   * (max_replica_count * number of GPUs per replica in the selected machine
   * type).
   *
   * @param int $maxReplicaCount
   */
  public function setMaxReplicaCount($maxReplicaCount)
  {
    $this->maxReplicaCount = $maxReplicaCount;
  }
  /**
   * @return int
   */
  public function getMaxReplicaCount()
  {
    return $this->maxReplicaCount;
  }
  /**
   * Required. Immutable. The minimum number of machine replicas that will be
   * always deployed on. This value must be greater than or equal to 1. If
   * traffic increases, it may dynamically be deployed onto more replicas, and
   * as traffic decreases, some of these extra replicas may be freed.
   *
   * @param int $minReplicaCount
   */
  public function setMinReplicaCount($minReplicaCount)
  {
    $this->minReplicaCount = $minReplicaCount;
  }
  /**
   * @return int
   */
  public function getMinReplicaCount()
  {
    return $this->minReplicaCount;
  }
  /**
   * Optional. Number of required available replicas for the deployment to
   * succeed. This field is only needed when partial deployment/mutation is
   * desired. If set, the deploy/mutate operation will succeed once
   * available_replica_count reaches required_replica_count, and the rest of the
   * replicas will be retried. If not set, the default required_replica_count
   * will be min_replica_count.
   *
   * @param int $requiredReplicaCount
   */
  public function setRequiredReplicaCount($requiredReplicaCount)
  {
    $this->requiredReplicaCount = $requiredReplicaCount;
  }
  /**
   * @return int
   */
  public function getRequiredReplicaCount()
  {
    return $this->requiredReplicaCount;
  }
  /**
   * Optional. If true, schedule the deployment workload on [spot
   * VMs](https://cloud.google.com/kubernetes-engine/docs/concepts/spot-vms).
   *
   * @param bool $spot
   */
  public function setSpot($spot)
  {
    $this->spot = $spot;
  }
  /**
   * @return bool
   */
  public function getSpot()
  {
    return $this->spot;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DedicatedResources::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DedicatedResources');
