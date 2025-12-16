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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1RoutingRule extends \Google\Collection
{
  protected $collection_key = 'otherTargets';
  /**
   * URI path prefix used to route to the specified environment. May contain one
   * or more wildcards. For example, path segments consisting of a single `*`
   * character will match any string.
   *
   * @var string
   */
  public $basepath;
  /**
   * Name of a deployment group in an environment bound to the environment group
   * in the following format:
   * `organizations/{org}/environment/{env}/deploymentGroups/{group}` Only one
   * of environment or deployment_group will be set.
   *
   * @var string
   */
  public $deploymentGroup;
  /**
   * The env group config revision_id when this rule was added or last updated.
   * This value is set when the rule is created and will only update if the the
   * environment_id changes. It is used to determine if the runtime is up to
   * date with respect to this rule. This field is omitted from the
   * IngressConfig unless the GetDeployedIngressConfig API is called with
   * view=FULL.
   *
   * @var string
   */
  public $envGroupRevision;
  /**
   * Name of an environment bound to the environment group in the following
   * format: `organizations/{org}/environments/{env}`. Only one of environment
   * or deployment_group will be set.
   *
   * @var string
   */
  public $environment;
  /**
   * Conflicting targets, which will be resource names specifying either
   * deployment groups or environments.
   *
   * @var string[]
   */
  public $otherTargets;
  /**
   * The resource name of the proxy revision that is receiving this basepath in
   * the following format: `organizations/{org}/apis/{api}/revisions/{rev}`.
   * This field is omitted from the IngressConfig unless the
   * GetDeployedIngressConfig API is called with view=FULL.
   *
   * @var string
   */
  public $receiver;
  /**
   * The unix timestamp when this rule was updated. This is updated whenever
   * env_group_revision is updated. This field is omitted from the IngressConfig
   * unless the GetDeployedIngressConfig API is called with view=FULL.
   *
   * @var string
   */
  public $updateTime;

  /**
   * URI path prefix used to route to the specified environment. May contain one
   * or more wildcards. For example, path segments consisting of a single `*`
   * character will match any string.
   *
   * @param string $basepath
   */
  public function setBasepath($basepath)
  {
    $this->basepath = $basepath;
  }
  /**
   * @return string
   */
  public function getBasepath()
  {
    return $this->basepath;
  }
  /**
   * Name of a deployment group in an environment bound to the environment group
   * in the following format:
   * `organizations/{org}/environment/{env}/deploymentGroups/{group}` Only one
   * of environment or deployment_group will be set.
   *
   * @param string $deploymentGroup
   */
  public function setDeploymentGroup($deploymentGroup)
  {
    $this->deploymentGroup = $deploymentGroup;
  }
  /**
   * @return string
   */
  public function getDeploymentGroup()
  {
    return $this->deploymentGroup;
  }
  /**
   * The env group config revision_id when this rule was added or last updated.
   * This value is set when the rule is created and will only update if the the
   * environment_id changes. It is used to determine if the runtime is up to
   * date with respect to this rule. This field is omitted from the
   * IngressConfig unless the GetDeployedIngressConfig API is called with
   * view=FULL.
   *
   * @param string $envGroupRevision
   */
  public function setEnvGroupRevision($envGroupRevision)
  {
    $this->envGroupRevision = $envGroupRevision;
  }
  /**
   * @return string
   */
  public function getEnvGroupRevision()
  {
    return $this->envGroupRevision;
  }
  /**
   * Name of an environment bound to the environment group in the following
   * format: `organizations/{org}/environments/{env}`. Only one of environment
   * or deployment_group will be set.
   *
   * @param string $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return string
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Conflicting targets, which will be resource names specifying either
   * deployment groups or environments.
   *
   * @param string[] $otherTargets
   */
  public function setOtherTargets($otherTargets)
  {
    $this->otherTargets = $otherTargets;
  }
  /**
   * @return string[]
   */
  public function getOtherTargets()
  {
    return $this->otherTargets;
  }
  /**
   * The resource name of the proxy revision that is receiving this basepath in
   * the following format: `organizations/{org}/apis/{api}/revisions/{rev}`.
   * This field is omitted from the IngressConfig unless the
   * GetDeployedIngressConfig API is called with view=FULL.
   *
   * @param string $receiver
   */
  public function setReceiver($receiver)
  {
    $this->receiver = $receiver;
  }
  /**
   * @return string
   */
  public function getReceiver()
  {
    return $this->receiver;
  }
  /**
   * The unix timestamp when this rule was updated. This is updated whenever
   * env_group_revision is updated. This field is omitted from the IngressConfig
   * unless the GetDeployedIngressConfig API is called with view=FULL.
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
class_alias(GoogleCloudApigeeV1RoutingRule::class, 'Google_Service_Apigee_GoogleCloudApigeeV1RoutingRule');
