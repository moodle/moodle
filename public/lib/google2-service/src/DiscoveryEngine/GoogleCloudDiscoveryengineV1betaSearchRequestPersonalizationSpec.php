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

class GoogleCloudDiscoveryengineV1betaSearchRequestPersonalizationSpec extends \Google\Model
{
  /**
   * Default value. In this case, server behavior defaults to Mode.AUTO.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Personalization is enabled if data quality requirements are met.
   */
  public const MODE_AUTO = 'AUTO';
  /**
   * Disable personalization.
   */
  public const MODE_DISABLED = 'DISABLED';
  /**
   * The personalization mode of the search request. Defaults to Mode.AUTO.
   *
   * @var string
   */
  public $mode;

  /**
   * The personalization mode of the search request. Defaults to Mode.AUTO.
   *
   * Accepted values: MODE_UNSPECIFIED, AUTO, DISABLED
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
class_alias(GoogleCloudDiscoveryengineV1betaSearchRequestPersonalizationSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchRequestPersonalizationSpec');
