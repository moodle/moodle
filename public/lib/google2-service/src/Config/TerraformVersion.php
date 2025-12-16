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

namespace Google\Service\Config;

class TerraformVersion extends \Google\Model
{
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The version is actively supported.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The version is deprecated.
   */
  public const STATE_DEPRECATED = 'DEPRECATED';
  /**
   * The version is obsolete.
   */
  public const STATE_OBSOLETE = 'OBSOLETE';
  /**
   * Output only. When the version is deprecated.
   *
   * @var string
   */
  public $deprecateTime;
  /**
   * Identifier. The version name is in the format: 'projects/{project_id}/locat
   * ions/{location}/terraformVersions/{terraform_version}'.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. When the version is obsolete.
   *
   * @var string
   */
  public $obsoleteTime;
  /**
   * Output only. The state of the version, ACTIVE, DEPRECATED or OBSOLETE.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. When the version is supported.
   *
   * @var string
   */
  public $supportTime;

  /**
   * Output only. When the version is deprecated.
   *
   * @param string $deprecateTime
   */
  public function setDeprecateTime($deprecateTime)
  {
    $this->deprecateTime = $deprecateTime;
  }
  /**
   * @return string
   */
  public function getDeprecateTime()
  {
    return $this->deprecateTime;
  }
  /**
   * Identifier. The version name is in the format: 'projects/{project_id}/locat
   * ions/{location}/terraformVersions/{terraform_version}'.
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
   * Output only. When the version is obsolete.
   *
   * @param string $obsoleteTime
   */
  public function setObsoleteTime($obsoleteTime)
  {
    $this->obsoleteTime = $obsoleteTime;
  }
  /**
   * @return string
   */
  public function getObsoleteTime()
  {
    return $this->obsoleteTime;
  }
  /**
   * Output only. The state of the version, ACTIVE, DEPRECATED or OBSOLETE.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, DEPRECATED, OBSOLETE
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
   * Output only. When the version is supported.
   *
   * @param string $supportTime
   */
  public function setSupportTime($supportTime)
  {
    $this->supportTime = $supportTime;
  }
  /**
   * @return string
   */
  public function getSupportTime()
  {
    return $this->supportTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TerraformVersion::class, 'Google_Service_Config_TerraformVersion');
