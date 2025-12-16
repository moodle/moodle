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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1ApiHubInstance extends \Google\Model
{
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The ApiHub instance has not been initialized or has been deleted.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The ApiHub instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The ApiHub instance has been created and is ready for use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The ApiHub instance is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The ApiHub instance is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The ApiHub instance encountered an error during a state change.
   */
  public const STATE_FAILED = 'FAILED';
  protected $configType = GoogleCloudApihubV1Config::class;
  protected $configDataType = '';
  /**
   * Output only. Creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the ApiHub instance.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Instance labels to represent user-provided metadata. Refer to
   * cloud documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Format:
   * `projects/{project}/locations/{location}/apiHubInstances/{apiHubInstance}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of the ApiHub instance.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Extra information about ApiHub instance state. Currently the
   * message would be populated when state is `FAILED`.
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
   * Required. Config of the ApiHub instance.
   *
   * @param GoogleCloudApihubV1Config $config
   */
  public function setConfig(GoogleCloudApihubV1Config $config)
  {
    $this->config = $config;
  }
  /**
   * @return GoogleCloudApihubV1Config
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
   * Optional. Description of the ApiHub instance.
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
   * Optional. Instance labels to represent user-provided metadata. Refer to
   * cloud documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
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
   * Identifier. Format:
   * `projects/{project}/locations/{location}/apiHubInstances/{apiHubInstance}`.
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
   * Output only. The current state of the ApiHub instance.
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
   * Output only. Extra information about ApiHub instance state. Currently the
   * message would be populated when state is `FAILED`.
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
class_alias(GoogleCloudApihubV1ApiHubInstance::class, 'Google_Service_APIhub_GoogleCloudApihubV1ApiHubInstance');
