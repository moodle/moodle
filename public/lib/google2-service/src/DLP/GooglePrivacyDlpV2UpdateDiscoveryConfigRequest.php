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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2UpdateDiscoveryConfigRequest extends \Google\Model
{
  protected $discoveryConfigType = GooglePrivacyDlpV2DiscoveryConfig::class;
  protected $discoveryConfigDataType = '';
  /**
   * Mask to control which fields get updated.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. New DiscoveryConfig value.
   *
   * @param GooglePrivacyDlpV2DiscoveryConfig $discoveryConfig
   */
  public function setDiscoveryConfig(GooglePrivacyDlpV2DiscoveryConfig $discoveryConfig)
  {
    $this->discoveryConfig = $discoveryConfig;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryConfig
   */
  public function getDiscoveryConfig()
  {
    return $this->discoveryConfig;
  }
  /**
   * Mask to control which fields get updated.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2UpdateDiscoveryConfigRequest::class, 'Google_Service_DLP_GooglePrivacyDlpV2UpdateDiscoveryConfigRequest');
