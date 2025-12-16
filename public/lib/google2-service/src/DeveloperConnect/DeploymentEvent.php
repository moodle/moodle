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

namespace Google\Service\DeveloperConnect;

class DeploymentEvent extends \Google\Collection
{
  /**
   * No state specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The deployment is active in the runtime.
   */
  public const STATE_STATE_ACTIVE = 'STATE_ACTIVE';
  /**
   * The deployment is not in the runtime.
   */
  public const STATE_STATE_INACTIVE = 'STATE_INACTIVE';
  protected $collection_key = 'artifactDeployments';
  protected $artifactDeploymentsType = ArtifactDeployment::class;
  protected $artifactDeploymentsDataType = 'array';
  /**
   * Output only. The create time of the DeploymentEvent.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time at which the DeploymentEvent was deployed. This would
   * be the min of all ArtifactDeployment deploy_times.
   *
   * @var string
   */
  public $deployTime;
  /**
   * Identifier. The name of the DeploymentEvent. This name is provided by DCI.
   * Format: projects/{project}/locations/{location}/insightsConfigs/{insights_c
   * onfig}/deploymentEvents/{uuid}
   *
   * @var string
   */
  public $name;
  protected $runtimeConfigType = RuntimeConfig::class;
  protected $runtimeConfigDataType = '';
  /**
   * Output only. The runtime assigned URI of the DeploymentEvent. For GKE, this
   * is the fully qualified replica set uri. e.g. container.googleapis.com/proje
   * cts/{project}/locations/{location}/clusters/{cluster}/k8s/namespaces/{names
   * pace}/apps/replicasets/{replica-set-id} For Cloud Run, this is the revision
   * name.
   *
   * @var string
   */
  public $runtimeDeploymentUri;
  /**
   * Output only. The state of the DeploymentEvent.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time at which the DeploymentEvent was undeployed, all
   * artifacts are considered undeployed once this time is set. This would be
   * the max of all ArtifactDeployment undeploy_times. If any ArtifactDeployment
   * is still active (i.e. does not have an undeploy_time), this field will be
   * empty.
   *
   * @var string
   */
  public $undeployTime;
  /**
   * Output only. The update time of the DeploymentEvent.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The artifact deployments of the DeploymentEvent. Each artifact
   * deployment contains the artifact uri and the runtime configuration uri. For
   * GKE, this would be all the containers images that are deployed in the pod.
   *
   * @param ArtifactDeployment[] $artifactDeployments
   */
  public function setArtifactDeployments($artifactDeployments)
  {
    $this->artifactDeployments = $artifactDeployments;
  }
  /**
   * @return ArtifactDeployment[]
   */
  public function getArtifactDeployments()
  {
    return $this->artifactDeployments;
  }
  /**
   * Output only. The create time of the DeploymentEvent.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The time at which the DeploymentEvent was deployed. This would
   * be the min of all ArtifactDeployment deploy_times.
   *
   * @param string $deployTime
   */
  public function setDeployTime($deployTime)
  {
    $this->deployTime = $deployTime;
  }
  /**
   * @return string
   */
  public function getDeployTime()
  {
    return $this->deployTime;
  }
  /**
   * Identifier. The name of the DeploymentEvent. This name is provided by DCI.
   * Format: projects/{project}/locations/{location}/insightsConfigs/{insights_c
   * onfig}/deploymentEvents/{uuid}
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
   * Output only. The runtime configurations where the DeploymentEvent happened.
   *
   * @param RuntimeConfig $runtimeConfig
   */
  public function setRuntimeConfig(RuntimeConfig $runtimeConfig)
  {
    $this->runtimeConfig = $runtimeConfig;
  }
  /**
   * @return RuntimeConfig
   */
  public function getRuntimeConfig()
  {
    return $this->runtimeConfig;
  }
  /**
   * Output only. The runtime assigned URI of the DeploymentEvent. For GKE, this
   * is the fully qualified replica set uri. e.g. container.googleapis.com/proje
   * cts/{project}/locations/{location}/clusters/{cluster}/k8s/namespaces/{names
   * pace}/apps/replicasets/{replica-set-id} For Cloud Run, this is the revision
   * name.
   *
   * @param string $runtimeDeploymentUri
   */
  public function setRuntimeDeploymentUri($runtimeDeploymentUri)
  {
    $this->runtimeDeploymentUri = $runtimeDeploymentUri;
  }
  /**
   * @return string
   */
  public function getRuntimeDeploymentUri()
  {
    return $this->runtimeDeploymentUri;
  }
  /**
   * Output only. The state of the DeploymentEvent.
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_ACTIVE, STATE_INACTIVE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The time at which the DeploymentEvent was undeployed, all
   * artifacts are considered undeployed once this time is set. This would be
   * the max of all ArtifactDeployment undeploy_times. If any ArtifactDeployment
   * is still active (i.e. does not have an undeploy_time), this field will be
   * empty.
   *
   * @param string $undeployTime
   */
  public function setUndeployTime($undeployTime)
  {
    $this->undeployTime = $undeployTime;
  }
  /**
   * @return string
   */
  public function getUndeployTime()
  {
    return $this->undeployTime;
  }
  /**
   * Output only. The update time of the DeploymentEvent.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeploymentEvent::class, 'Google_Service_DeveloperConnect_DeploymentEvent');
