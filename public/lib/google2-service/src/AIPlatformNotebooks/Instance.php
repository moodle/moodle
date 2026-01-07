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

namespace Google\Service\AIPlatformNotebooks;

class Instance extends \Google\Collection
{
  /**
   * The instance substate is unknown.
   */
  public const HEALTH_STATE_HEALTH_STATE_UNSPECIFIED = 'HEALTH_STATE_UNSPECIFIED';
  /**
   * The instance is known to be in an healthy state (for example, critical
   * daemons are running) Applies to ACTIVE state.
   */
  public const HEALTH_STATE_HEALTHY = 'HEALTHY';
  /**
   * The instance is known to be in an unhealthy state (for example, critical
   * daemons are not running) Applies to ACTIVE state.
   */
  public const HEALTH_STATE_UNHEALTHY = 'UNHEALTHY';
  /**
   * The instance has not installed health monitoring agent. Applies to ACTIVE
   * state.
   */
  public const HEALTH_STATE_AGENT_NOT_INSTALLED = 'AGENT_NOT_INSTALLED';
  /**
   * The instance health monitoring agent is not running. Applies to ACTIVE
   * state.
   */
  public const HEALTH_STATE_AGENT_NOT_RUNNING = 'AGENT_NOT_RUNNING';
  /**
   * State is not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The control logic is starting the instance.
   */
  public const STATE_STARTING = 'STARTING';
  /**
   * The control logic is installing required frameworks and registering the
   * instance with notebook proxy
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The instance is running.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The control logic is stopping the instance.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * The instance is stopped.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * The instance is deleted.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * The instance is upgrading.
   */
  public const STATE_UPGRADING = 'UPGRADING';
  /**
   * The instance is being created.
   */
  public const STATE_INITIALIZING = 'INITIALIZING';
  /**
   * The instance is suspending.
   */
  public const STATE_SUSPENDING = 'SUSPENDING';
  /**
   * The instance is suspended.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  protected $collection_key = 'upgradeHistory';
  /**
   * Output only. Instance creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Email address of entity that sent original CreateInstance
   * request.
   *
   * @var string
   */
  public $creator;
  /**
   * Optional. If true, the notebook instance will not register with the proxy.
   *
   * @var bool
   */
  public $disableProxyAccess;
  /**
   * Optional. If true, deletion protection will be enabled for this Workbench
   * Instance. If false, deletion protection will be disabled for this Workbench
   * Instance.
   *
   * @var bool
   */
  public $enableDeletionProtection;
  /**
   * Optional. Flag to enable managed end user credentials for the instance.
   *
   * @var bool
   */
  public $enableManagedEuc;
  /**
   * Optional. Flag that specifies that a notebook can be accessed with third
   * party identity provider.
   *
   * @var bool
   */
  public $enableThirdPartyIdentity;
  protected $gceSetupType = GceSetup::class;
  protected $gceSetupDataType = '';
  /**
   * Output only. Additional information about instance health. Example:
   * healthInfo": { "docker_proxy_agent_status": "1", "docker_status": "1",
   * "jupyterlab_api_status": "-1", "jupyterlab_status": "-1", "updated":
   * "2020-10-18 09:40:03.573409" }
   *
   * @var string[]
   */
  public $healthInfo;
  /**
   * Output only. Instance health_state.
   *
   * @var string
   */
  public $healthState;
  /**
   * Output only. Unique ID of the resource.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. The owner of this instance after creation. Format:
   * `alias@example.com` Currently supports one owner only. If not specified,
   * all of the service account users of your VM instance's service account can
   * use the instance.
   *
   * @var string[]
   */
  public $instanceOwners;
  /**
   * Optional. Labels to apply to this instance. These can be later modified by
   * the UpdateInstance method.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. The name of this notebook instance. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The proxy endpoint that is used to access the Jupyter
   * notebook.
   *
   * @var string
   */
  public $proxyUri;
  /**
   * Output only. Reserved for future use for Zone Isolation.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use for Zone Separation.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The state of this instance.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The workforce pools proxy endpoint that is used to access the
   * Jupyter notebook.
   *
   * @var string
   */
  public $thirdPartyProxyUrl;
  /**
   * Output only. Instance update time.
   *
   * @var string
   */
  public $updateTime;
  protected $upgradeHistoryType = UpgradeHistoryEntry::class;
  protected $upgradeHistoryDataType = 'array';

  /**
   * Output only. Instance creation time.
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
   * Output only. Email address of entity that sent original CreateInstance
   * request.
   *
   * @param string $creator
   */
  public function setCreator($creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return string
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * Optional. If true, the notebook instance will not register with the proxy.
   *
   * @param bool $disableProxyAccess
   */
  public function setDisableProxyAccess($disableProxyAccess)
  {
    $this->disableProxyAccess = $disableProxyAccess;
  }
  /**
   * @return bool
   */
  public function getDisableProxyAccess()
  {
    return $this->disableProxyAccess;
  }
  /**
   * Optional. If true, deletion protection will be enabled for this Workbench
   * Instance. If false, deletion protection will be disabled for this Workbench
   * Instance.
   *
   * @param bool $enableDeletionProtection
   */
  public function setEnableDeletionProtection($enableDeletionProtection)
  {
    $this->enableDeletionProtection = $enableDeletionProtection;
  }
  /**
   * @return bool
   */
  public function getEnableDeletionProtection()
  {
    return $this->enableDeletionProtection;
  }
  /**
   * Optional. Flag to enable managed end user credentials for the instance.
   *
   * @param bool $enableManagedEuc
   */
  public function setEnableManagedEuc($enableManagedEuc)
  {
    $this->enableManagedEuc = $enableManagedEuc;
  }
  /**
   * @return bool
   */
  public function getEnableManagedEuc()
  {
    return $this->enableManagedEuc;
  }
  /**
   * Optional. Flag that specifies that a notebook can be accessed with third
   * party identity provider.
   *
   * @param bool $enableThirdPartyIdentity
   */
  public function setEnableThirdPartyIdentity($enableThirdPartyIdentity)
  {
    $this->enableThirdPartyIdentity = $enableThirdPartyIdentity;
  }
  /**
   * @return bool
   */
  public function getEnableThirdPartyIdentity()
  {
    return $this->enableThirdPartyIdentity;
  }
  /**
   * Optional. Compute Engine setup for the notebook. Uses notebook-defined
   * fields.
   *
   * @param GceSetup $gceSetup
   */
  public function setGceSetup(GceSetup $gceSetup)
  {
    $this->gceSetup = $gceSetup;
  }
  /**
   * @return GceSetup
   */
  public function getGceSetup()
  {
    return $this->gceSetup;
  }
  /**
   * Output only. Additional information about instance health. Example:
   * healthInfo": { "docker_proxy_agent_status": "1", "docker_status": "1",
   * "jupyterlab_api_status": "-1", "jupyterlab_status": "-1", "updated":
   * "2020-10-18 09:40:03.573409" }
   *
   * @param string[] $healthInfo
   */
  public function setHealthInfo($healthInfo)
  {
    $this->healthInfo = $healthInfo;
  }
  /**
   * @return string[]
   */
  public function getHealthInfo()
  {
    return $this->healthInfo;
  }
  /**
   * Output only. Instance health_state.
   *
   * Accepted values: HEALTH_STATE_UNSPECIFIED, HEALTHY, UNHEALTHY,
   * AGENT_NOT_INSTALLED, AGENT_NOT_RUNNING
   *
   * @param self::HEALTH_STATE_* $healthState
   */
  public function setHealthState($healthState)
  {
    $this->healthState = $healthState;
  }
  /**
   * @return self::HEALTH_STATE_*
   */
  public function getHealthState()
  {
    return $this->healthState;
  }
  /**
   * Output only. Unique ID of the resource.
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
   * Optional. The owner of this instance after creation. Format:
   * `alias@example.com` Currently supports one owner only. If not specified,
   * all of the service account users of your VM instance's service account can
   * use the instance.
   *
   * @param string[] $instanceOwners
   */
  public function setInstanceOwners($instanceOwners)
  {
    $this->instanceOwners = $instanceOwners;
  }
  /**
   * @return string[]
   */
  public function getInstanceOwners()
  {
    return $this->instanceOwners;
  }
  /**
   * Optional. Labels to apply to this instance. These can be later modified by
   * the UpdateInstance method.
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
   * Output only. Identifier. The name of this notebook instance. Format:
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
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
   * Output only. The proxy endpoint that is used to access the Jupyter
   * notebook.
   *
   * @param string $proxyUri
   */
  public function setProxyUri($proxyUri)
  {
    $this->proxyUri = $proxyUri;
  }
  /**
   * @return string
   */
  public function getProxyUri()
  {
    return $this->proxyUri;
  }
  /**
   * Output only. Reserved for future use for Zone Isolation.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use for Zone Separation.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The state of this instance.
   *
   * Accepted values: STATE_UNSPECIFIED, STARTING, PROVISIONING, ACTIVE,
   * STOPPING, STOPPED, DELETED, UPGRADING, INITIALIZING, SUSPENDING, SUSPENDED
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
   * Output only. The workforce pools proxy endpoint that is used to access the
   * Jupyter notebook.
   *
   * @param string $thirdPartyProxyUrl
   */
  public function setThirdPartyProxyUrl($thirdPartyProxyUrl)
  {
    $this->thirdPartyProxyUrl = $thirdPartyProxyUrl;
  }
  /**
   * @return string
   */
  public function getThirdPartyProxyUrl()
  {
    return $this->thirdPartyProxyUrl;
  }
  /**
   * Output only. Instance update time.
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
  /**
   * Output only. The upgrade history of this instance.
   *
   * @param UpgradeHistoryEntry[] $upgradeHistory
   */
  public function setUpgradeHistory($upgradeHistory)
  {
    $this->upgradeHistory = $upgradeHistory;
  }
  /**
   * @return UpgradeHistoryEntry[]
   */
  public function getUpgradeHistory()
  {
    return $this->upgradeHistory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_AIPlatformNotebooks_Instance');
