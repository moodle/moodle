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

namespace Google\Service\VMMigrationService;

class NetworkInsights extends \Google\Model
{
  /**
   * @var string
   */
  public $sourceNetworkConfig;
  /**
   * @var string
   */
  public $sourceNetworkTerraform;

  /**
   * @param string
   */
  public function setSourceNetworkConfig($sourceNetworkConfig)
  {
    $this->sourceNetworkConfig = $sourceNetworkConfig;
  }
  /**
   * @return string
   */
  public function getSourceNetworkConfig()
  {
    return $this->sourceNetworkConfig;
  }
  /**
   * @param string
   */
  public function setSourceNetworkTerraform($sourceNetworkTerraform)
  {
    $this->sourceNetworkTerraform = $sourceNetworkTerraform;
  }
  /**
   * @return string
   */
  public function getSourceNetworkTerraform()
  {
    return $this->sourceNetworkTerraform;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkInsights::class, 'Google_Service_VMMigrationService_NetworkInsights');
