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

namespace Google\Service\DisplayVideo;

class EnvironmentTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when environment is not specified in this version. This enum
   * is a placeholder for default value and does not represent a real
   * environment option.
   */
  public const ENVIRONMENT_ENVIRONMENT_UNSPECIFIED = 'ENVIRONMENT_UNSPECIFIED';
  /**
   * Target inventory displayed in browsers. This includes inventory that was
   * designed for the device it was viewed on, such as mobile websites viewed on
   * a mobile device. ENVIRONMENT_WEB_NOT_OPTIMIZED, if targeted, should be
   * deleted prior to the deletion of this targeting option.
   */
  public const ENVIRONMENT_ENVIRONMENT_WEB_OPTIMIZED = 'ENVIRONMENT_WEB_OPTIMIZED';
  /**
   * Target inventory displayed in browsers. This includes inventory that was
   * not designed for the device but viewed on it, such as websites optimized
   * for desktop but viewed on a mobile device. ENVIRONMENT_WEB_OPTIMIZED should
   * be targeted prior to the addition of this targeting option.
   */
  public const ENVIRONMENT_ENVIRONMENT_WEB_NOT_OPTIMIZED = 'ENVIRONMENT_WEB_NOT_OPTIMIZED';
  /**
   * Target inventory displayed in apps.
   */
  public const ENVIRONMENT_ENVIRONMENT_APP = 'ENVIRONMENT_APP';
  /**
   * Output only. The serving environment.
   *
   * @var string
   */
  public $environment;

  /**
   * Output only. The serving environment.
   *
   * Accepted values: ENVIRONMENT_UNSPECIFIED, ENVIRONMENT_WEB_OPTIMIZED,
   * ENVIRONMENT_WEB_NOT_OPTIMIZED, ENVIRONMENT_APP
   *
   * @param self::ENVIRONMENT_* $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return self::ENVIRONMENT_*
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnvironmentTargetingOptionDetails::class, 'Google_Service_DisplayVideo_EnvironmentTargetingOptionDetails');
