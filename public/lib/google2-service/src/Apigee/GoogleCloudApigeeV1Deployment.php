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

class GoogleCloudApigeeV1Deployment extends \Google\Collection
{
  /**
   * Default value till public preview. After public preview this value should
   * not be returned.
   */
  public const PROXY_DEPLOYMENT_TYPE_PROXY_DEPLOYMENT_TYPE_UNSPECIFIED = 'PROXY_DEPLOYMENT_TYPE_UNSPECIFIED';
  /**
   * Deployment will be of type Standard if only Standard proxies are used
   */
  public const PROXY_DEPLOYMENT_TYPE_STANDARD = 'STANDARD';
  /**
   * Proxy will be of type Extensible if deployments uses one or more Extensible
   * proxies
   */
  public const PROXY_DEPLOYMENT_TYPE_EXTENSIBLE = 'EXTENSIBLE';
  /**
   * This value should never be returned.
   */
  public const STATE_RUNTIME_STATE_UNSPECIFIED = 'RUNTIME_STATE_UNSPECIFIED';
  /**
   * Runtime has loaded the deployment.
   */
  public const STATE_READY = 'READY';
  /**
   * Deployment is not fully ready in the runtime.
   */
  public const STATE_PROGRESSING = 'PROGRESSING';
  /**
   * Encountered an error with the deployment that requires intervention.
   */
  public const STATE_ERROR = 'ERROR';
  protected $collection_key = 'routeConflicts';
  /**
   * API proxy.
   *
   * @var string
   */
  public $apiProxy;
  /**
   * Time the API proxy was marked `deployed` in the control plane in
   * millisconds since epoch.
   *
   * @var string
   */
  public $deployStartTime;
  /**
   * Environment.
   *
   * @var string
   */
  public $environment;
  protected $errorsType = GoogleRpcStatus::class;
  protected $errorsDataType = 'array';
  protected $instancesType = GoogleCloudApigeeV1InstanceDeploymentStatus::class;
  protected $instancesDataType = 'array';
  protected $podsType = GoogleCloudApigeeV1PodStatus::class;
  protected $podsDataType = 'array';
  /**
   * Output only. The type of the deployment (standard or extensible) Deployed
   * proxy revision will be marked as extensible in following 2 cases. 1. The
   * deployed proxy revision uses extensible policies. 2. If a environment
   * supports flowhooks and flow hook is configured.
   *
   * @var string
   */
  public $proxyDeploymentType;
  /**
   * API proxy revision.
   *
   * @var string
   */
  public $revision;
  protected $routeConflictsType = GoogleCloudApigeeV1DeploymentChangeReportRoutingConflict::class;
  protected $routeConflictsDataType = 'array';
  /**
   * The full resource name of Cloud IAM Service Account that this deployment is
   * using, eg, `projects/-/serviceAccounts/{email}`.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Current state of the deployment. **Note**: This field is displayed only
   * when viewing deployment status.
   *
   * @var string
   */
  public $state;

  /**
   * API proxy.
   *
   * @param string $apiProxy
   */
  public function setApiProxy($apiProxy)
  {
    $this->apiProxy = $apiProxy;
  }
  /**
   * @return string
   */
  public function getApiProxy()
  {
    return $this->apiProxy;
  }
  /**
   * Time the API proxy was marked `deployed` in the control plane in
   * millisconds since epoch.
   *
   * @param string $deployStartTime
   */
  public function setDeployStartTime($deployStartTime)
  {
    $this->deployStartTime = $deployStartTime;
  }
  /**
   * @return string
   */
  public function getDeployStartTime()
  {
    return $this->deployStartTime;
  }
  /**
   * Environment.
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
   * Errors reported for this deployment. Populated only when state == ERROR.
   * **Note**: This field is displayed only when viewing deployment status.
   *
   * @param GoogleRpcStatus[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Status reported by each runtime instance. **Note**: This field is displayed
   * only when viewing deployment status.
   *
   * @param GoogleCloudApigeeV1InstanceDeploymentStatus[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return GoogleCloudApigeeV1InstanceDeploymentStatus[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Status reported by runtime pods. **Note**: **This field is deprecated**.
   * Runtime versions 1.3 and above report instance level status rather than pod
   * status.
   *
   * @param GoogleCloudApigeeV1PodStatus[] $pods
   */
  public function setPods($pods)
  {
    $this->pods = $pods;
  }
  /**
   * @return GoogleCloudApigeeV1PodStatus[]
   */
  public function getPods()
  {
    return $this->pods;
  }
  /**
   * Output only. The type of the deployment (standard or extensible) Deployed
   * proxy revision will be marked as extensible in following 2 cases. 1. The
   * deployed proxy revision uses extensible policies. 2. If a environment
   * supports flowhooks and flow hook is configured.
   *
   * Accepted values: PROXY_DEPLOYMENT_TYPE_UNSPECIFIED, STANDARD, EXTENSIBLE
   *
   * @param self::PROXY_DEPLOYMENT_TYPE_* $proxyDeploymentType
   */
  public function setProxyDeploymentType($proxyDeploymentType)
  {
    $this->proxyDeploymentType = $proxyDeploymentType;
  }
  /**
   * @return self::PROXY_DEPLOYMENT_TYPE_*
   */
  public function getProxyDeploymentType()
  {
    return $this->proxyDeploymentType;
  }
  /**
   * API proxy revision.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
  /**
   * Conflicts in the desired state routing configuration. The presence of
   * conflicts does not cause the state to be `ERROR`, but it will mean that
   * some of the deployment's base paths are not routed to its environment. If
   * the conflicts change, the state will transition to `PROGRESSING` until the
   * latest configuration is rolled out to all instances. **Note**: This field
   * is displayed only when viewing deployment status.
   *
   * @param GoogleCloudApigeeV1DeploymentChangeReportRoutingConflict[] $routeConflicts
   */
  public function setRouteConflicts($routeConflicts)
  {
    $this->routeConflicts = $routeConflicts;
  }
  /**
   * @return GoogleCloudApigeeV1DeploymentChangeReportRoutingConflict[]
   */
  public function getRouteConflicts()
  {
    return $this->routeConflicts;
  }
  /**
   * The full resource name of Cloud IAM Service Account that this deployment is
   * using, eg, `projects/-/serviceAccounts/{email}`.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Current state of the deployment. **Note**: This field is displayed only
   * when viewing deployment status.
   *
   * Accepted values: RUNTIME_STATE_UNSPECIFIED, READY, PROGRESSING, ERROR
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Deployment::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Deployment');
