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

namespace Google\Service\CloudComposer;

class Environment extends \Google\Model
{
  /**
   * The state of the environment is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The environment is in the process of being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The environment is currently running and healthy. It is ready for use.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The environment is being updated. It remains usable but cannot receive
   * additional update requests or be deleted at this time.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The environment is undergoing deletion. It cannot be used.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The environment has encountered an error and cannot be used.
   */
  public const STATE_ERROR = 'ERROR';
  protected $configType = EnvironmentConfig::class;
  protected $configDataType = '';
  /**
   * Output only. The time at which this environment was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-defined labels for this environment. The labels map can
   * contain no more than 64 entries. Entries of the labels map are UTF8 strings
   * that comply with the following restrictions: * Keys must conform to regexp:
   * \p{Ll}\p{Lo}{0,62} * Values must conform to regexp:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63} * Both keys and values are additionally
   * constrained to be <= 128 bytes in size.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the environment, in the form:
   * "projects/{projectId}/locations/{locationId}/environments/{environmentId}"
   * EnvironmentId must start with a lowercase letter followed by up to 63
   * lowercase letters, numbers, or hyphens, and cannot end with a hyphen.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * The current state of the environment.
   *
   * @var string
   */
  public $state;
  protected $storageConfigType = StorageConfig::class;
  protected $storageConfigDataType = '';
  /**
   * Output only. The time at which this environment was last modified.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The UUID (Universally Unique IDentifier) associated with this
   * environment. This value is generated when the environment is created.
   *
   * @var string
   */
  public $uuid;

  /**
   * Optional. Configuration parameters for this environment.
   *
   * @param EnvironmentConfig $config
   */
  public function setConfig(EnvironmentConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return EnvironmentConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The time at which this environment was created.
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
   * Optional. User-defined labels for this environment. The labels map can
   * contain no more than 64 entries. Entries of the labels map are UTF8 strings
   * that comply with the following restrictions: * Keys must conform to regexp:
   * \p{Ll}\p{Lo}{0,62} * Values must conform to regexp:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63} * Both keys and values are additionally
   * constrained to be <= 128 bytes in size.
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
   * Identifier. The resource name of the environment, in the form:
   * "projects/{projectId}/locations/{locationId}/environments/{environmentId}"
   * EnvironmentId must start with a lowercase letter followed by up to 63
   * lowercase letters, numbers, or hyphens, and cannot end with a hyphen.
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
   * Output only. Reserved for future use.
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
   * Output only. Reserved for future use.
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
   * The current state of the environment.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, RUNNING, UPDATING, DELETING,
   * ERROR
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
   * Optional. Storage configuration for this environment.
   *
   * @param StorageConfig $storageConfig
   */
  public function setStorageConfig(StorageConfig $storageConfig)
  {
    $this->storageConfig = $storageConfig;
  }
  /**
   * @return StorageConfig
   */
  public function getStorageConfig()
  {
    return $this->storageConfig;
  }
  /**
   * Output only. The time at which this environment was last modified.
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
   * Output only. The UUID (Universally Unique IDentifier) associated with this
   * environment. This value is generated when the environment is created.
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
class_alias(Environment::class, 'Google_Service_CloudComposer_Environment');
