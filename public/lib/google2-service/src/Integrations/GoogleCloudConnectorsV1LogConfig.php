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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1LogConfig extends \Google\Model
{
  /**
   * Log level unspecified.
   */
  public const LEVEL_LOG_LEVEL_UNSPECIFIED = 'LOG_LEVEL_UNSPECIFIED';
  /**
   * Only error logs are enabled.
   */
  public const LEVEL_ERROR = 'ERROR';
  /**
   * Info and error logs are enabled.
   */
  public const LEVEL_INFO = 'INFO';
  /**
   * Debug and high verbosity logs are enabled.
   */
  public const LEVEL_DEBUG = 'DEBUG';
  /**
   * Optional. Enabled represents whether logging is enabled or not for a
   * connection.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Optional. Log configuration level.
   *
   * @var string
   */
  public $level;

  /**
   * Optional. Enabled represents whether logging is enabled or not for a
   * connection.
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
   * Optional. Log configuration level.
   *
   * Accepted values: LOG_LEVEL_UNSPECIFIED, ERROR, INFO, DEBUG
   *
   * @param self::LEVEL_* $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }
  /**
   * @return self::LEVEL_*
   */
  public function getLevel()
  {
    return $this->level;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1LogConfig::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1LogConfig');
