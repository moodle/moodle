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

namespace Google\Service\WorkloadManager;

class AgentStatus extends \Google\Collection
{
  /**
   * The state is unspecified and has not been checked yet.
   */
  public const CLOUD_API_ACCESS_FULL_SCOPES_GRANTED_UNSPECIFIED_STATE = 'UNSPECIFIED_STATE';
  /**
   * The state is successful (enabled, granted, fully functional).
   */
  public const CLOUD_API_ACCESS_FULL_SCOPES_GRANTED_SUCCESS_STATE = 'SUCCESS_STATE';
  /**
   * The state is failed (disabled, denied, not fully functional).
   */
  public const CLOUD_API_ACCESS_FULL_SCOPES_GRANTED_FAILURE_STATE = 'FAILURE_STATE';
  /**
   * There was an internal error while checking the state, state is unknown.
   */
  public const CLOUD_API_ACCESS_FULL_SCOPES_GRANTED_ERROR_STATE = 'ERROR_STATE';
  /**
   * The state is unspecified and has not been checked yet.
   */
  public const CONFIGURATION_VALID_UNSPECIFIED_STATE = 'UNSPECIFIED_STATE';
  /**
   * The state is successful (enabled, granted, fully functional).
   */
  public const CONFIGURATION_VALID_SUCCESS_STATE = 'SUCCESS_STATE';
  /**
   * The state is failed (disabled, denied, not fully functional).
   */
  public const CONFIGURATION_VALID_FAILURE_STATE = 'FAILURE_STATE';
  /**
   * There was an internal error while checking the state, state is unknown.
   */
  public const CONFIGURATION_VALID_ERROR_STATE = 'ERROR_STATE';
  /**
   * The state is unspecified and has not been checked yet.
   */
  public const SYSTEMD_SERVICE_ENABLED_UNSPECIFIED_STATE = 'UNSPECIFIED_STATE';
  /**
   * The state is successful (enabled, granted, fully functional).
   */
  public const SYSTEMD_SERVICE_ENABLED_SUCCESS_STATE = 'SUCCESS_STATE';
  /**
   * The state is failed (disabled, denied, not fully functional).
   */
  public const SYSTEMD_SERVICE_ENABLED_FAILURE_STATE = 'FAILURE_STATE';
  /**
   * There was an internal error while checking the state, state is unknown.
   */
  public const SYSTEMD_SERVICE_ENABLED_ERROR_STATE = 'ERROR_STATE';
  /**
   * The state is unspecified and has not been checked yet.
   */
  public const SYSTEMD_SERVICE_RUNNING_UNSPECIFIED_STATE = 'UNSPECIFIED_STATE';
  /**
   * The state is successful (enabled, granted, fully functional).
   */
  public const SYSTEMD_SERVICE_RUNNING_SUCCESS_STATE = 'SUCCESS_STATE';
  /**
   * The state is failed (disabled, denied, not fully functional).
   */
  public const SYSTEMD_SERVICE_RUNNING_FAILURE_STATE = 'FAILURE_STATE';
  /**
   * There was an internal error while checking the state, state is unknown.
   */
  public const SYSTEMD_SERVICE_RUNNING_ERROR_STATE = 'ERROR_STATE';
  protected $collection_key = 'services';
  /**
   * Output only. The name of the agent.
   *
   * @var string
   */
  public $agentName;
  /**
   * Output only. The available version of the agent in artifact registry.
   *
   * @var string
   */
  public $availableVersion;
  /**
   * Output only. Whether the agent has full access to Cloud APIs.
   *
   * @var string
   */
  public $cloudApiAccessFullScopesGranted;
  /**
   * Output only. The error message for the agent configuration if invalid.
   *
   * @var string
   */
  public $configurationErrorMessage;
  /**
   * Output only. The path to the agent configuration file.
   *
   * @var string
   */
  public $configurationFilePath;
  /**
   * Output only. Whether the agent configuration is valid.
   *
   * @var string
   */
  public $configurationValid;
  /**
   * Output only. The installed version of the agent on the host.
   *
   * @var string
   */
  public $installedVersion;
  /**
   * Output only. The URI of the instance. Format: projects//zones//instances/
   *
   * @var string
   */
  public $instanceUri;
  protected $kernelVersionType = SapDiscoveryResourceInstancePropertiesKernelVersion::class;
  protected $kernelVersionDataType = '';
  protected $referencesType = AgentStatusReference::class;
  protected $referencesDataType = 'array';
  protected $servicesType = AgentStatusServiceStatus::class;
  protected $servicesDataType = 'array';
  /**
   * Output only. Whether the agent service is enabled in systemd.
   *
   * @var string
   */
  public $systemdServiceEnabled;
  /**
   * Output only. Whether the agent service is running in systemd.
   *
   * @var string
   */
  public $systemdServiceRunning;

  /**
   * Output only. The name of the agent.
   *
   * @param string $agentName
   */
  public function setAgentName($agentName)
  {
    $this->agentName = $agentName;
  }
  /**
   * @return string
   */
  public function getAgentName()
  {
    return $this->agentName;
  }
  /**
   * Output only. The available version of the agent in artifact registry.
   *
   * @param string $availableVersion
   */
  public function setAvailableVersion($availableVersion)
  {
    $this->availableVersion = $availableVersion;
  }
  /**
   * @return string
   */
  public function getAvailableVersion()
  {
    return $this->availableVersion;
  }
  /**
   * Output only. Whether the agent has full access to Cloud APIs.
   *
   * Accepted values: UNSPECIFIED_STATE, SUCCESS_STATE, FAILURE_STATE,
   * ERROR_STATE
   *
   * @param self::CLOUD_API_ACCESS_FULL_SCOPES_GRANTED_* $cloudApiAccessFullScopesGranted
   */
  public function setCloudApiAccessFullScopesGranted($cloudApiAccessFullScopesGranted)
  {
    $this->cloudApiAccessFullScopesGranted = $cloudApiAccessFullScopesGranted;
  }
  /**
   * @return self::CLOUD_API_ACCESS_FULL_SCOPES_GRANTED_*
   */
  public function getCloudApiAccessFullScopesGranted()
  {
    return $this->cloudApiAccessFullScopesGranted;
  }
  /**
   * Output only. The error message for the agent configuration if invalid.
   *
   * @param string $configurationErrorMessage
   */
  public function setConfigurationErrorMessage($configurationErrorMessage)
  {
    $this->configurationErrorMessage = $configurationErrorMessage;
  }
  /**
   * @return string
   */
  public function getConfigurationErrorMessage()
  {
    return $this->configurationErrorMessage;
  }
  /**
   * Output only. The path to the agent configuration file.
   *
   * @param string $configurationFilePath
   */
  public function setConfigurationFilePath($configurationFilePath)
  {
    $this->configurationFilePath = $configurationFilePath;
  }
  /**
   * @return string
   */
  public function getConfigurationFilePath()
  {
    return $this->configurationFilePath;
  }
  /**
   * Output only. Whether the agent configuration is valid.
   *
   * Accepted values: UNSPECIFIED_STATE, SUCCESS_STATE, FAILURE_STATE,
   * ERROR_STATE
   *
   * @param self::CONFIGURATION_VALID_* $configurationValid
   */
  public function setConfigurationValid($configurationValid)
  {
    $this->configurationValid = $configurationValid;
  }
  /**
   * @return self::CONFIGURATION_VALID_*
   */
  public function getConfigurationValid()
  {
    return $this->configurationValid;
  }
  /**
   * Output only. The installed version of the agent on the host.
   *
   * @param string $installedVersion
   */
  public function setInstalledVersion($installedVersion)
  {
    $this->installedVersion = $installedVersion;
  }
  /**
   * @return string
   */
  public function getInstalledVersion()
  {
    return $this->installedVersion;
  }
  /**
   * Output only. The URI of the instance. Format: projects//zones//instances/
   *
   * @param string $instanceUri
   */
  public function setInstanceUri($instanceUri)
  {
    $this->instanceUri = $instanceUri;
  }
  /**
   * @return string
   */
  public function getInstanceUri()
  {
    return $this->instanceUri;
  }
  /**
   * Output only. The kernel version of the system.
   *
   * @param SapDiscoveryResourceInstancePropertiesKernelVersion $kernelVersion
   */
  public function setKernelVersion(SapDiscoveryResourceInstancePropertiesKernelVersion $kernelVersion)
  {
    $this->kernelVersion = $kernelVersion;
  }
  /**
   * @return SapDiscoveryResourceInstancePropertiesKernelVersion
   */
  public function getKernelVersion()
  {
    return $this->kernelVersion;
  }
  /**
   * Output only. Optional references to public documentation.
   *
   * @param AgentStatusReference[] $references
   */
  public function setReferences($references)
  {
    $this->references = $references;
  }
  /**
   * @return AgentStatusReference[]
   */
  public function getReferences()
  {
    return $this->references;
  }
  /**
   * Output only. The services (process metrics, host metrics, etc.).
   *
   * @param AgentStatusServiceStatus[] $services
   */
  public function setServices($services)
  {
    $this->services = $services;
  }
  /**
   * @return AgentStatusServiceStatus[]
   */
  public function getServices()
  {
    return $this->services;
  }
  /**
   * Output only. Whether the agent service is enabled in systemd.
   *
   * Accepted values: UNSPECIFIED_STATE, SUCCESS_STATE, FAILURE_STATE,
   * ERROR_STATE
   *
   * @param self::SYSTEMD_SERVICE_ENABLED_* $systemdServiceEnabled
   */
  public function setSystemdServiceEnabled($systemdServiceEnabled)
  {
    $this->systemdServiceEnabled = $systemdServiceEnabled;
  }
  /**
   * @return self::SYSTEMD_SERVICE_ENABLED_*
   */
  public function getSystemdServiceEnabled()
  {
    return $this->systemdServiceEnabled;
  }
  /**
   * Output only. Whether the agent service is running in systemd.
   *
   * Accepted values: UNSPECIFIED_STATE, SUCCESS_STATE, FAILURE_STATE,
   * ERROR_STATE
   *
   * @param self::SYSTEMD_SERVICE_RUNNING_* $systemdServiceRunning
   */
  public function setSystemdServiceRunning($systemdServiceRunning)
  {
    $this->systemdServiceRunning = $systemdServiceRunning;
  }
  /**
   * @return self::SYSTEMD_SERVICE_RUNNING_*
   */
  public function getSystemdServiceRunning()
  {
    return $this->systemdServiceRunning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentStatus::class, 'Google_Service_WorkloadManager_AgentStatus');
