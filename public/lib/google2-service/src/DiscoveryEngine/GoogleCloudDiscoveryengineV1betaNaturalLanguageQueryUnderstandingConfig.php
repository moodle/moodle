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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaNaturalLanguageQueryUnderstandingConfig extends \Google\Model
{
  /**
   * Default value.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Natural Language Query Understanding is disabled.
   */
  public const MODE_DISABLED = 'DISABLED';
  /**
   * Natural Language Query Understanding is enabled.
   */
  public const MODE_ENABLED = 'ENABLED';
  /**
   * Mode of Natural Language Query Understanding. If this field is unset, the
   * behavior defaults to NaturalLanguageQueryUnderstandingConfig.Mode.DISABLED.
   *
   * @var string
   */
  public $mode;

  /**
   * Mode of Natural Language Query Understanding. If this field is unset, the
   * behavior defaults to NaturalLanguageQueryUnderstandingConfig.Mode.DISABLED.
   *
   * Accepted values: MODE_UNSPECIFIED, DISABLED, ENABLED
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaNaturalLanguageQueryUnderstandingConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaNaturalLanguageQueryUnderstandingConfig');
