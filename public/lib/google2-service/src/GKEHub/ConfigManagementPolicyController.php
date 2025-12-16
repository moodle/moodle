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

class ConfigManagementPolicyController extends \Google\Collection
{
  protected $collection_key = 'exemptableNamespaces';
  /**
   * Sets the interval for Policy Controller Audit Scans (in seconds). When set
   * to 0, this disables audit functionality altogether.
   *
   * @var string
   */
  public $auditIntervalSeconds;
  /**
   * Enables the installation of Policy Controller. If false, the rest of
   * PolicyController fields take no effect.
   *
   * @var bool
   */
  public $enabled;
  /**
   * The set of namespaces that are excluded from Policy Controller checks.
   * Namespaces do not need to currently exist on the cluster.
   *
   * @var string[]
   */
  public $exemptableNamespaces;
  /**
   * Logs all denies and dry run failures.
   *
   * @var bool
   */
  public $logDeniesEnabled;
  protected $monitoringType = ConfigManagementPolicyControllerMonitoring::class;
  protected $monitoringDataType = '';
  /**
   * Enable or disable mutation in policy controller. If true, mutation CRDs,
   * webhook and controller deployment will be deployed to the cluster.
   *
   * @var bool
   */
  public $mutationEnabled;
  /**
   * Enables the ability to use Constraint Templates that reference to objects
   * other than the object currently being evaluated.
   *
   * @var bool
   */
  public $referentialRulesEnabled;
  /**
   * Installs the default template library along with Policy Controller.
   *
   * @var bool
   */
  public $templateLibraryInstalled;
  /**
   * Output only. Last time this membership spec was updated.
   *
   * @var string
   */
  public $updateTime;

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
   * Enables the installation of Policy Controller. If false, the rest of
   * PolicyController fields take no effect.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
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
   * @param ConfigManagementPolicyControllerMonitoring $monitoring
   */
  public function setMonitoring(ConfigManagementPolicyControllerMonitoring $monitoring)
  {
    $this->monitoring = $monitoring;
  }
  /**
   * @return ConfigManagementPolicyControllerMonitoring
   */
  public function getMonitoring()
  {
    return $this->monitoring;
  }
  /**
   * Enable or disable mutation in policy controller. If true, mutation CRDs,
   * webhook and controller deployment will be deployed to the cluster.
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
  /**
   * Installs the default template library along with Policy Controller.
   *
   * @param bool $templateLibraryInstalled
   */
  public function setTemplateLibraryInstalled($templateLibraryInstalled)
  {
    $this->templateLibraryInstalled = $templateLibraryInstalled;
  }
  /**
   * @return bool
   */
  public function getTemplateLibraryInstalled()
  {
    return $this->templateLibraryInstalled;
  }
  /**
   * Output only. Last time this membership spec was updated.
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
class_alias(ConfigManagementPolicyController::class, 'Google_Service_GKEHub_ConfigManagementPolicyController');
