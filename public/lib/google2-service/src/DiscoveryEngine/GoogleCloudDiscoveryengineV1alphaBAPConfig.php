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

class GoogleCloudDiscoveryengineV1alphaBAPConfig extends \Google\Collection
{
  protected $collection_key = 'supportedConnectorModes';
  /**
   * Optional. The actions enabled on the associated BAP connection.
   *
   * @var string[]
   */
  public $enabledActions;
  /**
   * Required. The supported connector modes for the associated BAP connection.
   *
   * @var string[]
   */
  public $supportedConnectorModes;

  /**
   * Optional. The actions enabled on the associated BAP connection.
   *
   * @param string[] $enabledActions
   */
  public function setEnabledActions($enabledActions)
  {
    $this->enabledActions = $enabledActions;
  }
  /**
   * @return string[]
   */
  public function getEnabledActions()
  {
    return $this->enabledActions;
  }
  /**
   * Required. The supported connector modes for the associated BAP connection.
   *
   * @param string[] $supportedConnectorModes
   */
  public function setSupportedConnectorModes($supportedConnectorModes)
  {
    $this->supportedConnectorModes = $supportedConnectorModes;
  }
  /**
   * @return string[]
   */
  public function getSupportedConnectorModes()
  {
    return $this->supportedConnectorModes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaBAPConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaBAPConfig');
