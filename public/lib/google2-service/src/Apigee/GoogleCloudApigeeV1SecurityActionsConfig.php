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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityActionsConfig extends \Google\Model
{
  /**
   * The flag that controls whether this feature is enabled. This is `unset` by
   * default. When this flag is `false`, even if individual rules are enabled,
   * no SecurityActions will be enforced.
   *
   * @var bool
   */
  public $enabled;
  /**
   * This is a singleton resource, the name will always be set by
   * SecurityActions and any user input will be ignored. The name is always:
   * `organizations/{org}/environments/{env}/security_actions_config`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The update time for configuration.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The flag that controls whether this feature is enabled. This is `unset` by
   * default. When this flag is `false`, even if individual rules are enabled,
   * no SecurityActions will be enforced.
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
   * This is a singleton resource, the name will always be set by
   * SecurityActions and any user input will be ignored. The name is always:
   * `organizations/{org}/environments/{env}/security_actions_config`
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
   * Output only. The update time for configuration.
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
class_alias(GoogleCloudApigeeV1SecurityActionsConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityActionsConfig');
