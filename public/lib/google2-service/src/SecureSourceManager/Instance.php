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

namespace Google\Service\SecureSourceManager;

class Instance extends \Google\Model
{
  /**
   * Not set. This should only be the case for incoming requests.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Instance is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Instance is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Instance is paused.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * Instance is unknown, we are not sure if it's functioning.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * STATE_NOTE_UNSPECIFIED as the first value of State.
   */
  public const STATE_NOTE_STATE_NOTE_UNSPECIFIED = 'STATE_NOTE_UNSPECIFIED';
  /**
   * CMEK access is unavailable.
   */
  public const STATE_NOTE_PAUSED_CMEK_UNAVAILABLE = 'PAUSED_CMEK_UNAVAILABLE';
  /**
   * INSTANCE_RESUMING indicates that the instance was previously paused and is
   * under the process of being brought back.
   *
   * @deprecated
   */
  public const STATE_NOTE_INSTANCE_RESUMING = 'INSTANCE_RESUMING';
  /**
   * Output only. Create timestamp.
   *
   * @var string
   */
  public $createTime;
  protected $hostConfigType = HostConfig::class;
  protected $hostConfigDataType = '';
  /**
   * Optional. Immutable. Customer-managed encryption key name, in the format
   * projects/locations/keyRings/cryptoKeys.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Optional. Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. A unique identifier for an instance. The name should be of the
   * format:
   * `projects/{project_number}/locations/{location_id}/instances/{instance_id}`
   * `project_number`: Maps to a unique int64 id assigned to each project.
   * `location_id`: Refers to the region where the instance will be deployed.
   * Since Secure Source Manager is a regional service, it must be one of the
   * valid GCP regions. `instance_id`: User provided name for the instance, must
   * be unique for a project_number and location_id combination.
   *
   * @var string
   */
  public $name;
  protected $privateConfigType = PrivateConfig::class;
  protected $privateConfigDataType = '';
  /**
   * Output only. Current state of the instance.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. An optional field providing information about the current
   * instance state.
   *
   * @var string
   */
  public $stateNote;
  /**
   * Output only. Update timestamp.
   *
   * @var string
   */
  public $updateTime;
  protected $workforceIdentityFederationConfigType = WorkforceIdentityFederationConfig::class;
  protected $workforceIdentityFederationConfigDataType = '';

  /**
   * Output only. Create timestamp.
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
   * Output only. A list of hostnames for this instance.
   *
   * @param HostConfig $hostConfig
   */
  public function setHostConfig(HostConfig $hostConfig)
  {
    $this->hostConfig = $hostConfig;
  }
  /**
   * @return HostConfig
   */
  public function getHostConfig()
  {
    return $this->hostConfig;
  }
  /**
   * Optional. Immutable. Customer-managed encryption key name, in the format
   * projects/locations/keyRings/cryptoKeys.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Optional. Labels as key value pairs.
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
   * Optional. A unique identifier for an instance. The name should be of the
   * format:
   * `projects/{project_number}/locations/{location_id}/instances/{instance_id}`
   * `project_number`: Maps to a unique int64 id assigned to each project.
   * `location_id`: Refers to the region where the instance will be deployed.
   * Since Secure Source Manager is a regional service, it must be one of the
   * valid GCP regions. `instance_id`: User provided name for the instance, must
   * be unique for a project_number and location_id combination.
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
   * Optional. Private settings for private instance.
   *
   * @param PrivateConfig $privateConfig
   */
  public function setPrivateConfig(PrivateConfig $privateConfig)
  {
    $this->privateConfig = $privateConfig;
  }
  /**
   * @return PrivateConfig
   */
  public function getPrivateConfig()
  {
    return $this->privateConfig;
  }
  /**
   * Output only. Current state of the instance.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, PAUSED,
   * UNKNOWN
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
   * Output only. An optional field providing information about the current
   * instance state.
   *
   * Accepted values: STATE_NOTE_UNSPECIFIED, PAUSED_CMEK_UNAVAILABLE,
   * INSTANCE_RESUMING
   *
   * @param self::STATE_NOTE_* $stateNote
   */
  public function setStateNote($stateNote)
  {
    $this->stateNote = $stateNote;
  }
  /**
   * @return self::STATE_NOTE_*
   */
  public function getStateNote()
  {
    return $this->stateNote;
  }
  /**
   * Output only. Update timestamp.
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
   * Optional. Configuration for Workforce Identity Federation to support third
   * party identity provider. If unset, defaults to the Google OIDC IdP.
   *
   * @param WorkforceIdentityFederationConfig $workforceIdentityFederationConfig
   */
  public function setWorkforceIdentityFederationConfig(WorkforceIdentityFederationConfig $workforceIdentityFederationConfig)
  {
    $this->workforceIdentityFederationConfig = $workforceIdentityFederationConfig;
  }
  /**
   * @return WorkforceIdentityFederationConfig
   */
  public function getWorkforceIdentityFederationConfig()
  {
    return $this->workforceIdentityFederationConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_SecureSourceManager_Instance');
