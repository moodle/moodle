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

class Session extends \Google\Collection
{
  /**
   * The session state is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The session is created prior to running.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The session is running.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The session is terminating.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * The session is terminated successfully.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * The session is no longer running due to an error.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'stateHistory';
  /**
   * Output only. The time when the session was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The email address of the user who created the session.
   *
   * @var string
   */
  public $creator;
  protected $environmentConfigType = EnvironmentConfig::class;
  protected $environmentConfigDataType = '';
  protected $jupyterSessionType = JupyterConfig::class;
  protected $jupyterSessionDataType = '';
  /**
   * Optional. The labels to associate with the session. Label keys must contain
   * 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values may be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with a session.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the session.
   *
   * @var string
   */
  public $name;
  protected $runtimeConfigType = RuntimeConfig::class;
  protected $runtimeConfigDataType = '';
  protected $runtimeInfoType = RuntimeInfo::class;
  protected $runtimeInfoDataType = '';
  /**
   * Optional. The session template used by the session.Only resource names,
   * including project ID and location, are valid.Example: * https://www.googlea
   * pis.com/compute/v1/projects/[project_id]/locations/[dataproc_region]/sessio
   * nTemplates/[template_id] * projects/[project_id]/locations/[dataproc_region
   * ]/sessionTemplates/[template_id]The template must be in the same project
   * and Dataproc region as the session.
   *
   * @var string
   */
  public $sessionTemplate;
  protected $sparkConnectSessionType = SparkConnectConfig::class;
  protected $sparkConnectSessionDataType = '';
  /**
   * Output only. A state of the session.
   *
   * @var string
   */
  public $state;
  protected $stateHistoryType = SessionStateHistory::class;
  protected $stateHistoryDataType = 'array';
  /**
   * Output only. Session state details, such as the failure description if the
   * state is FAILED.
   *
   * @var string
   */
  public $stateMessage;
  /**
   * Output only. The time when the session entered the current state.
   *
   * @var string
   */
  public $stateTime;
  /**
   * Optional. The email address of the user who owns the session.
   *
   * @var string
   */
  public $user;
  /**
   * Output only. A session UUID (Unique Universal Identifier). The service
   * generates this value when it creates the session.
   *
   * @var string
   */
  public $uuid;

  /**
   * Output only. The time when the session was created.
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
   * Output only. The email address of the user who created the session.
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
   * Optional. Environment configuration for the session execution.
   *
   * @param EnvironmentConfig $environmentConfig
   */
  public function setEnvironmentConfig(EnvironmentConfig $environmentConfig)
  {
    $this->environmentConfig = $environmentConfig;
  }
  /**
   * @return EnvironmentConfig
   */
  public function getEnvironmentConfig()
  {
    return $this->environmentConfig;
  }
  /**
   * Optional. Jupyter session config.
   *
   * @param JupyterConfig $jupyterSession
   */
  public function setJupyterSession(JupyterConfig $jupyterSession)
  {
    $this->jupyterSession = $jupyterSession;
  }
  /**
   * @return JupyterConfig
   */
  public function getJupyterSession()
  {
    return $this->jupyterSession;
  }
  /**
   * Optional. The labels to associate with the session. Label keys must contain
   * 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values may be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with a session.
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
   * Identifier. The resource name of the session.
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
   * Optional. Runtime configuration for the session execution.
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
   * Output only. Runtime information about session execution.
   *
   * @param RuntimeInfo $runtimeInfo
   */
  public function setRuntimeInfo(RuntimeInfo $runtimeInfo)
  {
    $this->runtimeInfo = $runtimeInfo;
  }
  /**
   * @return RuntimeInfo
   */
  public function getRuntimeInfo()
  {
    return $this->runtimeInfo;
  }
  /**
   * Optional. The session template used by the session.Only resource names,
   * including project ID and location, are valid.Example: * https://www.googlea
   * pis.com/compute/v1/projects/[project_id]/locations/[dataproc_region]/sessio
   * nTemplates/[template_id] * projects/[project_id]/locations/[dataproc_region
   * ]/sessionTemplates/[template_id]The template must be in the same project
   * and Dataproc region as the session.
   *
   * @param string $sessionTemplate
   */
  public function setSessionTemplate($sessionTemplate)
  {
    $this->sessionTemplate = $sessionTemplate;
  }
  /**
   * @return string
   */
  public function getSessionTemplate()
  {
    return $this->sessionTemplate;
  }
  /**
   * Optional. Spark connect session config.
   *
   * @param SparkConnectConfig $sparkConnectSession
   */
  public function setSparkConnectSession(SparkConnectConfig $sparkConnectSession)
  {
    $this->sparkConnectSession = $sparkConnectSession;
  }
  /**
   * @return SparkConnectConfig
   */
  public function getSparkConnectSession()
  {
    return $this->sparkConnectSession;
  }
  /**
   * Output only. A state of the session.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, TERMINATING,
   * TERMINATED, FAILED
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
   * Output only. Historical state information for the session.
   *
   * @param SessionStateHistory[] $stateHistory
   */
  public function setStateHistory($stateHistory)
  {
    $this->stateHistory = $stateHistory;
  }
  /**
   * @return SessionStateHistory[]
   */
  public function getStateHistory()
  {
    return $this->stateHistory;
  }
  /**
   * Output only. Session state details, such as the failure description if the
   * state is FAILED.
   *
   * @param string $stateMessage
   */
  public function setStateMessage($stateMessage)
  {
    $this->stateMessage = $stateMessage;
  }
  /**
   * @return string
   */
  public function getStateMessage()
  {
    return $this->stateMessage;
  }
  /**
   * Output only. The time when the session entered the current state.
   *
   * @param string $stateTime
   */
  public function setStateTime($stateTime)
  {
    $this->stateTime = $stateTime;
  }
  /**
   * @return string
   */
  public function getStateTime()
  {
    return $this->stateTime;
  }
  /**
   * Optional. The email address of the user who owns the session.
   *
   * @param string $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }
  /**
   * @return string
   */
  public function getUser()
  {
    return $this->user;
  }
  /**
   * Output only. A session UUID (Unique Universal Identifier). The service
   * generates this value when it creates the session.
   *
   * @param string $uuid
   */
  public function setUuid($uuid)
  {
    $this->uuid = $uuid;
  }
  /**
   * @return string
   */
  public function getUuid()
  {
    return $this->uuid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Session::class, 'Google_Service_Dataproc_Session');
