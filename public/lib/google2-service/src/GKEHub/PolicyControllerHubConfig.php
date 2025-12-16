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

class PolicyControllerHubConfig extends \Google\Collection
{
  /**
   * Spec is unknown.
   */
  public const INSTALL_SPEC_INSTALL_SPEC_UNSPECIFIED = 'INSTALL_SPEC_UNSPECIFIED';
  /**
   * Request to uninstall Policy Controller.
   */
  public const INSTALL_SPEC_INSTALL_SPEC_NOT_INSTALLED = 'INSTALL_SPEC_NOT_INSTALLED';
  /**
   * Request to install and enable Policy Controller.
   */
  public const INSTALL_SPEC_INSTALL_SPEC_ENABLED = 'INSTALL_SPEC_ENABLED';
  /**
   * Request to suspend Policy Controller i.e. its webhooks. If Policy
   * Controller is not installed, it will be installed but suspended.
   */
  public const INSTALL_SPEC_INSTALL_SPEC_SUSPENDED = 'INSTALL_SPEC_SUSPENDED';
  /**
   * Request to stop all reconciliation actions by PoCo Hub controller. This is
   * a breakglass mechanism to stop PoCo Hub from affecting cluster resources.
   */
  public const INSTALL_SPEC_INSTALL_SPEC_DETACHED = 'INSTALL_SPEC_DETACHED';
  protected $collection_key = 'exemptableNamespaces';
  /**
   * Sets the interval for Policy Controller Audit Scans (in seconds). When set
   * to 0, this disables audit functionality altogether.
   *
   * @var string
   */
  public $auditIntervalSeconds;
  /**
   * The maximum number of audit violations to be stored in a constraint. If not
   * set, the internal default (currently 20) will be used.
   *
   * @var string
   */
  public $constraintViolationLimit;
  protected $deploymentConfigsType = PolicyControllerPolicyControllerDeploymentConfig::class;
  protected $deploymentConfigsDataType = 'map';
  /**
   * The set of namespaces that are excluded from Policy Controller checks.
   * Namespaces do not need to currently exist on the cluster.
   *
   * @var string[]
   */
  public $exemptableNamespaces;
  /**
   * The install_spec represents the intended state specified by the latest
   * request that mutated install_spec in the feature spec, not the lifecycle
   * state of the feature observed by the Hub feature controller that is
   * reported in the feature state.
   *
   * @var string
   */
  public $installSpec;
  /**
   * Logs all denies and dry run failures.
   *
   * @var bool
   */
  public $logDeniesEnabled;
  protected $monitoringType = PolicyControllerMonitoringConfig::class;
  protected $monitoringDataType = '';
  /**
   * Enables the ability to mutate resources using Policy Controller.
   *
   * @var bool
   */
  public $mutationEnabled;
  protected $policyContentType = PolicyControllerPolicyContentSpec::class;
  protected $policyContentDataType = '';
  /**
   * Enables the ability to use Constraint Templates that reference to objects
   * other than the object currently being evaluated.
   *
   * @var bool
   */
  public $referentialRulesEnabled;

  /**
   * Sets the interval for Policy Controller Audit Scans (in seconds). When set
   * to 0, this disables audit functionality altogether.
   *
   * @param string $auditIntervalSeconds
   */
  public function setAuditIntervalSeconds($auditIntervalSeconds)
  {
    $this->auditIntervalSeconds = $auditIntervalSeconds;
  }
  /**
   * @return string
   */
  public function getAuditIntervalSeconds()
  {
    return $this->auditIntervalSeconds;
  }
  /**
   * The maximum number of audit violations to be stored in a constraint. If not
   * set, the internal default (currently 20) will be used.
   *
   * @param string $constraintViolationLimit
   */
  public function setConstraintViolationLimit($constraintViolationLimit)
  {
    $this->constraintViolationLimit = $constraintViolationLimit;
  }
  /**
   * @return string
   */
  public function getConstraintViolationLimit()
  {
    return $this->constraintViolationLimit;
  }
  /**
   * Map of deployment configs to deployments (“admission”, “audit”,
   * “mutation”).
   *
   * @param PolicyControllerPolicyControllerDeploymentConfig[] $deploymentConfigs
   */
  public function setDeploymentConfigs($deploymentConfigs)
  {
    $this->deploymentConfigs = $deploymentConfigs;
  }
  /**
   * @return PolicyControllerPolicyControllerDeploymentConfig[]
   */
  public function getDeploymentConfigs()
  {
    return $this->deploymentConfigs;
  }
  /**
   * The set of namespaces that are excluded from Policy Controller checks.
   * Namespaces do not need to currently exist on the cluster.
   *
   * @param string[] $exemptableNamespaces
   */
  public function setExemptableNamespaces($exemptableNamespaces)
  {
    $this->exemptableNamespaces = $exemptableNamespaces;
  }
  /**
   * @return string[]
   */
  public function getExemptableNamespaces()
  {
    return $this->exemptableNamespaces;
  }
  /**
   * The install_spec represents the intended state specified by the latest
   * request that mutated install_spec in the feature spec, not the lifecycle
   * state of the feature observed by the Hub feature controller that is
   * reported in the feature state.
   *
   * Accepted values: INSTALL_SPEC_UNSPECIFIED, INSTALL_SPEC_NOT_INSTALLED,
   * INSTALL_SPEC_ENABLED, INSTALL_SPEC_SUSPENDED, INSTALL_SPEC_DETACHED
   *
   * @param self::INSTALL_SPEC_* $installSpec
   */
  public function setInstallSpec($installSpec)
  {
    $this->installSpec = $installSpec;
  }
  /**
   * @return self::INSTALL_SPEC_*
   */
  public function getInstallSpec()
  {
    return $this->installSpec;
  }
  /**
   * Logs all denies and dry run failures.
   *
   * @param bool $logDeniesEnabled
   */
  public function setLogDeniesEnabled($logDeniesEnabled)
  {
    $this->logDeniesEnabled = $logDeniesEnabled;
  }
  /**
   * @return bool
   */
  public function getLogDeniesEnabled()
  {
    return $this->logDeniesEnabled;
  }
  /**
   * Monitoring specifies the configuration of monitoring.
   *
   * @param PolicyControllerMonitoringConfig $monitoring
   */
  public function setMonitoring(PolicyControllerMonitoringConfig $monitoring)
  {
    $this->monitoring = $monitoring;
  }
  /**
   * @return PolicyControllerMonitoringConfig
   */
  public function getMonitoring()
  {
    return $this->monitoring;
  }
  /**
   * Enables the ability to mutate resources using Policy Controller.
   *
   * @param bool $mutationEnabled
   */
  public function setMutationEnabled($mutationEnabled)
  {
    $this->mutationEnabled = $mutationEnabled;
  }
  /**
   * @return bool
   */
  public function getMutationEnabled()
  {
    return $this->mutationEnabled;
  }
  /**
   * Specifies the desired policy content on the cluster
   *
   * @param PolicyControllerPolicyContentSpec $policyContent
   */
  public function setPolicyContent(PolicyControllerPolicyContentSpec $policyContent)
  {
    $this->policyContent = $policyContent;
  }
  /**
   * @return PolicyControllerPolicyContentSpec
   */
  public function getPolicyContent()
  {
    return $this->policyContent;
  }
  /**
   * Enables the ability to use Constraint Templates that reference to objects
   * other than the object currently being evaluated.
   *
   * @param bool $referentialRulesEnabled
   */
  public function setReferentialRulesEnabled($referentialRulesEnabled)
  {
    $this->referentialRulesEnabled = $referentialRulesEnabled;
  }
  /**
   * @return bool
   */
  public function getReferentialRulesEnabled()
  {
    return $this->referentialRulesEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyControllerHubConfig::class, 'Google_Service_GKEHub_PolicyControllerHubConfig');
