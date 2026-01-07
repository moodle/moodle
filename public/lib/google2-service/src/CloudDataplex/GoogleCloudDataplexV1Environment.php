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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Environment extends \Google\Model
{
  /**
   * State is not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is active, i.e., ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Resource is under creation.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is under deletion.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Resource is active but has unresolved actions.
   */
  public const STATE_ACTION_REQUIRED = 'ACTION_REQUIRED';
  /**
   * Output only. Environment creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the environment.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User friendly display name.
   *
   * @var string
   */
  public $displayName;
  protected $endpointsType = GoogleCloudDataplexV1EnvironmentEndpoints::class;
  protected $endpointsDataType = '';
  protected $infrastructureSpecType = GoogleCloudDataplexV1EnvironmentInfrastructureSpec::class;
  protected $infrastructureSpecDataType = '';
  /**
   * Optional. User defined labels for the environment.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The relative resource name of the environment, of the form: pr
   * ojects/{project_id}/locations/{location_id}/lakes/{lake_id}/environment/{en
   * vironment_id}
   *
   * @var string
   */
  public $name;
  protected $sessionSpecType = GoogleCloudDataplexV1EnvironmentSessionSpec::class;
  protected $sessionSpecDataType = '';
  protected $sessionStatusType = GoogleCloudDataplexV1EnvironmentSessionStatus::class;
  protected $sessionStatusDataType = '';
  /**
   * Output only. Current state of the environment.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System generated globally unique ID for the environment. This
   * ID will be different if the environment is deleted and re-created with the
   * same name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the environment was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Environment creation time.
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
   * Optional. Description of the environment.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. User friendly display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. URI Endpoints to access sessions associated with the
   * Environment.
   *
   * @param GoogleCloudDataplexV1EnvironmentEndpoints $endpoints
   */
  public function setEndpoints(GoogleCloudDataplexV1EnvironmentEndpoints $endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return GoogleCloudDataplexV1EnvironmentEndpoints
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Required. Infrastructure specification for the Environment.
   *
   * @param GoogleCloudDataplexV1EnvironmentInfrastructureSpec $infrastructureSpec
   */
  public function setInfrastructureSpec(GoogleCloudDataplexV1EnvironmentInfrastructureSpec $infrastructureSpec)
  {
    $this->infrastructureSpec = $infrastructureSpec;
  }
  /**
   * @return GoogleCloudDataplexV1EnvironmentInfrastructureSpec
   */
  public function getInfrastructureSpec()
  {
    return $this->infrastructureSpec;
  }
  /**
   * Optional. User defined labels for the environment.
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
   * Output only. The relative resource name of the environment, of the form: pr
   * ojects/{project_id}/locations/{location_id}/lakes/{lake_id}/environment/{en
   * vironment_id}
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
   * Optional. Configuration for sessions created for this environment.
   *
   * @param GoogleCloudDataplexV1EnvironmentSessionSpec $sessionSpec
   */
  public function setSessionSpec(GoogleCloudDataplexV1EnvironmentSessionSpec $sessionSpec)
  {
    $this->sessionSpec = $sessionSpec;
  }
  /**
   * @return GoogleCloudDataplexV1EnvironmentSessionSpec
   */
  public function getSessionSpec()
  {
    return $this->sessionSpec;
  }
  /**
   * Output only. Status of sessions created for this environment.
   *
   * @param GoogleCloudDataplexV1EnvironmentSessionStatus $sessionStatus
   */
  public function setSessionStatus(GoogleCloudDataplexV1EnvironmentSessionStatus $sessionStatus)
  {
    $this->sessionStatus = $sessionStatus;
  }
  /**
   * @return GoogleCloudDataplexV1EnvironmentSessionStatus
   */
  public function getSessionStatus()
  {
    return $this->sessionStatus;
  }
  /**
   * Output only. Current state of the environment.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, DELETING,
   * ACTION_REQUIRED
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
   * Output only. System generated globally unique ID for the environment. This
   * ID will be different if the environment is deleted and re-created with the
   * same name.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The time when the environment was last updated.
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
class_alias(GoogleCloudDataplexV1Environment::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Environment');
