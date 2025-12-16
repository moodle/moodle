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

namespace Google\Service\GKEHub;

class PolicyControllerPolicyControllerDeploymentConfig extends \Google\Collection
{
  /**
   * No affinity configuration has been specified.
   */
  public const POD_AFFINITY_AFFINITY_UNSPECIFIED = 'AFFINITY_UNSPECIFIED';
  /**
   * Affinity configurations will be removed from the deployment.
   */
  public const POD_AFFINITY_NO_AFFINITY = 'NO_AFFINITY';
  /**
   * Anti-affinity configuration will be applied to this deployment. Default for
   * admissions deployment.
   */
  public const POD_AFFINITY_ANTI_AFFINITY = 'ANTI_AFFINITY';
  protected $collection_key = 'podTolerations';
  protected $containerResourcesType = PolicyControllerResourceRequirements::class;
  protected $containerResourcesDataType = '';
  /**
   * Pod affinity configuration.
   *
   * @var string
   */
  public $podAffinity;
  /**
   * Pod anti-affinity enablement. Deprecated: use `pod_affinity` instead.
   *
   * @deprecated
   * @var bool
   */
  public $podAntiAffinity;
  protected $podTolerationsType = PolicyControllerToleration::class;
  protected $podTolerationsDataType = 'array';
  /**
   * Pod replica count.
   *
   * @var string
   */
  public $replicaCount;

  /**
   * Container resource requirements.
   *
   * @param PolicyControllerResourceRequirements $containerResources
   */
  public function setContainerResources(PolicyControllerResourceRequirements $containerResources)
  {
    $this->containerResources = $containerResources;
  }
  /**
   * @return PolicyControllerResourceRequirements
   */
  public function getContainerResources()
  {
    return $this->containerResources;
  }
  /**
   * Pod affinity configuration.
   *
   * Accepted values: AFFINITY_UNSPECIFIED, NO_AFFINITY, ANTI_AFFINITY
   *
   * @param self::POD_AFFINITY_* $podAffinity
   */
  public function setPodAffinity($podAffinity)
  {
    $this->podAffinity = $podAffinity;
  }
  /**
   * @return self::POD_AFFINITY_*
   */
  public function getPodAffinity()
  {
    return $this->podAffinity;
  }
  /**
   * Pod anti-affinity enablement. Deprecated: use `pod_affinity` instead.
   *
   * @deprecated
   * @param bool $podAntiAffinity
   */
  public function setPodAntiAffinity($podAntiAffinity)
  {
    $this->podAntiAffinity = $podAntiAffinity;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getPodAntiAffinity()
  {
    return $this->podAntiAffinity;
  }
  /**
   * Pod tolerations of node taints.
   *
   * @param PolicyControllerToleration[] $podTolerations
   */
  public function setPodTolerations($podTolerations)
  {
    $this->podTolerations = $podTolerations;
  }
  /**
   * @return PolicyControllerToleration[]
   */
  public function getPodTolerations()
  {
    return $this->podTolerations;
  }
  /**
   * Pod replica count.
   *
   * @param string $replicaCount
   */
  public function setReplicaCount($replicaCount)
  {
    $this->replicaCount = $replicaCount;
  }
  /**
   * @return string
   */
  public function getReplicaCount()
  {
    return $this->replicaCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyControllerPolicyControllerDeploymentConfig::class, 'Google_Service_GKEHub_PolicyControllerPolicyControllerDeploymentConfig');
