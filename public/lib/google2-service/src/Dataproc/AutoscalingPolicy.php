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

class AutoscalingPolicy extends \Google\Model
{
  /**
   * Not set.
   */
  public const CLUSTER_TYPE_CLUSTER_TYPE_UNSPECIFIED = 'CLUSTER_TYPE_UNSPECIFIED';
  /**
   * Standard dataproc cluster with a minimum of two primary workers.
   */
  public const CLUSTER_TYPE_STANDARD = 'STANDARD';
  /**
   * Clusters that can use only secondary workers and be scaled down to zero
   * secondary worker nodes.
   */
  public const CLUSTER_TYPE_ZERO_SCALE = 'ZERO_SCALE';
  protected $basicAlgorithmType = BasicAutoscalingAlgorithm::class;
  protected $basicAlgorithmDataType = '';
  /**
   * Optional. The type of the clusters for which this autoscaling policy is to
   * be configured.
   *
   * @var string
   */
  public $clusterType;
  /**
   * Required. The policy id.The id must contain only letters (a-z, A-Z),
   * numbers (0-9), underscores (_), and hyphens (-). Cannot begin or end with
   * underscore or hyphen. Must consist of between 3 and 50 characters.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. The labels to associate with this autoscaling policy. Label keys
   * must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values may be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with an autoscaling policy.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The "resource name" of the autoscaling policy, as described in
   * https://cloud.google.com/apis/design/resource_names. For
   * projects.regions.autoscalingPolicies, the resource name of the policy has
   * the following format:
   * projects/{project_id}/regions/{region}/autoscalingPolicies/{policy_id} For
   * projects.locations.autoscalingPolicies, the resource name of the policy has
   * the following format:
   * projects/{project_id}/locations/{location}/autoscalingPolicies/{policy_id}
   *
   * @var string
   */
  public $name;
  protected $secondaryWorkerConfigType = InstanceGroupAutoscalingPolicyConfig::class;
  protected $secondaryWorkerConfigDataType = '';
  protected $workerConfigType = InstanceGroupAutoscalingPolicyConfig::class;
  protected $workerConfigDataType = '';

  /**
   * @param BasicAutoscalingAlgorithm $basicAlgorithm
   */
  public function setBasicAlgorithm(BasicAutoscalingAlgorithm $basicAlgorithm)
  {
    $this->basicAlgorithm = $basicAlgorithm;
  }
  /**
   * @return BasicAutoscalingAlgorithm
   */
  public function getBasicAlgorithm()
  {
    return $this->basicAlgorithm;
  }
  /**
   * Optional. The type of the clusters for which this autoscaling policy is to
   * be configured.
   *
   * Accepted values: CLUSTER_TYPE_UNSPECIFIED, STANDARD, ZERO_SCALE
   *
   * @param self::CLUSTER_TYPE_* $clusterType
   */
  public function setClusterType($clusterType)
  {
    $this->clusterType = $clusterType;
  }
  /**
   * @return self::CLUSTER_TYPE_*
   */
  public function getClusterType()
  {
    return $this->clusterType;
  }
  /**
   * Required. The policy id.The id must contain only letters (a-z, A-Z),
   * numbers (0-9), underscores (_), and hyphens (-). Cannot begin or end with
   * underscore or hyphen. Must consist of between 3 and 50 characters.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Optional. The labels to associate with this autoscaling policy. Label keys
   * must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values may be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with an autoscaling policy.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The "resource name" of the autoscaling policy, as described in
   * https://cloud.google.com/apis/design/resource_names. For
   * projects.regions.autoscalingPolicies, the resource name of the policy has
   * the following format:
   * projects/{project_id}/regions/{region}/autoscalingPolicies/{policy_id} For
   * projects.locations.autoscalingPolicies, the resource name of the policy has
   * the following format:
   * projects/{project_id}/locations/{location}/autoscalingPolicies/{policy_id}
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
   * Optional. Describes how the autoscaler will operate for secondary workers.
   *
   * @param InstanceGroupAutoscalingPolicyConfig $secondaryWorkerConfig
   */
  public function setSecondaryWorkerConfig(InstanceGroupAutoscalingPolicyConfig $secondaryWorkerConfig)
  {
    $this->secondaryWorkerConfig = $secondaryWorkerConfig;
  }
  /**
   * @return InstanceGroupAutoscalingPolicyConfig
   */
  public function getSecondaryWorkerConfig()
  {
    return $this->secondaryWorkerConfig;
  }
  /**
   * Required. Describes how the autoscaler will operate for primary workers.
   *
   * @param InstanceGroupAutoscalingPolicyConfig $workerConfig
   */
  public function setWorkerConfig(InstanceGroupAutoscalingPolicyConfig $workerConfig)
  {
    $this->workerConfig = $workerConfig;
  }
  /**
   * @return InstanceGroupAutoscalingPolicyConfig
   */
  public function getWorkerConfig()
  {
    return $this->workerConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingPolicy::class, 'Google_Service_Dataproc_AutoscalingPolicy');
