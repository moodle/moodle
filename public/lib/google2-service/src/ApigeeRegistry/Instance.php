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

namespace Google\Service\ApigeeRegistry;

class Instance extends \Google\Model
{
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Instance has not been initialized or has been deleted.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The Instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The Instance has been created and is ready for use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The Instance is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The Instance is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The Instance encountered an error during a state change.
   */
  public const STATE_FAILED = 'FAILED';
  protected $buildType = Build::class;
  protected $buildDataType = '';
  protected $configType = Config::class;
  protected $configDataType = '';
  /**
   * Output only. Creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Format: `projects/locations/instance`. Currently only `locations/global` is
   * supported.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of the Instance.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Extra information of Instance.State if the state is `FAILED`.
   *
   * @var string
   */
  public $stateMessage;
  /**
   * Output only. Last update timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Build info of the Instance if it's in `ACTIVE` state.
   *
   * @param Build $build
   */
  public function setBuild(Build $build)
  {
    $this->build = $build;
  }
  /**
   * @return Build
   */
  public function getBuild()
  {
    return $this->build;
  }
  /**
   * Required. Config of the Instance.
   *
   * @param Config $config
   */
  public function setConfig(Config $config)
  {
    $this->config = $config;
  }
  /**
   * @return Config
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. Creation timestamp.
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
   * Format: `projects/locations/instance`. Currently only `locations/global` is
   * supported.
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
   * Output only. The current state of the Instance.
   *
   * Accepted values: STATE_UNSPECIFIED, INACTIVE, CREATING, ACTIVE, UPDATING,
   * DELETING, FAILED
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
   * Output only. Extra information of Instance.State if the state is `FAILED`.
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
   * Output only. Last update timestamp.
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
class_alias(Instance::class, 'Google_Service_ApigeeRegistry_Instance');
