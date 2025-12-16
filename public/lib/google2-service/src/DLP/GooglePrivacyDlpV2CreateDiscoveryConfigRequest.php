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

class GooglePrivacyDlpV2CreateDiscoveryConfigRequest extends \Google\Model
{
  /**
   * The config ID can contain uppercase and lowercase letters, numbers, and
   * hyphens; that is, it must match the regular expression: `[a-zA-Z\d-_]+`.
   * The maximum length is 100 characters. Can be empty to allow the system to
   * generate one.
   *
   * @var string
   */
  public $configId;
  protected $discoveryConfigType = GooglePrivacyDlpV2DiscoveryConfig::class;
  protected $discoveryConfigDataType = '';

  /**
   * The config ID can contain uppercase and lowercase letters, numbers, and
   * hyphens; that is, it must match the regular expression: `[a-zA-Z\d-_]+`.
   * The maximum length is 100 characters. Can be empty to allow the system to
   * generate one.
   *
   * @param string $configId
   */
  public function setConfigId($configId)
  {
    $this->configId = $configId;
  }
  /**
   * @return string
   */
  public function getConfigId()
  {
    return $this->configId;
  }
  /**
   * Required. The DiscoveryConfig to create.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CreateDiscoveryConfigRequest::class, 'Google_Service_DLP_GooglePrivacyDlpV2CreateDiscoveryConfigRequest');
